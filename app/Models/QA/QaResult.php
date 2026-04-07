<?php

namespace App\Models\QA;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QaResult extends Model
{
    protected $table = 'qa_results';

    protected $fillable = [
        'qa_call_id',
        'disposition',
        'total_score',
        'compliance_pass',
        // Call Handling (C1-C10)
        'c1_closer_consent',
        'c2_agent_identity',
        'c3_carrier_named',
        'c4_product_type_stated',
        'c5_health_questions_complete',
        'c6_proper_quote',
        'c7_coverage_amount',
        'c8_draft_date_confirmed',
        'c9_end_of_call_consent',
        'c10_waiting_period',
        // Application Requirements (C11)
        'c11_application_info_collected',
        // Behavioral Compliance (C12-C17)
        'c12_customer_not_on_dnc',
        'c13_customer_not_aggressive',
        'c14_customer_not_disinterested',
        'c15_no_pushy_sale',
        'c16_appropriate_language',
        'c17_customer_not_abusive',
        'score_opening',
        'score_discovery',
        'score_presentation',
        'score_objection_handling',
        'score_closing',
        'score_soft_skills',
        'score_call_control',
        'coaching_notes',
        'top_issue',
        'strengths',
        'improvements',
        'void_risk_reason',
        'compliance_failures',
        'raw_ai_response',
        // Extracted business data from transcript
        'customer_name',
        'closer_name_extracted',
        'is_sale',
        'sale_amount',
        'monthly_premium',
        'carrier_name',
        'policy_type',
        'customer_state',
        'call_type',
        'score_disposition',
        // DNC Risk Judge (standalone — does not affect score)
        'dnc_risk_level',
        'dnc_judge_verdict',
        'dnc_judge_reasoning',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'compliance_pass' => 'boolean',
        'strengths' => 'array',
        'improvements' => 'array',
        'compliance_failures' => 'array',
        'raw_ai_response' => 'array',
        'score_opening' => 'integer',
        'score_discovery' => 'integer',
        'score_presentation' => 'integer',
        'score_objection_handling' => 'integer',
        'score_closing' => 'integer',
        'score_soft_skills' => 'integer',
        'score_call_control' => 'integer',
        'is_sale' => 'boolean',
        'sale_amount' => 'decimal:2',
        'monthly_premium' => 'decimal:2',
        'dnc_risk_level' => 'string',
        'dnc_judge_verdict' => 'string',
        'dnc_judge_reasoning' => 'string',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function qaCall(): BelongsTo
    {
        return $this->belongsTo(QaCall::class, 'qa_call_id');
    }

    public function complianceFlags(): HasMany
    {
        return $this->hasMany(QaComplianceFlag::class, 'qa_result_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────

    public function getComplianceChecksAttribute(): array
    {
        return [
            'C1_closer_consent' => $this->c1_closer_consent,
            'C2_agent_identity' => $this->c2_agent_identity,
            'C3_carrier_named' => $this->c3_carrier_named,
            'C4_product_type_stated' => $this->c4_product_type_stated,
            'C5_health_questions_complete' => $this->c5_health_questions_complete,
            'C6_proper_quote' => $this->c6_proper_quote,
            'C7_coverage_amount' => $this->c7_coverage_amount,
            'C8_draft_date_confirmed' => $this->c8_draft_date_confirmed,
            'C9_end_of_call_consent' => $this->c9_end_of_call_consent,
            'C10_waiting_period' => $this->c10_waiting_period,
            'C11_application_info_collected' => $this->c11_application_info_collected,
            'C12_customer_not_on_dnc' => $this->c12_customer_not_on_dnc,
            'C13_customer_not_aggressive' => $this->c13_customer_not_aggressive,
            'C14_customer_not_disinterested' => $this->c14_customer_not_disinterested,
            'C15_no_pushy_sale' => $this->c15_no_pushy_sale,
            'C16_appropriate_language' => $this->c16_appropriate_language,
            'C17_customer_not_abusive' => $this->c17_customer_not_abusive,
        ];
    }

    public function getScoreBreakdownAttribute(): array
    {
        return [
            'opening' => $this->score_opening,
            'discovery' => $this->score_discovery,
            'presentation' => $this->score_presentation,
            'objection_handling' => $this->score_objection_handling,
            'closing' => $this->score_closing,
            'soft_skills' => $this->score_soft_skills,
            'call_control' => $this->score_call_control,
        ];
    }

    // ── Scopes ─────────────────────────────────────────────────────────

    public function scopeComplianceFail($query)
    {
        return $query->where('disposition', 'COMPLIANCE_FAIL');
    }

    public function scopeVoidRisk($query)
    {
        return $query->where('disposition', 'VOID_RISK');
    }
}
