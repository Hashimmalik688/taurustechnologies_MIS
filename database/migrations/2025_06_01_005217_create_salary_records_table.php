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
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->year('salary_year');
            $table->tinyInteger('salary_month'); // 1-12
            $table->decimal('basic_salary', 10, 2);
            $table->integer('target_sales');
            $table->integer('actual_sales')->default(0);
            $table->integer('extra_sales')->default(0); // calculated: actual_sales - target_sales (if positive)
            $table->decimal('bonus_per_extra_sale', 8, 2)->default(0);
            $table->decimal('total_bonus', 10, 2)->default(0); // calculated: extra_sales * bonus_per_extra_sale
            $table->decimal('total_deductions', 10, 2)->default(0); // sum of all deductions
            $table->decimal('gross_salary', 10, 2)->default(0); // basic_salary + total_bonus
            $table->decimal('net_salary', 10, 2)->default(0); // gross_salary - total_deductions
            $table->integer('working_days')->default(22);
            $table->integer('present_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->decimal('daily_salary', 8, 2)->default(0);
            $table->decimal('attendance_bonus', 10, 2)->default(0);
            $table->decimal('attendance_deduction', 10, 2)->default(0);
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Ensure one salary record per employee per month
            $table->unique(['user_id', 'salary_year', 'salary_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
