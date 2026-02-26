<?php

namespace App\Support;

/**
 * Maps insurance carrier names from the `insurance_carriers` table
 * to the many free-text variations found in leads.carrier_name.
 *
 * Used by reports to group messy imported carrier text under the
 * canonical carrier name the business actually uses.
 */
class CarrierAliases
{
    /**
     * Carrier name → array of SQL LIKE keywords.
     * Each keyword is matched as  carrier_name LIKE '%keyword%'  (case-insensitive).
     *
     * Keep these sorted alphabetically by carrier name.
     */
    public const MAP = [
        'AIG' => [
            'AIG',
            'RNA',
            'RNOA',
            'Royal Neighbor',
        ],
        'American Amicable' => [
            'American Amicable',
            'AmAm',
            'AMAM',
            'Am. Amicable',
            'Am Am',
            'Americo',
            'American- Ameicable',
            'American Legacy',
            'american home life',
            'Senior Choice',
            'MAMA',
            'AHL',
        ],
        'Foresters' => [
            'Foresters',
        ],
        'Globe Life' => [
            'Globe Life',
            'GTL',
            'Gerber',
            'Liberty Banker',
            'Liberty Bank',
            'Liberty',
            'LBL',
        ],
        'Lincoln Heritage' => [
            'Lincoln Heritage',
            'Lumico',
        ],
        'Mutual of Omaha' => [
            'Mutual of Omaha',
            'Mutual',
            'M.O.O',
            'MOO',
        ],
        'Securico' => [
            'Securico',
        ],
        'Transamerica' => [
            'Transamerica',
            'Trans America',
            'Trans-America',
            'Trans.America',
            'TransAemrica',
            'Transamerrica',
            'Transmerica',
            'T-America',
            'T.America',
        ],
    ];

    /**
     * Build a where closure that matches all known aliases for a carrier.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $carrierName  Canonical carrier name (key in MAP)
     */
    public static function applyFilter($query, string $carrierName): void
    {
        if ($carrierName === '__other__') {
            static::applyOtherFilter($query);
            return;
        }

        $keywords = static::MAP[$carrierName] ?? [$carrierName];

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhere('leads.carrier_name', 'LIKE', '%' . $kw . '%');
            }
        });
    }

    /**
     * Match leads whose carrier_name does NOT match any known alias.
     */
    public static function applyOtherFilter($query): void
    {
        $query->where(function ($q) {
            $q->where(function ($inner) {
                foreach (static::MAP as $keywords) {
                    foreach ($keywords as $kw) {
                        $inner->where('leads.carrier_name', 'NOT LIKE', '%' . $kw . '%');
                    }
                }
            })
            ->whereNotNull('leads.carrier_name')
            ->where('leads.carrier_name', '!=', '');
        });
    }

    /**
     * Given a raw carrier_name string, return the canonical carrier it maps to.
     * Returns null if no match (= "Other").
     */
    public static function resolve(?string $rawName): ?string
    {
        if (empty($rawName)) {
            return null;
        }

        $lower = strtolower(trim($rawName));

        foreach (static::MAP as $canonical => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lower, strtolower($kw))) {
                    return $canonical;
                }
            }
        }

        return null;
    }
}
