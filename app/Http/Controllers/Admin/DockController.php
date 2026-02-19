<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DockRecord;
use App\Models\User;
use App\Support\Roles;
use App\Support\Statuses;
use App\Traits\PayrollMonthCalculation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DockController extends Controller
{
    use PayrollMonthCalculation;
    /**
     * Display dock records dashboard
     */
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $userId = $request->get('user_id');

        $query = DockRecord::with(['user', 'dockedBy'])
            ->where('dock_month', $month)
            ->where('dock_year', $year);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $dockRecords = $query->orderBy('dock_date', 'desc')->paginate(20);

        // Get all employees for dropdown - exclude CEO users
        $employees = User::where('status', '!=', Statuses::USER_INACTIVE)
            ->get()
            ->filter(function ($user) {
                return $user && method_exists($user, 'hasRole') && !$user->hasRole(Roles::CEO);
            })
            ->sortBy('name')
            ->values();

        // Calculate statistics for the month
        $stats = [
            'total_docked' => DockRecord::where('dock_month', $month)
                ->where('dock_year', $year)
                ->where('status', Statuses::DOCK_ACTIVE)
                ->sum('amount'),
            'total_records' => DockRecord::where('dock_month', $month)
                ->where('dock_year', $year)
                ->count(),
            'active_records' => DockRecord::where('dock_month', $month)
                ->where('dock_year', $year)
                ->where('status', Statuses::DOCK_ACTIVE)
                ->count(),
        ];

        return view('admin.dock.index', compact('dockRecords', 'employees', 'month', 'year', 'stats'));
    }

    /**
     * Store a new dock record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'reason' => 'required|string|max:500',
            'dock_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Prevent docking CEO users - they are above the system
        $user = User::find($validated['user_id']);
        if ($user && $user->hasRole(Roles::CEO)) {
            return redirect()->back()
                ->with('error', 'CEO users cannot be docked - they are above the system.');
        }

        $dockDate = Carbon::parse($validated['dock_date']);
        
        // Calculate correct payroll month/year based on 26th-25th cycle
        $payrollMonthYear = $this->getPayrollMonthYear($dockDate);

        $dockRecord = DockRecord::create([
            'user_id' => $validated['user_id'],
            'docked_by' => Auth::id(),
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'dock_date' => $dockDate,
            'dock_month' => $payrollMonthYear['month'],
            'dock_year' => $payrollMonthYear['year'],
            'status' => Statuses::DOCK_ACTIVE,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('dock.index')
            ->with('success', 'Dock record created successfully!');
    }

    /**
     * Update a dock record
     */
    public function update(Request $request, DockRecord $dockRecord)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:999999.99',
            'reason' => 'required|string|max:500',
            'dock_date' => 'required|date',
            'status' => 'required|in:active,cancelled,applied',
            'notes' => 'nullable|string|max:1000',
        ]);

        $dockDate = Carbon::parse($validated['dock_date']);
        
        // Calculate correct payroll month/year based on 26th-25th cycle
        $payrollMonthYear = $this->getPayrollMonthYear($dockDate);

        $dockRecord->update([
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'dock_date' => $dockDate,
            'dock_month' => $payrollMonthYear['month'],
            'dock_year' => $payrollMonthYear['year'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('dock.index')
            ->with('success', 'Dock record updated successfully!');
    }

    /**
     * Cancel a dock record
     */
    public function cancel(DockRecord $dockRecord)
    {
        $dockRecord->update(['status' => 'cancelled']);

        return redirect()->route('dock.index')
            ->with('success', 'Dock record cancelled successfully!');
    }

    /**
     * Delete a dock record
     */
    public function destroy(DockRecord $dockRecord)
    {
        $dockRecord->delete();

        return redirect()->route('dock.index')
            ->with('success', 'Dock record deleted successfully!');
    }

    /**
     * View history for specific employee
     */
    public function history($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        
        $dockRecords = DockRecord::with('dockedBy')
            ->where('user_id', $userId)
            ->orderBy('dock_date', 'desc')
            ->paginate(50);

        $totalDocked = DockRecord::where('user_id', $userId)
            ->where('status', Statuses::DOCK_ACTIVE)
            ->sum('amount');

        return view('admin.dock.history', compact('user', 'dockRecords', 'totalDocked'));
    }

    /**
     * View dock records for current authenticated employee
     */
    public function myDockRecords()
    {
        $user = Auth::user();
        
        $dockRecords = DockRecord::with('dockedBy')
            ->where('user_id', $user->id)
            ->orderBy('dock_date', 'desc')
            ->paginate(20);

        $totalDocked = DockRecord::where('user_id', $user->id)
            ->where('status', Statuses::DOCK_ACTIVE)
            ->sum('amount');

        // Total applied (already deducted)
        $totalApplied = DockRecord::where('user_id', $user->id)
            ->where('status', 'applied')
            ->sum('amount');

        // Total cancelled
        $totalCancelled = DockRecord::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->sum('amount');

        // Status counts
        $statusCounts = DockRecord::where('user_id', $user->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly breakdown for trend chart (last 6 months)
        $monthlyData = DockRecord::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('dock_month, dock_year, sum(amount) as total, count(*) as count')
            ->groupBy('dock_year', 'dock_month')
            ->orderBy('dock_year', 'desc')
            ->orderBy('dock_month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Reason breakdown for donut chart
        $reasonBreakdown = DockRecord::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('reason, sum(amount) as total, count(*) as count')
            ->groupBy('reason')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Total records count (all statuses)
        $totalRecords = DockRecord::where('user_id', $user->id)->count();

        return view('employee.dock-records', compact(
            'user', 'dockRecords', 'totalDocked', 'totalApplied',
            'totalCancelled', 'statusCounts', 'monthlyData',
            'reasonBreakdown', 'totalRecords'
        ));
    }
}
