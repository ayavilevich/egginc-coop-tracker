<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $casts = ['raw_data' => 'json'];

    public static function getAllActiveContracts()
    {

    }
}
