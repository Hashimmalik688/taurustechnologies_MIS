<?php

namespace App\Models\QA;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaComplianceFlag extends Model
{
    protected $table = 'qa_compliance_flags';

    protected $fillable = [
        'qa_call_id',
        'qa_result_id',
        'agent_user_id',
        'check_code',
        'check_name',
        'check_label',
        'ai_reasoning',
        'flagged_at',
    ];

    protected $casts = [
        'flagged_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function qaCall(): BelongsTo
    {
        return $this->belongsTo(QaCall::class, 'qa_call_id');
    }

    public function qaResult(): BelongsTo
    {
        return $this->belongsTo(QaResult::class, 'qa_result_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    // ── Constants (C1-C11 active checks, renumbered per updated QA rules) ─

    public const CHECK_LABELS = [
        'C1'  => 'Closer Introduction',
        'C2'  => 'Carrier Named',
        'C3'  => 'Product Type Stated',
        'C4'  => 'Health Questions',
        'C5'  => 'Quote & Coverage',
        'C6'  => 'Draft Date Confirmed',
        'C7'  => 'End-of-Call Consent',
        'C8'  => 'Application Info Collected',
        'C9'  => 'DNC Honored',
        'C10' => 'Agent Handles Objections',
        'C11' => 'Appropriate Language',
    ];

    public const CHECK_NAMES = [
        'C1'  => 'agent_identity',
        'C2'  => 'carrier_named',
        'C3'  => 'product_type_stated',
        'C4'  => 'health_questions_complete',
        'C5'  => 'quote_and_coverage',
        'C6'  => 'draft_date_confirmed',
        'C7'  => 'end_of_call_consent',
        'C8'  => 'application_info_collected',
        'C9'  => 'customer_not_on_dnc',
        'C10' => 'agent_handles_objections',
        'C11' => 'appropriate_language',
    ];
}
