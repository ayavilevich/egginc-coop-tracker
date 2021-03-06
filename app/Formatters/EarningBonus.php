<?php
namespace App\Formatters;

use Litipk\BigNumbers\Decimal;

class EarningBonus extends Egg
{
    public function format($bonus, int $decimals = 0): string
    {
        $last = null;
        $bonus = Decimal::create($bonus); 
        foreach ($this->magnitudes as $magnitude) {
            if ($bonus->div(Decimal::create(pow(10, $magnitude->magnitude)))->isLessThan(Decimal::create(1))) {
                break;
            }
            $last = $magnitude;
        }

        if (!$last) {
            return $bonus;
        }

        return $bonus->div(Decimal::create(pow(10, $last->magnitude)))->floor(3) . $last->symbol;
    }
}
