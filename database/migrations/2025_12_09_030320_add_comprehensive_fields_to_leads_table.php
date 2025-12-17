<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Only add fields that don't exist yet - most fields already exist from previous migrations
            
            // Personal Details - NEW FIELDS ONLY
            if (!Schema::hasColumn('leads', 'driving_license_number')) {
                $table->string('driving_license_number')->nullable()->after('ssn');
            }
            if (!Schema::hasColumn('leads', 'height')) {
                $table->string('height')->nullable()->after('driving_license_number');
            }
            if (!Schema::hasColumn('leads', 'weight')) {
                $table->string('weight')->nullable()->after('height');
            }
            
            // Policy & Carrier Details - NEW FIELD (link to insurance carriers master table)
            if (!Schema::hasColumn('leads', 'insurance_carrier_id')) {
                $table->foreignId('insurance_carrier_id')->nullable()->after('carrier_name')->constrained('insurance_carriers')->nullOnDelete();
            }
            
            // Banking & Payment Details - NEW FIELDS
            if (!Schema::hasColumn('leads', 'card_info')) {
                $table->text('card_info')->nullable()->after('account_number'); // Store encrypted if needed
            }
            
            // Additional Details - NEW FIELDS
            if (!Schema::hasColumn('leads', 'ss_amount')) {
                $table->decimal('ss_amount', 10, 2)->nullable()->after('bank_balance'); // Social Security amount
            }
            if (!Schema::hasColumn('leads', 'ss_date')) {
                $table->date('ss_date')->nullable()->after('ss_amount'); // Social Security date
            }
            
            // Modify existing smoker to enum if it's currently string
            if (Schema::hasColumn('leads', 'smoker')) {
                DB::statement("ALTER TABLE leads MODIFY COLUMN smoker ENUM('yes', 'no') NULL");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'insurance_carrier_id')) {
                $table->dropForeign(['insurance_carrier_id']);
            }
            $columns_to_drop = [];
            if (Schema::hasColumn('leads', 'driving_license_number')) $columns_to_drop[] = 'driving_license_number';
            if (Schema::hasColumn('leads', 'height')) $columns_to_drop[] = 'height';
            if (Schema::hasColumn('leads', 'weight')) $columns_to_drop[] = 'weight';
            if (Schema::hasColumn('leads', 'insurance_carrier_id')) $columns_to_drop[] = 'insurance_carrier_id';
            if (Schema::hasColumn('leads', 'card_info')) $columns_to_drop[] = 'card_info';
            if (Schema::hasColumn('leads', 'ss_amount')) $columns_to_drop[] = 'ss_amount';
            if (Schema::hasColumn('leads', 'ss_date')) $columns_to_drop[] = 'ss_date';
            
            if (!empty($columns_to_drop)) {
                $table->dropColumn($columns_to_drop);
            }
            
            // Revert smoker back to string
            if (Schema::hasColumn('leads', 'smoker')) {
                DB::statement("ALTER TABLE leads MODIFY COLUMN smoker VARCHAR(255) NULL");
            }
        });
    }
};
