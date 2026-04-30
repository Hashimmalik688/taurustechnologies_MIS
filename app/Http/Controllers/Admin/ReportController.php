<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadLead;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Services\CommissionCalculationService;
use App\Models\LeadDial;
use App\Models\Partner;
use App\Models\User;
use App\Support\CarrierAliases;
use App\Support\Roles;
use App\Support\Statuses;
use App\Support\Teams;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Reports hub landing page.
     */
    public function hub()
    {
        return view('admin.reports.hub');
    }

    /**
     * Standalone per-closer performance page.
     */
    /**
     * Zoom logs page with call history from Zoom Phone webhooks.
     * This shows ALL calls captured from Zoom webhooks, not just MIS-tracked calls.
     */
    public function zoomLogs(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-zoom-logs'), 403, 'Access denied.');
        // All call times are displayed in Pacific Time (America/Los_Angeles)
        $displayTz = 'America/Los_Angeles';

        // Internal Zoom system events that carry no human caller data — hidden by default
        $systemEvents = [
            'phone.recording_started',
            'phone.recording_completed',
            'phone.recording_completed_for_access_member',
            'phone.caller_call_element_completed',
            'phone.callee_call_element_completed',
            'test',
        ];

        // ── One-row-per-call strategy ─────────────────────────────────────────
        // Zoom shows 1 row per call. We achieve this by preferring:
        //   1. phone.api_call_log     — from admin sync, guaranteed 1 per call
        //   2. phone.caller_call_log_completed / caller_call_history_completed
        //      for real-time calls not yet picked up by the 5-min sync
        //   3. Any other terminal events (voicemail, callee_missed/rejected)
        // When a user explicitly picks an event_type filter we skip deduplication.
        $terminalEvents = [
            'phone.api_call_log',
            'phone.caller_call_log_completed',
            'phone.caller_call_history_completed',
            'phone.callee_call_log_completed',
            'phone.callee_call_history_completed',
            'phone.callee_missed',
            'phone.callee_rejected',
            'phone.voicemail_received',
        ];

        $query = \App\Models\ZoomWebhookLog::query()
            ->with(['lead:id,cn_name,phone_number,state', 'agent:id,name,email'])
            ->orderBy('call_start_time', 'desc')
            ->orderBy('created_at', 'desc');

        // Default: one-row-per-call (matches Zoom's 1-per-call count)
        // Strategy (cascading priority):
        //   1st choice: phone.api_call_log  — admin sync, guaranteed 1 per call
        //   2nd choice: caller_call_history_completed — for live calls not yet in admin sync
        //   3rd choice: voicemail / missed / rejected for calls with none of the above
        // When a user explicitly picks an event_type filter we show raw events instead.
        if (! $request->filled('event_type')) {
            $query->where(function ($q) {
                // Always include admin sync rows (one per call, definitive)
                $q->where('event_type', 'phone.api_call_log')

                  // Include caller_call_history_completed ONLY for calls with no api_call_log
                  ->orWhere(function ($q2) {
                      $q2->where('event_type', 'phone.caller_call_history_completed')
                         ->whereNotNull('zoom_call_id')
                         ->whereNotIn('zoom_call_id', function ($sub) {
                             $sub->select('zoom_call_id')
                                 ->from('zoom_webhook_logs')
                                 ->where('event_type', 'phone.api_call_log')
                                 ->whereNotNull('zoom_call_id');
                         });
                  })

                  // Include voicemail/missed/rejected ONLY for calls with neither of the above
                  ->orWhere(function ($q2) {
                      $q2->whereIn('event_type', ['phone.voicemail_received', 'phone.callee_missed', 'phone.callee_rejected'])
                         ->whereNotNull('zoom_call_id')
                         ->whereNotIn('zoom_call_id', function ($sub) {
                             $sub->select('zoom_call_id')
                                 ->from('zoom_webhook_logs')
                                 ->whereIn('event_type', ['phone.api_call_log', 'phone.caller_call_history_completed'])
                                 ->whereNotNull('zoom_call_id');
                         });
                  });
            });
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('caller_number', 'like', "%{$search}%")
                  ->orWhere('callee_number', 'like', "%{$search}%")
                  ->orWhere('caller_name', 'like', "%{$search}%")
                  ->orWhere('callee_name', 'like', "%{$search}%")
                  ->orWhere('caller_email', 'like', "%{$search}%")
                  ->orWhere('callee_email', 'like', "%{$search}%")
                  ->orWhere('zoom_call_id', 'like', "%{$search}%");
            });
        }

        // Agent filter — filter by extension (unique per Zoom Phone user).
        // We don't require CRM user IDs here; call logs are tracked independently.
        if ($request->filled('agent_filter')) {
            $agentVal = $request->agent_filter;
            $query->where(function($q) use ($agentVal) {
                $q->where('caller_extension', $agentVal)
                  ->orWhere('callee_extension', $agentVal);
            });
        }

        // Call Result filter — maps Zoom-style normalized keys to all raw DB variants
        if ($request->filled('call_result')) {
            $resultGroups = [
                'connected'  => ['connected', 'Call connected', 'answered', 'Recorded', 'Auto Recorded'],
                'call_failed'=> ['call_failed', 'Call failed', 'Call Failed'],
                'no_answer'  => ['No Answer', 'no_answer'],
                'cancelled'  => ['Call Cancel', 'canceled', 'Cancelled'],
                'busy'       => ['Busy'],
                'declined'   => ['Rejected', 'rejected'],
                'abandoned'  => ['abandoned', 'Abandoned'],
                'voicemail'  => ['voicemail', 'Voicemail'],
            ];
            $key = $request->call_result;
            if (isset($resultGroups[$key])) {
                $query->whereIn('call_result', $resultGroups[$key]);
            } else {
                $query->where('call_result', $key);
            }
        }

        if ($request->filled('call_type')) {
            $typeSearch = strtolower($request->call_type);
            $query->where(function($q) use ($typeSearch) {
                $q->whereRaw('LOWER(call_type) = ?', [$typeSearch]);
            });
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('has_recording')) {
            if ($request->has_recording === 'yes') {
                $query->whereNotNull('recording_url');
            } else {
                $query->whereNull('recording_url');
            }
        }

        // Date filters: user picks dates in Pacific Time (PT), but DB stores UTC.
        // Convert PT date boundaries → UTC for correct querying.
        if ($request->filled('date_from')) {
            $fromUtc = \Carbon\Carbon::parse($request->date_from, $displayTz)->startOfDay()->utc();
            $query->where('call_start_time', '>=', $fromUtc);
        }

        if ($request->filled('date_to')) {
            $toUtc = \Carbon\Carbon::parse($request->date_to, $displayTz)->endOfDay()->utc();
            $query->where('call_start_time', '<=', $toUtc);
        }

        // Clone query BEFORE pagination for aggregate stats
        $statsQuery = clone $query;
        $hasDateFilter = $request->filled('date_from') || $request->filled('date_to');
        $hasAnyFilter = $request->hasAny(['search', 'agent_filter', 'call_result', 'call_type', 'event_type', 'has_recording', 'date_from', 'date_to']);

        // Paginate results
        $callLogs = $query->paginate(50)->appends($request->except('page'));

        // Calculate statistics from the FILTERED query (all KPIs reflect current filters including date)
        $connectedResults = ['connected', 'Call connected'];
        $answeredResults = ['connected', 'Recorded', 'Auto Recorded', 'answered', 'Call connected'];
        $declinedResults = ['Rejected', 'Busy'];
        $missedResults = ['No Answer', 'no_answer', 'Call Cancel', 'canceled', 'call_failed', 'abandoned'];

        $stats = [
            'total_calls' => (clone $statsQuery)->count(),
            'total_duration' => (clone $statsQuery)->sum('duration_seconds'),
            'connected_calls' => (clone $statsQuery)->whereIn('call_result', $connectedResults)->count(),
            'answered_calls' => (clone $statsQuery)->whereIn('call_result', $answeredResults)->count(),
            'declined_calls' => (clone $statsQuery)->whereIn('call_result', $declinedResults)->count(),
            'missed_calls' => (clone $statsQuery)->whereIn('call_result', $missedResults)->count(),
            'voicemail_calls' => (clone $statsQuery)->where(function($q) {
                $q->where('event_type', 'phone.voicemail_received')
                  ->orWhere('call_result', 'Voicemail');
            })->count(),
            'auto_recorded' => (clone $statsQuery)->where('call_result', 'Auto Recorded')->count(),
            'recorded_calls' => (clone $statsQuery)->where('call_result', 'Recorded')->count(),
            'outbound_calls' => (clone $statsQuery)->whereRaw('LOWER(call_type) = ?', ['outbound'])->count(),
            'inbound_calls' => (clone $statsQuery)->whereRaw('LOWER(call_type) = ?', ['inbound'])->count(),
            'has_date_filter' => $hasDateFilter,
            'has_any_filter' => $hasAnyFilter,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        // Today's calls in Pacific Time (only meaningful without date filter)
        if (!$hasDateFilter) {
            $todayStartUtc = \Carbon\Carbon::now($displayTz)->startOfDay()->utc();
            $todayEndUtc = \Carbon\Carbon::now($displayTz)->endOfDay()->utc();
            $stats['today_calls'] = (clone $statsQuery)->whereBetween('call_start_time', [$todayStartUtc, $todayEndUtc])->count();
        }

        // Calculate personalized stats if filtering by agent
        $userStats = null;
        if ($request->filled('agent_filter')) {
            $agentVal = $request->agent_filter;
            $userQuery = clone $statsQuery; // Already filtered by agent + date

            $userStats = [
                'total_dialed' => (clone $userQuery)->where(function ($q) {
                    // Include caller-side live webhook events AND API-synced outbound logs
                    $q->whereIn('event_type', [
                        'phone.caller_connected',
                        'phone.caller_ended',
                        'phone.caller_call_log_completed',
                        'phone.caller_call_history_completed',
                    ])->orWhere(function ($q2) {
                        $q2->where('event_type', 'phone.api_call_log')
                           ->whereRaw('LOWER(call_type) = ?', ['outbound']);
                    });
                })->count(),
                
                'connected' => (clone $userQuery)->where(function($q) use ($answeredResults) {
                    $q->whereIn('call_result', $answeredResults);
                })->count(),
                
                'failed' => (clone $userQuery)->where(function($q) use ($missedResults) {
                    $q->whereIn('call_result', $missedResults);
                })->count(),
                
                'with_recording' => (clone $userQuery)->whereNotNull('recording_url')->count(),
                
                'recordings_over_5min' => (clone $userQuery)
                    ->whereNotNull('recording_url')
                    ->where('duration_seconds', '>', 300)
                    ->count(),
                
                'total_talk_time' => (clone $userQuery)
                    ->where(function ($q) {
                        $q->whereIn('event_type', ['phone.caller_call_log_completed', 'phone.callee_call_log_completed'])
                          ->orWhere('event_type', 'phone.api_call_log');
                    })
                    ->sum('duration_seconds'),
                
                'avg_call_duration' => (clone $userQuery)
                    ->where('duration_seconds', '>', 0)
                    ->where(function ($q) {
                        $q->whereIn('event_type', ['phone.caller_call_log_completed', 'phone.callee_call_log_completed'])
                          ->orWhere('event_type', 'phone.api_call_log');
                    })
                    ->avg('duration_seconds'),
            ];
        }

        // Zoom-normalized call result filter options (normalized keys → display labels)
        $callResults = collect([
            'connected'   => 'Connected',
            'call_failed' => 'Call Failed',
            'no_answer'   => 'No Answer',
            'cancelled'   => 'Cancelled',
            'busy'        => 'Busy',
            'declined'    => 'Declined',
            'abandoned'   => 'Abandoned',
            'voicemail'   => 'Voicemail',
        ]);

        $callTypes = [
            'inbound' => 'Inbound',
            'outbound' => 'Outbound',
        ];

        $eventTypes = \App\Models\ZoomWebhookLog::select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type', 'event_type');

        // Agent dropdown: built directly from webhook log caller data.
        // Uses extension number as key (unique per Zoom Phone user).
        // Intentionally NOT linked to CRM user IDs — all closers tracked here
        // even if they haven't been added to the CRM yet.
        $agentOptions = \App\Models\ZoomWebhookLog::query()
            ->whereNotNull('caller_extension')
            ->where('caller_extension', '!=', '')
            ->whereNotNull('caller_name')
            ->where('caller_name', '!=', '')
            ->select('caller_name', 'caller_email', 'caller_extension')
            ->get()
            ->groupBy('caller_extension')
            ->map(function ($entries) {
                $first = $entries->first();
                return [
                    'extension' => $first->caller_extension,
                    'name'      => $first->caller_name ?? $first->caller_email ?? ('Ext. ' . $first->caller_extension),
                    'email'     => $first->caller_email,
                ];
            })
            ->sortBy('name')
            ->values();

        // ── Per-agent KPI breakdown ───────────────────────────────────────────
        // Pull a lightweight flat list of all matching log rows (same filtered
        // query as the call-logs table, just without pagination or eager-loads)
        // then aggregate per-agent entirely in PHP.
        $answeredResults = ['connected', 'Call connected', 'answered', 'Recorded', 'Auto Recorded'];
        $missedKpi       = ['No Answer', 'no_answer', 'Call Cancel', 'canceled', 'call_failed', 'abandoned'];
        $declinedKpi     = ['Rejected', 'Busy'];
        $recordedKpi     = ['Auto Recorded', 'Recorded'];

        $allLogs = (clone $statsQuery)
            ->withoutEagerLoads()
            ->reorder()
            ->get([
                'caller_name', 'caller_extension',
                'callee_name', 'callee_extension',
                'call_type', 'call_result', 'event_type',
                'duration_seconds', 'recording_url',
            ]);

        $agentKpisArr = [];

        foreach ($allLogs as $log) {
            $direction = strtolower($log->call_type ?? '');
            // Mirror same direction detection logic used in the view
            $ev = $log->event_type ?? '';
            if (str_contains($ev, 'callee') || str_contains($ev, 'voicemail')) $direction = 'inbound';
            elseif (str_contains($ev, 'caller') || str_contains($ev, 'callout')) $direction = 'outbound';

            if ($direction === 'inbound') {
                $ext  = $log->callee_extension ?? '';
                $name = $log->callee_name ?? ('Ext. ' . $ext);
            } else {
                $ext  = $log->caller_extension ?? '';
                $name = $log->caller_name ?? ('Ext. ' . $ext);
            }

            if (!$ext) continue;

            if (!isset($agentKpisArr[$ext])) {
                $agentKpisArr[$ext] = [
                    'name'           => $name,
                    'extension'      => $ext,
                    'outbound'       => 0,
                    'inbound'        => 0,
                    'total_calls'    => 0,
                    'total_duration' => 0,
                    'answered'       => 0,
                    'missed'         => 0,
                    'declined'       => 0,
                    'recorded'       => 0,
                    'voicemail'      => 0,
                ];
            }

            $res = $log->call_result ?? '';
            $agentKpisArr[$ext]['total_calls']++;
            $agentKpisArr[$ext]['total_duration'] += (int) ($log->duration_seconds ?? 0);
            if ($direction === 'outbound') $agentKpisArr[$ext]['outbound']++;
            else                           $agentKpisArr[$ext]['inbound']++;
            if (in_array($res, $answeredResults, true)) $agentKpisArr[$ext]['answered']++;
            if (in_array($res, $missedKpi, true))       $agentKpisArr[$ext]['missed']++;
            if (in_array($res, $declinedKpi, true))     $agentKpisArr[$ext]['declined']++;
            if (in_array($res, $recordedKpi, true))     $agentKpisArr[$ext]['recorded']++;
            if ($ev === 'phone.voicemail_received' || $res === 'Voicemail') $agentKpisArr[$ext]['voicemail']++;
        }

        $agentKpis = collect($agentKpisArr)->sortByDesc('total_calls')->values();

        return view('admin.reports.zoom-logs', compact(
            'callLogs',
            'callResults',
            'callTypes',
            'eventTypes',
            'agentOptions',
            'stats',
            'userStats',
            'agentKpis',
            'displayTz'
        ));
    }

    /**
     * Zoom Agent Performance — per-agent KPI breakdown.
     * Uses the SAME base query / dedup strategy as the Call Logs page so both tabs
     * always draw from identical underlying data.
     */
    public function zoomAgentPerformance(Request $request)
    {
        $displayTz = 'America/Los_Angeles';
        $dateFrom  = $request->filled('date_from') ? $request->date_from : null;
        $dateTo    = $request->filled('date_to')   ? $request->date_to   : null;
        $todayPt   = \Carbon\Carbon::now($displayTz)->toDateString();

        ['agentKpis' => $agentKpis, 'summaryTotalCalls' => $summaryTotalCalls,
         'summaryTotalDuration' => $summaryTotalDuration, 'summaryAnswered' => $summaryAnswered]
            = $this->buildAgentPerformanceData($dateFrom, $dateTo, $displayTz);

        return view('admin.reports.zoom-agent-performance', compact(
            'agentKpis', 'summaryTotalCalls', 'summaryTotalDuration', 'summaryAnswered',
            'dateFrom', 'dateTo', 'todayPt', 'displayTz'
        ));
    }

    /**
     * JSON endpoint for live polling — returns the same agent KPI data.
     */
    public function zoomAgentPerformanceData(Request $request)
    {
        $displayTz = 'America/Los_Angeles';
        $dateFrom  = $request->filled('date_from') ? $request->date_from : null;
        $dateTo    = $request->filled('date_to')   ? $request->date_to   : null;

        $data = $this->buildAgentPerformanceData($dateFrom, $dateTo, $displayTz);

        return response()->json([
            'agents'        => $data['agentKpis']->values(),
            'total_calls'   => $data['summaryTotalCalls'],
            'total_duration'=> $data['summaryTotalDuration'],
            'answered'      => $data['summaryAnswered'],
            'generated_at'  => now()->toISOString(),
        ]);
    }

    /**
     * Shared query logic for agent performance — used by both HTML and JSON endpoints.
     */
    private function buildAgentPerformanceData(?string $dateFrom, ?string $dateTo, string $displayTz): array
    {
        // One-row-per-call dedup priority:
        //   1st: phone.api_call_log              — admin API sync, has duration ✓
        //   2nd: phone.caller_call_log_completed — live webhook, has duration ✓
        //   3rd: phone.voicemail_received        — inbound voicemails (counted separately by callee)
        // NOTE: caller_call_log_completed and api_call_log both use UUID zoom_call_ids
        //       so the NOT IN dedup works correctly between them.
        $query = \App\Models\ZoomWebhookLog::query()
            ->where(function ($q) {
                $q->where('event_type', 'phone.api_call_log')
                  ->orWhere(function ($q2) {
                      // caller_call_log_completed has real duration_seconds; exclude calls already in api_call_log.
                      // Cap at 7200s (2h): Zoom occasionally fires batch/session-summary events with impossibly
                      // large durations (e.g. 75000s). Legitimate calls are always well under 2 hours.
                      $q2->where('event_type', 'phone.caller_call_log_completed')
                         ->where(function ($qd) {
                             $qd->whereNull('duration_seconds')
                                ->orWhere('duration_seconds', '<=', 7200);
                         })
                         ->whereNotNull('zoom_call_id')
                         ->whereNotIn('zoom_call_id', function ($sub) {
                             $sub->select('zoom_call_id')->from('zoom_webhook_logs')
                                 ->where('event_type', 'phone.api_call_log')
                                 ->whereNotNull('zoom_call_id');
                         });
                  })
                  ->orWhere(function ($q2) {
                      // Voicemail events — attributed to callee (agent who received the voicemail)
                      $q2->where('event_type', 'phone.voicemail_received');
                  });
            });

        if ($dateFrom) {
            $query->where('call_start_time', '>=',
                \Carbon\Carbon::parse($dateFrom, $displayTz)->startOfDay()->utc());
        }
        if ($dateTo) {
            $query->where('call_start_time', '<=',
                \Carbon\Carbon::parse($dateTo, $displayTz)->endOfDay()->utc());
        }

        $answeredResults = ['connected', 'Call connected', 'answered', 'Recorded', 'Auto Recorded'];
        $missedKpi       = ['No Answer', 'no_answer', 'Call Cancel', 'canceled', 'call_failed', 'abandoned'];
        $declinedKpi     = ['Rejected', 'Busy'];
        $recordedKpi     = ['Auto Recorded', 'Recorded'];
        // Minimum talk-time to count as a real human connection.
        // 'Call connected' at 1-4 s = voicemail system picked up; ≥10 s = human.
        $connectedMinDuration = 10;

        $allLogs = $query->orderBy('call_start_time', 'desc')->get([
            'caller_name', 'caller_extension',
            'callee_name', 'callee_extension',
            'call_result', 'call_type', 'event_type',
            'duration_seconds', 'raw_payload', 'mos', 'call_session_id',
        ]);

        // Name alias map — maps any Zoom display name to the canonical agent name.
        // Add entries here whenever a closer's number shows up under a different name in Zoom.
        $nameAliases = [
            'Phil Anderson'      => 'Abdullah Ayub',
            '37524 Abdullah Ayub'=> 'Abdullah Ayub',
            // e.g. 'Some Wrong Name' => 'Correct Agent Name',
        ];

        // Normalize a Zoom caller_name: strip leading digit-number prefixes (e.g. "37524 "),
        // then apply the alias map. Each closer may have 3+ numbers under the same name.
        $normalizeName = function (?string $name) use ($nameAliases): ?string {
            if ($name === null) return null;
            // Apply alias map first (exact match before stripping)
            if (isset($nameAliases[$name])) return $nameAliases[$name];
            // Strip leading numeric prefix: "37524 Abdullah Ayub" → "Abdullah Ayub"
            $stripped = preg_replace('/^\d+\s+/', '', $name);
            // Apply alias again after stripping (catches "37524 Phil Anderson" style)
            return $nameAliases[$stripped] ?? $stripped;
        };

        $agentKpisArr = [];

        $ensureAgent = function (string $key, string $name, string $ext) use (&$agentKpisArr): void {
            if (!isset($agentKpisArr[$key])) {
                $agentKpisArr[$key] = [
                    'name'           => $name,
                    'extension'      => $ext,
                    '_extensions'    => $ext ? [$ext] : [],
                    'total_calls'    => 0,
                    'total_duration' => 0,
                    'answered'       => 0,
                    'missed'         => 0,
                    'declined'       => 0,
                    'recorded'       => 0,
                    'voicemail'      => 0,
                    'mos_sum'        => 0.0,
                    'mos_count'      => 0,
                    '_mosHexIds'     => [],
                ];
            } elseif ($ext && strlen($ext) <= 6 && !in_array($ext, $agentKpisArr[$key]['_extensions'], true)) {
                $agentKpisArr[$key]['_extensions'][] = $ext;
                $agentKpisArr[$key]['extension'] = implode(', ', $agentKpisArr[$key]['_extensions']);
            }
        };

        foreach ($allLogs as $log) {
            $ev = $log->event_type ?? '';

            // ── Voicemail events: inbound calls where the agent received a voicemail ──
            // Attributed to the CALLEE (the agent who got the voicemail), not the caller.
            if ($ev === 'phone.voicemail_received') {
                // callee_name may be NULL in DB for older records — fall back to raw_payload
                $calleeName = $log->callee_name;
                if (!$calleeName && $log->raw_payload) {
                    $pl = is_array($log->raw_payload) ? $log->raw_payload : json_decode($log->raw_payload, true);
                    $calleeName = $pl['payload']['object']['callee_name'] ?? null;
                }
                if (!$calleeName) continue;
                $name = $normalizeName($calleeName);
                if (!$name) continue;
                $ext  = (string) ($log->callee_extension ?? '');
                $ensureAgent($name, $name, $ext);
                $agentKpisArr[$name]['voicemail']++;
                continue;
            }

            // ── Outbound call events ──
            $direction = strtolower($log->call_type ?? '');
            if (str_contains($ev, 'callee')) $direction = 'inbound';
            elseif (str_contains($ev, 'caller') || str_contains($ev, 'callout')) $direction = 'outbound';
            if ($direction !== 'outbound') continue;

            $ext     = (string) ($log->caller_extension ?? '');
            $rawName = $log->caller_name ?? null;
            $name    = $normalizeName($rawName) ?? ($ext ? 'Ext. ' . $ext : null);
            if (!$name) continue;

            $ensureAgent($name, $name, $ext);

            $res      = $log->call_result ?? '';
            $dur      = (int) ($log->duration_seconds ?? 0);
            $isConnResult = in_array($res, $answeredResults, true);
            // A call counts as "connected" only when it had a real connection result AND
            // lasted at least $connectedMinDuration seconds — filters voicemail-system pickups.
            $isConnected  = $isConnResult && $dur >= $connectedMinDuration;
            // A call was a "no-pickup" if it explicitly wasn't answered OR it connected
            // for only a few seconds (voicemail / immediate hang-up).
            $isNoPickup   = in_array($res, $missedKpi, true)
                         || ($isConnResult && $dur < $connectedMinDuration);

            $agentKpisArr[$name]['total_calls']++;
            $agentKpisArr[$name]['total_duration'] += $dur;
            if ($isConnected)                           $agentKpisArr[$name]['answered']++;
            if ($isNoPickup)                            $agentKpisArr[$name]['missed']++;
            if (in_array($res, $declinedKpi, true))     $agentKpisArr[$name]['declined']++;
            if (in_array($res, $recordedKpi, true) && $isConnected) $agentKpisArr[$name]['recorded']++;

            // MOS: use the value on this record if present; otherwise queue a raw_payload lookup.
            $logMos = isset($log->mos) && $log->mos > 0 ? (float) $log->mos : null;
            if ($logMos) {
                $agentKpisArr[$name]['mos_sum']   += $logMos;
                $agentKpisArr[$name]['mos_count'] += 1;
                $agentKpisArr[$name]['_mosHexIds'] = $agentKpisArr[$name]['_mosHexIds'] ?? [];
            } else {
                // caller_call_log_completed stores the log UUID as zoom_call_id;
                // the real hex session call_id lives in call_logs[0].call_id in raw_payload.
                $agentKpisArr[$name]['_mosHexIds'] = $agentKpisArr[$name]['_mosHexIds'] ?? [];
                $hexId = null;
                if ($log->call_session_id) {
                    $hexId = $log->call_session_id;
                } elseif ($log->raw_payload && $ev === 'phone.caller_call_log_completed') {
                    $pl = is_array($log->raw_payload) ? $log->raw_payload : json_decode($log->raw_payload, true);
                    $hexId = $pl['payload']['object']['call_logs'][0]['call_id'] ?? null;
                }
                if ($hexId) {
                    $agentKpisArr[$name]['_mosHexIds'][$hexId] = true;
                }
            }
        }

        // ── MOS second-pass: batch-fetch for records that couldn't self-resolve ──
        $allHexIds = [];
        foreach ($agentKpisArr as &$kpi) {
            if (!empty($kpi['_mosHexIds'])) {
                foreach (array_keys($kpi['_mosHexIds']) as $hid) {
                    $allHexIds[$hid] = true;
                }
            }
        }
        unset($kpi);
        if (!empty($allHexIds)) {
            $hexMosMap = \App\Models\ZoomWebhookLog::whereIn('zoom_call_id', array_keys($allHexIds))
                ->where('mos', '>', 0)
                ->whereNotNull('mos')
                ->pluck('mos', 'zoom_call_id');
            foreach ($agentKpisArr as $aKey => &$kpi) {
                foreach (array_keys($kpi['_mosHexIds'] ?? []) as $hid) {
                    if (isset($hexMosMap[$hid]) && $hexMosMap[$hid] > 0) {
                        $kpi['mos_sum']   += (float) $hexMosMap[$hid];
                        $kpi['mos_count'] += 1;
                    }
                }
            }
            unset($kpi);
        }

        // Strip internal tracking keys and compute derived fields
        $agentKpis = collect($agentKpisArr)
            ->map(function ($a) {
                $avgMos = $a['mos_count'] > 0 ? round($a['mos_sum'] / $a['mos_count'], 1) : null;
                return array_diff_key($a, ['_extensions' => true, 'mos_sum' => true, 'mos_count' => true, '_mosHexIds' => true])
                    + ['avg_mos' => $avgMos];
            })
            ->sortByDesc('total_calls')
            ->values();

        return [
            'agentKpis'          => $agentKpis,
            'summaryTotalCalls'   => $agentKpis->sum('total_calls'),
            'summaryTotalDuration'=> $agentKpis->sum('total_duration'),
            'summaryAnswered'     => $agentKpis->sum('answered'),
        ];
    }

    /**
     * Zoom webhook diagnostics - shows what events are captured vs missing
     */
    public function zoomDiagnostics()
    {
        // Get current event counts
        $current_events = \App\Models\ZoomWebhookLog::select('event_type')
            ->selectRaw('COUNT(*) as count')
            ->whereDate('call_start_time', '>=', today()->subDays(7))
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->pluck('count', 'event_type')
            ->toArray();

        // Calculate stats
        $crm_total = array_sum($current_events);
        $unique_calls = \App\Models\ZoomWebhookLog::distinct('zoom_call_id')
            ->whereNotNull('zoom_call_id')
            ->whereDate('call_start_time', '>=', today()->subDays(7))
            ->count('zoom_call_id');

        // Estimate missing events
        // Each call typically generates 2-3 events if all webhooks enabled
        // We're seeing ~2 events per call, but should see closer to 3-4
        $estimated_zoom_total = ceil($crm_total * 1.5); // Conservative estimate
        $coverage_percent = $crm_total > 0 ? round(($crm_total / $estimated_zoom_total) * 100) : 0;

        $stats = [
            'crm_events' => $crm_total,
            'unique_calls' => $unique_calls,
            'estimated_zoom' => $estimated_zoom_total,
            'coverage_percent' => $coverage_percent,
        ];

        return view('admin.reports.zoom-diagnostics', compact('current_events', 'stats'));
    }

    /**
     * Resolve the settlement type key from a lead's policy_type / settlement_type.
     * Maps "G.I", "Graded", "Level", "Modified" etc. → 'gi', 'graded', 'level', 'modified'
     */
    private function resolveSettlementKey($lead): string
    {
        $raw = strtolower(trim($lead->settlement_type ?: $lead->policy_type ?: ''));
        if (str_contains($raw, 'g.i') || str_contains($raw, 'gi')) return 'gi';
        if (str_contains($raw, 'grad')) return 'graded';
        if (str_contains($raw, 'modif')) return 'modified';
        return 'level'; // default
    }

    /**
     * Calculate revenue for one lead: premium × 9 × commission%
     * Looks up commission% from the cluster page (AgentCarrierState) by partner + carrier + settlement type.
     * Falls back to premium × 9 if no rate is configured.
     */
    private function calcLeadRevenue($lead): float
    {
        $premium = (float) ($lead->monthly_premium ?? 0);
        if ($premium <= 0) return 0.0;

        // Cannot calculate without both partner and carrier
        if (empty($lead->partner_id) || empty($lead->insurance_carrier_id)) return 0.0;

        $settlementType = $this->resolveSettlementKey($lead);

        $result = app(CommissionCalculationService::class)->calculateCommission(
            (int) $lead->partner_id,
            (int) $lead->insurance_carrier_id,
            $lead->state ?? '',
            $settlementType,
            $premium
        );

        return round($result['commission'] ?? 0, 2);
    }

    public function submissionPerformance(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-submission-performance'), 403, 'Access denied.');
        // Default to current month
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->endOfMonth()->toDateString());

        $query = Lead::whereNotNull('pending_contract_at')
            ->whereNotNull('closer_name');

        // Filter by pending_contract_at (submission date) not sale_date,
        // so the report accurately shows what was submitted in the selected period.
        if ($dateFrom) $query->whereDate('pending_contract_at', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('pending_contract_at', '<=', $dateTo);

        // Load all leads and calculate revenue from cluster rates (premium × 9 × commission%)
        $leads = $query->select(
            'insurance_carrier_id', 'carrier_name', 'partner_id', 'assigned_partner',
            'monthly_premium', 'settlement_type', 'policy_type', 'state'
        )->get();

        // Group by carrier name + assigned_partner text so rows always merge by display value,
        // regardless of whether the partner_id FK is populated on each lead.
        $carriersData = $leads
            ->groupBy(fn ($l) => trim(strtolower($l->carrier_name ?? '')) . '||' . trim(strtolower($l->assigned_partner ?? '')))
            ->map(function ($group) {
                $first        = $group->first();
                $totalRevenue = 0;
                foreach ($group as $lead) {
                    $totalRevenue += $this->calcLeadRevenue($lead);
                }
                return (object) [
                    'insurance_carrier_id' => $first->insurance_carrier_id,
                    'carrier_name'         => $first->carrier_name,
                    'assigned_partner'     => $first->assigned_partner,
                    'total_sales'          => $group->count(),
                    'total_premium'        => $group->sum('monthly_premium'),
                    'total_revenue'        => round($totalRevenue, 2),
                ];
            })
            ->values()
            ->sortByDesc('total_sales');

        $grandTotalSales   = $carriersData->sum('total_sales');
        $grandTotalPremium = $carriersData->sum('total_premium');
        $grandTotalRevenue = $carriersData->sum('total_revenue');

        return view('admin.reports.submission-performance', compact(
            'carriersData',
            'grandTotalSales',
            'grandTotalPremium',
            'grandTotalRevenue',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Drilldown: individual sales for a specific carrier+partner combination.
     */
    public function submissionPerformanceDrilldown(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-submission-performance'), 403, 'Access denied.');
        $dateFrom  = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo    = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $carrierName    = $request->get('carrier_name');
        $assignedPartner = $request->get('assigned_partner');

        $query = Lead::whereNotNull('pending_contract_at')
            ->whereNotNull('closer_name');

        // Filter by pending_contract_at (submission date) — consistent with the main report.
        if ($dateFrom)        $query->whereDate('pending_contract_at', '>=', $dateFrom);
        if ($dateTo)          $query->whereDate('pending_contract_at', '<=', $dateTo);
        if ($carrierName)     $query->where('carrier_name', $carrierName);
        if ($assignedPartner !== null) $query->where('assigned_partner', $assignedPartner ?: null);

        $rawLeads = $query->select(
                'id', 'cn_name', 'carrier_name', 'assigned_partner',
                'insurance_carrier_id', 'partner_id',
                'monthly_premium', 'agent_commission', 'agent_revenue', 'settlement_percentage',
                'policy_type', 'settlement_type', 'state', 'sale_date',
                'pending_contract_at', 'closer_name', 'issuance_status',
                'policy_number',
                'commission_calculation_notes', 'commission_calculated_at'
            )
            ->orderByDesc('pending_contract_at')
            ->get();

        // Attach calculated revenue to each lead for display
        $leads = $rawLeads->map(function ($lead) {
            $lead->eff_revenue = $this->calcLeadRevenue($lead);
            $lead->eff_rate    = null;
            return $lead;
        });

        $totalSales   = $leads->count();
        $totalPremium = $leads->sum('monthly_premium');
        $totalRevenue = $leads->sum('eff_revenue');
        $hasRevCount  = $leads->where('eff_revenue', '>', 0)->count();
        $noRevCount   = $totalSales - $hasRevCount;

        $carrierLabel = $leads->first()?->carrier_name ?? 'Unknown Carrier';
        $partnerLabel = $leads->first()?->assigned_partner ?? null;

        return view('admin.reports.submission-performance-drilldown', compact(
            'leads', 'totalSales', 'totalPremium', 'totalRevenue',
            'hasRevCount', 'noRevCount',
            'carrierLabel', 'partnerLabel',
            'dateFrom', 'dateTo',
            'carrierName', 'assignedPartner'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     * SALES STATUS REPORT
     * Pivot table: one row per carrier, columns = 8 pipeline stages
     * ────────────────────────────────────────────────────────────── */
    public function salesStatus(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-sales-status'), 403, 'Access denied.');
        $dateFrom  = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo    = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $dateField = $request->get('date_field', 'sale_date'); // 'sale_date' | 'paid_at'
        $carrierId = $request->get('carrier_id'); // optional single carrier filter

        // Allowed date fields (whitelist)
        if (!in_array($dateField, ['sale_date', 'paid_at'])) {
            $dateField = 'sale_date';
        }

        // Base: any lead that reached a sale
        $baseQuery = Lead::whereNotNull('sale_at');

        if ($dateFrom) $baseQuery->whereDate($dateField, '>=', $dateFrom);
        if ($dateTo)   $baseQuery->whereDate($dateField, '<=', $dateTo);
        if ($carrierId) $baseQuery->where('insurance_carrier_id', $carrierId);

        // Pull all leads with necessary columns
        $leads = $baseQuery->select(
            'id', 'insurance_carrier_id', 'carrier_name',
            'partner_id', 'assigned_partner',
            'sale_at', 'sale_date', 'paid_at',
            'pending_contract_at', 'submission_at',
            'issuance_status', 'not_issued_at',
            'not_paid_at', 'policy_died_at', 'declined_at', 'status'
        )->get();

        // Helper closures for each stage
        $stages = [
            'total_sales'       => fn($l) => true,
            'pending_contract'  => fn($l) => !is_null($l->pending_contract_at),
            'submitted'         => fn($l) => !is_null($l->submission_at),
            'issued'            => fn($l) => $l->issuance_status === 'Issued',
            'not_issued'        => fn($l) => !is_null($l->not_issued_at),
            'paid'              => fn($l) => !is_null($l->paid_at),
            'not_paid'          => fn($l) => !is_null($l->not_paid_at),
            'policy_died'       => fn($l) => !is_null($l->policy_died_at) || $l->status === 'chargeback',
            'declined'          => fn($l) => !is_null($l->declined_at),
        ];

        // Group by carrier_name + partner_id so same-named carriers stay split by partner
        $carriersData = $leads
            ->groupBy(fn($l) => strtolower(trim($l->carrier_name ?? 'unknown')) . '||' . ($l->partner_id ?? 'none'))
            ->map(function ($group) use ($stages) {
                $first  = $group->first();
                $counts = [];
                foreach ($stages as $key => $fn) {
                    $counts[$key] = $group->filter($fn)->count();
                }
                return (object) array_merge([
                    'insurance_carrier_id' => $first->insurance_carrier_id,
                    'carrier_name'         => $first->carrier_name ?: 'Unknown',
                    'partner_id'           => $first->partner_id,
                    'assigned_partner'     => $first->assigned_partner,
                ], $counts);
            })
            ->sortByDesc('total_sales')
            ->values();  // re-index AFTER sort so keys are 0,1,2...

        // Grand totals
        $grandTotals = [];
        foreach (array_keys($stages) as $key) {
            $grandTotals[$key] = $carriersData->sum($key);
        }

        // Carrier list for filter dropdown
        $allCarriers = \App\Models\InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.reports.sales-status', compact(
            'carriersData', 'grandTotals',
            'dateFrom', 'dateTo', 'dateField',
            'carrierId', 'allCarriers'
        ));
    }

    /**
     * Drilldown: individual leads for a carrier + stage combination.
     */
    public function salesStatusDrilldown(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-sales-status'), 403, 'Access denied.');
        $dateFrom    = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo      = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $dateField   = $request->get('date_field', 'sale_date');
        $carrierName = $request->get('carrier_name');
        $carrierId   = $request->get('carrier_id');   // fallback (legacy)
        $partnerId   = $request->get('partner_id');   // optional partner filter
        $stage       = $request->get('stage', 'total_sales');

        if (!in_array($dateField, ['sale_date', 'paid_at'])) {
            $dateField = 'sale_date';
        }

        $allowedStages = ['total_sales', 'pending_contract', 'submitted', 'issued', 'not_issued', 'paid', 'not_paid', 'policy_died', 'declined'];
        if (!in_array($stage, $allowedStages)) {
            $stage = 'total_sales';
        }

        $query = Lead::whereNotNull('sale_at');

        if ($dateFrom) $query->whereDate($dateField, '>=', $dateFrom);
        if ($dateTo)   $query->whereDate($dateField, '<=', $dateTo);
        // carrier filter
        if ($carrierName)    $query->whereRaw('LOWER(carrier_name) = ?', [strtolower($carrierName)]);
        elseif ($carrierId)  $query->where('insurance_carrier_id', $carrierId);
        // partner filter (null partner_id = leads with no partner)
        if ($partnerId === 'none') $query->whereNull('partner_id');
        elseif ($partnerId)        $query->where('partner_id', $partnerId);

        // Stage-specific filter
        match ($stage) {
            'pending_contract' => $query->whereNotNull('pending_contract_at'),
            'submitted'        => $query->whereNotNull('submission_at'),
            'issued'           => $query->where('issuance_status', 'Issued'),
            'not_issued'       => $query->whereNotNull('not_issued_at'),
            'paid'             => $query->whereNotNull('paid_at'),
            'not_paid'         => $query->whereNotNull('not_paid_at'),
            'policy_died'      => $query->where(fn($q) => $q->whereNotNull('policy_died_at')->orWhere('status', 'chargeback')),
            'declined'         => $query->whereNotNull('declined_at'),
            default            => null,
        };

        $leads = $query->select(
            'id', 'cn_name', 'carrier_name', 'assigned_partner',
            'insurance_carrier_id', 'partner_id',
            'monthly_premium', 'policy_number',
            'sale_date', 'paid_at', 'pending_contract_at', 'not_issued_at',
            'declined_at',
            'issuance_status', 'not_issued_disposition',
            'closer_name', 'status',
            'policy_type', 'settlement_type', 'state'
        )->orderByDesc('sale_date')->get();

        $stageLabelMap = [
            'total_sales'      => 'Total Sales',
            'pending_contract' => 'Pending Contract',
            'submitted'        => 'Submitted',
            'issued'           => 'Issued',
            'not_issued'       => 'Not Issued',
            'paid'             => 'Paid',
            'not_paid'         => 'Not Paid',
            'policy_died'      => 'Policy Died / Chargeback',
            'declined'         => 'Declined',
        ];

        $carrierLabel = $carrierName ?: ($leads->first()?->carrier_name ?? (\App\Models\InsuranceCarrier::find($carrierId)?->name ?? 'All Carriers'));
        $partnerLabel = $leads->first()?->assigned_partner ?? null;
        $stageLabel   = $stageLabelMap[$stage] ?? $stage;

        $totalSales   = $leads->count();
        $totalPremium = $leads->sum('monthly_premium');

        return view('admin.reports.sales-status-drilldown', compact(
            'leads', 'totalSales', 'totalPremium',
            'carrierLabel', 'partnerLabel', 'stageLabel', 'stage',
            'dateFrom', 'dateTo', 'dateField',
            'carrierId', 'carrierName', 'partnerId'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     * POLICY TYPE REPORT
     * Summary table: one row per policy type (G.I, Graded, Level, Modified, …)
     * ────────────────────────────────────────────────────────────── */
    public function policyTypeReport(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-policy-type'), 403, 'Access denied.');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->endOfMonth()->toDateString());

        $query = Lead::whereNotNull('sale_at')
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name');

        if ($dateFrom) $query->whereDate('sale_date', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('sale_date', '<=', $dateTo);

        $leads = $query->select(
            'id', 'policy_type', 'monthly_premium', 'settlement_type',
            'carrier_name', 'insurance_carrier_id', 'partner_id', 'assigned_partner',
            'state', 'sale_date'
        )->get();

        // Group by policy_type + carrier_name + assigned_partner
        $policyData = $leads
            ->groupBy(fn($l) =>
                $this->normalizePolicyType($l->policy_type) . '||' .
                trim(strtolower($l->carrier_name ?? '')) . '||' .
                trim(strtolower($l->assigned_partner ?? ''))
            )
            ->map(function ($group) {
                $first        = $group->first();
                $totalRevenue = 0;
                foreach ($group as $lead) {
                    $totalRevenue += $this->calcLeadRevenue($lead);
                }
                return (object) [
                    'policy_type'          => $this->normalizePolicyType($first->policy_type),
                    'carrier_name'         => $first->carrier_name,
                    'insurance_carrier_id' => $first->insurance_carrier_id,
                    'assigned_partner'     => $first->assigned_partner,
                    'partner_id'           => $first->partner_id,
                    'total_sales'          => $group->count(),
                    'total_premium'        => $group->sum('monthly_premium'),
                    'total_revenue'        => round($totalRevenue, 2),
                ];
            })
            ->sortByDesc('total_sales')
            ->values();

        $grandTotalSales   = $policyData->sum('total_sales');
        $grandTotalPremium = $policyData->sum('total_premium');
        $grandTotalRevenue = $policyData->sum('total_revenue');

        return view('admin.reports.policy-type-report', compact(
            'policyData',
            'grandTotalSales',
            'grandTotalPremium',
            'grandTotalRevenue',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Return all known raw DB values that map to a given canonical policy type.
     * If $canonical is null, returns a flat array of ALL known raw values (any type).
     */
    private function rawVariantsForPolicyType(?string $canonical = null): array
    {
        $map = [
            'Level'    => ['Level', 'level', 'level (pref)', 'level pref', 'level preferred', 'lvl',
                           'Std', 'std', 'Standard', 'standard', 'Standard Benefit', 'standard benefit',
                           'Preferred', 'preferred', 'Pref', 'pref'],
            'Graded'   => ['Graded', 'graded', 'Graded Benefit', 'graded benefit'],
            'G.I'      => ['G.I', 'G.I.', 'GI', 'gi', 'Gi', 'g.i', 'g.i.',
                           'Guaranteed Issue', 'guaranteed issue', 'Guaranteed', 'guaranteed', 'GU Issue'],
            'Modified' => ['Modified', 'modified', 'Modified Benefit', 'modified benefit', 'Mod', 'mod'],
        ];

        if ($canonical === null) {
            return array_merge(...array_values($map));
        }

        return $map[$canonical] ?? [$canonical];
    }

    /**
     * Normalize free-text policy_type values to canonical dropdown options.
     * Canonical: Level | Graded | G.I | Modified | Unknown
     */
    private function normalizePolicyType(?string $raw): string
    {
        if (empty($raw)) return 'Unknown';
        $v = strtolower(trim($raw));

        if (str_starts_with($v, 'level') || in_array($v, ['std', 'standard', 'standard benefit', 'preferred', 'pref'])) {
            return 'Level';
        }
        if (str_starts_with($v, 'graded')) {
            return 'Graded';
        }
        if (in_array($v, ['g.i', 'gi', 'g.i.', 'guaranteed issue', 'guaranteed', 'gu issue'])) {
            return 'G.I';
        }
        if (str_starts_with($v, 'modified') || str_starts_with($v, 'mod ')) {
            return 'Modified';
        }

        // already canonical
        $canonical = ['Level' => 1, 'Graded' => 1, 'G.I' => 1, 'Modified' => 1];
        if (isset($canonical[$raw])) return $raw;

        return 'Unknown';
    }

    /**
     * Drilldown: individual sales for a specific policy type.
     */
    public function policyTypeReportDrilldown(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-policy-type'), 403, 'Access denied.');
        $dateFrom        = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo          = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $policyType      = $request->get('policy_type', 'Unknown');
        $carrierName     = $request->get('carrier_name');
        $assignedPartner = $request->get('assigned_partner');

        $query = Lead::whereNotNull('sale_at')
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name');

        if ($dateFrom) $query->whereDate('sale_date', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('sale_date', '<=', $dateTo);

        if ($policyType === 'Unknown') {
            // All sales whose policy_type doesn't map to a canonical value
            $allKnown = $this->rawVariantsForPolicyType();
            $query->where(fn($q) =>
                $q->whereNull('policy_type')
                  ->orWhere('policy_type', '')
                  ->orWhereNotIn('policy_type', $allKnown)
            );
        } else {
            $variants = $this->rawVariantsForPolicyType($policyType);
            $query->whereIn('policy_type', $variants);
        }

        if ($carrierName !== null) $query->whereRaw('LOWER(carrier_name) = ?', [strtolower($carrierName)]);
        if ($assignedPartner !== null) {
            if ($assignedPartner === '') $query->where(fn($q) => $q->whereNull('assigned_partner')->orWhere('assigned_partner', ''));
            else $query->whereRaw('LOWER(assigned_partner) = ?', [strtolower($assignedPartner)]);
        }

        $rawLeads = $query->select(
            'id', 'cn_name', 'policy_type', 'carrier_name', 'assigned_partner',
            'insurance_carrier_id', 'partner_id',
            'monthly_premium', 'settlement_type', 'state', 'sale_date',
            'policy_number', 'closer_name', 'issuance_status',
            'agent_commission', 'agent_revenue', 'settlement_percentage',
            'commission_calculation_notes', 'commission_calculated_at'
        )->orderByDesc('sale_date')->get();

        $leads = $rawLeads->map(function ($lead) {
            $lead->eff_revenue = $this->calcLeadRevenue($lead);
            return $lead;
        });

        $totalSales   = $leads->count();
        $totalPremium = $leads->sum('monthly_premium');
        $totalRevenue = $leads->sum('eff_revenue');

        $carrierLabel = $carrierName ?: 'All Carriers';
        $partnerLabel = ($assignedPartner !== null && $assignedPartner !== '') ? $assignedPartner : null;

        return view('admin.reports.policy-type-report-drilldown', compact(
            'leads', 'totalSales', 'totalPremium', 'totalRevenue',
            'policyType', 'carrierLabel', 'partnerLabel',
            'dateFrom', 'dateTo',
            'carrierName', 'assignedPartner'
        ));
    }

    /**
     * Dialer Report — breakdown of disposed calls per closer.
     */
    public function dispositionReport(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-disposition'), 403, 'Access denied.');
        $timezone = 'America/Los_Angeles';
        $appTz    = config('app.timezone', 'America/Los_Angeles');

        $filter      = $request->input('filter', 'today');
        $customStart = $request->input('start_date');
        $customEnd   = $request->input('end_date');

        if ($filter === 'custom' && $customStart && $customEnd) {
            try {
                $startDate = Carbon::parse($customStart, $timezone)->startOfDay()->setTimezone($appTz);
                $endDate   = Carbon::parse($customEnd, $timezone)->endOfDay()->setTimezone($appTz);
            } catch (\Exception $e) {
                $startDate = Carbon::today($timezone)->startOfDay()->setTimezone($appTz);
                $endDate   = Carbon::today($timezone)->endOfDay()->setTimezone($appTz);
            }
        } elseif ($filter === 'week') {
            $startDate = Carbon::now($timezone)->startOfWeek()->startOfDay()->setTimezone($appTz);
            $endDate   = Carbon::now($timezone)->endOfDay()->setTimezone($appTz);
        } elseif ($filter === 'month') {
            $startDate = Carbon::now($timezone)->startOfMonth()->startOfDay()->setTimezone($appTz);
            $endDate   = Carbon::now($timezone)->endOfDay()->setTimezone($appTz);
        } else {
            $startDate = Carbon::today($timezone)->startOfDay()->setTimezone($appTz);
            $endDate   = Carbon::today($timezone)->endOfDay()->setTimezone($appTz);
        }

        $closerFilter      = $request->input('closer');
        $dispositionFilter = $request->input('disposition');
        $triggerFilter     = $request->input('trigger');

        // All disposition labels for reference
        $allDispositions = [
            'answering_machine', 'busy', 'dead_air', 'disconnected',
            'declined_sale', 'dnc', 'no_answer_ec', 'not_interested', 'no_pitch',
            'business_number', 'not_in_service',
            'callback_set', 'updated_data',
        ];

        // Base query — disposed calls only (keeps_in_calling = true)
        $base = BadLead::where('keeps_in_calling', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($closerFilter) $base->where('disposed_by', $closerFilter);
        if ($dispositionFilter) $base->where('disposition', $dispositionFilter);
        if ($triggerFilter) $base->where('trigger', $triggerFilter);

        // Overall KPIs
        $totalCalls = (clone $base)->count();
        $dispoCounts = (clone $base)->selectRaw('disposition, COUNT(*) as cnt')
            ->groupBy('disposition')->pluck('cnt', 'disposition')->toArray();

        // Per-closer breakdown
        $perCloser = (clone $base)
            ->selectRaw('disposed_by, disposition, `trigger`, COUNT(*) as cnt')
            ->groupBy('disposed_by', 'disposition', DB::raw('`trigger`'))
            ->with('disposedBy:id,name')
            ->get()
            ->groupBy('disposed_by');

        // Build per-closer summary rows
        $closerRows = $perCloser->map(function ($rows) use ($allDispositions, $startDate, $endDate) {
            $user  = $rows->first()->disposedBy;
            $name  = $user?->name ?? 'Unknown';
            $userId = $rows->first()->disposed_by;
            $total = $rows->sum('cnt');
            $byDisp = $rows->groupBy('disposition')->map(fn ($g) => $g->sum('cnt'));
            $endCallTotal  = $rows->where('trigger', 'end_call')->sum('cnt');
            $saveExitTotal = $rows->where('trigger', 'save_exit')->sum('cnt');

            // Count sales for this closer in the same date range
            $sales = 0;
            if ($userId && $user) {
                $sales = \App\Models\Lead::where(function ($q) use ($userId, $user) {
                        $q->where('managed_by', $userId)
                          ->orWhere('closer_id', $userId)
                          ->orWhere('closer_name', $user->name);
                    })
                    ->whereNotNull('sale_at')
                    ->whereBetween('sale_at', [$startDate, $endDate])
                    ->where('is_manual_sale', false)
                    ->whereExists(fn($q) => $q->from('audit_logs')
                        ->where('model', 'Lead')
                        ->where('action', 'sale_submitted')
                        ->whereColumn('model_id', 'leads.id'))
                    ->count();
            }

            return [
                'id'            => $userId,
                'name'          => $name,
                'total'         => $total,
                'end_call'      => $endCallTotal,
                'save_exit'     => $saveExitTotal,
                'sales'         => $sales,
                'dispositions'  => $byDisp,
            ];
        })->sortByDesc('total')->values();

        $totalSales = $closerRows->sum('sales');

        // Closers for filter dropdown
        $wantedRoles   = ['Ravens Closer', 'Peregrine Closer', 'Employee', 'Manager', 'Super Admin'];
        $existingRoles = \Spatie\Permission\Models\Role::whereIn('name', $wantedRoles)->pluck('name')->toArray();
        $closersList   = $existingRoles
            ? User::role($existingRoles)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('admin.reports.disposition-report', compact(
            'filter', 'customStart', 'customEnd',
            'totalCalls', 'totalSales', 'dispoCounts', 'closerRows', 'allDispositions',
            'closersList', 'closerFilter', 'dispositionFilter', 'triggerFilter',
            'startDate', 'endDate'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     * MANAGER SUBMISSION REPORT
     * Shows per-manager counts of Pending Contract and Declined actions.
     * Date range is based on the ACTION date (pending_contract_at / declined_at).
     * ────────────────────────────────────────────────────────────── */
    public function managerSubmissionReport(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-manager-submission'), 403, 'Access denied.');
        $dateFrom    = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo      = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $carrierId   = $request->get('carrier_id');
        $partnerName = $request->get('partner_name');
        $policyType  = $request->get('policy_type');

        // Leads where manager took action: approved to pending contract OR declined
        // Date range applies to the ACTION date, not sale_date
        $query = Lead::where(function ($q) use ($dateFrom, $dateTo) {
            $q->where(function ($q2) use ($dateFrom, $dateTo) {
                $q2->whereNotNull('pending_contract_at');
                if ($dateFrom) $q2->whereDate('pending_contract_at', '>=', $dateFrom);
                if ($dateTo)   $q2->whereDate('pending_contract_at', '<=', $dateTo);
            })->orWhere(function ($q2) use ($dateFrom, $dateTo) {
                $q2->whereNotNull('declined_at');
                if ($dateFrom) $q2->whereDate('declined_at', '>=', $dateFrom);
                if ($dateTo)   $q2->whereDate('declined_at', '<=', $dateTo);
            });
        })->whereNotNull('submission_by');

        if ($carrierId)   $query->where('insurance_carrier_id', $carrierId);
        if ($partnerName) $query->where('assigned_partner', $partnerName);
        if ($policyType)  $query->where('policy_type', $policyType);

        $leads = $query->select(
            'id', 'submission_by', 'pending_contract_at', 'declined_at', 'sale_date'
        )->get();

        // Group by manager
        $grouped = $leads->groupBy('submission_by');

        // Fetch manager users
        $managerIds = $grouped->keys()->filter()->toArray();
        $managers   = User::withTrashed()->whereIn('id', $managerIds)->get()->keyBy('id');

        // Helper: resolve the current (most-recent) action for a lead
        $currentAction = function ($lead) {
            if ($lead->pending_contract_at && $lead->declined_at) {
                return $lead->pending_contract_at > $lead->declined_at ? 'pending_contract' : 'declined';
            }
            return $lead->pending_contract_at ? 'pending_contract' : 'declined';
        };

        $rows = $grouped->map(function ($group, $managerId) use ($managers, $currentAction) {
            $pendingContract = $group->filter(fn($l) => $currentAction($l) === 'pending_contract')->count();
            $declined        = $group->filter(fn($l) => $currentAction($l) === 'declined')->count();
            $total           = $group->count();
            $manager         = $managers->get($managerId);

            return (object) [
                'manager_id'       => $managerId,
                'manager_name'     => $manager ? $manager->name . ($manager->trashed() ? ' (Terminated)' : '') : 'Unknown',
                'pending_contract' => $pendingContract,
                'declined'         => $declined,
                'total'            => $total,
            ];
        })->sortByDesc('total')->values();

        $grandPendingContract = $rows->sum('pending_contract');
        $grandDeclined        = $rows->sum('declined');
        $grandTotal           = $rows->sum('total');

        // Filter dropdowns
        $allCarriers = \App\Models\InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $allPartners = \App\Models\Lead::whereNotNull('assigned_partner')->where('assigned_partner', '!=', '')
            ->distinct()->orderBy('assigned_partner')->pluck('assigned_partner');
        $allPolicyTypes = ['Level', 'Graded', 'G.I', 'Modified'];

        return view('admin.reports.manager-submission-report', compact(
            'rows', 'grandPendingContract', 'grandDeclined', 'grandTotal',
            'dateFrom', 'dateTo', 'carrierId', 'partnerName', 'policyType',
            'allCarriers', 'allPartners', 'allPolicyTypes'
        ));
    }

    /**
     * Drilldown: individual leads actioned by a specific manager.
     */
    public function managerSubmissionDrilldown(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-manager-submission'), 403, 'Access denied.');
        $managerId   = $request->get('manager_id');
        $dateFrom    = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo      = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $actionFilter = $request->get('action'); // 'pending_contract' | 'declined' | null (both)
        $carrierId   = $request->get('carrier_id');
        $partnerName = $request->get('partner_name');
        $policyType  = $request->get('policy_type');

        $query = Lead::where(function ($q) use ($dateFrom, $dateTo, $actionFilter) {
            if ($actionFilter === 'pending_contract') {
                // Current action is pending_contract: has pending_contract_at in range,
                // AND it is more recent than declined_at (or there is no declined_at)
                $q->whereNotNull('pending_contract_at');
                if ($dateFrom) $q->whereDate('pending_contract_at', '>=', $dateFrom);
                if ($dateTo)   $q->whereDate('pending_contract_at', '<=', $dateTo);
                $q->where(function ($q2) {
                    $q2->whereNull('declined_at')
                       ->orWhereRaw('pending_contract_at > declined_at');
                });
            } elseif ($actionFilter === 'declined') {
                // Current action is declined: has declined_at in range,
                // AND it is more recent than pending_contract_at (or there is no pending_contract_at)
                $q->whereNotNull('declined_at');
                if ($dateFrom) $q->whereDate('declined_at', '>=', $dateFrom);
                if ($dateTo)   $q->whereDate('declined_at', '<=', $dateTo);
                $q->where(function ($q2) {
                    $q2->whereNull('pending_contract_at')
                       ->orWhereRaw('declined_at > pending_contract_at');
                });
            } else {
                // Both — include if action date matches for either
                $q->where(function ($q2) use ($dateFrom, $dateTo) {
                    $q2->where(function ($q3) use ($dateFrom, $dateTo) {
                        $q3->whereNotNull('pending_contract_at');
                        if ($dateFrom) $q3->whereDate('pending_contract_at', '>=', $dateFrom);
                        if ($dateTo)   $q3->whereDate('pending_contract_at', '<=', $dateTo);
                    })->orWhere(function ($q3) use ($dateFrom, $dateTo) {
                        $q3->whereNotNull('declined_at');
                        if ($dateFrom) $q3->whereDate('declined_at', '>=', $dateFrom);
                        if ($dateTo)   $q3->whereDate('declined_at', '<=', $dateTo);
                    });
                });
            }
        })->whereNotNull('submission_by');

        if ($managerId)   $query->where('submission_by', $managerId);
        if ($carrierId)   $query->where('insurance_carrier_id', $carrierId);
        if ($partnerName) $query->where('assigned_partner', $partnerName);
        if ($policyType)  $query->where('policy_type', $policyType);

        $leads = $query->select(
            'id', 'cn_name', 'carrier_name', 'insurance_carrier_id',
            'assigned_partner', 'partner_id',
            'policy_type', 'settlement_type', 'policy_number',
            'monthly_premium', 'agent_commission', 'settlement_percentage',
            'closer_name', 'sale_date', 'state',
            'pending_contract_at', 'declined_at', 'issuance_status',
            'submission_by', 'status'
        )->orderByDesc('pending_contract_at')->get();

        // Calculate commission per lead using the same logic as Submission Performance
        foreach ($leads as $lead) {
            $lead->eff_revenue = $this->calcLeadRevenue($lead);
        }

        $managerName  = null;
        if ($managerId) {
            $mgr = User::withTrashed()->find($managerId);
            $managerName = $mgr ? $mgr->name . ($mgr->trashed() ? ' (Terminated)' : '') : 'Unknown';
        }

        // Current-action counts — each lead counted only once based on its most-recent action
        $resolveAction = function ($lead) {
            if ($lead->pending_contract_at && $lead->declined_at) {
                return $lead->pending_contract_at > $lead->declined_at ? 'pending_contract' : 'declined';
            }
            return $lead->pending_contract_at ? 'pending_contract' : 'declined';
        };

        $totalLeads           = $leads->count();
        $totalPendingContract = $leads->filter(fn($l) => $resolveAction($l) === 'pending_contract')->count();
        $totalDeclined        = $leads->filter(fn($l) => $resolveAction($l) === 'declined')->count();
        $totalPremium         = $leads->sum('monthly_premium');
        $totalCommission      = $leads->sum('eff_revenue');

        $allCarriers    = \App\Models\InsuranceCarrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $allPartners    = \App\Models\Lead::whereNotNull('assigned_partner')->where('assigned_partner', '!=', '')
            ->distinct()->orderBy('assigned_partner')->pluck('assigned_partner');
        $allPolicyTypes = ['Level', 'Graded', 'G.I', 'Modified'];

        return view('admin.reports.manager-submission-drilldown', compact(
            'leads', 'managerName', 'managerId',
            'totalLeads', 'totalPendingContract', 'totalDeclined', 'totalPremium', 'totalCommission',
            'dateFrom', 'dateTo', 'actionFilter',
            'carrierId', 'partnerName', 'policyType',
            'allCarriers', 'allPartners', 'allPolicyTypes'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     * PEREGRINE TEAM REPORT
     * Shows PJC, Closer, and Validator performance for the Peregrine team.
     * Three sections: PJC submissions, Closer pipeline, Validator outcomes.
     * ────────────────────────────────────────────────────────────── */
    public function peregrineTeamReport(Request $request)
    {
        abort_unless(auth()->user()->canViewModule('report-peregrine-team'), 403, 'Access denied.');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        $baseQuery = fn() => Lead::where('team', Teams::PEREGRINE)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        // ── PJC Performance ───────────────────────────────────────
        // Group by the PJC user (verified_by) who submitted the lead
        $pjcLeads = ($baseQuery)()
            ->whereNotNull('verified_by')
            ->with('verifier:id,name')
            ->select('id', 'verified_by', 'account_verified_by', 'status', 'sale_at', 'created_at')
            ->get();

        $pjcRows = $pjcLeads->groupBy('verified_by')->map(function ($leads, $pjcId) {
            $verifier = $leads->first()->verifier;
            return (object) [
                'pjc_id'        => $pjcId,
                'pjc_name'      => $verifier?->name ?? $leads->first()->account_verified_by ?? 'Unknown',
                'total'         => $leads->count(),
                'with_closer'   => $leads->whereIn('status', [Statuses::LEAD_TRANSFERRED, Statuses::LEAD_CLOSED, Statuses::LEAD_SALE, Statuses::LEAD_DECLINED, Statuses::LEAD_RETURNED, Statuses::LEAD_FORWARDED])->count(),
                'with_validator'=> $leads->whereIn('status', [Statuses::LEAD_CLOSED, Statuses::LEAD_SALE, Statuses::LEAD_FORWARDED])->count(),
                'sales'         => $leads->whereNotNull('sale_at')->count(),
                'declined'      => $leads->where('status', Statuses::LEAD_DECLINED)->count(),
                'pending'       => $leads->where('status', Statuses::LEAD_PENDING)->count(),
            ];
        })->sortByDesc('total')->values();

        // ── Closer Performance ────────────────────────────────────
        $closerLeads = Lead::where('team', Teams::PEREGRINE)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->whereNotNull('managed_by')
            ->with('assignedCloser:id,name')
            ->select('id', 'managed_by', 'closer_name', 'status', 'sale_at', 'closed_at', 'created_at')
            ->get();

        $closerRows = $closerLeads->groupBy('managed_by')->map(function ($leads, $closerId) {
            $total   = $leads->count();
            $sales   = $leads->whereNotNull('sale_at')->count();
            $sentToValidator = $leads->whereIn('status', [Statuses::LEAD_CLOSED, Statuses::LEAD_SALE, Statuses::LEAD_FORWARDED])->count();
            $convRate = $total > 0 ? round(($sales / $total) * 100, 1) : 0;
            $closer  = $leads->first()->assignedCloser;
            return (object) [
                'closer_id'        => $closerId,
                'closer_name'      => $closer?->name ?? $leads->first()->closer_name ?? 'Unknown',
                'total_received'   => $total,
                'sent_to_validator'=> $sentToValidator,
                'sales'            => $sales,
                'returned'         => $leads->where('status', Statuses::LEAD_RETURNED)->count(),
                'declined'         => $leads->where('status', Statuses::LEAD_DECLINED)->count(),
                'conversion_rate'  => $convRate,
            ];
        })->sortByDesc('total_received')->values();

        // ── Validator Performance ─────────────────────────────────
        $validatorLeads = Lead::where('team', Teams::PEREGRINE)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->whereNotNull('assigned_validator_id')
            ->with('assignedValidator:id,name')
            ->select('id', 'assigned_validator_id', 'status', 'sale_at', 'validated_at', 'created_at')
            ->get();

        $validatorRows = $validatorLeads->groupBy('assigned_validator_id')->map(function ($leads, $valId) {
            $sales = $leads->whereNotNull('sale_at')->count();
            $total = $leads->count();
            $validator = $leads->first()->assignedValidator;
            return (object) [
                'validator_id'     => $valId,
                'validator_name'   => $validator?->name ?? 'Unknown',
                'total_received'   => $total,
                'marked_sale'      => $sales,
                'returned_closer'  => $leads->where('status', Statuses::LEAD_RETURNED)->count(),
                'declined'         => $leads->where('status', Statuses::LEAD_DECLINED)->count(),
                'pending_ho'       => $leads->where('status', Statuses::LEAD_PENDING)->count(),
                'conversion_rate'  => $total > 0 ? round(($sales / $total) * 100, 1) : 0,
            ];
        })->sortByDesc('total_received')->values();

        // ── Totals ────────────────────────────────────────────────
        $teamTotals = [
            'total_leads'    => ($baseQuery)()->count(),
            'total_sales'    => ($baseQuery)()->whereNotNull('sale_at')->count(),
            'total_declined' => ($baseQuery)()->where('status', Statuses::LEAD_DECLINED)->count(),
            'total_pending'  => ($baseQuery)()->where('status', Statuses::LEAD_PENDING)->count(),
        ];
        $teamTotals['conversion_rate'] = $teamTotals['total_leads'] > 0
            ? round(($teamTotals['total_sales'] / $teamTotals['total_leads']) * 100, 1)
            : 0;

        // ── Individual Sales Records ──────────────────────────────
        $salesLeads = Lead::where('team', Teams::PEREGRINE)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->whereNotNull('sale_at')
            ->with(['assignedCloser:id,name', 'assignedValidator:id,name'])
            ->select('id', 'cn_name', 'closer_name', 'managed_by', 'assigned_validator_id',
                     'sale_at', 'closed_at', 'monthly_premium', 'coverage_amount', 'policy_type', 'status')
            ->orderByDesc('sale_at')
            ->get();

        return view('admin.reports.peregrine-team-report', compact(
            'pjcRows', 'closerRows', 'validatorRows', 'teamTotals',
            'salesLeads', 'dateFrom', 'dateTo'
        ));
    }
}
