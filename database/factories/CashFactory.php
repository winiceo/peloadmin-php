<?php



use Faker\Generator as Faker;

$factory->define(Leven\Models\WalletCash::class, function (Faker $faker) {
    static $user_id;

    return [
        'user_id' => $user_id,
        'type' => 'alipay',
        'account' => '1212121212',
        'value' => random_int(1, 999999),
        'status' => rand(0, 2),
        'remark' => '',
    ];
});
