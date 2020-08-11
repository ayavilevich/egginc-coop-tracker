<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use mikehaertl\shellcommand\Command;
use Cache;

class CurrentContracts extends Controller
{
    public function index()
    {
        $contracts = $this->getContractsInfo();

        return Inertia::render('CurrentContracts', ['contracts' => $contracts]);
    }

    public function status($contractId)
    {
        $currentCoopIds = [
            'secret-projects' => [
                'secretadv1', 'secretadv8', 'secretadv3', 'secretadv4', 'secretadv5',
                'secretadv6', 'secretadv7',
            ],
            'video-games' => [
                'vgamesadv1', 'vgamesadv2', 'vgamesadv3', 'vgamesadv4', 'vgamesadv5',
                'vgamesadv6', 'gameadvx',
            ],
            'ion-drive' => [
                'ionadv1', 'ionadv2', 'ionadv3', 'ionadv4', 'ionadv5', 'ionadv6',
                'ionadvx1', 'ionadvx2'
            ],
        ];
        if (!isset($currentCoopIds[$contractId])) {
            abort(404);
        }

        $coopsInfo = [];
        foreach ($currentCoopIds[$contractId] as $coop) {
            $coopInfo = null;
            $cacheKey = $contractId . '-' . $coop;

            $coopInfo = Cache::remember($cacheKey, 60 * 5, function () use ($contractId, $coop) {
                $appInfoCommand = new Command([
                    'command' => 'node ./js/egg-inc.js getCoopStatus --contract ' . $contractId . ' --coop ' . $coop,
                    'procCwd' => base_path(),
                ]);

                if (!$appInfoCommand->execute()) {
                    throw new Exception('Unable to get coop data');
                }
                    return json_decode($appInfoCommand->getOutput());
            });

            $coopsInfo[] = $coopInfo;
        }

        return Inertia::render('ContractStatus', [
            'coopsInfo'    => $coopsInfo,
            'contractInfo' => $this->getContractInfo($contractId),
        ]);
    }

    private function getContractsInfo()
    {
        return Cache::remember('coops', 60 * 60, function () {
            $contractCommand = new Command([
                'command' => 'node ./js/egg-inc.js getAllActiveContracts',
                'procCwd' => base_path(),
            ]);

            $contracts = [];
            if ($contractCommand->execute()) {
                $contracts = json_decode($contractCommand->getOutput());
            }

            if (!$contracts) {
                throw new \Exception('Could not load contracts');
            }
            return $contracts;
        });
    }

    private function getContractInfo($identifier)
    {
        return collect($this->getContractsInfo())
            ->where('identifier', $identifier)
            ->first()
        ;
    }
}
