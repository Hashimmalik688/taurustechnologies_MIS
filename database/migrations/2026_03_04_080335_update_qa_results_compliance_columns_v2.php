<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replace old C1-C12 compliance columns with new C1-C17 company-specific criteria.
 * Old: generic industry defaults. New: Call Handling + Application + Behavioral.
 * Existing rows' raw_ai_response JSON preserves original scoring data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            $table->dropColumn([
                'c1_recording_disclosure',
                'c2_agent_identity',
                'c3_carrier_named',
                'c4_not_government_program',
                'c5_product_type_stated',
                'c6_waiting_period',
                'c7_premium_amount',
                'c8_coverage_amount',
                'c9_health_questions',
                'c10_beneficiary_collected',
                'c11_prospect_verbal_consent',
                'c12_dnc_honored',
            ]);
        });

        Schema::table('qa_results', function (Blueprint $table) {
            // Call Handling
            $table->enum('c1_closer_consent', ['pass', 'fail', 'na'])->default('na')->after('compliance_pass');
            $table->enum('c2_agent_identity', ['pass', 'fail', 'na'])->default('na')->after('c1_closer_consent');
            $table->enum('c3_carrier_named', ['pass', 'fail', 'na'])->default('na')->after('c2_agent_identity');
            $table->enum('c4_product_type_stated', ['pass', 'fail', 'na'])->default('na')->after('c3_carrier_named');
            $table->enum('c5_health_questions_complete', ['pass', 'fail', 'na'])->default('na')->after('c4_product_type_stated');
            $table->enum('c6_proper_quote', ['pass', 'fail', 'na'])->default('na')->after('c5_health_questions_complete');
            $table->enum('c7_coverage_amount', ['pass', 'fail', 'na'])->default('na')->after('c6_proper_quote');
            $table->enum('c8_draft_date_confirmed', ['pass', 'fail', 'na'])->default('na')->after('c7_coverage_amount');
            $table->enum('c9_end_of_call_consent', ['pass', 'fail', 'na'])->default('na')->after('c8_draft_date_confirmed');
            $table->enum('c10_waiting_period', ['pass', 'fail', 'na'])->default('na')->after('c9_end_of_call_consent');
            // Application Requirements
            $table->enum('c11_application_info_collected', ['pass', 'fail', 'na'])->default('na')->after('c10_waiting_period');
            // Behavioral Compliance
            $table->enum('c12_customer_not_on_dnc', ['pass', 'fail', 'na'])->default('na')->after('c11_application_info_collected');
            $table->enum('c13_customer_not_aggressive', ['pass', 'fail', 'na'])->default('na')->after('c12_customer_not_on_dnc');
            $table->enum('c14_customer_not_disinterested', ['pass', 'fail', 'na'])->default('na')->after('c13_customer_not_aggressive');
            $table->enum('c15_no_pushy_sale', ['pass', 'fail', 'na'])->default('na')->after('c14_customer_not_disinterested');
            $table->enum('c16_appropriate_language', ['pass', 'fail', 'na'])->default('na')->after('c15_no_pushy_sale');
            $table->enum('c17_customer_not_abusive', ['pass', 'fail', 'na'])->default('na')->after('c16_appropriate_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            $table->dropColumn([
                'c1_closer_consent', 'c2_agent_identity', 'c3_carrier_named',
                'c4_product_type_stated', 'c5_health_questions_complete', 'c6_proper_quote',
                'c7_coverage_amount', 'c8_draft_date_confirmed', 'c9_end_of_call_consent',
                'c10_waiting_period', 'c11_application_info_collected', 'c12_customer_not_on_dnc',
                'c13_customer_not_aggressive', 'c14_customer_not_disinterested',
                'c15_no_pushy_sale', 'c16_appropriate_language', 'c17_customer_not_abusive',
            ]);
        });

        Schema::table('qa_results', function (Blueprint $table) {
            $table->enum('c1_recording_disclosure', ['pass', 'fail', 'na'])->default('na')->after('compliance_pass');
            $table->enum('c2_agent_identity', ['pass', 'fail', 'na'])->default('na')->after('c1_recording_disclosure');
            $table->enum('c3_carrier_named', ['pass', 'fail', 'na'])->default('na')->after('c2_agent_identity');
            $table->enum('c4_not_government_program', ['pass', 'fail', 'na'])->default('na')->after('c3_carrier_named');
            $table->enum('c5_product_type_stated', ['pass', 'fail', 'na'])->default('na')->after('c4_not_government_program');
            $table->enum('c6_waiting_period', ['pass', 'fail', 'na'])->default('na')->after('c5_product_type_stated');
            $table->enum('c7_premium_amount', ['pass', 'fail', 'na'])->default('na')->after('c6_waiting_period');
            $table->enum('c8_coverage_amount', ['pass', 'fail', 'na'])->default('na')->after('c7_premium_amount');
            $table->enum('c9_health_questions', ['pass', 'fail', 'na'])->default('na')->after('c8_coverage_amount');
            $table->enum('c10_beneficiary_collected', ['pass', 'fail', 'na'])->default('na')->after('c9_health_questions');
            $table->enum('c11_prospect_verbal_consent', ['pass', 'fail', 'na'])->default('na')->after('c10_beneficiary_collected');
            $table->enum('c12_dnc_honored', ['pass', 'fail', 'na'])->default('na')->after('c11_prospect_verbal_consent');
        });
    }
};
