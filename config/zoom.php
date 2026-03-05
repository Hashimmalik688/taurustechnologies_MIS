<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Zoom API Authentication (General App - OAuth)
    |--------------------------------------------------------------------------
    |
    | Uses General App OAuth. Admin authorizes once → refresh token keeps it alive.
    | No Server-to-Server app needed.
    |
    */

    'auth_type' => env('ZOOM_AUTH_TYPE', 'oauth'),

    // General App OAuth Credentials
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'redirect_uri' => env('ZOOM_OAUTH_REDIRECT_URI', env('APP_URL') . '/zoom/callback'),
    'account_id' => env('ZOOM_ACCOUNT_ID'),

    // Legacy aliases (for backward compat with existing code)
    's2s' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
    ],
    'oauth' => [
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'redirect_uri' => env('ZOOM_OAUTH_REDIRECT_URI', env('APP_URL') . '/zoom/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin-Managed App (Call Logs — Account-Level Access)
    |--------------------------------------------------------------------------
    | Separate admin-managed Zoom app used solely for GET /phone/call_logs.
    | Returns ALL extensions' history with a single admin authorization.
    | Required scope: phone:read:list_call_logs:admin
    |
    | Setup:
    |  1. Create new Admin-managed app in Zoom Marketplace
    |  2. Add scope: phone:read:list_call_logs:admin
    |  3. Set ZOOM_ADMIN_CLIENT_ID + ZOOM_ADMIN_CLIENT_SECRET in .env
    |  4. Visit /zoom/admin-authorize once (as Hashim/admin) to authorize
    |
    */
    'admin_app' => [
        'client_id'     => env('ZOOM_ADMIN_CLIENT_ID'),
        'client_secret' => env('ZOOM_ADMIN_CLIENT_SECRET'),
        'redirect_uri'  => env('ZOOM_ADMIN_REDIRECT_URI', env('APP_URL') . '/zoom/admin-callback'),
    ],

    // API Base URL
    'base_url' => env('ZOOM_API_URL', 'https://api.zoom.us/v2'),
    'oauth_url' => env('ZOOM_OAUTH_URL', 'https://zoom.us/oauth/token'),
    'timeout' => env('ZOOM_API_TIMEOUT', 30),

    // Webhook Configuration
    'webhook' => [
        'enabled' => env('ZOOM_WEBHOOK_ENABLED', false),
        'secret' => env('ZOOM_WEBHOOK_SECRET'),
        'route' => '/api/zoom-webhook',
    ],

    // Token encryption (uses app.key by default)
    'encrypt_tokens' => env('ZOOM_ENCRYPT_TOKENS', true),

    // Call Log API Sync Settings
    'call_logs' => [
        'default_page_size' => 100,
        'max_page_size' => 300,
        'default_date_range' => 30,
        'sync_interval' => 5, // minutes
    ],

];
