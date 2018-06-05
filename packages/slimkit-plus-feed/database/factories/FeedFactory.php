<?php



use Faker\Generator as Faker;

$factory->define(\Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed::class, function (Faker $faker) {
    return [
        'feed_content' => $faker->shuffle(),
        'feed_from' => 5,
        'user_id' => $faker->randomNumber(),
        'feed_mark' => $faker->unique()->randomNumber(),
    ];
});
