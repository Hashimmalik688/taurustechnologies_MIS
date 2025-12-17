<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\CallLog;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Attendance summary using configured attendance period (default start day 25)
        $startDay = Setting::get('attendance_period_start_day', 25);
        $today = now();

        if ($today->day >= $startDay) {
            $periodStart = \Carbon\Carbon::create($today->year, $today->month, $startDay)->startOfDay();
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        } else {
            $periodStart = \Carbon\Carbon::create($today->copy()->subMonth()->year, $today->copy()->subMonth()->month, $startDay)->startOfDay();
            $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        }

        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
            ->get();

        $attendanceSummary = [
            'total_records' => $attendanceRecords->count(),
            'present_days' => $attendanceRecords->where('status', 'present')->count(),
            'absent_days' => $attendanceRecords->whereIn('status', ['absent', 'leave'])->count(),
            'late_days' => $attendanceRecords->filter(function ($rec) { return $rec->isLate(); })->count(),
            'total_working_hours' => $attendanceRecords->sum('working_hours'),
            'average_working_hours' => $attendanceRecords->count() > 0 ? round($attendanceRecords->sum('working_hours') / $attendanceRecords->count(), 1) : 0,
        ];

        // Get stats for the employee
        $stats = [
            'dialed_today' => $this->getDialedTodayCount($user->id),
            'calls_connected' => $this->getCallsConnectedCount($user->id),
            'sales_today' => $this->getSalesTodayCount($user->id),
            'mtd_sales' => $this->getMTDSalesCount($user->id),
            'attendance_summary' => $attendanceSummary,
            // Today's attendance status if any
            'today_status' => Attendance::where('user_id', $user->id)->whereDate('date', today())->first(),
        ];

        return view('employee.dashboard', compact('stats'));
    }

    public function leads()
    {
        // Get leads for employees to call - paginated for performance
        $leads = Lead::orderBy('created_at', 'desc')->paginate(100);

        return view('employee.leads', compact('leads'));
    }

    /**
     * Get count of unique leads dialed today by this employee
     */
    private function getDialedTodayCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->distinct('lead_id')
            ->count('lead_id');
    }

    /**
     * Get count of sales made today by this employee
     */
    private function getSalesTodayCount($userId)
    {
        return Lead::where('closer_name', Auth::user()->name)
            ->whereDate('sale_at', today())
            ->whereIn('status', ['accepted', 'underwritten'])
            ->count();
    }

    /**
     * Get count of calls connected today
     */
    private function getCallsConnectedCount($userId)
    {
        return CallLog::where('agent_id', $userId)
            ->whereDate('call_start_time', today())
            ->where('call_status', 'connected')
            ->count();
    }

    /**
     * Get MTD (Month-To-Date) sales count for this employee
     */
    private function getMTDSalesCount($userId)
    {
        return Lead::where('closer_name', Auth::user()->name)
            ->whereMonth('sale_at', now()->month)
            ->whereYear('sale_at', now()->year)
            ->whereIn('status', ['accepted', 'underwritten'])
            ->count();
    }
}
