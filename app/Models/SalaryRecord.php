<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'salary_year', 'salary_month', 'basic_salary',
        'target_sales', 'actual_sales', 'chargeback_count', 'net_approved_sales', 
        'next_month_target_adjustment', 'extra_sales', 'bonus_per_extra_sale',
        'total_bonus', 'total_deductions', 'gross_salary', 'net_salary',
        'status', 'notes', 'calculated_at', 'approved_at', 'paid_at',

        // Attendance fields (compatible with your existing AttendanceService)
        'working_days', 'present_days', 'leave_days', 'late_days', 'half_days', 'daily_salary',
        'attendance_bonus', 'attendance_deduction',
        
        // Dock fields
        'dock_amount', 'dock_details',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'basic_salary' => 'decimal:2',
        'total_bonus' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'daily_salary' => 'decimal:2',
        'attendance_bonus' => 'decimal:2',
        'attendance_deduction' => 'decimal:2',
        'dock_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deductions()
    {
        return $this->hasMany(SalaryDeduction::class);
    }
    
    /**
     * Get dock records for this employee for this month/year
     */
    public function dockRecords()
    {
        return $this->hasMany(DockRecord::class, 'user_id', 'user_id')
            ->whereMonth('dock_date', $this->salary_month)
            ->whereYear('dock_date', $this->salary_year);
    }

    /**
     * Relationship to salary components (new split payment system)
     */
    public function components()
    {
        return $this->hasMany(SalaryComponent::class, 'user_id', 'user_id')
            ->where('salary_month', $this->salary_month)
            ->where('salary_year', $this->salary_year);
    }

    /**
     * Get basic salary component
     */
    public function basicComponent()
    {
        return $this->components()->where('component_type', 'basic')->first();
    }

    /**
     * Get bonus salary component
     */
    public function bonusComponent()
    {
        return $this->components()->where('component_type', 'bonus')->first();
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->salary_month)->format('F');
    }

    // Attendance-related accessors compatible with your system
    public function getAttendancePercentageAttribute()
    {
        if ($this->working_days == 0) {
            return 0;
        }

        return round(($this->present_days / $this->working_days) * 100, 2);
    }

    public function getHasPerfectAttendanceAttribute()
    {
        return $this->leave_days == 0 && $this->present_days >= $this->working_days;
    }

    public function getPunctualityPercentageAttribute()
    {
        if ($this->present_days == 0) {
            return 0;
        }

        return round((($this->present_days - $this->late_days) / $this->present_days) * 100, 2);
    }

    public function getSandwichPenaltyDaysAttribute()
    {
        return $this->leave_days * 1; // 1 day penalty for each leave day
    }

    public function getTotalSalaryAdjustmentAttribute()
    {
        return $this->attendance_bonus + $this->attendance_deduction; // attendance_deduction is negative
    }

    public function getNetAttendanceImpactAttribute()
    {
        return $this->attendance_bonus + $this->attendance_deduction;
    }

    // Get attendance summary for display
    public function getAttendanceSummaryAttribute()
    {
        return [
            'working_days' => $this->working_days,
            'present_days' => $this->present_days,
            'leave_days' => $this->leave_days,
            'late_days' => $this->late_days,
            'attendance_percentage' => $this->attendance_percentage,
            'punctuality_percentage' => $this->punctuality_percentage,
            'has_perfect_attendance' => $this->has_perfect_attendance,
            'sandwich_penalty_days' => $this->sandwich_penalty_days,
        ];
    }
}
