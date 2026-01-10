<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLeadCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead;
        $title = 'New Lead Created';
        $message = "New lead for {$lead->cn_name} has been created.";
        
        // Notify managers
        $managers = User::role('Manager')->get();
        
        foreach ($managers as $manager) {
            Notification::createForUser(
                $manager->id,
                $title,
                $message,
                [
                    'type' => 'info',
                    'icon' => 'bx-user-plus',
                    'color' => 'info',
                    'data' => [
                        'lead_id' => $lead->id,
                        'lead_name' => $lead->cn_name,
                        'phone_number' => $lead->phone_number,
                    ],
                ]
            );
        }

        // Notify assigned employee if one is assigned
        if (!empty($lead->assigned_to)) {
            Notification::createForUser(
                $lead->assigned_to,
                $title,
                $message,
                [
                    'type' => 'info',
                    'icon' => 'bx-user-plus',
                    'color' => 'info',
                    'data' => [
                        'lead_id' => $lead->id,
                        'lead_name' => $lead->cn_name,
                        'phone_number' => $lead->phone_number,
                    ],
                ]
            );
        }
    }
}
