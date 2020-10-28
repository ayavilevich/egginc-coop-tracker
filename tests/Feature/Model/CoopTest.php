<?php

namespace Tests\Feature\Models;

use App\Models\Contract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoopTest extends TestCase
{
    use RefreshDatabase;

    public function testPosition()
    {
        $coop1 = $this->makeSampleCoop();
        $coop2 = $this->makeSampleCoop($coop1->contract()->first());

        $this->assertEquals(1, $coop1->position);
        $this->assertEquals(2, $coop2->position);
    }
}
