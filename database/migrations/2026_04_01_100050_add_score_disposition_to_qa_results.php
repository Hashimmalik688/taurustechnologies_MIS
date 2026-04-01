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
            $table->string('score_disposition', 20)->nullable()->after('disposition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qa_results', function (Blueprint $table) {
            $table->dropColumn('score_disposition');
        });
    }
};
