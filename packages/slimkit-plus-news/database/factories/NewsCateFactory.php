<?php



use Faker\Generator as Faker;

$factory->define(\Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'rank' => $faker->biasedNumberBetween(100, 999),
    ];
});
