<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qa_call_id')->unique()->index();
            $table->enum('disposition', [
                'COMPLIANCE_FAIL', 'VOID_RISK', 'EXCELLENT', 'GOOD', 'AVERAGE', 'POOR'
            ])->index();
            $table->decimal('total_score', 5, 2)->default(0)->index();
            $table->boolean('compliance_pass')->default(false)->index();

            // Compliance checks — pass/fail/na for each
            $table->enum('c1_recording_disclosure', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c2_agent_identity', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c3_carrier_named', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c4_not_government_program', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c5_product_type_stated', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c6_waiting_period', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c7_premium_amount', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c8_coverage_amount', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c9_health_questions', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c10_beneficiary_collected', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c11_prospect_verbal_consent', ['pass', 'fail', 'na'])->default('na');
            $table->enum('c12_dnc_honored', ['pass', 'fail', 'na'])->default('na');

            // Sales quality scores (1-10 each)
            $table->unsignedTinyInteger('score_opening')->default(0);
            $table->unsignedTinyInteger('score_discovery')->default(0);
            $table->unsignedTinyInteger('score_presentation')->default(0);
            $table->unsignedTinyInteger('score_objection_handling')->default(0);
            $table->unsignedTinyInteger('score_closing')->default(0);
            $table->unsignedTinyInteger('score_soft_skills')->default(0);
            $table->unsignedTinyInteger('score_call_control')->default(0);

            // AI output
            $table->text('coaching_notes')->nullable();
            $table->string('top_issue')->nullable();
            $table->json('strengths')->nullable();
            $table->json('improvements')->nullable();
            $table->text('void_risk_reason')->nullable();
            $table->json('compliance_failures')->nullable();
            $table->json('raw_ai_response')->nullable();

            $table->timestamps();

            $table->foreign('qa_call_id')->references('id')->on('qa_calls')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_results');
    }
};
