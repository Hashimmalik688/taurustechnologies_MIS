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

        // ── Only allow device registration from office/trusted networks ───
        // If the request has no cookie (i.e. first-time visit that would trigger
        // a new pending record), verify the IP is in an office network first.
        // Approved devices that already have a cookie bypass this — employees
        // working remotely are already approved and just need to be validated.
        $incomingToken = $request->cookie(self::COOKIE);
        if (empty($incomingToken) && ! $this->isFromAllowedNetwork($request->ip())) {
            return response('', 403);
        }

        $token = $request->cookie(self::COOKIE);
        $newCookie = null;

        // ── No cookie at all → first visit on this device ─────────────────
        if (empty($token)) {
            // If this IP already has a rejected entry, block immediately without
            // creating a new record (prevents re-registration after rejection).
            $existingRejected = AllowedDevice::where('last_seen_ip', $request->ip())
                ->where('status', 'rejected')
                ->exists();
            if ($existingRejected) {
                return $this->disabledResponse($request);
            }

            // If this IP already has a pending entry (e.g. different browser on
            // same machine), reuse that token instead of flooding the queue.
            $existingPending = AllowedDevice::where('last_seen_ip', $request->ip())
                ->where('status', 'pending')
                ->latest()
                ->first();
            if ($existingPending) {
                $token = $existingPending->device_token;
                $newCookie = $this->makeCookie($request, $token);
                $response = $this->pendingResponse($request, $token);
                return $newCookie ? $response->withCookie($newCookie) : $response;
            }

            $token = (string) Str::uuid();
            $newCookie = $this->makeCookie($request, $token);

            AllowedDevice::create([
                'device_token' => $token,
                'status'       => 'pending',
                'label'        => 'Auto-registered from ' . $request->ip(),
                'last_seen_ip' => $request->ip(),
                'last_seen_at' => now(),
            ]);

            $response = $this->pendingResponse($request, $token);
            return $newCookie ? $response->withCookie($newCookie) : $response;
        }

        // ── Token exists → look it up ──────────────────────────────────────
        $device = AllowedDevice::where('device_token', $token)->first();

        // Token in cookie but not in DB. Check if IP is already rejected before
        // issuing a new pending record.
        if (! $device) {
            $existingRejected = AllowedDevice::where('last_seen_ip', $request->ip())
                ->where('status', 'rejected')
                ->exists();
            if ($existingRejected) {
                return $this->disabledResponse($request);
            }

            $token = (string) Str::uuid();
            $newCookie = $this->makeCookie($request, $token);

            AllowedDevice::create([
                'device_token' => $token,
                'status'       => 'pending',
                'label'        => 'Auto-registered from ' . $request->ip(),
                'last_seen_ip' => $request->ip(),
                'last_seen_at' => now(),
            ]);

            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            $response = $this->pendingResponse($request, $token);
            return $newCookie ? $response->withCookie($newCookie) : $response;
        }

        // Permanently rejected — show disabled page, never allow re-registration
        if ($device->status === 'rejected') {
            return $this->disabledResponse($request);
        }

        // Pending – waiting for admin
        if ($device->status === 'pending') {
            // Keep last_seen_ip current so admin can identify the device
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

        // ── Approved ── update last-seen at most once per minute ──────────
        if (! $device->last_seen_at || $device->last_seen_at->diffInMinutes(now()) >= 1) {
            $device->updateQuietly([
                'last_seen_at' => now(),
                'last_seen_ip' => $request->ip(),
            ]);
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

    private function disabledResponse(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'This device has been disabled. Contact your administrator.'], 403);
        }

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Device Disabled</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;min-height:100vh}
    .c{text-align:center;max-width:480px;padding:40px}
    .s{width:80px;height:80px;margin:0 auto 24px;background:#dc3545;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px}
    h1{font-size:24px;font-weight:700;margin-bottom:10px;color:#fff}
    .m{font-size:15px;color:#9ca3af;line-height:1.6}
  </style>
</head>
<body>
  <div class="c">
    <div class="s">🔒</div>
    <h1>Device Disabled</h1>
    <p class="m">This device has been disabled by your administrator.<br>Contact them to regain access.</p>
  </div>
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
