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
        Schema::create('insurance_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Company name like "American Amicable", "Foresters"
            $table->decimal('base_commission_percentage', 5, 2)->nullable(); // Base commission %
            $table->integer('age_min')->nullable(); // Min age for age-based commission rules
            $table->integer('age_max')->nullable(); // Max age for age-based commission rules
            $table->text('plan_types')->nullable(); // JSON array of plan types this carrier offers
            $table->text('calculation_notes')->nullable(); // Notes about commission calculation formulas
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_carriers');
    }
};
