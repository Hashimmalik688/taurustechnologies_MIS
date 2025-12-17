<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Boss dashboard endpoint
     */
    private $bossEndpoint = 'https://dashboard.taurustechnologies.co/dashboard-metrics.php';
    
    /**
     * Cache duration in seconds (5 minutes)
     */
    private $cacheDuration = 300;
    
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
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
        // Fetch data from boss's dashboard with caching
        $bossData = $this->fetchBossDashboardData();
        
        // Extract data from boss's dashboard
        $users_count = User::count(); // Local database count
        $total_sales_today = $bossData['totalSalesToday'] ?? 0;
        $total_monthly_sales = $bossData['done'] ?? $bossData['TOTAL'] ?? 0;
        $total_revenue = $bossData['totalRevenue'] ?? $bossData['total_revenue'] ?? 0;
        
        // Sales status
        $done_count = $bossData['done'] ?? $bossData['TOTAL'] ?? 0;
        $approved_count = $bossData['approved'] ?? $bossData['APPROVED'] ?? 0;
        $underwriting_count = $bossData['underwriting'] ?? $bossData['UW'] ?? 0;
        $declined_count = $bossData['declined'] ?? $bossData['DECLINED'] ?? 0;
        
        // Attendance
        $attendance = $bossData['attendance'] ?? [];
        $present_count = $this->countPresent($attendance);
        $absent_count = $this->countAbsent($attendance);
        
        // Sales per closer
        $sales_per_closer = $bossData['salesPerCloser'] ?? [];
        
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
        // Retained = leads that were sales (accepted/underwritten) in the last 30 days and NOT chargedback
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
        
        // Pending leads from local database
        $pending_leads_count = Lead::where('status', 'pending')->count();
        $pending_leads = Lead::where('status', 'pending')->latest()->take(10)->get();
        
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

    /**
     * Language Translation
     */
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        }
        return redirect()->back();
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = '/images/' . $avatarName;
        }

        $user->update();

        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => 'User Details Updated successfully!',
            ], 200);
        }

        Session::flash('message', 'Something went wrong!');
        Session::flash('alert-class', 'alert-danger');
        return response()->json([
            'isSuccess' => false,
            'Message' => 'Something went wrong!',
        ], 200);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => 'Your Current password does not match. Please try again.',
            ], 200);
        }

        $user = User::find($id);
        $user->password = Hash::make($request->get('password'));
        $user->update();

        if ($user) {
            Session::flash('message', 'Password updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => 'Password updated successfully!',
            ], 200);
        }

        Session::flash('message', 'Something went wrong!');
        Session::flash('alert-class', 'alert-danger');
        return response()->json([
            'isSuccess' => false,
            'Message' => 'Something went wrong!',
        ], 200);
    }
}