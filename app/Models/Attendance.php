<?php

namespace App\Models;

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
        'status',
        'auto_checkout',
    ];

    protected $casts = [
        'date' => 'date',
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'auto_checkout' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if user is late (assuming office starts at 9 AM)
    public function isLate()
    {
        if (! $this->login_time) {
            return false;
        }

        // Consider late if login after 9:15 AM
        $lateThreshold = Carbon::parse($this->date->format('Y-m-d').' 09:15:00');

        return $this->login_time > $lateThreshold;
    }

    // Calculate working hours
    public function getWorkingHoursAttribute()
    {
        if (! $this->login_time || ! $this->logout_time) {
            return 0;
        }

        return round($this->login_time->diffInHours($this->logout_time, true), 1);
    }

    // Get formatted login time
    public function getFormattedLoginTimeAttribute()
    {
        return $this->login_time ? $this->login_time->format('H:i') : 'N/A';
    }

    // Get formatted logout time
    public function getFormattedLogoutTimeAttribute()
    {
        return $this->logout_time ? $this->logout_time->format('H:i') : 'N/A';
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
        return $query->whereRaw("TIME(login_time) > '09:15:00'");
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
        $records = self::forUser($userId)->forMonth($month, $year)->get();

        return [
            'total_records' => $records->count(),
            'present_days' => $records->where('status', 'present')->count(),
            'absent_days' => $records->whereIn('status', ['absent', 'leave'])->count(),
            'late_days' => $records->filter(function ($record) {
                return $record->isLate();
            })->count(),
            'total_working_hours' => $records->sum('working_hours'),
            'average_working_hours' => $records->count() > 0 ? round($records->sum('working_hours') / $records->count(), 1) : 0,
        ];
    }

    // Check if attendance record exists for a specific date
    public static function hasRecordForDate($userId, $date)
    {
        return self::where('user_id', $userId)
            ->where('date', $date)
            ->exists();
    }

    // Get attendance status with color coding for UI
    public function getStatusWithColorAttribute()
    {
        $colors = [
            'present' => '#28a745', // Green
            'absent' => '#dc3545',  // Red
            'leave' => '#ffc107',   // Yellow
            'late' => '#fd7e14',    // Orange
        ];

        $status = $this->status;
        if ($status === 'present' && $this->isLate()) {
            $status = 'late';
        }

        return [
            'status' => $status,
            'color' => $colors[$status] ?? '#6c757d',
            'label' => ucfirst($status),
        ];
    }
}
