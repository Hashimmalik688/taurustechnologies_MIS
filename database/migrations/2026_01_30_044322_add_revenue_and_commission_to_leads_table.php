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
            // Agent commission and revenue tracking
            $table->decimal('agent_commission', 15, 2)->nullable()->after('assigned_agent_set_at')->comment('Calculated commission: Monthly Premium × 9 × Settlement %');
            $table->decimal('agent_revenue', 15, 2)->nullable()->after('agent_commission')->comment('Final revenue after calculations');
            $table->decimal('settlement_percentage', 5, 2)->nullable()->after('agent_revenue')->comment('Settlement % used for commission calculation');
            $table->text('commission_calculation_notes')->nullable()->after('settlement_percentage')->comment('Details about how commission was calculated');
            $table->timestamp('commission_calculated_at')->nullable()->after('commission_calculation_notes')->comment('When commission was calculated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'agent_commission',
                'agent_revenue',
                'settlement_percentage',
                'commission_calculation_notes',
                'commission_calculated_at',
            ]);
        });
    }
};
