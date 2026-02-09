<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EPMSProject;
use App\Models\EPMSTask;
use App\Models\EPMSMilestone;
use App\Models\EPMSExternalCost;
use App\Models\EPMSTaskDependency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class EPMSProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index()
    {
        $projects = EPMSProject::with(['creator', 'projectManager', 'tasks', 'milestones'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.epms.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $users = User::whereIn('name', ['Super Admin', 'Manager', 'CEO', 'Co-ordinator'])
            ->orWhereHas('roles', function ($query) {
                $query->whereIn('name', ['Super Admin', 'Manager', 'CEO', 'Co-ordinator']);
            })
            ->orderBy('name')
            ->get();

        return view('admin.epms.create', compact('users'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string',
            'region' => 'required|in:US,PK',
            'currency' => 'required|in:USD,PKR',
            'contract_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'project_manager_id' => 'nullable|exists:users,id',
            'milestones' => 'nullable|array',
            'milestones.*.name' => 'required_with:milestones|string|max:255',
            'milestones.*.due_date' => 'required_with:milestones|date',
            'milestones.*.description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'planning';
        $validated['health_score'] = 'green';

        // Create project
        $project = EPMSProject::create(Arr::except($validated, ['milestones']));

        // Create milestones if provided
        if (!empty($validated['milestones'])) {
            $order = 1;
            foreach ($validated['milestones'] as $milestoneData) {
                if (!empty($milestoneData['name']) && !empty($milestoneData['due_date'])) {
                    EPMSMilestone::create([
                        'project_id' => $project->id,
                        'name' => $milestoneData['name'],
                        'description' => $milestoneData['description'] ?? null,
                        'due_date' => $milestoneData['due_date'],
                        'order' => $order++,
                        'status' => 'pending',
                    ]);
                }
            }
        }

        return redirect()->route('epms.show', $project)
            ->with('success', 'Project created successfully! Add tasks and external costs below.');
    }

    /**
     * Display the project dashboard (detailed view with Gantt)
     */
    public function show($id)
    {
        $project = EPMSProject::with([
            'tasks.assignedUser',
            'tasks.dependencies.dependsOnTask',
            'tasks.dependents.task',
            'milestones',
            'externalCosts',
            'creator',
            'projectManager'
        ])->findOrFail($id);

        // Update analytics before showing
        $project->updateAnalytics();

        // Get team members
        $teamMembers = User::all();

        // Calculate PKR exchange rate (mock - in production use live API)
        $pkrRate = 278.50; // USD to PKR

        // Calculate currency risk if US client
        $currencyRisk = null;
        if ($project->region === 'US') {
            // Mock fluctuation data
            $currencyRisk = [
                'current_rate' => $pkrRate,
                'last_week_rate' => 280.00,
                'change_percentage' => round((($pkrRate - 280.00) / 280.00) * 100, 2),
                'impact_on_margin' => round($project->contract_value * 0.01, 2) // 1% impact estimate
            ];
        }

        // Prepare Gantt chart data
        $ganttTasks = $project->tasks->map(function($task) {
            $customClass = '';
            if ($task->status === 'completed') {
                $customClass = 'bar-completed';
            } elseif ($task->priority === 'urgent') {
                $customClass = 'bar-urgent';
            }
            return [
                'id' => 'task-' . $task->id,
                'name' => $task->name,
                'start' => $task->start_date->format('Y-m-d'),
                'end' => $task->end_date->format('Y-m-d'),
                'progress' => $task->progress,
                'custom_class' => $customClass
            ];
        })->toArray();

        $ganttMilestones = $project->milestones->map(function($milestone) {
            return [
                'id' => 'milestone-' . $milestone->id,
                'name' => '◆ ' . $milestone->name,
                'start' => $milestone->due_date->format('Y-m-d'),
                'end' => $milestone->due_date->format('Y-m-d'),
                'progress' => $milestone->status === 'completed' ? 100 : 0,
                'custom_class' => 'bar-milestone'
            ];
        })->toArray();

        return view('admin.epms.show', compact('project', 'teamMembers', 'currencyRisk', 'pkrRate', 'ganttTasks', 'ganttMilestones'));
    }

    /**
     * Show the form for editing the project
     */
    public function edit($id)
    {
        $project = EPMSProject::findOrFail($id);
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Admin', 'Manager', 'CEO', 'Co-ordinator']);
        })->orderBy('name')->get();

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
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string',
            'region' => 'required|in:US,PK',
            'currency' => 'required|in:USD,PKR',
            'contract_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'status' => 'required|in:planning,in-progress,on-hold,completed,cancelled',
            'project_manager_id' => 'nullable|exists:users,id',
        ]);

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

    /**
     * Add a milestone to the project
     */
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

        return redirect()->route('epms.show', $project)
            ->with('success', 'Milestone added successfully!');
    }

    /**
     * Add a task to the project
     */
    public function addTask(Request $request, $id)
    {
        $project = EPMSProject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'milestone_id' => 'nullable|exists:epms_milestones,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'task_type' => 'required|in:standard,revision',
            'estimated_hours' => 'nullable|integer|min:0',
        ]);

        $validated['project_id'] = $project->id;
        $validated['order'] = $project->tasks()->max('order') + 1;

        EPMSTask::create($validated);
        $project->updateAnalytics();

        return redirect()->route('epms.show', $project)
            ->with('success', 'Task added successfully!');
    }

    /**
     * Update task status
     */
    public function updateTaskStatus(Request $request, $id, $taskId)
    {
        $project = EPMSProject::findOrFail($id);
        $task = EPMSTask::where('project_id', $project->id)->findOrFail($taskId);

        $validated = $request->validate([
            'status' => 'required|in:todo,in-progress,review,completed',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $task->update($validated);

        if ($validated['status'] === 'completed') {
            $task->completed_at = now();
            $task->progress = 100;
            $task->save();
        }

        // Update milestone status
        if ($task->milestone) {
            $task->milestone->updateStatus();
        }

        $project->updateAnalytics();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated!',
            'project' => $project->fresh()
        ]);
    }

    /**
     * Update task dates (for Gantt chart drag)
     */
    public function updateTaskDates(Request $request, $id, $taskId)
    {
        $project = EPMSProject::findOrFail($id);
        $task = EPMSTask::where('project_id', $project->id)->findOrFail($taskId);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $task->update($validated);

        // Adjust dependent tasks
        $task->adjustDependentTasks();

        $project->updateAnalytics();

        return response()->json([
            'success' => true,
            'message' => 'Task dates updated!',
            'affected_tasks' => $task->dependentTasks()->pluck('id')
        ]);
    }

    /**
     * Update milestone date (cascades to tasks)
     */
    public function updateMilestoneDate(Request $request, $id, $milestoneId)
    {
        $project = EPMSProject::findOrFail($id);
        $milestone = EPMSMilestone::where('project_id', $project->id)->findOrFail($milestoneId);

        $validated = $request->validate([
            'due_date' => 'required|date',
        ]);

        $milestone->adjustDate($validated['due_date']);
        $project->updateAnalytics();

        return response()->json([
            'success' => true,
            'message' => 'Milestone and dependent tasks updated!',
            'affected_tasks' => $milestone->tasks()->pluck('id')
        ]);
    }

    /**
     * Add task dependency
     */
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

        return response()->json([
            'success' => true,
            'message' => 'Task dependency created!'
        ]);
    }

    /**
     * Add external cost
     */
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

        return redirect()->route('epms.show', $project)
            ->with('success', 'External cost added successfully!');
    }

    /**
     * Get Gantt chart data (JSON API)
     */
    public function getGanttData($id)
    {
        $project = EPMSProject::with([
            'tasks.assignedUser',
            'tasks.dependencies.dependsOnTask',
            'milestones'
        ])->findOrFail($id);

        $ganttData = [
            'tasks' => $project->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'start' => $task->start_date->format('Y-m-d'),
                    'end' => $task->end_date->format('Y-m-d'),
                    'progress' => $task->progress,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'assigned_to' => $task->assignedUser ? $task->assignedUser->name : null,
                    'milestone_id' => $task->milestone_id,
                    'dependencies' => $task->dependencies->map(function ($dep) {
                        return [
                            'id' => $dep->depends_on_task_id,
                            'type' => $dep->dependency_type
                        ];
                    })
                ];
            }),
            'milestones' => $project->milestones->map(function ($milestone) {
                return [
                    'id' => $milestone->id,
                    'name' => $milestone->name,
                    'date' => $milestone->due_date->format('Y-m-d'),
                    'status' => $milestone->status
                ];
            })
        ];

        return response()->json($ganttData);
    }
}
