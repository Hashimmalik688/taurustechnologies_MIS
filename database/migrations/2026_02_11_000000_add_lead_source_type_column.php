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
            $table->string('source_type')->nullable()->after('source')->index();
        });

        // Mark all existing leads without verified_by as 'imported' (backward compatibility)
        // Peregrine leads are those with verified_by, validated_by, or created by peregrine users
        DB::table('leads')
            ->whereNull('verified_by')
            ->whereNull('validated_by')
            ->update(['source_type' => 'imported']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
