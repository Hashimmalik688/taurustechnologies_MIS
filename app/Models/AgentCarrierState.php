<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentCarrierState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_id',
        'insurance_carrier_id',
        'state',
        'settlement_level_pct',
        'settlement_graded_pct',
        'settlement_gi_pct',
        'settlement_modified_pct',
        'notes',
    ];

    protected $casts = [
        'settlement_level_pct' => 'decimal:2',
        'settlement_graded_pct' => 'decimal:2',
        'settlement_gi_pct' => 'decimal:2',
        'settlement_modified_pct' => 'decimal:2',
    ];

    /**
     * Get the agent/user for this carrier state.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the partner for this carrier state.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the insurance carrier for this state.
     */
    public function insuranceCarrier(): BelongsTo
    {
        return $this->belongsTo(InsuranceCarrier::class);
    }

    /**
     * Get the settlement percentage for a specific type.
     *
     * @param string $type 'level', 'graded', 'gi', or 'modified'
     * @return float|null
     */
    public function getSettlementPercentage(string $type): ?float
    {
        $column = 'settlement_' . $type . '_pct';
        return $this->$column;
    }
}
