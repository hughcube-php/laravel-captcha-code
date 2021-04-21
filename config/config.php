<?php

return [
    'default' => 'default',

    'defaults' => [
        'storage' => [
            'driver' => 'cache',
        ],
        'generator' => [
            'driver' => 'default',
            'length' => 4,
            'string' => env("CAPTCHA_CODE_GENERATOR_STRING"),
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
