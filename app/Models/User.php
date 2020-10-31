<?php

namespace App\Models;

use App\Api\EggInc;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RestCord\DiscordClient;
use stdClass;

class User extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'discord_token_expires' => 'datetime',
    ];

    protected $with = ['roles'];

    public function getCurrentDiscordToken()
    {
        if ($this->discord_token_expires->lt(now())) {
            // make call to https://discord.com/api/v6/oauth2/token
            // https://discord.com/developers/docs/topics/oauth2#authorization-code-grant-refresh-token-exchange-example
        }
        return $this->discord_token;
    }

    public function discordGuilds()
    {
        $discord = new DiscordClient([
            'token'     => $this->getCurrentDiscordToken(),
            'tokenType' => 'OAuth',
        ]);
        $guilds = $discord->user->getCurrentUserGuilds();

        foreach ($guilds as $key => $guild) {
            $guild->isAdmin = $guild->permissions & 8;
            // weird bug with vue or something that causes this number to change
            $guild->id = (string) $guild->id;
            $guildModel = Guild::findByDiscordGuild($guild);

            if (!$guild->isAdmin && !$guildModel->getIsBotMemberOfAttribute()) {
                unset($guilds[$key]);
            }
        }
        return $guilds;
    }

    public function guilds()
    {
        return $this->belongsToMany(Guild::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getEggPlayerInfo(): stdClass
    {
        return resolve(EggInc::class)->getPlayerInfo($this->egg_inc_player_id);
    }
}
