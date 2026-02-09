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
            $table->decimal('salary_advance', 10, 2)->default(0)->nullable()->after('bonus_payday_date');
            $table->decimal('tax_deduction', 10, 2)->default(0)->nullable()->after('salary_advance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salary_advance', 'tax_deduction']);
        });
    }
};
