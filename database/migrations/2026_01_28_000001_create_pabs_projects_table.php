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
        Schema::create('pabs_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique(); // Format: SEC-01-2026-0001
            $table->string('project_name');
            $table->text('description');
            $table->unsignedTinyInteger('section_id'); // 1-11 representing the 11 domains
            $table->enum('status', [
                'DRAFT',
                'SCOPING',
                'QUOTING',
                'PENDING APPROVAL',
                'BUDGET ALLOCATED',
                'IN PROGRESS',
                'COMPLETED',
                'ARCHIVED'
            ])->default('DRAFT');
            
            // Scoping
            $table->string('scoping_document_path')->nullable(); // Path to uploaded PDF/JPG
            $table->timestamp('scoping_completed_at')->nullable();
            
            // Quoting
            $table->decimal('vendor_a_quote', 12, 2)->nullable();
            $table->string('vendor_a_name')->nullable();
            $table->decimal('vendor_b_quote', 12, 2)->nullable();
            $table->string('vendor_b_name')->nullable();
            $table->decimal('vendor_c_quote', 12, 2)->nullable();
            $table->string('vendor_c_name')->nullable();
            
            // Approval
            $table->enum('approval_status', ['APPROVED', 'REJECTED', 'CLARIFICATION NEEDED'])->nullable();
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW'])->nullable();
            $table->decimal('approved_budget', 12, 2)->nullable();
            $table->date('target_deadline')->nullable();
            $table->text('approval_notes')->nullable();
            
            // Execution
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->decimal('total_budget', 12, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Variance tracking
            $table->boolean('variance_flagged')->default(false);
            $table->text('variance_notes')->nullable();
            
            // User tracking
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('scoping_lead_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('allocated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('allocated_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('project_code');
            $table->index('section_id');
            $table->index('status');
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pabs_projects');
    }
};
