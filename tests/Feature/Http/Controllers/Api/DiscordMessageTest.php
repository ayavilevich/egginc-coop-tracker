<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Api\EggInc;
use App\Models\Contract;
use App\Models\Coop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Mockery;
use RestCord\DiscordClient;
use RestCord\OverriddenGuzzleClient;
use StdClass;
use Tests\TestCase;

class DiscordMessageTest extends TestCase
{
    use RefreshDatabase;

    private $atBotUser = 'eb!';

    private $guildId = 1;

    private function sendDiscordMessage(string $message): string
    {
        $this->mockGuildCall();

        $response = $this->postJson(
            '/api/discord-message',
            [
                'channel'   => ['guild' => ['id' => $this->guildId]],
                'content'   => $this->atBotUser . $message,
                'atBotUser' => $this->atBotUser,
                'author'    => ['id' => 723977563650654259],
            ]
        );

        if ($response->getStatusCode() == 500) {
            dd($response->getContent());
        }

        return $response
            ->assertStatus(200)
            ->decodeResponseJson('message')
        ;
    }

    private function mockGuildCall()
    {
        app()->bind(DiscordClient::class, function () {
            return Mockery::mock(DiscordClient::class, function ($mock) {
                $guildCall = Mockery::mock(OverriddenGuzzleClient::class, function ($mock) {
                    $guild = new StdClass;
                    $guild->name = 'Test';

                    $mock
                        ->shouldReceive('getGuild')
                        ->andReturn($guild)
                    ;

                    $role = new StdClass;
                    $role->id = 1;
                    $role->name = 'Everybody';
                    $roles = [$role];

                    $mock
                        ->shouldReceive('getGuildRoles')
                        ->andReturn($roles);
                    ;

                    $member = new StdClass;
                    $member->user = new StdClass;
                    $member->user->bot = false;
                    $member->user->id = 123456;
                    $member->user->username = 'Test';
                    $member->roles = [1];
                    $members = [$member];

                    $mock
                        ->shouldReceive('listGuildMembers')
                        ->andReturn($members)
                    ;
                });

                $mock->guild = $guildCall;

                $userCall = Mockery::mock(OverriddenGuzzleClient::class, function ($mock) {
                    $user = new StdClass;
                    $user->id = 123456;
                    $user->username = 'Test';
                    $user->email = 'test@example.com';

                    $mock
                        ->shouldReceive('getUser')
                        ->andReturn($user)
                    ;
                });

                $mock->user = $userCall;
            });
        });
    }

    public function testLove()
    {
        $message = $this->sendDiscordMessage('love');

        $this->assertEquals('What is this thing called love?', $message);
    }

    public function testHelp()
    {
        $message = $this->sendDiscordMessage('help');

        $expect = <<<HELP
```
eb!help - Displays list of commands
eb!contracts - Display current contracts with IDs
eb!status {Contract ID} - Display coop info for contract
eb!s {Contract ID} - Short version of status
eb!add {Contract ID} {Coop} {?Coop} - Add coop to tracking, multiple can be added by this command. When multiple is added, the position of the coops is set.
eb!delete {contractID} {Coop} - Remove coop from tracking

eb!set-player-id {@Discord Name} {Egg Inc Player ID}
```
HELP;
        $this->assertEquals($expect, $message);
    }

    public function testHi()
    {
        $message = $this->sendDiscordMessage('hi');

        $this->assertEquals('Hello <@723977563650654259>!', $message);
    }

    public function testCurrentContracts()
    {
        $contract = $this->makeSampleContract(['expiration' => now()->addDays(7)]);

        $message = $this->sendDiscordMessage('contracts');

        $expect = <<<CONTRACTS
```
{$contract->identifier}($contract->name)
```
CONTRACTS;
        $this->assertEquals($expect, $message);
    }

    public function testAdd()
    {
        $contract = $this->makeSampleContract();

        $message = $this->sendDiscordMessage('add ' . $contract->identifier . ' test');
        $expect = 'Coop added successfully.';

        $this->assertEquals($expect, $message);
    }

    /**
     * @depends testAdd
     */
    public function testAddMultiple()
    {
        $contract = $this->makeSampleContract();

        $message = $this->sendDiscordMessage('add ' . $contract->identifier . ' test test2');
        $expect = 'Coops added successfully.';

        $this->assertEquals($expect, $message);

        $coops = Coop::contract($contract->identifier)
            ->guild($this->guildId)
            ->orderBy('position')
            ->get()
        ;
        $this->assertEquals(2, $coops->count());

        foreach ($coops as $coop) {
            switch ($coop->position) {
                case 1:
                    $this->assertEquals('test', $coop->coop);
                    break;
                case 2:
                    $this->assertEquals('test2', $coop->coop);
                    break;
            }
        }
    }

