<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_compliance_flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qa_call_id')->index();
            $table->unsignedBigInteger('qa_result_id')->index();
            $table->unsignedBigInteger('agent_user_id')->nullable()->index();
            $table->string('check_code', 20)->index()->comment('e.g. C1, C2, C3...');
            $table->string('check_name')->comment('e.g. recording_disclosure');
            $table->string('check_label')->comment('Human readable label');
            $table->text('ai_reasoning')->nullable();
            $table->timestamp('flagged_at');
            $table->timestamps();

            $table->foreign('qa_call_id')->references('id')->on('qa_calls')->cascadeOnDelete();
            $table->foreign('qa_result_id')->references('id')->on('qa_results')->cascadeOnDelete();
            $table->foreign('agent_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_compliance_flags');
    }
};
