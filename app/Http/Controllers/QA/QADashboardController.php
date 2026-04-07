<?php

namespace App\Http\Controllers\QA;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\QA\QaCall;
use App\Models\QA\QaComplianceFlag;
use App\Models\QA\QaDailyStat;
use App\Models\QA\QaResult;
use App\Models\Setting;
use App\Models\User;
use App\Services\QA\ClaudeService;
use App\Services\QA\GeminiService;
use App\Services\QA\QAResultService;
use App\Services\QA\QAScoringPrompt;
use App\Services\QA\ZoomTranscriptParser;
use App\Support\Statuses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QADashboardController extends Controller
{
    // ── GET /qa/scoring ─────────────────────────────────────────────────

    public function index()
    {
        return view('qa.dashboard');
    }

    // ── GET /qa/my-report ───────────────────────────────────────────────
    // Personal QA report page for the authenticated closer — shows only
    // their own scored calls, category scores, and coaching notes.

    public function myReport()
    {
        $user = auth()->user();

        // Determine back URL based on the user's role
        if ($user->hasRole('Ravens Closer')) {
            $backUrl = route('ravens.dashboard');
        } else {
            $backUrl = url()->previous(route('home'));
        }

        return view('qa.dashboard', [
            'myMode'   => true,
            'myUserId' => $user->id,
            'myBackUrl' => $backUrl,
        ]);
    }

    // ── GET /qa/api/overview ────────────────────────────────────────────

    public function overview(Request $request): JsonResponse
    {
        $range = $request->input('range', 'today');
        $page = $request->input('page', 1);
        $filter = $request->input('qa_filter', 'all');
        // calls_range allows the Scored Calls section to filter independently
        $callsRange = $request->input('calls_range') ?: $range;

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

        // ── Score trend — try QaDailyStat first, fall back to live query ─
        $trendWindow = match ($range) {
            'today'        => 14,
            '7d', 'week'   => 14,
            '30d', 'month' => 30,
            '90d'          => 90,
            'all'          => 90,
            default        => 30,
        };
        $scoreTrend = QaDailyStat::selectRaw('stat_date, ROUND(AVG(avg_score), 1) as avg')
            ->where('stat_date', '>=', now()->subDays($trendWindow)->toDateString())
            ->groupBy('stat_date')
            ->orderBy('stat_date')
            ->pluck('avg', 'stat_date');

        // Fall back to direct qa_calls query if QaDailyStat has no entries
        if ($scoreTrend->isEmpty()) {
            $scoreTrend = QaResult::join('qa_calls', 'qa_results.qa_call_id', '=', 'qa_calls.id')
                ->where('qa_calls.processing_status', 'completed')
                ->where('qa_calls.call_start_time', '>=', now()->subDays($trendWindow)->startOfDay())
                ->whereNotNull('qa_results.total_score')
                ->selectRaw('DATE(qa_calls.call_start_time) as stat_date, ROUND(AVG(qa_results.total_score), 1) as avg')
                ->groupBy('stat_date')
                ->orderBy('stat_date')
                ->pluck('avg', 'stat_date');
        }

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
            ->when(true, fn ($q) => $this->applyRange($q, $callsRange));

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
        $range = $request->input('range', '30d');
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
                SUM(CASE WHEN disposition = "EXCEPTIONAL" THEN 1 ELSE 0 END) as exceptional_count,
                SUM(CASE WHEN disposition = "GOOD" THEN 1 ELSE 0 END) as good_count,
                SUM(CASE WHEN disposition = "AVERAGE" THEN 1 ELSE 0 END) as average_count,
                SUM(CASE WHEN disposition = "POOR" THEN 1 ELSE 0 END) as poor_count,
                SUM(CASE WHEN compliance_pass = 0 OR disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fails,
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
                'exceptional_count' => $summary->exceptional_count ?? 0,
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
        $call = QaCall::with(['qaResult', 'complianceFlags', 'lead'])->findOrFail($id);

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
                'lead_phone'    => $call->lead?->phone_number,
                'customer_name' => $call->lead?->cn_name ?? $result?->customer_name ?? null,
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
                'score_disposition' => $result->score_disposition,
                'total_score' => $result->total_score,
                'compliance_pass' => $result->compliance_pass,
                'compliance_checks' => [
                    'C1_agent_identity'             => $this->mapComplianceValue($result->c2_agent_identity),
                    'C2_carrier_named'              => $this->mapComplianceValue($result->c3_carrier_named),
                    'C3_product_type_stated'        => $this->mapComplianceValue($result->c4_product_type_stated),
                    'C4_health_questions_complete'  => $this->mapComplianceValue($result->c5_health_questions_complete),
                    'C5_quote_and_coverage'         => $this->mapComplianceValue($result->c6_proper_quote),
                    'C6_draft_date_confirmed'       => $this->mapComplianceValue($result->c8_draft_date_confirmed),
                    'C7_recorded_consent'           => $this->mapComplianceValue($result->c9_end_of_call_consent),
                    'C8_application_info_collected' => $this->mapComplianceValue($result->c11_application_info_collected),
                    'C9_customer_not_on_dnc'        => $this->mapComplianceValue($result->c12_customer_not_on_dnc),
                    'C10_agent_handles_objections'  => $this->mapComplianceValue($result->c14_customer_not_disinterested),
                    'C11_appropriate_language'      => $this->mapComplianceValue($result->c16_appropriate_language),
                ],
                'compliance_failures' => $result->compliance_failures,
                'compliance_details'  => $result->raw_ai_response['compliance_details'] ?? [],
                'score_breakdown' => [
                    'opening'            => (int) ($result->score_opening ?? 0),
                    'discovery'          => (int) ($result->score_discovery ?? 0),
                    'presentation'       => (int) ($result->score_presentation ?? 0),
                    'objection_handling' => (int) ($result->score_objection_handling ?? 0),
                    'closing'            => (int) ($result->score_closing ?? 0),
                    'soft_skills'        => (int) ($result->score_soft_skills ?? 0),
                    'call_control'       => (int) ($result->score_call_control ?? 0),
                ],
                'coaching_notes' => $result->coaching_notes,
                'top_issue' => $result->top_issue,
                'strengths' => $result->strengths,
                'improvements' => $result->improvements,
                'void_risk_reason' => $result->void_risk_reason,
                'informational_notes' => $result->raw_ai_response['informational_notes'] ?? [],
                'dnc_judge' => [
                    'risk_level' => $result->dnc_risk_level ?? 'NONE',
                    'verdict'    => $result->dnc_judge_verdict ?? 'Clean',
                    'reasoning'  => $result->dnc_judge_reasoning,
                ],
            ] : null,
            'compliance_flags' => $call->complianceFlags->map(fn ($f) => [
                'check_code' => $f->check_code,
                'check_label' => $f->check_label,
                'ai_reasoning' => $f->ai_reasoning,
                'flagged_at' => $f->flagged_at->toIso8601String(),
            ]),
            'transcript' => $transcriptLines,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════════

    /** Convert 'pass'/'fail'/'na' DB string to boolean|null for the frontend. */
    private function mapComplianceValue(?string $v): bool|null
    {
        return match ($v) {
            'pass' => true,
            'fail' => false,
            default => null,
        };
    }

    // ── GET /qa/manual ───────────────────────────────────────────────────

    public function showManualSubmit()
    {
        $agents = User::whereHas('roles', fn($q) => $q->whereIn('name', [
            'Ravens Closer', 'Peregrine Closer', 'Agent', 'Employee',
        ]))->orderBy('name')->get(['id', 'name']);

        return view('qa.manual', compact('agents'));
    }

    // ── POST /qa/api/manual-score ────────────────────────────────────────

    public function manualScore(Request $request): JsonResponse
    {
        $request->validate([
            'transcript'    => 'required|string|min:200',
            'agent_user_id' => 'nullable|integer|exists:users,id',
            'call_date'     => 'nullable|date',
        ]);

        $raw      = $request->input('transcript');
        $agentId  = $request->input('agent_user_id');
        $callDate = $request->input('call_date')
            ? now()->parse($request->input('call_date'))
            : now();

        // Parse Zoom transcript → diarized AGENT:/CUSTOMER: format
        $parsed = ZoomTranscriptParser::parse($raw);

        if (empty(trim($parsed['diarized']))) {
            return response()->json([
                'success' => false,
                'message' => 'Could not parse transcript. Please check the format.',
            ], 422);
        }

        $durationSeconds = $parsed['duration_seconds'] ?: 600;
        $agentUser       = $agentId ? User::find($agentId) : null;

        // Create a QaCall record for this manual submission
        $qaCall = QaCall::create([
            'zoom_call_id'        => 'manual-' . uniqid(),
            'agent_user_id'       => $agentId,
            'agent_name'          => $agentUser?->name ?? $parsed['agent_name'] ?? 'Unknown',
            'agent_email'         => $agentUser?->email,
            'duration_seconds'    => $durationSeconds,
            'call_start_time'     => $callDate,
            'transcript_plain'    => $raw,
            'transcript_diarized' => $parsed['diarized'],
            'transcript_source'   => 'manual',
            'processing_status'   => 'scoring',
            'scored_by'           => 'claude',
        ]);

        Log::info('[QA:Manual] Scoring manual transcript', [
            'qa_call_id'   => $qaCall->id,
            'agent_name'   => $qaCall->agent_name,
            'duration_sec' => $durationSeconds,
            'utterances'   => count($parsed['lines']),
            'by_user'      => auth()->user()?->name,
        ]);

        try {
            $claude   = new ClaudeService();
            $aiResult = $claude->analyzePreLabeledCall($parsed['diarized'], $durationSeconds);
        } catch (\Throwable $e) {
            Log::warning('[QA:Manual] Claude failed, falling back to Gemini', [
                'error' => $e->getMessage(),
            ]);
            try {
                $gemini   = new GeminiService();
                $aiResult = $gemini->analyzePreLabeledCall($parsed['diarized'], $durationSeconds);
            } catch (\Throwable $e2) {
                $qaCall->update([
                    'processing_status' => 'failed',
                    'failure_reason'    => $e2->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'AI scoring failed: ' . $e2->getMessage(),
                ], 500);
            }
        }

        // Persist result
        $resultService = new QAResultService();
        $qaResult      = $resultService->saveResult($qaCall, $aiResult);

        // Rebuild compliance_checks for frontend (individual columns → map)
        $compChecks = [
            'C1_agent_identity'             => $this->mapComplianceValue($qaResult->c2_agent_identity),
            'C2_carrier_named'              => $this->mapComplianceValue($qaResult->c3_carrier_named),
            'C3_product_type_stated'        => $this->mapComplianceValue($qaResult->c4_product_type_stated),
            'C4_health_questions_complete'  => $this->mapComplianceValue($qaResult->c5_health_questions_complete),
            'C5_quote_and_coverage'         => $this->mapComplianceValue($qaResult->c6_proper_quote),
            'C6_draft_date_confirmed'       => $this->mapComplianceValue($qaResult->c8_draft_date_confirmed),
            'C7_recorded_consent'           => $this->mapComplianceValue($qaResult->c9_end_of_call_consent),
            'C8_application_info_collected' => $this->mapComplianceValue($qaResult->c11_application_info_collected),
            'C9_customer_not_on_dnc'        => $this->mapComplianceValue($qaResult->c12_customer_not_on_dnc),
            'C10_agent_handles_objections'  => $this->mapComplianceValue($qaResult->c14_customer_not_disinterested),
            'C11_appropriate_language'      => $this->mapComplianceValue($qaResult->c16_appropriate_language),
        ];

        Log::info('[QA:Manual] Scoring complete', [
            'qa_call_id'  => $qaCall->id,
            'disposition' => $qaResult->disposition,
            'score_disposition' => $qaResult->score_disposition,
            'total_score' => $qaResult->total_score,
            'compliance'  => $qaResult->compliance_pass ? 'PASS' : 'FAIL',
        ]);

        return response()->json([
            'success'    => true,
            'qa_call_id' => $qaCall->id,
            'result' => [
                'disposition'         => $qaResult->disposition,
                'score_disposition'   => $qaResult->score_disposition,
                'total_score'         => $qaResult->total_score,
                'compliance_pass'     => $qaResult->compliance_pass,
                'compliance_checks'   => $compChecks,
                'compliance_failures' => $qaResult->compliance_failures,
                'compliance_details'  => $qaResult->raw_ai_response['compliance_details'] ?? [],
                'void_risk_reason'    => $qaResult->void_risk_reason,
                'informational_notes' => $qaResult->raw_ai_response['informational_notes'] ?? [],
                'score_breakdown' => [
                    'opening'            => (int)($qaResult->score_opening ?? 0),
                    'discovery'          => (int)($qaResult->score_discovery ?? 0),
                    'presentation'       => (int)($qaResult->score_presentation ?? 0),
                    'objection_handling' => (int)($qaResult->score_objection_handling ?? 0),
                    'closing'            => (int)($qaResult->score_closing ?? 0),
                    'soft_skills'        => (int)($qaResult->score_soft_skills ?? 0),
                    'call_control'       => (int)($qaResult->score_call_control ?? 0),
                ],
                'coaching_notes'  => $qaResult->coaching_notes,
                'top_issue'       => $qaResult->top_issue,
                'strengths'       => $qaResult->strengths,
                'improvements'    => $qaResult->improvements,
                'customer_name'   => $qaCall->lead?->cn_name ?? $qaResult->customer_name,
                'carrier_name'    => $qaResult->carrier_name,
                'is_sale'         => $qaResult->is_sale,
                'monthly_premium' => $qaResult->monthly_premium,
                'sale_amount'     => $qaResult->sale_amount,
            ],
            'parsed' => [
                'agent_name'      => $parsed['agent_name'],
                'duration_seconds'=> $durationSeconds,
                'utterance_count' => count($parsed['lines']),
            ],
        ]);
    }

    private function getTeamStats(string $range): array
    {
        $base = QaCall::completed()
            ->when(true, fn ($q) => $this->applyRange($q, $range));

        $callsScored = (clone $base)->count();

        $scores = (clone $base)
            ->join('qa_results', 'qa_calls.id', '=', 'qa_results.qa_call_id')
            ->selectRaw('
                ROUND(AVG(total_score), 1) as avg_score,
                SUM(CASE WHEN compliance_pass = 0 OR disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fails,
                SUM(CASE WHEN disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                SUM(CASE WHEN disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_count,
                SUM(CASE WHEN disposition = "EXCEPTIONAL" THEN 1 ELSE 0 END) as exceptional_count,
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
            'exceptional_count' => $scores->exceptional_count ?? 0,
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

        // Short calls (<7 min) — skipped — from all calls processed (including failed)
        $shortCallsSkipped = QaCall::where('processing_status', 'completed')
            ->when(true, fn ($q) => $this->applyRange($q, $range))
            ->where(function ($q) {
                $q->whereNull('duration_seconds')
                    ->orWhere('duration_seconds', '<', 420);
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
                SUM(CASE WHEN compliance_pass = 0 OR disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fails,
                SUM(CASE WHEN disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                SUM(CASE WHEN disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_count,
                SUM(CASE WHEN disposition = "EXCEPTIONAL" THEN 1 ELSE 0 END) as exceptional_count,
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
                'exceptional_count' => $row->exceptional_count ?? 0,
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
                SUM(CASE WHEN qa_results.disposition = "POOR" OR qa_results.compliance_pass = 0 THEN 1 ELSE 0 END) as bad_calls,
                SUM(CASE WHEN qa_results.disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risks,
                SUM(CASE WHEN qa_results.compliance_pass = 1 THEN 1 ELSE 0 END) as compliance_pass_count,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.sale_amount ELSE 0 END), 0) as total_coverage,
                ROUND(SUM(CASE WHEN qa_results.is_sale = 1 THEN qa_results.monthly_premium ELSE 0 END), 2) as total_premium
            ')
            ->orderByDesc('avg_score')
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
                'compliance_rate' => $row->total_calls > 0
                    ? round(($row->compliance_pass_count / $row->total_calls) * 100, 1) : 0,
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
            'customer_name' => $call->lead?->cn_name ?? $call->qaResult?->customer_name ?? null,
            'duration_seconds' => $call->duration_seconds,
            'call_start_time' => $call->call_start_time?->toIso8601String(),
            'disposition' => $call->qaResult?->disposition,
            'score_disposition' => $call->qaResult?->score_disposition,
            'total_score' => $call->qaResult?->total_score,
            'compliance_pass' => $call->qaResult?->compliance_pass,
            'compliance_failures' => $call->qaResult?->compliance_failures ?? [],
            'scored_by' => $call->scored_by,
            'is_sale' => (bool) $call->qaResult?->is_sale,
            'sale_amount' => $call->qaResult?->sale_amount,
            'monthly_premium' => $call->qaResult?->monthly_premium,
            'carrier_name' => $call->qaResult?->carrier_name,
        ];
    }

    /**
     * POST /qa/api/rerun-today
     *
     * Resets all of today's completed QA calls back to 'pending' and re-queues
     * them for AI scoring with the latest prompt. Transcripts are already stored,
     * so only the AI scoring step (and diarization for Whisper calls) is rerun.
     */
    public function rerunToday(): JsonResponse
    {
        $calls = QaCall::whereDate('call_start_time', today())
            ->where('processing_status', 'completed')
            ->get();

        if ($calls->isEmpty()) {
            return response()->json(['success' => true, 'count' => 0, 'message' => 'No completed calls found for today.']);
        }

        $count = 0;
        foreach ($calls as $call) {
            // Delete existing QA result so it gets re-created fresh
            $call->qaResult()->delete();

            // For Whisper calls, also clear the stale diarized transcript so the AI
            // re-diarizes with the fixed speaker identification prompt.
            $update = ['processing_status' => 'pending', 'failure_reason' => null, 'retry_count' => 0];
            if ($call->transcript_source !== 'zoom') {
                $update['transcript_diarized'] = '';
            }
            $call->update($update);

            \App\Jobs\QA\DownloadAndProcessRecording::dispatch($call->id);
            $count++;
        }

        Log::info('[QA:RerunToday] Re-queued today\'s calls', ['count' => $count, 'date' => today()->toDateString()]);

        return response()->json(['success' => true, 'count' => $count]);
    }

    private function applyRange($query, string $range, string $column = 'call_start_time')
    {
        // Handle custom date ranges: range format can be "startDate,endDate" (ISO 8601)
        if (strpos($range, ',') !== false) {
            [$startStr, $endStr] = explode(',', $range, 2);
            $startDate = \Carbon\Carbon::parse($startStr)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endStr)->endOfDay();
            return $query->whereBetween($column, [$startDate, $endDate]);
        }

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
                SUM(CASE WHEN qa_results.is_sale = 1 AND (qa_results.compliance_pass = 0 OR qa_results.disposition = "COMPLIANCE_FAIL") THEN 1 ELSE 0 END) as compliance_fail_sales,
                SUM(CASE WHEN qa_results.disposition = "EXCELLENT" THEN 1 ELSE 0 END) as excellent_calls,
                SUM(CASE WHEN qa_results.disposition = "GOOD" THEN 1 ELSE 0 END) as good_calls,
                SUM(CASE WHEN qa_results.disposition = "AVERAGE" THEN 1 ELSE 0 END) as avg_calls,
                SUM(CASE WHEN qa_results.disposition = "POOR" THEN 1 ELSE 0 END) as poor_calls,
                SUM(CASE WHEN qa_results.disposition = "VOID_RISK" THEN 1 ELSE 0 END) as void_risk_calls,
                SUM(CASE WHEN qa_results.compliance_pass = 0 OR qa_results.disposition = "COMPLIANCE_FAIL" THEN 1 ELSE 0 END) as compliance_fail_calls,
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
        } elseif ($filter === 'compliance_fail') {
            $filteredQuery->whereHas('qaResult', fn ($q) => $q->where('compliance_pass', false)->orWhere('disposition', 'COMPLIANCE_FAIL'));
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
                SUM(CASE WHEN qa_results.disposition = "POOR" OR qa_results.compliance_pass = 0 THEN 1 ELSE 0 END) as bad_calls,
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
            ->with(['agent:id,name,email', 'qaResult', 'lead:id,cn_name'])
            ->orderByDesc('call_start_time')
            ->paginate(20, ['*'], 'page', $page);

        $items = collect($calls->items())->map(fn (QaCall $call) => [
            'id' => $call->id,
            'customer_name' => $call->lead?->cn_name ?? $call->qaResult?->customer_name ?? 'Unknown',
            'customer_phone' => $call->callee_number,
            'closer_name' => $call->agent?->name ?? $call->qaResult?->closer_name_extracted ?? $call->agent_name ?? 'Unknown',
            'closer_id' => $call->agent_user_id,
            'call_date' => $call->call_start_time?->toIso8601String(),
            'duration' => $call->duration_seconds,
            'disposition' => $call->qaResult?->disposition ?? 'N/A',
            'score_disposition' => $call->qaResult?->score_disposition,
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

    // ── GET /qa/api/qa-status ──────────────────────────────────────────────
    // Returns whether QA scoring is currently enabled and custom prompt info.

    public function qaStatus(): JsonResponse
    {
        return response()->json([
            'qa_enabled'    => (bool) Setting::get('qa_enabled', true),
            'scored_custom' => QAScoringPrompt::hasCustomPrompt(),
        ]);
    }

    // ── POST /qa/api/toggle ───────────────────────────────────────────────
    // Toggle QA scoring on or off globally.

    public function toggleQa(Request $request): JsonResponse
    {
        $current = (bool) Setting::get('qa_enabled', true);
        $newValue = ! $current;

        Setting::set('qa_enabled', $newValue ? '1' : '0', 'boolean', 'Whether QA scoring agent processes new calls', 'qa');

        Log::info('[QA] QA scoring toggled', [
            'by_user' => auth()->user()?->name ?? 'unknown',
            'enabled' => $newValue,
        ]);

        return response()->json([
            'qa_enabled' => $newValue,
            'message'    => $newValue ? 'QA scoring resumed.' : 'QA scoring paused. Future calls will not be scored.',
        ]);
    }

    // ── GET /qa/script ────────────────────────────────────────────────────
    // Show the QA script editor page.

    public function showScript()
    {
        $template  = QAScoringPrompt::getTemplate();
        $hasCustom = QAScoringPrompt::hasCustomPrompt();

        return view('qa.script', compact('template', 'hasCustom'));
    }

    // ── POST /qa/api/script ───────────────────────────────────────────────
    // Save a custom QA scoring script/template.

    public function saveScript(Request $request): JsonResponse
    {
        $request->validate([
            'type'    => 'required|in:scored',
            'content' => 'required|string|min:100',
        ]);

        $type    = $request->input('type');
        $content = $request->input('content');

        QAScoringPrompt::saveTemplate($type, $content);

        Log::info('[QA] QA prompt template updated', [
            'type'    => $type,
            'by_user' => auth()->user()?->name ?? 'unknown',
            'length'  => strlen($content),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Scoring script saved. New calls will use this prompt.',
        ]);
    }

    // ── POST /qa/api/script/reset ─────────────────────────────────────────
    // Reset a QA scoring script back to the built-in default.

    public function resetScript(Request $request): JsonResponse
    {
        $request->validate(['type' => 'required|in:scored']);
        $type = $request->input('type');

        QAScoringPrompt::resetTemplate($type);

        Log::info('[QA] QA prompt template reset to default', [
            'type'    => $type,
            'by_user' => auth()->user()?->name ?? 'unknown',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Script reset to built-in default.',
            'template' => QAScoringPrompt::getTemplate($type),
        ]);
    }

    public function deleteCall(int $id): JsonResponse
    {
        try {
            $call = QaCall::findOrFail($id);

            // Reset lead QA status to In Review so it shows back in the QA Review queue
            if ($call->lead_id) {
                DB::table('leads')
                    ->where('id', $call->lead_id)
                    ->update([
                        'qa_status'      => Statuses::QA_PENDING,
                        'qa_reason'      => null,
                        'qa_reviewed_at' => null,
                        'qa_user_id'     => null,
                        'updated_at'     => now(),
                    ]);
            }

            QaResult::where('qa_call_id', $id)->delete();
            $call->delete();

            Log::info('[QA] Call deleted', [
                'qa_call_id' => $id,
                'agent_name' => $call->agent_name,
                'lead_id'    => $call->lead_id,
                'by_user'    => auth()->user()?->name ?? 'unknown',
            ]);

            return response()->json(['success' => true, 'message' => 'Call deleted.']);

        } catch (\Throwable $e) {
            Log::error('[QA] Delete call failed', ['qa_call_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ASSEMBLYAI — AUDIO UPLOAD & TRANSCRIPTION PIPELINE
    // ══════════════════════════════════════════════════════════════════════════

    // ── GET /qa/upload ──────────────────────────────────────────────────────

    public function showUploadScore()
    {
        $agents = \App\Models\User::whereHas('roles', fn ($q) => $q->whereIn('name', [
            'Ravens Closer', 'Peregrine Closer', 'Agent', 'Employee',
        ]))->orderBy('name')->get(['id', 'name']);

        return view('qa.upload', compact('agents'));
    }

    // ── POST /qa/api/upload-transcribe ──────────────────────────────────────
    //
    // 1. Accept audio file upload (mp3/wav/m4a/mp4/ogg/webm)
    // 2. Store locally in storage/app/qa_audio/
    // 3. Upload to AssemblyAI
    // 4. Create a QaCall record with status = 'transcribing'
    // 5. Return the QaCall ID and AssemblyAI transcript_id for async polling

    public function uploadAndTranscribe(Request $request): JsonResponse
    {
        $request->validate([
            'audio'           => 'required|file|mimes:mp3,wav,m4a,mp4,ogg,webm,flac,aac|max:51200',
            'audio2'          => 'nullable|file|mimes:mp3,wav,m4a,mp4,ogg,webm,flac,aac|max:51200',
            'audio_extra.*'   => 'nullable|file|mimes:mp3,wav,m4a,mp4,ogg,webm,flac,aac|max:51200',
            'agent_user_id'   => 'nullable|integer|exists:users,id',
            'call_date'       => 'nullable|date',
            'swap_speakers'   => 'nullable|boolean',
        ]);

        $file       = $request->file('audio');
        $file2      = $request->file('audio2');          // optional Part 2
        $extraFiles = $request->file('audio_extra', []); // optional Parts 3, 4, 5…
        $agentId    = $request->input('agent_user_id');
        $callDate   = $request->input('call_date') ? now()->parse($request->input('call_date')) : now();
        $agentUser  = $agentId ? \App\Models\User::find($agentId) : null;

        // Store all files up front so we can clean up on error
        $storedPath  = $file->store('qa_audio', 'local');
        $storedPath2 = $file2 ? $file2->store('qa_audio', 'local') : null;

        $extraStored = [];
        foreach ($extraFiles as $ef) {
            $extraStored[] = $ef ? $ef->store('qa_audio', 'local') : null;
        }

        Log::info('[QA:Upload] Audio received', [
            'original_name'  => $file->getClientOriginalName(),
            'original_name2' => $file2?->getClientOriginalName(),
            'extra_parts'    => count(array_filter($extraFiles)),
            'size_kb'        => round($file->getSize() / 1024, 1),
            'stored_path'    => $storedPath,
            'by_user'        => auth()->user()?->name,
        ]);

        $transcriptId2 = null;
        $extraPartsData = [];
        try {
            $assembly = new \App\Services\QA\AssemblyAIService();

            // Upload Part 1
            $uploadUrl    = $assembly->uploadAudio($file->getRealPath());
            $transcriptId = $assembly->submitTranscription($uploadUrl);

            // Upload Part 2 (if provided)
            if ($file2 && $storedPath2) {
                $uploadUrl2    = $assembly->uploadAudio($file2->getRealPath());
                $transcriptId2 = $assembly->submitTranscription($uploadUrl2);
            }

            // Upload Parts 3+ (if provided)
            foreach ($extraFiles as $i => $ef) {
                if (!$ef) continue;
                $euUrl = $assembly->uploadAudio($ef->getRealPath());
                $etId  = $assembly->submitTranscription($euUrl);
                $extraPartsData[] = [
                    'audio_file_path'          => $extraStored[$i] ?? null,
                    'audio_original_name'      => $ef->getClientOriginalName(),
                    'assemblyai_transcript_id' => $etId,
                ];
            }

        } catch (\Throwable $e) {
            Log::error('[QA:Upload] AssemblyAI error', ['error' => $e->getMessage()]);

            $allPaths = array_filter(array_merge(
                [$storedPath, $storedPath2],
                $extraStored,
            ));
            \Illuminate\Support\Facades\Storage::disk('local')->delete($allPaths);

            return response()->json([
                'success' => false,
                'message' => 'AssemblyAI error: ' . $e->getMessage(),
            ], 500);
        }

        $qaCall = QaCall::create([
            'zoom_call_id'             => 'upload-' . uniqid(),
            'agent_user_id'            => $agentId,
            'agent_name'               => $agentUser?->name ?? 'Unknown',
            'agent_email'              => $agentUser?->email,
            'call_start_time'          => $callDate,
            'transcript_source'        => 'assemblyai',
            'processing_status'        => 'transcribing',
            'scored_by'                => 'claude',
            'audio_file_path'          => $storedPath,
            'audio_original_name'      => $file->getClientOriginalName(),
            'assemblyai_transcript_id' => $transcriptId,
            'assemblyai_status'        => 'processing',
            // Part 2 (nullable)
            'audio_file_path_2'          => $storedPath2,
            'audio_original_name_2'      => $file2?->getClientOriginalName(),
            'assemblyai_transcript_id_2' => $transcriptId2,
            // Parts 3+ (JSON array, nullable)
            'extra_parts'                => $extraPartsData ?: null,
        ]);

        $totalParts = 1 + (int)(bool)$transcriptId2 + count($extraPartsData);

        Log::info('[QA:Upload] QaCall created, transcription queued', [
            'qa_call_id'     => $qaCall->id,
            'transcript_id'  => $transcriptId,
            'transcript_id2' => $transcriptId2,
            'extra_parts'    => count($extraPartsData),
            'total_parts'    => $totalParts,
        ]);

        return response()->json([
            'success'       => true,
            'qa_call_id'    => $qaCall->id,
            'transcript_id' => $transcriptId,
            'total_parts'   => $totalParts,
            'message'       => $totalParts > 1
                ? "All {$totalParts} audio parts uploaded. Transcribing…"
                : 'Audio uploaded. Transcription in progress…',
        ]);
    }

    // ── GET /qa/api/transcription/{qaCallId}/status ─────────────────────────
    //
    // Poll AssemblyAI for transcript status.
    // When completed, automatically triggers Claude scoring.
    // Returns current status and — when done — the scoring result.

    public function transcriptionStatus(int $qaCallId): JsonResponse
    {
        $qaCall = QaCall::findOrFail($qaCallId);

        if (!$qaCall->assemblyai_transcript_id) {
            return response()->json(['success' => false, 'message' => 'No AssemblyAI transcript ID for this call.'], 404);
        }

        // If already fully scored, return cached result immediately
        if ($qaCall->processing_status === 'completed') {
            return $this->buildScoredResponse($qaCall);
        }

        // If previously errored, surface the error
        if ($qaCall->processing_status === 'failed') {
            return response()->json([
                'success'    => false,
                'status'     => 'failed',
                'message'    => $qaCall->failure_reason ?? 'Processing failed.',
                'qa_call_id' => $qaCall->id,
            ]);
        }

        try {
            $assembly = new \App\Services\QA\AssemblyAIService();
            $data     = $assembly->getTranscript($qaCall->assemblyai_transcript_id);
            $status   = $data['status'] ?? 'unknown';

            // Persist latest AssemblyAI status
            $qaCall->update(['assemblyai_status' => $status]);

            if ($status === 'error') {
                $qaCall->update([
                    'processing_status' => 'failed',
                    'failure_reason'    => 'AssemblyAI error: ' . ($data['error'] ?? 'unknown'),
                ]);

                return response()->json([
                    'success'    => false,
                    'status'     => 'error',
                    'message'    => 'Transcription failed: ' . ($data['error'] ?? 'unknown'),
                    'qa_call_id' => $qaCall->id,
                ]);
            }

            if ($status !== 'completed') {
                return response()->json([
                    'success'    => true,
                    'status'     => $status,   // 'queued' or 'processing'
                    'qa_call_id' => $qaCall->id,
                    'message'    => 'Transcription in progress…',
                ]);
            }

            // ─── Transcript is ready — parse + score ─────────────────────

            $parsed      = $assembly->parseTranscriptResult($data);
            $diarized    = $parsed['diarized'];
            $durationSec = $parsed['duration_seconds'] ?: $qaCall->duration_seconds ?: 600;

            // ─── Part 2: if a second transcript exists, wait for it then merge ───
            if ($qaCall->assemblyai_transcript_id_2) {
                $data2   = $assembly->getTranscript($qaCall->assemblyai_transcript_id_2);
                $status2 = $data2['status'] ?? 'unknown';

                if ($status2 === 'error') {
                    // Part 2 failed — log and continue with Part 1 only
                    Log::warning('[QA:Upload] Part 2 transcript failed, scoring Part 1 only', [
                        'qa_call_id' => $qaCall->id,
                        'error'      => $data2['error'] ?? 'unknown',
                    ]);
                } elseif ($status2 !== 'completed') {
                    // Part 2 still processing — tell frontend to keep polling
                    return response()->json([
                        'success'    => true,
                        'status'     => 'processing',
                        'qa_call_id' => $qaCall->id,
                        'message'    => 'Part 1 transcribed. Waiting for Part 2…',
                    ]);
                } else {
                    // Both parts ready — merge transcripts
                    $parsed2   = $assembly->parseTranscriptResult($data2);
                    $diarized2 = $parsed2['diarized'];

                    // Apply independent speaker swap for Part 2 if requested
                    if (request()->boolean('swap_speakers_2')) {
                        $diarized2 = \App\Services\QA\AssemblyAIService::swapSpeakers($diarized2);
                    }

                    $diarized = $diarized
                        . "\n\n[--- CALL DISCONNECTED — PART 2 ---]\n\n"
                        . $diarized2;
                    // Use the longer duration as the representative duration
                    $durationSec = max($durationSec, $parsed2['duration_seconds'] ?: 0);

                    Log::info('[QA:Upload] Two-part transcript merged', [
                        'qa_call_id'    => $qaCall->id,
                        'part1_seconds' => $parsed['duration_seconds'],
                        'part2_seconds' => $parsed2['duration_seconds'],
                    ]);
                }
            }

            // ─── Parts 3+: if extra_parts are stored, wait for each then merge ───
            if (!empty($qaCall->extra_parts)) {
                $swapExtra = request()->input('swap_extra', []);
                foreach ($qaCall->extra_parts as $i => $extraPart) {
                    $etId = $extraPart['assemblyai_transcript_id'] ?? null;
                    if (!$etId) continue;

                    $partNum  = $i + 3; // index 0 = Part 3, index 1 = Part 4 …
                    $dataExt  = $assembly->getTranscript($etId);
                    $statusExt = $dataExt['status'] ?? 'unknown';

                    if ($statusExt === 'error') {
                        Log::warning('[QA:Upload] Extra part transcript failed, skipping', [
                            'qa_call_id' => $qaCall->id,
                            'part'       => $partNum,
                            'error'      => $dataExt['error'] ?? 'unknown',
                        ]);
                        continue;
                    }

                    if ($statusExt !== 'completed') {
                        // Still processing — tell frontend to keep polling
                        return response()->json([
                            'success'    => true,
                            'status'     => 'processing',
                            'qa_call_id' => $qaCall->id,
                            'message'    => "Parts 1–" . ($partNum - 1) . " transcribed. Waiting for Part {$partNum}…",
                        ]);
                    }

                    $parsedExt   = $assembly->parseTranscriptResult($dataExt);
                    $diarizedExt = $parsedExt['diarized'];

                    // Apply per-part speaker swap if requested
                    if (!empty($swapExtra[$i])) {
                        $diarizedExt = \App\Services\QA\AssemblyAIService::swapSpeakers($diarizedExt);
                    }

                    $diarized    .= "\n\n[--- CALL DISCONNECTED — PART {$partNum} ---]\n\n" . $diarizedExt;
                    $durationSec  = max($durationSec, $parsedExt['duration_seconds'] ?: 0);

                    Log::info('[QA:Upload] Extra part transcript merged', [
                        'qa_call_id'  => $qaCall->id,
                        'part'        => $partNum,
                        'seconds'     => $parsedExt['duration_seconds'],
                    ]);
                }
            }

            // Optional speaker swap from the original upload request
            if (request()->boolean('swap_speakers')) {
                $diarized = \App\Services\QA\AssemblyAIService::swapSpeakers($diarized);
            }

            // Persist transcript
            $qaCall->update([
                'transcript_plain'    => $parsed['text'],
                'transcript_diarized' => $diarized,
                'duration_seconds'    => $durationSec,
                'processing_status'   => 'scoring',
                'assemblyai_status'   => 'completed',
            ]);

            // Score with Claude → fallback Gemini
            try {
                $claude   = new ClaudeService();
                $aiResult = $claude->analyzePreLabeledCall($diarized, $durationSec);
                $qaCall->update(['scored_by' => 'claude']);
            } catch (\Throwable $e) {
                Log::warning('[QA:Upload] Claude failed, falling back to Gemini', ['error' => $e->getMessage()]);
                try {
                    $gemini   = new GeminiService();
                    $aiResult = $gemini->analyzePreLabeledCall($diarized, $durationSec);
                    $qaCall->update(['scored_by' => 'gemini']);
                } catch (\Throwable $e2) {
                    $qaCall->update([
                        'processing_status' => 'failed',
                        'failure_reason'    => 'AI scoring failed: ' . $e2->getMessage(),
                    ]);
                    return response()->json([
                        'success'    => false,
                        'status'     => 'scoring_failed',
                        'message'    => 'AI scoring failed: ' . $e2->getMessage(),
                        'qa_call_id' => $qaCall->id,
                    ], 500);
                }
            }

            // Persist scoring result
            $resultService = new QAResultService();
            $resultService->saveResult($qaCall, $aiResult);

            // Delete audio files from disk — no longer needed after scoring
            $extraAudioPaths = array_column($qaCall->extra_parts ?? [], 'audio_file_path');
            \Illuminate\Support\Facades\Storage::disk('local')->delete(
                array_filter(array_merge(
                    [$qaCall->audio_file_path, $qaCall->audio_file_path_2],
                    $extraAudioPaths,
                ))
            );

            Log::info('[QA:Upload] Scoring complete', [
                'qa_call_id'  => $qaCall->id,
                'disposition' => $aiResult['disposition'] ?? 'unknown',
                'score_disposition' => $aiResult['score_disposition'] ?? null,
                'total_score' => $aiResult['total_score'] ?? 0,
                'scored_by'   => $qaCall->scored_by,
            ]);

            return $this->buildScoredResponse($qaCall->fresh(['qaResult']));

        } catch (\Throwable $e) {
            Log::error('[QA:Upload] Status poll error', [
                'qa_call_id' => $qaCall->id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success'    => false,
                'status'     => 'error',
                'message'    => $e->getMessage(),
                'qa_call_id' => $qaCall->id,
            ], 500);
        }
    }

    /**
     * Build the standardised scored response payload.
     */
    private function buildScoredResponse(QaCall $qaCall): JsonResponse
    {
        $result = $qaCall->qaResult;

        $compChecks = [
            'C1_agent_identity'             => $this->mapComplianceValue($result?->c2_agent_identity),
            'C2_carrier_named'              => $this->mapComplianceValue($result?->c3_carrier_named),
            'C3_product_type_stated'        => $this->mapComplianceValue($result?->c4_product_type_stated),
            'C4_health_questions_complete'  => $this->mapComplianceValue($result?->c5_health_questions_complete),
            'C5_quote_and_coverage'         => $this->mapComplianceValue($result?->c6_proper_quote),
            'C6_draft_date_confirmed'       => $this->mapComplianceValue($result?->c8_draft_date_confirmed),
            'C7_recorded_consent'           => $this->mapComplianceValue($result?->c9_end_of_call_consent),
            'C8_application_info_collected' => $this->mapComplianceValue($result?->c11_application_info_collected),
            'C9_customer_not_on_dnc'        => $this->mapComplianceValue($result?->c12_customer_not_on_dnc),
            'C10_agent_handles_objections'  => $this->mapComplianceValue($result?->c14_customer_not_disinterested),
            'C11_appropriate_language'      => $this->mapComplianceValue($result?->c16_appropriate_language),
        ];

        return response()->json([
            'success'    => true,
            'status'     => 'completed',
            'qa_call_id' => $qaCall->id,
            'result'     => $result ? [
                'disposition'         => $result->disposition,
                'score_disposition'   => $result->score_disposition,
                'total_score'         => $result->total_score,
                'compliance_pass'     => $result->compliance_pass,
                'compliance_checks'   => $compChecks,
                'compliance_failures' => $result->compliance_failures,
                'compliance_details'  => $result->raw_ai_response['compliance_details'] ?? [],
                'void_risk_reason'    => $result->void_risk_reason,
                'informational_notes' => $result->raw_ai_response['informational_notes'] ?? [],
                'score_breakdown'     => [
                    'opening'            => (int) ($result->score_opening ?? 0),
                    'discovery'          => (int) ($result->score_discovery ?? 0),
                    'presentation'       => (int) ($result->score_presentation ?? 0),
                    'objection_handling' => (int) ($result->score_objection_handling ?? 0),
                    'closing'            => (int) ($result->score_closing ?? 0),
                    'soft_skills'        => (int) ($result->score_soft_skills ?? 0),
                    'call_control'       => (int) ($result->score_call_control ?? 0),
                ],
                'coaching_notes'  => $result->coaching_notes,
                'top_issue'       => $result->top_issue,
                'strengths'       => $result->strengths,
                'improvements'    => $result->improvements,
                'customer_name'   => $qaCall->lead?->cn_name ?? $result->customer_name,
                'carrier_name'    => $result->carrier_name,
                'is_sale'         => (bool) $result->is_sale,
                'monthly_premium' => $result->monthly_premium,
                'sale_amount'     => $result->sale_amount,
            ] : null,
            'transcript' => $qaCall->transcript_diarized,
        ]);
    }

    // ── GET /qa/api/closer-sales ─────────────────────────────────────

    /**
     * Return sales (leads) made by a specific closer around a given date.
     * Query params: agent_user_id, date (YYYY-MM-DD)
     */
    public function closerSales(Request $request): JsonResponse
    {
        $agentId = $request->input('agent_user_id');
        $date    = $request->input('date');

        if (! $agentId) {
            return response()->json(['success' => false, 'message' => 'agent_user_id is required.'], 422);
        }

        $query = Lead::where('closer_id', $agentId)
            ->whereNotNull('sale_date');

        if ($date) {
            $query->whereDate('sale_date', $date);
        }

        $leads = $query->orderByDesc('sale_date')
            ->limit(50)
            ->get([
                'id', 'cn_name', 'phone_number', 'sale_date',
                'coverage_amount', 'monthly_premium', 'carrier_name',
                'issuance_status', 'qa_status', 'state',
            ]);

        return response()->json([
            'success' => true,
            'sales'   => $leads->map(fn ($l) => [
                'id'              => $l->id,
                'cn_name'         => $l->cn_name ?? 'N/A',
                'phone'           => $l->phone_number ? substr($l->phone_number, -4) : '',
                'sale_date'       => $l->sale_date?->format('M j, Y'),
                'coverage'        => $l->coverage_amount ? '$' . number_format($l->coverage_amount, 0) : '—',
                'premium'         => $l->monthly_premium ? '$' . number_format($l->monthly_premium, 2) . '/mo' : '—',
                'carrier'         => $l->carrier_name ?? '—',
                'issuance_status' => $l->issuance_status ?? '—',
                'qa_status'       => $l->qa_status,
                'state'           => $l->state ?? '',
            ]),
        ]);
    }

    // ── POST /qa/api/calls/{id}/link-sale ────────────────────────────

    /**
     * Link a QA call to a specific lead (sale) and auto-set QA status on the lead.
     */
    public function linkSale(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'lead_id' => 'required|integer|exists:leads,id',
        ]);

        $qaCall = QaCall::with('qaResult')->findOrFail($id);
        $lead   = Lead::findOrFail($request->input('lead_id'));

        // Link
        $qaCall->lead_id = $lead->id;
        $qaCall->save();

        // Auto-determine QA status from score
        $result = $qaCall->qaResult;
        if ($result) {
            $score = (int) $result->total_score;

            if ($score >= 75) {
                $qaStatus = Statuses::QA_GOOD;
            } elseif ($score >= 50) {
                $qaStatus = Statuses::QA_AVG;
            } else {
                $qaStatus = Statuses::QA_BAD;
            }

            // Build a concise reason from the AI result
            $reasons = [];
            if ($result->disposition) {
                $reasons[] = 'Disposition: ' . str_replace('_', ' ', $result->disposition);
            }
            $reasons[] = "Score: {$score}/100";
            if (! $result->compliance_pass) {
                $failures = $result->compliance_failures ?? [];
                $reasons[] = 'Compliance FAIL (' . count($failures) . ' issue' . (count($failures) !== 1 ? 's' : '') . ')';
            } else {
                $reasons[] = 'Compliance PASS';
            }
            if ($result->top_issue) {
                $reasons[] = 'Top issue: ' . $result->top_issue;
            }

            $lead->qa_status      = $qaStatus;
            $lead->qa_reason      = implode(' · ', $reasons);
            $lead->qa_reviewed_at = now();
            $lead->qa_user_id     = auth()->id();
            $lead->save();
        }

        return response()->json([
            'success'   => true,
            'qa_status' => $lead->qa_status,
            'qa_reason' => $lead->qa_reason,
            'lead_id'   => $lead->id,
            'cn_name'   => $lead->cn_name,
        ]);
    }
}

