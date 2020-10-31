<?php

namespace Tests\Unit\Models;

use App\Api\EggInc;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class UserTest extends TestCase
{
    private function getUser(string $testuser = 'mot3rror'): User
    {
        $this->instance(EggInc::class, Mockery::mock(EggInc::class, function ($mock) use ($testuser) {
            $player = json_decode(file_get_contents(base_path('tests/files/' . $testuser . '-player-info.json')));

            $mock
                ->shouldReceive('getPlayerInfo')
                ->withArgs(['123456'])
                ->andReturn($player)
            ;
        }));
        $user = new User;
        $user->egg_inc_player_id = '123456';
        return $user;
    }

    public function testGetPlayerInfo()
    {
        $user = $this->getUser('mot3rror');

        $expects = json_decode(file_get_contents(base_path('tests/files/mot3rror-player-info.json')));
        $actual = $user->getEggPlayerInfo();
        $this->assertEquals($expects, $actual);
    }

    public function sampleUsersForEachSoulEggBonus()
    {
        return [
            [
                'mot3rror',
                27099562,
            ],
            [
                '1132Ace',
                1683,
            ],
            [
                'ladykojac',
                4215,
            ],
            [
                'mstrixie',
                598762,
            ],
            [
                'oobebanoobe',
                449859,
            ],
        ];
    }

    /**
     * @dataProvider sampleUsersForEachSoulEggBonus
     */
    public function testEachSoulEggBonus($user, $expects)
    {
        $user = $this->getUser($user);

        $actual = $user->getEachSoulEggBonus();

        $this->assertEquals($expects, $actual);
    }

    public function samplePlayersEarningBonus()
    {
        return [
            [
                'mot3rror',
                2.5263954836751094E+25,
            ],
            /*[
                '1132Ace',
                1.1734311252202E+17,
            ],*/
            [
                'ladykojac',
                1.1557447998440884E+20,
            ],
            [
                'mstrixie',
                2.9986788655459293E+23,
            ],
            [
                'oobebanoobe',
                9.650462683552914E+21,
            ],
        ];
    }

    /**
     * @dataProvider samplePlayersEarningBonus
     */
    public function testGetPlayerEarningBonus($user, $expects)
    {
        $user = $this->getUser($user);

        $actual = $user->getPlayerEarningBonus();

        $this->assertEquals($expects, $actual);
    }
}
