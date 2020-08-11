<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coop extends Model
{
    public function scopeContract($query, $contract)
    {
        return $query->where('contract', $contract);
    }
}
