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
     * Supports both @[Full Name] (multi-word) and @word (single-word) formats
     */
    public function getMentionedUsers()
    {
        $mentions = [];
        
        // Find @[Full Name] patterns (multi-word mentions)
        if (preg_match_all('/@\[([^\]]+)\]/', $this->message, $matches)) {
            $mentions = array_merge($mentions, $matches[1] ?? []);
        }
        
        // Find @word patterns (single-word mentions, including @everyone)
        // Remove the bracketed mentions first to avoid double-matching
        $cleaned = preg_replace('/@\[[^\]]+\]/', '', $this->message);
        if (preg_match_all('/@(\w+)/', $cleaned, $matches)) {
            $mentions = array_merge($mentions, $matches[1] ?? []);
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
        
        // Get user IDs by name (case-insensitive match)
        return User::where(function ($query) use ($mentions) {
            foreach ($mentions as $mention) {
                $query->orWhere('name', 'LIKE', $mention);
            }
        })->pluck('id')->toArray();
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
        
        // Check both exact name match and case-insensitive match
        foreach ($mentions as $mention) {
            if (strcasecmp($mention, $user->name) === 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Parse message with mention highlighting
     */
    public function getParsedMessage()
    {
        $parsed = $this->message;
        
        // Highlight @[Full Name] mentions first
        $parsed = preg_replace_callback(
            '/@\[([^\]]+)\]/',
            function($matches) {
                $mention = $matches[1];
                return '<span class="mention-highlight">@' . htmlspecialchars($mention) . '</span>';
            },
            $parsed
        );
        
        // Highlight @word mentions (including @everyone)
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
