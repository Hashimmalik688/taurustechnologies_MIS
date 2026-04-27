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
            if (!Schema::hasColumn('leads', 'first_name')) {
                $table->string('first_name')->nullable()->after('cn_name');
            }
            if (!Schema::hasColumn('leads', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('leads', 'bank_address')) {
                $table->string('bank_address')->nullable()->after('bank_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(array_filter(['first_name', 'last_name', 'bank_address'], function ($col) {
                return Schema::hasColumn('leads', $col);
            }));
        });
    }
};
