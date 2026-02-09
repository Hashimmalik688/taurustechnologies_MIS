<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityAnnouncement extends Model
{
    protected $table = 'community_announcements';

    protected $fillable = [
        'community_id',
        'title',
        'message',
        'priority',
        'created_by',
        'is_active',
        'show_in_banner',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_banner' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the community that owns this announcement
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the user who created this announcement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get active announcements only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: Get announcements that should show in banner
     */
    public function scopeForBanner($query)
    {
        return $query->where('show_in_banner', true)->active();
    }

    /**
     * Check if announcement is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'urgent' => 'red',
            'warning' => 'yellow',
            'info' => 'blue',
            default => 'blue',
        };
    }

    /**
     * Get priority icon for UI
     */
    public function getPriorityIcon(): string
    {
        return match($this->priority) {
            'urgent' => 'exclamation-circle',
            'warning' => 'alert-circle',
            'info' => 'info-circle',
            default => 'info-circle',
        };
    }
}
