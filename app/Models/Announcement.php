<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'animation',
        'background_color',
        'icon',
        'auto_dismiss',
        'is_active',
        'created_by',
        'community_id',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this announcement
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the community this announcement belongs to (if any)
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Scope to get only active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Get the current active announcement
     */
    public static function getCurrent()
    {
        return self::active()->latest('published_at')->first();
    }

    /**
     * Get animation class for CSS
     */
    public function getAnimationClass()
    {
        return 'announce-' . $this->animation;
    }

    /**
     * Get background color class
     */
    public function getBackgroundClass()
    {
        return 'announce-bg-' . $this->background_color;
    }

    /**
     * Get icon class
     */
    public function getIconClass()
    {
        $icons = [
            'warning' => 'bx-exclamation-circle',
            'info' => 'bx-info-circle',
            'important' => 'bx-star',
            'star' => 'bx-star-fill',
            'check' => 'bx-check-circle',
            'alert' => 'bx-bell',
        ];
        return 'bx ' . ($icons[$this->icon] ?? 'bx-info-circle');
    }
}
