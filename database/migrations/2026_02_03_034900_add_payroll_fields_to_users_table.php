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
            $table->boolean('is_qualified_for_punctuality')->default(true)->after('punctuality_bonus');
            $table->integer('working_days_monthly')->default(22)->after('is_qualified_for_punctuality');
            $table->decimal('override_punctuality_bonus', 8, 2)->default(0)->after('working_days_monthly');
            $table->decimal('other_deductions', 10, 2)->default(0)->after('tax_deduction');
            $table->decimal('other_allowances', 10, 2)->default(0)->after('other_deductions');
            $table->string('payroll_notes', 500)->nullable()->after('other_allowances');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_qualified_for_punctuality',
                'working_days_monthly',
                'override_punctuality_bonus',
                'other_deductions',
                'other_allowances',
                'payroll_notes'
            ]);
        });
    }
};
