<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'color',
        'data',
        'read_at',
        'is_important',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'is_important' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for recent notifications (last 30 days).
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays(30));
    }

    /**
     * Scope for important notifications.
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Get time ago formatted string.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get formatted date for grouping.
     */
    public function getDateGroupAttribute(): string
    {
        return $this->created_at->format('Y-m-d');
    }

    /**
     * Get human readable date for grouping.
     */
    public function getDateGroupLabelAttribute(): string
    {
        $date = $this->created_at;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        if ($date->isSameDay($today)) {
            return 'Today';
        } elseif ($date->isSameDay($yesterday)) {
            return 'Yesterday';
        } elseif ($date->isCurrentWeek()) {
            return $date->format('l'); // Day name (e.g., Monday)
        } else {
            return $date->format('M d, Y');
        }
    }

    /**
     * Create a new notification for a user.
     */
    public static function createForUser($userId, $title, $message, $options = [])
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $options['type'] ?? 'info',
            'icon' => $options['icon'] ?? null,
            'color' => $options['color'] ?? 'primary',
            'data' => $options['data'] ?? null,
            'is_important' => $options['is_important'] ?? false,
        ]);
    }
}
