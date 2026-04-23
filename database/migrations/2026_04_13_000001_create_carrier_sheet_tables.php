<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Rates table (replicates the RATES sheet) ──────────────
        Schema::create('carrier_sheet_rates', function (Blueprint $table) {
            $table->id();
            $table->string('carrier_slug', 30)->unique();        // e.g. ta-f1, aig-y1
            $table->string('carrier_label', 80);                  // e.g. "T.A (F-1)"
            $table->string('partner_code', 10);                   // F-1, Y-1, E-1
            $table->decimal('level_rate', 6, 4)->nullable();      // e.g. 1.1500
            $table->decimal('graded_rate', 6, 4)->nullable();
            $table->decimal('gi_rate', 6, 4)->nullable();
            $table->decimal('modified_rate', 6, 4)->nullable();
            $table->unsignedTinyInteger('gi_multiplier')->default(9); // SEC uses 1
            $table->json('custom_policy_types')->nullable();      // AETNA overrides
            $table->string('title_color', 7)->default('#1A237E'); // hex
            $table->boolean('uses_hardcoded_rates')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            // ── Performance indexes ────────────────────────────────
            $table->index('is_active');  // filter active carriers
        });

        // ── Per-policy entries (rows 4–200 on each carrier sheet) ─
        Schema::create('carrier_sheet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_sheet_rate_id')
                  ->constrained('carrier_sheet_rates')
                  ->cascadeOnDelete();
            $table->unsignedInteger('sr_number')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('policy_number', 60)->nullable();
            $table->string('name', 120)->nullable();
            $table->string('face_value', 20)->nullable();         // "5K", "10K" etc.
            $table->decimal('premium', 10, 2)->default(0);
            $table->string('policy_type', 30)->nullable();        // level,graded,gi,modified,preferred,standard,super_preferred
            $table->string('status', 20)->default('approved');    // approved,paid,chargeback,declined
            $table->date('draft_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('commission', 12, 2)->nullable();     // server-calculated
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);        // server-calculated
            $table->decimal('chargeback_amount', 12, 2)->default(0);
            $table->decimal('rate_override', 6, 4)->nullable();   // AIG E-1 hardcoded rows
            $table->text('notes')->nullable();
            $table->date('period_month')->nullable();              // first-of-month for filtering
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // ── Performance indexes ────────────────────────────────
            $table->index(['carrier_sheet_rate_id', 'period_month']);
            $table->index(['carrier_sheet_rate_id', 'status']);
            $table->index('policy_number');                                    // lead lookup
            $table->index('name');                                             // lead lookup
            $table->index('entry_date');                                       // date filtering
            $table->index('deleted_at');                                       // soft delete queries
            $table->index(['carrier_sheet_rate_id', 'entry_date']);          // sheet + date queries
            $table->index(['carrier_sheet_rate_id', 'status', 'period_month']); // summary calculations
        });

        // ── Opening chargebacks (Row 3 per carrier per month) ─────
        Schema::create('carrier_sheet_opening_cbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_sheet_rate_id')
                  ->constrained('carrier_sheet_rates')
                  ->cascadeOnDelete();
            $table->date('period_month');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();

            // ── Performance indexes ────────────────────────────────
            $table->unique(['carrier_sheet_rate_id', 'period_month'], 'cs_opening_cb_rate_month_unique');
            $table->index('period_month');  // period filtering
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier_sheet_opening_cbs');
        Schema::dropIfExists('carrier_sheet_entries');
        Schema::dropIfExists('carrier_sheet_rates');
    }
};
