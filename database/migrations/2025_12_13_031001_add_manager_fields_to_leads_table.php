<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Manager fields (separate from QA)
            $table->enum('manager_status', ['pending', 'approved', 'declined', 'underwriting', 'chargeback'])->default('pending')->after('qa_user_id');
            $table->text('manager_reason')->nullable()->after('manager_status');
            $table->foreignId('manager_user_id')->nullable()->after('manager_reason')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['manager_user_id']);
            $table->dropColumn(['manager_status', 'manager_reason', 'manager_user_id']);
        });
    }
};
