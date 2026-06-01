<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AllowedDevice;
use App\Models\Setting;

class RestrictToAllowedDevice
{
    /**
     * Cookie name. Kept short and opaque — EncryptCookies middleware
     * transparently encrypts the value so the browser only sees a signed blob.
     * HttpOnly = JS cannot read or steal it.
     */
    const COOKIE = 'cdvt'; // "crm device token" — deliberately undescriptive

    /**
     * Routes that bypass device checks entirely (webhooks, etc.)
     */
    protected array $except = [
        'zoom/webhook',
        'device/activate',
        'api/zoom-webhook',
    ];

    /**
     * Known bot/crawler/scanner User-Agent keywords.
     * Requests matching these never get a cookie or a DB record — they just get 403.
     */
    protected array $botSignatures = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'adsbot',
        'bingpreview', 'facebookexternalhit', 'ia_archiver', 'wget', 'curl',
        'python-requests', 'go-http-client', 'java/', 'libwww',
        'masscan', 'zgrab', 'nuclei', 'nmap', 'sqlmap', 'nikto',
        'scanner', 'shodan', 'censys', 'internet-measurement',
        'httpclient', 'okhttp', 'axios/', 'nessus', 'qualys',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // ── Silently block known bots/scanners — no cookie, no DB record ──
        $ua = strtolower($request->userAgent() ?? '');
        foreach ($this->botSignatures as $sig) {
            if (str_contains($ua, $sig)) {
                return response('', 403);
            }
        }

        // ── Also block headless/empty UA (scanners without a UA string) ───
        if (empty($ua)) {
            return response('', 403);
        }

        // ── Token check first — approved tokens bypass IP restriction ────────
        // This allows remote users with dynamic IPs to access the system once
        // their device token has been approved by an admin.
        $token  = $request->cookie(self::COOKIE);
        $device = null;

        if (! empty($token)) {
            $device = AllowedDevice::where('device_token', $token)->first();

            if ($device && $device->status === 'approved') {
                if (! $device->last_seen_at || $device->last_seen_at->diffInMinutes(now()) >= 1) {
                    $device->updateQuietly([
                        'last_seen_at' => now(),
                        'last_seen_ip' => $request->ip(),
                    ]);
                }

                return $next($request);
            }
        }

        // ── IP check — gates everyone without an approved token ───────────
        if (! $this->isFromAllowedNetwork($request->ip())) {
            return response('', 403);
        }

        // ── No token → show registration page (IP already cleared above) ──
        if (empty($token)) {
            return $this->notRegisteredResponse($request);
        }

        // Token in cookie but not in DB → treat as unregistered
        if (! $device) {
            return $this->notRegisteredResponse($request);
        }

        // Rejected or pending — show disabled page
        if (in_array($device->status, ['rejected', 'pending'])) {
            return $this->disabledResponse($request, $token);
        }

        // Pending – waiting for admin
        if ($device->status === 'pending') {
            $device->updateQuietly(['last_seen_ip' => $request->ip()]);

            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return $this->pendingResponse($request, $token);
        }

        // Disabled by admin
        if ($device->status === 'disabled') {
            \App\Models\AuditLog::logAction(
                action: 'blocked_access',
                user: Auth::user(),
                description: "Access blocked — device [{$device->label}] has been disabled by admin"
            );

            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return $this->disabledResponse($request);
        }

