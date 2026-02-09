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
        Schema::table('users', function (Blueprint $table) {
            // Flag to mark users who were mistakenly created as partners
            // This helps filter them out from user queries
            // New partners should ONLY exist in partners table
            $table->boolean('is_partner')->default(false)->after('email');
            $table->index('is_partner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_partner']);
            $table->dropColumn('is_partner');
        });
    }
};
