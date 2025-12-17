<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users or create a test user
        $users = User::all();

        if ($users->isEmpty()) {
            // Create a test user if none exist
            $users = collect([
                User::create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'password' => bcrypt('password'),
                ]),
            ]);
        }

        $notificationTypes = [
            [
                'title' => 'Your order is placed',
                'message' => 'If several languages coalesce the grammar of the resulting language is more simple.',
                'type' => 'success',
                'icon' => 'bx-cart',
                'color' => 'primary',
                'is_important' => false,
            ],
            [
                'title' => 'James Lemire',
                'message' => 'It will seem like simplified English, as a skeptical Cambridge friend.',
                'type' => 'info',
                'icon' => null,
                'color' => 'secondary',
                'is_important' => false,
            ],
            [
                'title' => 'Your item is shipped',
                'message' => 'If several languages coalesce the grammar of the resulting language.',
                'type' => 'info',
                'icon' => 'bx-badge-check',
                'color' => 'success',
                'is_important' => false,
            ],
            [
                'title' => 'Payment Received',
                'message' => 'Your payment of $299.00 has been processed successfully.',
                'type' => 'success',
                'icon' => 'bx-money',
                'color' => 'success',
                'is_important' => true,
            ],
            [
                'title' => 'Salena Layfield',
                'message' => 'As a skeptical Cambridge friend of mine occidental.',
                'type' => 'info',
                'icon' => null,
                'color' => 'secondary',
                'is_important' => false,
            ],
            [
                'title' => 'Security Alert',
                'message' => 'New login detected from unknown device. Please verify if this was you.',
                'type' => 'warning',
                'icon' => 'bx-shield',
                'color' => 'warning',
                'is_important' => true,
            ],
            [
                'title' => 'Profile Updated',
                'message' => 'Your profile information has been updated successfully.',
                'type' => 'success',
                'icon' => 'bx-user',
                'color' => 'info',
                'is_important' => false,
            ],
            [
                'title' => 'System Maintenance',
                'message' => 'Scheduled maintenance will occur tonight from 2:00 AM to 4:00 AM.',
                'type' => 'warning',
                'icon' => 'bx-wrench',
                'color' => 'warning',
                'is_important' => true,
            ],
            [
                'title' => 'Welcome to our platform!',
                'message' => 'Thank you for joining us. Explore all the amazing features we have to offer.',
                'type' => 'success',
                'icon' => 'bx-user-plus',
                'color' => 'success',
                'is_important' => false,
            ],
            [
                'title' => 'Newsletter Subscription',
                'message' => 'You have successfully subscribed to our weekly newsletter.',
                'type' => 'info',
                'icon' => 'bx-mail-send',
                'color' => 'info',
                'is_important' => false,
            ],
        ];

        foreach ($users as $user) {
            // Create notifications for different time periods
            foreach ($notificationTypes as $index => $notificationType) {
                $createdAt = $this->getRandomDate($index);
                $readAt = $this->shouldBeRead($index) ? $createdAt->addMinutes(rand(5, 120)) : null;

                Notification::create([
                    'user_id' => $user->id,
                    'title' => $notificationType['title'],
                    'message' => $notificationType['message'],
                    'type' => $notificationType['type'],
                    'icon' => $notificationType['icon'],
                    'color' => $notificationType['color'],
                    'is_important' => $notificationType['is_important'],
                    'read_at' => $readAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }

    /**
     * Get a random date for notification creation.
     */
    private function getRandomDate(int $index): Carbon
    {
        $dates = [
            Carbon::now()->subMinutes(5),      // 5 minutes ago
            Carbon::now()->subMinutes(30),     // 30 minutes ago
            Carbon::now()->subHours(1),       // 1 hour ago
            Carbon::now()->subHours(3),       // 3 hours ago
            Carbon::yesterday()->subHours(2), // Yesterday
            Carbon::yesterday()->subHours(5), // Yesterday
            Carbon::now()->subDays(2),        // 2 days ago
            Carbon::now()->subDays(3),        // 3 days ago
            Carbon::now()->subWeek(),         // 1 week ago
            Carbon::now()->subWeeks(2),       // 2 weeks ago
        ];

        return $dates[$index % count($dates)];
    }

    /**
     * Determine if notification should be marked as read.
     */
    private function shouldBeRead(int $index): bool
    {
        // Make older notifications more likely to be read
        $readProbabilities = [0.2, 0.3, 0.5, 0.7, 0.8, 0.9, 0.9, 0.95, 0.95, 1.0];
        $probability = $readProbabilities[$index % count($readProbabilities)];

        return rand(1, 100) <= ($probability * 100);
    }
}
