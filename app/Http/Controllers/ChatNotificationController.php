<?php

namespace App\Http\Controllers;

use App\Models\ChatNotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get notification preferences for the current user
     */
    public function getPreferences()
    {
        $userId = Auth::id();
        
        $preferences = ChatNotificationPreference::where('user_id', $userId)
            ->first() ?? ChatNotificationPreference::create([
                'user_id' => $userId,
                'notify_on_message' => true,
                'notify_on_mention' => true,
                'notify_sound_enabled' => true,
                'notify_desktop' => true,
                'quiet_hours_enabled' => false,
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '08:00',
            ]);

        return response()->json([
            'success' => true,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'notify_on_message' => 'boolean',
            'notify_on_mention' => 'boolean',
            'notify_sound_enabled' => 'boolean',
            'notify_desktop' => 'boolean',
            'quiet_hours_enabled' => 'boolean',
            'quiet_hours_start' => 'date_format:H:i',
            'quiet_hours_end' => 'date_format:H:i',
        ]);

        $userId = Auth::id();
        
        $preferences = ChatNotificationPreference::updateOrCreate(
            ['user_id' => $userId],
            $request->only([
                'notify_on_message',
                'notify_on_mention',
                'notify_sound_enabled',
                'notify_desktop',
                'quiet_hours_enabled',
                'quiet_hours_start',
                'quiet_hours_end',
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
            'preferences' => $preferences,
        ]);
    }

    /**
     * Request permission for desktop notifications
     */
    public function requestPermission()
    {
        return response()->json([
            'success' => true,
            'message' => 'Permission request handled by browser',
        ]);
    }

    /**
     * Check if user should receive notifications based on preferences
     */
    public function shouldNotify(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'is_mention' => 'boolean',
        ]);

        $userId = $request->user_id;
        $isMention = $request->is_mention ?? false;

        $preferences = ChatNotificationPreference::where('user_id', $userId)->first();

        if (!$preferences) {
            return response()->json(['should_notify' => true]);
        }

        // Check if notifications are enabled
        if ($isMention && !$preferences->notify_on_mention) {
            return response()->json(['should_notify' => false]);
        }

        if (!$isMention && !$preferences->notify_on_message) {
            return response()->json(['should_notify' => false]);
        }

        // Check quiet hours
        if ($preferences->quiet_hours_enabled && $this->isInQuietHours($preferences)) {
            return response()->json(['should_notify' => false]);
        }

        return response()->json(['should_notify' => true]);
    }

    /**
     * Check if current time is within quiet hours
     */
    private function isInQuietHours(ChatNotificationPreference $preferences): bool
    {
        $now = now();
        $currentTime = $now->format('H:i');
        $start = $preferences->quiet_hours_start;
        $end = $preferences->quiet_hours_end;

        if ($start < $end) {
            // Normal case: 08:00 - 22:00
            return $currentTime >= $start && $currentTime < $end;
        } else {
            // Overnight case: 22:00 - 08:00
            return $currentTime >= $start || $currentTime < $end;
        }
    }

    /**
     * Store notification subscription (for push notifications)
     */
    public function subscribeToNotifications(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'p256dh' => 'required|string',
            'auth' => 'required|string',
        ]);

        $userId = Auth::id();

        $subscription = ChatNotificationPreference::where('user_id', $userId)->first();
        
        if ($subscription) {
            $subscription->update([
                'push_subscription' => json_encode([
                    'endpoint' => $request->endpoint,
                    'keys' => [
                        'p256dh' => $request->p256dh,
                        'auth' => $request->auth,
                    ],
                ]),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription saved',
        ]);
    }

    /**
     * Get notification settings view
     */
    public function settingsView()
    {
        $preferences = ChatNotificationPreference::where('user_id', Auth::id())
            ->first() ?? ChatNotificationPreference::create([
                'user_id' => Auth::id(),
                'notify_on_message' => true,
                'notify_on_mention' => true,
                'notify_sound_enabled' => true,
                'notify_desktop' => true,
                'quiet_hours_enabled' => false,
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '08:00',
            ]);

        return view('chat.notifications.settings', [
            'preferences' => $preferences,
        ]);
    }
}
