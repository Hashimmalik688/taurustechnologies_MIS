<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'agent_id',
        'created_by',
        'phone_number',
        'call_type',
        'call_status',
        'call_start_time',
        'call_end_time',
        'duration_seconds',
        'outcome',
        'recording_url',
        'notes',
        'summary',
        'follow_up_date',
        'needs_follow_up',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'call_start_time' => 'datetime',
        'call_end_time' => 'datetime',
        'follow_up_date' => 'datetime',
        'needs_follow_up' => 'boolean',
        'duration_seconds' => 'integer',
    ];

    /**
     * Get the lead associated with the call log.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the agent who made the call.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the user who created this call log.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for calls needing follow-up.
     */
    public function scopeNeedsFollowUp($query)
    {
        return $query->where('needs_follow_up', true)
                     ->whereNull('follow_up_date')
                     ->orWhere('follow_up_date', '<=', now());
    }

    /**
     * Scope for calls by agent.
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope for successful calls.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('call_status', 'completed')
                     ->whereIn('outcome', ['interested', 'information_sent', 'sale_made', 'callback_requested']);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
