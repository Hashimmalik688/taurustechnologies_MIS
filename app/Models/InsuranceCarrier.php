<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceCarrier extends Model
{
    protected $fillable = [
        'name',
        'payment_module',
        'base_commission_percentage',
        'age_min',
        'age_max',
        'plan_types',
        'calculation_notes',
        'is_active',
    ];

    protected $casts = [
        'base_commission_percentage' => 'decimal:2',
        'age_min' => 'integer',
        'age_max' => 'integer',
        'plan_types' => 'array', // Cast JSON to array
        'is_active' => 'boolean',
    ];

    /**
     * Get all leads associated with this carrier
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get all commission brackets for this carrier
     */
    public function commissionBrackets(): HasMany
    {
        return $this->hasMany(CarrierCommissionBracket::class)->orderBy('age_min');
    }

    /**
     * Get the applicable commission percentage for a given age
     */
    public function getCommissionForAge($age)
    {
        $bracket = $this->commissionBrackets()
            ->where('age_min', '<=', $age)
            ->where('age_max', '>=', $age)
            ->first();

        return $bracket ? $bracket->commission_percentage : $this->base_commission_percentage;
    }

    /**
     * Get all agent-specific commission rates for this carrier
     */
    public function agentCommissions(): HasMany
    {
        return $this->hasMany(AgentCarrierCommission::class);
    }

    /**
     * Get all agent-state settlement rates for this carrier
     */
    public function agentStates(): HasMany
    {
        return $this->hasMany(AgentCarrierState::class);
    }

    /**
     * Get commission percentage for specific agent
     */
    public function getCommissionForAgent($agentId)
    {
        $agentCommission = $this->agentCommissions()
            ->where('user_id', $agentId)
            ->first();

        return $agentCommission ? $agentCommission->commission_percentage : $this->base_commission_percentage;
    }
}
