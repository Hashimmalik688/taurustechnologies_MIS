<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Http\Middleware\RestrictToAllowedDevice;
use Carbon\Carbon;

class AccountSwitchingDetector
{
    /**
     * Detect if someone is rapidly switching between accounts.
     * Primary signal: device token (cdvt cookie). Fallback: IP + User-Agent.
     */
    public static function detectSuspiciousLogin($userId, $ipAddress, $userAgent)
    {
        $deviceToken = request()->cookie(RestrictToAllowedDevice::COOKIE);

        // Build the base query — different user, last 2 minutes, login action
        $query = AuditLog::where('action', 'login')
            ->where('user_id', '!=', $userId)
            ->where('created_at', '>', Carbon::now()->subMinutes(2))
            ->orderBy('created_at', 'desc');

        // Prefer device token match (much more precise than IP/UA)
        if (!empty($deviceToken)) {
            $lastLogin = (clone $query)->where('device_fingerprint', $deviceToken)->first();
        } else {
            $lastLogin = (clone $query)
                ->where('ip_address', $ipAddress)
                ->where('user_agent', $userAgent)
                ->first();
        }

        if (!$lastLogin) {
            return ['is_suspicious' => false, 'message' => null, 'suspect_user_id' => null];
        }

        $secondsAgo    = Carbon::now()->diffInSeconds($lastLogin->created_at);
        $previousUser  = User::find($lastLogin->user_id);
        $currentUser   = User::find($userId);
        $signal        = !empty($deviceToken) ? 'device token' : 'IP + browser';

        return [
            'is_suspicious'   => true,
            'message'         => "ALERT: Account switching detected via {$signal}! "
                . "{$previousUser->name} logged in {$secondsAgo}s ago from this device, now {$currentUser->name} is logging in.",
            'suspect_user_id' => $lastLogin->user_id,
            'previous_user'   => $previousUser->name,
            'seconds_between' => $secondsAgo,
            'signal'          => $signal,
        ];
    }

    /**
     * Log suspicious account switching for admin review.
     */
    public static function logSuspiciousActivity($currentUserId, $previousUserId, $ipAddress, $userAgent, $secondsBetween)
    {
        $deviceToken = request()->cookie(RestrictToAllowedDevice::COOKIE);

        \Log::warning('Suspicious account switching detected', [
            'current_user_id'          => $currentUserId,
            'previous_user_id'         => $previousUserId,
            'ip_address'               => $ipAddress,
            'user_agent'               => $userAgent,
            'device_token'             => $deviceToken,
            'seconds_between_logins'   => $secondsBetween,
            'timestamp'                => Carbon::now(),
        ]);
    }
}
