<?php

namespace App\Http\Controllers;

use App\Models\Guild as GuildModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Guild extends Controller
{
    public function index(Request $request, $guildId)
    {
        $guilds = $request->user()->guilds();
        $guild = collect($guilds)
            ->where('id', $guildId)
            ->first()
        ;

        if (!$guild) {
            return redirect()->route('home');
        }

        $guildModel = GuildModel::unguarded(function () use ($guild) {
            return GuildModel::updateOrCreate(['discord_id' => $guild->id], ['name' => $guild->name]);
        });

        return Inertia::render('Guild', [
            'guild'      => $guild,
            'guildModel' => $guildModel,
        ]);
    }
}
