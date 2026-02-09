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
        if (Schema::hasTable('pabs_tickets')) {
            Schema::table('pabs_tickets', function (Blueprint $table) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('project_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pabs_tickets')) {
            Schema::table('pabs_tickets', function (Blueprint $table) {
                $table->dropColumn('total_cost');
            });
        }
    }
};
