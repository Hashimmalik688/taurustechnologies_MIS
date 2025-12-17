<?php

// app/Services/AttendanceService.php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class AttendanceService
{
    public function isInOfficeNetwork($ipAddress = null)
    {
        // Check if attendance is enabled
        if (! Setting::get('attendance_enabled', true)) {
            return false;
        }

        $ip = $ipAddress ?: Request::ip();
        $allowedNetworks = Setting::get('office_networks', ['192.168.1.0/24']);

        // If it's an array string from database, convert it
        if (is_string($allowedNetworks)) {
            $allowedNetworks = explode(',', $allowedNetworks);
        }

        foreach ($allowedNetworks as $network) {
            $network = trim($network);
            if ($this->ipInRange($ip, $network)) {
                return true;
            }
        }

        return false;
    }

    private function ipInRange($ip, $range)
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$subnet, $bits] = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) === $subnet;
    }

    public function markAttendance($userId, $forceOffice = false)
    {
        $currentTime = Carbon::now();
        $currentHour = $currentTime->hour;
        
        // Check if within allowed office hours (6 PM - 6 AM)
        // Allowed: 18:00 (6 PM) to 06:00 (6 AM)
        // Blocked: 06:00 (6 AM) to 18:00 (6 PM)
        $isWithinOfficeHours = $currentHour >= 18 || $currentHour < 6;
        
        if (!$isWithinOfficeHours && !$forceOffice) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked between 6:00 PM and 6:00 AM. Office hours are 7:00 PM to 5:00 AM with 1-hour buffer.',
            ];
        }
        
        // For night shift (7 PM - 5 AM), if current time is before 5 AM,
        // the attendance belongs to previous day's shift
        $shiftDate = $currentTime->hour < 5 
            ? Carbon::yesterday() 
            : Carbon::today();
        
        // Check if it's weekend and weekend attendance is not allowed
        if (! Setting::get('allow_weekend_attendance', false)) {
            if ($shiftDate->isWeekend()) {
                return [
                    'success' => false,
                    'message' => 'Attendance marking is not allowed on weekends.',
                ];
            }
        }

        // Check office network unless forced
        if (! $forceOffice && ! $this->isInOfficeNetwork()) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked from office network.',
                'debug_ip' => Request::ip(),
                'allowed_networks' => Setting::get('office_networks'),
            ];
        }

        // Check if attendance already exists for this shift
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $shiftDate)
            ->first();

        if ($attendance) {
            return [
                'success' => false,
                'message' => 'Attendance already marked for this shift.',
                'attendance' => $attendance,
            ];
        }

        // Get office start time and late threshold
        $officeStartTime = Setting::get('office_start_time', '19:00');
        $lateThreshold = Setting::get('late_threshold_minutes', 15);

        // For night shift (crossing midnight), we need special handling
        // Office starts at 7 PM (19:00), late threshold is 7:15 PM (19:15)
        $startTime = Carbon::createFromFormat('H:i', $officeStartTime);
        $lateTime = $startTime->copy()->addMinutes($lateThreshold);

        // Determine status based on current time
        // For night shift: 19:00 (7 PM) is on-time, 19:16+ is late
        $status = 'present';
        
        // Convert times to minutes since midnight for comparison
        $currentMinutes = $currentTime->hour * 60 + $currentTime->minute;
        $lateMinutes = $lateTime->hour * 60 + $lateTime->minute;
        
        // If office starts in evening (like 19:00), handle night shift
        if ($startTime->hour >= 12) {
            // Night shift: on-time if arrival is between 19:00-19:15 OR after midnight until 05:00
            if ($currentMinutes > $lateMinutes && $currentMinutes < 1440) {
                // After late time but same day (after 19:15 PM)
                $status = 'late';
            } elseif ($currentMinutes < 300) {
                // After midnight (00:00-05:00) - still considered on time for night shift
                $status = 'present';
            }
        } else {
            // Day shift: simple comparison
            if ($currentTime->format('H:i') > $lateTime->format('H:i')) {
                $status = 'late';
            }
        }

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $shiftDate,
            'login_time' => $currentTime,
            'ip_address' => Request::ip(),
            'status' => $status,
        ]);

        return [
            'success' => true,
            'message' => 'Attendance marked successfully.',
            'attendance' => $attendance,
            'status' => $status,
        ];
    }

    public function markLogout($userId)
    {
        $currentTime = Carbon::now();
        
        // For night shift (7 PM - 5 AM), if current time is before 5 AM,
        // the attendance belongs to previous day's shift
        $shiftDate = $currentTime->hour < 5 
            ? Carbon::yesterday() 
            : Carbon::today();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $shiftDate)
            ->first();

        if ($attendance && ! $attendance->logout_time) {
            $attendance->update([
                'logout_time' => Carbon::now(),
            ]);

            return [
                'success' => true,
                'message' => 'Logout time recorded.',
                'attendance' => $attendance,
            ];
        }

        return [
            'success' => false,
            'message' => 'No active attendance found for today.',
        ];
    }

    // New method to check and mark attendance on dashboard visits
    public function checkAndMarkDailyAttendance($userId)
    {
        $currentTime = Carbon::now();
        $currentHour = $currentTime->hour;
        
        // Check if within allowed office hours (6 PM - 6 AM)
        $isWithinOfficeHours = $currentHour >= 18 || $currentHour < 6;
        
        if (!$isWithinOfficeHours) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked between 6:00 PM and 6:00 AM.',
            ];
        }
        
        // For night shift (7 PM - 5 AM), if current time is before 5 AM,
        // the attendance belongs to previous day's shift
        $shiftDate = $currentTime->hour < 5 
            ? Carbon::yesterday() 
            : Carbon::today();

        // Check if attendance already exists for this shift
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $shiftDate)
            ->first();

        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Attendance already marked for this shift.',
                'attendance' => $existingAttendance,
            ];
        }

        // Check if user is in office network
        if ($this->isInOfficeNetwork()) {
            return $this->markAttendance($userId);
        }

        return [
            'success' => false,
            'message' => 'Not in office network, attendance not marked.',
            'should_show_manual' => true,
        ];
    }
    
    /**
     * Auto-checkout employees who haven't checked out after 6 AM
     * Should be run via scheduled command
     */
    public function autoCheckoutOverdueAttendances()
    {
        $currentTime = Carbon::now();
        
        // Only run this between 6:00 AM and 6:30 AM
        if ($currentTime->hour !== 6) {
            return [
                'success' => false,
                'message' => 'Auto-checkout only runs between 6:00 AM and 6:30 AM.',
            ];
        }
        
        // Get previous shift date (yesterday since we're after 6 AM)
        $shiftDate = Carbon::yesterday();
        
        // Find all attendance records from previous shift without logout
        $overdueAttendances = Attendance::where('date', $shiftDate)
            ->whereNull('logout_time')
            ->get();
        
        $checkedOutCount = 0;
        
        foreach ($overdueAttendances as $attendance) {
            // Set logout time to 6:00 AM
            $checkoutTime = Carbon::create(
                $shiftDate->year,
                $shiftDate->month,
                $shiftDate->day,
                6,
                0,
                0
            )->addDay(); // Next day 6 AM
            
            $attendance->update([
                'logout_time' => $checkoutTime,
                'auto_checkout' => true,
            ]);
            
            $checkedOutCount++;
        }
        
        return [
            'success' => true,
            'message' => "Auto-checkout completed for {$checkedOutCount} employee(s).",
            'checked_out_count' => $checkedOutCount,
        ];
    }
}
