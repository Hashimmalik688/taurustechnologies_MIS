<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSaleCreatedNotification implements ShouldQueue
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
    public function handle(SaleCreated $event): void
    {
        $lead = $event->lead;
        $closerName = $event->closerName;
        
        $title = 'New Sale Completed';
        $message = "New sale completed by {$closerName} for {$lead->cn_name} - {$lead->carrier_name}";
        
        // Notify managers
        $managers = User::role('Manager')->get();
        
        foreach ($managers as $manager) {
            Notification::createForUser(
                $manager->id,
                $title,
                $message,
                [
                    'type' => 'success',
                    'icon' => 'bx-check-circle',
                    'color' => 'success',
                    'is_important' => true,
                    'data' => [
                        'lead_id' => $lead->id,
                        'lead_name' => $lead->cn_name,
                        'carrier_name' => $lead->carrier_name,
                        'closer_name' => $closerName,
                        'sale_amount' => $lead->monthly_premium ?? 0,
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
                    'type' => 'success',
                    'icon' => 'bx-check-circle',
                    'color' => 'success',
                    'is_important' => true,
                    'data' => [
                        'lead_id' => $lead->id,
                        'lead_name' => $lead->cn_name,
                        'carrier_name' => $lead->carrier_name,
                        'closer_name' => $closerName,
                        'sale_amount' => $lead->monthly_premium ?? 0,
                    ],
                ]
            );
        }
    }
}
