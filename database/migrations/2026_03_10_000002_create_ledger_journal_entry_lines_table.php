<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')
                  ->constrained('ledger_journal_entries')
                  ->cascadeOnDelete();
            $table->foreignId('account_id')
                  ->constrained('chart_of_accounts')
                  ->restrictOnDelete();
            $table->foreignId('partner_id')
                  ->nullable()
                  ->constrained('partners')
                  ->nullOnDelete();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('description', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('journal_entry_id');
            $table->index('account_id');
            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_journal_entry_lines');
    }
};
