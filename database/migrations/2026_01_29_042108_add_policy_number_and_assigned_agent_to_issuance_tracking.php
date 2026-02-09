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
            $table->string('issued_policy_number')->nullable()->after('issuance_date');
            $table->unsignedBigInteger('assigned_agent_id')->nullable()->after('issued_policy_number');
            $table->timestamp('policy_number_set_at')->nullable()->after('assigned_agent_id');
            $table->timestamp('assigned_agent_set_at')->nullable()->after('policy_number_set_at');
            
            // Foreign key constraint for assigned_agent_id
            $table->foreign('assigned_agent_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['assigned_agent_id']);
            $table->dropColumn(['issued_policy_number', 'assigned_agent_id', 'policy_number_set_at', 'assigned_agent_set_at']);
        });
    }
};
