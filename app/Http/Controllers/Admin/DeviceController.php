<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return redirect()->route('settings.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_token' => ['required', 'string', 'max:100', 'unique:allowed_devices,device_token'],
            'name'         => ['nullable', 'string', 'max:255'],
            'label'        => ['required', 'string', 'max:255'],
            'user_id'      => ['nullable', 'exists:users,id'],
        ]);

        AllowedDevice::create([
            'device_token' => $request->device_token,
            'name'         => $request->name,
            'label'        => $request->label,
            'user_id'      => $request->user_id,
            'added_by'     => auth()->id(),
            'status'       => 'approved',
        ]);

        return redirect()->back()->with('success', 'Device "' . $request->label . '" approved.');
    }

    public function approve(AllowedDevice $device)
    {
        $request = request();
        $request->validate([
            'name'  => ['nullable', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $device->update([
            'status' => 'approved',
            'added_by' => auth()->id(),
            'name'   => $request->name  ?: $device->name,
            'label'  => $request->label ?: $device->label,
        ]);

        return redirect()->back()->with('success', 'Device approved.');
    }

    public function update(Request $request, AllowedDevice $device)
    {
        $request->validate([
            'name'         => ['nullable', 'string', 'max:255'],
            'label'        => ['required', 'string', 'max:255'],
            'status'       => ['required', 'in:approved,pending,disabled,rejected'],
            'device_token' => ['required', 'string', 'max:100', 'unique:allowed_devices,device_token,' . $device->id],
            'user_id'      => ['nullable', 'exists:users,id'],
        ]);

        $device->update([
            'name'         => request('name'),
            'label'        => request('label'),
            'status'       => request('status'),
            'device_token' => request('device_token'),
            'user_id'      => request('user_id'),
        ]);

        return redirect()->back()->with('success', 'Device "' . $device->fresh()->label . '" updated.');
    }

    public function disable(AllowedDevice $device)
    {
        $device->update(['status' => 'disabled']);
        return redirect()->back()->with('success', 'Device "' . $device->label . '" disabled.');
    }

    public function enable(AllowedDevice $device)
    {
        $device->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Device "' . $device->label . '" re-enabled.');
    }

    public function destroy(AllowedDevice $device)
    {
        $label = $device->label;
        
        // If already rejected, permanently delete it
        if ($device->status === 'rejected') {
            $device->delete();
            return redirect()->back()->with('success', 'Device "' . $label . '" permanently deleted.');
        }
        
        // Otherwise, mark as rejected to block the token permanently
        $device->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Device "' . $label . '" rejected and permanently blocked.');
    }

    public function activate(Request $request)
    {
        $request->validate(['device_token' => ['required', 'string', 'max:100']]);

        $device = AllowedDevice::where('device_token', $request->device_token)
            ->where('status', 'approved')
            ->first();

        if (! $device) {
            return response('<!DOCTYPE html><html><body style="font-family:sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;height:100vh;text-align:center"><div><h2>Not Approved Yet</h2><p style="color:#9ca3af">Your token has not been approved by your administrator yet.<br>Please wait and try again.</p><a href="/" style="color:#68d391">← Go Back</a></div></body></html>', 403)->header('Content-Type', 'text/html');
        }

        // Auto-link device to authenticated user on activation
        if (auth()->check() && !$device->user_id) {
            $device->update(['user_id' => auth()->id()]);
        }

        $cookie = \Symfony\Component\HttpFoundation\Cookie::create(
            name:     \App\Http\Middleware\RestrictToAllowedDevice::COOKIE,
            value:    $device->device_token,
            expire:   time() + 60 * 60 * 24 * 365 * 5,
            path:     '/',
            secure:   $request->isSecure(),
            httpOnly: true,
            sameSite: 'Lax',
        );

        $device->updateQuietly(['last_seen_ip' => $request->ip(), 'last_seen_at' => now()]);

        return redirect('/')->withCookie($cookie);
    }

    public function myDevices(Request $request)
    {
        $cookieName = \App\Http\Middleware\RestrictToAllowedDevice::COOKIE;
        $token = $request->cookie($cookieName);
        $device = $token ? AllowedDevice::where('device_token', $token)->first() : null;

        return view('my-devices', compact('device', 'token'));
    }

    public function updateMyDeviceName(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        $cookieName = \App\Http\Middleware\RestrictToAllowedDevice::COOKIE;
        $token = $request->cookie($cookieName);
        $device = $token ? AllowedDevice::where('device_token', $token)->first() : null;

        if (! $device || $device->status !== 'approved') {
            return redirect()->back()->with('error', 'Device not found or not approved.');
        }

        $device->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Device name updated.');
    }

    public function getToken()
    {
        // Generate a UUID for the user (persisted in localStorage on client)
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Get Your Device Token</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
    .container{text-align:center;max-width:520px;background:#2d3748;border-radius:12px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,0.5)}
    .icon{width:80px;height:80px;margin:0 auto 24px;background:#556ee6;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px}
    h1{font-size:24px;font-weight:700;margin-bottom:10px;color:#fff}
    .desc{font-size:15px;color:#9ca3af;line-height:1.6;margin-bottom:30px}
    .token-box{background:#1a202c;border:1px solid #4a5568;border-radius:10px;padding:20px;margin-bottom:24px;text-align:left}
    .token-label{display:block;color:#f0f0f0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;color:#9ca3af}
    .token-value{display:block;word-break:break-all;background:#0a0d13;padding:12px 14px;border-radius:6px;font-family:"Courier New",monospace;font-size:13px;color:#68d391;user-select:all;border:1px solid #4a5568;margin-bottom:10px}
    .token-hint{font-size:13px;color:#9ca3af;line-height:1.5}
    .steps{text-align:left;background:#3a4556;border-radius:10px;padding:20px;margin-bottom:24px;font-size:14px;line-height:2;color:#9ca3af}
    .steps strong{color:#e4e6eb;font-weight:600}
    .buttons{display:flex;gap:12px;flex-wrap:wrap;justify-content:center}
    .btn{display:inline-block;border:none;color:#e4e6eb;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;font-weight:500;transition:all .2s ease}
    .btn-copy{background:#4a5568}
    .btn-copy:hover{background:#718096}
    .btn-send{background:linear-gradient(135deg,#2563eb,#1d4ed8)}
    .btn-send:hover{background:linear-gradient(135deg,#1d4ed8,#1e40af)}
  </style>
</head>
<body>
  <div class="container">
    <div class="icon">🔑</div>
    <h1>Your Device Token</h1>
    <p class="desc">Send this token to your administrator to get your device approved.</p>

    <div class="token-box">
      <label class="token-label">Device Token</label>
      <code class="token-value" id="tok"></code>
      <small class="token-hint">This token is saved on your device. Bookmark this page.</small>
    </div>

    <div class="steps">
      <div>1. <strong>Copy</strong> the token above</div>
      <div>2. <strong>Send</strong> it to your admin via email or message</div>
      <div>3. Admin approves it in <strong>Settings → Approve a Device</strong></div>
      <div>4. Come back and click <strong>Activate</strong></div>
    </div>

    <div class="buttons">
      <button class="btn btn-copy" onclick="copyToken()">📋 Copy Token</button>
      <button class="btn btn-send" onclick="openMail()">✉️ Send to Admin</button>
      <button class="btn btn-send" onclick="activate()">✓ Activate (after approval)</button>
    </div>

    <form id="activateForm" action="/device/activate" method="POST" style="display:none">
      <input type="hidden" name="_token" id="csrf">
      <input type="hidden" name="device_token" id="activateToken">
    </form>
  </div>

  <script>
    var tok = null;
    (function(){
      var k = "cdvt_pending";
      tok = localStorage.getItem(k);
      if(!tok){ tok = ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g,function(c){return(c^crypto.getRandomValues(new Uint8Array(1))[0]&15>>c/4).toString(16)}); localStorage.setItem(k, tok); }
      document.getElementById("tok").textContent = tok;
    })();
    function copyToken(){
      navigator.clipboard.writeText(document.getElementById("tok").textContent.trim()).then(function(){
        var b=event.target; b.textContent="✓ Copied!";
        setTimeout(function(){b.textContent="📋 Copy Token"},2000);
      });
    }
    function openMail(){
      var tokenText = document.getElementById("tok").textContent.trim();
      var subject = encodeURIComponent("Device Token for Taurus CRM Approval");
      var body = encodeURIComponent("Hi Admin,\n\nPlease approve my device token:\n\n" + tokenText + "\n\nThank you");
      window.location.href = "mailto:?subject=" + subject + "&body=" + body;
    }
    function activate(){
      document.getElementById("activateToken").value = tok;
      fetch("/sanctum/csrf-cookie").then(function(){
        document.getElementById("activateForm").submit();
      });
    }
  </script>
</body>
</html>';

        return response($html)->header('Content-Type', 'text/html');
    }
}
