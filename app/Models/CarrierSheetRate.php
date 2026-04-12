<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarrierSheetRate extends Model
{
    protected $fillable = [
        'carrier_slug',
        'carrier_label',
        'partner_code',
        'level_rate',
        'graded_rate',
        'gi_rate',
        'modified_rate',
        'gi_multiplier',
        'custom_policy_types',
        'title_color',
        'uses_hardcoded_rates',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'level_rate'           => 'decimal:4',
        'graded_rate'          => 'decimal:4',
        'gi_rate'              => 'decimal:4',
        'modified_rate'        => 'decimal:4',
        'gi_multiplier'        => 'integer',
        'custom_policy_types'  => 'array',
        'uses_hardcoded_rates' => 'boolean',
        'is_active'            => 'boolean',
        'sort_order'           => 'integer',
    ];

    /* ── Relationships ─────────────────────────────────── */

    public function entries(): HasMany
    {
        return $this->hasMany(CarrierSheetEntry::class);
    }

    public function openingChargebacks(): HasMany
    {
        return $this->hasMany(CarrierSheetOpeningCb::class);
    }

    /* ── Helpers ───────────────────────────────────────── */

    /**
     * Get the list of valid policy types for this carrier.
     */
    public function getPolicyTypes(): array
    {
        if (!empty($this->custom_policy_types)) {
            return $this->custom_policy_types;
        }

        return ['level', 'graded', 'gi', 'modified'];
    }

    /**
     * Look up the rate for a given policy type.
     * Returns null when the carrier doesn't support that type.
     */
    public function getRateForType(?string $policyType): ?float
    {
        if (!$policyType) {
            return null;
        }

        $type = strtolower(trim($policyType));

        // AETNA: preferred / standard / super_preferred all use level_rate
        if (in_array($type, ['preferred', 'standard', 'super_preferred', 'super preferred'])) {
            return $this->level_rate ? (float) $this->level_rate : null;
        }

        return match ($type) {
            'level'    => $this->level_rate    ? (float) $this->level_rate    : null,
            'graded'   => $this->graded_rate   ? (float) $this->graded_rate   : null,
            'gi'       => $this->gi_rate       ? (float) $this->gi_rate       : null,
            'modified' => $this->modified_rate ? (float) $this->modified_rate : null,
            default    => null,
        };
    }

    /**
     * Get the multiplier to use for a given policy type.
     * GI uses gi_multiplier (1 for SEC, 9 for others); everything else uses 9.
     */
    public function getMultiplier(string $policyType): int
    {
        $type = strtolower(trim($policyType));

        if ($type === 'gi') {
            return $this->gi_multiplier;
        }

        return 9;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
