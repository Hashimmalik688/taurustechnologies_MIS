<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allowed_devices', function (Blueprint $table) {
            // status replaces is_active: pending / approved / disabled
            $table->string('status', 20)->default('pending')->after('id');
        });

        // Migrate existing data: is_active=true → approved, is_active=false → disabled
        DB::table('allowed_devices')->where('is_active', true)->update(['status' => 'approved']);
        DB::table('allowed_devices')->where('is_active', false)->update(['status' => 'disabled']);

        Schema::table('allowed_devices', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('allowed_devices', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('id');
        });

        DB::table('allowed_devices')->where('status', 'approved')->update(['is_active' => true]);
        DB::table('allowed_devices')->where('status', '!=', 'approved')->update(['is_active' => false]);

        Schema::table('allowed_devices', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
