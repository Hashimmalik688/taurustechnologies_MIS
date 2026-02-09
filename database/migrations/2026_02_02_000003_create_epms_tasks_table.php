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
        Schema::create('epms_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->foreignId('milestone_id')->nullable()->constrained('epms_milestones')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Task Details
            $table->enum('status', ['todo', 'in-progress', 'review', 'completed'])->default('todo');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('task_type', ['standard', 'revision'])->default('standard');
            
            // Dates
            $table->date('start_date');
            $table->date('end_date');
            $table->date('completed_at')->nullable();
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            
            // Progress
            $table->integer('progress')->default(0); // 0-100
            $table->integer('estimated_hours')->default(0);
            $table->integer('actual_hours')->default(0);
            
            // Positioning for Gantt chart
            $table->integer('order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('project_id');
            $table->index('milestone_id');
            $table->index('status');
            $table->index('assigned_to');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epms_tasks');
    }
};
