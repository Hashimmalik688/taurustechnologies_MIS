<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Zoom API Authentication
    |--------------------------------------------------------------------------
    |
    | Choose 'server_to_server' or 'oauth'.
    |
    */

    'auth_type' => env('ZOOM_AUTH_TYPE', 'server_to_server'),

    // Server-to-Server (JWT) Configuration
    's2s' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
    ],

    // OAuth Configuration
    'oauth' => [
        'client_id' => env('ZOOM_OAUTH_CLIENT_ID'),
        'client_secret' => env('ZOOM_OAUTH_CLIENT_SECRET'),
        'redirect_uri' => env('ZOOM_OAUTH_REDIRECT_URI', env('APP_URL') . '/admin/zoom/callback'),
    ],

    // API Base URL
    'base_url' => env('ZOOM_API_URL', 'https://api.zoom.us/v2'),
    'timeout' => env('ZOOM_API_TIMEOUT', 30),

    // Webhook Configuration
    'webhook' => [
        'enabled' => env('ZOOM_WEBHOOK_ENABLED', false),
        'secret' => env('ZOOM_WEBHOOK_SECRET'),
        'route' => '/api/zoom-webhook',
    ],

    // Token encryption (uses app.key by default)
    'encrypt_tokens' => env('ZOOM_ENCRYPT_TOKENS', true),

    // Call Log Settings
    'call_logs' => [
        'default_page_size' => 100,
        'max_page_size' => 300,
        'default_date_range' => 30,
        'sync_interval' => '*/15',
    ],

];
