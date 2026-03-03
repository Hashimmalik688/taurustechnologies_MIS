<?php

namespace App\Http\Controllers\QA;

use App\Http\Controllers\Controller;
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
        $page = $request->input('page', 1);
        $filter = $request->input('qa_filter', 'all');

        // ── Primary Team KPIs ──────────────────────────────────────────
        $teamStats = $this->getTeamStats($range);

        // ── Extended KPIs (Final Expense industry metrics) ─────────────
        $extendedKpis = $this->getExtendedKpis($range);

        // ── Agent leaderboard (all agents, ranked by avg score) ────────
        $leaderboard = $this->getAgentLeaderboard($range);

        // ── Sales summary (from salesQA logic) ─────────────────────────
        $salesSummary = $this->getSalesSummary($range);

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

        // ── Paginated calls list with filter ───────────────────────────
        $baseQuery = QaCall::with('qaResult')
            ->completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range));

        if ($filter === 'sales_only') {
            $baseQuery->whereHas('qaResult', fn ($q) => $q->where('is_sale', true));
        } elseif ($filter !== 'all') {
            $disposition = strtoupper($filter);
            $baseQuery->whereHas('qaResult', fn ($q) => $q->where('disposition', $disposition));
        }

        $calls = $baseQuery->orderByDesc('call_start_time')
            ->paginate(20, ['*'], 'page', $page);

        $callItems = collect($calls->items())->map(fn ($call) => $this->formatCallSummary($call));

        // ── Per-closer breakdown ───────────────────────────────────────
        $closerBreakdown = $this->getCloserBreakdown($range, $filter);

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
            'sales_summary' => $salesSummary,
            'agent_leaderboard' => $leaderboard,
            'disposition_chart' => $dispositionChart,
            'score_trend' => $scoreTrend,
            'compliance_trend' => $complianceTrend,
            'top_issues' => $topIssues,
            'compliance_breakdown' => $complianceBreakdown,
            'team_category_averages' => $teamCategoryAverages,
            'score_distribution' => $scoreDistribution,
            'calls' => $callItems,
            'calls_pagination' => [
                'current_page' => $calls->currentPage(),
                'last_page' => $calls->lastPage(),
                'total' => $calls->total(),
            ],
            'closer_breakdown' => $closerBreakdown,
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
                'agent_name' => $call->agent?->name ?? $result?->closer_name_extracted ?? $call->agent_name ?? 'Unknown',
                'agent_email' => $call->agent_email,
                'agent_user_id' => $call->agent_user_id,
                'caller_number' => $call->caller_number,
                'callee_number' => $call->callee_number,
                'customer_name' => $result?->customer_name ?? null,
                'duration_seconds' => $call->duration_seconds,
                'call_start_time' => $call->call_start_time?->toIso8601String(),
                'scored_by' => $call->scored_by,
                'processing_status' => $call->processing_status,
                'is_sale' => (bool) $result?->is_sale,
                'carrier_name' => $result?->carrier_name,
                'sale_amount' => $result?->sale_amount,
                'monthly_premium' => $result?->monthly_premium,
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
                SUM(CASE WHEN compliance_pass = 1 THEN 1 ELSE 0 END) as compliance_pass_count,
                SUM(CASE WHEN qa_results.is_sale = 1 THEN 1 ELSE 0 END) as sales_count,
                SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE 0 END) as total_coverage
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
                'sales_count' => $row->sales_count ?? 0,
                'total_coverage' => $row->total_coverage ?? 0,
            ])
            ->toArray();
    }

    /**
     * Sales summary KPIs (merged from salesQA endpoint).
     */
    private function getSalesSummary(string $range): array
    {
        $summary = QaCall::where('processing_status', 'completed')
            ->whereHas('qaResult')
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                SUM(CASE WHEN qa_results.is_sale = 1 THEN 1 ELSE 0 END) as total_sales,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE 0 END), 0) as total_coverage,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.monthly_premium ELSE 0 END), 2) as total_premium,
                ROUND(AVG(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE NULL END), 0) as avg_coverage,
                ROUND(AVG(CASE WHEN qa_results.is_sale = 1 THEN qa_results.monthly_premium ELSE NULL END), 2) as avg_premium
            ')
            ->first();

        return [
            'total_sales' => $summary->total_sales ?? 0,
            'total_coverage' => $summary->total_coverage ?? 0,
            'total_premium' => $summary->total_premium ?? 0,
            'avg_coverage' => $summary->avg_coverage ?? 0,
            'avg_premium' => $summary->avg_premium ?? 0,
        ];
    }

    /**
     * Per-closer performance breakdown.
     */
    private function getCloserBreakdown(string $range, string $filter = 'all'): array
    {
        $baseQuery = QaCall::where('processing_status', 'completed')
            ->whereHas('qaResult')
            ->whereNotNull('agent_user_id');
        $this->applyRange($baseQuery, $range);

        if ($filter === 'sales_only') {
            $baseQuery->whereHas('qaResult', fn ($q) => $q->where('is_sale', true));
        } elseif ($filter !== 'all') {
            $disposition = strtoupper($filter);
            $baseQuery->whereHas('qaResult', fn ($q) => $q->where('disposition', $disposition));
        }

        return $baseQuery
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->join('users', 'qa_calls.agent_user_id', '=', 'users.id')
            ->groupBy('qa_calls.agent_user_id', 'users.name')
            ->selectRaw('
                qa_calls.agent_user_id,
                users.name as closer_name,
                COUNT(*) as total_calls,
                SUM(CASE WHEN qa_results.is_sale = 1 THEN 1 ELSE 0 END) as total_sales,
                ROUND(AVG(qa_results.total_score), 1) as avg_score,
                SUM(CASE WHEN qa_results.disposition IN ("EXCELLENT","GOOD") THEN 1 ELSE 0 END) as good_calls,
                SUM(CASE WHEN qa_results.disposition IN ("POOR","COMPLIANCE_FAIL") THEN 1 ELSE 0 END) as bad_calls,
                SUM(CASE WHEN qa_results.disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE 0 END), 0) as total_coverage,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.monthly_premium ELSE 0 END), 2) as total_premium
            ')
            ->orderByDesc('total_calls')
            ->limit(30)
            ->get()
            ->map(fn ($row) => [
                'closer_id' => $row->agent_user_id,
                'closer_name' => $row->closer_name,
                'total_calls' => $row->total_calls,
                'total_sales' => $row->total_sales,
                'avg_score' => $row->avg_score,
                'good_calls' => $row->good_calls,
                'bad_calls' => $row->bad_calls,
                'void_risks' => $row->void_risks,
                'total_coverage' => $row->total_coverage,
                'total_premium' => $row->total_premium,
                'sale_rate' => $row->total_calls > 0
                    ? round(($row->total_sales / $row->total_calls) * 100, 1) : 0,
            ])
            ->toArray();
    }

    private function formatCallSummary(QaCall $call): array
    {
        return [
            'id' => $call->id,
            'zoom_call_id' => $call->zoom_call_id,
            'agent_name' => $call->agent?->name ?? $call->qaResult?->closer_name_extracted ?? $call->agent_name ?? 'Unknown',
            'agent_user_id' => $call->agent_user_id,
            'caller_number' => $call->caller_number,
            'callee_number' => $call->callee_number,
            'customer_name' => $call->qaResult?->customer_name ?? null,
            'duration_seconds' => $call->duration_seconds,
            'call_start_time' => $call->call_start_time?->toIso8601String(),
            'disposition' => $call->qaResult?->disposition,
            'total_score' => $call->qaResult?->total_score,
            'compliance_pass' => $call->qaResult?->compliance_pass,
            'scored_by' => $call->scored_by,
            'is_sale' => (bool) $call->qaResult?->is_sale,
            'sale_amount' => $call->qaResult?->sale_amount,
            'monthly_premium' => $call->qaResult?->monthly_premium,
            'carrier_name' => $call->qaResult?->carrier_name,
        ];
    }

    private function applyRange($query, string $range, string $column = 'call_start_time')
    {
        return match ($range) {
            'today' => $query->whereDate($column, today()),
            'week', '7d' => $query->where($column, '>=', now()->subDays(7)->startOfDay()),
            'month', '30d' => $query->where($column, '>=', now()->subDays(30)->startOfDay()),
            '90d' => $query->where($column, '>=', now()->subDays(90)->startOfDay()),
            'all' => $query, // no date filter
            default => $query->where($column, '>=', now()->subDays(30)->startOfDay()),
        };
    }

    // ── GET /qa/api/sales ──────────────────────────────────────────────
    // Sales data comes from QA pipeline: when Gemini scores a call and
    // determines a sale was made, it sets is_sale=true with extracted details.
    // This is the SINGLE source of truth — no separate sales system.

    public function salesQA(Request $request): JsonResponse
    {
        $range = $request->input('range', 'today');
        $page = $request->input('page', 1);
        $filter = $request->input('qa_filter', 'all'); // all, excellent, good, average, poor, compliance_fail, void_risk, sales_only

        // Base: all completed QA calls in range
        $baseQuery = QaCall::where('processing_status', 'completed')
            ->whereHas('qaResult');
        $this->applyRange($baseQuery, $range);

        // Summary KPIs across ALL scored calls (not just sales)
        $summaryBase = QaCall::where('processing_status', 'completed')
            ->whereHas('qaResult');
        $this->applyRange($summaryBase, $range);

        $summary = $summaryBase
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                COUNT(*) as total_calls,
                SUM(CASE WHEN qa_results.is_sale = 1 THEN 1 ELSE 0 END) as total_sales,
                SUM(CASE WHEN qa_results.is_sale = 1 AND qa_results.disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_sales,
                SUM(CASE WHEN qa_results.is_sale = 1 AND qa_results.disposition = "GOOD" THEN 1 ELSE 0 END) as good_sales,
                SUM(CASE WHEN qa_results.is_sale = 1 AND qa_results.disposition = "AVERAGE" THEN 1 ELSE 0 END) as avg_sales,
                SUM(CASE WHEN qa_results.is_sale = 1 AND qa_results.disposition = "POOR" THEN 1 ELSE 0 END) as poor_sales,
                SUM(CASE WHEN qa_results.is_sale = 1 AND qa_results.disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risk_sales,
                SUM(CASE WHEN qa_results.is_sale = 1 AND qa_results.disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fail_sales,
                SUM(CASE WHEN qa_results.disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_calls,
                SUM(CASE WHEN qa_results.disposition = "GOOD" THEN 1 ELSE 0 END) as good_calls,
                SUM(CASE WHEN qa_results.disposition = "AVERAGE" THEN 1 ELSE 0 END) as avg_calls,
                SUM(CASE WHEN qa_results.disposition = "POOR" THEN 1 ELSE 0 END) as poor_calls,
                SUM(CASE WHEN qa_results.disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risk_calls,
                SUM(CASE WHEN qa_results.disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fail_calls,
                ROUND(AVG(qa_results.total_score), 1) as avg_score,
                ROUND(AVG(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE NULL END), 0) as avg_coverage,
                ROUND(AVG(CASE WHEN qa_results.is_sale = 1 THEN qa_results.monthly_premium ELSE NULL END), 2) as avg_premium,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE 0 END), 0) as total_coverage,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.monthly_premium ELSE 0 END), 2) as total_premium
            ')
            ->first();

        // Apply filters for the list/breakdown
        $filteredQuery = clone $baseQuery;
        if ($filter === 'sales_only') {
            $filteredQuery->whereHas('qaResult', fn ($q) => $q->where('is_sale', true));
        } elseif ($filter !== 'all') {
            $disposition = strtoupper($filter);
            $filteredQuery->whereHas('qaResult', fn ($q) => $q->where('disposition', $disposition));
        }

        // Per-closer breakdown
        $closerBreakdown = (clone $filteredQuery)
            ->whereNotNull('agent_user_id')
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->join('users', 'qa_calls.agent_user_id', '=', 'users.id')
            ->groupBy('qa_calls.agent_user_id', 'users.name')
            ->selectRaw('
                qa_calls.agent_user_id,
                users.name as closer_name,
                COUNT(*) as total_calls,
                SUM(CASE WHEN qa_results.is_sale = 1 THEN 1 ELSE 0 END) as total_sales,
                ROUND(AVG(qa_results.total_score), 1) as avg_score,
                SUM(CASE WHEN qa_results.disposition IN ("EXCELLENT","GOOD") THEN 1 ELSE 0 END) as good_calls,
                SUM(CASE WHEN qa_results.disposition IN ("POOR","COMPLIANCE_FAIL") THEN 1 ELSE 0 END) as bad_calls,
                SUM(CASE WHEN qa_results.disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE 0 END), 0) as total_coverage,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.monthly_premium ELSE 0 END), 2) as total_premium
            ')
            ->orderByDesc('total_calls')
            ->limit(30)
            ->get()
            ->map(fn ($row) => [
                'closer_id' => $row->agent_user_id,
                'closer_name' => $row->closer_name,
                'total_calls' => $row->total_calls,
                'total_sales' => $row->total_sales,
                'avg_score' => $row->avg_score,
                'good_calls' => $row->good_calls,
                'bad_calls' => $row->bad_calls,
                'void_risks' => $row->void_risks,
                'total_coverage' => $row->total_coverage,
                'total_premium' => $row->total_premium,
                'sale_rate' => $row->total_calls > 0
                    ? round(($row->total_sales / $row->total_calls) * 100, 1) : 0,
            ])
            ->toArray();

        // Paginated call list with results
        $calls = (clone $filteredQuery)
            ->with(['agent:id,name,email', 'qaResult'])
            ->orderByDesc('call_start_time')
            ->paginate(20, ['*'], 'page', $page);

        $items = collect($calls->items())->map(fn (QaCall $call) => [
            'id' => $call->id,
            'customer_name' => $call->qaResult?->customer_name ?? 'Unknown',
            'customer_phone' => $call->callee_number,
            'closer_name' => $call->agent?->name ?? $call->qaResult?->closer_name_extracted ?? $call->agent_name ?? 'Unknown',
            'closer_id' => $call->agent_user_id,
            'call_date' => $call->call_start_time?->toIso8601String(),
            'duration' => $call->duration_seconds,
            'disposition' => $call->qaResult?->disposition ?? 'N/A',
            'total_score' => $call->qaResult?->total_score ?? 0,
            'is_sale' => (bool) $call->qaResult?->is_sale,
            'sale_amount' => $call->qaResult?->sale_amount,
            'monthly_premium' => $call->qaResult?->monthly_premium,
            'carrier_name' => $call->qaResult?->carrier_name,
            'policy_type' => $call->qaResult?->policy_type,
            'customer_state' => $call->qaResult?->customer_state,
            'compliance_pass' => $call->qaResult?->compliance_pass,
            'void_risk_reason' => $call->qaResult?->void_risk_reason,
            'coaching_notes' => $call->qaResult?->coaching_notes,
            'top_issue' => $call->qaResult?->top_issue,
            'caller_number' => $call->caller_number,
        ]);

        return response()->json([
            'summary' => [
                'total_calls' => $summary->total_calls ?? 0,
                'total_sales' => $summary->total_sales ?? 0,
                'excellent_sales' => $summary->excellent_sales ?? 0,
                'good_sales' => $summary->good_sales ?? 0,
                'avg_sales' => $summary->avg_sales ?? 0,
                'poor_sales' => $summary->poor_sales ?? 0,
                'void_risk_sales' => $summary->void_risk_sales ?? 0,
                'compliance_fail_sales' => $summary->compliance_fail_sales ?? 0,
                'excellent_calls' => $summary->excellent_calls ?? 0,
                'good_calls' => $summary->good_calls ?? 0,
                'avg_calls' => $summary->avg_calls ?? 0,
                'poor_calls' => $summary->poor_calls ?? 0,
                'void_risk_calls' => $summary->void_risk_calls ?? 0,
                'compliance_fail_calls' => $summary->compliance_fail_calls ?? 0,
                'avg_score' => $summary->avg_score ?? 0,
                'avg_coverage' => $summary->avg_coverage ?? 0,
                'avg_premium' => $summary->avg_premium ?? 0,
                'total_coverage' => $summary->total_coverage ?? 0,
                'total_premium' => $summary->total_premium ?? 0,
            ],
            'closer_breakdown' => $closerBreakdown,
            'calls' => $items,
            'pagination' => [
                'current_page' => $calls->currentPage(),
                'last_page' => $calls->lastPage(),
                'total' => $calls->total(),
            ],
        ]);
    }
}
