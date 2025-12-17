<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetentionDashboardController extends Controller
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

        // Get retention stats
        $stats = [
            'total_chargebacks' => Lead::where('status', 'chargeback')->count(),
            'yet_to_retain' => Lead::where('status', 'chargeback')
                ->where(function($q) {
                    $q->whereNull('retention_status')
                      ->orWhere('retention_status', 'pending');
                })->count(),
            'retained_today' => Lead::where('retention_status', 'retained')
                ->whereDate('retained_at', today())->count(),
            'retained_mtd' => Lead::where('retention_status', 'retained')
                ->whereMonth('retained_at', now()->month)
                ->whereYear('retained_at', now()->year)->count(),
            'rewrite_count' => Lead::where('status', 'chargeback')
                ->where('is_rewrite', true)->count(),
            'attendance_summary' => $attendanceSummary,
            'today_status' => Attendance::where('user_id', $user->id)->whereDate('date', today())->first(),
        ];

        return view('retention.dashboard', compact('stats'));
    }
}
