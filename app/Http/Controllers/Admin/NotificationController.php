<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications with pagination and grouping.
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $type = $request->get('type', 'all'); // all, unread, read

        $query = auth()->user()->notifications()->latest();

        // Filter by type
        switch ($type) {
            case 'unread':
                $query->unread();
                break;
            case 'read':
                $query->read();
                break;
        }

        $notifications = $query->paginate($perPage);

        // Group notifications by date
        $groupedNotifications = $notifications->getCollection()->groupBy(function ($notification) {
            return $notification->date_group;
        });

        // Add labels for each group
        $groupedWithLabels = $groupedNotifications->map(function ($notifications, $date) {
            return [
                'date' => $date,
                'label' => $notifications->first()->date_group_label,
                'notifications' => $notifications,
            ];
        });

        return view('admin.notifications.index', [
            'groupedNotifications' => $groupedWithLabels,
            'notifications' => $notifications,
            'currentType' => $type,
            'unreadCount' => auth()->user()->notifications()->unread()->count(),
            'totalCount' => auth()->user()->notifications()->count(),
        ]);
    }

    /**
     * Get notifications for topbar (most recent 5).
     */
    public function topbar()
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = auth()->user()->notifications()->unread()->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'time_ago' => $notification->time_ago,
                    'is_read' => $notification->isRead(),
                    'data' => $notification->data,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Ensure user can only mark their own notifications
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'unread_count' => auth()->user()->notifications()->unread()->count(),
        ]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as unread',
            'unread_count' => auth()->user()->notifications()->unread()->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Get unread count.
     */
    public function unreadCount(): JsonResponse
    {
        $count = auth()->user()->notifications()->unread()->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Create a test notification (for development).
     */
    public function createTest(): JsonResponse
    {
        $types = [
            [
                'title' => 'Your order is placed',
                'message' => 'If several languages coalesce the grammar',
                'type' => 'success',
                'icon' => 'bx-cart',
                'color' => 'primary',
            ],
            [
                'title' => 'Your item is shipped',
                'message' => 'If several languages coalesce the grammar',
                'type' => 'info',
                'icon' => 'bx-badge-check',
                'color' => 'success',
            ],
            [
                'title' => 'Payment received',
                'message' => 'Your payment has been processed successfully',
                'type' => 'success',
                'icon' => 'bx-money',
                'color' => 'success',
            ],
        ];

        $randomType = $types[array_rand($types)];

        Notification::createForUser(
            auth()->id(),
            $randomType['title'],
            $randomType['message'],
            [
                'type' => $randomType['type'],
                'icon' => $randomType['icon'],
                'color' => $randomType['color'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Test notification created',
        ]);
    }
}
