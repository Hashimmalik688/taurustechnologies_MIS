<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Lead;
use App\Models\User;
use App\Services\CommissionCalculationService;
use App\Services\RevenueCalculationService;
use App\Support\Roles;
use App\Support\Statuses;
use App\Support\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.attendance');
    }

    /**
     * Show the application dashboard.
     */
    public function index(Request $request)
    {
        // If authenticated and accessing root, show dashboard
        if (Auth::check() && ($request->path() === '/' || $request->path() === 'index')) {
            return $this->root();
        }

        // Otherwise, check if view exists for the path
        if (view()->exists($request->path())) {
            return view($request->path());
        }

        return abort(404);
    }

    /**
     * Smart router - redirects each user to their appropriate landing page based on role.
     * This is the entry point for / and post-login redirects.
     */
    public function root()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Role-based redirects to each role's primary workspace
        $roleRedirects = [
            Roles::VERIFIER            => 'verifier.dashboard',
            Roles::PEREGRINE_CLOSER    => 'peregrine.closers.index',
            Roles::PEREGRINE_VALIDATOR => 'validator.index',
            Roles::PEREGRINE_MANAGER   => 'settings.reports.hub',
            Roles::EMPLOYEE            => 'attendance.dashboard',
            Roles::RAVENS_CLOSER       => 'ravens.dashboard',
            Roles::QA                  => 'qa.review',
            Roles::RETENTION_OFFICER   => 'retention.dashboard',
            Roles::HR                  => 'attendance.index',
        ];

        foreach ($roleRedirects as $role => $routeName) {
            if ($user->hasRole($role)) {
                return redirect()->route($routeName);
            }
        }

        // Admin roles (Super Admin, CEO, Manager, Co-ordinator) - check dashboard permission
        if ($user->canViewModule('dashboard')) {
            return redirect()->route('dashboard');
        }

        // If dashboard is restricted, find first accessible module
        $fallbackRoutes = [
            'sales'      => 'sales.index',
            'leads'      => 'leads.index',
            'retention'  => 'retention.index',
            'attendance' => 'attendance.index',
            'users'      => 'users.index',
            'settings'   => 'settings.index',
        ];

        foreach ($fallbackRoutes as $moduleSlug => $routeName) {
            if ($user->canViewModule($moduleSlug)) {
                try {
                    return redirect()->route($routeName);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        // Last resort - attendance dashboard (everyone should have this)
        return redirect()->route('attendance.dashboard');
    }

    // ── Commission helpers — same logic as Submission Performance report ──
    private function resolveSettlementKey($lead): string
    {
        $raw = strtolower(trim($lead->settlement_type ?: $lead->policy_type ?: ''));
        if (str_contains($raw, 'g.i') || str_contains($raw, 'gi')) return 'gi';
        if (str_contains($raw, 'grad')) return 'graded';
        if (str_contains($raw, 'modif')) return 'modified';
        return 'level';
    }

    private function calcLeadRevenue($lead): float
    {
        $premium = (float) ($lead->monthly_premium ?? 0);
        if ($premium <= 0) return 0.0;
        if (empty($lead->partner_id) || empty($lead->insurance_carrier_id)) return 0.0;
        $result = app(CommissionCalculationService::class)->calculateCommission(
            (int) $lead->partner_id,
            (int) $lead->insurance_carrier_id,
            $lead->state ?? '',
            $this->resolveSettlementKey($lead),
            $premium
        );
        return round($result['commission'] ?? 0, 2);
    }

    /**
     * Executive Dashboard (Company Overview) with live data.
     * Accessible at /dashboard with role.permission:dashboard,view middleware.
     */
    public function executiveDashboard(Request $request)
    {
        $today = today(); // PT (app timezone = America/Los_Angeles)

        // ── Period: accept ?period=YYYY-MM or default to current 3rd→3rd ──
        $periodParam = $request->get('period'); // e.g. "2026-04"
        if ($periodParam && preg_match('/^\d{4}-\d{2}$/', $periodParam)) {
            // period='2026-04' means the period STARTING on Apr 3 → set anchor to Apr 3
            $anchor = \Carbon\Carbon::createFromFormat('Y-m', $periodParam)->setDay(3);
        } else {
            $anchor = $today->copy();
        }
        if ($anchor->day >= 3) {
            $rev_period_start = $anchor->copy()->setDay(3)->startOfDay();
            $rev_period_end   = $anchor->copy()->addMonthNoOverflow()->setDay(3)->endOfDay();
        } else {
            $rev_period_start = $anchor->copy()->subMonthNoOverflow()->setDay(3)->startOfDay();
            $rev_period_end   = $anchor->copy()->setDay(3)->endOfDay();
        }
        $revenue_period_label = $rev_period_start->format('M j') . ' → ' . $rev_period_end->format('M j');
        // For the selector: which month is the "anchor" (the start month of the period)
        $selected_period = $rev_period_start->format('Y-m');

        // ── All MTD leads in the 3rd→3rd period (Pending Contract page) ──
        $revenue_leads = Lead::whereNotNull('pending_contract_at')
            ->whereDate('pending_contract_at', '>=', $rev_period_start)
            ->whereDate('pending_contract_at', '<=', $rev_period_end)
            ->get(['id', 'monthly_premium', 'carrier_name', 'assigned_partner', 'closer_name',
                   'submission_status', 'pending_contract_at',
                   'partner_id', 'insurance_carrier_id', 'state', 'settlement_type', 'policy_type']);

        $mtd_sales      = $revenue_leads->count();
        $total_revenue  = $revenue_leads->sum('monthly_premium');

        // Daily avg premium: total premium / distinct days that had sales
        $distinct_sale_days = max(1, $revenue_leads->groupBy(
            fn($l) => \Carbon\Carbon::parse($l->pending_contract_at)->toDateString()
        )->count());
        $daily_avg_premium = round($total_revenue / $distinct_sale_days, 0);

        // Est. commission — same calculation as Submission Performance report (premium × 9 × commission%)
        $est_commission = 0;
        foreach ($revenue_leads as $lead) { $est_commission += $this->calcLeadRevenue($lead); }
        $est_commission = round($est_commission, 0);

        // ── Submitted / Approved / Declined from Pending Submission page ──
        // Matches PendingsApprovedController baseConditions exactly (sale_date based)
        $subBase = Lead::where(function ($q) {
                $q->where('ravens_validation_status', 'valid')
                  ->orWhereNotNull('validated_at');
            })
            ->whereNotNull('closer_name')
            ->whereNotNull('cn_name')->where('cn_name', '!=', '')
            ->where(function ($q) { $q->whereNotNull('sale_at')->orWhereNotNull('sale_date'); })
            ->where(function ($q) {
                $q->where(function ($s) { $s->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function ($s) { $s->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function ($s) { $s->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            })
            ->whereDate('sale_date', '>=', $rev_period_start)
            ->whereDate('sale_date', '<=', $rev_period_end);

        $submitted_count    = (clone $subBase)->count();
        // Approved = submission_status='approved' (includes those moved to Pending Contract)
        $mtd_approved       = (clone $subBase)->where('submission_status', Statuses::SUB_APPROVED)->count();
        // Declined = still in submission stage (pending_contract_at IS NULL) + declined
        $sub_declined_count = (clone $subBase)->whereNull('pending_contract_at')
                                ->where('submission_status', Statuses::SUB_DECLINED)->count();

        // ── Today leads (filter from MTD set) ────────────────────
        $today_leads    = $revenue_leads->filter(fn($l) =>
            $l->pending_contract_at && \Carbon\Carbon::parse($l->pending_contract_at)->isSameDay($today)
        );
        $today_sales         = $today_leads->count();
        $today_revenue       = $today_leads->sum('monthly_premium');
        $today_approved      = $today_leads->where('submission_status', Statuses::SUB_APPROVED)->count();
        $today_declined      = $today_leads->where('submission_status', Statuses::SUB_DECLINED)->count();
        $today_est_commission = 0;
        foreach ($today_leads as $lead) { $today_est_commission += $this->calcLeadRevenue($lead); }
        $today_est_commission = round($today_est_commission, 0);

        // Aliases for pipeline / backward compat
        $done_count     = $submitted_count;
        $approved_count = $mtd_approved;
        $declined_count = $sub_declined_count;

        // Revenue by carrier (top 5 for summary panel)
        $revenue_by_carrier = $revenue_leads
            ->groupBy('carrier_name')
            ->map(fn($g) => [
                'carrier'  => $g->first()->carrier_name ?? 'Unknown',
                'count'    => $g->count(),
                'premium'  => $g->sum('monthly_premium'),
            ])
            ->sortByDesc('premium')
            ->values()
            ->take(5)
            ->toArray();

        $revenue_total_submissions = $revenue_leads->count();

        // ── Attendance ────────────────────────────────────────────
        $todayAttendances = \App\Models\Attendance::with('user')
            ->whereDate('date', $today->toDateString())
            ->get();

        $attendance = $todayAttendances->map(fn($att) => [
            'name'   => optional($att->user)->name ?? '—',
            'status' => $att->status,
        ])->toArray();

        $present_count          = $todayAttendances->whereIn('status', [Statuses::ATTENDANCE_PRESENT, Statuses::ATTENDANCE_LATE])->count();
        $absent_count           = $todayAttendances->where('status', Statuses::ATTENDANCE_ABSENT)->count();
        $total_attendance_count = $todayAttendances->count();

        // ── Sales per closer (uses revenue_leads = 3rd→3rd period) ──
        $closerGroups = $revenue_leads->filter(fn($l) => !empty($l->closer_name))->groupBy('closer_name');

        $sales_per_closer = [];
        foreach ($closerGroups as $closerName => $sales) {
            $todaySales = $sales->filter(fn($s) =>
                $s->pending_contract_at && \Carbon\Carbon::parse($s->pending_contract_at)->isSameDay($today)
            )->count();
            $user = User::where('name', $closerName)->first();
            $team = ($user && $user->hasRole([Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR]))
                ? Teams::PEREGRINE
                : Teams::RAVENS;

            $sales_per_closer[] = [
                'closer'      => $closerName,
                'today'       => $todaySales,
                'mtd'         => $sales->count(),
                'approvedMTD' => $sales->where('submission_status', Statuses::SUB_APPROVED)->count(),
                'declinedMTD' => $sales->where('submission_status', Statuses::SUB_DECLINED)->count(),
                'uwMTD'       => 0,
                'team'        => $team,
            ];
        }
        usort($sales_per_closer, fn($a, $b) => $b['mtd'] - $a['mtd']);

        // ── Team counts ───────────────────────────────────────────
        $peregrine_count = User::role([Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR])
            ->where('status', '!=', Statuses::USER_INACTIVE)->count();
        $ravens_count = User::role(Roles::RAVENS_CLOSER)
            ->where('status', '!=', Statuses::USER_INACTIVE)->count();

        // ── Manager Submission Breakdown — today PT ───────────────
        $managerTodayLeads = Lead::where(function ($q) use ($today) {
                $q->whereDate('pending_contract_at', $today)
                  ->orWhereDate('declined_at', $today);
            })
            ->whereNotNull('submission_by')
            ->select('id', 'submission_by', 'pending_contract_at', 'declined_at', 'monthly_premium')
            ->get();

        $currentAction = function ($lead) {
            if ($lead->pending_contract_at && $lead->declined_at) {
                return $lead->pending_contract_at > $lead->declined_at ? 'pending_contract' : 'declined';
            }
            return $lead->pending_contract_at ? 'pending_contract' : 'declined';
        };

        $managerGroups = $managerTodayLeads->groupBy('submission_by');
        $managerIds    = $managerGroups->keys()->filter()->toArray();
        $managerUsers  = User::withTrashed()->whereIn('id', $managerIds)->get()->keyBy('id');

        $manager_breakdown = $managerGroups->map(function ($group, $managerId) use ($managerUsers, $currentAction) {
            $pending  = $group->filter(fn($l) => $currentAction($l) === 'pending_contract');
            $declined = $group->filter(fn($l) => $currentAction($l) === 'declined');
            $manager  = $managerUsers->get($managerId);

            return [
                'manager_name'     => $manager ? $manager->name : 'Unknown',
                'pending_contract' => $pending->count(),
                'declined'         => $declined->count(),
                'total'            => $group->count(),
                'total_premium'    => (float) $pending->sum('monthly_premium'),
                'decline_rate'     => $group->count() > 0 ? round($declined->count() / $group->count() * 100) : 0,
            ];
        })->sortByDesc('total')->values()->toArray();

        $mgr_total_pending  = collect($manager_breakdown)->sum('pending_contract');
        $mgr_total_declined = collect($manager_breakdown)->sum('declined');
        $mgr_total_premium  = collect($manager_breakdown)->sum('total_premium');

        // ── Chargebacks ───────────────────────────────────────────
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd   = now()->subMonthNoOverflow()->endOfMonth();

        $cb_this_count = Lead::where('status', Statuses::LEAD_CHARGEBACK)
            ->where('updated_at', '>=', $thisMonthStart)->count();
        $cb_this_amt   = Lead::where('status', Statuses::LEAD_CHARGEBACK)
            ->where('updated_at', '>=', $thisMonthStart)->sum('monthly_premium') ?? 0;

        $cb_last_count = Lead::where('status', Statuses::LEAD_CHARGEBACK)
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])->count();
        $cb_last_amt   = Lead::where('status', Statuses::LEAD_CHARGEBACK)
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])->sum('monthly_premium') ?? 0;

        // ── Retention ─────────────────────────────────────────────
        $retention_cb       = Lead::where('status', Statuses::LEAD_CHARGEBACK)->count();
        $retention_retained = Lead::where('retention_status', Statuses::RETENTION_RETAINED)->count();
        $retention_pending  = Lead::where('status', Statuses::LEAD_CHARGEBACK)
            ->where(fn($q) => $q->whereNull('retention_status')
                ->orWhere('retention_status', Statuses::RETENTION_PENDING))
            ->count();

        return view('index', compact(
            'today_sales',
            'today_revenue',
            'today_approved',
            'today_declined',
            'today_est_commission',
            'mtd_sales',
            'total_revenue',
            'daily_avg_premium',
            'est_commission',
            'distinct_sale_days',
            'submitted_count',
            'sub_declined_count',
            'revenue_period_label',
            'selected_period',
            'revenue_by_carrier',
            'revenue_total_submissions',
            'done_count',
            'approved_count',
            'declined_count',
            'attendance',
            'present_count',
            'absent_count',
            'total_attendance_count',
            'sales_per_closer',
            'peregrine_count',
            'ravens_count',
            'manager_breakdown',
            'mgr_total_pending',
            'mgr_total_declined',
            'mgr_total_premium',
            'cb_this_count',
            'cb_this_amt',
            'cb_last_count',
            'cb_last_amt',
            'retention_cb',
            'retention_retained',
            'retention_pending'
        ));
    }

    /**
     * Get KPI data for live dashboard updates (API endpoint)
     * Returns fresh KPI data without page layout
     */
    public function getKpiData(Request $request)
    {
        $today = today();

        // ── Period: accept ?period=YYYY-MM ───────────────────────
        $periodParam = $request->get('period');
        if ($periodParam && preg_match('/^\d{4}-\d{2}$/', $periodParam)) {
            // period='2026-04' means the period STARTING on Apr 3 → anchor = Apr 3
            $anchor = \Carbon\Carbon::createFromFormat('Y-m', $periodParam)->setDay(3);
        } else {
            $anchor = $today->copy();
        }
        if ($anchor->day >= 3) {
            $rev_start = $anchor->copy()->setDay(3)->startOfDay();
            $rev_end   = $anchor->copy()->addMonthNoOverflow()->setDay(3)->endOfDay();
        } else {
            $rev_start = $anchor->copy()->subMonthNoOverflow()->setDay(3)->startOfDay();
            $rev_end   = $anchor->copy()->setDay(3)->endOfDay();
        }

        $revLeads = Lead::whereNotNull('pending_contract_at')
            ->whereDate('pending_contract_at', '>=', $rev_start)
            ->whereDate('pending_contract_at', '<=', $rev_end)
            ->get(['id', 'monthly_premium', 'closer_name', 'submission_status', 'pending_contract_at',
                   'carrier_name', 'assigned_partner',
                   'partner_id', 'insurance_carrier_id', 'state', 'settlement_type', 'policy_type']);

        $done_count    = $revLeads->count();
        $total_revenue = $revLeads->sum('monthly_premium');

        // Daily avg premium
        $distinct_sale_days = max(1, $revLeads->groupBy(
            fn($l) => \Carbon\Carbon::parse($l->pending_contract_at)->toDateString()
        )->count());
        $daily_avg_premium = round($total_revenue / $distinct_sale_days, 0);

        // Est. commission — same as Submission Performance report (premium × 9 × commission%)
        $est_commission = 0;
        foreach ($revLeads as $lead) { $est_commission += $this->calcLeadRevenue($lead); }
        $est_commission = round($est_commission, 0);

        // Submitted / Approved / Declined — matching Pending Submission page exactly
        $subBase = Lead::where(function ($q) {
                $q->where('ravens_validation_status', 'valid')
                  ->orWhereNotNull('validated_at');
            })
            ->whereNotNull('closer_name')
            ->whereNotNull('cn_name')->where('cn_name', '!=', '')
            ->where(function ($q) { $q->whereNotNull('sale_at')->orWhereNotNull('sale_date'); })
            ->where(function ($q) {
                $q->where(function ($s) { $s->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function ($s) { $s->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function ($s) { $s->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            })
            ->whereDate('sale_date', '>=', $rev_start)
            ->whereDate('sale_date', '<=', $rev_end);

        $submitted_count = (clone $subBase)->count();
        $approved_count  = (clone $subBase)->where('submission_status', Statuses::SUB_APPROVED)->count();
        $declined_count  = (clone $subBase)->whereNull('pending_contract_at')
                             ->where('submission_status', Statuses::SUB_DECLINED)->count();

        // Today metrics (filter from revLeads — Pending Contract page)
        $todayRevLeads  = $revLeads->filter(fn($l) =>
            $l->pending_contract_at && \Carbon\Carbon::parse($l->pending_contract_at)->isSameDay($today)
        );
        $today_sales         = $todayRevLeads->count();
        $today_revenue       = $todayRevLeads->sum('monthly_premium');
        $today_approved      = $todayRevLeads->where('submission_status', Statuses::SUB_APPROVED)->count();
        $today_declined      = $todayRevLeads->where('submission_status', Statuses::SUB_DECLINED)->count();
        $today_est_commission = 0;
        foreach ($todayRevLeads as $lead) { $today_est_commission += $this->calcLeadRevenue($lead); }
        $today_est_commission = round($today_est_commission, 0);

        // Revenue by carrier
        $rev_by_carrier = $revLeads->groupBy('carrier_name')
            ->map(fn($g) => [
                'carrier' => $g->first()->carrier_name ?? 'Unknown',
                'count'   => $g->count(),
                'premium' => $g->sum('monthly_premium'),
            ])
            ->sortByDesc('premium')->values()->take(5)->toArray();

        // Attendance
        $todayAttendances = \App\Models\Attendance::with('user')
            ->whereDate('date', $today->toDateString())
            ->get();

        $attendance = $todayAttendances->map(fn($att) => [
            'name'   => optional($att->user)->name ?? '—',
            'status' => $att->status,
        ])->toArray();

        $present_count          = $todayAttendances->whereIn('status', [Statuses::ATTENDANCE_PRESENT, Statuses::ATTENDANCE_LATE])->count();
        $total_attendance_count = $todayAttendances->count();

        // Sales per closer (uses revLeads = 3rd→3rd period)
        $closerGroupsKpi = $revLeads->filter(fn($l) => !empty($l->closer_name))->groupBy('closer_name');

        $sales_per_closer = [];
        foreach ($closerGroupsKpi as $closerName => $sales) {
            $todaySalesKpi = $sales->filter(fn($s) =>
                $s->pending_contract_at && \Carbon\Carbon::parse($s->pending_contract_at)->isSameDay($today)
            )->count();
            $user = User::where('name', $closerName)->first();
            $team = ($user && $user->hasRole([Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR]))
                ? Teams::PEREGRINE : Teams::RAVENS;

            $sales_per_closer[] = [
                'closer'   => $closerName,
                'today'    => $todaySalesKpi,
                'mtd'      => $sales->count(),
                'approved' => $sales->where('submission_status', Statuses::SUB_APPROVED)->count(),
                'declined' => $sales->where('submission_status', Statuses::SUB_DECLINED)->count(),
                'uw'       => 0,
                'team'     => $team,
            ];
        }
        usort($sales_per_closer, fn($a, $b) => $b['mtd'] - $a['mtd']);

        // Manager breakdown today
        $managerTodayLeads = Lead::where(fn($q) => $q->whereDate('pending_contract_at', $today)
                ->orWhereDate('declined_at', $today))
            ->whereNotNull('submission_by')
            ->select('id', 'submission_by', 'pending_contract_at', 'declined_at', 'monthly_premium')
            ->get();

        $currentAction = function ($lead) {
            if ($lead->pending_contract_at && $lead->declined_at) {
                return $lead->pending_contract_at > $lead->declined_at ? 'pending_contract' : 'declined';
            }
            return $lead->pending_contract_at ? 'pending_contract' : 'declined';
        };

        $managerGroups = $managerTodayLeads->groupBy('submission_by');
        $managerIds    = $managerGroups->keys()->filter()->toArray();
        $managerUsers  = User::withTrashed()->whereIn('id', $managerIds)->get()->keyBy('id');

        $manager_breakdown = $managerGroups->map(function ($group, $managerId) use ($managerUsers, $currentAction) {
            $pending  = $group->filter(fn($l) => $currentAction($l) === 'pending_contract');
            $declined = $group->filter(fn($l) => $currentAction($l) === 'declined');
            $manager  = $managerUsers->get($managerId);
            return [
                'manager_name'     => $manager ? $manager->name : 'Unknown',
                'pending_contract' => $pending->count(),
                'declined'         => $declined->count(),
                'total'            => $group->count(),
                'total_premium'    => (float) $pending->sum('monthly_premium'),
                'decline_rate'     => $group->count() > 0 ? round($declined->count() / $group->count() * 100) : 0,
            ];
        })->sortByDesc('total')->values()->toArray();

        // Chargebacks
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd   = now()->subMonthNoOverflow()->endOfMonth();

        $cb_this_count = Lead::where('status', Statuses::LEAD_CHARGEBACK)->where('updated_at', '>=', $thisMonthStart)->count();
        $cb_this_amt   = Lead::where('status', Statuses::LEAD_CHARGEBACK)->where('updated_at', '>=', $thisMonthStart)->sum('monthly_premium') ?? 0;
        $cb_last_count = Lead::where('status', Statuses::LEAD_CHARGEBACK)->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])->count();
        $cb_last_amt   = Lead::where('status', Statuses::LEAD_CHARGEBACK)->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])->sum('monthly_premium') ?? 0;

        // Retention
        $retention_cb       = Lead::where('status', Statuses::LEAD_CHARGEBACK)->count();
        $retention_retained = Lead::where('retention_status', Statuses::RETENTION_RETAINED)->count();
        $retention_pending  = Lead::where('status', Statuses::LEAD_CHARGEBACK)
            ->where(fn($q) => $q->whereNull('retention_status')->orWhere('retention_status', Statuses::RETENTION_PENDING))
            ->count();

        return response()->json([
            'success'            => true,
            'timestamp'          => now()->toIso8601String(),
            'revPeriodLabel'     => $rev_start->format('M j') . ' → ' . $rev_end->format('M j'),
            'done'               => $done_count,
            'submitted'          => $submitted_count,
            'approved'           => $approved_count,
            'declined'           => $declined_count,
            'totalRevenue'       => $total_revenue,
            'dailyAvgPremium'    => $daily_avg_premium,
            'estCommission'      => $est_commission,
            'distinctSaleDays'   => $distinct_sale_days,
            'todaySales'         => $today_sales,
            'todayRevenue'       => $today_revenue,
            'todayApproved'      => $today_approved,
            'todayDeclined'      => $today_declined,
            'todayEstCommission' => $today_est_commission,
            'revByCarrier'       => $rev_by_carrier,
            'attendance'         => $attendance,
            'presentCount'       => $present_count,
            'totalAttendance'    => $total_attendance_count,
            'salesPerCloser'     => $sales_per_closer,
            'managerBreakdown'   => $manager_breakdown,
        ]);
    }

    /**
     * Revenue analytics page
     */
    public function revenue(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $revenueService = new RevenueCalculationService();
        
        // Get monthly revenue details
        $monthlyRevenue = $revenueService->calculateMonthlyRevenue($year, $month);
        
        // Get year-to-date revenue
        $ytdRevenue = $revenueService->calculateYearToDateRevenue($year);
        
        // Get top performers
        $topPerformers = $revenueService->getTopPerformers($year, $month, 10);

        return view('admin.revenue.index', compact('monthlyRevenue', 'ytdRevenue', 'topPerformers', 'year', 'month'));
    }

    /**
     * Chill Party API — all active Ravens Closers with their sale status for today.
     * Those without a sale today are the "chill party".
     * Used by the topbar Chill Party widget (always visible).
     */
    public function freeloaders()
    {
        $allClosersUsers = User::role([Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER])
            ->where('status', '!=', Statuses::USER_INACTIVE)
            ->get(['id', 'name', 'email']);

        $salesCountByName = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_date')
            ->whereDate('sale_date', today())
            ->whereIn('closer_name', $allClosersUsers->pluck('name'))
            ->selectRaw('closer_name, COUNT(*) as sale_count')
            ->groupBy('closer_name')
            ->pluck('sale_count', 'closer_name');

        $closersWithSaleToday = $salesCountByName->keys();

        // Fetch passport photos (matched by email → employees table)
        // Only include photo when show_strip_photo = true
        $empRows = Employee::whereIn('email', $allClosersUsers->pluck('email'))
            ->get(['email', 'passport_image', 'show_strip_photo']);
        $photosByEmail = $empRows->mapWithKeys(fn($e) => [
            $e->email => ($e->show_strip_photo && $e->passport_image) ? $e->passport_image : null,
        ]);

        $freeloaderNames = $allClosersUsers
            ->filter(fn($u) => !$closersWithSaleToday->contains($u->name))
            ->pluck('name')
            ->values();

        // Return all closers: no-sale first, then sold — each with hasSale flag + saleCount
        $allClosers = $allClosersUsers
            ->sortBy(fn($u) => $closersWithSaleToday->contains($u->name) ? 1 : 0) // no-sale first
            ->map(fn($u) => [
                'name'      => $u->name,
                'photo'     => $photosByEmail->get($u->email)
                                    ? asset('storage/' . $photosByEmail->get($u->email))
                                    : null,
                'hasSale'   => $closersWithSaleToday->contains($u->name),
                'saleCount' => (int) ($salesCountByName->get($u->name) ?? 0),
            ])
            ->values();

        return response()->json([
            'freeloaders' => $freeloaderNames,          // kept for motivational popup JS
            'allClosers'  => $allClosers,
            'chillParty'  => $allClosers->filter(fn($c) => !$c['hasSale'])->values(), // compat
            'count'       => $freeloaderNames->count(),
            'total'       => $allClosersUsers->count(),
        ]);
    }
}
