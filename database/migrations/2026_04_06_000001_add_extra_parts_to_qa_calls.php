<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add extra_parts JSON column to qa_calls to support 3+ part recordings.
     * Each element: { audio_file_path, audio_original_name, assemblyai_transcript_id }
     */
    public function up(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->json('extra_parts')->nullable()->after('assemblyai_transcript_id_2');
        });
    }

    public function down(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->dropColumn('extra_parts');
        });
    }
};
