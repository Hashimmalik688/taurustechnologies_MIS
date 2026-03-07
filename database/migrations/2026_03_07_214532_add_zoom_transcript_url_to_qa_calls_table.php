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
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->string('zoom_transcript_url')->nullable()->after('recording_url');
            $table->string('transcript_source')->nullable()->default(null)
                ->comment('zoom or whisper — null means not yet transcribed')
                ->after('transcript_diarized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->dropColumn(['zoom_transcript_url', 'transcript_source']);
        });
    }
};
