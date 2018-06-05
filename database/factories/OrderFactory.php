<?php



use Faker\Generator as Faker;

$factory->define(Leven\Models\WalletOrder::class, function (Faker $faker) {
    static $user_id;

    return [
        'owner_id' => $user_id,
        'target_type' => 'user',
        'target_id' => 'id',
        'title' => '测试标题',
        'body' => '测试内容',
        'type' => 1,
        'amount' => random_int(1, 999999),
        'state' => rand(-1, 1),
    ];
});
