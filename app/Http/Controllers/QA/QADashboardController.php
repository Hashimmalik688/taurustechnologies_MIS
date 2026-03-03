<?php

namespace App\Http\Controllers\QA;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\QA\QaCall;
use App\Models\QA\QaComplianceFlag;
use App\Models\QA\QaDailyStat;
use App\Models\QA\QaResult;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QADashboardController extends Controller
{
    // ── GET /qa/scoring ─────────────────────────────────────────────────

    public function index()
    {
        return view('qa.dashboard');
    }

    // ── GET /qa/api/overview ────────────────────────────────────────────

    public function overview(Request $request): JsonResponse
    {
        $range = $request->input('range', 'today');

        // ── Primary Team KPIs ──────────────────────────────────────────
        $teamStats = $this->getTeamStats($range);

        // ── Extended KPIs (Final Expense industry metrics) ─────────────
        $extendedKpis = $this->getExtendedKpis($range);

        // ── Agent leaderboard (all agents, ranked by avg score) ────────
        $leaderboard = $this->getAgentLeaderboard($range);

        // ── Compliance alerts (recent fails) ───────────────────────────
        $complianceAlerts = QaComplianceFlag::with(['qaCall', 'agent'])
            ->whereHas('qaCall', fn ($q) => $this->applyRange($q, $range, 'call_start_time'))
            ->orderByDesc('flagged_at')
            ->limit(15)
            ->get()
            ->map(fn ($flag) => [
                'id' => $flag->id,
                'agent_name' => $flag->agent?->name ?? $flag->qaCall?->agent_name,
                'agent_user_id' => $flag->agent_user_id ?? $flag->qaCall?->agent_user_id,
                'check_code' => $flag->check_code,
                'check_label' => $flag->check_label,
                'qa_call_id' => $flag->qa_call_id,
                'flagged_at' => $flag->flagged_at->toIso8601String(),
            ]);

        // ── Disposition distribution ───────────────────────────────────
        $dispositionChart = QaResult::join('qa_calls', 'qa_results.qa_call_id', '=', 'qa_calls.id')
            ->where('qa_calls.processing_status', 'completed')
            ->when(true, fn ($q) => $this->applyRange($q, $range, 'qa_calls.call_start_time'))
            ->groupBy('disposition')
            ->selectRaw('disposition, COUNT(*) as count')
            ->pluck('count', 'disposition');

        // ── Score trend (7 days) ───────────────────────────────────────
        $scoreTrend = QaDailyStat::selectRaw('stat_date, ROUND(AVG(avg_score), 1) as avg')
            ->where('stat_date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('stat_date')
            ->orderBy('stat_date')
            ->pluck('avg', 'stat_date');

        // ── Compliance Rate Trend (7 days) ─────────────────────────────
        $complianceTrend = QaCall::completed()
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->where('qa_calls.call_start_time', '>=', now()->subDays(7)->startOfDay())
            ->selectRaw('DATE(qa_calls.call_start_time) as stat_date, ROUND(SUM(CASE WHEN qa_results.compliance_pass = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 1) as rate')
            ->groupBy('stat_date')
            ->orderBy('stat_date')
            ->pluck('rate', 'stat_date');

        // ── Top issues ─────────────────────────────────────────────────
        $topIssues = QaResult::join('qa_calls', 'qa_results.qa_call_id', '=', 'qa_calls.id')
            ->where('qa_calls.processing_status', 'completed')
            ->when(true, fn ($q) => $this->applyRange($q, $range, 'qa_calls.call_start_time'))
            ->whereNotNull('top_issue')
            ->where('top_issue', '!=', '')
            ->groupBy('top_issue')
            ->selectRaw('top_issue, COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'top_issue');

        // ── Per-compliance-check failure rates ─────────────────────────
        $complianceBreakdown = $this->getComplianceBreakdown($range);

        // ── Category averages (team-wide) ──────────────────────────────
        $teamCategoryAverages = $this->getTeamCategoryAverages($range);

        // ── Score distribution (histogram buckets) ─────────────────────
        $scoreDistribution = $this->getScoreDistribution($range);

        // ── Recent calls (10) ──────────────────────────────────────────
        $recentCalls = QaCall::with('qaResult')
            ->completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->orderByDesc('call_start_time')
            ->limit(10)
            ->get()
            ->map(fn ($call) => $this->formatCallSummary($call));

        // ── Void risks ─────────────────────────────────────────────────
        $voidRisks = QaCall::with('qaResult')
            ->completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->whereHas('qaResult', fn ($q) => $q->where('disposition', 'VOID_RISK'))
            ->orderByDesc('call_start_time')
            ->limit(10)
            ->get()
            ->map(fn ($call) => array_merge($this->formatCallSummary($call), [
                'void_risk_reason' => $call->qaResult?->void_risk_reason,
            ]));

        // ── Processing queue ───────────────────────────────────────────
        $processingNow = QaCall::processing()->count();
        $pendingCount = QaCall::pending()->count();
        $failedCount = QaCall::failed()->count();

        return response()->json([
            'team_stats' => $teamStats,
            'extended_kpis' => $extendedKpis,
            'agent_leaderboard' => $leaderboard,
            'compliance_alerts' => $complianceAlerts,
            'disposition_chart' => $dispositionChart,
            'score_trend' => $scoreTrend,
            'compliance_trend' => $complianceTrend,
            'top_issues' => $topIssues,
            'compliance_breakdown' => $complianceBreakdown,
            'team_category_averages' => $teamCategoryAverages,
            'score_distribution' => $scoreDistribution,
            'recent_calls' => $recentCalls,
            'void_risks' => $voidRisks,
            'processing_now' => $processingNow,
            'pending_count' => $pendingCount,
            'failed_count' => $failedCount,
        ]);
    }

    // ── GET /qa/api/agents/{id} ────────────────────────────────────────

    public function agentDetail(Request $request, int $id): JsonResponse
    {
        $range = $request->input('range', 'today');
        $user = User::findOrFail($id);

        // Agent summary stats (extended)
        $summary = QaCall::where('agent_user_id', $id)
            ->completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                COUNT(*) as calls_scored,
                ROUND(AVG(total_score), 1) as avg_score,
                ROUND(MIN(total_score), 1) as min_score,
                ROUND(MAX(total_score), 1) as max_score,
                SUM(CASE WHEN disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_count,
                SUM(CASE WHEN disposition = "GOOD" THEN 1 ELSE 0 END) as good_count,
                SUM(CASE WHEN disposition = "AVERAGE" THEN 1 ELSE 0 END) as average_count,
                SUM(CASE WHEN disposition = "POOR" THEN 1 ELSE 0 END) as poor_count,
                SUM(CASE WHEN disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fails,
                SUM(CASE WHEN disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                SUM(CASE WHEN compliance_pass = 1 THEN 1 ELSE 0 END) as compliance_pass_count,
                ROUND(AVG(score_opening), 1) as avg_opening,
                ROUND(AVG(score_discovery), 1) as avg_discovery,
                ROUND(AVG(score_presentation), 1) as avg_presentation,
                ROUND(AVG(score_objection_handling), 1) as avg_objection_handling,
                ROUND(AVG(score_closing), 1) as avg_closing,
                ROUND(AVG(score_soft_skills), 1) as avg_soft_skills,
                ROUND(AVG(score_call_control), 1) as avg_call_control
            ')
            ->first();

        // Average handle time for this agent
        $avgHandleTime = QaCall::where('agent_user_id', $id)
            ->completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->whereNotNull('duration_seconds')
            ->where('duration_seconds', '>', 0)
            ->avg('duration_seconds');

        // Score trend (14 days)
        $scoreTrend = QaDailyStat::where('agent_user_id', $id)
            ->where('stat_date', '>=', now()->subDays(14)->toDateString())
            ->orderBy('stat_date')
            ->get()
            ->map(fn ($s) => [
                'date' => $s->stat_date->toDateString(),
                'avg_score' => $s->avg_score,
                'calls_scored' => $s->calls_scored,
            ]);

        // Category averages
        $categoryAverages = [
            'opening' => $summary->avg_opening ?? 0,
            'discovery' => $summary->avg_discovery ?? 0,
            'presentation' => $summary->avg_presentation ?? 0,
            'objection_handling' => $summary->avg_objection_handling ?? 0,
            'closing' => $summary->avg_closing ?? 0,
            'soft_skills' => $summary->avg_soft_skills ?? 0,
            'call_control' => $summary->avg_call_control ?? 0,
        ];

        // Compliance history
        $complianceHistory = QaComplianceFlag::where('agent_user_id', $id)
            ->with('qaCall')
            ->orderByDesc('flagged_at')
            ->limit(50)
            ->get()
            ->map(fn ($flag) => [
                'id' => $flag->id,
                'check_code' => $flag->check_code,
                'check_label' => $flag->check_label,
                'qa_call_id' => $flag->qa_call_id,
                'flagged_at' => $flag->flagged_at->toIso8601String(),
            ]);

        // Per-check failure counts for this agent
        $agentComplianceBreakdown = QaComplianceFlag::where('agent_user_id', $id)
            ->whereHas('qaCall', fn ($q) => $this->applyRange($q, $range, 'call_start_time'))
            ->groupBy('check_code', 'check_label')
            ->selectRaw('check_code, check_label, COUNT(*) as count')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'check_code' => $row->check_code,
                'check_label' => $row->check_label,
                'count' => $row->count,
            ]);

        // Paginated calls
        $page = $request->input('page', 1);
        $calls = QaCall::with('qaResult')
            ->where('agent_user_id', $id)
            ->completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->orderByDesc('call_start_time')
            ->paginate(15, ['*'], 'page', $page);

        $callItems = collect($calls->items())->map(fn ($call) => $this->formatCallSummary($call));

        // Recurring issues
        $recurringIssues = QaResult::join('qa_calls', 'qa_results.qa_call_id', '=', 'qa_calls.id')
            ->where('qa_calls.agent_user_id', $id)
            ->where('qa_calls.processing_status', 'completed')
            ->whereNotNull('top_issue')
            ->where('top_issue', '!=', '')
            ->groupBy('top_issue')
            ->selectRaw('top_issue, COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'top_issue');

        // Disposition distribution for this agent
        $agentDispositions = QaResult::join('qa_calls', 'qa_results.qa_call_id', '=', 'qa_calls.id')
            ->where('qa_calls.agent_user_id', $id)
            ->where('qa_calls.processing_status', 'completed')
            ->when(true, fn ($q) => $this->applyRange($q, $range, 'qa_calls.call_start_time'))
            ->groupBy('disposition')
            ->selectRaw('disposition, COUNT(*) as count')
            ->pluck('count', 'disposition');

        $callsScored = $summary->calls_scored ?? 0;
        $complianceRate = $callsScored > 0
            ? round(($summary->compliance_pass_count / $callsScored) * 100, 1)
            : 0;

        return response()->json([
            'agent' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'summary' => [
                'calls_scored' => $callsScored,
                'avg_score' => $summary->avg_score ?? 0,
                'min_score' => $summary->min_score ?? 0,
                'max_score' => $summary->max_score ?? 0,
                'compliance_fails' => $summary->compliance_fails ?? 0,
                'compliance_rate' => $complianceRate,
                'void_risks' => $summary->void_risks ?? 0,
                'avg_handle_time' => round($avgHandleTime ?? 0),
                'excellent_count' => $summary->excellent_count ?? 0,
                'good_count' => $summary->good_count ?? 0,
                'average_count' => $summary->average_count ?? 0,
                'poor_count' => $summary->poor_count ?? 0,
            ],
            'score_trend' => $scoreTrend,
            'category_averages' => $categoryAverages,
            'compliance_history' => $complianceHistory,
            'compliance_breakdown' => $agentComplianceBreakdown,
            'disposition_chart' => $agentDispositions,
            'calls' => $callItems,
            'calls_pagination' => [
                'current_page' => $calls->currentPage(),
                'last_page' => $calls->lastPage(),
                'total' => $calls->total(),
            ],
            'recurring_issues' => $recurringIssues,
        ]);
    }

    // ── GET /qa/api/calls ──────────────────────────────────────────────

    public function calls(Request $request): JsonResponse
    {
        $range = $request->input('range', 'today');
        $page = $request->input('page', 1);

        $calls = QaCall::with('qaResult')
            ->completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->orderByDesc('call_start_time')
            ->paginate(20, ['*'], 'page', $page);

        $items = collect($calls->items())->map(fn ($call) => $this->formatCallSummary($call));

        return response()->json([
            'calls' => $items,
            'pagination' => [
                'current_page' => $calls->currentPage(),
                'last_page' => $calls->lastPage(),
                'total' => $calls->total(),
            ],
        ]);
    }

    // ── GET /qa/api/calls/{id} ─────────────────────────────────────────

    public function callDetail(int $id): JsonResponse
    {
        $call = QaCall::with(['qaResult', 'complianceFlags'])->findOrFail($id);

        // Parse diarized transcript into array
        $transcriptLines = [];
        if ($call->transcript_diarized) {
            foreach (explode("\n", $call->transcript_diarized) as $line) {
                $line = trim($line);
                if (!$line) continue;

                if (str_starts_with($line, 'AGENT:')) {
                    $transcriptLines[] = [
                        'speaker' => 'AGENT',
                        'text' => trim(substr($line, 6)),
                    ];
                } elseif (str_starts_with($line, 'CUSTOMER:')) {
                    $transcriptLines[] = [
                        'speaker' => 'CUSTOMER',
                        'text' => trim(substr($line, 9)),
                    ];
                } else {
                    $transcriptLines[] = [
                        'speaker' => 'UNKNOWN',
                        'text' => $line,
                    ];
                }
            }
        }

        $result = $call->qaResult;

        return response()->json([
            'call' => [
                'id' => $call->id,
                'zoom_call_id' => $call->zoom_call_id,
                'agent_name' => $call->agent_name,
                'agent_email' => $call->agent_email,
                'agent_user_id' => $call->agent_user_id,
                'caller_number' => $call->caller_number,
                'callee_number' => $call->callee_number,
                'duration_seconds' => $call->duration_seconds,
                'call_start_time' => $call->call_start_time?->toIso8601String(),
                'scored_by' => $call->scored_by,
                'processing_status' => $call->processing_status,
            ],
            'qa_result' => $result ? [
                'disposition' => $result->disposition,
                'total_score' => $result->total_score,
                'compliance_pass' => $result->compliance_pass,
                'compliance_checks' => $result->compliance_checks,
                'compliance_failures' => $result->compliance_failures,
                'score_breakdown' => $result->score_breakdown,
                'coaching_notes' => $result->coaching_notes,
                'top_issue' => $result->top_issue,
                'strengths' => $result->strengths,
                'improvements' => $result->improvements,
                'void_risk_reason' => $result->void_risk_reason,
            ] : null,
            'compliance_flags' => $call->complianceFlags->map(fn ($f) => [
                'check_code' => $f->check_code,
                'check_label' => $f->check_label,
                'flagged_at' => $f->flagged_at->toIso8601String(),
            ]),
            'transcript' => $transcriptLines,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════════

    private function getTeamStats(string $range): array
    {
        $base = QaCall::completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range));

        $callsScored = (clone $base)->count();

        $scores = (clone $base)
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                ROUND(AVG(total_score), 1) as avg_score,
                SUM(CASE WHEN disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fails,
                SUM(CASE WHEN disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                SUM(CASE WHEN disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_count,
                SUM(CASE WHEN disposition = "GOOD" THEN 1 ELSE 0 END) as good_count,
                SUM(CASE WHEN disposition = "AVERAGE" THEN 1 ELSE 0 END) as average_count,
                SUM(CASE WHEN disposition = "POOR" THEN 1 ELSE 0 END) as poor_count,
                SUM(CASE WHEN compliance_pass = 1 THEN 1 ELSE 0 END) as compliance_pass_count
            ')
            ->first();

        $complianceRate = $callsScored > 0
            ? round(($scores->compliance_pass_count / $callsScored) * 100, 1)
            : 0;

        return [
            'calls_scored' => $callsScored,
            'avg_score' => $scores->avg_score ?? 0,
            'compliance_fails' => $scores->compliance_fails ?? 0,
            'compliance_rate' => $complianceRate,
            'void_risks' => $scores->void_risks ?? 0,
            'excellent_count' => $scores->excellent_count ?? 0,
            'good_count' => $scores->good_count ?? 0,
            'average_count' => $scores->average_count ?? 0,
            'poor_count' => $scores->poor_count ?? 0,
        ];
    }

    /**
     * Extended KPIs for Final Expense call center QA.
     * Industry metrics: AHT, compliance rate, score distribution, DNC rate, etc.
     */
    private function getExtendedKpis(string $range): array
    {
        $base = QaCall::completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range));

        // Average Handle Time (AHT)
        $avgHandleTime = (clone $base)
            ->whereNotNull('duration_seconds')
            ->where('duration_seconds', '>', 0)
            ->avg('duration_seconds');

        // Total unique agents scored
        $agentsScored = (clone $base)
            ->whereNotNull('agent_user_id')
            ->distinct('agent_user_id')
            ->count('agent_user_id');

        // Short calls (<3 min) — skipped — from all calls processed (including failed)
        $shortCallsSkipped = QaCall::where('processing_status', 'completed')
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->where(function ($q) {
                $q->whereNull('duration_seconds')
                    ->orWhere('duration_seconds', '<', 180);
            })
            ->count();

        // Calls with DNC violations (C12)
        $dncViolations = QaComplianceFlag::where('check_code', 'C12')
            ->whereHas('qaCall', fn ($q) => $this->applyRange($q, $range, 'call_start_time'))
            ->count();

        // Recording disclosure failures (C1) — critical for FE compliance
        $recordingDisclosureFailures = QaComplianceFlag::where('check_code', 'C1')
            ->whereHas('qaCall', fn ($q) => $this->applyRange($q, $range, 'call_start_time'))
            ->count();

        // AI scoring model usage
        $scoredByGemini = (clone $base)->where('scored_by', 'gemini')->count();
        $scoredByClaude = (clone $base)->where('scored_by', 'claude')->count();

        // Calls above 80 (passing threshold industry standard)
        $callsScored = (clone $base)->count();
        $passingCalls = (clone $base)
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->where('qa_results.total_score', '>=', 80)
            ->count();
        $passingRate = $callsScored > 0 ? round(($passingCalls / $callsScored) * 100, 1) : 0;

        return [
            'avg_handle_time' => round($avgHandleTime ?? 0),
            'agents_scored' => $agentsScored,
            'short_calls_skipped' => $shortCallsSkipped,
            'dnc_violations' => $dncViolations,
            'recording_disclosure_fails' => $recordingDisclosureFailures,
            'scored_by_gemini' => $scoredByGemini,
            'scored_by_claude' => $scoredByClaude,
            'passing_rate' => $passingRate,
        ];
    }

    /**
     * Per-compliance-check failure rates across all calls.
     * Helps identify which compliance areas are most problematic.
     */
    private function getComplianceBreakdown(string $range): array
    {
        return QaComplianceFlag::whereHas('qaCall', fn ($q) => $this->applyRange($q, $range, 'call_start_time'))
            ->groupBy('check_code', 'check_label')
            ->selectRaw('check_code, check_label, COUNT(*) as count')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'check_code' => $row->check_code,
                'check_label' => $row->check_label,
                'count' => $row->count,
            ])
            ->toArray();
    }

    /**
     * Team-wide category score averages.
     */
    private function getTeamCategoryAverages(string $range): array
    {
        $cats = QaCall::completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                ROUND(AVG(score_opening), 1) as opening,
                ROUND(AVG(score_discovery), 1) as discovery,
                ROUND(AVG(score_presentation), 1) as presentation,
                ROUND(AVG(score_objection_handling), 1) as objection_handling,
                ROUND(AVG(score_closing), 1) as closing,
                ROUND(AVG(score_soft_skills), 1) as soft_skills,
                ROUND(AVG(score_call_control), 1) as call_control
            ')
            ->first();

        return [
            'opening' => $cats->opening ?? 0,
            'discovery' => $cats->discovery ?? 0,
            'presentation' => $cats->presentation ?? 0,
            'objection_handling' => $cats->objection_handling ?? 0,
            'closing' => $cats->closing ?? 0,
            'soft_skills' => $cats->soft_skills ?? 0,
            'call_control' => $cats->call_control ?? 0,
        ];
    }

    /**
     * Score distribution in buckets (0-39, 40-59, 60-79, 80-89, 90-100).
     */
    private function getScoreDistribution(string $range): array
    {
        $result = QaCall::completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                SUM(CASE WHEN total_score < 40 THEN 1 ELSE 0 END) as "0-39",
                SUM(CASE WHEN total_score >= 40 AND total_score < 60 THEN 1 ELSE 0 END) as "40-59",
                SUM(CASE WHEN total_score >= 60 AND total_score < 80 THEN 1 ELSE 0 END) as "60-79",
                SUM(CASE WHEN total_score >= 80 AND total_score < 90 THEN 1 ELSE 0 END) as "80-89",
                SUM(CASE WHEN total_score >= 90 THEN 1 ELSE 0 END) as "90-100"
            ')
            ->first();

        return [
            '0-39' => (int) ($result->{'0-39'} ?? 0),
            '40-59' => (int) ($result->{'40-59'} ?? 0),
            '60-79' => (int) ($result->{'60-79'} ?? 0),
            '80-89' => (int) ($result->{'80-89'} ?? 0),
            '90-100' => (int) ($result->{'90-100'} ?? 0),
        ];
    }

    private function getAgentLeaderboard(string $range): array
    {
        return QaCall::completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->whereNotNull('agent_user_id')
            ->groupBy('agent_user_id', 'agent_name', 'agent_email')
            ->selectRaw('
                agent_user_id,
                agent_name,
                agent_email,
                COUNT(*) as calls_scored,
                ROUND(AVG(total_score), 1) as avg_score,
                ROUND(AVG(qa_calls.duration_seconds), 0) as avg_handle_time,
                SUM(CASE WHEN disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fails,
                SUM(CASE WHEN disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                SUM(CASE WHEN disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_count,
                SUM(CASE WHEN compliance_pass = 1 THEN 1 ELSE 0 END) as compliance_pass_count
            ')
            ->orderByDesc('avg_score')
            ->limit(30)
            ->get()
            ->map(fn ($row) => [
                'agent_user_id' => $row->agent_user_id,
                'agent_name' => $row->agent_name,
                'agent_email' => $row->agent_email,
                'calls_scored' => $row->calls_scored,
                'avg_score' => $row->avg_score,
                'avg_handle_time' => $row->avg_handle_time,
                'compliance_fails' => $row->compliance_fails,
                'compliance_rate' => $row->calls_scored > 0
                    ? round(($row->compliance_pass_count / $row->calls_scored) * 100, 1)
                    : 0,
                'void_risks' => $row->void_risks,
                'excellent_count' => $row->excellent_count,
            ])
            ->toArray();
    }

    private function formatCallSummary(QaCall $call): array
    {
        return [
            'id' => $call->id,
            'zoom_call_id' => $call->zoom_call_id,
            'agent_name' => $call->agent_name,
            'agent_user_id' => $call->agent_user_id,
            'caller_number' => $call->caller_number,
            'callee_number' => $call->callee_number,
            'duration_seconds' => $call->duration_seconds,
            'call_start_time' => $call->call_start_time?->toIso8601String(),
            'disposition' => $call->qaResult?->disposition,
            'total_score' => $call->qaResult?->total_score,
            'compliance_pass' => $call->qaResult?->compliance_pass,
            'scored_by' => $call->scored_by,
        ];
    }

    private function applyRange($query, string $range, string $column = 'call_start_time')
    {
        return match ($range) {
            'today' => $query->whereDate($column, today()),
            'week' => $query->where($column, '>=', now()->startOfWeek()),
            'month' => $query->where($column, '>=', now()->startOfMonth()),
            default => $query->whereDate($column, today()),
        };
    }

    // ── GET /qa/api/sales ──────────────────────────────────────────────

    public function salesQA(Request $request): JsonResponse
    {
        $range = $request->input('range', 'today');
        $page = $request->input('page', 1);
        $filter = $request->input('qa_filter', 'all'); // all, good, avg, bad, pending

        // Query leads that have a sale_at (i.e. actual sales)
        $query = Lead::whereNotNull('sale_at')
            ->when($range === 'today', fn ($q) => $q->whereDate('sale_at', today()))
            ->when($range === 'week', fn ($q) => $q->where('sale_at', '>=', now()->startOfWeek()))
            ->when($range === 'month', fn ($q) => $q->where('sale_at', '>=', now()->startOfMonth()))
            ->when($filter !== 'all', fn ($q) => $q->where('qa_status', match ($filter) {
                'good' => 'Good',
                'avg' => 'Avg',
                'bad' => 'Bad',
                'pending' => 'Pending',
                'review' => 'In Review',
                default => $filter,
            }));

        // Summary KPIs
        $summaryQuery = Lead::whereNotNull('sale_at')
            ->when($range === 'today', fn ($q) => $q->whereDate('sale_at', today()))
            ->when($range === 'week', fn ($q) => $q->where('sale_at', '>=', now()->startOfWeek()))
            ->when($range === 'month', fn ($q) => $q->where('sale_at', '>=', now()->startOfMonth()));

        $summary = $summaryQuery->selectRaw('
            COUNT(*) as total_sales,
            SUM(CASE WHEN qa_status = "Good" THEN 1 ELSE 0 END) as good_sales,
            SUM(CASE WHEN qa_status = "Avg" THEN 1 ELSE 0 END) as avg_sales,
            SUM(CASE WHEN qa_status = "Bad" THEN 1 ELSE 0 END) as bad_sales,
            SUM(CASE WHEN qa_status = "Pending" OR qa_status IS NULL THEN 1 ELSE 0 END) as pending_sales,
            SUM(CASE WHEN qa_status = "In Review" THEN 1 ELSE 0 END) as review_sales,
            SUM(CASE WHEN manager_status = "approved" THEN 1 ELSE 0 END) as mgr_approved,
            SUM(CASE WHEN manager_status = "declined" THEN 1 ELSE 0 END) as mgr_declined,
            SUM(CASE WHEN status = "chargeback" THEN 1 ELSE 0 END) as chargebacks,
            ROUND(AVG(CASE WHEN coverage_amount IS NOT NULL THEN coverage_amount ELSE NULL END), 0) as avg_coverage,
            ROUND(AVG(CASE WHEN monthly_premium IS NOT NULL THEN monthly_premium ELSE NULL END), 2) as avg_premium
        ')->first();

        // Per-closer breakdown
        $closerBreakdown = Lead::whereNotNull('sale_at')
            ->whereNotNull('closer_id')
            ->when($range === 'today', fn ($q) => $q->whereDate('sale_at', today()))
            ->when($range === 'week', fn ($q) => $q->where('sale_at', '>=', now()->startOfWeek()))
            ->when($range === 'month', fn ($q) => $q->where('sale_at', '>=', now()->startOfMonth()))
            ->join('users', 'leads.closer_id', '=', 'users.id')
            ->groupBy('closer_id', 'users.name')
            ->selectRaw('
                closer_id,
                users.name as closer_name,
                COUNT(*) as total_sales,
                SUM(CASE WHEN leads.qa_status = "Good" THEN 1 ELSE 0 END) as good_sales,
                SUM(CASE WHEN leads.qa_status = "Avg" THEN 1 ELSE 0 END) as avg_sales,
                SUM(CASE WHEN leads.qa_status = "Bad" THEN 1 ELSE 0 END) as bad_sales,
                SUM(CASE WHEN leads.qa_status = "Pending" OR leads.qa_status IS NULL THEN 1 ELSE 0 END) as pending_sales,
                SUM(CASE WHEN leads.status = "chargeback" THEN 1 ELSE 0 END) as chargebacks
            ')
            ->orderByDesc('total_sales')
            ->limit(30)
            ->get()
            ->map(fn ($row) => [
                'closer_id' => $row->closer_id,
                'closer_name' => $row->closer_name,
                'total_sales' => $row->total_sales,
                'good_sales' => $row->good_sales,
                'avg_sales' => $row->avg_sales,
                'bad_sales' => $row->bad_sales,
                'pending_sales' => $row->pending_sales,
                'chargebacks' => $row->chargebacks,
                'good_rate' => $row->total_sales > 0
                    ? round(($row->good_sales / $row->total_sales) * 100, 1) : 0,
            ])
            ->toArray();

        // Paginated sales list
        $sales = (clone $query)
            ->with(['assignedCloser:id,name,email', 'qaUser:id,name'])
            ->orderByDesc('sale_at')
            ->paginate(20, ['*'], 'page', $page);

        $items = collect($sales->items())->map(fn (Lead $lead) => [
            'id' => $lead->id,
            'cn_name' => $lead->cn_name,
            'phone_number' => $lead->phone_number,
            'closer_name' => $lead->assignedCloser?->name ?? $lead->closer_name ?? 'Unknown',
            'closer_id' => $lead->closer_id,
            'sale_date' => $lead->sale_at?->toIso8601String(),
            'qa_status' => $lead->qa_status ?? 'Pending',
            'qa_reason' => $lead->qa_reason,
            'manager_status' => $lead->manager_status ?? 'pending',
            'lead_status' => $lead->status,
            'carrier_name' => $lead->carrier_name,
            'coverage_amount' => $lead->coverage_amount,
            'monthly_premium' => $lead->monthly_premium,
            'state' => $lead->state,
        ]);

        return response()->json([
            'summary' => [
                'total_sales' => $summary->total_sales ?? 0,
                'good_sales' => $summary->good_sales ?? 0,
                'avg_sales' => $summary->avg_sales ?? 0,
                'bad_sales' => $summary->bad_sales ?? 0,
                'pending_sales' => $summary->pending_sales ?? 0,
                'review_sales' => $summary->review_sales ?? 0,
                'mgr_approved' => $summary->mgr_approved ?? 0,
                'mgr_declined' => $summary->mgr_declined ?? 0,
                'chargebacks' => $summary->chargebacks ?? 0,
                'avg_coverage' => $summary->avg_coverage ?? 0,
                'avg_premium' => $summary->avg_premium ?? 0,
            ],
            'closer_breakdown' => $closerBreakdown,
            'sales' => $items,
            'pagination' => [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'total' => $sales->total(),
            ],
        ]);
    }
}
