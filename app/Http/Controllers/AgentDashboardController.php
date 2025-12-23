<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgentDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Agent');
    }

    /**
     * Show the agent dashboard.
     */
    public function index()
    {
        $agent = Auth::user();
        
        // Get agent's assigned leads/sales
        $assignedLeads = Lead::where(function($query) use ($agent) {
            $query->where('assigned_to', $agent->id)
                  ->orWhere('agent_id', $agent->id)
                  ->orWhere('created_by', $agent->id);
        })
        ->with(['status', 'assignedTo'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Statistics
        $stats = [
            'total_leads' => $assignedLeads->count(),
            'today_leads' => $assignedLeads->filter(function($lead) {
                return $lead->created_at->isToday();
            })->count(),
            'this_month_leads' => $assignedLeads->filter(function($lead) {
                return $lead->created_at->isCurrentMonth();
            })->count(),
            'pending' => $assignedLeads->where('status', 'pending')->count(),
            'contacted' => $assignedLeads->whereIn('status', ['contacted', 'in_progress'])->count(),
            'sold' => $assignedLeads->where('status', 'sold')->count(),
            'closed' => $assignedLeads->where('status', 'closed')->count(),
        ];

        // Monthly revenue calculation
        $monthlyRevenue = Lead::where(function($query) use ($agent) {
            $query->where('assigned_to', $agent->id)
                  ->orWhere('agent_id', $agent->id);
        })
        ->where('status', 'sold')
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->sum('monthly_premium');

        // Recent activity - last 10 leads
        $recentLeads = $assignedLeads->take(10);

        // Get agent's carrier commissions
        $carrierCommissions = \App\Models\AgentCarrierCommission::where('user_id', $agent->id)
            ->with('insuranceCarrier')
            ->get();

        // Sales by status breakdown
        $salesByStatus = [
            'new' => $assignedLeads->where('status', 'new')->count(),
            'contacted' => $assignedLeads->where('status', 'contacted')->count(),
            'qualified' => $assignedLeads->where('status', 'qualified')->count(),
            'proposal' => $assignedLeads->where('status', 'proposal')->count(),
            'negotiation' => $assignedLeads->where('status', 'negotiation')->count(),
            'sold' => $assignedLeads->where('status', 'sold')->count(),
            'lost' => $assignedLeads->where('status', 'lost')->count(),
        ];

        return view('agent.dashboard', compact(
            'agent',
            'stats',
            'monthlyRevenue',
            'recentLeads',
            'carrierCommissions',
            'salesByStatus'
        ));
    }
}
