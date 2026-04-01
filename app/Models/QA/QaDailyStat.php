<?php

namespace App\Models\QA;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaDailyStat extends Model
{
    protected $table = 'qa_daily_stats';

    protected $fillable = [
        'agent_user_id',
        'stat_date',
        'calls_scored',
        'avg_score',
        'min_score',
        'max_score',
        'compliance_fails',
        'void_risks',
        'excellent_count',
        'exceptional_count',
        'good_count',
        'average_count',
        'poor_count',
        'avg_opening',
        'avg_discovery',
        'avg_presentation',
        'avg_objection_handling',
        'avg_closing',
        'avg_soft_skills',
        'avg_call_control',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'avg_score' => 'decimal:2',
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'calls_scored' => 'integer',
        'compliance_fails' => 'integer',
        'void_risks' => 'integer',
        'excellent_count' => 'integer',
        'exceptional_count' => 'integer',
        'good_count' => 'integer',
        'average_count' => 'integer',
        'poor_count' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }
}
