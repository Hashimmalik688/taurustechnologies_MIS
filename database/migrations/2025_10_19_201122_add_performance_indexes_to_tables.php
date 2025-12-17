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
        // Add indexes to leads table for faster queries
        Schema::table('leads', function (Blueprint $table) {
            $table->index('status', 'idx_leads_status');
            $table->index('created_at', 'idx_leads_created_at');
            $table->index('phone_number', 'idx_leads_phone');
            $table->index('forwarded_by', 'idx_leads_forwarded_by');
            $table->index('managed_by', 'idx_leads_managed_by');
            $table->index(['status', 'created_at'], 'idx_leads_status_created');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
            $table->index('created_at', 'idx_users_created_at');
        });

        // Add indexes to call_logs table
        Schema::table('call_logs', function (Blueprint $table) {
            $table->index('lead_id', 'idx_call_logs_lead_id');
            $table->index('agent_id', 'idx_call_logs_agent_id');
            $table->index('created_at', 'idx_call_logs_created_at');
            $table->index('call_status', 'idx_call_logs_status');
        });

        // Add indexes to attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('user_id', 'idx_attendances_user_id');
            $table->index('date', 'idx_attendances_date');
            $table->index(['user_id', 'date'], 'idx_attendances_user_date');
        });

        // Add indexes to carriers table
        Schema::table('carriers', function (Blueprint $table) {
            $table->index('lead_id', 'idx_carriers_lead_id');
            $table->index('status', 'idx_carriers_status');
        });

        // Add indexes to salary_records table
        if (Schema::hasTable('salary_records')) {
            Schema::table('salary_records', function (Blueprint $table) {
                    if (Schema::hasColumn('salary_records', 'user_id')) {
                        $table->index('user_id', 'idx_salary_records_user_id');
                    }
                    if (Schema::hasColumn('salary_records', 'month')) {
                        $table->index('month', 'idx_salary_records_month');
                    }
                    if (Schema::hasColumn('salary_records', 'year')) {
                        $table->index('year', 'idx_salary_records_year');
                    }
            });
        }

        // Add indexes to notifications table
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('user_id', 'idx_notifications_user_id');
                $table->index('read_at', 'idx_notifications_read_at');
                $table->index(['user_id', 'read_at'], 'idx_notifications_user_read');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_status');
            $table->dropIndex('idx_leads_created_at');
            $table->dropIndex('idx_leads_phone');
            $table->dropIndex('idx_leads_forwarded_by');
            $table->dropIndex('idx_leads_managed_by');
            $table->dropIndex('idx_leads_status_created');
        });

        // Drop indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_created_at');
        });

        // Drop indexes from call_logs table
        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropIndex('idx_call_logs_lead_id');
            $table->dropIndex('idx_call_logs_agent_id');
            $table->dropIndex('idx_call_logs_created_at');
            $table->dropIndex('idx_call_logs_status');
        });

        // Drop indexes from attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_user_id');
            $table->dropIndex('idx_attendances_date');
            $table->dropIndex('idx_attendances_user_date');
        });

        // Drop indexes from carriers table
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropIndex('idx_carriers_lead_id');
            $table->dropIndex('idx_carriers_status');
        });

        // Drop indexes from salary_records table
        if (Schema::hasTable('salary_records')) {
            Schema::table('salary_records', function (Blueprint $table) {
                try { $table->dropIndex('idx_salary_records_user_id'); } catch (\Exception $e) {}
                try { $table->dropIndex('idx_salary_records_month'); } catch (\Exception $e) {}
                try { $table->dropIndex('idx_salary_records_year'); } catch (\Exception $e) {}
            });
        }

        // Drop indexes from notifications table
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                try { $table->dropIndex('idx_notifications_user_id'); } catch (\Exception $e) {}
                try { $table->dropIndex('idx_notifications_read_at'); } catch (\Exception $e) {}
                try { $table->dropIndex('idx_notifications_user_read'); } catch (\Exception $e) {}
            });
        }
    }
};
