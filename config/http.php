<?php



return [
    'cors' => [
        'credentials' => false,
        'origin' => ['*'],
        'methods' => ['*'],       // => ['GET', 'POST', 'PATCH', 'PUT', 'OPTION', 'PUT', 'DELETE']
        'allow-headers' => ['*'], // => ['Origin', 'Content-Type', 'Accept', 'X-Requested-With']
        'expose-headers' => [],
        'max-age' => 0,
    ],
    'spa' => [
        'open' => false,
        'uri' => null,
    ],
    'web' => [
        'open' => false,
        'uri' => null,
    ],
];
