<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks WHO assigned followup persons and bank verifiers, and WHEN.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Followup assignment audit
            $table->foreignId('followup_assigned_by')->nullable()->after('assigned_followup_person')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('followup_assigned_at')->nullable()->after('followup_assigned_by');

            // Bank verifier assignment audit
            $table->foreignId('bank_verifier_assigned_by')->nullable()->after('assigned_bank_verifier')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('bank_verifier_assigned_at')->nullable()->after('bank_verifier_assigned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['followup_assigned_by']);
            $table->dropForeign(['bank_verifier_assigned_by']);
            $table->dropColumn([
                'followup_assigned_by',
                'followup_assigned_at',
                'bank_verifier_assigned_by',
                'bank_verifier_assigned_at',
            ]);
        });
    }
};
