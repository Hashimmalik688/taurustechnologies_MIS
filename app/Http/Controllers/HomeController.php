<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\InsuranceCarrier;
use App\Services\CommissionCalculationService;
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
     * Executive Dashboard with live data from local database
     */
    public function root()
    {
        // Fetch data from boss's dashboard with caching (for attendance)
        $bossData = $this->fetchBossDashboardData();
        
        // Get current month start and today
        $monthStart = now()->startOfMonth();
        $today = today();
        
        // Extract data from local database
        $users_count = User::count();
        
        // Sales counts using manager_status field (submitted sales with closer_name)
        $total_sales_today = Lead::whereNotNull('closer_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->whereDate('sale_at', $today)
            ->count();
            
        // Month-to-date sales (all submitted)
        $total_monthly_sales = Lead::whereNotNull('closer_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();
        
        // Calculate revenue from issued and verified sales (Revenue Analytics logic)
        // Revenue = Total partner commissions (Premium × 9 × Settlement %)
        $issued_sales = Lead::where('status', 'accepted')
            ->where('manager_status', 'approved')
            ->where('issuance_status', 'Issued')
            ->get();
            
        // Revenue is the sum of all partner commissions
        $total_revenue = $issued_sales->sum('agent_revenue');
        
        // Sales status counts by manager_status (MTD)
        $done_count = $total_monthly_sales; // Total submitted MTD
        $approved_count = Lead::whereNotNull('closer_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->where('manager_status', 'approved')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();
            
        $underwriting_count = Lead::whereNotNull('closer_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->where('manager_status', 'underwriting')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();
            
        $declined_count = Lead::whereNotNull('closer_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->where('manager_status', 'declined')
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->count();
        
        // Attendance from boss's dashboard (fallback to local if unavailable)
        $attendance = $bossData['attendance'] ?? [];
        if (empty($attendance)) {
            // Fallback: get from local database
            $attendanceRecords = Attendance::whereDate('date', $today)
                ->with('user:id,name')
                ->get();
            $attendance = $attendanceRecords->map(function($att) {
                return [
                    'name' => $att->user->name ?? 'Unknown',
                    'status' => $att->status ?? 'absent'
                ];
            })->toArray();
        }
        $present_count = $this->countPresent($attendance);
        $absent_count = $this->countAbsent($attendance);
        
        // Sales per closer - Calculate from local database
        $closers = Lead::whereNotNull('closer_name')
            ->where(function($q) {
                $q->whereNotNull('sale_at')
                  ->orWhereNotNull('sale_date');
            })
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->get()
            ->groupBy('closer_name');
            
        $sales_per_closer = [];
        foreach ($closers as $closerName => $sales) {
            $todaySales = $sales->filter(function($sale) use ($today) {
                return $sale->sale_at && $sale->sale_at->isSameDay($today);
            })->count();
            
            $sales_per_closer[] = [
                'closer' => $closerName,
                'today' => $todaySales,
                'mtd' => $sales->count(),
                'approvedMTD' => $sales->where('manager_status', 'approved')->count(),
                'declinedMTD' => $sales->where('manager_status', 'declined')->count(),
                'uwMTD' => $sales->where('manager_status', 'underwriting')->count(),
                'team' => $sales->first()->team ?? 'ravens'
            ];
        }
        
        // Sort by MTD sales descending
        usort($sales_per_closer, function($a, $b) {
            return $b['mtd'] - $a['mtd'];
        });
        
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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = User::find($id);
        
        if (!$user) {
            Session::flash('message', 'User not found!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json([
                'isSuccess' => false,
                'Message' => 'User not found!',
            ], 404);
        }

        // Update name if provided
        if ($request->filled('name')) {
            $user->name = trim($request->name);
        }

        // Update email if provided
        if ($request->filled('email')) {
            $user->email = strtolower(trim($request->email));
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            
            // Create directory if it doesn't exist
            if (!is_dir($avatarPath)) {
                mkdir($avatarPath, 0755, true);
            }
            
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = '/images/' . $avatarName;
        }

        $user->save();

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