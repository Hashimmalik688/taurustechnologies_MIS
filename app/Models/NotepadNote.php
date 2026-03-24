<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotepadNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'color',
        'is_pinned',
        'is_shared',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_shared' => 'boolean',
    ];

    /**
     * The user who owns this note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Users this note has been specifically shared with.
     */
    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'notepad_note_shares', 'note_id', 'user_id')->withTimestamps();
    }
}
