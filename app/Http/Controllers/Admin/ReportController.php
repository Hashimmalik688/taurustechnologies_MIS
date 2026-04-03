<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadLead;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
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
    public function perCloser()
    {
        $closerIdsFromLeads = Lead::whereNotNull('managed_by')
            ->distinct()
            ->pluck('managed_by');

        $closerUsers = User::withTrashed()
            ->where(function ($q) use ($closerIdsFromLeads) {
                $q->whereHas('roles', function ($r) {
                        $r->whereIn('name', [Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER]);
                    })
                  ->orWhereIn('id', $closerIdsFromLeads);
            })
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($user) {
                $label = $user->trashed() ? $user->name . ' (Terminated)' : $user->name;
                return ['user:' . $user->id => $label];
            });

        $userNames = $closerUsers->values()->map(fn ($v) => preg_replace('/ \(Terminated\)$/', '', $v));
        $textOnlyClosers = Lead::select('closer_name')
            ->whereNotNull('closer_name')
            ->where('closer_name', '!=', '')
            ->whereNull('managed_by')
            ->distinct()
            ->orderBy('closer_name')
            ->pluck('closer_name')
            ->filter(fn ($name) => !$userNames->contains($name))
            ->mapWithKeys(fn ($name) => ['name:' . $name => $name]);

        $closers = $closerUsers->union($textOnlyClosers);

        return view('admin.reports.per-closer', compact('closers'));
    }

    /**
     * Zoom logs page with call history from Zoom Phone webhooks.
     * This shows ALL calls captured from Zoom webhooks, not just MIS-tracked calls.
     */
    public function zoomLogs(Request $request)
    {
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
     * Show the reports hub page.
     */
    public function index()
    {
        // ── Closers ───────────────────────────────────────────────────
        // Many older leads only have a text closer_name / closer_id field;
        // newer leads use the managed_by FK.  Build a unified list that
        // covers both so every closer is filterable.
        //
        // We use "user:<id>" for FK-based closers and "name:<text>" for
        // text-only closers so the filter can query the right column.

        $closerIdsFromLeads = Lead::whereNotNull('managed_by')
            ->distinct()
            ->pluck('managed_by');

        $closerUsers = User::withTrashed()
            ->where(function ($q) use ($closerIdsFromLeads) {
                $q->whereHas('roles', function ($r) {
                        $r->whereIn('name', ['Ravens Closer', 'Peregrine Closer']);
                    })
                  ->orWhereIn('id', $closerIdsFromLeads);
            })
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($user) {
                $label = $user->trashed() ? $user->name . ' (Terminated)' : $user->name;
                return ['user:' . $user->id => $label];
            });

        // Text-only closer names that don't map to a user record above
        $userNames = $closerUsers->values()->map(fn ($v) => preg_replace('/ \(Terminated\)$/', '', $v));
        $textOnlyClosers = Lead::select('closer_name')
            ->whereNotNull('closer_name')
            ->where('closer_name', '!=', '')
            ->whereNull('managed_by')
            ->distinct()
            ->orderBy('closer_name')
            ->pluck('closer_name')
            ->filter(fn ($name) => !$userNames->contains($name))
            ->mapWithKeys(fn ($name) => ['name:' . $name => $name]);

        $closers = $closerUsers->union($textOnlyClosers);

        // ── Managers ──────────────────────────────────────────────────
        $managers = User::role(['Manager', 'Super Admin', 'Co-ordinator'])
            ->orderBy('name')
            ->pluck('name', 'id');

        // ── Carriers ──────────────────────────────────────────────────
        // Use the defined insurance carriers from the DB. The filter uses
        // CarrierAliases to match the many free-text variations in leads.
        $carriers = InsuranceCarrier::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        // ── Partners ──────────────────────────────────────────────────
        // Same issue: most leads use assigned_partner text, not partner_id FK.
        $partnersFk = Partner::where('is_active', true)
            ->orderBy('name')
            ->pluck('name');

        $partnersText = Lead::select('assigned_partner')
            ->whereNotNull('assigned_partner')
            ->where('assigned_partner', '!=', '')
            ->distinct()
            ->pluck('assigned_partner')
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '');

        $partners = $partnersFk->merge($partnersText)->unique()->sort()->values();

        // ── Verifiers ─────────────────────────────────────────────────
        $verifiers = User::role(['Verifier'])
            ->orderBy('name')
            ->pluck('name', 'id');

        $statuses = [
            Statuses::LEAD_PENDING      => 'Pending',
            Statuses::LEAD_ACCEPTED     => 'Accepted',
            Statuses::LEAD_SALE         => 'Sale',
            Statuses::LEAD_DECLINED     => 'Declined',
            Statuses::LEAD_RETURNED     => 'Returned',
            Statuses::LEAD_CHARGEBACK   => 'Chargeback',
            Statuses::LEAD_UNDERWRITTEN => 'Underwritten',
            Statuses::LEAD_TRANSFERRED  => 'Transferred',
            Statuses::LEAD_CLOSED       => 'Closed',
        ];

        $teams = Lead::select('team')
            ->whereNotNull('team')
            ->where('team', '!=', '')
            ->distinct()
            ->orderBy('team')
            ->pluck('team');

        $sources = Lead::select('source')
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        $states = Lead::select('state')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        return view('admin.reports.index', compact(
            'closers',
            'managers',
            'carriers',
            'partners',
            'verifiers',
            'statuses',
            'teams',
            'sources',
            'states'
        ));
    }

    /**
     * Generate report data based on filters.
     */
    public function generate(Request $request)
    {
        $query = Lead::query()
            ->leftJoin('users as closer_user', 'leads.managed_by', '=', 'closer_user.id')
            ->leftJoin('users as verifier_user', 'leads.verified_by', '=', 'verifier_user.id')
            ->leftJoin('insurance_carriers', 'leads.insurance_carrier_id', '=', 'insurance_carriers.id')
            ->leftJoin('partners', 'leads.partner_id', '=', 'partners.id')
            ->select([
                'leads.id',
                'leads.cn_name',
                'leads.phone_number',
                'leads.state',
                'leads.status',
                'leads.carrier_name',
                'leads.coverage_amount',
                'leads.monthly_premium',
                'leads.policy_type',
                'leads.policy_number',
                'leads.source',
                'leads.team',
                'leads.closer_name',
                'leads.sale_date',
                'leads.sale_at',
                'leads.issuance_status',
                'leads.qa_status',
                'leads.submission_status',
                'leads.retention_status',
                'leads.agent_commission',
                'leads.agent_revenue',
                'leads.settlement_percentage',
                'leads.assigned_partner',
                'leads.created_at',
                'closer_user.name as closer_user_name',
                'verifier_user.name as verifier_user_name',
                'insurance_carriers.name as insurance_carrier_name',
                'partners.name as partner_name',
            ]);

        // Apply all filters (report type presets + individual filters)
        $this->applyFilters($query, $request);

        // Sorting
        $sortBy = $request->input('sort_by', 'leads.created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = [
            'leads.created_at', 'leads.cn_name', 'leads.status', 'leads.sale_date',
            'leads.coverage_amount', 'leads.monthly_premium', 'closer_user.name',
            'insurance_carriers.name', 'partners.name',
        ];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('leads.created_at', 'desc');
        }

        // Summary stats
        $summaryQuery = clone $query;
        $summary = [
            'total_records'    => $summaryQuery->count(),
            'total_premium'    => (clone $summaryQuery)->sum('leads.monthly_premium') ?? 0,
            'total_coverage'   => (clone $summaryQuery)->sum('leads.coverage_amount') ?? 0,
            'total_commission' => (clone $summaryQuery)->sum('leads.agent_commission') ?? 0,
            'total_revenue'    => (clone $summaryQuery)->sum('leads.agent_revenue') ?? 0,
        ];

        // Paginate results
        $perPage = $request->input('per_page', 25);
        $results = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            return response()->json([
                'html'    => view('admin.reports._results', compact('results', 'summary'))->render(),
                'summary' => $summary,
            ]);
        }

        return view('admin.reports.index', compact('results', 'summary'));
    }

    /**
     * Export report data to CSV.
     */
    public function export(Request $request)
    {
        // Re-run the query without pagination
        $query = Lead::query()
            ->leftJoin('users as closer_user', 'leads.managed_by', '=', 'closer_user.id')
            ->leftJoin('users as verifier_user', 'leads.verified_by', '=', 'verifier_user.id')
            ->leftJoin('insurance_carriers', 'leads.insurance_carrier_id', '=', 'insurance_carriers.id')
            ->leftJoin('partners', 'leads.partner_id', '=', 'partners.id')
            ->select([
                'leads.id',
                'leads.cn_name',
                'leads.phone_number',
                'leads.state',
                'leads.status',
                'leads.carrier_name',
                'leads.coverage_amount',
                'leads.monthly_premium',
                'leads.policy_type',
                'leads.policy_number',
                'leads.source',
                'leads.team',
                'leads.closer_name',
                'leads.sale_date',
                'leads.qa_status',
                'leads.submission_status',
                'leads.retention_status',
                'leads.issuance_status',
                'leads.agent_commission',
                'leads.agent_revenue',
                'leads.assigned_partner',
                'leads.created_at',
                'closer_user.name as closer_user_name',
                'verifier_user.name as verifier_user_name',
                'insurance_carriers.name as insurance_carrier_name',
                'partners.name as partner_name',
            ]);

        // Apply same filters as generate()
        $this->applyFilters($query, $request);

        $results = $query->orderBy('leads.created_at', 'desc')->get();

        $filename = 'report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID', 'Client Name', 'Phone', 'State', 'Status',
                'Carrier', 'Coverage', 'Premium', 'Policy Type', 'Policy #',
                'Source', 'Team', 'Closer', 'Partner',
                'Sale Date', 'QA Status', 'Manager Status',
                'Retention', 'Issuance', 'Commission', 'Revenue', 'Created',
            ]);

            foreach ($results as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->cn_name,
                    $row->phone_number,
                    $row->state,
                    $row->status,
                    $row->insurance_carrier_name ?? $row->carrier_name,
                    $row->coverage_amount,
                    $row->monthly_premium,
                    $row->policy_type,
                    $row->policy_number,
                    $row->source,
                    $row->team,
                    $row->closer_user_name ?? $row->closer_name,
                    $row->partner_name ?? $row->assigned_partner,
                    $row->sale_date,
                    $row->qa_status,
                    $row->submission_status,
                    $row->retention_status,
                    $row->issuance_status,
                    $row->agent_commission,
                    $row->agent_revenue,
                    $row->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Apply filters to a query (shared between generate and export).
     *
     * Field mapping notes:
     *  - Carrier: most leads use text `carrier_name`, not FK `insurance_carrier_id`.
     *  - Closer:  older leads use text `closer_name`/`closer_id`, newer use FK `managed_by`.
     *             The closer dropdown sends "user:<id>" or "name:<text>".
     *  - Partner: most leads use text `assigned_partner`, not FK `partner_id`.
     */
    private function applyFilters($query, Request $request)
    {
        $reportType = $request->input('report_type', 'all');

        switch ($reportType) {
            case 'sales':
                // A "sale" is any lead where the closer submitted a sale (has sale_date).
                // This includes pending (awaiting manager), approved (sale), declined, etc.
                $query->whereNotNull('leads.sale_date');
                break;
            case 'partner':
                $query->where(function ($q) {
                    $q->whereNotNull('leads.partner_id')
                      ->orWhere(function ($q2) {
                          $q2->whereNotNull('leads.assigned_partner')
                             ->where('leads.assigned_partner', '!=', '');
                      });
                });
                break;
            case 'submissions':
                $query->whereIn('leads.submission_status', [
                    Statuses::SUB_APPROVED,
                    Statuses::SUB_PENDING,
                    Statuses::SUB_UNDERWRITING,
                ]);
                break;
            case 'chargebacks':
                $query->where('leads.status', Statuses::LEAD_CHARGEBACK);
                break;
            case 'retention':
                $query->whereNotNull('leads.retention_status');
                break;
            case 'issuance':
                $query->whereNotNull('leads.issuance_status');
                break;
        }

        // Closer: "user:123" = managed_by FK, "name:John" = closer_name text
        if ($request->filled('closer_id')) {
            $raw = $request->closer_id;
            if (str_starts_with($raw, 'user:')) {
                $userId = (int) substr($raw, 5);
                $query->where(function ($q) use ($userId) {
                    $q->where('leads.managed_by', $userId)
                      ->orWhere('leads.closer_id', $userId);
                });
            } elseif (str_starts_with($raw, 'name:')) {
                $name = substr($raw, 5);
                $query->where('leads.closer_name', $name);
            } else {
                // Fallback for plain IDs
                $query->where(function ($q) use ($raw) {
                    $q->where('leads.managed_by', $raw)
                      ->orWhere('leads.closer_id', $raw);
                });
            }
        }
        if ($request->filled('manager_id')) {
            $query->where('leads.submission_by', $request->manager_id);
        }
        // Carrier: use alias mapping to match free-text carrier_name variations
        if ($request->filled('carrier_id')) {
            CarrierAliases::applyFilter($query, $request->carrier_id);
        }
        // Partner: match against text assigned_partner OR partner_id FK via name
        if ($request->filled('partner_id')) {
            $partnerName = $request->partner_id;
            $query->where(function ($q) use ($partnerName) {
                $q->where(DB::raw('TRIM(leads.assigned_partner)'), $partnerName)
                  ->orWhereHas('partner', function ($q2) use ($partnerName) {
                      $q2->where('name', $partnerName);
                  });
            });
        }
        if ($request->filled('verifier_id')) {
            $query->where('leads.verified_by', $request->verifier_id);
        }
        if ($request->filled('status')) {
            $query->where('leads.status', $request->status);
        }
        if ($request->filled('team')) {
            $query->where('leads.team', $request->team);
        }
        if ($request->filled('source')) {
            $query->where('leads.source', $request->source);
        }
        if ($request->filled('state')) {
            $query->where('leads.state', $request->state);
        }
        if ($request->filled('qa_status')) {
            $query->where('leads.qa_status', $request->qa_status);
        }
        if ($request->filled('submission_status')) {
            $query->where('leads.submission_status', $request->submission_status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('leads.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('leads.created_at', '<=', $request->date_to);
        }
        if ($request->filled('sale_date_from')) {
            $query->whereDate('leads.sale_date', '>=', $request->sale_date_from);
        }
        if ($request->filled('sale_date_to')) {
            $query->whereDate('leads.sale_date', '<=', $request->sale_date_to);
        }
    }

    /**
     * Per-closer performance stats (AJAX endpoint).
     */
    public function closerStats(Request $request)
    {
        $tz = 'America/Los_Angeles';
        $appTz = config('app.timezone', 'America/Los_Angeles');

        // Build PT date range for whereBetween queries
        $startDate = $request->filled('cs_date_from')
            ? Carbon::parse($request->cs_date_from, $tz)->startOfDay()->setTimezone($appTz)
            : Carbon::now($tz)->startOfMonth()->setTimezone($appTz);
        $endDate = $request->filled('cs_date_to')
            ? Carbon::parse($request->cs_date_to, $tz)->endOfDay()->setTimezone($appTz)
            : Carbon::now($tz)->endOfDay()->setTimezone($appTz);

        // Get all closers (users with closer roles)
        $closerQuery = User::withTrashed()
            ->whereHas('roles', function ($r) {
                $r->whereIn('name', [Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER]);
            });

        // Optional single closer filter
        if ($request->filled('cs_closer')) {
            $raw = $request->cs_closer;
            if (str_starts_with($raw, 'user:')) {
                $closerQuery->where('id', (int) substr($raw, 5));
            } elseif (str_starts_with($raw, 'name:')) {
                $closerQuery->where('name', substr($raw, 5));
            } else {
                $closerQuery->where('id', $raw);
            }
        }

        // Optional team filter — team is derived from role, not the department column
        if ($request->filled('cs_team')) {
            $teamRole = strtolower($request->cs_team) === 'ravens'
                ? Roles::RAVENS_CLOSER
                : Roles::PEREGRINE_CLOSER;
            $closerQuery->whereHas('roles', fn ($r) => $r->where('name', $teamRole));
        }

        $closers = $closerQuery->orderBy('name')->get();

        if ($closers->isEmpty()) {
            return response()->json(['html' => '', 'rows' => []]);
        }

        $closerIds = $closers->pluck('id')->toArray();

        // ── Aggregate dials from lead_dials ────────────────────────────
        $dials = LeadDial::select(
                'user_id',
                DB::raw('COUNT(*) as total_dialed'),
                DB::raw("SUM(CASE WHEN outcome = 'connected' THEN 1 ELSE 0 END) as connected")
            )
            ->whereIn('user_id', $closerIds)
            ->whereBetween('dialed_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // ── Aggregate dispositions from bad_leads ─────────────────────
        $disposed = BadLead::select(
                'disposed_by',
                DB::raw('COUNT(*) as total_disposed')
            )
            ->whereIn('disposed_by', $closerIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('disposed_by')
            ->get()
            ->keyBy('disposed_by');

        // ── Aggregate sales from leads ────────────────────────────────
        // Match sales the same way the Ravens dashboard does:
        // check closer_name (text), closer_id, or managed_by FK.
        $closerNames = $closers->pluck('name', 'id')->toArray();

        $salesRaw = collect();
        foreach ($closerIds as $cId) {
            $cName = $closerNames[$cId] ?? '';
            $count = Lead::where(function ($q) use ($cId, $cName) {
                    $q->where('managed_by', $cId)
                      ->orWhere('closer_id', $cId);
                    if ($cName) {
                        $q->orWhere('closer_name', $cName);
                    }
                })
                ->whereNotNull('sale_date')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('sale_at', [$startDate, $endDate])
                      ->orWhereBetween('sale_date', [$startDate, $endDate]);
                })
                ->count();

            $salesRaw->put($cId, (object) ['total_sales' => $count]);
        }

        // ── Build rows ────────────────────────────────────────────────
        $rows = [];
        $totals = [
            'dialed'    => 0,
            'connected' => 0,
            'disposed'  => 0,
            'sales'     => 0,
        ];

        foreach ($closers as $closer) {
            $id = $closer->id;
            $dialed    = $dials->get($id)->total_dialed ?? 0;
            $connected = $dials->get($id)->connected ?? 0;
            $disposedCount = $disposed->get($id)->total_disposed ?? 0;
            $sales     = $salesRaw->get($id)->total_sales ?? 0;

            $contactRate    = $dialed > 0 ? round(($connected / $dialed) * 100, 1) : 0;
            $conversionRate = $connected > 0 ? round(($sales / $connected) * 100, 1) : 0;
            $disposalRate   = $dialed > 0 ? round(($disposedCount / $dialed) * 100, 1) : 0;
            $salesRate      = $dialed > 0 ? round(($sales / $dialed) * 100, 1) : 0;

            // Determine team from role
            $roleNames = $closer->roles->pluck('name')->toArray();
            $team = in_array(Roles::RAVENS_CLOSER, $roleNames) ? 'Ravens' : 'Peregrine';

            $totals['dialed']    += $dialed;
            $totals['connected'] += $connected;
            $totals['disposed']  += $disposedCount;
            $totals['sales']     += $sales;

            $rows[] = [
                'id'              => $id,
                'name'            => $closer->name . ($closer->trashed() ? ' (Terminated)' : ''),
                'team'            => $team,
                'dialed'          => $dialed,
                'connected'       => $connected,
                'disposed'        => $disposedCount,
                'sales'           => $sales,
                'contact_rate'    => $contactRate,
                'conversion_rate' => $conversionRate,
                'disposal_rate'   => $disposalRate,
                'sales_rate'      => $salesRate,
            ];
        }

        // Totals row ratios
        $totals['contact_rate']    = $totals['dialed'] > 0 ? round(($totals['connected'] / $totals['dialed']) * 100, 1) : 0;
        $totals['conversion_rate'] = $totals['connected'] > 0 ? round(($totals['sales'] / $totals['connected']) * 100, 1) : 0;
        $totals['disposal_rate']   = $totals['dialed'] > 0 ? round(($totals['disposed'] / $totals['dialed']) * 100, 1) : 0;
        $totals['sales_rate']      = $totals['dialed'] > 0 ? round(($totals['sales'] / $totals['dialed']) * 100, 1) : 0;

        $html = view('admin.reports._closer_stats', [
            'rows'      => $rows,
            'totals'    => $totals,
            'startDate' => $startDate->copy()->setTimezone($tz)->format('M d, Y'),
            'endDate'   => $endDate->copy()->setTimezone($tz)->format('M d, Y'),
        ])->render();

        return response()->json([
            'html'   => $html,
            'rows'   => $rows,
            'totals' => $totals,
        ]);
    }

    /**
     * Export per-closer stats to CSV.
     */
    public function closerStatsExport(Request $request)
    {
        // Re-use closerStats logic to get the data
        $data = $this->closerStats($request)->getData(true);
        $rows = $data['rows'] ?? [];
        $totals = $data['totals'] ?? [];

        $filename = 'closer_stats_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows, $totals) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Closer', 'Team', 'Total Dialed', 'Connected', 'Disposed',
                'Sales', 'Contact Rate %', 'Conversion Rate %',
                'Disposal Rate %', 'Sales Rate %',
            ]);

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row['name'],
                    $row['team'],
                    $row['dialed'],
                    $row['connected'],
                    $row['disposed'],
                    $row['sales'],
                    $row['contact_rate'] . '%',
                    $row['conversion_rate'] . '%',
                    $row['disposal_rate'] . '%',
                    $row['sales_rate'] . '%',
                ]);
            }

            // Totals row
            fputcsv($file, [
                'TOTAL', '',
                $totals['dialed'] ?? 0,
                $totals['connected'] ?? 0,
                $totals['disposed'] ?? 0,
                $totals['sales'] ?? 0,
                ($totals['contact_rate'] ?? 0) . '%',
                ($totals['conversion_rate'] ?? 0) . '%',
                ($totals['disposal_rate'] ?? 0) . '%',
                ($totals['sales_rate'] ?? 0) . '%',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Submission Performance Report — carrier-wise breakdown of leads that have
     * been approved and sent to Pending Contract (pending_contract_at IS NOT NULL).
     * Shows total sales count and total monthly premium per carrier.
     */
    /**
     * Build a lookup map of commission rates from AgentCarrierState and InsuranceCarrier.
     * Returns: [ 'partner_carrier_state' => pct, 'partner_carrier' => pct, 'carrier' => pct ]
     * Used to estimate revenue when agent_commission is not yet stored on a lead.
     */
    private function buildCommissionRateMap(): array
    {
        // State-specific rates: keyed by "{partner_id}_{carrier_id}_{state}_{type}"
        $stateRates = [];
        \App\Models\AgentCarrierState::whereNotNull('partner_id')
            ->whereNotNull('insurance_carrier_id')
            ->select('partner_id', 'insurance_carrier_id', 'state',
                'settlement_level_pct', 'settlement_graded_pct',
                'settlement_gi_pct', 'settlement_modified_pct')
            ->get()
            ->each(function ($r) use (&$stateRates) {
                $base = "{$r->partner_id}_{$r->insurance_carrier_id}_{$r->state}";
                $stateRates["{$base}_level"]    = $r->settlement_level_pct;
                $stateRates["{$base}_graded"]   = $r->settlement_graded_pct;
                $stateRates["{$base}_gi"]       = $r->settlement_gi_pct;
                $stateRates["{$base}_modified"] = $r->settlement_modified_pct;
            });

        // Partner-carrier fallback: best available rate per partner+carrier (take max across states)
        $partnerCarrierRates = [];
        \App\Models\AgentCarrierState::whereNotNull('partner_id')
            ->whereNotNull('insurance_carrier_id')
            ->select('partner_id', 'insurance_carrier_id',
                DB::raw('MAX(settlement_level_pct) as lvl'),
                DB::raw('MAX(settlement_graded_pct) as grd'),
                DB::raw('MAX(settlement_gi_pct) as gi_pct'),
                DB::raw('MAX(settlement_modified_pct) as mod_pct'))
            ->groupBy('partner_id', 'insurance_carrier_id')
            ->get()
            ->each(function ($r) use (&$partnerCarrierRates) {
                $base = "{$r->partner_id}_{$r->insurance_carrier_id}";
                $partnerCarrierRates["{$base}_level"]    = $r->lvl;
                $partnerCarrierRates["{$base}_graded"]   = $r->grd;
                $partnerCarrierRates["{$base}_gi"]       = $r->gi_pct;
                $partnerCarrierRates["{$base}_modified"] = $r->mod_pct;
            });

        // Carrier base rates (last fallback)
        $carrierBaseRates = \App\Models\InsuranceCarrier::whereNotNull('base_commission_percentage')
            ->where('base_commission_percentage', '>', 0)
            ->pluck('base_commission_percentage', 'id')
            ->toArray();

        return compact('stateRates', 'partnerCarrierRates', 'carrierBaseRates');
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
     * Calculate effective revenue for one lead using the rate map.
     * Priority: stored agent_commission → state-specific rate → partner-carrier rate → carrier base rate
     * Returns ['revenue' => float, 'rate' => float|null, 'source' => string]
     */
    private function calcLeadRevenue($lead, array $rateMap): array
    {
        // Use stored commission if valid
        if ($lead->agent_commission > 0) {
            $rate = $lead->monthly_premium > 0
                ? round(($lead->agent_commission / ($lead->monthly_premium * 9)) * 100, 2)
                : $lead->settlement_percentage;
            return ['revenue' => (float)$lead->agent_commission, 'rate' => $rate, 'source' => 'stored'];
        }

        $premium = (float)($lead->monthly_premium ?? 0);
        if ($premium <= 0) {
            return ['revenue' => 0, 'rate' => null, 'source' => 'no_premium'];
        }

        $type   = $this->resolveSettlementKey($lead);
        $pId    = $lead->partner_id;
        $cId    = $lead->insurance_carrier_id;
        $state  = $lead->state ?? '';

        // 1. State-specific rate
        if ($pId && $cId && $state) {
            $key = "{$pId}_{$cId}_{$state}_{$type}";
            $pct = $rateMap['stateRates'][$key] ?? null;
            if (!$pct && $type !== 'level') {
                $pct = $rateMap['stateRates']["{$pId}_{$cId}_{$state}_level"] ?? null;
            }
            if ($pct > 0) {
                return ['revenue' => round($premium * 9 * ($pct / 100), 2), 'rate' => $pct, 'source' => 'partner_carrier_state'];
            }
        }

        // 2. Partner-carrier fallback (max rate across states)
        if ($pId && $cId) {
            $key = "{$pId}_{$cId}_{$type}";
            $pct = $rateMap['partnerCarrierRates'][$key] ?? null;
            if (!$pct && $type !== 'level') {
                $pct = $rateMap['partnerCarrierRates']["{$pId}_{$cId}_level"] ?? null;
            }
            if ($pct > 0) {
                return ['revenue' => round($premium * 9 * ($pct / 100), 2), 'rate' => $pct, 'source' => 'partner_carrier'];
            }
        }

        // 3. Carrier base rate
        if ($cId && isset($rateMap['carrierBaseRates'][$cId])) {
            $pct = $rateMap['carrierBaseRates'][$cId];
            return ['revenue' => round($premium * 9 * ($pct / 100), 2), 'rate' => $pct, 'source' => 'carrier_base'];
        }

        return ['revenue' => 0, 'rate' => null, 'source' => 'no_rate'];
    }

    public function submissionPerformance(Request $request)
    {
        // Default to current month
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->endOfMonth()->toDateString());

        $query = Lead::whereNotNull('pending_contract_at')
            ->whereNotNull('closer_name');

        if ($dateFrom) $query->whereDate('sale_date', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('sale_date', '<=', $dateTo);

        // Load all leads and calculate revenue dynamically from configured rates
        $leads = $query->select(
            'insurance_carrier_id', 'carrier_name', 'partner_id', 'assigned_partner',
            'monthly_premium', 'agent_commission', 'settlement_type', 'policy_type',
            'state', 'settlement_percentage'
        )->get();

        $rateMap = $this->buildCommissionRateMap();

        // Group by carrier+partner, aggregating with dynamic revenue
        $carriersData = $leads
            ->groupBy(fn ($l) => ($l->insurance_carrier_id ?? 'null') . '_' . ($l->partner_id ?? 'null'))
            ->map(function ($group) use ($rateMap) {
                $first        = $group->first();
                $totalRevenue = 0;
                foreach ($group as $lead) {
                    $totalRevenue += $this->calcLeadRevenue($lead, $rateMap)['revenue'];
                }
                return (object) [
                    'insurance_carrier_id' => $first->insurance_carrier_id,
                    'carrier_name'         => $first->carrier_name,
                    'partner_id'           => $first->partner_id,
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
        $dateFrom  = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo    = $request->get('date_to',   now()->endOfMonth()->toDateString());
        $carrierId = $request->get('carrier');
        $partnerId = $request->get('partner');

        $query = Lead::whereNotNull('pending_contract_at')
            ->whereNotNull('closer_name');

        if ($dateFrom)  $query->whereDate('sale_date', '>=', $dateFrom);
        if ($dateTo)    $query->whereDate('sale_date', '<=', $dateTo);
        if ($carrierId) $query->where('insurance_carrier_id', $carrierId);
        if ($partnerId) $query->where('partner_id', $partnerId);

        $rawLeads = $query->select(
                'id', 'cn_name', 'carrier_name', 'assigned_partner',
                'insurance_carrier_id', 'partner_id',
                'monthly_premium', 'agent_commission', 'settlement_percentage',
                'policy_type', 'settlement_type', 'state', 'sale_date',
                'pending_contract_at', 'closer_name', 'issuance_status',
                'commission_calculation_notes', 'commission_calculated_at'
            )
            ->orderByDesc('sale_date')
            ->get();

        $rateMap = $this->buildCommissionRateMap();

        // Attach dynamic revenue info to each lead
        $leads = $rawLeads->map(function ($lead) use ($rateMap) {
            $calc = $this->calcLeadRevenue($lead, $rateMap);
            $lead->eff_revenue = $calc['revenue'];
            $lead->eff_rate    = $calc['rate'];
            $lead->rev_source  = $calc['source'];
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
            'carrierId', 'partnerId'
        ));
    }
}
