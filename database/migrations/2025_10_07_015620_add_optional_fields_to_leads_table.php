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
            // Only add fields that don't exist yet
            // driving_license, emergency_contact, future_draft_date already exist

            if (!Schema::hasColumn('leads', 'comments')) {
                $table->text('comments')->nullable()->after('status');
            }

            if (!Schema::hasColumn('leads', 'preset_line')) {
                $table->string('preset_line')->nullable()->after('closer_name');
            }

            if (!Schema::hasColumn('leads', 'acc_number')) {
                $table->string('acc_number')->nullable()->after('routing_number');
            }
        });

        // Add indexes separately
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'preset_line')) {
                $table->index('preset_line');
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
                'driving_license',
                'emergency_contact',
                'future_draft_date',
                'comments',
                'preset_line',
                'acc_number',
            ]);
        });
    }
};
