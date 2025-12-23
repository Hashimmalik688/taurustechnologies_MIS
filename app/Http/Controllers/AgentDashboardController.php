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
        
        // Calculate statistics
        $stats = [
            'total_sales' => $allLeads->count(),
            'approved' => $allLeads->where('status', 'accepted')->count(),
            'declined' => $allLeads->where('status', 'rejected')->count(),
            'revenue' => $allLeads->where('status', 'accepted')->sum('monthly_premium'),
            'company_share' => $allLeads->where('status', 'accepted')->sum('monthly_premium') * 0.3, // 30% company share
        ];
        
        // Get paginated leads for table
        $leads = Lead::where('closer_name', $agent->name)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('agent.dashboard', compact('agent', 'stats', 'leads'));
    }
}
