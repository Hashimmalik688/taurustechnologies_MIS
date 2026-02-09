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
        Schema::create('epms_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->date('completed_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'missed'])->default('pending');
            $table->integer('order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('project_id');
            $table->index('due_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epms_milestones');
    }
};
