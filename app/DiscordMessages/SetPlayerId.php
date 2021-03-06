<?php
namespace App\DiscordMessages;

use App\Models\User;
use RestCord\DiscordClient;

class SetPlayerId extends Base
{
    protected $middlewares = [];

    public function message(): string
    {
        $parts = $this->parts;
        $user = User::unguarded(function () use ($parts) {
            return User::updateOrCreate(
                ['discord_id' => $this->authorId],
                [
                    'egg_inc_player_id' => $parts[1],
                    'username'          => $this->authorName,
                ]
            );
        });

        return 'Player ID set successfully.';
    }
}
