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

    /**
     * Scope: only CC Partner companies (outsource sales firms — distinct from
     * affiliate `partner`/`agent` records; they have no ledger/commission).
     */
    public function scopeCcPartners($query)
    {
        return $query->where('type', 'cc_partner');
    }

    /**
     * Scope: only closer-type (a CC Partner's sales staff who submit the sale form).
     */
    public function scopeClosers($query)
    {
        return $query->where('type', 'closer');
    }

    /**
     * Closers belonging to this CC Partner (downline of type=closer).
     */
    public function closers()
    {
        return $this->hasMany(Partner::class, 'parent_partner_id')->where('type', 'closer');
    }

    /**
     * The CC Partner this record belongs to. For a closer this is the parent;
     * for a CC Partner it is itself.
     */
    public function company()
    {
        return $this->isCloser() ? $this->parent : $this;
    }

    /**
     * A CC Partner company (can manage closers and see the submissions roll-up).
     */
    public function isCcPartner(): bool
    {
        return $this->type === 'cc_partner';
    }

    /**
     * A CC Partner's closer (submits the sale form, sees own submissions).
     */
    public function isCloser(): bool
    {
        return $this->type === 'closer';
    }

    /**
     * Partner ids whose submitted sales roll up under this record:
     * a CC Partner sees itself + all its closers; a closer sees only itself.
     */
    public function salesScopeIds(): array
    {
        if ($this->isCcPartner()) {
            return $this->closers()->pluck('id')->push($this->id)->all();
        }

        return [$this->id];
    }
}
