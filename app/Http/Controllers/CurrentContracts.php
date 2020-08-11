<?php
namespace App\Http\Controllers;

use App\Models\Coop;
use Cache;
use Illuminate\Http\Request;
use Inertia\Inertia;
use mikehaertl\shellcommand\Command;

class CurrentContracts extends Controller
{
    public function index()
    {
        $contracts = $this->getContractsInfo();

        return Inertia::render('CurrentContracts', ['contracts' => $contracts]);
    }

    public function status($contractId)
    {
        $coops = Coop::contract($contractId)->get();

        if ($coops->count() == 0) {
            abort(404);
        }

        $coopsInfo = [];
        foreach ($coops as $coop) {
            $coopInfo = null;
            $cacheKey = $contractId . '-' . $coop->coop;

            $coopInfo = Cache::remember($cacheKey, 60 * 5, function () use ($contractId, $coop) {
                $appInfoCommand = new Command([
                    'command' => 'node ./js/egg-inc.js getCoopStatus --contract ' . $contractId . ' --coop ' . $coop->coop,
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
