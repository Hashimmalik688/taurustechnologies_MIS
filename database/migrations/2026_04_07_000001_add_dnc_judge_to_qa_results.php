<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            $table->string('dnc_risk_level', 10)->nullable()->after('void_risk_reason')
                ->comment('DNC Judge standalone risk level: HIGH | MEDIUM | LOW | NONE');
            $table->string('dnc_judge_verdict', 30)->nullable()->after('dnc_risk_level')
                ->comment('DNC Judge verdict: Litigator | DNC Risk | Aggressive Opt-Out | Clean');
            $table->text('dnc_judge_reasoning')->nullable()->after('dnc_judge_verdict')
                ->comment('AI explanation of which signals triggered the DNC Judge verdict');
        });
    }

    public function down(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            $table->dropColumn(['dnc_risk_level', 'dnc_judge_verdict', 'dnc_judge_reasoning']);
        });
    }
};
