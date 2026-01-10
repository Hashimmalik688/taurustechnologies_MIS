<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
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
        
        // Redirect Paraguins Closers to their dashboard
        if (Auth::check() && Auth::user()->hasRole('Paraguins Closer')) {
            return redirect()->route('paraguins.closers.index');
        }

        // Redirect Paraguins Validators to their dashboard
        if (Auth::check() && Auth::user()->hasRole('Paraguins Validator')) {
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

        // Redirect HR to dock section (HR only has access to Dock, Attendance, and Public Holidays)
        if (Auth::check() && Auth::user()->hasRole('HR')) {
            return redirect()->route('dock.index');
        }

        // Redirect Trainer to attendance dashboard
        if (Auth::check() && Auth::user()->hasRole('Trainer')) {
            return redirect()->route('attendance.dashboard');
        }

        // Fetch data from boss's dashboard with caching
        $bossData = $this->fetchBossDashboardData();

        // Extract data from boss's dashboard with local caching
        $users_count = Cache::remember('dashboard_users_count', 300, function () {
            return User::count();
        });
        $total_sales_today = $bossData['totalSalesToday'] ?? 0;
        $total_monthly_sales = $bossData['done'] ?? $bossData['TOTAL'] ?? 0;
        $total_revenue = $bossData['totalRevenueMTD'] ?? $bossData['totalRevenue'] ?? $bossData['total_revenue'] ?? 0;

        // Sales status
        $done_count = $bossData['done'] ?? $bossData['TOTAL'] ?? 0;
        $approved_count = $bossData['approved'] ?? $bossData['APPROVED'] ?? 0;
        $underwriting_count = $bossData['underwriting'] ?? $bossData['UW'] ?? 0;
        $declined_count = $bossData['declined'] ?? $bossData['DECLINED'] ?? 0;

        // Attendance - Use local database instead of external API
        $trackableRoles = ['Employee', 'Paraguins Closer', 'Paraguins Validator', 'Verifier', 'Trainer', 'Ravens Closer'];
        $todayAttendances = \App\Models\Attendance::with('user')
            ->whereDate('date', today())
            ->get();
        
        $attendance = $todayAttendances->map(function($att) {
            return [
                'name' => $att->user->name,
                'status' => $att->status,
            ];
        })->toArray();
        
        $present_count = $todayAttendances->whereIn('status', ['present', 'late'])->count();
        $absent_count = $todayAttendances->where('status', 'absent')->count();

        // Sales per closer
        $sales_per_closer = $bossData['salesPerCloser'] ?? [];
        
        // Get team counts from users with roles
        $paraguins_count = User::role(['Paraguins Closer', 'Paraguins Validator'])
            ->where('status', '!=', 'inactive')
            ->count();
        $ravens_count = User::role('Ravens Closer')
            ->where('status', '!=', 'inactive')
            ->count();
        
        // Enhance sales_per_closer with team info from database
        $sales_per_closer = collect($sales_per_closer)->map(function($closer) {
            $user = User::where('name', $closer['closer'] ?? '')->first();
            if ($user) {
                if ($user->hasRole(['Paraguins Closer', 'Paraguins Validator'])) {
                    $closer['team'] = 'paraguins';
                } elseif ($user->hasRole('Ravens Closer')) {
                    $closer['team'] = 'ravens';
                }
            }
            return $closer;
        })->toArray();

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

        // Retention - Calculate from local database
        // CB = leads marked as chargeback status
        // Retained = leads that were sales (accepted/underwritten) more than 30 days old and NOT chargedback
        // Pending = leads that are accepted but less than 30 days old (pending retention period)
        $retention_cb = Lead::where('status', 'chargeback')->count();
        $retention_retained = Lead::whereIn('status', ['accepted', 'underwritten'])
            ->where('sale_at', '<', now()->subDays(30))
            ->count();
        $retention_pending = Lead::whereIn('status', ['accepted', 'underwritten'])
            ->where('sale_at', '>=', now()->subDays(30))
            ->count();

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
            'paraguins_count',
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
}
