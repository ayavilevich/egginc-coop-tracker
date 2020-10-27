<?php
namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ContractCollection extends Collection
{
    public function getInRawFormat(): array
    {
        return array_values($this->pluck('raw_data')->all());
    }
}
