<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VerifierController extends Controller
{
    public function create(string $team = 'peregrine')
    {
        $team = strtolower($team);
        if (!in_array($team, ['ravens', 'peregrine'])) {
            abort(404);
        }

        // Fetch Peregrine closers (by role or department)
        $closers = User::role('Peregrine Closer')
            ->orWhere('department', 'peregrine')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('verifier.create', [
            'closers' => $closers,
            'team' => $team,
        ]);
    }

    public function store(Request $request, string $team = 'peregrine')
    {
        $team = strtolower($team);
        if (!in_array($team, ['ravens', 'peregrine'])) {
            abort(404);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'cn_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'verifier_name' => ['required', 'string', 'max:255'],
            'closer_id' => ['required', 'exists:users,id'],
            'date_of_birth' => ['required', 'date'],
            'age' => ['required', 'integer', 'min:18', 'max:100'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'account_type' => ['required', 'in:Checking,Savings,Card'],
            'address' => ['required', 'string', 'max:500'],
            'state' => ['required', 'string', 'max:50'],
            'zip_code' => ['required', 'string', 'max:10'],
        ]);

        $closer = User::findOrFail($validated['closer_id']);

        // Create a minimal Lead record with allowed fields
        Lead::create([
            'date' => $validated['date'],
            'cn_name' => $validated['cn_name'],
            'phone_number' => $validated['phone_number'],
            'account_verified_by' => $validated['verifier_name'],
            'closer_name' => $closer->name,
            'managed_by' => $validated['closer_id'],
            'date_of_birth' => $validated['date_of_birth'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'account_type' => $validated['account_type'],
            'address' => $validated['address'],
            'state' => $validated['state'],
            'zip_code' => $validated['zip_code'],
            'status' => 'transferred',
            'team' => $team,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'transferred_at' => now(),
        ]);

        return redirect()->route('verifier.create', ['team' => $team])
            ->with('success', 'Verification submission saved and transferred to closer.');
    }

    public function dashboard(Request $request)
    {
        // Check if user explicitly chose a filter, otherwise default to 'all'
        $filter = $request->has('filter') ? $request->get('filter') : 'all';
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Get date range based on office hours (7am-5pm MT)
        [$startDate, $endDate] = $this->getDateRange($filter, $customStart, $customEnd);

        // Get all leads submitted by this verifier
        $leadsQuery = Lead::where(function($query) {
                $query->where('verified_by', auth()->id())
                      ->orWhere('account_verified_by', auth()->user()->name);
            });
        
        // Apply date filter only if a specific filter is selected (not 'all')
        if ($filter !== 'all') {
            $leadsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        // Calculate filtered total
        $filteredTotal = Lead::where(function($query) {
                $query->where('verified_by', auth()->id())
                      ->orWhere('account_verified_by', auth()->user()->name);
            });
            
        if ($filter !== 'all') {
            $filteredTotal->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $filteredTotal = $filteredTotal->count();

        // Daily stats within the selected date range (or all time if 'all')
        $todayStats = $this->getDailyStats(auth()->id(), auth()->user()->name, $startDate, $endDate, $filter);

        return view('verifier.dashboard', compact('leads', 'todayStats', 'filter', 'startDate', 'endDate', 'filteredTotal'));
    }

    /**
     * Get daily stats for verifier
     */
    private function getDailyStats($verifierId, $verifierName, $startDate, $endDate, $filter = 'all')
    {
        $query = Lead::where(function($query) use ($verifierId, $verifierName) {
                $query->where('verified_by', $verifierId)
                      ->orWhere('account_verified_by', $verifierName);
            });
        
        // Only apply date filter if not 'all'
        if ($filter !== 'all') {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $leads = $query->get();

        return [
            'total_verified' => $leads->count(),
            'transferred' => $leads->where('status', 'transferred')->count(),
            'closed' => $leads->where('status', 'closed')->count(),
            'sales' => $leads->where('status', 'sale')->count(),
            'pending' => $leads->where('status', 'pending')->count(),
            'declined' => $leads->where('status', 'declined')->count(),
        ];
    }

    /**
     * Helper method to get date range based on filter
     * Office hours: 7pm PKT to 5am PKT = 7am MT to 5pm MT
     */
    private function getDateRange($filter, $customStart = null, $customEnd = null)
    {
        $timezone = 'America/Denver';
        
        switch ($filter) {
            case 'today':
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            case 'yesterday':
                $start = Carbon::yesterday($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::yesterday($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            case 'week':
                $start = Carbon::now($timezone)->startOfWeek()->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            case 'custom':
                if ($customStart && $customEnd) {
                    try {
                        $start = Carbon::parse($customStart, $timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::parse($customEnd, $timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                        return [$start, $end];
                    } catch (\Exception $e) {
                        $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                        $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                        return [$start, $end];
                    }
                }
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
            
            default:
                $start = Carbon::today($timezone)->setTime(7, 0, 0)->setTimezone('UTC');
                $end = Carbon::today($timezone)->setTime(17, 0, 0)->setTimezone('UTC');
                return [$start, $end];
        }
    }
}
