<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix cascade delete constraints for user-related data to preserve employee history
     */
    public function up(): void
    {
        // Fix salary_records table
        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        // Fix dock_records table (user_id)
        Schema::table('dock_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        // Fix dock_records table (docked_by)
        Schema::table('dock_records', function (Blueprint $table) {
            $table->dropForeign(['docked_by']);
            $table->foreign('docked_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        // Fix call_events table
        Schema::table('call_events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        // Fix call_logs table (agent_id)
        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        // Fix agent_carrier_commissions table
        Schema::table('agent_carrier_commissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        // Fix vendor_transactions table (agent_id)
        Schema::table('vendor_transactions', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        // Fix bad_leads table (disposed_by)
        if (Schema::hasTable('bad_leads')) {
            Schema::table('bad_leads', function (Blueprint $table) {
                $table->dropForeign(['disposed_by']);
                $table->foreign('disposed_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to cascade delete (not recommended, but for migration rollback)
        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('dock_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['docked_by']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('docked_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('call_events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('agent_carrier_commissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('vendor_transactions', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
        });

        if (Schema::hasTable('bad_leads')) {
            Schema::table('bad_leads', function (Blueprint $table) {
                $table->dropForeign(['disposed_by']);
                $table->foreign('disposed_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
};
