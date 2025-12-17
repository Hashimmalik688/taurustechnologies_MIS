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
        Schema::table('users', function (Blueprint $table) {
            // User status tracking
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('country');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->string('current_session_ip')->nullable()->after('last_login_ip');
            
            // Time tracking
            $table->timestamp('time_in')->nullable()->after('current_session_ip');
            $table->timestamp('time_out')->nullable()->after('time_in');
            
            // Indexes for quick lookups
            $table->index('status');
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['last_login_at']);
            $table->dropColumn([
                'status',
                'last_login_at',
                'last_login_ip',
                'current_session_ip',
                'time_in',
                'time_out',
            ]);
        });
    }
};
