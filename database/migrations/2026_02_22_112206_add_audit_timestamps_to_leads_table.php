<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds audit timestamps so we can track WHO did QA/Manager review and WHEN.
     * Also adds bank_verified_by to track which user performed bank verification.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // QA review timestamp - when did QA review this lead
            $table->timestamp('qa_reviewed_at')->nullable()->after('qa_user_id');

            // Manager review timestamp - when did the manager review this lead
            $table->timestamp('manager_reviewed_at')->nullable()->after('manager_user_id');

            // Bank verification - track which user verified (separate from assigned_bank_verifier)
            $table->foreignId('bank_verified_by')->nullable()->after('bank_verification_notes')
                  ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['bank_verified_by']);
            $table->dropColumn(['qa_reviewed_at', 'manager_reviewed_at', 'bank_verified_by']);
        });
    }
};
