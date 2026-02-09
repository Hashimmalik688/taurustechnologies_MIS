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
            // QA fields
            $table->enum('qa_status', ['Pending', 'Good', 'Avg', 'Bad'])->default('Pending')->after('retention_officer_id');
            $table->text('qa_reason')->nullable()->after('qa_status');
            $table->foreignId('qa_user_id')->nullable()->after('qa_reason')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['qa_user_id']);
            $table->dropColumn(['qa_status', 'qa_reason', 'qa_user_id']);
        });
    }
};
