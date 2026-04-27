<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen rate_override from decimal(6,4) — max 99.9999 — to decimal(10,4)
     * so values like 134 or 1500 are accepted without overflow.
     * This column is carrier-sheet-only and is never linked to MIS calculations.
     */
    public function up(): void
    {
        Schema::table('carrier_sheet_entries', function (Blueprint $table) {
            $table->decimal('rate_override', 10, 4)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('carrier_sheet_entries', function (Blueprint $table) {
            $table->decimal('rate_override', 6, 4)->nullable()->change();
        });
    }
};
