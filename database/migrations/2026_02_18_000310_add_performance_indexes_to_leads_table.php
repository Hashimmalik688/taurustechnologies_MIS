<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes for columns frequently used in WHERE, ORDER BY, and GROUP BY clauses.
     * These target the heaviest pages: Sales, QA Review, Bank Verification, Retention, Calling.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Sales page: filters by closer_name + sale_at/sale_date
            $table->index('closer_name', 'idx_leads_closer_name');
            $table->index('sale_at', 'idx_leads_sale_at');
            $table->index('sale_date', 'idx_leads_sale_date');

            // Sales / QA / Manager pages: filters by manager_status
            $table->index('manager_status', 'idx_leads_manager_status');

            // Issuance / Bank Verification pages
            $table->index('issuance_status', 'idx_leads_issuance_status');
            $table->index('bank_verification_status', 'idx_leads_bank_verification_status');

            // Retention pages
            $table->index('retention_status', 'idx_leads_retention_status');

            // QA Review page
            $table->index('qa_status', 'idx_leads_qa_status');

            // Calling page / Peregrine leads: filters by verified_by
            $table->index('verified_by', 'idx_leads_verified_by');

            // Composite indexes for the most common multi-column query patterns
            $table->index(['closer_name', 'sale_at'], 'idx_leads_closer_sale_at');
            $table->index(['status', 'manager_status'], 'idx_leads_status_manager');
            $table->index(['status', 'retention_status'], 'idx_leads_status_retention');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_closer_name');
            $table->dropIndex('idx_leads_sale_at');
            $table->dropIndex('idx_leads_sale_date');
            $table->dropIndex('idx_leads_manager_status');
            $table->dropIndex('idx_leads_issuance_status');
            $table->dropIndex('idx_leads_bank_verification_status');
            $table->dropIndex('idx_leads_retention_status');
            $table->dropIndex('idx_leads_qa_status');
            $table->dropIndex('idx_leads_verified_by');
            $table->dropIndex('idx_leads_closer_sale_at');
            $table->dropIndex('idx_leads_status_manager');
            $table->dropIndex('idx_leads_status_retention');
        });
    }
};
