<?php
namespace App\Formatters;

class Egg
{
    public function __construct()
    {
        $this->magnitudes = json_decode(file_get_contents(base_path('resources/js/magnitudeFormat.json')));
    }

    public function format(int $eggs, int $decimals = 0): string
    {
        $last;
        foreach ($this->magnitudes as $magnitude) {
            if ($eggs / pow(10, $magnitude->magnitude) < 1) {
                break;
            }
            $last = $magnitude;
        }

        if (!$last) {
            return $eggs;
        }
        if ($decimals) {
            return round(($eggs * ($decimals * 10))/ pow(10, $last->magnitude)) / ($decimals * 10) . $last->symbol;
        }
        return round($eggs / pow(10, $last->magnitude)) . $last->symbol;
    }
}
