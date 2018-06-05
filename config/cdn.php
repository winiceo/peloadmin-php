<?php



return [

    /*
    |--------------------------------------------------------------------------
    | Default CDN Generator
    |--------------------------------------------------------------------------
    |
    | This option controls the default CDN driver, and you can select any of
    | the supported drivers.
    |
    | Supported: "filesystem", "qiniu", "alioss"
    |
    */

    'default' => 'filesystem',

    /*
    |--------------------------------------------------------------------------
    | CDN generators
    |--------------------------------------------------------------------------
    |
    | Here, you can define all supported driver configurations, and all
    | configurations can be configured arbitrarily, but the only thing to note
    | is that the "driver" entry must exist.
    |
    */

    'generators' => [
        'filesystem' => [
            'driver' => \Leven\Cdn\Adapter\Filesystem::class,
            'disk' => 'public',
            'public' => null,
        ],
        'qiniu' => [
            'driver' => \Leven\Cdn\Adapter\Qiniu::class,
            'domain' => null,
            'ak' => null,
            'sk' => null,
            'sign' => false,
            'expires' => 3600,
            'type' => 'cdn', // cdn or object
            'bucket' => null,
        ],
        'alioss' => [
            'driver' => \Leven\Cdn\Adapter\AliOss::class,
            'AccessKeyId' => null,
            'AccessKeySecret' => null,
            'bucket' => null,
            'endpoint' => null,
            'ssl' => false,
            'public' => true,
            'expires' => 3600,
        ],
    ],
];
