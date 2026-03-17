<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sales Pipeline Stage Migration
 *
 * Adds all columns needed for the 7-stage sales pipeline:
 *   Submission → Pendings Approved → Pending Contract → Issued
 *     → Followup Done → Pending Draft → Paid Sales
 *
 * Plus two retention-owned exception states:
 *   Not Issued  (blocks at Pendings Approved, dispositioned by Retention)
 *   Not Paid    (blocks at Pending Draft, FDFP-typed by Retention)
 *
 * And one terminal-but-re-dialable state:
 *   Policy Died (lead reset to active → back in Ravens queue)
 *
 * ─────────────────────────────────────────────────────────────────
 * DB NAMING CONVENTION (enforced from this migration forward)
 * ─────────────────────────────────────────────────────────────────
 *  - Stage-specific FK user references  → {stage}_by_id  (bigint FK)
 *  - Stage-specific timestamps          → {stage}_at     (timestamp)
 *  - Stage-specific status/reason enum  → {stage}_{noun} (enum)
 *
 *  Examples:
 *    pending_contract_at, pending_contract_by_id
 *    not_issued_at,       not_issued_by_id,       not_issued_disposition
 *    followup_done_at,    followup_done_by_id
 *    paid_at,             paid_by_id
 * ─────────────────────────────────────────────────────────────────
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {

            // ── 1. PENDINGS APPROVED → PENDING CONTRACT transition ───────────
            // Set when a manager explicitly sends a lead from Pendings Approved
            // to Pending Contract.  NULL = still in Pendings Approved queue.
            $table->timestamp('pending_contract_at')->nullable()->after('issuance_date');
            $table->foreignId('pending_contract_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('pending_contract_at');

            // ── 2. NOT ISSUED (Retention exception at Pendings Approved) ──────
            // Manager marks a lead Not Issued; Retention officer resolves it.
            $table->enum('not_issued_disposition', [
                'email_missing',
                'ssn_missing',
                'postal_mail_missing',
                'beneficiary_incomplete',
                'doctor_info_missing',
                'underwriting_by_law',
            ])->nullable()->after('pending_contract_by_id');

            $table->timestamp('not_issued_at')->nullable()->after('not_issued_disposition');

            $table->foreignId('not_issued_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('not_issued_at');

            // Retention officer who resolved the Not Issued block
            $table->foreignId('not_issued_resolved_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('not_issued_by_id');
            $table->timestamp('not_issued_resolved_at')->nullable()->after('not_issued_resolved_by_id');

            // ── 3. FOLLOWUP DONE ──────────────────────────────────────────────
            // Set by the assigned closer after confirming policy papers with client.
            $table->timestamp('followup_done_at')->nullable()->after('not_issued_resolved_at');
            $table->foreignId('followup_done_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('followup_done_at');

            // ── 4. PENDING DRAFT ──────────────────────────────────────────────
            // Set when the manager moves a Followup Done lead to Pending Draft.
            $table->timestamp('pending_draft_at')->nullable()->after('followup_done_by_id');
            $table->foreignId('pending_draft_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('pending_draft_at');

            // ── 5. NOT PAID / FDFP (Retention exception at Pending Draft) ─────
            // FDFP = First Draft First Pay — marks why the first premium didn't clear.
            // 'manual_action' requires a secondary disposition (same set as Not Issued).
            $table->enum('not_paid_fdfp_type', [
                'unstable_to_locate',
                'insufficient_fund',
                'unauthorized_payments',
                'manual_action',
            ])->nullable()->after('pending_draft_by_id');

            // Only populated when not_paid_fdfp_type = 'manual_action'
            $table->enum('not_paid_manual_disposition', [
                'email_missing',
                'ssn_missing',
                'postal_mail_missing',
                'beneficiary_incomplete',
                'doctor_info_missing',
                'underwriting_by_law',
            ])->nullable()->after('not_paid_fdfp_type');

            $table->timestamp('not_paid_at')->nullable()->after('not_paid_manual_disposition');
            $table->foreignId('not_paid_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('not_paid_at');

            // ── 6. PAID SALES ────────────────────────────────────────────────
            // Set by a Manager/Finance role when the first premium draft clears.
            $table->timestamp('paid_at')->nullable()->after('not_paid_by_id');
            $table->foreignId('paid_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('paid_at');

            // ── 7. POLICY DIED ───────────────────────────────────────────────
            // Terminal-but-re-dialable: lead.status reset to 'active' →
            // back in Ravens queue.  No retention action permitted.
            $table->enum('policy_died_reason', [
                'chargeback_failed_payment',
                'chargeback_cancellation',
            ])->nullable()->after('paid_by_id');

            $table->timestamp('policy_died_at')->nullable()->after('policy_died_reason');
            $table->foreignId('policy_died_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('policy_died_at');

            // ── Indexes for pipeline stage queries ───────────────────────────
            $table->index('pending_contract_at', 'leads_pending_contract_at_idx');
            $table->index('not_issued_at',       'leads_not_issued_at_idx');
            $table->index('followup_done_at',    'leads_followup_done_at_idx');
            $table->index('pending_draft_at',    'leads_pending_draft_at_idx');
            $table->index('not_paid_at',         'leads_not_paid_at_idx');
            $table->index('paid_at',             'leads_paid_at_idx');
            $table->index('policy_died_at',      'leads_policy_died_at_idx');
        });

        // ── Backfill: all existing manager-approved leads belong in            ──
        // ── Pending Contract or beyond (they pre-date this pipeline split).    ──
        // ── Set pending_contract_at = issuance_date ?? sale_at ?? created_at  ──
        DB::statement("
            UPDATE leads
            SET pending_contract_at = COALESCE(issuance_date, sale_at, created_at)
            WHERE manager_status = 'approved'
              AND pending_contract_at IS NULL
        ");

        // ── Backfill: existing 'Incomplete' issuances → not_issued_at         ──
        // ── Maps to 'underwriting_by_law' as a safe catch-all.               ──
        DB::statement("
            UPDATE leads
            SET not_issued_at          = COALESCE(updated_at, created_at),
                not_issued_disposition = 'underwriting_by_law'
            WHERE issuance_status = 'Incomplete'
              AND not_issued_at IS NULL
        ");

        // ── Backfill: existing 'Issued' leads that have a followup_done       ──
        // ── indicator (followup_status = 'Yes') get followup_done_at set.    ──
        DB::statement("
            UPDATE leads
            SET followup_done_at = COALESCE(updated_at, created_at)
            WHERE issuance_status = 'Issued'
              AND followup_status = 'Yes'
              AND followup_done_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['pending_contract_by_id']);
            $table->dropForeign(['not_issued_by_id']);
            $table->dropForeign(['not_issued_resolved_by_id']);
            $table->dropForeign(['followup_done_by_id']);
            $table->dropForeign(['pending_draft_by_id']);
            $table->dropForeign(['not_paid_by_id']);
            $table->dropForeign(['paid_by_id']);
            $table->dropForeign(['policy_died_by_id']);

            $table->dropIndex('leads_pending_contract_at_idx');
            $table->dropIndex('leads_not_issued_at_idx');
            $table->dropIndex('leads_followup_done_at_idx');
            $table->dropIndex('leads_pending_draft_at_idx');
            $table->dropIndex('leads_not_paid_at_idx');
            $table->dropIndex('leads_paid_at_idx');
            $table->dropIndex('leads_policy_died_at_idx');

            $table->dropColumn([
                'pending_contract_at',
                'pending_contract_by_id',
                'not_issued_disposition',
                'not_issued_at',
                'not_issued_by_id',
                'not_issued_resolved_by_id',
                'not_issued_resolved_at',
                'followup_done_at',
                'followup_done_by_id',
                'pending_draft_at',
                'pending_draft_by_id',
                'not_paid_fdfp_type',
                'not_paid_manual_disposition',
                'not_paid_at',
                'not_paid_by_id',
                'paid_at',
                'paid_by_id',
                'policy_died_reason',
                'policy_died_at',
                'policy_died_by_id',
            ]);
        });
    }
};
