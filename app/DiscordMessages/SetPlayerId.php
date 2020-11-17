<?php
namespace App\DiscordMessages;

use App\Models\User;
use RestCord\DiscordClient;

class SetPlayerId extends Base
{
    protected $middlewares = ['requiresGuild', 'isAdmin'];

    public function message(): string
    {
        $parts = $this->parts;
        $user = User::unguarded(function () use ($parts) {
            $discord = app()->makeWith(DiscordClient::class, [
                'token'     => config('services.discord.token'),
                'tokenType' => 'Bot',
            ]);

            $userId = str_replace(['<@!', '>'], '', $parts[1]);
            $user = $discord->user->getUser(['user.id' => (int) $userId]);

            return User::updateOrCreate(
                ['discord_id' => $user->id],
                [
                    'egg_inc_player_id' => $parts[2],
                    'username'          => $user->username,
                ]
            );
        });

        return 'Player ID set successfully.';
    }
}
