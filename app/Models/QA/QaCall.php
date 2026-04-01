<?php

namespace App\Models\QA;

use App\Models\CallLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QaCall extends Model
{
    protected $table = 'qa_calls';

    protected $fillable = [
        'zoom_call_id',
        'call_log_id',
        'agent_user_id',
        'agent_name',
        'agent_email',
        'zoom_user_id',
        'zoom_call_log_id',
        'caller_number',
        'callee_number',
        'duration_seconds',
        'call_start_time',
        'recording_url',
        'zoom_transcript_url',
        'local_recording_path',
        'transcript_plain',
        'transcript_diarized',
        'transcript_source',
        'processing_status',
        'failure_reason',
        'scored_by',
        'retry_count',
        'assemblyai_transcript_id',
        'assemblyai_status',
        'audio_file_path',
        'audio_original_name',
    ];

    protected $casts = [
        'call_start_time' => 'datetime',
        'duration_seconds' => 'integer',
        'retry_count' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallLog::class, 'call_log_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    public function qaResult(): HasOne
    {
        return $this->hasOne(QaResult::class, 'qa_call_id');
    }

    public function complianceFlags(): HasMany
    {
        return $this->hasMany(QaComplianceFlag::class, 'qa_call_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('processing_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->whereIn('processing_status', ['downloading', 'transcribing', 'scoring']);
    }

    public function scopeForAgent($query, int $agentId)
    {
        return $query->where('agent_user_id', $agentId);
    }

    public function scopeInDateRange($query, string $range)
    {
        return match ($range) {
            'today' => $query->whereDate('call_start_time', today()),
            'week' => $query->where('call_start_time', '>=', now()->startOfWeek()),
            'month' => $query->where('call_start_time', '>=', now()->startOfMonth()),
            default => $query->whereDate('call_start_time', today()),
        };
    }
}
