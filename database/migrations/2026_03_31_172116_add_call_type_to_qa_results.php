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
        Schema::table('qa_results', function (Blueprint $table) {
            $table->string('call_type', 20)->nullable()->default(null)->after('raw_ai_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            $table->dropColumn('call_type');
        });
    }
};
