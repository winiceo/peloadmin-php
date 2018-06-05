<?php



use Faker\Generator as Faker;

$factory->define(Leven\Models\TagCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'weight' => $faker->numberBetween(100, 999),
    ];
});
