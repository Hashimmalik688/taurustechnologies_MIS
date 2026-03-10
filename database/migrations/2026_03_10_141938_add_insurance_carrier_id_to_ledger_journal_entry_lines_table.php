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
        Schema::table('ledger_journal_entry_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('insurance_carrier_id')
                  ->nullable()
                  ->after('partner_id');

            $table->foreign('insurance_carrier_id')
                  ->references('id')
                  ->on('insurance_carriers')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_journal_entry_lines', function (Blueprint $table) {
            $table->dropForeign(['insurance_carrier_id']);
            $table->dropColumn('insurance_carrier_id');
        });
    }
};
