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
        Schema::table('zoom_tokens', function (Blueprint $table) {
            $table->string('zoom_email')->nullable()->after('account_id');
            $table->string('zoom_name')->nullable()->after('zoom_email');
            $table->string('zoom_user_id')->nullable()->after('zoom_name');
            $table->unsignedSmallInteger('zoom_extension')->nullable()->after('zoom_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_tokens', function (Blueprint $table) {
            $table->dropColumn(['zoom_email', 'zoom_name', 'zoom_user_id', 'zoom_extension']);
        });
    }
};
