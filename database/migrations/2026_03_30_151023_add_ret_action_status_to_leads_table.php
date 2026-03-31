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
            // Retention officer action/disposition status for not-issued & not-paid cases
            $table->string('ret_action_status', 30)->nullable()->after('retention_notes')
                  ->comment('pending|in_progress|waiting_on_cx|fixed|cancelled|recalled');
            $table->timestamp('ret_action_updated_at')->nullable()->after('ret_action_status');
            $table->unsignedBigInteger('ret_action_updated_by')->nullable()->after('ret_action_updated_at');
            $table->foreign('ret_action_updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['ret_action_updated_by']);
            $table->dropColumn(['ret_action_status', 'ret_action_updated_at', 'ret_action_updated_by']);
        });
    }
};
