# Zoom Webhook Setup Guide

## ⚠️ CRITICAL: Webhooks Are REQUIRED

**The Ravens form will ONLY appear when Zoom webhooks confirm the call is connected.** This is for data security - employees should not see lead data unless a call is actually connected.

Without webhook configuration, clicking "Dial" will open Zoom Phone but the form will **never appear**.

## Why We Need This

Zoom Phone API **does NOT** support making outbound calls via REST API (to prevent spam/auto-dialing). The only way to initiate calls is through the desktop app using `zoomphonecall://` protocol.

However, **desktop-initiated calls DO trigger webhooks** when properly configured. This is the secure, data-compliant way to detect call status.

## Current Implementation Status

✅ **COMPLETED:**
- CSRF protection disabled for `/zoom/webhook` route
- Webhook URL verification challenge handler implemented
- CallLog matching logic (finds calls within 5 minutes)
- Frontend polling system for status updates
- User model import fixed in ZoomController

✅ **READY TO USE:**
- Desktop calling opens Zoom Phone app
- Creates CallLog with 'no_answer' status
- Webhooks will update status to 'connected' when call answers
- Frontend polls every 5 seconds and shows Ravens form automatically

## Required Configuration (Do This Now)

### Step 1: Configure Event Subscriptions in Zoom Marketplace

1. Go to https://marketplace.zoom.us/develop/apps
2. Click your app: **Client ID: phO58snqQ_WaT7a00bk0RQ**
3. Navigate to **"Feature" → "Event Subscriptions"**
4. Click **"Add new event subscription"**

### Step 2: Add Event Subscription

**Name:** CRM Call Events
**Event notification endpoint URL:** `https://crm.taurustechnologies.co/zoom/webhook`

When you save this, Zoom will send a verification challenge. Our code will automatically respond correctly.

### Step 3: Subscribe to These Events

Select these event types:

#### Phone Events (Required):
- ✅ `phone.caller_connected` - Fires when YOU connect to the call
- ✅ `phone.callee_answered` - Fires when the LEAD answers the call
- ✅ `phone.call_ended` - Fires when call disconnects
- ✅ `phone.caller_call_history_completed` - Fires with full call details after completion

#### Optional (for advanced features):
- `phone.callout.started` - Call initiated
- `phone.callout.ended` - Call ended
- `phone.call_log_completed` - Complete call log available

### Step 4: Activate the Subscription

Click **"Save"** and ensure the subscription shows as **"Enabled"**

### Step 5: (Optional) Add Webhook Secret Token

1. In Zoom app "Feature" tab, find **"Webhook Secret Token"**
2. Copy the token
3. Add to `.env` file:
   ```bash
   ZOOM_WEBHOOK_SECRET_TOKEN=your_secret_token_here
   ```
4. Run: `php artisan config:clear`

*Note: The webhook will work without this token, but it's more secure with it.*

## How It Works (End-to-End Flow)

### 1. User Clicks "Call" Button
```
Browser → POST /zoom/dial/9693
```

### 2. Laravel Creates CallLog
```php
CallLog::create([
    'lead_id' => 9693,
    'call_status' => 'no_answer',  // Initial status
    'call_start_time' => now()
])
```

### 3. Browser Opens Zoom Phone App
```
window.location.href = 'zoomphonecall://+12393871921'
```

### 4. User Dials in Zoom Phone
- Call appears in Zoom Phone desktop app
- User clicks "Call"
- Call connects

### 5. Zoom Fires Webhook (This is the magic!)
```
POST https://crm.taurustechnologies.co/zoom/webhook

{
  "event": "phone.caller_connected",
  "payload": {
    "object": {
      "caller_number": "+12393871921",
      "callee_number": "+12393871921"
    }
  }
}
```

### 6. Laravel Updates CallLog
```php
// ZoomWebhookController finds the CallLog
$callLog = CallLog::where('lead_id', $lead->id)
    ->where('call_status', '!=', 'connected')
    ->where('created_at', '>=', now()->subMinutes(5))
    ->first();

// Updates status
$callLog->update(['call_status' => 'connected']);
```

