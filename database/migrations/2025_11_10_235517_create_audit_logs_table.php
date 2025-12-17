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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // User who performed the action
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_email')->nullable(); // Store email for reference even if user is deleted
            
            // Action details
            $table->string('action'); // e.g., 'user_created', 'password_reset', 'role_changed', 'login', 'logout'
            $table->string('model')->nullable(); // e.g., 'User', 'Lead', 'SalaryRecord'
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            
            // Request details
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable(); // Browser info
            
            // Additional details
            $table->json('changes')->nullable(); // Old and new values for updates
            $table->text('description')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for fast lookup
            $table->index('user_id');
            $table->index('action');
            $table->index('model');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
