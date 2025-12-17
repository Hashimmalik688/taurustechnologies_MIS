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
        Schema::table('users', function (Blueprint $table) {
            // Add punctuality bonus field
            $table->decimal('punctuality_bonus', 8, 2)->default(0)->after('bonus_per_extra_sale');
            
            // Add flag to indicate if employee is in sales (has sales targets)
            $table->boolean('is_sales_employee')->default(true)->after('punctuality_bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['punctuality_bonus', 'is_sales_employee']);
        });
    }
};
