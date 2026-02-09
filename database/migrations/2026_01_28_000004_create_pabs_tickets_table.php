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
        
        Schema::create('pabs_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique(); // Format: TICKET-2026-0001
            $table->string('subject');
            $table->text('description');
            $table->unsignedTinyInteger('section_id'); // 1-11 representing the 11 domains
            $table->foreignId('project_id')->nullable()->constrained('pabs_projects')->onDelete('set null');
            $table->decimal('total_cost', 12, 2)->nullable(); // Total cost associated with ticket
            $table->decimal('quote_amount', 12, 2)->nullable();
            $table->enum('status', ['OPEN', 'IN PROGRESS', 'ON HOLD', 'RESOLVED', 'CLOSED'])->default('OPEN');
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW'])->default('MEDIUM');
            $table->enum('approval_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('ticket_code');
            $table->index('section_id');
            $table->index('project_id');
            $table->index('status');
            $table->index('created_by');
            $table->index('assigned_to');
        });
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pabs_tickets');
    }
};
