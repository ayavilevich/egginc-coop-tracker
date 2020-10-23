<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Home extends Controller
{
    public function index(Request $request)
    {
        $guilds = [];
        if ($request->user()) {
            $guilds = $request->user()->guilds();
        }

        return Inertia::render('Home', ['guilds' => $guilds]);
    }
}
