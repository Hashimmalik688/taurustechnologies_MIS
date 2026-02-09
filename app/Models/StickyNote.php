<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StickyNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'color',
        'position_x',
        'position_y',
        'z_index',
    ];

    /**
     * Get the user that owns the sticky note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
