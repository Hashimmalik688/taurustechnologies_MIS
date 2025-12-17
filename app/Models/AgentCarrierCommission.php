<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentCarrierCommission extends Model
{
    protected $fillable = [
        'user_id',
        'insurance_carrier_id',
        'commission_percentage',
        'notes',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
    ];

    /**
     * Get the agent (user) for this commission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the insurance carrier for this commission
     */
    public function insuranceCarrier()
    {
        return $this->belongsTo(InsuranceCarrier::class);
    }
}
