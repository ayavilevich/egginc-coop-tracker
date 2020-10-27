<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Contract;
use Faker\Generator as Faker;

$factory->define(Contract::class, function (Faker $faker) {
    return [
        'name'       => 'Last Minute Decoration',
        'identifier' => 'halloween-2020',
        'raw_data'   => json_decode(file_get_contents(base_path('tests/files/halloween-2020.json'))),
    ];
});
