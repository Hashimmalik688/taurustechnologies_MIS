<?php

namespace Database\Seeders;

use App\Models\QA\QaCall;
use App\Models\QA\QaComplianceFlag;
use App\Models\QA\QaDailyStat;
use App\Models\QA\QaResult;
use App\Models\User;
use Illuminate\Database\Seeder;

class QaSampleDataSeeder extends Seeder
{
    /**
     * Seed sample QA data for dashboard testing.
     * Run: php artisan db:seed --class=QaSampleDataSeeder
     */
    public function run(): void
    {
        // Get some real agent users, or fall back to first few users
        $agents = User::role(['Employee', 'Ravens Closer', 'Peregrine Closer'])
            ->inRandomOrder()
            ->limit(6)
            ->get();

        if ($agents->isEmpty()) {
            $agents = User::inRandomOrder()->limit(6)->get();
        }

        if ($agents->isEmpty()) {
            echo "No users found. Please seed users first.\n";
            return;
        }

        echo "Seeding QA sample data for {$agents->count()} agents...\n";

        $dispositions = ['EXCELLENT', 'GOOD', 'AVERAGE', 'POOR', 'COMPLIANCE_FAIL', 'VOID_RISK'];
        $weights = [15, 30, 25, 15, 10, 5]; // Weighted distribution

        $complianceCodes = [
            'C1' => 'Recording Disclosure',
            'C2' => 'Agent Identity',
            'C3' => 'Carrier Named',
            'C4' => 'Not Government Program',
            'C5' => 'Product Type Stated',
            'C6' => 'Waiting Period',
            'C7' => 'Premium Amount',
            'C8' => 'Coverage Amount',
            'C9' => 'Health Questions',
            'C10' => 'Beneficiary Collected',
            'C11' => 'Verbal Consent',
            'C12' => 'DNC Honored',
        ];

        $topIssues = [
            'Failed to state recording disclosure',
            'Did not confirm beneficiary details',
            'Rushed through health questions',
            'Missing waiting period explanation',
            'Did not confirm coverage amount',
            'Agent spoke over customer',
            'Failed to handle objection properly',
            'No clear closing attempt',
            'Did not identify carrier by name',
            'Premium amount unclear',
        ];

        $coachingNotes = [
            'Agent should slow down during the opening and ensure recording disclosure is clearly stated.',
            'Needs improvement on objection handling — practice feel/felt/found technique.',
            'Good energy but must cover all compliance checkpoints before closing.',
            'Excellent rapport building. Focus on asking more health screening questions.',
            'Strong closer but rushing through product explanation. Take time to explain waiting period.',
        ];

        $scoredByOptions = ['gemini', 'claude'];

        // Generate 14 days of calls
        for ($day = 13; $day >= 0; $day--) {
            $date = now()->subDays($day);
            $callsPerDay = rand(8, 20);

            foreach ($agents as $agent) {
                $agentCallCount = rand(2, max(2, intdiv($callsPerDay, $agents->count()) + 1));

                for ($c = 0; $c < $agentCallCount; $c++) {
                    $duration = rand(180, 1800); // 3-30 min
                    $callTime = $date->copy()->setTime(rand(8, 17), rand(0, 59), rand(0, 59));

                    // Random disposition (weighted)
                    $disposition = $this->weightedRandom($dispositions, $weights);

                    // Score based on disposition
                    $totalScore = match ($disposition) {
                        'EXCELLENT' => rand(90, 100),
                        'GOOD' => rand(75, 89),
                        'AVERAGE' => rand(60, 74),
                        'POOR' => rand(30, 59),
                        'COMPLIANCE_FAIL' => rand(20, 65),
                        'VOID_RISK' => rand(10, 45),
                    };

                    // Category scores correlate loosely with total
                    $baseCategory = (int) ($totalScore / 10);
                    $catScores = [
                        'opening' => max(1, min(10, $baseCategory + rand(-1, 1))),
                        'discovery' => max(1, min(10, $baseCategory + rand(-2, 1))),
                        'presentation' => max(1, min(10, $baseCategory + rand(-1, 2))),
                        'objection_handling' => max(1, min(10, $baseCategory + rand(-2, 1))),
                        'closing' => max(1, min(10, $baseCategory + rand(-1, 1))),
                        'soft_skills' => max(1, min(10, $baseCategory + rand(0, 2))),
                        'call_control' => max(1, min(10, $baseCategory + rand(-1, 1))),
                    ];

                    // Compliance pass/fail
                    $compliancePass = !in_array($disposition, ['COMPLIANCE_FAIL', 'VOID_RISK']);
                    $compChecks = [];
                    foreach (array_keys($complianceCodes) as $code) {
                        if ($compliancePass) {
                            $compChecks[strtolower($code)] = rand(1, 100) > 5 ? 'pass' : 'fail'; // 5% random fail
                        } else {
                            $compChecks[strtolower($code)] = rand(1, 100) > 30 ? 'pass' : 'fail'; // 30% fail rate
                        }
                    }

                    // Ensure at least one fail for compliance_fail disposition
                    if ($disposition === 'COMPLIANCE_FAIL') {
                        $failKey = array_keys($compChecks)[array_rand(array_keys($compChecks))];
                        $compChecks[$failKey] = 'fail';
                    }

                    $scoredBy = $scoredByOptions[array_rand($scoredByOptions)];

                    // Create QA Call
                    $qaCall = QaCall::create([
                        'zoom_call_id' => 'SAMPLE-' . uniqid(),
                        'agent_user_id' => $agent->id,
                        'agent_name' => $agent->name,
                        'agent_email' => $agent->email,
                        'caller_number' => '+1' . rand(2000000000, 9999999999),
                        'callee_number' => '+1' . rand(2000000000, 9999999999),
                        'duration_seconds' => $duration,
                        'call_start_time' => $callTime,
                        'processing_status' => 'completed',
                        'scored_by' => $scoredBy,
                        'transcript_plain' => 'Sample transcript for testing purposes.',
                        'transcript_diarized' => "AGENT: Hello, this is {$agent->name} calling from Taurus Insurance.\nCUSTOMER: Hi there.\nAGENT: I'm calling about the final expense life insurance plan we discussed.\nCUSTOMER: Oh yes, I remember.\nAGENT: Great! Let me go over the details with you...",
                    ]);

                    // Determine compliance failures list
                    $compFailures = [];
                    foreach ($compChecks as $code => $status) {
                        if ($status === 'fail') {
                            $cUpper = strtoupper($code);
                            $compFailures[] = $complianceCodes[$cUpper] ?? $code;
                        }
                    }

                    // Create QA Result
                    $qaResult = QaResult::create([
                        'qa_call_id' => $qaCall->id,
                        'disposition' => $disposition,
                        'total_score' => $totalScore,
                        'compliance_pass' => $compliancePass,
                        'c1_recording_disclosure' => $compChecks['c1'],
                        'c2_agent_identity' => $compChecks['c2'],
                        'c3_carrier_named' => $compChecks['c3'],
                        'c4_not_government_program' => $compChecks['c4'],
                        'c5_product_type_stated' => $compChecks['c5'],
                        'c6_waiting_period' => $compChecks['c6'],
                        'c7_premium_amount' => $compChecks['c7'],
                        'c8_coverage_amount' => $compChecks['c8'],
                        'c9_health_questions' => $compChecks['c9'],
                        'c10_beneficiary_collected' => $compChecks['c10'],
                        'c11_prospect_verbal_consent' => $compChecks['c11'],
                        'c12_dnc_honored' => $compChecks['c12'],
                        'score_opening' => $catScores['opening'],
                        'score_discovery' => $catScores['discovery'],
                        'score_presentation' => $catScores['presentation'],
                        'score_objection_handling' => $catScores['objection_handling'],
                        'score_closing' => $catScores['closing'],
                        'score_soft_skills' => $catScores['soft_skills'],
                        'score_call_control' => $catScores['call_control'],
                        'coaching_notes' => $coachingNotes[array_rand($coachingNotes)],
                        'top_issue' => $totalScore < 80 ? $topIssues[array_rand($topIssues)] : null,
                        'strengths' => $totalScore >= 70 ? array_slice($topIssues, 0, rand(1, 3)) : null,
                        'improvements' => $totalScore < 85 ? array_slice($topIssues, 3, rand(1, 3)) : null,
                        'void_risk_reason' => $disposition === 'VOID_RISK' ? 'Multiple compliance failures indicate high risk of policy void.' : null,
                        'compliance_failures' => !empty($compFailures) ? $compFailures : null,
                    ]);

                    // Create compliance flags for fails
                    foreach ($compChecks as $code => $status) {
                        if ($status === 'fail') {
                            $cUpper = strtoupper($code);
                            QaComplianceFlag::create([
                                'qa_call_id' => $qaCall->id,
                                'qa_result_id' => $qaResult->id,
                                'agent_user_id' => $agent->id,
                                'check_code' => $cUpper,
                                'check_name' => strtolower(str_replace(' ', '_', $complianceCodes[$cUpper] ?? $code)),
                                'check_label' => $complianceCodes[$cUpper] ?? $code,
                                'ai_reasoning' => 'AI detected failure in ' . ($complianceCodes[$cUpper] ?? $code),
                                'flagged_at' => $callTime,
                            ]);
                        }
                    }
                }

                // Create daily stat for this agent/day
                $agentDayCalls = QaCall::where('agent_user_id', $agent->id)
                    ->whereDate('call_start_time', $date->toDateString())
                    ->where('processing_status', 'completed')
                    ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id');

                $stats = $agentDayCalls->selectRaw('
                    COUNT(*) as total,
                    ROUND(AVG(total_score), 1) as avg_score,
                    MIN(total_score) as min_score,
                    MAX(total_score) as max_score,
                    SUM(CASE WHEN disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as comp_fails,
                    SUM(CASE WHEN disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                    SUM(CASE WHEN disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent,
                    SUM(CASE WHEN disposition = "GOOD" THEN 1 ELSE 0 END) as good,
                    SUM(CASE WHEN disposition = "AVERAGE" THEN 1 ELSE 0 END) as average,
                    SUM(CASE WHEN disposition = "POOR" THEN 1 ELSE 0 END) as poor,
                    ROUND(AVG(score_opening), 1) as avg_opening,
                    ROUND(AVG(score_discovery), 1) as avg_discovery,
                    ROUND(AVG(score_presentation), 1) as avg_presentation,
                    ROUND(AVG(score_objection_handling), 1) as avg_objection_handling,
                    ROUND(AVG(score_closing), 1) as avg_closing,
                    ROUND(AVG(score_soft_skills), 1) as avg_soft_skills,
                    ROUND(AVG(score_call_control), 1) as avg_call_control
                ')->first();

                if ($stats && $stats->total > 0) {
                    QaDailyStat::updateOrCreate(
                        ['agent_user_id' => $agent->id, 'stat_date' => $date->toDateString()],
                        [
                            'calls_scored' => $stats->total,
                            'avg_score' => $stats->avg_score,
                            'min_score' => $stats->min_score,
                            'max_score' => $stats->max_score,
                            'compliance_fails' => $stats->comp_fails,
                            'void_risks' => $stats->void_risks,
                            'excellent_count' => $stats->excellent,
                            'good_count' => $stats->good,
                            'average_count' => $stats->average,
                            'poor_count' => $stats->poor,
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
            }
        }

        $totalCalls = QaCall::where('zoom_call_id', 'like', 'SAMPLE-%')->count();
        $totalResults = QaResult::count();
        $totalFlags = QaComplianceFlag::count();
        $totalStats = QaDailyStat::count();

        echo "✅ Seeded {$totalCalls} calls, {$totalResults} results, {$totalFlags} flags, {$totalStats} daily stats.\n";
    }

    private function weightedRandom(array $items, array $weights): string
    {
        $totalWeight = array_sum($weights);
        $rand = rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($items as $i => $item) {
            $cumulative += $weights[$i];
            if ($rand <= $cumulative) {
                return $item;
            }
        }

        return $items[0];
    }
}
