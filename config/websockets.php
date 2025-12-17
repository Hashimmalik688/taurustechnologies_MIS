<?php

use BeyondCode\LaravelWebSockets\Statistics\Logger\NullLogger;

return [
    'apps' => [
        [
            'id' => env('PUSHER_APP_ID', 'app-id'),
            'name' => env('APP_NAME', 'Laravel'),
            'key' => env('PUSHER_APP_KEY', 'your-key'),
            'secret' => env('PUSHER_APP_SECRET', 'your-secret'),
            'path' => env('PUSHER_APP_PATH', ''),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => true,
        ],
    ],

    'dashboard' => [
        'port' => env('WEBSOCKETS_DASHBOARD_PORT', 6001),
    ],

    'statistics' => [
        'model' => null,
        'logger' => NullLogger::class,
    ],

    'ssl' => [
        'local_cert' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT', null),
        'local_pk' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_PK', null),
        'passphrase' => env('LARAVEL_WEBSOCKETS_SSL_PASSPHRASE', null),
        'verify_peer' => env('LARAVEL_WEBSOCKETS_SSL_VERIFY_PEER', false),
    ],

    // Path for storing channel statistics (optional)
    'channel_manager' => [
        'redis' => [
            'connection' => env('WEBSOCKETS_REDIS_CONNECTION', 'default'),
        ],
    ],
];
