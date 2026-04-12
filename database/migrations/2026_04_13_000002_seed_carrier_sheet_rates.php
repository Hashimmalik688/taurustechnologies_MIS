<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Seed the 8 carriers into carrier_sheet_rates ────────
        $carriers = [
            [
                'carrier_slug'   => 'ta-f1',
                'carrier_label'  => 'T.A (F-1)',
                'partner_code'   => 'F-1',
                'level_rate'     => 1.1500,
                'graded_rate'    => 0.7300,
                'gi_rate'        => null,
                'modified_rate'  => null,
                'gi_multiplier'  => 9,
                'title_color'    => '#1A237E',
                'sort_order'     => 1,
            ],
            [
                'carrier_slug'   => 'ta-y1',
                'carrier_label'  => 'T.A (Y-1)',
                'partner_code'   => 'Y-1',
                'level_rate'     => 1.2500,
                'graded_rate'    => 0.8300,
                'gi_rate'        => null,
                'modified_rate'  => null,
                'gi_multiplier'  => 9,
                'title_color'    => '#1A237E',
                'sort_order'     => 2,
            ],
            [
                'carrier_slug'   => 'aig-y1',
                'carrier_label'  => 'AIG (Y-1)',
                'partner_code'   => 'Y-1',
                'level_rate'     => 1.1700,
                'graded_rate'    => 0.7700,
                'gi_rate'        => 0.7250,
                'modified_rate'  => null,
                'gi_multiplier'  => 9,
                'title_color'    => '#0D47A1',
                'sort_order'     => 3,
            ],
            [
                'carrier_slug'   => 'aig-e1',
                'carrier_label'  => 'AIG (E-1)',
                'partner_code'   => 'E-1',
                'level_rate'     => 0.9700,
                'graded_rate'    => 0.6500,
                'gi_rate'        => 0.6250,
                'modified_rate'  => null,
                'gi_multiplier'  => 9,
                'title_color'    => '#0D47A1',
                'sort_order'     => 4,
            ],
            [
                'carrier_slug'   => 'amam-y1',
                'carrier_label'  => 'AMAM (Y-1)',
                'partner_code'   => 'Y-1',
                'level_rate'     => 0.8000,
                'graded_rate'    => null,
                'gi_rate'        => null,
                'modified_rate'  => null,
                'gi_multiplier'  => 9,
                'title_color'    => '#1B5E20',
                'sort_order'     => 5,
            ],
            [
                'carrier_slug'   => 'sec-f1',
                'carrier_label'  => 'SEC (F-1)',
                'partner_code'   => 'F-1',
                'level_rate'     => null,
                'graded_rate'    => null,
                'gi_rate'        => 0.8500,
                'modified_rate'  => 0.8500,
                'gi_multiplier'  => 1,     // SEC GI uses ×1, not ×9
                'title_color'    => '#4A148C',
                'sort_order'     => 6,
            ],
            [
                'carrier_slug'   => 'ra-f1',
                'carrier_label'  => 'R.A (F-1)',
                'partner_code'   => 'F-1',
                'level_rate'     => 0.8500,
                'graded_rate'    => 0.6500,
                'gi_rate'        => null,
                'modified_rate'  => null,
                'gi_multiplier'  => 9,
                'title_color'    => '#880E4F',
                'sort_order'     => 7,
            ],
            [
                'carrier_slug'          => 'aetna-y1',
                'carrier_label'         => 'AETNA (Y-1)',
                'partner_code'          => 'Y-1',
                'level_rate'            => 1.2500,   // maps to Preferred/Standard/Super Preferred
                'graded_rate'           => null,
                'gi_rate'               => null,
                'modified_rate'         => 1.1500,
                'gi_multiplier'         => 9,
                'uses_hardcoded_rates'  => true,
                'custom_policy_types'   => json_encode(['preferred', 'standard', 'super_preferred', 'modified']),
                'title_color'           => '#004D40',
                'sort_order'            => 8,
            ],
        ];

        $now = now();
        foreach ($carriers as $c) {
            DB::table('carrier_sheet_rates')->insert(array_merge($c, [
                'uses_hardcoded_rates' => $c['uses_hardcoded_rates'] ?? false,
                'custom_policy_types'  => $c['custom_policy_types'] ?? null,
                'is_active'            => true,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]));
        }

        // ── Register permission module ──────────────────────────
        Module::updateOrCreate(
            ['slug' => 'carrier-sheet'],
            [
                'name'        => 'Carrier Sheet',
                'description' => 'Commission tracking workbook — carrier sheets, rates & dashboard',
                'category'    => 'Reports',
                'sort_order'  => 145,
                'is_active'   => true,
            ]
        );
    }

    public function down(): void
    {
        DB::table('carrier_sheet_rates')->truncate();
        Module::where('slug', 'carrier-sheet')->delete();
    }
};
