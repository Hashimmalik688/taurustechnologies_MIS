<?php

namespace App\Services\QA;

use App\Models\QA\QaCall;
use App\Models\QA\QaComplianceFlag;
use App\Models\QA\QaDailyStat;
use App\Models\QA\QaResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QAResultService
{
    /**
     * Compliance check code → DB column mapping.
     */
    private const COMPLIANCE_MAP = [
        // Call Handling
        'C1_closer_consent' => 'c1_closer_consent',
        'C2_agent_identity' => 'c2_agent_identity',
        'C3_carrier_named' => 'c3_carrier_named',
        'C4_product_type_stated' => 'c4_product_type_stated',
        'C5_health_questions_complete' => 'c5_health_questions_complete',
        'C6_proper_quote' => 'c6_proper_quote',
        'C7_coverage_amount' => 'c7_coverage_amount',
        'C8_draft_date_confirmed' => 'c8_draft_date_confirmed',
        'C9_end_of_call_consent' => 'c9_end_of_call_consent',
        'C10_waiting_period' => 'c10_waiting_period',
        // Application Requirements
        'C11_application_info_collected' => 'c11_application_info_collected',
        // Behavioral Compliance
        'C12_customer_not_on_dnc' => 'c12_customer_not_on_dnc',
        'C13_customer_not_aggressive' => 'c13_customer_not_aggressive',
        'C14_customer_not_disinterested' => 'c14_customer_not_disinterested',
        'C15_no_pushy_sale' => 'c15_no_pushy_sale',
        'C16_appropriate_language' => 'c16_appropriate_language',
        'C17_customer_not_abusive' => 'c17_customer_not_abusive',
    ];

    /**
     * Save all AI scoring output to the database in a single transaction.
     */
    public function saveResult(QaCall $qaCall, array $aiResponse): QaResult
    {
        return DB::transaction(function () use ($qaCall, $aiResponse) {
            Log::info('[QA:Result] Saving result', [
                'qa_call_id' => $qaCall->id,
                'disposition' => $aiResponse['disposition'] ?? 'unknown',
            ]);

            // Build compliance check columns
            $complianceData = [];
            $complianceChecks = $aiResponse['compliance_checks'] ?? [];
            foreach (self::COMPLIANCE_MAP as $aiKey => $dbColumn) {
                $value = $complianceChecks[$aiKey] ?? 'na';
                $complianceData[$dbColumn] = in_array($value, ['pass', 'fail', 'na']) ? $value : 'na';
            }

            // Extract score breakdown
            $scores = $aiResponse['score_breakdown'] ?? [];

            // Extract business data (names, sale info) from AI response
            $extracted = $aiResponse['extracted_data'] ?? [];

            // Create the QA result
            $qaResult = QaResult::create(array_merge($complianceData, [
                'qa_call_id' => $qaCall->id,
                'disposition' => $this->validateDisposition($aiResponse['disposition'] ?? 'POOR'),
                'total_score' => floatval($aiResponse['total_score'] ?? 0),
                'compliance_pass' => (bool) ($aiResponse['compliance_pass'] ?? false),
                'score_opening' => $this->clampScore($scores['opening'] ?? 0),
                'score_discovery' => $this->clampScore($scores['discovery'] ?? 0),
                'score_presentation' => $this->clampScore($scores['presentation'] ?? 0),
                'score_objection_handling' => $this->clampScore($scores['objection_handling'] ?? 0),
                'score_closing' => $this->clampScore($scores['closing'] ?? 0),
                'score_soft_skills' => $this->clampScore($scores['soft_skills'] ?? 0),
                'score_call_control' => $this->clampScore($scores['call_control'] ?? 0),
                'coaching_notes' => $aiResponse['coaching_notes'] ?? null,
                'top_issue' => $aiResponse['top_issue'] ?? null,
                'strengths' => $aiResponse['strengths'] ?? [],
                'improvements' => $aiResponse['improvements'] ?? [],
                'void_risk_reason' => $aiResponse['void_risk_reason'] ?? null,
                'compliance_failures' => $aiResponse['compliance_failures'] ?? [],
                'raw_ai_response' => $aiResponse,
                // Extracted business data from transcript
                'customer_name' => $extracted['customer_name'] ?? null,
                'closer_name_extracted' => $extracted['closer_name'] ?? null,
                'is_sale' => (bool) ($extracted['is_sale'] ?? false),
                'sale_amount' => $extracted['sale_amount'] ? floatval($extracted['sale_amount']) : null,
                'monthly_premium' => $extracted['monthly_premium'] ? floatval($extracted['monthly_premium']) : null,
                'carrier_name' => $extracted['carrier_name'] ?? null,
                'policy_type' => $extracted['policy_type'] ?? null,
                'customer_state' => $extracted['customer_state'] ?? null,
            ]));

            // Create individual compliance flag rows for failed items
            $this->createComplianceFlags($qaCall, $qaResult, $complianceChecks);

            // Mark the call as completed
            $qaCall->update(['processing_status' => 'completed']);

            // Re-aggregate daily stats for this agent + date
            $this->updateDailyStats($qaCall->agent_user_id, $qaCall->call_start_time?->toDateString() ?? today()->toDateString());

            Log::info('[QA:Result] Result saved successfully', [
                'qa_call_id' => $qaCall->id,
                'qa_result_id' => $qaResult->id,
                'disposition' => $qaResult->disposition,
                'total_score' => $qaResult->total_score,
                'is_sale' => $qaResult->is_sale,
                'customer_name' => $qaResult->customer_name,
                'closer_name_extracted' => $qaResult->closer_name_extracted,
            ]);

            return $qaResult;
        });
    }

    /**
     * Create QaComplianceFlag rows for each failed compliance check.
     */
    private function createComplianceFlags(QaCall $qaCall, QaResult $qaResult, array $complianceChecks): void
    {
        foreach ($complianceChecks as $aiKey => $value) {
            if ($value !== 'fail') continue;

            // Extract code from key (e.g., "C1_recording_disclosure" → "C1")
            $code = strtoupper(explode('_', $aiKey)[0]);
            $name = QaComplianceFlag::CHECK_NAMES[$code] ?? str_replace($code . '_', '', $aiKey);
            $label = QaComplianceFlag::CHECK_LABELS[$code] ?? $name;

            QaComplianceFlag::create([
                'qa_call_id' => $qaCall->id,
                'qa_result_id' => $qaResult->id,
                'agent_user_id' => $qaCall->agent_user_id,
                'check_code' => $code,
                'check_name' => $name,
                'check_label' => $label,
                'ai_reasoning' => null,
                'flagged_at' => now(),
            ]);
        }
    }

    /**
     * Re-aggregate qa_daily_stats for a given agent and date.
     */
    public function updateDailyStats(?int $agentUserId, string $date): void
    {
        if (!$agentUserId) return;

        $stats = QaCall::where('agent_user_id', $agentUserId)
            ->whereDate('call_start_time', $date)
            ->where('processing_status', 'completed')
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                COUNT(*) as calls_scored,
                ROUND(AVG(qa_results.total_score), 2) as avg_score,
                ROUND(MIN(qa_results.total_score), 2) as min_score,
                ROUND(MAX(qa_results.total_score), 2) as max_score,
                SUM(CASE WHEN qa_results.disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fails,
                SUM(CASE WHEN qa_results.disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                SUM(CASE WHEN qa_results.disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_count,
                SUM(CASE WHEN qa_results.disposition = "GOOD" THEN 1 ELSE 0 END) as good_count,
                SUM(CASE WHEN qa_results.disposition = "AVERAGE" THEN 1 ELSE 0 END) as average_count,
                SUM(CASE WHEN qa_results.disposition = "POOR" THEN 1 ELSE 0 END) as poor_count,
                ROUND(AVG(qa_results.score_opening), 2) as avg_opening,
                ROUND(AVG(qa_results.score_discovery), 2) as avg_discovery,
                ROUND(AVG(qa_results.score_presentation), 2) as avg_presentation,
                ROUND(AVG(qa_results.score_objection_handling), 2) as avg_objection_handling,
                ROUND(AVG(qa_results.score_closing), 2) as avg_closing,
                ROUND(AVG(qa_results.score_soft_skills), 2) as avg_soft_skills,
                ROUND(AVG(qa_results.score_call_control), 2) as avg_call_control
            ')
            ->first();

        if (!$stats || $stats->calls_scored == 0) {
            QaDailyStat::where('agent_user_id', $agentUserId)
                ->where('stat_date', $date)
                ->delete();
            return;
        }

        QaDailyStat::updateOrCreate(
            ['agent_user_id' => $agentUserId, 'stat_date' => $date],
            [
                'calls_scored' => $stats->calls_scored,
                'avg_score' => $stats->avg_score,
                'min_score' => $stats->min_score,
                'max_score' => $stats->max_score,
                'compliance_fails' => $stats->compliance_fails,
                'void_risks' => $stats->void_risks,
                'excellent_count' => $stats->excellent_count,
                'good_count' => $stats->good_count,
                'average_count' => $stats->average_count,
                'poor_count' => $stats->poor_count,
                'avg_opening' => $stats->avg_opening,
                'avg_discovery' => $stats->avg_discovery,
                'avg_presentation' => $stats->avg_presentation,
                'avg_objection_handling' => $stats->avg_objection_handling,
                'avg_closing' => $stats->avg_closing,
                'avg_soft_skills' => $stats->avg_soft_skills,
                'avg_call_control' => $stats->avg_call_control,
            ]
        );
    }

    private function validateDisposition(string $disposition): string
    {
        $valid = ['COMPLIANCE_FAIL', 'VOID_RISK', 'EXCELLENT', 'GOOD', 'AVERAGE', 'POOR'];
        return in_array($disposition, $valid) ? $disposition : 'POOR';
    }

    private function clampScore(mixed $score): int
    {
        return max(0, min(10, intval($score)));
    }
}
