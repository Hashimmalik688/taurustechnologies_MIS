<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notify_on_message',
        'notify_on_mention',
        'notify_sound_enabled',
        'notify_desktop',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'push_subscription',
    ];

    protected $casts = [
        'notify_on_message' => 'boolean',
        'notify_on_mention' => 'boolean',
        'notify_sound_enabled' => 'boolean',
        'notify_desktop' => 'boolean',
        'quiet_hours_enabled' => 'boolean',
    ];

    /**
     * Get the user this preference belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
