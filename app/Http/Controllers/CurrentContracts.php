<?php
namespace App\Http\Controllers;

use App\Api\EggInc;
use App\Models\Coop;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            $coopsInfo[] = $coop->getCoopInfo();
        }

        return Inertia::render('ContractStatus', [
            'coopsInfo'    => $coopsInfo,
            'contractInfo' => $this->getContractInfo($contractId),
        ]);
    }

    private function getContractsInfo()
    {
        return resolve(EggInc::class)->getCurrentContracts();
    }

    private function getContractInfo($identifier)
    {
        return collect($this->getContractsInfo())
            ->where('identifier', $identifier)
            ->first()
        ;
    }
}
