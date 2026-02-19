<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class RestrictToOfficeNetwork
{
    /**
     * Handle an incoming request.
     *
     * Blocks ALL requests from IPs not in the allowed office networks.
     * No login page, no password reset, no assets — nothing.
     * If you're not on the office network, you see a static "Access Denied" page only.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Get allowed networks from settings (cached for 1 hour via Setting::get)
        $allowedNetworks = Setting::get('office_networks', []);

        // If no networks are configured, block everyone (safe default — never leave open)
        if (empty($allowedNetworks)) {
            // Force logout if somehow authenticated
            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return $this->blockedResponse($request, $ip);
        }

        // Normalize: if stored as comma-separated string
        if (is_string($allowedNetworks)) {
            $allowedNetworks = array_filter(array_map('trim', explode(',', $allowedNetworks)));
        }

        // Check if current IP is in any allowed network
        foreach ($allowedNetworks as $network) {
            $network = trim($network);
            if (empty($network)) {
                continue;
            }
            if ($this->ipInRange($ip, $network)) {
                return $next($request);
            }
        }

        // IP not allowed — log if authenticated, then force logout
        if (Auth::check()) {
            $user = Auth::user();

            \App\Models\AuditLog::logAction(
                action: 'blocked_access',
                user: $user,
                model: 'User',
                model_id: $user->id,
                description: "Access blocked — IP {$ip} is not in any allowed office network"
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->blockedResponse($request, $ip);
    }

    /**
     * Check if an IP is within a network range (exact match or CIDR notation).
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === trim($range);
        }

        [$subnet, $bits] = explode('/', $range, 2);
        $bits = (int) $bits;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long(trim($subnet));

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = -1 << (32 - $bits);
        $subnetLong &= $mask;

        return ($ipLong & $mask) === $subnetLong;
    }

    /**
     * Return a fully self-contained blocked response.
     * No layouts, no Blade includes, no external assets — pure inline HTML/CSS.
     * Nothing for inspect element to exploit.
     */
    private function blockedResponse(Request $request, string $ip): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Access denied. Your IP address is not authorized.',
            ], 403);
        }

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Access Denied</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#1a1d21;color:#e4e6eb;display:flex;align-items:center;justify-content:center;min-height:100vh}
.c{text-align:center;max-width:480px;padding:40px}
.s{width:80px;height:80px;margin:0 auto 24px;background:#dc3545;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px}
h1{font-size:28px;font-weight:700;margin-bottom:12px;color:#fff}
.m{font-size:16px;color:#9ca3af;line-height:1.6;margin-bottom:24px}
.b{display:inline-block;background:#2d3748;border:1px solid #4a5568;border-radius:6px;padding:8px 16px;font-family:"Courier New",monospace;font-size:14px;color:#f56565;margin-bottom:24px}
.h{font-size:13px;color:#6b7280;line-height:1.5}
</style>
</head>
<body>
<div class="c">
<div class="s">&#128274;</div>
<h1>Access Denied</h1>
<p class="m">Your network is not authorized to access this system.<br>This application can only be accessed from the office network.</p>
<div class="b">Your IP: ' . e($ip) . '</div>
<p class="h">If you believe this is an error, contact your system administrator.</p>
</div>
</body>
</html>';

        return response($html, 403)
            ->header('Content-Type', 'text/html')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
