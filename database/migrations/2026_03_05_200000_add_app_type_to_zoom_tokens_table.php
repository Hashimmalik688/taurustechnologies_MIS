<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zoom_tokens', function (Blueprint $table) {
            // 'user'  = user-managed app (existing per-user tokens)
            // 'admin' = admin-managed app (single token covering all extensions)
            $table->string('app_type')->default('user')->after('auth_type');
            $table->index('app_type');
        });
    }

    public function down(): void
    {
        Schema::table('zoom_tokens', function (Blueprint $table) {
            $table->dropIndex(['app_type']);
            $table->dropColumn('app_type');
        });
    }
};
