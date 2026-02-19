<?php

// app/Services/AttendanceService.php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\PublicHoliday;
use App\Support\Statuses;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class AttendanceService
{
    /** Office timezone used for all attendance time calculations */
    public const TIMEZONE = 'Asia/Karachi';

    public function isInOfficeNetwork($ipAddress = null)
    {
        // Check if attendance is enabled
        if (! Setting::get('attendance_enabled', true)) {
            return false;
        }

        $ip = $ipAddress ?: Request::ip();
        $allowedNetworks = Setting::get('office_networks', []);

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
        // Always use internet-based PKT (Asia/Karachi) time for attendance
        $currentTime = Carbon::now(self::TIMEZONE);
        // Allow marking attendance only at or after Office Start Time
        $officeStartTimeRaw = Setting::get('office_start_time', '19:00');
        try {
            $startTime = Carbon::createFromFormat('H:i', $officeStartTimeRaw, self::TIMEZONE);
        } catch (\Exception $e) {
            $startTime = Carbon::createFromFormat('h:i A', $officeStartTimeRaw, self::TIMEZONE);
        }
        if ($currentTime->lessThan($startTime)) {
            return [
                'success' => false,
                'message' => 'Attendance cannot be marked before office start time (' . $startTime->format('g:i A') . ').',
            ];
        }
        // Always use internet-based PKT (Asia/Karachi) time for attendance
        $currentTime = Carbon::now(self::TIMEZONE);
        $currentHour = $currentTime->hour;

        // Get office start time and late time from settings
        $officeStartTimeRaw = Setting::get('office_start_time', '19:00');
        $lateTimeRaw = Setting::get('late_time', '19:15'); // e.g., '19:15' or '07:15 PM'

        // Accept both '19:00' and '07:00 PM' formats for office start
        try {
            $startTime = Carbon::createFromFormat('H:i', $officeStartTimeRaw, self::TIMEZONE);
        } catch (\Exception $e) {
            $startTime = Carbon::createFromFormat('h:i A', $officeStartTimeRaw, self::TIMEZONE);
        }

        // Accept both '19:15' and '07:15 PM' formats for late time
        try {
            $lateTime = Carbon::createFromFormat('H:i', $lateTimeRaw, self::TIMEZONE);
        } catch (\Exception $e) {
            $lateTime = Carbon::createFromFormat('h:i A', $lateTimeRaw, self::TIMEZONE);
        }

        // Get configurable attendance window settings
        $bufferHours = (int) Setting::get('attendance_buffer_hours', '1');
        $shiftDurationHours = (int) Setting::get('shift_duration_hours', '10');
        
        // Calculate allowed attendance window based on settings
        $windowStart = $startTime->copy()->subHours($bufferHours);
        $windowEnd = $startTime->copy()->addHours($shiftDurationHours + $bufferHours);
        
        // Check if current time is within the attendance window
        $isWithinOfficeHours = $currentTime->between($windowStart, $windowEnd, true);
        
        if (!$isWithinOfficeHours && !$forceOffice) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked between ' . $windowStart->format('g:i A') . ' and ' . $windowEnd->format('g:i A') . '. Office hours are ' . $startTime->format('g:i A') . ' to ' . $startTime->copy()->addHours($shiftDurationHours)->format('g:i A') . ' with ' . $bufferHours . '-hour buffer.',
            ];
        }

        // For night shift (7 PM - 5 AM), if current time is before 5 AM, attendance belongs to previous day's shift
        $shiftDate = $currentTime->hour < 5 ? Carbon::yesterday() : Carbon::today();

        // Check if it's a public holiday
        if (PublicHoliday::isHoliday($shiftDate)) {
            return [
                'success' => false,
                'message' => 'Today is a public holiday. Attendance marking is not required.',
            ];
        }

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

        // (late_threshold_minutes logic removed, only late_time is used)

        // Night shift logic: if office start is in evening (e.g., 19:00/7pm), attendance window is 7pm today to 5am next day
        $nightShift = $startTime->hour >= 12; // 12:00 or later is night shift
        $attendanceDate = $currentTime->copy()->setTimezone(self::TIMEZONE)->toDateString();

        if ($nightShift) {
            // Attendance window: 7pm today to 5am next day
            $shiftEnd = $startTime->copy()->addHours(10); // 7pm + 10h = 5am next day
            if ($currentTime->between($startTime, $shiftEnd, true)) {
                // If after midnight but before 5am (early morning), assign attendance to previous date
                if ($currentTime->hour < 5) {
                    $attendanceDate = $currentTime->copy()->subDay()->toDateString();
                }
                // Late if after fixed late time (e.g., 7:15pm), but before 5am
                $status = $currentTime->lessThanOrEqualTo($lateTime) ? Statuses::ATTENDANCE_PRESENT : Statuses::ATTENDANCE_LATE;
            } else {
                // Outside attendance window
                $status = Statuses::ATTENDANCE_ABSENT;
            }
        } else {
            // Day shift logic (default)
            $status = $currentTime->lessThanOrEqualTo($lateTime) ? Statuses::ATTENDANCE_PRESENT : Statuses::ATTENDANCE_LATE;
        }

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $shiftDate,
            'login_time' => $currentTime,
            'ip_address' => Request::ip(),
            'device_fingerprint' => Request::header('X-Device-Fingerprint')
                ?: Request::cookie('device_fingerprint')
                ?: Request::header('X-Device-ID')
                ?: Request::cookie('device_id'),
            'device_name' => Request::header('X-Device-Name')
                ?: Request::cookie('device_name'),
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
        $currentTime = Carbon::now(self::TIMEZONE);
        
        // Checkout cutoff is 6am (1 hour after shift ends at 5am)
        // After 6am, missed checkout is missed - no retroactive checkout allowed
        if ($currentTime->hour >= 6) {
            return [
                'success' => false,
                'message' => 'Checkout window has closed. Cutoff time is 6:00 AM.',
            ];
        }
        
        // For night shift (7 PM - 6 AM), if current time is before 6 AM,
        // the attendance belongs to previous day's shift
        $shiftDate = $currentTime->hour < 6 
            ? Carbon::yesterday(self::TIMEZONE) 
            : Carbon::today(self::TIMEZONE);

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $shiftDate)
            ->whereNotNull('login_time')
            ->whereNull('logout_time')
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
            'message' => 'No active attendance found to check out.',
        ];
    }

    // New method to check and mark attendance on dashboard visits
    public function checkAndMarkDailyAttendance($userId)
    {
        $currentTime = Carbon::now(self::TIMEZONE);
        
        // Get office start time from settings
        $officeStartTimeRaw = Setting::get('office_start_time', '19:00');
        try {
            $startTime = Carbon::createFromFormat('H:i', $officeStartTimeRaw, self::TIMEZONE);
        } catch (\Exception $e) {
            $startTime = Carbon::createFromFormat('h:i A', $officeStartTimeRaw, self::TIMEZONE);
        }
        
        // Calculate allowed attendance window with 1-hour buffer
        $bufferHours = (int) Setting::get('attendance_buffer_hours', '1');
        $shiftDurationHours = (int) Setting::get('shift_duration_hours', '10');
        $windowStart = $startTime->copy()->subHours($bufferHours);
        $windowEnd = $startTime->copy()->addHours($shiftDurationHours + $bufferHours);
        
        // Check if within allowed office hours
        $isWithinOfficeHours = $currentTime->between($windowStart, $windowEnd, true);
        
        if (!$isWithinOfficeHours) {
            return [
                'success' => false,
                'message' => 'Attendance can only be marked between ' . $windowStart->format('g:i A') . ' and ' . $windowEnd->format('g:i A') . '.',
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
        
        // Only run this between 5:00 AM and 5:30 AM
        if ($currentTime->hour !== 5) {
            return [
                'success' => false,
                'message' => 'Auto-checkout only runs between 5:00 AM and 5:30 AM.',
            ];
        }
        
        // Get previous shift date (yesterday since we're after 5 AM)
        $shiftDate = Carbon::yesterday();
        
        // Find all attendance records from previous shift without logout
        $overdueAttendances = Attendance::where('date', $shiftDate)
            ->whereNull('logout_time')
            ->get();
        
        $checkedOutCount = 0;
        
        foreach ($overdueAttendances as $attendance) {
            // Set logout time to 5:00 AM
            $checkoutTime = Carbon::create(
                $shiftDate->year,
                $shiftDate->month,
                $shiftDate->day,
                5,
                0,
                0
            )->addDay(); // Next day 5 AM
            
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
