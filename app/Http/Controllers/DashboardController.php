<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Services\RevenueCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Boss dashboard endpoint
     */
    private $bossEndpoint = 'https://backend.taurustechnologies.co/webhook/dashboard-metrics';

    /**
     * Cache duration in seconds (5 minutes)
     */
    private $cacheDuration = 300;

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
     * Executive Dashboard with live data from boss's dashboard
     */
    public function root()
    {
        // Redirect Agents to their own dashboard
        if (Auth::check() && Auth::user()->hasRole('Agent')) {
            return redirect()->route('agent.dashboard');
        }

        // Redirect Verifiers to their dashboard
        if (Auth::check() && Auth::user()->hasRole('Verifier')) {
            return redirect()->route('verifier.dashboard');
        }
        
        // Redirect Peregrine Closers to their dashboard
        if (Auth::check() && Auth::user()->hasRole('Peregrine Closer')) {
            return redirect()->route('peregrine.closers.index');
        }

        // Redirect Peregrine Validators to their dashboard
        if (Auth::check() && Auth::user()->hasRole('Peregrine Validator')) {
            return redirect()->route('validator.index');
        }
        
        // Redirect employees to attendance dashboard (they only have access to attendance + chat)
        if (Auth::check() && Auth::user()->hasRole('Employee')) {
            return redirect()->route('attendance.dashboard');
        }
        
        // Redirect Ravens Closer to their dashboard
        if (Auth::check() && Auth::user()->hasRole('Ravens Closer')) {
            return redirect()->route('ravens.dashboard');
        }

        // Redirect QA to QA Review page
        if (Auth::check() && Auth::user()->hasRole('QA')) {
            return redirect()->route('qa.review');
        }

        // Redirect Retention Officer to retention page
        if (Auth::check() && Auth::user()->hasRole('Retention Officer')) {
            return redirect()->route('retention.dashboard');
        }

        // Redirect HR to attendance (HR has view-only access to Attendance and E.M.S)
        if (Auth::check() && Auth::user()->hasRole('HR')) {
            return redirect()->route('attendance.index');
        }

        // Redirect Trainer to attendance dashboard
        if (Auth::check() && Auth::user()->hasRole('Trainer')) {
            return redirect()->route('attendance.dashboard');
        }

        // Fetch data from boss's dashboard with caching (for attendance only)
        $bossData = $this->fetchBossDashboardData();

        // Extract data from local database using proper fields
        $users_count = Cache::remember('dashboard_users_count', 300, function () {
            return User::count();
        });
        
        // Get current month start and today
        $monthStart = now()->startOfMonth();
        $today = today();
        
        // Sales counts using sale_date field (from sales management page)
        $total_sales_today = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_date')
            ->whereDate('sale_date', $today)
            ->count();
            
        // Month-to-date sales (all submitted)
        $total_monthly_sales = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_date')
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->count();
        
        // Calculate revenue from issued and verified sales (Revenue Analytics logic)
        // Use agent_revenue (calculated commission) with fallback to monthly_premium
        $issued_sales = Lead::where('status', 'accepted')
            ->where('manager_status', 'approved')
            ->where('issuance_status', 'Issued')
            ->get();
            
        $total_revenue = $issued_sales->sum(function($lead) {
            return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
        });
        
        // Sales status counts by manager_status (MTD) using sale_date
        $done_count = $total_monthly_sales; // Total submitted MTD
        $approved_count = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_date')
            ->where('manager_status', 'approved')
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->count();
            
        $underwriting_count = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_date')
            ->where('manager_status', 'underwriting')
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->count();
            
        $declined_count = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_date')
            ->where('manager_status', 'declined')
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->count();

        // Attendance - Use local database instead of external API
        // Use USA Eastern Time for consistency
        $usaToday = now()->setTimezone('America/New_York')->toDateString();
        $trackableRoles = ['Employee', 'Peregrine Closer', 'Peregrine Validator', 'Verifier', 'Trainer', 'Ravens Closer'];
        $todayAttendances = \App\Models\Attendance::with('user')
            ->whereDate('date', $usaToday)
            ->get();
        
        $attendance = $todayAttendances->map(function($att) {
            return [
                'name' => $att->user->name,
                'status' => $att->status,
            ];
        })->toArray();
        
        $present_count = $todayAttendances->whereIn('status', ['present', 'late'])->count();
        $absent_count = $todayAttendances->where('status', 'absent')->count();

        // Sales per closer - Calculate from local database using manager_status and sale_date
        $closers = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_date')
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->get()
            ->groupBy('closer_name');
            
        $sales_per_closer = [];
        foreach ($closers as $closerName => $sales) {
            $todaySales = $sales->filter(function($sale) use ($today) {
                return $sale->sale_date && $sale->sale_date->isSameDay($today);
            })->count();
            
            // Get user to determine team
            $user = User::where('name', $closerName)->first();
            $team = 'ravens'; // default
            if ($user) {
                if ($user->hasRole(['Peregrine Closer', 'Peregrine Validator'])) {
                    $team = 'peregrine';
                }
            }
            
            $sales_per_closer[] = [
                'closer' => $closerName,
                'today' => $todaySales,
                'mtd' => $sales->count(),
                'approvedMTD' => $sales->where('manager_status', 'approved')->count(),
                'declinedMTD' => $sales->where('manager_status', 'declined')->count(),
                'uwMTD' => $sales->where('manager_status', 'underwriting')->count(),
                'team' => $team
            ];
        }
        
        // Sort by MTD sales descending
        usort($sales_per_closer, function($a, $b) {
            return $b['mtd'] - $a['mtd'];
        });
        
        // Get team counts from users with roles
        $peregrine_count = User::role(['Peregrine Closer', 'Peregrine Validator'])
            ->where('status', '!=', 'inactive')
            ->count();
        $ravens_count = User::role('Ravens Closer')
            ->where('status', '!=', 'inactive')
            ->count();

        // Chargebacks - Calculate from local database
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        $cb_this_count = Lead::where('status', 'chargeback')
            ->where('updated_at', '>=', $thisMonthStart)
            ->count();
        $cb_this_amt = Lead::where('status', 'chargeback')
            ->where('updated_at', '>=', $thisMonthStart)
            ->sum('monthly_premium') ?? 0;
        
        $cb_last_count = Lead::where('status', 'chargeback')
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
        $cb_last_amt = Lead::where('status', 'chargeback')
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('monthly_premium') ?? 0;

        // Retention - Only count chargebacks (CB)
        // Retention metric tracks leads that were chargedback
        $retention_cb = Lead::where('status', 'chargeback')->count();
        $retention_retained = 0; // Not tracking retained separately
        $retention_pending = 0; // Not tracking pending separately

        // Financial overview
        $financial = $bossData['financialOverview'] ?? [];
        $financial_received = $financial['amountReceived'] ?? 0;
        $financial_pending = $financial['totalPending'] ?? 0;
        $financial_balance = $financial['amountReceived'] ?? 0;

        // Agent revenue
        $agent_revenue = $bossData['totalAgentRevenue']['combined'] ?? 0;
        $avg_agent_revenue = $bossData['totalAgentRevenue']['avgPerAgent'] ?? 0;

        // Per agent breakdown
        $per_agent = $bossData['perAgent'] ?? [];

        // Pending leads from local database with caching
        $pending_leads_count = Cache::remember('dashboard_pending_leads_count', 60, function () {
            return Lead::where('status', 'pending')->count();
        });
        $pending_leads = Cache::remember('dashboard_pending_leads', 60, function () {
            return Lead::where('status', 'pending')->latest()->take(10)->get();
        });

        return view('index', compact(
            'users_count',
            'total_sales_today',
            'total_monthly_sales',
            'total_revenue',
            'done_count',
            'approved_count',
            'underwriting_count',
            'declined_count',
            'attendance',
            'present_count',
            'absent_count',
            'sales_per_closer',
            'peregrine_count',
            'ravens_count',
            'cb_this_count',
            'cb_this_amt',
            'cb_last_count',
            'cb_last_amt',
            'retention_cb',
            'retention_retained',
            'retention_pending',
            'financial_received',
            'financial_pending',
            'financial_balance',
            'agent_revenue',
            'avg_agent_revenue',
            'per_agent',
            'pending_leads_count',
            'pending_leads'
        ));
    }

    /**
     * Get KPI data for live dashboard updates (API endpoint)
     * Returns fresh KPI data without page layout
     */
    public function getKpiData()
    {
        // Get current month and today
        $today = today();
        
        // Sales counts using manager_status field and sale_at date (same as root method)
        $total_sales_today = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereDate('sale_at', $today)
            ->count();
            
        // Month-to-date sales (all submitted)
        $total_monthly_sales = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();
        
        // Sales status counts by manager_status (MTD) - consistent with root method
        $done_count = $total_monthly_sales; // Total submitted MTD
        $approved_count = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('manager_status', 'approved')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();
            
        $underwriting_count = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('manager_status', 'underwriting')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();
            
        $declined_count = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('manager_status', 'declined')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();

        // Attendance - Use local database
        $usaToday = now()->setTimezone('America/New_York')->toDateString();
        $todayAttendances = \App\Models\Attendance::with('user')
            ->whereDate('date', $usaToday)
            ->get();
        
        $attendance = $todayAttendances->map(function($att) {
            return [
                'name' => $att->user->name,
                'status' => $att->status,
            ];
        })->toArray();

        // Sales per closer - Calculate from local database (same as root method)
        $closers = Lead::whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->get()
            ->groupBy('closer_name');
            
        $sales_per_closer = [];
        foreach ($closers as $closerName => $sales) {
            $todaySales = $sales->filter(function($sale) use ($today) {
                return $sale->sale_at && $sale->sale_at->isSameDay($today);
            })->count();
            
            // Get user to determine team
            $user = User::where('name', $closerName)->first();
            $team = 'ravens'; // default
            if ($user) {
                if ($user->hasRole(['Peregrine Closer', 'Peregrine Validator'])) {
                    $team = 'peregrine';
                }
            }
            
            // Count statuses
            $approvedSales = $sales->where('manager_status', 'approved')->count();
            $declinedSales = $sales->where('manager_status', 'declined')->count();
            $uwSales = $sales->where('manager_status', 'underwriting')->count();
            
            $sales_per_closer[] = [
                'closer' => $closerName,
                'today' => $todaySales,
                'mtd' => $sales->count(),
                'approved' => $approvedSales,
                'declined' => $declinedSales,
                'uw' => $uwSales,
                'team' => $team
            ];
        }
        
        // Sort by MTD sales descending
        usort($sales_per_closer, function($a, $b) {
            return $b['mtd'] - $a['mtd'];
        });

        // Chargebacks
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        $cb_this_count = Lead::where('status', 'chargeback')
            ->where('updated_at', '>=', $thisMonthStart)
            ->count();
        $cb_this_amt = Lead::where('status', 'chargeback')
            ->where('updated_at', '>=', $thisMonthStart)
            ->sum('monthly_premium') ?? 0;
        
        $cb_last_count = Lead::where('status', 'chargeback')
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
        $cb_last_amt = Lead::where('status', 'chargeback')
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('monthly_premium') ?? 0;

        // Retention
        $retention_cb = Lead::where('status', 'chargeback')->count();
        $retention_retained = Lead::whereIn('status', ['accepted', 'underwritten'])
            ->where('sale_at', '<', now()->subDays(30))
            ->count();
        // Only count 'accepted' (not yet underwritten) in pending retention
        $retention_pending = Lead::where('status', 'accepted')
            ->where('sale_at', '>=', now()->subDays(30))
            ->count();

        // Calculate revenue from issued and approved sales (same logic as root method)
        // Use agent_revenue (calculated commission) with fallback to monthly_premium
        $issued_sales = Lead::where('status', 'accepted')
            ->where('manager_status', 'approved')
            ->where('issuance_status', 'Issued')
            ->get();
            
        $total_revenue = $issued_sales->sum(function($lead) {
            return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
        });

        // Return as JSON for AJAX updates
        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'totalSalesToday' => $total_sales_today,
            'done' => $done_count,
            'approved' => $approved_count,
            'underwriting' => $underwriting_count,
            'declined' => $declined_count,
            'totalRevenue' => $total_revenue,
            'attendance' => $attendance,
            'salesPerCloser' => $sales_per_closer,
            'chargebacks' => [
                'thisMonth' => [
                    'count' => $cb_this_count,
                    'amount' => $cb_this_amt
                ],
                'lastMonth' => [
                    'count' => $cb_last_count,
                    'amount' => $cb_last_amt
                ]
            ],
            'retention' => [
                'cb' => $retention_cb,
                'retained' => $retention_retained,
                'pending' => $retention_pending
            ]
        ]);
    }

    /**
     * Fetch data from boss's dashboard endpoint with caching
     */
    private function fetchBossDashboardData()
    {
        try {
            return Cache::remember('boss_dashboard_data', $this->cacheDuration, function () {
                $response = Http::timeout(30)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->get($this->bossEndpoint);

                if ($response->successful()) {
                    return $response->json();
                }

                // Return empty array if request fails
                \Log::warning('Failed to fetch boss dashboard data', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);

                return [];
            });
        } catch (\Exception $e) {
            \Log::error('Boss dashboard fetch error', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Count present employees
     */
    private function countPresent($attendance)
    {
        if (!is_array($attendance)) return 0;

        return collect($attendance)->filter(function($att) {
            $status = strtolower($att['status'] ?? '');
            return in_array($status, ['present', 'p', 'on time', 'ontime', 'late', 'half day']);
        })->count();
    }

    /**
     * Count absent employees
     */
    private function countAbsent($attendance)
    {
        if (!is_array($attendance)) return 0;

        return collect($attendance)->filter(function($att) {
            $status = strtolower($att['status'] ?? '');
            return in_array($status, ['absent', 'a']);
        })->count();
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
}
