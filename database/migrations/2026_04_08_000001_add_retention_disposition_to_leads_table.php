<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // New retention disposition field — replaces ret_action_status in the UI.
            // Values: pending | retained | resold | rewrite | recalled_to_closer | cancelled
            $table->string('retention_disposition', 50)
                  ->nullable()
                  ->default('pending')
                  ->after('ret_action_updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('retention_disposition');
        });
    }
};
