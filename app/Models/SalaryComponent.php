<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'salary_year', 'salary_month', 'component_type', 'payment_date',
        'basic_salary', 'calculated_amount', 'approved_amount', 'deductions', 'net_amount',
        'target_sales', 'actual_sales', 'chargeback_count', 'net_approved_sales', 'extra_sales', 'bonus_per_extra_sale',
        'working_days', 'present_days', 'leave_days', 'late_days', 'daily_salary', 'attendance_bonus', 'attendance_deduction',
        'dock_deductions', 'manual_deductions',
        'status', 'notes', 'calculated_at', 'approved_at', 'paid_at'
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_date' => 'date',
        'basic_salary' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'daily_salary' => 'decimal:2',
        'attendance_bonus' => 'decimal:2',
        'attendance_deduction' => 'decimal:2',
        'dock_deductions' => 'decimal:2',
        'manual_deductions' => 'decimal:2',
        'bonus_per_extra_sale' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deductions()
    {
        return $this->hasMany(SalaryDeduction::class, 'salary_component_id');
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->salary_month)->format('F');
    }

    public function getComponentLabelAttribute()
    {
        return ucfirst($this->component_type) . ' Salary (' . $this->payment_date->format('d M Y') . ')';
    }

    /**
     * Get attendance-related attributes for basic component
     */
    public function getAttendancePercentageAttribute()
    {
        if ($this->component_type !== 'basic' || $this->working_days == 0) {
            return 0;
        }
        return round(($this->present_days / $this->working_days) * 100, 2);
    }

    public function getHasPerfectAttendanceAttribute()
    {
        if ($this->component_type !== 'basic') {
            return false;
        }
        return $this->leave_days == 0 && $this->present_days >= $this->working_days;
    }

    /**
     * Get sales-related attributes for bonus component
     */
    public function getHasSalesDataAttribute()
    {
        return $this->component_type === 'bonus' && $this->actual_sales !== null;
    }

    public function getSalesTargetStatusAttribute()
    {
        if ($this->component_type !== 'bonus' || $this->net_approved_sales === null) {
            return null;
        }
        
        if ($this->net_approved_sales < $this->target_sales) {
            return 'below_target';
        }
        return 'above_target';
    }

    /**
     * Scope to get basic salary components
     */
    public function scopeBasic($query)
    {
        return $query->where('component_type', 'basic');
    }

    /**
     * Scope to get bonus components
     */
    public function scopeBonus($query)
    {
        return $query->where('component_type', 'bonus');
    }

    /**
     * Scope to get components for a specific month/year
     */
    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('salary_month', $month)->where('salary_year', $year);
    }

    /**
     * Scope to get unpaid components
     */
    public function scopeUnpaid($query)
    {
        return $query->whereNotIn('status', ['paid']);
    }
}
