<?php



use Faker\Generator as Faker;

$factory->define(\Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News::class, function (Faker $faker) {
    return [
        'title' => $faker->firstName,
        'subject' => $faker->firstName,
        'content' => $faker->text,
        'from' => $faker->firstName,
        'text_content' => $faker->text,
        'author' => $faker->name,
    ];
});
