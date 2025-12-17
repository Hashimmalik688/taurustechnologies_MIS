<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierCommissionBracket extends Model
{
    protected $fillable = [
        'insurance_carrier_id',
        'age_min',
        'age_max',
        'commission_percentage',
        'notes',
    ];

    /**
     * Get the insurance carrier that owns this bracket
     */
    public function insuranceCarrier()
    {
        return $this->belongsTo(InsuranceCarrier::class);
    }

    /**
     * Check if a given age falls within this bracket
     */
    public function containsAge($age)
    {
        return $age >= $this->age_min && $age <= $this->age_max;
    }
}
