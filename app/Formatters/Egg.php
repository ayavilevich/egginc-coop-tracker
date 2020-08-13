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
        return round($eggs / pow(10, $last->magnitude)) . $last->symbol;
    }
}
