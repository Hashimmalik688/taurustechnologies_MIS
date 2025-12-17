<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_id',
        'name',
        'policy_number',
        'premium_amount',
        'coverage_amount',
        'phone',
        'email',
        'website',
        'status',
        'notes',
        'forwarded_by',
        'managed_by',
        'sale_at',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the user who forwarded this carrier
     */
    public function forwardedBy()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    /**
     * Get the user who manages this carrier
     */
    public function managedBy()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }
}
