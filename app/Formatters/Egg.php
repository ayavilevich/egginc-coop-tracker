<?php
namespace App\Formatters;

class Egg
{
    public function __construct()
    {
        $this->magnitudes = json_decode(file_get_contents(base_path('resources/js/magnitudeFormat.json')));
    }

    public function format(int $eggs): string
    {
        $last = null;
        foreach ($this->magnitudes as $magnitude) {
            if ($eggs / pow(10, $magnitude->magnitude) < 1) {
                break;
            }
            $last = $magnitude;
        }

        if (!$last) {
            return $eggs;
        }

        $decimals = 0;
        if ($last->symbol == 'Q') {
            $decimals = 1;
        }

        if ($decimals) {
            return round(($eggs * ($decimals * 10))/ pow(10, $last->magnitude)) / ($decimals * 10) . $last->symbol;
        }
        return round($eggs / pow(10, $last->magnitude)) . $last->symbol;
    }
}
