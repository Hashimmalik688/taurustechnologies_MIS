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
        // Drop the old unique constraint
        Schema::table('petty_cash_ledgers', function (Blueprint $table) {
            $table->dropUnique(['serial_number']);
        });

        // Add a conditional unique constraint that excludes soft-deleted records
        Schema::table('petty_cash_ledgers', function (Blueprint $table) {
            $table->unique(['serial_number', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petty_cash_ledgers', function (Blueprint $table) {
            $table->dropUnique(['serial_number', 'deleted_at']);
            $table->unique(['serial_number']);
        });
    }
};
