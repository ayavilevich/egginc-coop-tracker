<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use mikehaertl\shellcommand\Command;

class CurrentContracts extends Controller
{
    public function index()
    {
        $contractCommand = new Command([
            'command' => 'node .\js\egg-inc.js getAllActiveContracts',
            'procCwd' => base_path(),
        ]
        );

        $contracts = [];
        if ($contractCommand->execute()) {
            $contracts = json_decode($contractCommand->getOutput());
        }

        if (!$contracts) {
            throw new \Exception('Could not load contracts');
        }
        // dd($contracts);

        return Inertia::render('CurrentContracts', ['contracts' => $contracts]);
    }
}
