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
        Schema::table('partners', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_partner_id')->nullable()->after('id');
            $table->string('type', 20)->default('partner')->after('parent_partner_id');
            $table->foreign('parent_partner_id')->references('id')->on('partners')->onDelete('set null');
            $table->index('parent_partner_id');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['parent_partner_id']);
            $table->dropIndex(['parent_partner_id']);
            $table->dropColumn(['parent_partner_id', 'type']);
        });
    }
};
