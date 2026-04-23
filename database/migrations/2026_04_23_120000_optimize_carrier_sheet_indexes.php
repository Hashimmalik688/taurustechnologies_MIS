<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance-optimized indexes to carrier_sheet tables.
     * These indexes target the most common query patterns:
     * - Filtering by period_month + status
     * - Looking up by policy_number for lead matching
     * - Looking up by name for lead matching
     * - Ordering by entry_date
     */
    public function up(): void
    {
        Schema::table('carrier_sheet_entries', function (Blueprint $table) {
            // Optimize lead lookup by policy number (used in lead() method)
            $table->index('policy_number', 'cs_entries_policy_number_idx');
            
            // Optimize lead lookup by name (fallback in lead() method)
            $table->index('name', 'cs_entries_name_idx');
            
            // Optimize queries filtering by entry_date (used for period fallback)
            $table->index('entry_date', 'cs_entries_entry_date_idx');
            
            // Optimize the common query pattern: carrier + period + status
            $table->index(['carrier_sheet_rate_id', 'period_month', 'status'], 'cs_entries_carrier_period_status_idx');
            
            // Optimize sorting and pagination
            $table->index(['carrier_sheet_rate_id', 'sr_number'], 'cs_entries_carrier_sr_idx');
            
            // Optimize soft delete queries (adds deleted_at to existing indexes)
            $table->index('deleted_at', 'cs_entries_deleted_at_idx');
        });

        Schema::table('carrier_sheet_opening_cbs', function (Blueprint $table) {
            // Optimize fetching opening balances for dashboard (SUM queries)
            $table->index('carrier_sheet_rate_id', 'cs_opening_cb_carrier_idx');
        });

        Schema::table('carrier_sheet_rates', function (Blueprint $table) {
            // Optimize ordering and active filtering
            $table->index(['is_active', 'sort_order'], 'cs_rates_active_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrier_sheet_entries', function (Blueprint $table) {
            $table->dropIndex('cs_entries_policy_number_idx');
            $table->dropIndex('cs_entries_name_idx');
            $table->dropIndex('cs_entries_entry_date_idx');
            $table->dropIndex('cs_entries_carrier_period_status_idx');
            $table->dropIndex('cs_entries_carrier_sr_idx');
            $table->dropIndex('cs_entries_deleted_at_idx');
        });

        Schema::table('carrier_sheet_opening_cbs', function (Blueprint $table) {
            $table->dropIndex('cs_opening_cb_carrier_idx');
        });

        Schema::table('carrier_sheet_rates', function (Blueprint $table) {
            $table->dropIndex('cs_rates_active_sort_idx');
        });
    }
};
