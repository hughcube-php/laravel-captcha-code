<?php

return [
    'default' => 'default',

    'defaults' => [
        'storage' => [
            'driver' => 'cache',
            'cache' => 'default',
        ],
        'generator' => [
            'driver' => 'default',
            'length' => 4,
            //'string' => '0123456789',
        ],
        'defaultTtl' => 10 * 60,
        'defaultCodes' => [
            // 'test' => '8888',
        ],
    ],

    'stores' => [
        'default' => [],
    ],
];
