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
        Schema::create('epms_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Client Information
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->enum('region', ['US', 'PK'])->default('US');
            
            // Financial Details
            $table->enum('currency', ['USD', 'PKR'])->default('USD');
            $table->decimal('contract_value', 15, 2);
            $table->decimal('external_costs', 15, 2)->default(0);
            $table->decimal('gross_profit', 15, 2)->default(0);
            $table->decimal('margin_percentage', 5, 2)->default(0);
            
            // Dates
            $table->date('start_date');
            $table->date('deadline');
            $table->date('estimated_completion_date')->nullable();
            
            // Project Status
            $table->enum('status', ['planning', 'in-progress', 'on-hold', 'completed', 'cancelled'])->default('planning');
            $table->enum('health_score', ['green', 'yellow', 'red'])->default('green');
            
            // Analytics
            $table->decimal('project_velocity', 8, 2)->default(0);
            $table->integer('scope_creep_count')->default(0);
            $table->integer('total_tasks')->default(0);
            $table->integer('completed_tasks')->default(0);
            $table->integer('revision_tasks')->default(0);
            
            // Ownership
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('project_manager_id')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('region');
            $table->index('health_score');
            $table->index('deadline');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epms_projects');
    }
};
