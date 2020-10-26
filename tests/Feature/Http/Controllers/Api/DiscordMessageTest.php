<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscordMessageTest extends TestCase
{
    use RefreshDatabase;

    private function sendDiscordMessage(string $message): string
    {
        $response = $this->postJson(
            '/api/discord-message',
            [
                'channel'   => ['guild' => ['id' => 1]],
                'content'   => 'eb!' . $message,
                'atBotUser' => 'eb!',
            ]
        );

        return $response
            ->assertStatus(200)
            ->decodeResponseJson('message')
        ;
    }

    public function testLove()
    {
        $message = $this->sendDiscordMessage('love');

        $this->assertEquals('What is this thing called love?', $message);
    }
}
