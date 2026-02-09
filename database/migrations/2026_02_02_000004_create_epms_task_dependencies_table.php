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
        Schema::create('epms_task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('epms_tasks')->onDelete('cascade');
            $table->foreignId('depends_on_task_id')->constrained('epms_tasks')->onDelete('cascade');
            $table->enum('dependency_type', ['finish-to-start', 'start-to-start', 'finish-to-finish', 'start-to-finish'])->default('finish-to-start');
            $table->integer('lag_days')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('task_id');
            $table->index('depends_on_task_id');
            
            // Unique constraint to prevent duplicate dependencies
            $table->unique(['task_id', 'depends_on_task_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epms_task_dependencies');
    }
};
