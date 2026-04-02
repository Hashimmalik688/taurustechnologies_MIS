<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->string('assemblyai_transcript_id_2')->nullable()->after('assemblyai_transcript_id');
            $table->string('audio_file_path_2')->nullable()->after('audio_file_path');
            $table->string('audio_original_name_2')->nullable()->after('audio_original_name');
        });
    }

    public function down(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->dropColumn(['assemblyai_transcript_id_2', 'audio_file_path_2', 'audio_original_name_2']);
        });
    }
};
