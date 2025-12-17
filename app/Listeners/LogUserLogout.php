<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;

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

            // Update logout time
            $user->update([
                'time_out' => now(),
            ]);

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
