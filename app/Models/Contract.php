<?php

namespace App\Models;

use App\Collections\ContractCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $casts = [
        'raw_data'   => 'object',
        'expiration' => 'datetime',
    ];

    public static function getAllActiveContracts()
    {
        return self::query()
            ->whereDate('expiration', '>', now())
            ->get()
        ;
    }

    public function newCollection(array $models = []): Collection
    {
        return new ContractCollection($models);
    }
}