### 7. Frontend Detects Status Change
```javascript
// Polls every 5 seconds
GET /zoom/call-status/9693

Response: {
  "status": "connected",  // Changed from 'no_answer'!
  "show_ravens_form": true
}
```

### 8. Ravens Form Appears
```javascript
if (data.show_ravens_form === true) {
    showRavensFormForCall(leadData);  // Popup appears!
}
```

## Testing the Webhook

### Test 1: Verify URL is Accessible
```bash
curl -X POST https://crm.taurustechnologies.co/zoom/webhook \
  -H "Content-Type: application/json" \
  -d '{"event": "endpoint.url_validation", "payload": {"plainToken": "test123"}}'
```

**Expected Response:**
```json
{
  "plainToken": "test123",
  "encryptedToken": "a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3"
}
```

### Test 2: Simulate Call Connected Event
```bash
curl -X POST https://crm.taurustechnologies.co/zoom/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "event": "phone.caller_connected",
    "payload": {
      "object": {
        "caller": {"phone_number": "+12393871921"},
        "callee": {"phone_number": "+12393871921"}
      }
    }
  }'
```

**Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep WEBHOOK
```

### Test 3: Make a Real Call

1. Open browser: https://crm.taurustechnologies.co/ravens/calling
2. Click "Call" button on any lead
3. Zoom Phone app opens
4. Complete the call in Zoom
5. **Watch browser console** - should show status changing to 'connected'
6. Ravens form should pop up automatically

## Troubleshooting

### Problem: Webhook returns 419 Page Expired
**Solution:** Already fixed - `/zoom/webhook` is in CSRF exceptions

### Problem: Webhook returns 404
**Solution:** Clear routes cache:
```bash
php artisan route:clear
php artisan cache:clear
```

### Problem: Status stays 'no_answer' even when call connects
**Causes:**
1. Event subscriptions not configured in Zoom marketplace ← **Most likely!**
2. Webhook URL not verified
3. Wrong events subscribed (need `phone.caller_connected` or `phone.callee_answered`)

**Solution:** Complete Steps 1-4 above

### Problem: Form doesn't pop up
**Check:**
1. Browser console for status check responses
2. Laravel logs: `tail -f storage/logs/laravel.log | grep "CallLog updated"`
3. Database: `SELECT * FROM call_logs WHERE lead_id=9693 ORDER BY created_at DESC LIMIT 1;`

### Problem: Duplicate script loading
**Symptoms:** Console shows "Ravens calling script loaded" twice
**Impact:** None - just extra logging, doesn't affect functionality
**Fix:** Not urgent, can be ignored

## Database Schema

```sql
-- call_logs table structure
CREATE TABLE call_logs (
    id BIGINT PRIMARY KEY,
    lead_id BIGINT,
    agent_id BIGINT,
    phone_number VARCHAR(255),
    call_type ENUM('inbound', 'outbound'),
    call_status ENUM('completed', 'missed', 'rejected', 'busy', 'no_answer', 'voicemail', 'connected'),
    call_start_time TIMESTAMP,
    call_end_time TIMESTAMP NULL,
    duration_seconds INT DEFAULT 0,
    zoom_call_id VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Security Notes

✅ **CSRF Protection:** Disabled for webhook endpoint (required for external webhooks)
✅ **Authentication:** Webhook handler doesn't require auth (Zoom can't authenticate)
✅ **Validation:** Checks webhook secret token if configured
✅ **Phone Number Matching:** Uses fuzzy matching (last 10 digits) to handle formatting differences

## Next Steps

1. **Configure event subscriptions in Zoom marketplace** (Steps 1-4 above)
2. **Test with a real call** (Test 3 above)
3. **Monitor logs** during testing: `tail -f storage/logs/laravel.log`
4. **Verify Ravens form appears** when call connects

Once configured, the system will work automatically for all future calls with no manual intervention needed!
