<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => 'Last Minute Decoration',
            'identifier' => 'halloween-2020',
            'raw_data'   => json_decode(file_get_contents(base_path('tests/files/halloween-2020.json'))),
        ];
    }
}
