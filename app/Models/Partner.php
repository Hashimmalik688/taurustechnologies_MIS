<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Partner extends Authenticatable
{
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'ssn_last4',
        'password',
        'is_active',
        'our_commission_percentage',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get the carrier states for this partner
     */
    public function carrierStates()
    {
        return $this->hasMany(AgentCarrierState::class, 'partner_id');
    }

    /**
     * Get unique carriers for this partner
     */
    public function carriers()
    {
        return $this->belongsToMany(
            InsuranceCarrier::class,
            'agent_carrier_states',
            'partner_id',
            'insurance_carrier_id'
        )->distinct();
    }
}
