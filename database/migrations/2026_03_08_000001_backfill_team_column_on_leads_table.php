<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill the leads.team column so every lead has a clear team owner.
     *
     * Rules applied in order:
     *   1. Already has team set                    → leave it (no change)
     *   2. verified_by IS NOT NULL                 → 'peregrine'  (verifier pipeline)
     *   3. validated_by IS NOT NULL                → 'peregrine'  (validator pipeline)
     *   4. Everything else (imported CSV, etc.)    → 'ravens'
     */
    public function up(): void
    {
        // Peregrine: leads that went through the verifier pipeline
        DB::table('leads')
            ->whereNull('team')
            ->where(function ($q) {
                $q->whereNotNull('verified_by')
                  ->orWhereNotNull('validated_by');
            })
            ->update(['team' => 'peregrine']);

        // Ravens: everything else
        DB::table('leads')
            ->whereNull('team')
            ->update(['team' => 'ravens']);
    }

    public function down(): void
    {
        // Revert: clear the team column for all leads
        DB::table('leads')->update(['team' => null]);
    }
};
