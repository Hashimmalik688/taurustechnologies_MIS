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

    /**
     * Get the state-specific settlement records for this agent-carrier combination
     */
    public function carrierStates()
    {
        return $this->hasMany(AgentCarrierState::class, 'user_id', 'user_id')
            ->where('insurance_carrier_id', $this->insurance_carrier_id);
    }
}
