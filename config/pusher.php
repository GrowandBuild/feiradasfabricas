<?php

return [
    'app_id' => env('PUSHER_APP_ID', 'local'),
    'key' => env('PUSHER_APP_KEY', 'local'),
    'secret' => env('PUSHER_APP_SECRET', 'local'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
        'host' => env('PUSHER_HOST', '127.0.0.1'),
        'port' => env('PUSHER_PORT', 6001),
        'scheme' => env('PUSHER_SCHEME', 'http'),
        'encrypted' => false,
        'useTLS' => false,
    ],
];
