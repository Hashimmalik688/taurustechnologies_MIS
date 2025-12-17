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
        Schema::table('leads', function (Blueprint $table) {
            // Add sale_date field - the date when closer made the sale
            if (!Schema::hasColumn('leads', 'sale_date')) {
                $table->date('sale_date')->nullable()->after('sale_at');
            }
            
            // Add gender field if not exists
            if (!Schema::hasColumn('leads', 'gender')) {
                $table->string('gender')->nullable()->after('date_of_birth');
            }
            
            // Add encrypted card fields if not exist
            if (!Schema::hasColumn('leads', 'card_number')) {
                $table->text('card_number')->nullable()->after('card_info');
            }
            if (!Schema::hasColumn('leads', 'cvv')) {
                $table->text('cvv')->nullable()->after('card_number');
            }
            if (!Schema::hasColumn('leads', 'expiry_date')) {
                $table->string('expiry_date')->nullable()->after('cvv');
            }
            
            // Add preset_line if not exists
            if (!Schema::hasColumn('leads', 'preset_line')) {
                $table->string('preset_line')->nullable()->after('comments');
            }
            
            // Add age, state, zip_code if not exist
            if (!Schema::hasColumn('leads', 'age')) {
                $table->string('age')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('leads', 'state')) {
                $table->string('state')->nullable()->after('address');
            }
            if (!Schema::hasColumn('leads', 'zip_code')) {
                $table->string('zip_code')->nullable()->after('state');
            }
            
            // Add acc_number if not exists (separate from account_number)
            if (!Schema::hasColumn('leads', 'acc_number')) {
                $table->string('acc_number')->nullable()->after('account_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'sale_date',
                'gender',
                'card_number',
                'cvv',
                'expiry_date',
                'preset_line',
                'age',
                'state',
                'zip_code',
                'acc_number',
            ]);
        });
    }
};
