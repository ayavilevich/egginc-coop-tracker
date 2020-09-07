<?php

namespace App\Models;

use App\Api\EggInc;
use App\Formatters\Egg;
use App\Formatters\TimeLeft;
use Illuminate\Database\Eloquent\Model;

class Coop extends Model
{
    protected $fillable = ['contract', 'coop'];

    public function scopeContract($query, $contract)
    {
        return $query->where('contract', $contract);
    }

    public function scopeCoop($query, $coop)
    {
        return $query->where('coop', $coop);
    }

    public function getCoopInfo(): \StdClass
    {
        return resolve(EggInc::class)->getCoopInfo($this->contract, $this->coop);
    }

    public function getCurrentEggs(): int
    {
        return $this->getCoopInfo()->eggs;
    }

    public function getEggsNeeded(): int
    {
        return end($this->getContractInfo()->goalsList)->targetAmount;
    }

    public function getProjectedEggs(): int
    {
        return $this->getCurrentEggs() + ($this->getTotalRate() * $this->getTimeLeft());
    }

    public function getProjectedEggsFormatted(): string
    {
        return resolve(Egg::class)->format($this->getProjectedEggs(), 1);
    }

    public function getCurrentEggsFormatted(): string
    {
        return resolve(Egg::class)->format($this->getCurrentEggs());
    }

    public function getEggsNeededFormatted(): string
    {
        return resolve(Egg::class)->format(end($this->getContractInfo()->goalsList)->targetAmount);
    }

    public function getContractInfo(): \StdClass
    {
        return collect(resolve(EggInc::class)->getCurrentContracts())
            ->where('identifier', $this->contract)
            ->first()
        ;
    }

    public function getEggsLeftNeeded(): int
    {
        return $this->getEggsNeeded() - $this->getCurrentEggs();
    }

    public function getEstimateCompletion(): string
    {
        if ($this->getEggsLeftNeeded() < 0) {
            return 'Complete';
        }
        $seconds = ceil($this->getEggsLeftNeeded() / $this->getTotalRate());

        return resolve(TimeLeft::class)->format($seconds);
    }

    public function getTotalRate(): int
    {
        return $this->getCoopInfo()->totalRate ?: 1;
    }

    public function getTimeLeft(): int
    {
        return $this->getCoopInfo()->timeLeft;
    }

    public function getMembers(): int
    {
        return count($this->getCoopInfo()->members);
    }

    public function getContractSize(): int
    {
        return $this->getContractInfo()->maxCoopSize;
    }
}
