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
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_record_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'tax', 'insurance', 'loan', 'absence', 'other'
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->boolean('is_percentage')->default(false); // if true, amount is percentage of basic salary
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_deductions');
    }
};
