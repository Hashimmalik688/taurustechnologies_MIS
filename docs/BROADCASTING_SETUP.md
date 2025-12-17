Self-hosted Laravel WebSockets (quick setup)

This project includes a `MessageSent` event and client-side Echo wiring (CDN-based) to subscribe to `chat.conversation.{id}` private channels.

Recommended approach: use BeyondCode's `laravel-websockets` package so you can host a Pusher-compatible websocket server locally.

Steps (Windows / PowerShell):

1) Install the package (composer)

```powershell
cd path\to\taurus-crm-master
composer require beyondcode/laravel-websockets
```

2) Publish vendor files and migrations

```powershell
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
```

3) Update your `.env` (example values)

Set the broadcast driver and pusher-style keys. These keys are used by both the client and the WebSocket server; with `laravel-websockets` you can pick any values.

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local_key
PUSHER_APP_SECRET=local_secret
PUSHER_APP_CLUSTER=mt1
WEBSOCKETS_HOST=127.0.0.1
WEBSOCKETS_PORT=6001
WEBSOCKETS_SCHEME=http
PUSHER_APP_USE_TLS=false
```

4) Start the websocket server

```powershell
# Start the Laravel queue/worker and websockets server in separate terminals (or use supervisors)
php artisan websockets:serve
```

5) Front-end (optional via npm)

The project includes a CDN-based Echo bootstrap in the chat view that will attempt to connect using the `.env` values. If you prefer to bundle with your assets, install:

```powershell
npm install --save laravel-echo pusher-js
# then import Echo in your JS and initialize with the same options
```

6) Broadcasting auth (private channels)

Make sure `routes/channels.php` has a proper authorization callback for `chat.conversation.{id}`. Example:

```php
Broadcast::channel('chat.conversation.{conversationId}', function ($user, $conversationId) {
    return \App\Models\ChatConversation::where('id', $conversationId)
        ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
        ->exists();
});
```

7) Test

- Open the app in two browser windows as different users.
- Select the same conversation.
- Send a message from one window — the other should receive it in real-time (the UI polls as a fallback if Echo isn't configured).

Troubleshooting

- If the Echo client fails to connect, check `php artisan websockets:serve` logs.
- Ensure `BROADCAST_DRIVER=pusher` and the `PUSHER_APP_*` values match between `.env` and the client config (the view reads env values into the client script).
- For HTTPS / production, use TLS and configure `wss` ports and SSL certs in `config/websockets.php`.

Security note

- The example uses private channels. Ensure `routes/channels.php` authorizes access correctly to prevent unauthorized access to conversations.

If you want, I can now:
- Add the broadcast auth snippet to `routes/channels.php` (safe change),
- Install and publish the `laravel-websockets` package locally (I can show the exact commands for you to run),
- Or implement UI improvements at the same time (avatars, better layout). Let me know which next step you prefer.