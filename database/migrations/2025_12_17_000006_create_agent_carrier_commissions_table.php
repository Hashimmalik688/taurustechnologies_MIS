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
        Schema::create('agent_carrier_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Agent
            $table->foreignId('insurance_carrier_id')->constrained()->onDelete('cascade');
            $table->decimal('commission_percentage', 5, 2); // e.g., 15.50%
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint: one commission rate per agent per carrier
            $table->unique(['user_id', 'insurance_carrier_id'], 'agent_carrier_unique');
            
            // Index for faster lookups
            $table->index('user_id');
            $table->index('insurance_carrier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_carrier_commissions');
    }
};
