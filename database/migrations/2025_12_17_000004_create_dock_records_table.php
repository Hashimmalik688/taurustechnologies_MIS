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
        Schema::create('dock_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('docked_by')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->text('reason');
            $table->date('dock_date');
            $table->tinyInteger('dock_month');
            $table->integer('dock_year');
            $table->enum('status', ['active', 'cancelled', 'applied'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['user_id', 'dock_month', 'dock_year']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dock_records');
    }
};
