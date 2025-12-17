<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DockRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'docked_by',
        'amount',
        'reason',
        'dock_date',
        'dock_month',
        'dock_year',
        'status',
        'notes',
    ];

    protected $casts = [
        'dock_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the employee being docked
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created the dock record
     */
    public function dockedBy()
    {
        return $this->belongsTo(User::class, 'docked_by');
    }
}
