<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allowed_devices', function (Blueprint $table) {
            $table->string('name')->nullable()->after('label');
            $table->string('last_seen_ip', 45)->nullable()->after('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('allowed_devices', function (Blueprint $table) {
            $table->dropColumn(['name', 'last_seen_ip']);
        });
    }
};
