<?php

namespace App\Models;

use App\Api\EggInc;
use Illuminate\Database\Eloquent\Model;

class Coop extends Model
{
    public function scopeContract($query, $contract)
    {
        return $query->where('contract', $contract);
    }

    public function getCoopInfo()
    {
        return resolve(EggInc::class)->getCoopInfo($this->contract, $this->coop);
    }
}
