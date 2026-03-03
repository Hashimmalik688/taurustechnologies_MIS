<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_user_id')->index();
            $table->date('stat_date')->index();
            $table->integer('calls_scored')->default(0);
            $table->decimal('avg_score', 5, 2)->default(0);
            $table->decimal('min_score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(0);
            $table->integer('compliance_fails')->default(0);
            $table->integer('void_risks')->default(0);
            $table->integer('excellent_count')->default(0);
            $table->integer('good_count')->default(0);
            $table->integer('average_count')->default(0);
            $table->integer('poor_count')->default(0);
            $table->decimal('avg_opening', 4, 2)->default(0);
            $table->decimal('avg_discovery', 4, 2)->default(0);
            $table->decimal('avg_presentation', 4, 2)->default(0);
            $table->decimal('avg_objection_handling', 4, 2)->default(0);
            $table->decimal('avg_closing', 4, 2)->default(0);
            $table->decimal('avg_soft_skills', 4, 2)->default(0);
            $table->decimal('avg_call_control', 4, 2)->default(0);
            $table->timestamps();

            $table->unique(['agent_user_id', 'stat_date']);
            $table->foreign('agent_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_daily_stats');
    }
};
