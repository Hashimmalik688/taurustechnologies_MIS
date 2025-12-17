# Chat System Setup - 100% Local, No External APIs

Your CRM has a fully local chat system with text, images, and audio support. No Pusher, no SaaS, no external dependencies.

## Features
- ✅ Text messages
- ✅ Image attachments
- ✅ Audio attachments (MP3, WAV, etc.)
- ✅ Real-time updates via local Reverb server
- ✅ Local backups to `storage/app/chat-backups/`
- ✅ Works offline (no internet required)

## Quick Start

### 1. Start the Local WebSocket Server (Reverb)
```powershell
php artisan reverb:start
```
Leave this running in one terminal.

### 2. Start the Laravel Application
```powershell
php artisan serve
```
Leave this running in another terminal.

### 3. Access Chat
Navigate to `/chat` in your browser. Messages will appear instantly.

## Configuration

All settings are in `.env` (already configured):

```env
BROADCAST_DRIVER=reverb

# Local WebSocket Server (Reverb)
REVERB_APP_ID=local-app
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

## Backup Chat Data

Export all chat data (conversations, messages, attachments) to JSON:

```powershell
# Basic backup (JSON only)
php artisan chat:backup

# Backup with attachment file copies
php artisan chat:backup --copy-attachments
```

Backups are stored in `storage/app/chat-backups/<timestamp>/`.

## Production Deployment

On your production server:

1. Update `.env` with your server's IP/domain:
   ```env
   REVERB_HOST=your-server-ip-or-domain
   REVERB_SCHEME=https  # if using SSL
   ```

2. Run Reverb as a background service (using systemd, supervisor, or Windows Task Scheduler):
   ```bash
   php artisan reverb:start
   ```

3. Ensure port 8080 is accessible to your users (or change to another port).

## Troubleshooting

**Chat messages don't appear instantly:**
- Verify Reverb is running: `php artisan reverb:start`
- Check browser console (F12) for connection errors
- Ensure port 8080 isn't blocked by firewall

**Fallback to Polling:**
If you don't need real-time updates, you can disable Reverb:
```env
BROADCAST_DRIVER=log
```
The chat will work with 5-second polling instead.

## Technical Details

- **Backend:** Laravel 11 API routes (`/api/chat/*`)
- **WebSocket Server:** Laravel Reverb (native to Laravel 11)
- **Frontend:** Vanilla JavaScript with Laravel Echo
- **Storage:** Local MySQL database + `storage/public/chat-attachments/`
- **Broadcasting:** Private channels per conversation for security

No external services. Everything runs on your server.
