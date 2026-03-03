<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_calls', function (Blueprint $table) {
            $table->id();
            $table->string('zoom_call_id')->unique()->index();
            $table->unsignedBigInteger('call_log_id')->nullable()->index();
            $table->unsignedBigInteger('agent_user_id')->nullable()->index();
            $table->string('agent_name')->nullable();
            $table->string('agent_email')->nullable();
            $table->string('zoom_user_id')->nullable()->index();
            $table->string('caller_number')->nullable();
            $table->string('callee_number')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->timestamp('call_start_time')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('local_recording_path')->nullable();
            $table->text('transcript_plain')->nullable();
            $table->longText('transcript_diarized')->nullable();
            $table->enum('processing_status', [
                'pending', 'downloading', 'transcribing', 'scoring', 'completed', 'failed', 'skipped'
            ])->default('pending')->index();
            $table->string('failure_reason')->nullable();
            $table->string('scored_by')->default('gemini')->comment('gemini or claude');
            $table->integer('retry_count')->default(0);
            $table->timestamps();

            $table->foreign('call_log_id')->references('id')->on('call_logs')->nullOnDelete();
            $table->foreign('agent_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_calls');
    }
};
