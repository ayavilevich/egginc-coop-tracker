<?php

namespace App\Models;

use App\Api\EggInc;
use App\Formatters\EarningBonus;
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

    protected $appends = ['player_earning_bonus_formatted', 'player_egg_rank', 'drones', 'soul_eggs', 'eggs_of_prophecy', 'player_earning_bonus'];

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

    public function getEggPlayerInfo(): ?stdClass
    {
        if (!$this->egg_inc_player_id) {
            return null;
        }
        return resolve(EggInc::class)->getPlayerInfo($this->egg_inc_player_id);
    }

    public function getEggsOfProphecyAttribute(): int
    {
        $info = $this->getEggPlayerInfo();
        if (!$info) {
            return 0;
        }

        return $info->game->eggsOfProphecy;
    }

    public function getEachSoulEggBonus(): int
    {
        $info = $this->getEggPlayerInfo();
        if (!$info) {
            return 0;
        }

        $epicResearch = collect($info->game->epicResearchList);
        $prophecyBonus = $epicResearch->where('id', 'prophecy_bonus')->first()->level;
        $soulBonus = $epicResearch->where('id', 'soul_eggs')->first()->level;
        $eggsOfProphecy = $this->getEggsOfProphecyAttribute();

        return floor(((.1 + $soulBonus * .01) * (1.05 + $prophecyBonus * .01) ** $eggsOfProphecy) * 100);
    }

    public function getPlayerEarningBonus(): float
    {
        return floor($this->getEachSoulEggBonus() * $this->getSoulEggsAttribute());
    }

    public function getPlayerEarningBonusAttribute(): float
    {
        return $this->getPlayerEarningBonus();
    }

    public function getSoulEggsAttribute(): float
    {
        $info = $this->getEggPlayerInfo();

        if (!$info) {
            return 0;
        }

        return $info->game->soulEggsD;
    }

    public function getPlayerEarningBonusFormatted(): string
    {
        return resolve(EarningBonus::class)->format($this->getPlayerEarningBonus());
    }

    public function getPlayerEarningBonusFormattedAttribute(): string
    {
        return $this->getPlayerEarningBonusFormatted();
    }

    public function getPlayerEggRank(): string
    {
        $roles = json_decode(file_get_contents(base_path('resources/js/roleMagnitude.json')));
        $earningBonus = $this->getPlayerEarningBonus();

        $last = null;
        foreach ($roles as $role) {
            // if (soulPower / Math.pow(10, MagnitudeFormat[i].magnitude) < 1) {
            if ($earningBonus / pow(10, $role->magnitude) < 1) {
                break;
            }
            $last = $role;
        }

        if (!$last) {
            return '';
        }
        return $last->name;
    }

    public function getPlayerEggRankAttribute(): string
    {
        return $this->getPlayerEggRank();
    }

    public function getDronesAttribute(): int
    {
        $info = $this->getEggPlayerInfo();

        if (!$info) {
            return 0;
        }
        return $info->stats->droneTakedowns;
    }

    public function scopeWithEggIncId($query)
    {
        return $query->where(function ($query) {
            return $query->where('egg_inc_player_id', '!=', '')->orWhereNotNull('egg_inc_player_id');
        });
    }
}
