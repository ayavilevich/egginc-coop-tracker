<?php

namespace App\Http\Controllers\Api;

use App\Api\EggInc;
use App\Exceptions\CoopNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Coop;
use App\SimilarText;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use kbATeam\MarkdownTable\Column;
use kbATeam\MarkdownTable\Table;

class DiscordMessage extends Controller
{
    private $validCommands = ['help', 'status', 'contracts', 'love', 'hi', 'add', 'delete', 's',];

    private $guildId;

    public function receive(Request $request): array
    {
        $this->guildId = $request->input('channel.guild.id');

        if (!$this->guildId) {
            return ['message' => 'Invalid Server.'];
        }

        $message = trim(str_replace($request->input('atBotUser'), '', $request->input('content')));
        $parts = explode(' ', $message);
        $command = $parts['0'];

        if (!in_array($command, $this->validCommands)) {
            $message = 'Invalid command: ' . $command;
        } else {
            $message = $this->$command($parts, $request);
        }

        return ['message' => $message];
    }

    private function hi(array $parts, Request $request): string
    {
        \Log::info('hello', ['data' => $request->all()]);
        return 'Hello <@' . $request->input('author.id') . '>!';
    }

    private function love(): string
    {
        return 'What is this thing called love?';
    }

    private function help(): string
    {
        return <<<HELP
```
eb!help - Displays list of commands
eb!contracts - Display current contracts with IDs
eb!status {Contract ID} - Display coop info for contract
eb!s {Contract ID} - Short version of status
eb!add {Contract ID} {Coop} {?Coop} - Add coop to tracking, multiple can be added by this command. When multiple is added, the position of the coops is set.
eb!delete {contractID} {Coop} - Remove coop from tracking
```
HELP;
    }

    private function coops(string $contract): Collection
    {
        return Coop::contract($contract)
            ->guild($this->guildId)
            ->orderBy('position')
            ->get()
        ;
    }

    private function status(array $parts): string
    {
        $coops = $this->coops($parts[1]);

        if ($coops->count() == 0) {
            return 'Invalid contract ID or no coops setup.';
        }

        try {
            $contractInfo = $this->getContractInfo($parts[1]);
        } catch (\Exception $e) {
            $contractInfo = null;
        }
        $firstCoop = $coops->first();
        $messages = [
            $contractInfo ? $contractInfo->name : $parts[1],
            URL::signedRoute('contract-status', ['contractId' => $parts[1]], 60 * 60),
        ];

        $table = new Table();
        $table->addColumn('name', new Column('Coop ' . $firstCoop->getContractSize() . '', Column::ALIGN_LEFT));
        $table->addColumn('progress', new Column($firstCoop->getEggsNeededFormatted(), Column::ALIGN_LEFT));
        $table->addColumn('time-left', new Column('E Time', Column::ALIGN_LEFT));
        $table->addColumn('projected', new Column('Proj', Column::ALIGN_LEFT));

        $data = [];
        foreach ($coops as $coop) {
            try {
                $data[] = [
                    'name'      => $coop->coop . ' ' . $coop->getMembers() . '',
                    'progress'  => $coop->getCurrentEggsFormatted(),
                    'time-left' => $coop->getEstimateCompletion(),
                    'projected' => $coop->getProjectedEggsFormatted(),
                ];
            } catch (CoopNotFoundException $e) {
                $data[] = [
                    'name'     => $coop->coop,
                    'progress' => 'NA',
                ];
            }
        }

        $messages[] = '```';
        foreach ($table->generate($data) as $row) {
            $messages[] = $row;
        }
        $messages[] = '```';

        return implode("\n", $messages);
    }

    private function s(array $parts): string
    {
        $coops = $this->coops($parts[1]);

        if ($coops->count() == 0) {
            return 'Invalid contract ID or no coops setup.';
        }

        try {
            $contractInfo = $this->getContractInfo($parts[1]);
        } catch (\Exception $e) {
            $contractInfo = null;
        }
        $firstCoop = $coops->first();
        $messages = [
            $contractInfo ? $contractInfo->name : $parts[1]
        ];

        $table = new Table();
        $table->addColumn('name', new Column('C ' . $firstCoop->getContractSize() . '', Column::ALIGN_LEFT));
        $table->addColumn('progress', new Column($firstCoop->getEggsNeededFormatted(), Column::ALIGN_LEFT));
        $table->addColumn('time-left', new Column('E Time', Column::ALIGN_LEFT));
        $table->addColumn('projected', new Column('Proj', Column::ALIGN_LEFT));

        $similarText = new SimilarText;
        $similarPart = $similarText->similar($coops->pluck('coop')->all());

        $data = [];
        foreach ($coops as $coop) {
            $coopName = str_replace($similarPart, '', $coop->coop);
            try {
                $data[] = [
                    'name'      => $coopName . ' ' . $coop->getMembers() . '',
                    'progress'  => $coop->getCurrentEggsFormatted(),
                    'time-left' => $coop->getEstimateCompletion(),
                    'projected' => $coop->getProjectedEggsFormatted(),
                ];
            } catch (CoopNotFoundException $e) {
                $data[] = [
                    'name'     => $coopName,
                    'progress' => 'NA',
                ];
            }
        }

        $messages[] = '```';
        foreach ($table->generate($data) as $row) {
            $messages[] = $row;
        }
        $messages[] = '```';

        return implode("\n", $messages);
    }

    private function contracts(): string
    {
        $contracts = $this->getContractsInfo();

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

        $contractIsValid = $this->getContractInfo($parts[1]);

        if (!$contractIsValid) {
            return 'Contract is invalid.';
        }

        if (!$parts[2]) {
            return 'Coop name is required';
        }

        if (isset($parts[3])) {
            $position = 1;

            foreach ($parts as $key => $part) {
                if (in_array($key, [0, 1])) {
                    continue;
                }

                Coop::unguarded(function () use ($parts, $part, $position) {
                    Coop::updateOrCreate(
                        [
                            'contract' => $parts[1],
                            'coop'     => $part,
                            'guild_id' => $this->guildId,
                        ],
                        [
                            'position' => $position,
                        ]
                    );
                });

                $position++;
            }

            return 'Coops added successfully.';
        } else {
            $coopCheck = Coop::contract($parts[1])
                ->guild($this->guildId)
                ->coop($parts[2])
                ->first()
            ;

            if ($coopCheck) {
                return 'Coop is already being tracked.';
            }

            $coop = new Coop([
                'contract' => $parts[1],
                'coop'     => $parts[2],
            ]);
            $coop->guild_id = $this->guildId;
            $coop->save();
            if ($coop->id) {
                return 'Coop added successfully.';
            } else {
                return 'Was not able to add coop.';
            }
        }        
    }

    private function delete(array $parts, Request $request): string
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
            ->guild($this->guildId)
            ->first()
        ;

        if (!$coop) {
            return 'Coop does not exist yet.';
        }

        if ($coop->delete()) {
            return 'Coop has been deleted.';
        } else {
            return 'Was not able to delete the coop.';
        }
    }

    private function getContractInfo(string $identifier): ?\StdClass
    {
        $contract = Contract::firstWhere('identifier', $identifier);

        if (!$contract) {
            abort(404, 'Contract not found.');
        }

        return $contract->raw_data;
    }
}
