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
        Schema::disableForeignKeyConstraints();
        
        Schema::create('pabs_project_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pabs_projects')->onDelete('cascade');
            $table->foreignId('approved_by')->constrained('users')->onDelete('restrict');
            $table->enum('action', ['APPROVED', 'REJECTED', 'CLARIFICATION NEEDED']);
            $table->text('comments')->nullable();
            $table->decimal('approved_budget', 12, 2)->nullable();
            $table->date('target_deadline')->nullable();
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW'])->nullable();
            $table->timestamp('approved_at');
            $table->timestamps();
            
            // Indexes
            $table->index('project_id');
            $table->index('approved_by');
            $table->index('approved_at');
        });
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pabs_project_approvals');
    }
};
