<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE call_logs MODIFY COLUMN call_status ENUM('completed','missed','rejected','busy','no_answer','voicemail','connected','ringing') DEFAULT 'completed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any rows using new values back to 'completed'
        DB::table('call_logs')->where('call_status', 'connected')->update(['call_status' => 'completed']);
        DB::table('call_logs')->where('call_status', 'ringing')->update(['call_status' => 'no_answer']);
        DB::statement("ALTER TABLE call_logs MODIFY COLUMN call_status ENUM('completed','missed','rejected','busy','no_answer','voicemail') DEFAULT 'completed'");
    }
};
