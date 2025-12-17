<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallEvent extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'caller_number',
        'callee_number',
        'status',
        'lead_data',
        'webhook_data',
        'is_read',
        'event_time',
    ];

    protected $casts = [
        'lead_data' => 'array',
        'webhook_data' => 'array',
        'is_read' => 'boolean',
        'event_time' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
