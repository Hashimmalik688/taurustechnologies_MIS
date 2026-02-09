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
        Schema::create('manual_payroll_entries', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->date('join_date')->nullable();
            $table->integer('payroll_month'); // Month for which payroll is calculated (1-12)
            $table->integer('payroll_year'); // Year for which payroll is calculated
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('punctuality_bonus', 10, 2)->default(0);
            $table->integer('full_days')->default(0);
            $table->integer('half_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->boolean('is_qualified')->default(false);
            $table->decimal('dock_amount', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->decimal('salary_advance', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_payroll_entries');
    }
};
