<?php

namespace Tests\Unit\Models;

use App\Api\EggInc;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testGetPlayerInfo()
    {
        $this->instance(EggInc::class, Mockery::mock(EggInc::class, function ($mock) {
            $contracts = json_decode(file_get_contents(base_path('tests/files/mot3rror-player-info.json')));

            $mock
                ->shouldReceive('getPlayerInfo')
                ->withArgs(['123456'])
                ->once()
                ->andReturn($contracts)
            ;
        }));
        $user = new User;
        $user->egg_inc_player_id = '123456';

        $expects = json_decode(file_get_contents(base_path('tests/files/mot3rror-player-info.json')));
        $actual = $user->getEggPlayerInfo();
        $this->assertEquals($expects, $actual);
    }
}
