<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_record_id', 'type', 'description', 'amount',
        'is_percentage', 'notes',
    ];

    protected $casts = [
        'is_percentage' => 'boolean',
    ];

    public function salaryRecord()
    {
        return $this->belongsTo(SalaryRecord::class);
    }

    public function getCalculatedAmountAttribute()
    {
        if ($this->is_percentage) {
            return ($this->salaryRecord->basic_salary * $this->amount) / 100;
        }

        return $this->amount;
    }
}
