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

    public function index()
    {
        $agent = Auth::user();
        
        // Get leads assigned to this agent (where closer_name matches agent's name)
        $allLeads = Lead::where('closer_name', $agent->name)->get();
        
        // Get issued leads assigned to this agent
        $issuedLeads = Lead::where('assigned_agent_id', $agent->id)
            ->where('issuance_status', 'Issued')
            ->with(['insuranceCarrier', 'assignedAgent'])
            ->get();
        
        // Combine both for total sales count
        $combinedLeadsIds = $allLeads->pluck('id')->merge($issuedLeads->pluck('id'))->unique();
        $totalSalesCount = $combinedLeadsIds->count();
        
        // Calculate statistics - include both closed sales and issued apps
        $stats = [
            'total_sales' => $totalSalesCount,
            'approved' => $allLeads->where('status', 'accepted')->count() + $issuedLeads->where('manager_status', 'approved')->count(),
            'declined' => $allLeads->where('status', 'rejected')->count(),
            'revenue' => $allLeads->where('status', 'accepted')->sum('monthly_premium') + $issuedLeads->sum('monthly_premium'),
            'company_share' => ($allLeads->where('status', 'accepted')->sum('monthly_premium') + $issuedLeads->sum('monthly_premium')) * 0.3, // 30% company share
            'issued_count' => $issuedLeads->count(),
            'issued_revenue' => $issuedLeads->sum('monthly_premium'),
            'issued_coverage' => $issuedLeads->sum('coverage_amount'),
        ];
        
        // Get paginated leads for table - include both closer_name and assigned agent leads
        $leads = Lead::where(function($query) use ($agent) {
            $query->where('closer_name', $agent->name)
                  ->orWhere('assigned_agent_id', $agent->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(50);

        return view('agent.dashboard', compact('agent', 'stats', 'leads', 'issuedLeads'));
    }
}