    /**
     * @depends testAddMultiple
     */
    public function testUpdatePosition()
    {
        $this->testAddMultiple();

        $contract = Contract::find(1);

        $message = $this->sendDiscordMessage('add ' . $contract->identifier . ' test2 test');
        $expect = 'Coops added successfully.';

        $this->assertEquals($expect, $message);

        $coops = Coop::contract($contract->identifier)
            ->guild($this->guildId)
            ->orderBy('position')
            ->get()
        ;
        $this->assertEquals(2, $coops->count());

        foreach ($coops as $coop) {
            switch ($coop->position) {
                case 1:
                    $this->assertEquals('test2', $coop->coop);
                    break;
                case 2:
                    $this->assertEquals('test', $coop->coop);
                    break;
            }
        }
    }

    public function testDelete()
    {
        $contract = $this->makeSampleContract();
        $coop = $this->makeSampleCoop($contract);

        $message = $this->sendDiscordMessage('delete ' . $contract->identifier . ' test');
        $expect = 'Coop has been deleted.';

        $this->assertEquals($expect, $message);
    }

    public function testStatus()
    {
        $this->instance(EggInc::class, Mockery::mock(EggInc::class, function ($mock) {
            $coopInfo = json_decode(file_get_contents(base_path('tests/files/halloween-2020-test.json')));

            $mock
                ->shouldReceive('getCoopInfo')
                ->andReturn($coopInfo)
            ;
        }));

        $contract = $this->makeSampleContract();
        $coop = $this->makeSampleCoop($contract);

        $url = URL::signedRoute('contract-status', ['guildId' => $this->guildId, 'contractId' => $contract->identifier], 60 * 60);
        $message = $this->sendDiscordMessage('status ' . $contract->identifier);
        $expect = <<<STATUS
Last Minute Decoration
{$url}
```
Coop 13 | 1Q  | E Time  | Proj
------- | --- | ------- | ----
test 13 | 1q  | 446d 6h | 11q 
```
STATUS;

        $this->assertEquals($expect, $message);
    }

    public function testShortStatus()
    {
        $this->instance(EggInc::class, Mockery::mock(EggInc::class, function ($mock) {
            $coopInfo = json_decode(file_get_contents(base_path('tests/files/halloween-2020-test.json')));

            $mock
                ->shouldReceive('getCoopInfo')
                ->andReturn($coopInfo)
            ;
        }));

        $contract = $this->makeSampleContract();
        $this->makeSampleCoop($contract, 'honeyadv1');
        $this->makeSampleCoop($contract, 'honeyadv2');
        $this->makeSampleCoop($contract, 'honeyadv3');

        $message = $this->sendDiscordMessage('s ' . $contract->identifier);
        $expect = <<<STATUS
Last Minute Decoration
```
C 13 | 1Q  | E Time  | Proj
---- | --- | ------- | ----
1 13 | 1q  | 446d 6h | 11q 
2 13 | 1q  | 446d 6h | 11q 
3 13 | 1q  | 446d 6h | 11q 
```
STATUS;

        $this->assertEquals($expect, $message);
    }

    public function testSetPlayerId()
    {
        $message = $this->sendDiscordMessage('set-player-id <@!123456> 12345');
        $expect = 'Player ID set successfully.';

        $this->assertDatabaseHas('guilds', ['discord_id' => 1, 'name' => 'Test']);
        $this->assertDatabaseHas('users', ['discord_id' => 123456, 'egg_inc_player_id' => '12345']);

        $this->assertEquals($message, $expect);
    }

    
    /**
     * depends testSetPlayerId
     */
    public function testListPlayersWithEggId()
    {
        $this->testSetPlayerId();

        $message = $this->sendDiscordMessage('players egg_id');
        $expect = <<<PLAYERS
```
Discord | Egg Inc ID
------- | ----------
Test    | 12345     
```
PLAYERS;

        $this->assertEquals($expect, $message);
    }

    public function testListPlayerWithRank()
    {
        $this->testSetPlayerId();

        $this->instance(EggInc::class, Mockery::mock(EggInc::class, function ($mock) {
            $player = json_decode(file_get_contents(base_path('tests/files/mot3rror-player-info.json')));

            $mock
                ->shouldReceive('getPlayerInfo')
                ->withArgs(['12345'])
                ->andReturn($player)
            ;
        }));

        $message = $this->sendDiscordMessage('players rank');
        $expect = <<<PLAYERS
```
Discord | Rank         
------- | -------------
Test    | Zettafarmer 3
```
PLAYERS;

        $this->assertEquals($expect, $message);
    }
}
