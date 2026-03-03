<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'zoom_call_id',
        'call_session_id',
        'caller_number',
        'caller_did_number',
        'caller_name',
        'caller_email',
        'caller_user_id',
        'caller_extension',
        'callee_number',
        'callee_did_number',
        'callee_name',
        'callee_email',
        'callee_user_id',
        'callee_extension',
        'call_type',
        'call_status',
        'call_result',
        'call_start_time',
        'call_end_time',
        'duration_seconds',
        'answer_time',
        'ringing_start_time',
        'recording_url',
        'recording_id',
        'recording_file_path',
        'recording_file_size',
        'recording_type',
        'recording_start_time',
        'recording_end_time',
        'transcript_text',
        'transcript_url',
        'transcript_file_path',
        'call_cost',
        'call_rate',
        'lead_id',
        'agent_id',
        'matched_call_log_id',
        'raw_payload',
        'is_processed',
        'processing_notes',
        'processed_at',
    ];

    protected $casts = [
        'call_start_time' => 'datetime',
        'call_end_time' => 'datetime',
        'answer_time' => 'datetime',
        'ringing_start_time' => 'datetime',
        'recording_start_time' => 'datetime',
        'recording_end_time' => 'datetime',
        'processed_at' => 'datetime',
        'raw_payload' => 'array',
        'is_processed' => 'boolean',
        'duration_seconds' => 'integer',
        'recording_file_size' => 'integer',
        'call_cost' => 'decimal:4',
    ];

    /**
     * Get the lead associated with this call (if matched).
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the agent associated with this call (if matched).
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the matched call log from MIS (if exists).
     */
    public function matchedCallLog()
    {
        return $this->belongsTo(CallLog::class, 'matched_call_log_id');
    }

    /**
     * Scope for calls with recordings.
     */
    public function scopeWithRecording($query)
    {
        return $query->whereNotNull('recording_url');
    }

    /**
     * Scope for calls with transcripts.
     */
    public function scopeWithTranscript($query)
    {
        return $query->whereNotNull('transcript_text');
    }

    /**
     * Scope for answered calls.
     */
    public function scopeAnswered($query)
    {
        return $query->where('call_status', 'answered')
                     ->orWhere('call_result', 'Call connected');
    }

    /**
     * Scope for missed calls.
     */
    public function scopeMissed($query)
    {
        return $query->whereIn('call_status', ['missed', 'no_answer', 'voicemail']);
    }

    /**
     * Scope for calls by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('call_start_time', [$from, $to]);
    }

    /**
     * Scope for specific event types.
     */
    public function scopeEventType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope for unprocessed logs.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Get formatted duration as MM:SS.
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration_seconds <= 0) {
            return '00:00';
        }
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->recording_file_size) {
            return 'N/A';
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->recording_file_size;
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if call was answered/connected.
     */
    public function wasAnswered()
    {
        return in_array($this->call_status, ['answered', 'connected', 'completed']) 
            || $this->call_result === 'Call connected'
            || $this->duration_seconds > 0;
    }

    /**
     * Get display phone number (prioritize caller or callee based on call type).
     */
    public function getDisplayPhoneAttribute()
    {
        if ($this->call_type === 'inbound') {
            return $this->caller_number ?? $this->caller_did_number;
        }
        return $this->callee_number ?? $this->callee_did_number;
    }

    /**
     * Get display name.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->call_type === 'inbound') {
            return $this->caller_name;
        }
        return $this->callee_name;
    }

    /**
     * Check if this call is linked to MIS.
     */
    public function isLinkedToMis()
    {
        return $this->lead_id !== null || $this->agent_id !== null;
    }
}
