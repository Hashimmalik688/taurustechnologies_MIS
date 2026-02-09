<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'avatar',
        'created_by',
        'posting_restricted',
    ];

    protected $casts = [
        'posting_restricted' => 'boolean',
    ];

    /**
     * Get the user who created this community
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get announcements for this community
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Get community announcements (new system)
     */
    public function communityAnnouncements()
    {
        return $this->hasMany(CommunityAnnouncement::class);
    }

    /**
     * Get chat conversations for this community
     */
    public function chatConversations()
    {
        return $this->hasMany(ChatConversation::class);
    }

    /**
     * Get members of this community
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'community_members', 'community_id', 'user_id')
            ->withPivot('added_by', 'can_post')
            ->withTimestamps();
    }

    /**
     * Scope: Only communities created by managers
     */
    public static function createdByManagers()
    {
        return self::whereHas('creator', function ($query) {
            $query->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'Manager');
            });
        });
    }
}
