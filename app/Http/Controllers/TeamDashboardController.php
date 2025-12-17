<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Paraguins Team Dashboard
     */
    public function paraguinsTeam()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        // Get paraguins team members
        $paraguinsTeam = User::where('department', 'paraguins')
            ->orWhereHas('roles', function($q) {
                $q->whereIn('name', ['Verifier', 'Live Closer', 'Verification Officer']);
            })
            ->get();

        $stats = [
            'month_data' => Lead::whereMonth('created_at', Carbon::now()->month)
                                ->where('team', 'paraguins')
                                ->count(),
            'total_calls' => DB::table('call_logs')
                               ->whereIn('agent_id', $paraguinsTeam->pluck('id'))
                               ->whereDate('call_start_time', '>=', $startOfMonth)
                               ->count(),
            'closed_sales' => Lead::where('team', 'paraguins')
                                  ->where('status', 'sale')
                                  ->whereMonth('created_at', Carbon::now()->month)
                                  ->count(),
            'total_transfers' => Lead::where('team', 'paraguins')
                                     ->where('status', 'transferred')
                                     ->whereMonth('created_at', Carbon::now()->month)
                                     ->count(),
        ];

        // Sales per closer
        $salesPerCloser = User::whereIn('id', $paraguinsTeam->pluck('id'))
            ->withCount([
                'leadsManaged as total_sales' => function ($query) use ($startOfMonth) {
                    $query->where('created_at', '>=', $startOfMonth)
                          ->where('team', 'paraguins');
                },
                'leadsManaged as sales_today' => function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                          ->where('team', 'paraguins');
                }
            ])
            ->get();

        return view('team-dashboards.paraguins', compact('stats', 'salesPerCloser'));
    }

    /**
     * Ravens Team Dashboard
     */
    public function ravensTeam()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        $ravensTeam = User::where('department', 'ravens')
            ->orWhereHas('roles', function($q) {
                $q->whereIn('name', ['Employee', 'Agent']);
            })
            ->get();

        $stats = [
            'sales_mtd' => Lead::where('team', 'ravens')
                               ->where('status', 'accepted')
                               ->where('created_at', '>=', $startOfMonth)
                               ->count(),
            'sales_today' => Lead::where('team', 'ravens')
                                  ->where('status', 'accepted')
                                  ->whereDate('created_at', $today)
                                  ->count(),
            'chargebacks' => DB::table('ledger_entries')
                               ->where('category', 'chargeback')
                               ->whereMonth('transaction_date', Carbon::now()->month)
                               ->count(),
            'approved' => Lead::where('team', 'ravens')
                              ->where('status', 'accepted')
                              ->whereMonth('created_at', Carbon::now()->month)
                              ->count(),
            'declined' => Lead::where('team', 'ravens')
                              ->where('status', 'rejected')
                              ->whereMonth('created_at', Carbon::now()->month)
                              ->count(),
            'underwriting' => Lead::where('team', 'ravens')
                                   ->where('status', 'underwritten')
                                   ->whereMonth('created_at', Carbon::now()->month)
                                   ->count(),
        ];

        // Sales per closer
        $salesPerCloser = User::whereIn('id', $ravensTeam->pluck('id'))
            ->withCount([
                'leadsManaged as total_sales' => function ($query) use ($startOfMonth) {
                    $query->where('created_at', '>=', $startOfMonth);
                },
                'leadsManaged as sales_today' => function ($query) use ($today) {
                    $query->whereDate('created_at', $today);
                }
            ])
            ->get();

        return view('team-dashboards.ravens', compact('stats', 'salesPerCloser'));
    }

    /**
     * Detailed Performance View for a specific closer
     */
    public function closerDetails(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Date range from request or default to current month
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Performance metrics
        $metrics = [
            'total_leads' => $user->leadsManaged()
                                  ->whereBetween('created_at', [$start, $end])
                                  ->count(),
            'approved' => $user->leadsManaged()
                               ->where('status', 'accepted')
                               ->whereBetween('created_at', [$start, $end])
                               ->count(),
            'declined' => $user->leadsManaged()
                               ->where('status', 'rejected')
                               ->whereBetween('created_at', [$start, $end])
                               ->count(),
            'underwriting' => $user->leadsManaged()
                                   ->where('status', 'underwritten')
                                   ->whereBetween('created_at', [$start, $end])
                                   ->count(),
            'pending' => $user->leadsManaged()
                              ->where('status', 'pending')
                              ->whereBetween('created_at', [$start, $end])
                              ->count(),
        ];

        // Daily breakdown
        $dailyStats = $user->leadsManaged()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as declined')
            )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Call activity
        $callStats = DB::table('call_logs')
            ->where('agent_id', $userId)
            ->whereBetween('call_start_time', [$start, $end])
            ->selectRaw('
                COUNT(*) as total_calls,
                SUM(duration_seconds) as total_duration,
                AVG(duration_seconds) as avg_duration
            ')
            ->first();

        return view('team-dashboards.closer-details', compact('user', 'metrics', 'dailyStats', 'callStats', 'startDate', 'endDate'));
    }
}
