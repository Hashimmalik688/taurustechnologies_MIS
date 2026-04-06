<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link the Paid Sales pipeline to the double-entry accounting journal.
     *
     * leads.ledger_journal_entry_id  → SET when a paid sale is posted to the ledger
     * ledger_journal_entries.lead_id → SET so the journal entry knows which lead it belongs to
     */
    public function up(): void
    {
        // Track which ledger journal entry was created for each paid lead
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('ledger_journal_entry_id')
                  ->nullable()
                  ->after('not_paid_comment');

            $table->foreign('ledger_journal_entry_id')
                  ->references('id')
                  ->on('ledger_journal_entries')
                  ->nullOnDelete();

            $table->index('ledger_journal_entry_id');
        });

        // Allow the journal entry to reference back to its originating lead
        Schema::table('ledger_journal_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')
                  ->nullable()
                  ->after('created_by');

            $table->foreign('lead_id')
                  ->references('id')
                  ->on('leads')
                  ->nullOnDelete();

            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['ledger_journal_entry_id']);
            $table->dropIndex(['ledger_journal_entry_id']);
            $table->dropColumn('ledger_journal_entry_id');
        });

        Schema::table('ledger_journal_entries', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropIndex(['lead_id']);
            $table->dropColumn('lead_id');
        });
    }
};
