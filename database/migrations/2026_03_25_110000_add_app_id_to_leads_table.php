<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Application ID assigned at Submissions stage (Declined, Underwriting, or Approved)
            $table->string('app_id', 100)->nullable()->after('assigned_partner');
            $table->index('app_id', 'leads_app_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_app_id_idx');
            $table->dropColumn('app_id');
        });
    }
};
