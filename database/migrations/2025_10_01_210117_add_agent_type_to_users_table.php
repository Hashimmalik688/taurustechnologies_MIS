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
            $table->enum('agent_type', ['employee', 'us_agent', 'vendor'])->default('employee')->after('employment_status');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('bonus_per_extra_sale');
            $table->string('company_name')->nullable()->after('name');
            $table->text('vendor_notes')->nullable()->after('address');

            $table->index('agent_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['agent_type']);
            $table->dropColumn(['agent_type', 'commission_rate', 'company_name', 'vendor_notes']);
        });
    }
};
