<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number', 20)->unique(); // e.g. JE-2026-0001
            $table->date('entry_date');
            $table->enum('type', ['sale', 'payment_received', 'opening_balance', 'general']);
            $table->string('reference', 100)->nullable();
            $table->text('description');
            $table->boolean('is_posted')->default(true);
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index('entry_date');
            $table->index('type');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_journal_entries');
    }
};
