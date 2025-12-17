<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DockRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DockController extends Controller
{
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

        // Get all employees for dropdown
        $employees = User::where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();

        // Calculate statistics for the month
        $stats = [
            'total_docked' => DockRecord::where('dock_month', $month)
                ->where('dock_year', $year)
                ->where('status', 'active')
                ->sum('amount'),
            'total_records' => DockRecord::where('dock_month', $month)
                ->where('dock_year', $year)
                ->count(),
            'active_records' => DockRecord::where('dock_month', $month)
                ->where('dock_year', $year)
                ->where('status', 'active')
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

        $dockDate = Carbon::parse($validated['dock_date']);

        $dockRecord = DockRecord::create([
            'user_id' => $validated['user_id'],
            'docked_by' => Auth::id(),
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'dock_date' => $dockDate,
            'dock_month' => $dockDate->month,
            'dock_year' => $dockDate->year,
            'status' => 'active',
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

        $dockRecord->update([
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'dock_date' => $dockDate,
            'dock_month' => $dockDate->month,
            'dock_year' => $dockDate->year,
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
        $user = User::findOrFail($userId);
        
        $dockRecords = DockRecord::with('dockedBy')
            ->where('user_id', $userId)
            ->orderBy('dock_date', 'desc')
            ->paginate(50);

        $totalDocked = DockRecord::where('user_id', $userId)
            ->where('status', 'active')
            ->sum('amount');

        return view('admin.dock.history', compact('user', 'dockRecords', 'totalDocked'));
    }
}
