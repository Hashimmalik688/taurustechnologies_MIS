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
        // Build query showing ALL call events (matches Zoom's dashboard view)
        $query = \App\Models\ZoomWebhookLog::query()
            ->with(['lead:id,cn_name,phone_number,state', 'agent:id,name,email'])
            ->orderBy('call_start_time', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('user_filter')) {
            $userSearch = $request->user_filter;
            $query->where(function($q) use ($userSearch) {
                $q->where('caller_number', 'like', "%{$userSearch}%")
                  ->orWhere('callee_number', 'like', "%{$userSearch}%")
                  ->orWhere('caller_name', 'like', "%{$userSearch}%")
                  ->orWhere('callee_name', 'like', "%{$userSearch}%")
                  ->orWhere('caller_extension', 'like', "%{$userSearch}%")
                  ->orWhere('callee_extension', 'like', "%{$userSearch}%");
            });
        }

        if ($request->filled('call_status')) {
            $query->where(function($q) use ($request) {
                $q->where('call_status', $request->call_status)
                  ->orWhere('call_result', 'like', '%' . $request->call_status . '%');
            });
        }

        if ($request->filled('call_type')) {
            $query->where('call_type', $request->call_type);
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

        if ($request->filled('date_from')) {
            $query->whereDate('call_start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('call_start_time', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('caller_number', 'like', "%{$search}%")
                  ->orWhere('callee_number', 'like', "%{$search}%")
                  ->orWhere('caller_name', 'like', "%{$search}%")
                  ->orWhere('callee_name', 'like', "%{$search}%")
                  ->orWhere('zoom_call_id', 'like', "%{$search}%");
            });
        }

        // Paginate results
        $callLogs = $query->paginate(50)->appends($request->all());

        // Calculate personalized stats if filtering by user
        $userStats = null;
        if ($request->filled('user_filter')) {
            $userSearch = $request->user_filter;
            $userQuery = \App\Models\ZoomWebhookLog::query()
                ->where(function($q) use ($userSearch) {
                    $q->where('caller_number', 'like', "%{$userSearch}%")
                      ->orWhere('callee_number', 'like', "%{$userSearch}%")
                      ->orWhere('caller_name', 'like', "%{$userSearch}%")
                      ->orWhere('callee_name', 'like', "%{$userSearch}%")
                      ->orWhere('caller_extension', 'like', "%{$userSearch}%")
                      ->orWhere('callee_extension', 'like', "%{$userSearch}%");
                });

            // Apply same date filters
            if ($request->filled('date_from')) {
                $userQuery->whereDate('call_start_time', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $userQuery->whereDate('call_start_time', '<=', $request->date_to);
            }

            $userStats = [
                'total_dialed' => (clone $userQuery)->whereIn('event_type', [
                    'phone.caller_connected', 
                    'phone.caller_ended', 
                    'phone.caller_call_log_completed',
                    'phone.callout.started'
                ])->count(),
                
                'connected' => (clone $userQuery)->whereIn('call_result', [
                    'Connected', 
                    'answered', 
                    'Answered', 
                    'Call connected'
                ])->orWhereIn('event_type', ['phone.caller_connected', 'phone.callee_answered'])->count(),
                
                'failed' => (clone $userQuery)->where(function($q) {
                    $q->whereIn('call_result', ['No Answer', 'Missed', 'Rejected', 'Busy', 'Failed', 'Declined'])
                      ->orWhereIn('call_status', ['missed', 'rejected', 'busy', 'failed', 'no_answer']);
                })->count(),
                
                'with_recording' => (clone $userQuery)->whereNotNull('recording_url')->count(),
                
                'recordings_over_5min' => (clone $userQuery)
                    ->whereNotNull('recording_url')
                    ->where('duration_seconds', '>', 300)
                    ->count(),
                
                'total_talk_time' => (clone $userQuery)
                    ->whereIn('event_type', ['phone.caller_call_log_completed', 'phone.callee_call_log_completed'])
                    ->sum('duration_seconds'),
            ];
        }

        // Get filter options
        $callStatuses = [
            'answered' => 'Answered',
            'connected' => 'Connected',
            'completed' => 'Completed',
            'no answer' => 'No Answer',
            'missed' => 'Missed',
            'rejected' => 'Rejected',
            'busy' => 'Busy',
            'voicemail' => 'Voicemail',
            'Call connected' => 'Call Connected',
        ];

        $callTypes = [
            'inbound' => 'Inbound',
            'outbound' => 'Outbound',
            'internal' => 'Internal',
        ];

        $eventTypes = \App\Models\ZoomWebhookLog::select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type', 'event_type');

        // Calculate statistics (counting ALL events like Zoom dashboard)
        $stats = [
            'total_calls' => \App\Models\ZoomWebhookLog::count(),
            'total_duration' => \App\Models\ZoomWebhookLog::sum('duration_seconds'),
            'calls_with_recording' => \App\Models\ZoomWebhookLog::whereNotNull('recording_url')->count(),
            'today_calls' => \App\Models\ZoomWebhookLog::whereDate('call_start_time', today())->count(),
            'answered_calls' => \App\Models\ZoomWebhookLog::whereIn('call_result', ['Connected', 'answered', 'Answered', 'Call connected'])->count(),
        ];

        return view('admin.reports.zoom-logs', compact(
            'callLogs',
            'callStatuses',
            'callTypes',
            'eventTypes',
            'stats',
            'userStats'
        ));
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
                'leads.manager_status',
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
                'leads.manager_status',
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
                    $row->manager_status,
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
                $query->whereIn('leads.manager_status', [
                    Statuses::MGR_APPROVED,
                    Statuses::MGR_PENDING,
                    Statuses::MGR_UNDERWRITING,
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
            $query->where('leads.manager_user_id', $request->manager_id);
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
        if ($request->filled('manager_status')) {
            $query->where('leads.manager_status', $request->manager_status);
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
        $tz = 'America/Denver';
        $appTz = config('app.timezone', 'Asia/Karachi');

        // Build MT date range, then convert to app timezone (Asia/Karachi)
        // so whereBetween matches the PKT timestamps stored by now().
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

        // Optional team filter
        if ($request->filled('cs_team')) {
            $closerQuery->where('department', $request->cs_team);
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
}
