<?php

namespace App\Http\Controllers\Api;

use App\Api\EggInc;
use App\Exceptions\CoopNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Coop;
use Illuminate\Http\Request;

class DiscordMessage extends Controller
{
    private $validCommands = ['help', 'status', 'contracts', 'love', 'hi', 'add', 'remove'];

    public function receive(Request $request): array
    {
        $message = trim(str_replace($request->input('atBotUser') . ' ', '', $request->input('content')));
        $parts = explode(' ', $message);
        $command = $parts['0'];

        if (!in_array($command, $this->validCommands)) {
            $message = 'Invalid command: ' . $command;
        } else {
            $message = $this->$command($parts, $request);
        }

        return [
            'message' => $message,
        ];
    }

    private function hi(array $parts, Request $request): string
    {
        return 'Hello <@' . $request->input('author.id') . '>';
    }

    private function love(): string
    {
        return 'What is this thing called love?';
    }

    private function help(): string
    {
        return <<<HELP
```
@EggBert help - Displays list of commands
@EggBert contracts - Display current contracts with IDs
@EggBert status contractId - Display coop info for contract
@EggBert add {contractID} {Coop} - Add coop to tracking
@EggBert remove {contractID} {Coop} - Remove coop from tracking

```
HELP;
    }

    private function status(array $parts): string
    {
        $coops = Coop::contract($parts[1])->get();

        if ($coops->count() == 0) {
            return 'Invalid contract ID or no coops setup.';
        }

        $message = [config('app.url') . route('contract-status', ['contractId' => $parts[1]], false)];
        foreach ($coops as $coop) {
            $coopLine = '';
            try {
                if ($coop->getEggsLeftNeeded() < 0) {
                    $coopLine .= '~~';
                }
                $coopLine .= $coop->coop . ' (' . $coop->getMembers() . '/' . $coop->getContractSize() . ') - ' . $coop->getCurrentEggsFormatted() . '/' . $coop->getEggsNeededFormatted() . ' - ' . $coop->getEstimateCompletion() . ' - Projected: ' . $coop->getProjectedEggsFormatted();

                if ($coop->getEggsLeftNeeded() < 0) {
                    $coopLine .= '~~';
                }
            } catch (CoopNotFoundException $e) {
                $coopLine = $coop->coop . ' Not created yet.';
            }
            $message[] = $coopLine;
        }

        return implode("\n", $message);
    }

    private function contracts(): string
    {
        $contracts = resolve(EggInc::class)->getCurrentContracts();

        $message[] = '```';

        foreach ($contracts as $contract) {
            $message[] = $contract->identifier . '(' . $contract->name . ')';
        }
        $message[] = '```';

        return implode("\n", $message);
    }

    private function isAdmin($userId)
    {
        return in_array($userId, explode(',', env('DISCORD_ADMIN_USERS')));
    }

    private function add(array $parts, Request $request): string
    {
        if (!$this->isAdmin($request->input('author.id'))) {
            return 'You are not allowed to do that.';
        }

        if (!$parts[1]) {
            return 'Contract ID required';
        }

        if (!$parts[2]) {
            return 'Coop name is required';
        }

        $coopCheck = Coop::contract($parts[1])
            ->coop($parts[2])
            ->first()
        ;

        if ($coopCheck) {
            return 'Coop is already being tracked.';
        }

        $contractIsValid = collect(resolve(EggInc::class)->getCurrentContracts())->where('identifier', $parts[1])->first();

        if (!$contractIsValid) {
            return 'Contract is invalid.';
        }

        $coop = Coop::create([
            'contract' => $parts[1],
            'coop' => $parts[2],
        ]);

        if ($coop->id) {
            return 'Coop added successfully.';
        } else {
            return 'Was not able to add coop.';
        }
    }

    private function remove(array $parts, Request $request): string
    {
        if (!$this->isAdmin($request->input('author.id'))) {
            return 'You are not allowed to do that.' . $request->input('author.id');
        }

        if (!$parts[1]) {
            return 'Contract ID required';
        }

        if (!$parts[2]) {
            return 'Coop name is required';
        }

        $coop = Coop::contract($parts[1])
            ->coop($parts[2])
            ->first()
        ;

        if (!$coop) {
            return 'Coop does not exist yet.';
        }

        if ($coop->delete()) {
            return 'Coop has been deleted';
        } else {
            return 'Was not able to delete the coop.';
        }
    }
}
