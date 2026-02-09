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
        Schema::create('epms_external_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('cost_type', ['asset', 'api', 'subcontractor', 'software', 'hardware', 'other'])->default('other');
            $table->decimal('amount', 15, 2);
            $table->enum('currency', ['USD', 'PKR'])->default('USD');
            $table->date('incurred_date')->nullable();
            $table->string('vendor_name')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_period', ['monthly', 'quarterly', 'yearly'])->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('project_id');
            $table->index('cost_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epms_external_costs');
    }
};
