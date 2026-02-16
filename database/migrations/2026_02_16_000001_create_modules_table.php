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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name (e.g., "Employee Management")
            $table->string('slug')->unique(); // Unique identifier (e.g., "ems")
            $table->text('description')->nullable(); // Brief description of what the module does
            $table->string('category')->nullable(); // Category grouping: HR, Sales, Operations, Settings, etc.
            $table->integer('sort_order')->default(0); // For ordering in UI
            $table->boolean('is_active')->default(true); // Can disable modules without deleting
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
