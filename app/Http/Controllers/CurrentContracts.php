<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrentContracts extends Controller
{
    public function index()
    {
        return view('current-contracts.index');
    }
}
