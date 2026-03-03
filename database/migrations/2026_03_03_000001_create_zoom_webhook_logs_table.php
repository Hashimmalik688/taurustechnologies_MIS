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
        Schema::create('zoom_webhook_logs', function (Blueprint $table) {
            $table->id();
            
            // Webhook event details
            $table->string('event_type')->index(); // e.g., phone.call_ended, phone.recording_completed
            $table->string('zoom_call_id')->nullable()->index(); // Zoom's internal call ID
            $table->string('call_session_id')->nullable(); // Session identifier
            
            // Caller information
            $table->string('caller_number')->nullable()->index();
            $table->string('caller_did_number')->nullable();
            $table->string('caller_name')->nullable();
            $table->string('caller_email')->nullable();
            $table->string('caller_user_id')->nullable(); // Zoom user ID
            $table->string('caller_extension')->nullable();
            
            // Callee information
            $table->string('callee_number')->nullable()->index();
            $table->string('callee_did_number')->nullable();
            $table->string('callee_name')->nullable();
            $table->string('callee_email')->nullable();
            $table->string('callee_user_id')->nullable(); // Zoom user ID
            $table->string('callee_extension')->nullable();
            
            // Call details
            $table->enum('call_type', ['inbound', 'outbound', 'internal'])->nullable();
            $table->string('call_status')->nullable(); // answered, missed, voicemail, busy, etc.
            $table->string('call_result')->nullable(); // Zoom's result field
            $table->timestamp('call_start_time')->nullable()->index();
            $table->timestamp('call_end_time')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->timestamp('answer_time')->nullable(); // When call was answered
            $table->timestamp('ringing_start_time')->nullable();
            
            // Recording information
            $table->string('recording_url')->nullable();
            $table->string('recording_id')->nullable();
            $table->string('recording_file_path')->nullable();
            $table->bigInteger('recording_file_size')->nullable(); // bytes
            $table->string('recording_type')->nullable(); // automatic, on_demand
            $table->timestamp('recording_start_time')->nullable();
            $table->timestamp('recording_end_time')->nullable();
            
            // Transcription (if available)
            $table->text('transcript_text')->nullable();
            $table->string('transcript_url')->nullable();
            $table->string('transcript_file_path')->nullable();
            
            // Cost/billing info (if Zoom provides)
            $table->decimal('call_cost', 10, 4)->nullable();
            $table->string('call_rate')->nullable();
            
            // MIS Integration (optional link to internal records)
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('matched_call_log_id')->nullable()->constrained('call_logs')->nullOnDelete();
            
            // Raw webhook payload for debugging/audit
            $table->json('raw_payload')->nullable();
            
            // Metadata
            $table->boolean('is_processed')->default(false)->index(); // For async processing
            $table->text('processing_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['caller_number', 'call_start_time']);
            $table->index(['callee_number', 'call_start_time']);
            $table->index(['event_type', 'created_at']);
            $table->index(['lead_id', 'call_start_time']);
            $table->index(['agent_id', 'call_start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_webhook_logs');
    }
};
