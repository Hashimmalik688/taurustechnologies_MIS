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
        Schema::create('agent_carrier_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Agent
            $table->foreignId('insurance_carrier_id')->constrained()->onDelete('cascade');
            $table->string('state', 2); // US State code (e.g., 'FL', 'TX')
            $table->decimal('settlement_level_pct', 5, 2)->nullable(); // Level %
            $table->decimal('settlement_graded_pct', 5, 2)->nullable(); // Graded %
            $table->decimal('settlement_gi_pct', 5, 2)->nullable(); // GI %
            $table->decimal('settlement_modified_pct', 5, 2)->nullable(); // Modified %
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint: one record per agent per carrier per state
            $table->unique(['user_id', 'insurance_carrier_id', 'state'], 'agent_carrier_state_unique');
            
            // Indexes for faster lookups
            $table->index('user_id');
            $table->index('insurance_carrier_id');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_carrier_states');
    }
};
