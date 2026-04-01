<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            // Stores the AssemblyAI transcript job ID so we can poll its status
            $table->string('assemblyai_transcript_id')->nullable()->after('transcript_source')->index();

            // 'queued' | 'processing' | 'completed' | 'error' — mirrors AssemblyAI status
            $table->string('assemblyai_status')->nullable()->after('assemblyai_transcript_id');

            // Path to the uploaded audio file in Laravel storage (uploaded_calls disk)
            $table->string('audio_file_path')->nullable()->after('assemblyai_status');

            // Original filename as uploaded by the QA reviewer
            $table->string('audio_original_name')->nullable()->after('audio_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->dropColumn([
                'assemblyai_transcript_id',
                'assemblyai_status',
                'audio_file_path',
                'audio_original_name',
            ]);
        });
    }
};
