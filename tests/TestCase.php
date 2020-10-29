<?php

namespace Tests;

use App\Models\Contract;
use App\Models\Coop;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function makeSampleContract(array $data = []): Contract
    {
        return factory(Contract::class)->create($data);
    }

    public function makeSampleCoop(?Contract $contract = null, string $coopName = 'test'): Coop
    {
        if (!$contract) {
            $contract = $this->makeSampleContract();
        }
        $coop = Coop::make([
            'contract' => $contract->identifier,
            'coop'     => $coopName,
        ]);
        $coop->guild_id = 1;
        $coop->save();

        return $coop;
    }
}
