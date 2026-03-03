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
        'c1_recording_disclosure',
        'c2_agent_identity',
        'c3_carrier_named',
        'c4_not_government_program',
        'c5_product_type_stated',
        'c6_waiting_period',
        'c7_premium_amount',
        'c8_coverage_amount',
        'c9_health_questions',
        'c10_beneficiary_collected',
        'c11_prospect_verbal_consent',
        'c12_dnc_honored',
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
            'C1_recording_disclosure' => $this->c1_recording_disclosure,
            'C2_agent_identity' => $this->c2_agent_identity,
            'C3_carrier_named' => $this->c3_carrier_named,
            'C4_not_government_program' => $this->c4_not_government_program,
            'C5_product_type_stated' => $this->c5_product_type_stated,
            'C6_waiting_period' => $this->c6_waiting_period,
            'C7_premium_amount' => $this->c7_premium_amount,
            'C8_coverage_amount' => $this->c8_coverage_amount,
            'C9_health_questions' => $this->c9_health_questions,
            'C10_beneficiary_collected' => $this->c10_beneficiary_collected,
            'C11_prospect_verbal_consent' => $this->c11_prospect_verbal_consent,
            'C12_dnc_honored' => $this->c12_dnc_honored,
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
