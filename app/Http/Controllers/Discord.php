<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Socialite;

class Discord extends Controller
{
    public function redirect()
    {
        return Socialite::driver('discord')
            ->scopes([
                'identify',
                'email',
                'guilds',
            ])
            ->redirect()
        ;
    }

    public function callback()
    {
        $userInfo = Socialite::driver('discord')->user();

        User::unguarded(function () use ($userInfo) {
            $user = User::updateOrCreate(
                ['discord_id' => $userInfo->getId()],
                [
                    'username'              => $userInfo->getName(),
                    'email'                 => $userInfo->getEmail(),
                    'discord_token'         => $userInfo->token,
                    'discord_token_expires' => now()->addSeconds($userInfo->expiresIn),
                    'discord_refresh_token' => $userInfo->refreshToken,
                ]
            );
            Auth::login($user);
        });

        return redirect()->route('home');
    }
}
