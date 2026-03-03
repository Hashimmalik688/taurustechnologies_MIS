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
            $table->string('zoom_call_log_id')->nullable()->after('zoom_user_id')
                ->comment('Zoom-side call_log_id (UUID) from recording webhook — used for file_url download fallback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qa_calls', function (Blueprint $table) {
            $table->dropColumn('zoom_call_log_id');
        });
    }
};
