<?php

namespace Tests\Unit\Formatters;

use App\Formatters\Egg;
use Tests\TestCase;

class EggTest extends TestCase
{
    public function formattingData()
    {
        return [
            [
                '1Q',
                '1,000,000,000,000,000,000',
            ],
            [
                '523q',
                '523,450,000,000,000,000',
            ],
            [
                '1.2Q',
                '1,200,000,000,000,000,000',
            ],
        ];
    }

    /**
     * @dataProvider formattingData
     */
    public function testFormatting(string $expect, string $number)
    {
        $egg = new Egg;
        $number = (int) str_replace(',', '', $number);
        $this->assertEquals($expect, $egg->format($number));
    }
}
