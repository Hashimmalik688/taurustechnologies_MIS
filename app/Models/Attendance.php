<?php

namespace App\Models;

use App\Support\Statuses;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'login_time',
        'logout_time',
        'ip_address',
        'device_fingerprint',
        'device_name',
        'status',
        'working_hours',
        'auto_checkout',
    ];

    protected $casts = [
        'date' => 'date',
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'auto_checkout' => 'boolean',
    ];

    /**
     * Boot method to auto-calculate working hours
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($attendance) {
            // Auto-calculate working hours if both times exist
            if ($attendance->login_time && $attendance->logout_time) {
                try {
                    // Use the actual attendance date for calculation, not today's date
                    $attendanceDate = $attendance->date ?? Carbon::today();
                    
                    // Parse login and logout times with the actual attendance date
                    $loginTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $attendance->login_time->format('H:i:s'));
                    $logoutTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $attendance->logout_time->format('H:i:s'));
                    
                    $attendance->working_hours = round($loginTime->diffInHours($logoutTime, true), 1);
                } catch (\Exception $e) {
                    // If parsing fails, set working hours to 0
                    $attendance->working_hours = 0;
                }
            } else {
                // No logout time means 0 working hours
                $attendance->working_hours = 0;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Check if user is late based on office start time setting
    public function isLate()
    {
        if (! $this->login_time) {
            return false;
        }

        $officeStartTimeRaw = \App\Models\Setting::get('office_start_time', '09:00');
        $lateThreshold = (int) \App\Models\Setting::get('late_threshold_minutes', 15);

        try {
            $startTime = Carbon::createFromFormat('H:i', $officeStartTimeRaw);
        } catch (\Exception $e) {
            $startTime = Carbon::createFromFormat('h:i A', $officeStartTimeRaw);
        }
        $lateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $startTime->copy()->addMinutes($lateThreshold)->format('H:i:s'));
        $loginTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->login_time->format('H:i:s'));

        return $loginTime->greaterThan($lateTime);
    }

    // Calculate working hours
    public function getWorkingHoursAttribute()
    {
        if (! $this->login_time || ! $this->logout_time) {
            return 0;
        }

        // Use the actual attendance date for calculation
        $attendanceDate = $this->date ?? Carbon::today();
        
        // Extract time from login_time and logout_time (which may have been cast with today's date)
        // Get just the time parts: HH:MM:SS
        $loginTimeStr = $this->login_time instanceof \DateTime 
            ? $this->login_time->format('H:i:s')
            : (is_string($this->login_time) ? $this->login_time : '00:00:00');
        
        $logoutTimeStr = $this->logout_time instanceof \DateTime 
            ? $this->logout_time->format('H:i:s')
            : (is_string($this->logout_time) ? $this->logout_time : '00:00:00');
        
        // Parse login and logout times with the ACTUAL attendance date
        $loginTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $loginTimeStr);
        $logoutTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $logoutTimeStr);
        
        return round($loginTime->diffInHours($logoutTime, true), 1);
    }

    /**
     * Get current working hours (live calculation if still working)
     * Returns hours with decimal precision
     */
    public function getCurrentWorkingHours()
    {
        if (! $this->login_time) {
            return 0;
        }

        // If there's a logout time, calculate based on that
        if ($this->logout_time) {
            $endTime = Carbon::parse($this->logout_time);
            $startTime = Carbon::parse($this->login_time);

            return round($startTime->diffInHours($endTime, true), 1);
        }

        // For records without logout, only show live hours if it's today
        // Otherwise return 0 (prevents showing 300+ hours for old records)
        if (! $this->date->isToday()) {
            return 0;
        }

        $endTime = Carbon::now();
        $startTime = Carbon::parse($this->login_time);

        return round($startTime->diffInHours($endTime, true), 1);
    }

    /**
     * Get current working hours formatted as "Xh Ym" string
     */
    public function getFormattedCurrentWorkingHours()
    {
        if (! $this->login_time) {
            return '-';
        }

        // If there's a logout time, calculate based on that
        if ($this->logout_time) {
            $endTime = Carbon::parse($this->logout_time);
            $startTime = Carbon::parse($this->login_time);

            $totalMinutes = $startTime->diffInMinutes($endTime);
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            return "{$hours}h {$minutes}m";
        }

        // For records without logout, only show live hours if it's today
        // For old records, show "Incomplete"
        if (! $this->date->isToday()) {
            return 'Incomplete';
        }

        $endTime = Carbon::now();
        $startTime = Carbon::parse($this->login_time);

        $totalMinutes = $startTime->diffInMinutes($endTime);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return "{$hours}h {$minutes}m";
    }

    /**
     * Check if employee is still working (no logout time)
     */
    public function isStillWorking()
    {
        return $this->login_time && ! $this->logout_time;
    }

    // Get formatted login time
    public function getFormattedLoginTimeAttribute()
    {
        return $this->login_time ? $this->login_time->format('g:i A') : 'N/A';
    }

    // Get formatted logout time
    public function getFormattedLogoutTimeAttribute()
    {
        return $this->logout_time ? $this->logout_time->format('g:i A') : 'N/A';
    }

    // Check if it's a full working day (at least 8 hours)
    public function isFullWorkingDay()
    {
        return $this->working_hours >= 8;
    }

    // Scopes for easier querying
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->whereIn('status', ['absent', 'leave']);
    }

    public function scopeLate($query)
    {
        $officeStartTimeRaw = \App\Models\Setting::get('office_start_time', '09:00');
        $lateThreshold = (int) \App\Models\Setting::get('late_threshold_minutes', 15);

        try {
            $startTime = Carbon::createFromFormat('H:i', $officeStartTimeRaw);
        } catch (\Exception $e) {
            $startTime = Carbon::createFromFormat('h:i A', $officeStartTimeRaw);
        }
        $lateTimeStr = $startTime->copy()->addMinutes($lateThreshold)->format('H:i:s');

        return $query->whereRaw("TIME(login_time) > '$lateTimeStr'");
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Static method to get attendance summary for a user/month
    public static function getAttendanceSummary($userId, $month, $year)
    {
        $records = self::forUser($userId)->forMonth($month, $year)->get()
            ->keyBy(function($a) { return $a->date->format('Y-m-d'); });

        // Calculate actual workdays (excluding weekends and holidays)
        $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $workdays = 0;
        $present = 0;
        $late = 0;
        $absent = 0;
        $totalHours = 0;
        
        $cursor = $startOfMonth->copy();
        $now = \Carbon\Carbon::now();
        
        while ($cursor->lte($endOfMonth)) {
            // Skip if future date
            if ($cursor->gt($now)) {
                $cursor->addDay();
                continue;
            }
            
            // Skip weekends
            if (in_array($cursor->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY])) {
                $cursor->addDay();
                continue;
            }
            
            // Skip public holidays
            if (PublicHoliday::isHoliday($cursor)) {
                $cursor->addDay();
                continue;
            }
            
            $workdays++;
            $att = $records->get($cursor->format('Y-m-d'));
            
            if ($att) {
                if ($att->status === 'present') $present++;
                if ($att->status === 'late') $late++;
                $totalHours += $att->working_hours ?? 0;
            } else {
                $absent++;
            }
            
            $cursor->addDay();
        }

        return [
            'total_days' => $workdays,
            'present_days' => $present,
            'late_days' => $late,
            'absent_days' => $absent,
            'total_working_hours' => round($totalHours, 1),
            'average_working_hours' => $workdays > 0 ? round($totalHours / $workdays, 1) : 0,
        ];
    }

    // Check if attendance record exists for a specific date
    public static function hasRecordForDate($userId, $date)
    {
        return self::where('user_id', $userId)
            ->where('date', $date)
            ->exists();
    }

    /**
     * Attendance status → CSS variable map for UI.
     *
     * Values are CSS custom-property names defined in _root.scss so they
     * adapt automatically when the theme changes.  Views should render
     * them as:  style="color: var({{ $color }})"
     */
    public const STATUS_COLORS = [
        Statuses::ATTENDANCE_PRESENT => '--bs-status-present',
        Statuses::ATTENDANCE_ABSENT  => '--bs-status-absent',
        Statuses::ATTENDANCE_LEAVE   => '--bs-status-leave',
        Statuses::ATTENDANCE_LATE    => '--bs-status-late',
    ];

    public const STATUS_COLOR_DEFAULT = '--bs-status-default';

    // Get attendance status with color coding for UI
    public function getStatusWithColorAttribute()
    {
        $status = $this->status;
        if ($status === Statuses::ATTENDANCE_PRESENT && $this->isLate()) {
            $status = Statuses::ATTENDANCE_LATE;
        }

        return [
            'status' => $status,
            'color' => self::STATUS_COLORS[$status] ?? self::STATUS_COLOR_DEFAULT,
            'label' => ucfirst($status),
        ];
    }
}
