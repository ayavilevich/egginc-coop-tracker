<?php
namespace Tests\Unit;

use App\SimilarText;
use Tests\TestCase;

class SimilarTextTest extends TestCase
{
    public function similarTests()
    {
        return [
            [
                ['coopadv1', 'coopadv2', 'coopadv3'],
                'coopadv',
            ],
            [
                ['united states', 'united states of america', 'states of america'],
                'states'
            ]
        ];
    }

    /**
     * @dataProvider similarTests
     */
    public function testGetSimilar($strings, $expected)
    {
        $similarText = new SimilarText;

        $actual = $similarText->similar($strings);

        $this->assertEquals($expected, $actual);
    }
}
