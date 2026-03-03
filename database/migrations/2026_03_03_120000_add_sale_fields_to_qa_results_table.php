<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            // AI-extracted fields from transcript
            $table->string('customer_name')->nullable()->after('raw_ai_response')
                ->comment('Customer name extracted from transcript by AI');
            $table->string('closer_name_extracted')->nullable()->after('customer_name')
                ->comment('Closer name extracted from transcript by AI (cross-reference with qa_calls.agent_name)');
            $table->boolean('is_sale')->default(false)->after('closer_name_extracted')
                ->index()->comment('Whether a sale was made during this call');
            $table->decimal('sale_amount', 10, 2)->nullable()->after('is_sale')
                ->comment('Coverage/death benefit amount if sale was made');
            $table->decimal('monthly_premium', 8, 2)->nullable()->after('sale_amount')
                ->comment('Monthly premium amount if sale was made');
            $table->string('carrier_name')->nullable()->after('monthly_premium')
                ->comment('Insurance carrier name mentioned in call');
            $table->string('policy_type')->nullable()->after('carrier_name')
                ->comment('e.g., Whole Life, Term, Graded, Modified');
            $table->string('customer_state')->nullable()->after('policy_type')
                ->comment('Customer state if mentioned');
        });
    }

    public function down(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'closer_name_extracted',
                'is_sale',
                'sale_amount',
                'monthly_premium',
                'carrier_name',
                'policy_type',
                'customer_state',
            ]);
        });
    }
};
