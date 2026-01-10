# Zoom Phone Integration with Taurus CRM

Complete guide to integrate Zoom Phone with your CRM for automatic call logging and dialing.

---

## 🎯 What You'll Get

- ✅ Click-to-call from CRM (dial leads directly)
- ✅ Automatic call logging (duration, status, recording)

## 📋 Prerequisites
   - Cost: ~$15-25/user/month
   
2. **Zoom Phone Numbers**
   - Purchase phone numbers from Zoom
   - Assign to users who will make calls

3. **Zoom Developer Account**
   - Free at https://marketplace.zoom.us
   - Used to create OAuth app

### Technical Requirements
- CRM deployed and accessible online (https://crm.taurustechnologies.co)
- Admin access to Zoom Account
- Admin access to CRM server
### Step 1: Create Zoom OAuth App (15 minutes)

#### 1.1 Go to Zoom App Marketplace
Visit: https://marketplace.zoom.us → Click **Develop** → **Build App**

- App Name: `Taurus CRM Integration`
- Company Name: `Taurus Technologies`

#### 1.5 Save Credentials
On the left sidebar of your Zoom App page, under **App Credentials**, you will see:
- **Client ID** (visible)
- **Client Secret** (hidden, click to reveal)

**Account ID:**
- For user-managed apps, Account ID is not always shown directly. If required, you can find it in your Zoom account profile (https://zoom.us/account/profile) or in the JWT/OAuth credentials section for account-level apps. For most CRM integrations, Client ID and Client Secret are sufficient.

Click **Continue**. Your app is now in **Local Test** mode (see top right). You do not need to activate or publish for internal CRM use.
- Developer Email: Your email
- Click **Create**

#### 1.3 Configure App Information
- **Short Description:** "CRM integration for call management"
- **Long Description:** "Integrates Zoom Phone with Taurus CRM for automatic call logging and click-to-dial functionality"
- Upload logo (optional)
- Click **Continue**

#### 1.4 Configure OAuth Settings

**OAuth Redirect URL:**
```
https://crm.taurustechnologies.co/zoom/callback
```

**Add OAuth allow lists:**
```
https://crm.taurustechnologies.co
```

**Scopes Required:**
- `phone:read` - Read phone call history
- `phone:write` - Make calls via API
- `phone:read:admin` - Read all phone data (admin)
- `user:read` - Read user information
- `recording:read` - Access call recordings

Click **Continue**

#### 1.5 Save Credentials
Copy these values (you'll need them):
- **Client ID:** `abcdef123456...`
- **Client Secret:** `xyz789secret...`
- **Account ID:** `abc123account...`

### Step 2: Configure CRM Environment (5 minutes)

1. **SSH to your CRM server:**
   ```bash
   ssh root@your-server-ip
   cd /var/www/taurus-crm
   ```

2. **Update .env file:**
   ```bash
   nano .env
   ```
   Add these lines (replace with your actual values):
   ```ini
   # Zoom Phone Integration
   ZOOM_CLIENT_ID=your_client_id_here
   ZOOM_CLIENT_SECRET=your_client_secret_here
   ZOOM_REDIRECT_URI=https://crm.taurustechnologies.co/zoom/callback

   # Zoom Phone Settings
   ZOOM_PHONE_ENABLED=true
   ZOOM_AUTO_DIAL_ENABLED=true
   ZOOM_CALL_RECORDING_ENABLED=true
   ```
   *(Account ID is not required for user-managed OAuth apps)*

   Save and exit (Ctrl+X, Y, Enter)

3. **Clear Laravel config/cache:**
   ```bash
   php artisan config:cache
   php artisan cache:clear
   ```

Click **Continue** → **Activate** app

---

### Step 2: Configure CRM Environment (5 minutes)

#### 2.1 SSH to Your Server
```bash
ssh root@75.119.145.66
cd /var/www/taurus-crm
```

#### 2.2 Update .env File
```bash
nano .env
```

Add these lines (replace with your actual values):
```ini
# Zoom Phone Integration
ZOOM_CLIENT_ID=your_client_id_here
ZOOM_CLIENT_SECRET=your_client_secret_here
ZOOM_ACCOUNT_ID=your_account_id_here
ZOOM_REDIRECT_URI=https://crm.taurustechnologies.co/zoom/callback

# Zoom Phone Settings
ZOOM_PHONE_ENABLED=true
ZOOM_AUTO_DIAL_ENABLED=true
ZOOM_CALL_RECORDING_ENABLED=true
```

Save and exit (Ctrl+X, Y, Enter)

#### 2.3 Clear Cache
```bash
php artisan config:cache
php artisan cache:clear
```

---

### Step 3: Add Zoom Routes (5 minutes)

#### 3.1 Edit Routes File
```bash
nano routes/web.php
```

Add these routes before the closing `});`:
```php
// Zoom Phone Integration
Route::group(['prefix' => 'zoom', 'as' => 'zoom.', 'middleware' => ['auth']], function () {
    Route::get('/authorize', [App\Http\Controllers\ZoomController::class, 'authorize'])->name('authorize');
    Route::get('/callback', [App\Http\Controllers\ZoomController::class, 'callback'])->name('callback');
    Route::post('/dial/{leadId}', [App\Http\Controllers\ZoomController::class, 'makeCall'])->name('dial');
    Route::get('/call-status/{callId}', [App\Http\Controllers\ZoomController::class, 'getCallStatus'])->name('status');
    Route::post('/webhook', [App\Http\Controllers\ZoomController::class, 'webhook'])->name('webhook');
});
```

---

### Step 4: Create Zoom Controller (10 minutes)

```bash
php artisan make:controller ZoomController
```

Edit the file:
```bash
nano app/Http/Controllers/ZoomController.php
```

Paste this complete controller:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ZoomToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomController extends Controller
{
    private $baseUrl = 'https://api.zoom.us/v2';
    
    /**
     * Redirect to Zoom OAuth
     */
    public function authorize()
    {
        $clientId = config('zoom.client_id');
        $redirectUri = config('zoom.redirect_uri');
        
        $url = "https://zoom.us/oauth/authorize?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}";
        
        return redirect($url);
    }
    
    /**
     * Handle OAuth callback
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return redirect()->route('root')->with('error', 'Zoom authorization failed');
        }
        
        // Exchange code for token
        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('zoom.redirect_uri'),
            'client_id' => config('zoom.client_id'),
            'client_secret' => config('zoom.client_secret'),
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            // Save token for this user
            ZoomToken::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
                ]
            );
            
            return redirect()->route('root')->with('success', 'Zoom Phone connected successfully!');
        }
        
        return redirect()->route('root')->with('error', 'Failed to connect Zoom Phone');
    }
    
    /**
     * Make a call to a lead
     */
    public function makeCall(Request $request, $leadId)
    {
        $lead = Lead::findOrFail($leadId);
        $token = $this->getValidToken();
        
        if (!$token) {
            return response()->json(['error' => 'Zoom not authorized. Please connect your Zoom account.'], 401);
        }
        
        // Get user's Zoom phone number
        $userResponse = Http::withToken($token)
            ->get($this->baseUrl . '/users/me');
        
        if (!$userResponse->successful()) {
            return response()->json(['error' => 'Failed to get user info'], 500);
        }
        
        $userData = $userResponse->json();
        
        // Make the call
        $callResponse = Http::withToken($token)
            ->post($this->baseUrl . '/phone/users/' . $userData['id'] . '/calls', [
                'to' => $lead->phone_number,
                'from' => $userData['phone_number'] ?? config('zoom.default_phone_number'),
            ]);
        
        if ($callResponse->successful()) {
            $callData = $callResponse->json();
            
            // Log call in database
            \App\Models\CallLog::create([
                'user_id' => Auth::id(),
                'lead_id' => $lead->id,
                'zoom_call_id' => $callData['id'],
                'phone_number' => $lead->phone_number,
                'status' => 'initiated',
                'started_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'call_id' => $callData['id'],
                'message' => 'Call initiated to ' . $lead->cn_name,
            ]);
        }
        
        return response()->json(['error' => 'Failed to initiate call'], 500);
    }
    
    /**
     * Get call status
     */
    public function getCallStatus($callId)
    {
        $token = $this->getValidToken();
        
        if (!$token) {
            return response()->json(['error' => 'Not authorized'], 401);
        }
        
        $response = Http::withToken($token)
            ->get($this->baseUrl . "/phone/calls/{$callId}");
        
        if ($response->successful()) {
            return response()->json($response->json());
        }
        
        return response()->json(['error' => 'Failed to get call status'], 500);
    }
    
    /**
     * Webhook receiver for call events
     */
    public function webhook(Request $request)
    {
        $event = $request->input('event');
        $payload = $request->input('payload');
        
        Log::info('Zoom webhook received', ['event' => $event, 'payload' => $payload]);
        
        // Handle different events
        switch ($event) {
            case 'phone.call_ended':
                $this->handleCallEnded($payload);
                break;
            case 'phone.call_log_completed':
                $this->handleCallLogCompleted($payload);
                break;
        }
        
        return response()->json(['status' => 'received']);
    }
    
    /**
     * Handle call ended event
     */
    private function handleCallEnded($payload)
    {
        $callLog = \App\Models\CallLog::where('zoom_call_id', $payload['object']['id'])->first();
        
        if ($callLog) {
            $callLog->update([
                'status' => 'completed',
                'duration' => $payload['object']['duration'] ?? 0,
                'ended_at' => now(),
            ]);
        }
    }
    
    /**
     * Handle call log completed (includes recording)
     */
    private function handleCallLogCompleted($payload)
    {
        $callLog = \App\Models\CallLog::where('zoom_call_id', $payload['object']['id'])->first();
        
        if ($callLog && isset($payload['object']['recording_url'])) {
            $callLog->update([
                'recording_url' => $payload['object']['recording_url'],
            ]);
        }
    }
    
    /**
        if (!$tokenRecord) {
            return null;
            // Refresh token
            $response = Http::asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $tokenRecord->refresh_token,
                'client_id' => config('zoom.client_id'),
                'client_secret' => config('zoom.client_secret'),
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $tokenRecord->update([
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
        
        return $tokenRecord->access_token;
    }
}
```
### Step 5: Create Database Tables (5 minutes)

#### 5.1 Create Migrations
```bash
php artisan make:migration create_zoom_tokens_table
php artisan make:migration add_zoom_fields_to_call_logs_table
```

#### 5.2 Edit zoom_tokens migration
```bash
nano database/migrations/*_create_zoom_tokens_table.php
```

```php
public function up()
{
    Schema::create('zoom_tokens', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->text('access_token');
        $table->text('refresh_token');
        $table->timestamp('expires_at');
        $table->timestamps();
        
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->unique('user_id');
    });
}
```

#### 5.3 Edit call_logs migration
```bash
nano database/migrations/*_add_zoom_fields_to_call_logs_table.php
```

```php
public function up()
{
    Schema::table('call_logs', function (Blueprint $table) {
        $table->string('zoom_call_id')->nullable()->after('id');
        $table->string('recording_url')->nullable()->after('duration');
        $table->index('zoom_call_id');
    });
}

public function down()
{
    Schema::table('call_logs', function (Blueprint $table) {
        $table->dropColumn(['zoom_call_id', 'recording_url']);
    });
}
```

#### 5.4 Run Migrations
```bash
php artisan migrate
```

---

### Step 6: Create ZoomToken Model (2 minutes)

```bash
php artisan make:model ZoomToken
```

Edit:
```bash
nano app/Models/ZoomToken.php
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomToken extends Model
{
    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

### Step 7: Add Click-to-Call Button in CRM (10 minutes)

#### 7.1 Edit Ravens Calling Page
```bash
nano resources/views/ravens/calling.blade.php
```

Find the leads table section and add a call button column. Look for where phone numbers are displayed and add:

```html
<!-- In the table header -->
<th>Actions</th>

<!-- In the table body (where leads are listed) -->
<td>
    <button onclick="makeZoomCall({{ $lead->id }}, '{{ $lead->phone_number }}')" 
            class="btn btn-sm btn-success">
        <i class="bx bx-phone-call"></i> Call
    </button>
</td>
```

#### 7.2 Add JavaScript for Zoom Calling
Add this at the bottom of ravens/calling.blade.php before `@endsection`:

```javascript
<script>
// Check if Zoom is connected
let zoomConnected = false;

// Check Zoom connection status on page load
window.addEventListener('DOMContentLoaded', function() {
    fetch('/zoom/status')
        .then(response => response.json())
        .then(data => {
            zoomConnected = data.connected;
            if (!zoomConnected) {
                showZoomConnectBanner();
            }
        })
        .catch(error => console.error('Error checking Zoom status:', error));
});

// Show banner to connect Zoom
function showZoomConnectBanner() {
    const banner = document.createElement('div');
    banner.className = 'alert alert-warning alert-dismissible fade show';
    banner.innerHTML = `
        <strong>Connect Zoom Phone</strong> to enable click-to-call functionality.
        <a href="/zoom/authorize" class="btn btn-sm btn-warning ms-2">Connect Now</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.card').insertAdjacentElement('beforebegin', banner);
}

// Make a Zoom call
function makeZoomCall(leadId, phoneNumber) {
    if (!zoomConnected) {
        toastr.warning('Please connect your Zoom Phone first');
        window.location.href = '/zoom/authorize';
        return;
    }
    
    // Show dialing indicator
    toastr.info('Dialing ' + phoneNumber + '...');
    
    // Make API call to initiate Zoom call
    fetch(`/zoom/dial/${leadId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            // Optionally open call modal or show call status
            pollCallStatus(data.call_id);
        } else {
            toastr.error(data.error || 'Failed to initiate call');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Failed to make call');
    });
}

// Poll call status
function pollCallStatus(callId) {
    const interval = setInterval(() => {
        fetch(`/zoom/call-status/${callId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'completed' || data.status === 'failed') {
                    clearInterval(interval);
                    if (data.status === 'completed') {
                        toastr.success('Call completed - Duration: ' + data.duration + 's');
                    }
                }
            })
            .catch(error => {
                clearInterval(interval);
                console.error('Error polling call status:', error);
            });
    }, 3000); // Poll every 3 seconds
    
    // Stop polling after 5 minutes
    setTimeout(() => clearInterval(interval), 300000);
}
</script>
```

---

### Step 8: Configure Zoom Webhooks (10 minutes)

#### 8.1 In Zoom App Marketplace
Go to your app → **Features** tab

#### 8.2 Add Event Subscriptions
- Click **Add Event Subscription**
- Subscription Name: `Call Events`
- Event notification endpoint URL:
  ```
  https://crm.taurustechnologies.co/zoom/webhook
  ```

#### 8.3 Subscribe to Events
Select these events:
- ✅ `Phone call ended`
- ✅ `Phone call log completed`
- ✅ `Phone recording completed`

#### 8.4 Validate Endpoint
Zoom will send a validation request. Your webhook endpoint must respond with the challenge token.

Add this to your `webhook()` method in ZoomController:
```php
// At the start of webhook() method
if ($request->has('challenge')) {
    return response()->json([
        'challenge' => $request->input('challenge')
    ]);
}
```

---

### Step 9: Test the Integration (10 minutes)

#### 9.1 Connect Zoom Account
1. Login to CRM as Super Admin
2. Visit: https://crm.taurustechnologies.co/zoom/authorize
3. Click **Authorize** on Zoom's OAuth page
4. You'll be redirected back to CRM

#### 9.2 Test Click-to-Call
1. Go to Ravens Calling page
2. Click the green **Call** button next to any lead
3. Your Zoom Phone should ring
4. Answer and the call will connect to the lead's number

#### 9.3 Verify Call Logging
1. After call ends, check `call_logs` table:
   ```bash
   mysql -u taurus_user -p taurus
   SELECT * FROM call_logs ORDER BY created_at DESC LIMIT 5;
   ```
2. You should see the call with duration and status

---

## 🎨 Advanced Features

### Auto-Dial Mode
Already implemented in ravens/calling.blade.php - the "Start Auto-Dial" button will automatically call leads from the queue using Zoom Phone.

### Call Recording Playback
Add this to lead detail page:
```php
@if($lead->callLogs()->whereNotNull('recording_url')->exists())
    <div class="card">
        <div class="card-header">Call Recordings</div>
        <div class="card-body">
            @foreach($lead->callLogs()->whereNotNull('recording_url')->get() as $call)
                <div class="mb-2">
                    <strong>{{ $call->created_at->format('M d, Y H:i') }}</strong>
                    ({{ $call->duration }}s)
                    <audio controls src="{{ $call->recording_url }}"></audio>
                </div>
            @endforeach
        </div>
    </div>
@endif
```

### Missed Call Notifications
Create a scheduled command to check for missed calls:
```bash
php artisan make:command CheckMissedCalls
```

---

## 🐛 Troubleshooting

### "Not Authorized" Error
**Solution:** User needs to connect Zoom account
```
Visit: /zoom/authorize
```

### Calls Not Connecting
**Check:**
1. User has Zoom Phone license
2. User has assigned phone number in Zoom
3. OAuth scopes include `phone:write`

**Debug:**
```bash
tail -f storage/logs/laravel.log | grep -i zoom
```

### Webhooks Not Received
**Check:**
1. Webhook URL is publicly accessible (HTTPS required)
2. Challenge validation is working
3. Event subscriptions are active

**Test webhook:**
```bash
curl -X POST https://crm.taurustechnologies.co/zoom/webhook \
  -H "Content-Type: application/json" \
  -d '{"event":"test","payload":{}}'
```

### Call Logs Not Saving
**Check:**
```sql
DESCRIBE call_logs;
-- Ensure zoom_call_id and recording_url columns exist
```

If missing:
```bash
php artisan migrate
```

---

## 💰 Cost Breakdown

### ✅ Your Current Setup
You already have **Zoom Phone Unlimited Calling Plan** for each user - perfect! This means:
- ✅ Unlimited calls included
- ✅ No per-minute charges
- ✅ Ready to integrate immediately
- ✅ Just need phone numbers assigned to users

### Additional Costs (if needed)
- **Additional phone numbers:** ~$10/month per local number
- **Toll-free numbers:** ~$5/month + usage fees
- **International calling:** May have additional charges depending on plan

**Your integration is ready to go - no additional calling costs!** 🎉

---

## 📱 Alternative: Twilio Integration

If Zoom Phone is too expensive, you can use Twilio instead:
- **Cost:** ~$1/number + $0.01/min
- **Features:** Same as Zoom (calls, SMS, recording)
- **Integration:** Similar process, different API

Let me know if you want Twilio integration docs instead!

---

## ✅ Checklist

- [ ] Zoom Phone licenses purchased
- [ ] OAuth app created in Zoom Marketplace
- [ ] Scopes added: phone:read, phone:write, phone:read:admin
- [ ] .env configured with Zoom credentials
- [ ] ZoomController created
- [ ] Routes added for /zoom/*
- [ ] Migrations run (zoom_tokens, call_logs updates)
- [ ] Click-to-call buttons added to UI
- [ ] Webhooks configured in Zoom
- [ ] Tested: Authorization flow works
- [ ] Tested: Click-to-call works
- [ ] Tested: Call logging works
- [ ] Tested: Webhooks received

---

## 📞 Support

### Zoom Support
- Developer Forum: https://devforum.zoom.us
- API Docs: https://marketplace.zoom.us/docs/api-reference

### CRM Integration Help
- Check logs: `tail -f storage/logs/laravel.log`
- Test endpoint: https://crm.taurustechnologies.co/zoom/authorize
- Webhook logs: Search for "Zoom webhook" in logs

---

**Ready to integrate?** Follow the steps above and you'll have Zoom Phone working with your CRM in about 1-2 hours!
