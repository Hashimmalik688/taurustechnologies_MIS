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
        Schema::table('salary_records', function (Blueprint $table) {
            // Add chargeback tracking
            $table->integer('chargeback_count')->default(0)->after('actual_sales');
            $table->integer('net_approved_sales')->default(0)->after('chargeback_count');
            $table->integer('next_month_target_adjustment')->default(0)->after('net_approved_sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropColumn(['chargeback_count', 'net_approved_sales', 'next_month_target_adjustment']);
        });
    }
};
