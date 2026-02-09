<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;
use App\Models\Attendance;
use App\Models\Partner;
use Carbon\Carbon;

class LogUserLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $user = $event->user;

            // Check if this is a Partner logout - if so, skip logging
            if ($user instanceof Partner) {
                return;
            }

            // Update logout time on User model for historical reference
            $user->update([
                'time_out' => now(),
            ]);

            // DISABLED: Do not auto-checkout on logout
            // This prevents issues when WiFi drops, PC turns off, or user logs out
            // Checkout must be done manually via the checkout button
            
            // Log the logout action
            AuditLog::logAction(
                action: 'logout',
                user: $user,
                model: 'User',
                model_id: $user->id,
                description: "User logged out from IP {$user->current_session_ip}"
            );
        }
    }
}
