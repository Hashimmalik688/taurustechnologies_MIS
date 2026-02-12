<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance epms_projects for internal use
        Schema::table('epms_projects', function (Blueprint $table) {
            // Make client fields nullable (not needed for internal projects)
            $table->string('client_name')->nullable()->change();

            // Internal project fields
            $table->enum('methodology', ['agile', 'waterfall', 'hybrid', 'kanban'])->default('agile')->after('status');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('methodology');
            $table->string('category')->nullable()->after('priority'); // e.g., "Web App", "Mobile", "API"
            $table->decimal('budget', 15, 2)->default(0)->after('category');
            $table->decimal('budget_spent', 15, 2)->default(0)->after('budget');
            $table->text('objectives')->nullable()->after('budget_spent');
            $table->json('tags')->nullable()->after('objectives');
            $table->json('ai_plan')->nullable()->after('tags'); // Stores OpenAI generated plan
            $table->text('ai_prompt')->nullable()->after('ai_plan'); // The prompt used
            $table->string('repository_url')->nullable()->after('ai_prompt');
            $table->string('tech_stack')->nullable()->after('repository_url');
        });

        // 2. Add sprint support and kanban columns to tasks
        Schema::table('epms_tasks', function (Blueprint $table) {
            $table->foreignId('sprint_id')->nullable()->after('milestone_id');
            $table->string('kanban_column')->default('backlog')->after('status'); // backlog, todo, in-progress, review, testing, done
            $table->string('label')->nullable()->after('kanban_column');
            $table->string('color')->nullable()->after('label'); // for Kanban card color
            $table->integer('story_points')->default(0)->after('estimated_hours');
            $table->integer('kanban_order')->default(0)->after('order');
        });

        // 3. Project Members with RACI roles
        Schema::create('epms_project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('raci_role', ['responsible', 'accountable', 'consulted', 'informed'])->default('responsible');
            $table->string('project_role')->nullable(); // e.g., "Developer", "Designer", "QA"
            $table->boolean('is_lead')->default(false);
            $table->timestamps();

            $table->unique(['project_id', 'user_id', 'raci_role']);
            $table->index('project_id');
            $table->index('user_id');
        });

        // 4. Risk Register
        Schema::create('epms_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('probability', ['very_low', 'low', 'medium', 'high', 'very_high'])->default('medium');
            $table->enum('impact', ['very_low', 'low', 'medium', 'high', 'very_high'])->default('medium');
            $table->integer('severity_score')->default(0); // probability * impact
            $table->text('mitigation_plan')->nullable();
            $table->text('contingency_plan')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->enum('status', ['identified', 'analyzing', 'mitigating', 'resolved', 'accepted'])->default('identified');
            $table->enum('category', ['technical', 'schedule', 'budget', 'resource', 'scope', 'quality', 'external'])->default('technical');
            $table->date('identified_date')->nullable();
            $table->date('resolved_date')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->index('status');
            $table->index('severity_score');
        });

        // 5. Document Management
        Schema::create('epms_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained('epms_tasks')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type')->nullable(); // pdf, doc, image, etc.
            $table->integer('file_size')->default(0); // bytes
            $table->integer('version')->default(1);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('category')->nullable(); // requirements, design, technical, meeting_notes
            $table->timestamps();

            $table->index('project_id');
            $table->index('task_id');
        });

        // 6. Sprints (for Agile methodology)
        Schema::create('epms_sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->string('name');
            $table->text('goal')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->integer('capacity_points')->default(0); // Total story points capacity
            $table->integer('completed_points')->default(0);
            $table->integer('sprint_number')->default(1);
            $table->text('retrospective_notes')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->index('status');
        });

        // 7. Comments (for projects and tasks)
        Schema::create('epms_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained('epms_tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('body');
            $table->string('type')->default('comment'); // comment, status_change, assignment, system
            $table->timestamps();

            $table->index(['project_id', 'task_id']);
            $table->index('user_id');
        });

        // 8. AI Plans History
        Schema::create('epms_ai_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('epms_projects')->onDelete('cascade');
            $table->foreignId('generated_by')->constrained('users');
            $table->text('prompt');
            $table->longText('response')->nullable();
            $table->json('plan_data')->nullable(); // Structured plan (WBS, tasks, milestones, risks)
            $table->enum('status', ['generating', 'completed', 'failed', 'applied'])->default('generating');
            $table->timestamps();

            $table->index('project_id');
        });

        // 9. WBS (Work Breakdown Structure) items
        Schema::create('epms_wbs_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('epms_projects')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('epms_wbs_items')->onDelete('cascade');
            $table->string('code'); // e.g. "1.1.2"
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('level', ['phase', 'deliverable', 'work_package', 'activity'])->default('work_package');
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->integer('estimated_hours')->default(0);
            $table->integer('actual_hours')->default(0);
            $table->integer('progress')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('project_id');
            $table->index('parent_id');
            $table->index('code');
        });

        // Add foreign key for sprint_id on tasks
        Schema::table('epms_tasks', function (Blueprint $table) {
            $table->foreign('sprint_id')->references('id')->on('epms_sprints')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('epms_tasks', function (Blueprint $table) {
            $table->dropForeign(['sprint_id']);
        });

        Schema::dropIfExists('epms_wbs_items');
        Schema::dropIfExists('epms_ai_plans');
        Schema::dropIfExists('epms_comments');
        Schema::dropIfExists('epms_sprints');
        Schema::dropIfExists('epms_documents');
        Schema::dropIfExists('epms_risks');
        Schema::dropIfExists('epms_project_members');

        Schema::table('epms_tasks', function (Blueprint $table) {
            $table->dropColumn(['sprint_id', 'kanban_column', 'label', 'color', 'story_points', 'kanban_order']);
        });

        Schema::table('epms_projects', function (Blueprint $table) {
            $table->dropColumn([
                'methodology', 'priority', 'category', 'budget', 'budget_spent',
                'objectives', 'tags', 'ai_plan', 'ai_prompt', 'repository_url', 'tech_stack'
            ]);
        });
    }
};
