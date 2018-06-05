<?php



use Faker\Generator as Faker;

$factory->define(Leven\Models\Advertising::class, function (Faker $faker) {
    return [
        'space_id' => 1,
        'title' => '测试标题',
        'type' => 'image',
        'data' => [
            'image' => 'http://xxx/xxx.jpg',
            'url' => 'http://www.xxxxx.com',
        ],
        'sort' => 0,
    ];
});
