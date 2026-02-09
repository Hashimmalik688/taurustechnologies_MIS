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
        // Drop the old foreign key constraint with cascade delete
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Re-add the foreign key without cascade delete
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); // Prevent deletion if attendance records exist
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new foreign key constraint
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Re-add the old foreign key with cascade delete
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
