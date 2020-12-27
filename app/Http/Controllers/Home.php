<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Home extends Controller
{
    public function index(Request $request)
    {
        $guilds = new \StdClass;
        $playerInfo = new \StdClass;
        if ($request->user()) {
            $guilds = $request->user()->discordGuilds();

            $playerInfo = $request->user()->getEggPlayerInfo();
        }

        return Inertia::render('Home', [
            'guilds'     => $guilds,
            'playerInfo' => $playerInfo,
            'user'       => $request->user(),
        ]);
    }
}
