<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a notification for a specific user.
     */
    public function createForUser(User $user, string $title, string $message, array $options = []): Notification
    {
        return Notification::createForUser($user->id, $title, $message, $options);
    }

    /**
     * Create a notification for multiple users.
     */
    public function createForUsers(Collection $users, string $title, string $message, array $options = []): Collection
    {
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push($this->createForUser($user, $title, $message, $options));
        }

        return $notifications;
    }

    /**
     * Create a notification for all users.
     */
    public function createForAllUsers(string $title, string $message, array $options = []): Collection
    {
        $users = User::all();

        return $this->createForUsers($users, $title, $message, $options);
    }

    /**
     * Create order-related notifications.
     */
    public function createOrderNotification(User $user, string $status, array $orderData = []): Notification
    {
        $notifications = [
            'placed' => [
                'title' => 'Order Placed Successfully',
                'message' => 'Your order #'.($orderData['order_number'] ?? 'N/A').' has been placed successfully.',
                'icon' => 'bx-cart',
                'color' => 'primary',
                'type' => 'success',
            ],
            'confirmed' => [
                'title' => 'Order Confirmed',
                'message' => 'Your order has been confirmed and is being processed.',
                'icon' => 'bx-check-circle',
                'color' => 'success',
                'type' => 'success',
            ],
            'shipped' => [
                'title' => 'Order Shipped',
                'message' => 'Your order has been shipped and is on its way.',
                'icon' => 'bx-package',
                'color' => 'info',
                'type' => 'info',
            ],
            'delivered' => [
                'title' => 'Order Delivered',
                'message' => 'Your order has been delivered successfully.',
                'icon' => 'bx-badge-check',
                'color' => 'success',
                'type' => 'success',
            ],
            'cancelled' => [
                'title' => 'Order Cancelled',
                'message' => 'Your order has been cancelled as requested.',
                'icon' => 'bx-x-circle',
                'color' => 'danger',
                'type' => 'warning',
            ],
        ];

        $notificationData = $notifications[$status] ?? $notifications['placed'];
        $notificationData['data'] = $orderData;

        return $this->createForUser($user, $notificationData['title'], $notificationData['message'], $notificationData);
    }

    /**
     * Create payment-related notifications.
     */
    public function createPaymentNotification(User $user, string $status, array $paymentData = []): Notification
    {
        $notifications = [
            'received' => [
                'title' => 'Payment Received',
                'message' => 'We have received your payment of $'.($paymentData['amount'] ?? '0'),
                'icon' => 'bx-money',
                'color' => 'success',
                'type' => 'success',
            ],
            'failed' => [
                'title' => 'Payment Failed',
                'message' => 'Your payment could not be processed. Please try again.',
                'icon' => 'bx-error-circle',
                'color' => 'danger',
                'type' => 'error',
            ],
            'refunded' => [
                'title' => 'Payment Refunded',
                'message' => 'Your refund of $'.($paymentData['amount'] ?? '0').' has been processed.',
                'icon' => 'bx-receipt',
                'color' => 'info',
                'type' => 'info',
            ],
        ];

        $notificationData = $notifications[$status] ?? $notifications['received'];
        $notificationData['data'] = $paymentData;

        return $this->createForUser($user, $notificationData['title'], $notificationData['message'], $notificationData);
    }

    /**
     * Create system notifications.
     */
    public function createSystemNotification(User $user, string $type, string $message, array $options = []): Notification
    {
        $systemNotifications = [
            'maintenance' => [
                'title' => 'System Maintenance',
                'icon' => 'bx-wrench',
                'color' => 'warning',
                'type' => 'warning',
            ],
            'update' => [
                'title' => 'System Update',
                'icon' => 'bx-upload',
                'color' => 'info',
                'type' => 'info',
            ],
            'security' => [
                'title' => 'Security Alert',
                'icon' => 'bx-shield',
                'color' => 'danger',
                'type' => 'error',
                'is_important' => true,
            ],
            'welcome' => [
                'title' => 'Welcome!',
                'icon' => 'bx-user-plus',
                'color' => 'success',
                'type' => 'success',
            ],
        ];

        $notificationData = $systemNotifications[$type] ?? $systemNotifications['update'];
        $notificationData['message'] = $message;
        $notificationData = array_merge($notificationData, $options);

        return $this->createForUser($user, $notificationData['title'], $notificationData['message'], $notificationData);
    }

    /**
     * Get notification statistics for a user.
     */
    public function getUserNotificationStats(User $user): array
    {
        $notifications = $user->notifications();

        return [
            'total' => $notifications->count(),
            'unread' => $notifications->unread()->count(),
            'read' => $notifications->read()->count(),
            'today' => $notifications->whereDate('created_at', today())->count(),
            'this_week' => $notifications->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),
            'this_month' => $notifications->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Clean old notifications (older than specified days).
     */
    public function cleanOldNotifications(int $days = 90): int
    {
        $deletedCount = Notification::where('created_at', '<', now()->subDays($days))->delete();

        return $deletedCount;
    }

    /**
     * Get notifications by type for a user.
     */
    public function getUserNotificationsByType(User $user, string $type): Collection
    {
        return $user->notifications()->where('type', $type)->get();
    }

    /**
     * Mark multiple notifications as read.
     */
    public function markMultipleAsRead(array $notificationIds, User $user): int
    {
        return $user->notifications()
            ->whereIn('id', $notificationIds)
            ->update(['read_at' => now()]);
    }
}
