<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManualPayrollEntry extends Model
{
    use HasFactory;

    protected $table = 'manual_payroll_entries';

    protected $fillable = [
        'employee_name',
        'join_date',
        'payroll_month',
        'payroll_year',
        'basic_salary',
        'punctuality_bonus',
        'full_days',
        'half_days',
        'late_days',
        'is_qualified',
        'dock_amount',
        'other_deductions',
        'other_allowances',
        'salary_advance',
        'notes',
    ];

    protected $casts = [
        'join_date' => 'date',
        'payroll_month' => 'integer',
        'payroll_year' => 'integer',
        'basic_salary' => 'decimal:2',
        'punctuality_bonus' => 'decimal:2',
        'full_days' => 'integer',
        'half_days' => 'integer',
        'late_days' => 'integer',
        'is_qualified' => 'boolean',
        'dock_amount' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'salary_advance' => 'decimal:2',
    ];
}
