<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadDial extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'dialed_at',
        'outcome',
        'notes',
    ];

    protected $casts = [
        'dialed_at' => 'datetime',
    ];

    /**
     * Get the lead that was dialed.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the user who dialed.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
