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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('device_fingerprint', 100)->nullable();
            $table->string('device_name', 255)->nullable();
        });
        
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('device_fingerprint', 100)->nullable();
            $table->string('device_name', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['device_fingerprint', 'device_name']);
        });
        
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['device_fingerprint', 'device_name']);
        });
    }
};
