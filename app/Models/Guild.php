<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RestCord\DiscordClient;

class Guild extends Model
{
    protected $with = ['members'];

    protected $appends = ['is_bot_member_of'];

    private function getDiscordClient(): DiscordClient
    {
        return new DiscordClient([
            'token'     => config('services.discord.token'),
            'tokenType' => 'Bot',
        ]);
    }

    public function getIsBotMemberOfAttribute(): bool
    {
        return (bool) collect($this->getDiscordClient()->user->getCurrentUserGuilds())
            ->where('id', $this->discord_id)
            ->first()
        ;
    }

    public function syncMembers()
    {
        $members = $this->getGuildMembers();
        $users = collect();

        foreach ($members as $member) {
            if ($member->user->bot) {
                continue;
            }

            $user = User::unguarded(function () use ($member) {
                return User::updateOrCreate(
                    ['discord_id' => $member->user->id],
                    ['username' => $member->user->username]
                );
            });
            $users[] = $user;
        }
        $this->members()->sync($users->pluck('id'));
    }

    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    // need to setup this to run when new server is added and add webhook to monitor members
    public function getGuildMembers()
    {
        return $this->getDiscordClient()->guild->listGuildMembers(['guild.id' => (int) $this->discord_id, 'limit' => 100]);
    }
}
