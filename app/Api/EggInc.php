<?php
namespace App\Api;

use Cache;
use mikehaertl\shellcommand\Command;

class EggInc
{
    public function getCoopInfo(string $contract, string $coop): \StdClass
    {
        $cacheKey = $contract . '-' . $coop;

        return Cache::remember($cacheKey, 60 * 5, function () use ($contract, $coop) {
            $appInfoCommand = new Command([
                'command' => 'node ./js/egg-inc.js getCoopStatus --contract ' . $contract . ' --coop ' . $coop,
                'procCwd' => base_path(),
            ]);

            if (!$appInfoCommand->execute()) {
                throw new Exception('Unable to get coop data');
            }
            return json_decode($appInfoCommand->getOutput());
        });
    }

    public function getCurrentContracts(): array
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
}
