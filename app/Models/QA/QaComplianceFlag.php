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

    // ── Constants ──────────────────────────────────────────────────────

    public const CHECK_LABELS = [
        'C1' => 'Recording Consent',
        'C2' => 'Agent Identity',
        'C3' => 'Carrier Named',
        'C4' => 'Not Government Program',
        'C5' => 'Product Type Stated',
        'C6' => 'Waiting Period Disclosed',
        'C7' => 'Premium Amount Confirmed',
        'C8' => 'Coverage Amount Confirmed',
        'C9' => 'Health Questions Asked',
        'C10' => 'Beneficiary Collected',
        'C11' => 'Prospect Verbal Consent',
        'C12' => 'DNC Honored',
    ];

    public const CHECK_NAMES = [
        'C1' => 'recording_consent',
        'C2' => 'agent_identity',
        'C3' => 'carrier_named',
        'C4' => 'not_government_program',
        'C5' => 'product_type_stated',
        'C6' => 'waiting_period',
        'C7' => 'premium_amount',
        'C8' => 'coverage_amount',
        'C9' => 'health_questions',
        'C10' => 'beneficiary_collected',
        'C11' => 'prospect_verbal_consent',
        'C12' => 'dnc_honored',
    ];
}
