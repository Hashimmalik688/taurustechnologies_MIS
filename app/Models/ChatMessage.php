<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'message',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['user', 'attachments'];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Get the user who sent this message
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all attachments for this message
     */
    public function attachments()
    {
        return $this->hasMany(ChatAttachment::class, 'message_id');
    }

    /**
     * Extract @mentions from message
     * Returns array of usernames mentioned in the message
     */
    public function getMentionedUsers()
    {
        $mentions = [];
        
        // Find @username patterns (including @everyone)
        if (preg_match_all('/@(\w+)/', $this->message, $matches)) {
            $mentions = $matches[1] ?? [];
        }
        
        return array_unique($mentions);
    }

    /**
     * Get mentioned user IDs
     */
    public function getMentionedUserIds()
    {
        $mentions = $this->getMentionedUsers();
        
        if (in_array('everyone', $mentions)) {
            // Return all users in the conversation
            return $this->conversation->users()->pluck('user_id')->toArray();
        }
        
        // Get user IDs by username
        return User::whereIn('name', $mentions)->pluck('id')->toArray();
    }

    /**
     * Check if message mentions a specific user
     */
    public function mentionsUser(User $user)
    {
        $mentions = $this->getMentionedUsers();
        
        if (in_array('everyone', $mentions)) {
            return true;
        }
        
        return in_array($user->name, $mentions);
    }

    /**
     * Parse message with mention highlighting
     */
    public function getParsedMessage()
    {
        $parsed = $this->message;
        
        // Highlight @mentions
        $parsed = preg_replace_callback(
            '/@(\w+)/',
            function($matches) {
                $mention = $matches[1];
                return '<span class="mention-highlight">@' . htmlspecialchars($mention) . '</span>';
            },
            $parsed
        );
        
        return $parsed;
    }

    /**
     * Get all read receipts for this message
     */
    public function reads()
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id');
    }

    /**
     * Check if message is read by a specific user
     */
    public function isReadBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}
