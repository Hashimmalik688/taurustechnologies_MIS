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
        
        // Simple stats
        $stats = [
            'total_leads' => 0,
            'today_leads' => 0,
            'pending' => 0,
            'closed' => 0,
        ];

        return view('agent.dashboard', compact('agent', 'stats'));
    }
}
