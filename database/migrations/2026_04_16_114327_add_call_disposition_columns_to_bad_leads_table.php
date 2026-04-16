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
        Schema::table('bad_leads', function (Blueprint $table) {
            // Which button triggered this record: 'end_call' | 'save_exit'
            // NULL = legacy disposed calls (old Dispose Lead dropdown)
            $table->string('trigger', 20)->nullable()->after('notes');
            // When true, the lead is NOT removed from the calling system
            $table->boolean('keeps_in_calling')->default(false)->after('trigger');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bad_leads', function (Blueprint $table) {
            $table->dropColumn(['trigger', 'keeps_in_calling']);
        });
    }
};
