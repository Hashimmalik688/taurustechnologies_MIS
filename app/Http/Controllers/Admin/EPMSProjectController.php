<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EPMSProject;
use App\Models\EPMSTask;
use App\Models\EPMSMilestone;
use App\Models\EPMSExternalCost;
use App\Models\EPMSTaskDependency;
use App\Models\EPMSProjectMember;
use App\Models\EPMSRisk;
use App\Models\EPMSDocument;
use App\Models\EPMSSprint;
use App\Models\EPMSComment;
use App\Models\EPMSAiPlan;
use App\Models\EPMSWbsItem;
use App\Models\User;
use App\Services\OpenAIProjectPlannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class EPMSProjectController extends Controller
{
    /**
     * Display the EPMS Dashboard with all projects overview
     */
    public function index()
    {
        $projects = EPMSProject::with(['creator', 'projectManager', 'tasks', 'milestones', 'members', 'risks', 'sprints'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Dashboard stats
        $stats = [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'in-progress')->count(),
            'planning' => $projects->where('status', 'planning')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'on_hold' => $projects->where('status', 'on-hold')->count(),
            'total_budget' => $projects->sum('budget'),
            'total_spent' => $projects->sum('budget_spent'),
            'total_tasks' => $projects->sum('total_tasks'),
            'completed_tasks' => $projects->sum('completed_tasks'),
            'critical_risks' => $projects->sum(function ($p) {
                return $p->risks->where('severity_score', '>=', 20)->whereNotIn('status', ['resolved'])->count();
            }),
        ];

        return view('admin.epms.index', compact('projects', 'stats'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $aiService = new OpenAIProjectPlannerService();
        $aiConfigured = $aiService->isConfigured();

        return view('admin.epms.create', compact('users', 'aiConfigured'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'methodology' => 'required|in:agile,waterfall,hybrid,kanban',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'required|in:USD,PKR',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'project_manager_id' => 'nullable|exists:users,id',
            'objectives' => 'nullable|string',
            'tech_stack' => 'nullable|string',
            'repository_url' => 'nullable|url',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'milestones' => 'nullable|array',
            'milestones.*.name' => 'required_with:milestones|string|max:255',
            'milestones.*.due_date' => 'required_with:milestones|date',
            'milestones.*.description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'planning';
        $validated['health_score'] = 'green';
        $validated['contract_value'] = $validated['budget'] ?? 0;
        $validated['region'] = $validated['currency'] === 'PKR' ? 'PK' : 'US';
        $validated['client_name'] = 'Taurus Technologies'; // Internal project

        DB::beginTransaction();
        try {
            $project = EPMSProject::create(Arr::except($validated, ['milestones', 'team_members']));

            // Add team members
            if (!empty($validated['team_members'])) {
                foreach ($validated['team_members'] as $userId) {
                    EPMSProjectMember::create([
                        'project_id' => $project->id,
                        'user_id' => $userId,
                        'raci_role' => 'responsible',
                    ]);
                }
            }

            // Add project manager as accountable member
            if ($validated['project_manager_id']) {
                EPMSProjectMember::firstOrCreate([
                    'project_id' => $project->id,
                    'user_id' => $validated['project_manager_id'],
                    'raci_role' => 'accountable',
                ], ['is_lead' => true, 'project_role' => 'Project Manager']);
            }

            // Create milestones
            if (!empty($validated['milestones'])) {
                $order = 1;
                foreach ($validated['milestones'] as $ms) {
                    if (!empty($ms['name']) && !empty($ms['due_date'])) {
                        EPMSMilestone::create([
                            'project_id' => $project->id,
                            'name' => $ms['name'],
                            'description' => $ms['description'] ?? null,
                            'due_date' => $ms['due_date'],
                            'order' => $order++,
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('epms.show', $project)
                ->with('success', 'Project created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    /**
     * Display the project dashboard
     */
    public function show($id)
    {
        $project = EPMSProject::with([
            'tasks.assignedUser',
            'tasks.dependencies.dependsOnTask',
            'tasks.dependents.task',
            'tasks.sprint',
            'milestones',
            'externalCosts',
            'creator',
            'projectManager',
            'members.user',
            'risks.owner',
            'documents.uploader',
            'sprints.tasks',
            'comments.user',
            'wbsItems' => function ($q) { $q->whereNull('parent_id')->with('descendants'); },
        ])->findOrFail($id);

        $project->updateAnalytics();

        $teamMembers = User::all();

        // Gantt chart data
        $ganttTasks = $project->tasks->map(function ($task) {
            return [
                'id' => 'task-' . $task->id,
                'name' => $task->name,
                'start' => $task->start_date->format('Y-m-d'),
                'end' => $task->end_date->format('Y-m-d'),
                'progress' => $task->progress,
                'custom_class' => $task->status === 'completed' ? 'bar-completed' : ($task->priority === 'urgent' ? 'bar-urgent' : ''),
                'dependencies' => $task->dependencies->map(fn($d) => 'task-' . $d->depends_on_task_id)->implode(', '),
            ];
        })->toArray();

        $ganttMilestones = $project->milestones->map(function ($milestone) {
            return [
                'id' => 'milestone-' . $milestone->id,
                'name' => '◆ ' . $milestone->name,
                'start' => $milestone->due_date->format('Y-m-d'),
                'end' => $milestone->due_date->format('Y-m-d'),
                'progress' => $milestone->status === 'completed' ? 100 : 0,
                'custom_class' => 'bar-milestone',
            ];
        })->toArray();

        // Kanban board data
        $kanbanColumns = [
            'backlog' => ['label' => 'Backlog', 'color' => '#64748b', 'icon' => 'bx-archive'],
            'todo' => ['label' => 'To Do', 'color' => '#3b82f6', 'icon' => 'bx-list-check'],
            'in-progress' => ['label' => 'In Progress', 'color' => '#f59e0b', 'icon' => 'bx-loader-circle'],
            'review' => ['label' => 'Review', 'color' => '#8b5cf6', 'icon' => 'bx-search-alt'],
            'testing' => ['label' => 'Testing', 'color' => '#ec4899', 'icon' => 'bx-test-tube'],
            'done' => ['label' => 'Done', 'color' => '#10b981', 'icon' => 'bx-check-circle'],
        ];

        $kanbanBoard = [];
        foreach ($kanbanColumns as $key => $col) {
            $kanbanBoard[$key] = [
                'label' => $col['label'],
                'color' => $col['color'],
                'icon' => $col['icon'],
                'tasks' => $project->tasks->where('kanban_column', $key)->sortBy('kanban_order')->values(),
            ];
        }

        // Burndown data (for active sprint)
        $burndownData = [];
        $activeSprint = $project->sprints->where('status', 'active')->first();
        if ($activeSprint) {
            $burndownData = $activeSprint->getBurndownData();
        }

        // Risk matrix data
        $riskMatrix = $this->buildRiskMatrixData($project->risks);

        // Resource workload
        $resourceWorkload = $this->buildResourceWorkload($project);

        // AI service check
        $aiService = new OpenAIProjectPlannerService();
        $aiConfigured = $aiService->isConfigured();

        return view('admin.epms.show', compact(
            'project', 'teamMembers', 'ganttTasks', 'ganttMilestones',
            'kanbanBoard', 'kanbanColumns', 'burndownData', 'activeSprint',
            'riskMatrix', 'resourceWorkload', 'aiConfigured'
        ));
    }

    /**
     * Show the form for editing the project
     */
    public function edit($id)
    {
        $project = EPMSProject::with('members')->findOrFail($id);
        $users = User::orderBy('name')->get();

        return view('admin.epms.edit', compact('project', 'users'));
    }

    /**
     * Update the project
     */
    public function update(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'methodology' => 'required|in:agile,waterfall,hybrid,kanban',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'required|in:USD,PKR',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'status' => 'required|in:planning,in-progress,on-hold,completed,cancelled',
            'project_manager_id' => 'nullable|exists:users,id',
            'objectives' => 'nullable|string',
            'tech_stack' => 'nullable|string',
            'repository_url' => 'nullable|url',
        ]);

        $validated['contract_value'] = $validated['budget'] ?? 0;
        $validated['region'] = $validated['currency'] === 'PKR' ? 'PK' : 'US';

        $project->update($validated);
        $project->updateAnalytics();

        return redirect()->route('epms.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Delete the project
     */
    public function destroy($id)
    {
        $project = EPMSProject::findOrFail($id);
        $project->delete();

        return redirect()->route('epms.index')
            ->with('success', 'Project deleted successfully!');
    }

    // ==================== MILESTONES ====================

    public function addMilestone(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $validated['project_id'] = $project->id;
        $validated['order'] = $project->milestones()->max('order') + 1;

        EPMSMilestone::create($validated);

        return redirect()->route('epms.show', $project)->with('success', 'Milestone added!');
    }

    public function updateMilestoneDate(Request $request, $id, $milestoneId)
    {
        $project = EPMSProject::findOrFail($id);
        $milestone = EPMSMilestone::where('project_id', $project->id)->findOrFail($milestoneId);

        $milestone->adjustDate($request->validate(['due_date' => 'required|date'])['due_date']);
        $project->updateAnalytics();

        return response()->json(['success' => true, 'message' => 'Milestone updated!']);
    }

    // ==================== TASKS ====================

    public function addTask(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'milestone_id' => 'nullable|exists:epms_milestones,id',
            'sprint_id' => 'nullable|exists:epms_sprints,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'task_type' => 'required|in:standard,revision',
            'kanban_column' => 'nullable|in:backlog,todo,in-progress,review,testing,done',
            'estimated_hours' => 'nullable|integer|min:0',
            'story_points' => 'nullable|integer|min:0',
            'label' => 'nullable|string|max:100',
        ]);

        $validated['project_id'] = $project->id;
        $validated['order'] = $project->tasks()->max('order') + 1;
        $validated['kanban_column'] = $validated['kanban_column'] ?? 'backlog';
        $validated['kanban_order'] = $project->tasks()->where('kanban_column', $validated['kanban_column'])->max('kanban_order') + 1;

        EPMSTask::create($validated);
        $project->updateAnalytics();

        return redirect()->route('epms.show', $project)->with('success', 'Task added!');
    }

    public function updateTaskStatus(Request $request, $id, $taskId)
    {
        $project = EPMSProject::findOrFail($id);
        $task = EPMSTask::where('project_id', $project->id)->findOrFail($taskId);

        $validated = $request->validate([
            'status' => 'required|in:todo,in-progress,review,completed',
            'progress' => 'nullable|integer|min:0|max:100',
            'kanban_column' => 'nullable|string',
        ]);

        $task->update($validated);

        if ($validated['status'] === 'completed') {
            $task->completed_at = now();
            $task->progress = 100;
            $task->kanban_column = 'done';
            $task->save();
        }

        if ($task->milestone) {
            $task->milestone->updateStatus();
        }
        if ($task->sprint) {
            $task->sprint->updateCompletedPoints();
        }

        $project->updateAnalytics();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated!',
            'project' => $project->fresh(),
        ]);
    }

    public function updateTaskDates(Request $request, $id, $taskId)
    {
        $project = EPMSProject::findOrFail($id);
        $task = EPMSTask::where('project_id', $project->id)->findOrFail($taskId);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $task->update($validated);
        $task->adjustDependentTasks();
        $project->updateAnalytics();

        return response()->json(['success' => true, 'message' => 'Task dates updated!']);
    }

    /**
     * Move task on Kanban board (AJAX)
     */
    public function moveTask(Request $request, $id, $taskId)
    {
        $project = EPMSProject::findOrFail($id);
        $task = EPMSTask::where('project_id', $project->id)->findOrFail($taskId);

        $validated = $request->validate([
            'kanban_column' => 'required|in:backlog,todo,in-progress,review,testing,done',
            'kanban_order' => 'required|integer|min:0',
        ]);

        // Map kanban column to task status
        $statusMap = [
            'backlog' => 'todo',
            'todo' => 'todo',
            'in-progress' => 'in-progress',
            'review' => 'review',
            'testing' => 'review',
            'done' => 'completed',
        ];

        $task->kanban_column = $validated['kanban_column'];
        $task->kanban_order = $validated['kanban_order'];
        $task->status = $statusMap[$validated['kanban_column']];

        if ($task->status === 'completed') {
            $task->progress = 100;
            $task->completed_at = now();
        }

        $task->save();

        if ($task->milestone) {
            $task->milestone->updateStatus();
        }
        if ($task->sprint) {
            $task->sprint->updateCompletedPoints();
        }
        $project->updateAnalytics();

        return response()->json(['success' => true, 'message' => 'Task moved!']);
    }

    // ==================== DEPENDENCIES ====================

    public function addTaskDependency(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'task_id' => 'required|exists:epms_tasks,id',
            'depends_on_task_id' => 'required|exists:epms_tasks,id|different:task_id',
            'dependency_type' => 'required|in:finish-to-start,start-to-start,finish-to-finish,start-to-finish',
            'lag_days' => 'nullable|integer',
        ]);

        EPMSTaskDependency::create($validated);

        return response()->json(['success' => true, 'message' => 'Dependency created!']);
    }

    // ==================== EXTERNAL COSTS ====================

    public function addExternalCost(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_type' => 'required|in:asset,api,subcontractor,software,hardware,other',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,PKR',
            'incurred_date' => 'nullable|date',
            'vendor_name' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:monthly,quarterly,yearly',
        ]);

        $validated['project_id'] = $project->id;

        EPMSExternalCost::create($validated);
        $project->updateAnalytics();

        return redirect()->route('epms.show', $project)->with('success', 'Cost added!');
    }

    // ==================== SPRINTS ====================

    public function storeSprint(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'capacity_points' => 'nullable|integer|min:0',
        ]);

        $validated['project_id'] = $project->id;
        $validated['sprint_number'] = $project->sprints()->max('sprint_number') + 1;

        EPMSSprint::create($validated);

        return redirect()->route('epms.show', $project)->with('success', 'Sprint created!');
    }

    public function startSprint(Request $request, $id, $sprintId)
    {
        $project = EPMSProject::findOrFail($id);

        // End any active sprint
        $project->sprints()->where('status', 'active')->update(['status' => 'completed']);

        $sprint = EPMSSprint::where('project_id', $project->id)->findOrFail($sprintId);
        $sprint->update(['status' => 'active']);

        return response()->json(['success' => true, 'message' => 'Sprint started!']);
    }

    public function completeSprint(Request $request, $id, $sprintId)
    {
        $project = EPMSProject::findOrFail($id);
        $sprint = EPMSSprint::where('project_id', $project->id)->findOrFail($sprintId);

        $sprint->update([
            'status' => 'completed',
            'retrospective_notes' => $request->input('retrospective_notes'),
        ]);

        return response()->json(['success' => true, 'message' => 'Sprint completed!']);
    }

    // ==================== RISKS ====================

    public function storeRisk(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'probability' => 'required|in:very_low,low,medium,high,very_high',
            'impact' => 'required|in:very_low,low,medium,high,very_high',
            'category' => 'required|in:technical,schedule,budget,resource,scope,quality,external',
            'mitigation_plan' => 'nullable|string',
            'contingency_plan' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $validated['project_id'] = $project->id;

        EPMSRisk::create($validated);

        return redirect()->route('epms.show', $project)->with('success', 'Risk added to register!');
    }

    public function updateRiskStatus(Request $request, $id, $riskId)
    {
        $project = EPMSProject::findOrFail($id);
        $risk = EPMSRisk::where('project_id', $project->id)->findOrFail($riskId);

        $validated = $request->validate([
            'status' => 'required|in:identified,analyzing,mitigating,resolved,accepted',
        ]);

        $risk->update($validated);
        if ($validated['status'] === 'resolved') {
            $risk->update(['resolved_date' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Risk status updated!']);
    }

    // ==================== TEAM MEMBERS (RACI) ====================

    public function storeMember(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'raci_role' => 'required|in:responsible,accountable,consulted,informed',
            'project_role' => 'nullable|string|max:255',
        ]);

        $validated['project_id'] = $project->id;

        EPMSProjectMember::updateOrCreate(
            ['project_id' => $project->id, 'user_id' => $validated['user_id'], 'raci_role' => $validated['raci_role']],
            $validated
        );

        return redirect()->route('epms.show', $project)->with('success', 'Team member added!');
    }

    public function removeMember($id, $memberId)
    {
        $project = EPMSProject::findOrFail($id);
        EPMSProjectMember::where('project_id', $project->id)->findOrFail($memberId)->delete();

        return response()->json(['success' => true, 'message' => 'Member removed!']);
    }

    // ==================== DOCUMENTS ====================

    public function storeDocument(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:20480', // 20MB max
            'category' => 'nullable|string',
            'task_id' => 'nullable|exists:epms_tasks,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('epms/documents/' . $project->id, 'public');

        EPMSDocument::create([
            'project_id' => $project->id,
            'task_id' => $validated['task_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
            'category' => $validated['category'] ?? null,
        ]);

        return redirect()->route('epms.show', $project)->with('success', 'Document uploaded!');
    }

    public function downloadDocument($id, $docId)
    {
        $project = EPMSProject::findOrFail($id);
        $doc = EPMSDocument::where('project_id', $project->id)->findOrFail($docId);

        return Storage::disk('public')->download($doc->file_path, $doc->name . '.' . $doc->file_type);
    }

    // ==================== COMMENTS ====================

    public function storeComment(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'body' => 'required|string',
            'task_id' => 'nullable|exists:epms_tasks,id',
        ]);

        EPMSComment::create([
            'project_id' => $project->id,
            'task_id' => $validated['task_id'] ?? null,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'type' => 'comment',
        ]);

        return response()->json(['success' => true, 'message' => 'Comment added!']);
    }

    // ==================== WBS ====================

    public function storeWbsItem(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:epms_wbs_items,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:phase,deliverable,work_package,activity',
            'estimated_hours' => 'nullable|integer|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        // Auto-generate code
        if ($validated['parent_id']) {
            $parent = EPMSWbsItem::findOrFail($validated['parent_id']);
            $childCount = $parent->children()->count() + 1;
            $validated['code'] = $parent->code . '.' . $childCount;
        } else {
            $rootCount = $project->wbsItems()->whereNull('parent_id')->count() + 1;
            $validated['code'] = (string) $rootCount;
        }

        $validated['project_id'] = $project->id;
        $validated['order'] = EPMSWbsItem::where('project_id', $project->id)
            ->where('parent_id', $validated['parent_id'] ?? null)
            ->max('order') + 1;

        EPMSWbsItem::create($validated);

        return redirect()->route('epms.show', $project)->with('success', 'WBS item added!');
    }

    // ==================== AI PLANNING ====================

    public function generateAiPlan(Request $request, $id = null)
    {
        $validated = $request->validate([
            'prompt' => 'required|string|min:20',
            'methodology' => 'nullable|in:agile,waterfall,hybrid,kanban',
            'team_size' => 'nullable|integer|min:1',
            'budget' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string',
        ]);

        $aiService = new OpenAIProjectPlannerService();

        if (!$aiService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'OpenAI API key is not configured. Add OPENAI_API_KEY to your .env file.',
            ], 422);
        }

        // Save AI plan record
        $aiPlan = EPMSAiPlan::create([
            'project_id' => $id,
            'generated_by' => Auth::id(),
            'prompt' => $validated['prompt'],
            'status' => 'generating',
        ]);

        $result = $aiService->generateProjectPlan($validated['prompt'], [
            'methodology' => $validated['methodology'] ?? 'agile',
            'team_size' => $validated['team_size'] ?? 'not specified',
            'budget' => $validated['budget'] ?? 'not specified',
            'duration' => $validated['duration'] ?? 'not specified',
        ]);

        if ($result['success']) {
            $aiPlan->update([
                'response' => $result['raw_response'],
                'plan_data' => $result['plan'],
                'status' => 'completed',
            ]);

            return response()->json([
                'success' => true,
                'plan' => $result['plan'],
                'plan_id' => $aiPlan->id,
            ]);
        }

        $aiPlan->update(['status' => 'failed', 'response' => $result['error']]);

        return response()->json([
            'success' => false,
            'error' => $result['error'],
        ], 422);
    }

    /**
     * Apply an AI-generated plan to a project (create milestones, tasks, risks, WBS)
     */
    public function applyAiPlan(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'plan_id' => 'required|exists:epms_ai_plans,id',
        ]);

        $aiPlan = EPMSAiPlan::findOrFail($validated['plan_id']);
        $plan = $aiPlan->plan_data;

        if (!$plan) {
            return response()->json(['success' => false, 'error' => 'No plan data found'], 422);
        }

        DB::beginTransaction();
        try {
            // Update project metadata
            if (isset($plan['project_summary'])) {
                $project->update([
                    'tech_stack' => $plan['project_summary']['tech_stack'] ?? null,
                    'category' => $plan['project_summary']['category'] ?? null,
                    'methodology' => $plan['project_summary']['methodology'] ?? $project->methodology,
                    'ai_plan' => $plan,
                    'ai_prompt' => $aiPlan->prompt,
                ]);
            }

            // Create milestones
            $milestoneMap = [];
            if (!empty($plan['milestones'])) {
                $order = $project->milestones()->max('order') + 1;
                foreach ($plan['milestones'] as $idx => $ms) {
                    $weeksFromStart = $ms['week_number'] ?? (($idx + 1) * 2);
                    $milestone = EPMSMilestone::create([
                        'project_id' => $project->id,
                        'name' => $ms['name'],
                        'description' => $ms['description'] ?? null,
                        'due_date' => $project->start_date->copy()->addWeeks($weeksFromStart),
                        'order' => $order++,
                    ]);
                    $milestoneMap[$idx] = $milestone->id;
                }
            }

            // Create tasks
            $taskMap = [];
            if (!empty($plan['tasks'])) {
                $taskOrder = $project->tasks()->max('order') + 1;
                foreach ($plan['tasks'] as $idx => $t) {
                    $milestoneIdx = $t['milestone_index'] ?? null;
                    $milestoneId = ($milestoneIdx !== null && isset($milestoneMap[$milestoneIdx])) ? $milestoneMap[$milestoneIdx] : null;

                    $task = EPMSTask::create([
                        'project_id' => $project->id,
                        'milestone_id' => $milestoneId,
                        'name' => $t['name'],
                        'description' => $t['description'] ?? null,
                        'priority' => $t['priority'] ?? 'medium',
                        'estimated_hours' => $t['estimated_hours'] ?? 0,
                        'story_points' => $t['story_points'] ?? 0,
                        'start_date' => $project->start_date->copy()->addDays($idx * 2),
                        'end_date' => $project->start_date->copy()->addDays(($idx * 2) + max(($t['estimated_hours'] ?? 8) / 8, 1)),
                        'kanban_column' => 'backlog',
                        'label' => implode(', ', $t['skills_required'] ?? []),
                        'order' => $taskOrder++,
                    ]);
                    $taskMap[$idx] = $task->id;
                }

                // Create dependencies
                foreach ($plan['tasks'] as $idx => $t) {
                    if (!empty($t['dependencies']) && isset($taskMap[$idx])) {
                        foreach ($t['dependencies'] as $depIdx) {
                            if (isset($taskMap[$depIdx])) {
                                EPMSTaskDependency::create([
                                    'task_id' => $taskMap[$idx],
                                    'depends_on_task_id' => $taskMap[$depIdx],
                                    'dependency_type' => 'finish-to-start',
                                ]);
                            }
                        }
                    }
                }
            }

            // Create risks
            if (!empty($plan['risks'])) {
                foreach ($plan['risks'] as $r) {
                    EPMSRisk::create([
                        'project_id' => $project->id,
                        'title' => $r['title'],
                        'description' => $r['description'] ?? null,
                        'probability' => $r['probability'] ?? 'medium',
                        'impact' => $r['impact'] ?? 'medium',
                        'category' => $r['category'] ?? 'technical',
                        'mitigation_plan' => $r['mitigation_plan'] ?? null,
                    ]);
                }
            }

            // Create WBS
            if (!empty($plan['wbs'])) {
                $this->createWbsFromPlan($project->id, $plan['wbs'], null);
            }

            // Create sprints
            if (!empty($plan['sprints'])) {
                $sprintStart = $project->start_date->copy();
                foreach ($plan['sprints'] as $s) {
                    $durationWeeks = $s['duration_weeks'] ?? 2;
                    $sprint = EPMSSprint::create([
                        'project_id' => $project->id,
                        'name' => $s['name'],
                        'goal' => $s['goal'] ?? null,
                        'start_date' => $sprintStart,
                        'end_date' => $sprintStart->copy()->addWeeks($durationWeeks),
                        'sprint_number' => $project->sprints()->max('sprint_number') + 1,
                    ]);

                    // Assign tasks to sprint
                    if (!empty($s['task_indices'])) {
                        foreach ($s['task_indices'] as $tIdx) {
                            if (isset($taskMap[$tIdx])) {
                                EPMSTask::where('id', $taskMap[$tIdx])->update(['sprint_id' => $sprint->id]);
                            }
                        }
                    }

                    $sprintStart = $sprintStart->copy()->addWeeks($durationWeeks);
                }
            }

            $aiPlan->update(['status' => 'applied']);
            $project->updateAnalytics();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'AI plan applied to project!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ==================== GANTT DATA API ====================

    public function getGanttData($id)
    {
        $project = EPMSProject::with([
            'tasks.assignedUser', 'tasks.dependencies.dependsOnTask', 'milestones'
        ])->findOrFail($id);

        return response()->json([
            'tasks' => $project->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'start' => $task->start_date->format('Y-m-d'),
                    'end' => $task->end_date->format('Y-m-d'),
                    'progress' => $task->progress,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'assigned_to' => $task->assignedUser?->name,
                    'milestone_id' => $task->milestone_id,
                    'dependencies' => $task->dependencies->map(fn($dep) => [
                        'id' => $dep->depends_on_task_id,
                        'type' => $dep->dependency_type,
                    ]),
                ];
            }),
            'milestones' => $project->milestones->map(function ($ms) {
                return [
                    'id' => $ms->id,
                    'name' => $ms->name,
                    'date' => $ms->due_date->format('Y-m-d'),
                    'status' => $ms->status,
                ];
            }),
        ]);
    }

    // ==================== HELPERS ====================

    private function buildRiskMatrixData($risks): array
    {
        $matrix = [];
        $levels = ['very_low', 'low', 'medium', 'high', 'very_high'];

        foreach ($levels as $prob) {
            foreach ($levels as $imp) {
                $key = $prob . '_' . $imp;
                $matrix[$key] = $risks->filter(function ($r) use ($prob, $imp) {
                    return $r->probability === $prob && $r->impact === $imp;
                })->count();
            }
        }

        return $matrix;
    }

    private function buildResourceWorkload($project): array
    {
        $members = $project->members()->with('user')->get();
        $workload = [];

        foreach ($members as $member) {
            $assignedTasks = $project->tasks()
                ->where('assigned_to', $member->user_id)
                ->whereNotIn('status', ['completed'])
                ->count();

            $totalHours = $project->tasks()
                ->where('assigned_to', $member->user_id)
                ->whereNotIn('status', ['completed'])
                ->sum('estimated_hours');

            $workload[] = [
                'user' => $member->user,
                'role' => $member->project_role ?? $member->raci_role,
                'tasks' => $assignedTasks,
                'hours' => $totalHours,
                'utilization' => min(($totalHours / max(160, 1)) * 100, 100), // 160 hours = full month
            ];
        }

        return $workload;
    }

    private function createWbsFromPlan(int $projectId, array $items, ?int $parentId): void
    {
        foreach ($items as $item) {
            $wbs = EPMSWbsItem::create([
                'project_id' => $projectId,
                'parent_id' => $parentId,
                'code' => $item['code'] ?? '0',
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'level' => $item['level'] ?? 'work_package',
                'estimated_hours' => $item['estimated_hours'] ?? 0,
                'order' => EPMSWbsItem::where('project_id', $projectId)->where('parent_id', $parentId)->max('order') + 1,
            ]);

            if (!empty($item['children'])) {
                $this->createWbsFromPlan($projectId, $item['children'], $wbs->id);
            }
        }
    }
}
