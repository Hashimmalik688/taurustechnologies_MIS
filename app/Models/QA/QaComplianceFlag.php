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
        // Call Handling
        'C1' => 'Closer Consent',
        'C2' => 'Agent Identity',
        'C3' => 'Carrier Named',
        'C4' => 'Product Type Stated',
        'C5' => 'Health Questions Complete',
        'C6' => 'Proper Quote Given',
        'C7' => 'Coverage Amount Confirmed',
        'C8' => 'Draft Date Confirmed',
        'C9' => 'End-of-Call Consent',
        'C10' => 'Waiting Period Disclosed',
        // Application Requirements
        'C11' => 'Application Info Collected',
        // Behavioral Compliance
        'C12' => 'Customer Not on DNC',
        'C13' => 'Customer Not Aggressive',
        'C14' => 'Customer Not Disinterested',
        'C15' => 'No Pushy Sale',
        'C16' => 'Appropriate Language',
        'C17' => 'Customer Not Abusive',
    ];

    public const CHECK_NAMES = [
        'C1' => 'closer_consent',
        'C2' => 'agent_identity',
        'C3' => 'carrier_named',
        'C4' => 'product_type_stated',
        'C5' => 'health_questions_complete',
        'C6' => 'proper_quote',
        'C7' => 'coverage_amount',
        'C8' => 'draft_date_confirmed',
        'C9' => 'end_of_call_consent',
        'C10' => 'waiting_period',
        'C11' => 'application_info_collected',
        'C12' => 'customer_not_on_dnc',
        'C13' => 'customer_not_aggressive',
        'C14' => 'customer_not_disinterested',
        'C15' => 'no_pushy_sale',
        'C16' => 'appropriate_language',
        'C17' => 'customer_not_abusive',
    ];
}
