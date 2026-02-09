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
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->unsignedTinyInteger('salary_year');
            $table->unsignedTinyInteger('salary_month');
            $table->enum('component_type', ['basic', 'bonus'])->default('basic');
            $table->date('payment_date');
            
            // Salary amounts
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->decimal('calculated_amount', 10, 2)->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->nullable();
            
            // Sales bonus specifics (if bonus component)
            $table->integer('target_sales')->nullable();
            $table->integer('actual_sales')->nullable();
            $table->integer('chargeback_count')->nullable();
            $table->integer('net_approved_sales')->nullable();
            $table->integer('extra_sales')->nullable();
            $table->decimal('bonus_per_extra_sale', 8, 2)->nullable();
            
            // Attendance specifics (if basic component)
            $table->integer('working_days')->nullable();
            $table->integer('present_days')->nullable();
            $table->integer('leave_days')->nullable();
            $table->integer('late_days')->nullable();
            $table->decimal('daily_salary', 8, 2)->nullable();
            $table->decimal('attendance_bonus', 10, 2)->default(0);
            $table->decimal('attendance_deduction', 10, 2)->default(0);
            
            // Dock deductions (basic component only)
            $table->decimal('dock_deductions', 10, 2)->default(0);
            
            // Manual deductions
            $table->decimal('manual_deductions', 10, 2)->default(0);
            
            // Workflow
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid'])->default('draft');
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Unique constraint per component per month
            $table->unique(['user_id', 'salary_year', 'salary_month', 'component_type', 'payment_date']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
