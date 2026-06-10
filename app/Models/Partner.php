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
        'parent_partner_id',
        'type',
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

    /**
     * Parent partner (upline)
     */
    public function parent()
    {
        return $this->belongsTo(Partner::class, 'parent_partner_id');
    }

    /**
     * Child agents (downline)
     */
    public function agents()
    {
        return $this->hasMany(Partner::class, 'parent_partner_id');
    }

    /**
     * Scope: only partner-type (not agents)
     */
    public function scopePartners($query)
    {
        return $query->where('type', 'partner');
    }

    /**
     * Scope: only agent-type
     */
    public function scopeAgents($query)
    {
        return $query->where('type', 'agent');
    }
}
