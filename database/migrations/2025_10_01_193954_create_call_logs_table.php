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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Call details
            $table->string('phone_number');
            $table->enum('call_type', ['inbound', 'outbound'])->default('outbound');
            $table->enum('call_status', ['completed', 'missed', 'rejected', 'busy', 'no_answer', 'voicemail'])->default('completed');
            $table->dateTime('call_start_time');
            $table->dateTime('call_end_time')->nullable();
            $table->integer('duration_seconds')->default(0); // Call duration in seconds

            // Call outcome
            $table->enum('outcome', [
                'interested',
                'not_interested',
                'callback_requested',
                'information_sent',
                'sale_made',
                'no_answer',
                'wrong_number',
                'do_not_call'
            ])->nullable();

            // Recording and notes
            $table->string('recording_url')->nullable();
            $table->text('notes')->nullable();
            $table->text('summary')->nullable();

            // Follow-up
            $table->dateTime('follow_up_date')->nullable();
            $table->boolean('needs_follow_up')->default(false);

            // System info
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('lead_id');
            $table->index('agent_id');
            $table->index('created_by');
            $table->index('call_start_time');
            $table->index('call_status');
            $table->index('outcome');
            $table->index('needs_follow_up');
            $table->index(['agent_id', 'call_start_time']);
            $table->index(['lead_id', 'call_start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
