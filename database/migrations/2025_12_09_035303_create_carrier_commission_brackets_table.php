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
        Schema::create('carrier_commission_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_carrier_id')->constrained('insurance_carriers')->onDelete('cascade');
            $table->integer('age_min'); // Minimum age for this bracket (e.g., 18, 40, 60)
            $table->integer('age_max'); // Maximum age for this bracket (e.g., 39, 59, 85)
            $table->decimal('commission_percentage', 5, 2); // Commission % for this age bracket
            $table->text('notes')->nullable(); // Optional notes for this bracket
            $table->timestamps();
            
            // Ensure logical age ranges
            $table->index(['insurance_carrier_id', 'age_min', 'age_max']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_commission_brackets');
    }
};
