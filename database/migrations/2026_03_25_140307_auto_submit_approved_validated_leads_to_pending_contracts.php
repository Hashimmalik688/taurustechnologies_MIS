<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Auto-submit existing approved + validated leads to Pending Contracts.
     *
     * This bridges the old manager_status flow to the new direct flow:
     * - Leads with manager_status=approved, ravens_validation_status=valid,
     *   and no pending_contract_at get moved to Pending Contracts automatically.
     */
    public function up(): void
    {
        $affected = DB::table('leads')
            ->where('manager_status', 'approved')
            ->where('ravens_validation_status', 'valid')
            ->whereNull('pending_contract_at')
            ->update([
                'pending_contract_at'    => now(),
                'pending_contract_by_id' => 1, // System / Super Admin
                'updated_at'             => now(),
            ]);

        if ($affected > 0) {
            echo "  Auto-submitted {$affected} approved+validated leads to Pending Contracts.\n";
        }
    }

    /**
     * Reverse: clear pending_contract_at for leads that were auto-submitted
     * (only those submitted by system user at this migration's timestamp).
     */
    public function down(): void
    {
        // No safe rollback — these leads were legitimately approved.
        // Manual intervention needed if reverting.
    }
};
