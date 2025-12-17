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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // who recorded it
            $table->foreignId('lead_id')->nullable()->constrained();
            $table->date('transaction_date');
            $table->enum('type', ['debit', 'credit']); // debit = money out, credit = money in
            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->nullable();
            $table->string('category')->nullable(); // commission, payment, refund, etc
            $table->text('description');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('transaction_date');
            $table->index('type');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
