<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;

class AccountSwitchingDetector
{
    /**
     * Detect if someone is rapidly switching between accounts
     * 
     * @param int $userId Current user logging in
     * @param string $ipAddress IP address
     * @param string $userAgent Browser fingerprint
     * @return array ['is_suspicious' => bool, 'message' => string, 'suspect_user_id' => int|null]
     */
    public static function detectSuspiciousLogin($userId, $ipAddress, $userAgent)
    {
        // Get the last login from same IP and browser within last 2 minutes
        $lastLogin = AuditLog::where('action', 'login')
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->where('user_id', '!=', $userId) // Different user
            ->where('created_at', '>', Carbon::now()->subMinutes(2))
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastLogin) {
            return [
                'is_suspicious' => false,
                'message' => null,
                'suspect_user_id' => null
            ];
        }

        // Someone else logged in from same device within 2 minutes
        $secondsAgo = Carbon::now()->diffInSeconds($lastLogin->created_at);
        $previousUser = User::find($lastLogin->user_id);
        $currentUser = User::find($userId);

        return [
            'is_suspicious' => true,
            'message' => "ALERT: Account switching detected! {$previousUser->name} logged in {$secondsAgo}s ago from this device, now {$currentUser->name} is logging in.",
            'suspect_user_id' => $lastLogin->user_id,
            'previous_user' => $previousUser->name,
            'seconds_between' => $secondsAgo
        ];
    }

    /**
     * Log suspicious account switching for admin review
     */
    public static function logSuspiciousActivity($currentUserId, $previousUserId, $ipAddress, $userAgent, $secondsBetween)
    {
        \Log::warning('Suspicious account switching detected', [
            'current_user_id' => $currentUserId,
            'previous_user_id' => $previousUserId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'seconds_between_logins' => $secondsBetween,
            'timestamp' => Carbon::now()
        ]);
    }
}