        return $next($request);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function makeCookie(Request $request, string $token): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::make(
            name:     self::COOKIE,
            value:    $token,
            minutes:  60 * 24 * 365 * 5,   // 5-year lifetime
            path:     '/',
            domain:   null,
            secure:   $request->isSecure(),
            httpOnly: true,                 // ← JS cannot read this
            raw:      false,
            sameSite: 'Lax',
        );
    }

    private function pendingResponse(Request $request, string $token): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Device pending admin approval.'], 403);
        }

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Device Pending Approval</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;min-height:100vh}
    .c{text-align:center;max-width:520px;padding:40px}
    .s{width:80px;height:80px;margin:0 auto 24px;background:#f6c90e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px}
    h1{font-size:24px;font-weight:700;margin-bottom:10px;color:#fff}
    .m{font-size:15px;color:#9ca3af;line-height:1.6;margin-bottom:20px}
    .t{background:#2d3748;border:1px solid #4a5568;border-radius:10px;padding:18px 20px;margin-bottom:20px;text-align:left}
    .t strong{display:block;color:#f0f0f0;font-size:13px;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
    .t code{display:block;word-break:break-all;background:#1a202c;padding:10px 14px;border-radius:6px;font-family:"Courier New",monospace;font-size:13px;color:#68d391;margin-bottom:10px;user-select:all}
    .t small{color:#9ca3af;font-size:13px;line-height:1.5}
    .btn{display:inline-block;background:#4a5568;border:none;color:#e4e6eb;padding:9px 20px;border-radius:8px;font-size:14px;cursor:pointer;margin-top:8px;text-decoration:none}
    .btn:hover{background:#718096}
    .btn-refresh{background:linear-gradient(135deg,#2563eb,#1d4ed8);margin-left:8px}
    .btn-refresh:hover{background:linear-gradient(135deg,#1d4ed8,#1e40af)}
  </style>
</head>
<body>
  <div class="c">
    <div class="s">⏳</div>
    <h1>Device Pending Approval</h1>
    <p class="m">This device has not been approved yet.<br>Your administrator needs to approve it before you can log in.</p>
    <div class="t">
      <strong>Your Device Token</strong>
      <code id="tok">' . e($token) . '</code>
      <small>Send this token to your administrator. Once they approve it, refresh this page.</small>
    </div>
    <button class="btn" onclick="copyToken()">Copy Token</button>
    <a href="javascript:location.reload()" class="btn btn-refresh">Refresh After Approval</a>
  </div>
  <script>
    function copyToken(){
      navigator.clipboard.writeText(document.getElementById("tok").textContent.trim()).then(function(){
        var btn=document.querySelector(".btn");
        btn.textContent="Copied!";
        setTimeout(function(){btn.textContent="Copy Token"},2000);
      });
    }
  </script>
</body>
</html>';

        return response($html, 403)->header('Content-Type', 'text/html');
    }

    private function disabledResponse(Request $request, ?string $token = null): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'This device has been disabled. Contact your administrator.'], 403);
        }

        $tokenBlock = $token
            ? '<div class="t">
      <strong>Blocked Device Token</strong>
      <code id="tok">' . e($token) . '</code>
      <small>Send this token to your administrator so they can identify and re-enable your device.</small>
    </div>
    <button class="btn" onclick="copyToken()">Copy Token</button>'
            : '<p class="m" style="margin-top:16px">No device token found — your administrator will need to identify your device by IP address.</p>';

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Device Disabled</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;min-height:100vh}
    .c{text-align:center;max-width:520px;padding:40px}
    .s{width:80px;height:80px;margin:0 auto 24px;background:#dc3545;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px}
    h1{font-size:24px;font-weight:700;margin-bottom:10px;color:#fff}
    .m{font-size:15px;color:#9ca3af;line-height:1.6}
    .t{background:#2d3748;border:1px solid #4a5568;border-radius:10px;padding:18px 20px;margin:20px 0;text-align:left}
    .t strong{display:block;color:#f0f0f0;font-size:13px;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
    .t code{display:block;word-break:break-all;background:#1a202c;padding:10px 14px;border-radius:6px;font-family:"Courier New",monospace;font-size:13px;color:#fc8181;margin-bottom:10px;user-select:all}
    .t small{color:#9ca3af;font-size:13px;line-height:1.5}
    .btn{display:inline-block;background:#4a5568;border:none;color:#e4e6eb;padding:9px 20px;border-radius:8px;font-size:14px;cursor:pointer;margin-top:8px}
    .btn:hover{background:#718096}
  </style>
</head>
<body>
  <div class="c">
    <div class="s">🔒</div>
    <h1>Device Disabled</h1>
    <p class="m">This device has been disabled by your administrator.<br>Contact them to regain access.</p>
    ' . $tokenBlock . '
  </div>
  <script>
    function copyToken(){
      navigator.clipboard.writeText(document.getElementById("tok").textContent.trim()).then(function(){
        var btn=document.querySelector(".btn");btn.textContent="Copied!";
        setTimeout(function(){btn.textContent="Copy Token"},2000);
      });
    }
  </script>
</body>
</html>';

        return response($html, 403)->header('Content-Type', 'text/html');
    }

    private function notRegisteredResponse(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Device not registered. Contact your administrator.'], 403);
        }

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Device Not Registered</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;min-height:100vh}
    .c{text-align:center;max-width:520px;padding:40px}
    .s{width:80px;height:80px;margin:0 auto 24px;background:#556ee6;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px}
    h1{font-size:24px;font-weight:700;margin-bottom:10px;color:#fff}
    .m{font-size:15px;color:#9ca3af;line-height:1.6;margin-bottom:20px}
    .t{background:#2d3748;border:1px solid #4a5568;border-radius:10px;padding:18px 20px;margin-bottom:20px;text-align:left}
    .t strong{display:block;color:#f0f0f0;font-size:13px;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
    .t code{display:block;word-break:break-all;background:#1a202c;padding:10px 14px;border-radius:6px;font-family:"Courier New",monospace;font-size:13px;color:#68d391;margin-bottom:10px;user-select:all}
    .t small{color:#9ca3af;font-size:13px;line-height:1.5}
    .steps{text-align:left;background:#2d3748;border-radius:10px;padding:16px 20px;margin-bottom:20px;font-size:14px;color:#9ca3af;line-height:2}
    .steps span{color:#e4e6eb;font-weight:600}
    .btn{display:inline-block;background:#4a5568;border:none;color:#e4e6eb;padding:9px 20px;border-radius:8px;font-size:14px;cursor:pointer}
    .btn:hover{background:#718096}
    .btn-activate{background:linear-gradient(135deg,#2563eb,#1d4ed8);margin-left:8px}
    .btn-activate:hover{background:linear-gradient(135deg,#1d4ed8,#1e40af)}
    .act-input{width:100%;background:#1a202c;border:1px solid #4a5568;border-radius:8px;padding:10px 14px;font-family:"Courier New",monospace;font-size:13px;color:#e4e6eb;margin-top:10px}
    .act-input:focus{outline:none;border-color:#556ee6}
  </style>
</head>
<body>
  <div class="c">
    <div class="s">🖥️</div>
    <h1>Device Not Registered</h1>
    <p class="m">This device has not been registered with the system.<br>Follow the steps below to get access.</p>

    <div class="t">
      <strong>Your Device Token</strong>
      <code id="tok"></code>
      <small>Copy this token and send it to your administrator to get approved.</small>
    </div>

    <div class="steps">
      <div>1. <span>Copy</span> the token above and send it to your admin</div>
      <div>2. Admin adds it in <span>Settings → Allowed Devices</span></div>
      <div>3. Come back and click <span>Activate</span> below</div>
    </div>

    <button class="btn" onclick="copyToken()">Copy Token</button>
    <button class="btn btn-activate" onclick="activate()">✓ Activate (after admin approves)</button>

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
      navigator.clipboard.writeText(tok).then(function(){
        var b=document.querySelectorAll(".btn")[0]; b.textContent="Copied!";
        setTimeout(function(){b.textContent="Copy Token"},2000);
      });
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

        return response($html, 403)->header('Content-Type', 'text/html');
    }

    /**
     * Returns true if the IP belongs to one of the configured office/trusted networks.
     * Reads from the same `office_networks` setting used by AttendanceService.
     * If no networks are configured, falls back to allowing all IPs (open registration).
     */
    private function isFromAllowedNetwork(string $ip): bool
    {
        $networks = Setting::get('office_networks', []);

        if (is_string($networks)) {
            $networks = array_filter(array_map('trim', explode(',', $networks)));
        }

        // No networks configured → don't lock anyone out; behave as before.
        if (empty($networks)) {
            return true;
        }

        foreach ($networks as $network) {
            $network = trim($network);
            if (empty($network)) {
                continue;
            }

            if (strpos($network, '/') === false) {
                // Plain IP match
                if ($ip === $network) {
                    return true;
                }
            } else {
                // CIDR range
                [$subnet, $bits] = explode('/', $network, 2);
                $ipLong     = ip2long($ip);
                $subnetLong = ip2long($subnet);
                if ($ipLong !== false && $subnetLong !== false) {
                    $mask = -1 << (32 - (int) $bits);
                    if (($ipLong & $mask) === ($subnetLong & $mask)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
