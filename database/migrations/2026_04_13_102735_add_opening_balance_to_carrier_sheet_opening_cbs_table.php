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
        Schema::table('carrier_sheet_opening_cbs', function (Blueprint $table) {
            $table->decimal('opening_balance', 12, 2)->default(0)->after('amount')->comment('Starting balance carried forward (+/−)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrier_sheet_opening_cbs', function (Blueprint $table) {
            $table->dropColumn('opening_balance');
        });
    }
};
