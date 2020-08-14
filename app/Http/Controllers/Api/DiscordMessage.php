<?php

namespace App\Http\Controllers\Api;

use App\Api\EggInc;
use App\Http\Controllers\Controller;
use App\Models\Coop;
use Illuminate\Http\Request;

class DiscordMessage extends Controller
{
    private $validCommands = ['help', 'status', 'contracts', 'love'];

    public function receive(Request $request): array
    {
        $message = str_replace($request->input('atBotUser') . ' ', '', $request->input('content'));
        $parts = explode(' ', $message);
        $command = $parts['0'];

        if (!in_array($command, $this->validCommands)) {
            $message = 'Invalid command: ' . $command;
        } else {
            $message = $this->$command($parts);
        }

        return [
            'message' => $message,
        ];
    }

    private function love(): string
    {
        return 'What is this thing called love?';
    }

    private function help(): string
    {
        return <<<HELP
```
@EggInc-GroupStatus help - Displays list of commands
@EggInc-GroupStatus contracts - Display current contracts with IDs
@EggInc-GroupStatus status contractId - Display coop info for contract
```
HELP;
    }

    private function status(array $parts): string
    {
        $coops = Coop::contract($parts[1])->get();

        if ($coops->count() == 0) {
            return 'Invalid contract ID or no coops setup.';
        }

        $message = [];
        foreach ($coops as $coop) {
            $message[] = $coop->coop . ' - ' . $coop->getCurrentEggsFormatted() . '/' . $coop->getEggsNeededFormatted() . ' - ' . $coop->getEstimateCompletion();
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
}
