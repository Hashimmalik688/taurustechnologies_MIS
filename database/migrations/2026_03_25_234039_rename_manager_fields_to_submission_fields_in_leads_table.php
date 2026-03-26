<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename manager_* fields to submission_* for clarity.
     * Each pipeline stage has its own status/by/at fields:
     * - Validation: ravens_validation_status, ravens_validated_by, ravens_validated_at
     * - Submissions: submission_status, submission_by, submission_at, submission_reason
     * - Pending Contracts: pending_contract_at, pending_contract_by_id, etc.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('manager_status', 'submission_status');
            $table->renameColumn('manager_reason', 'submission_reason');
            $table->renameColumn('manager_user_id', 'submission_by');
            $table->renameColumn('manager_reviewed_at', 'submission_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('submission_status', 'manager_status');
            $table->renameColumn('submission_reason', 'manager_reason');
            $table->renameColumn('submission_by', 'manager_user_id');
            $table->renameColumn('submission_at', 'manager_reviewed_at');
        });
    }
};
