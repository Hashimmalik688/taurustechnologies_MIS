@use('App\Support\Statuses')
@extends('layouts.master')

@section('title')
    {{ $project->name }} - Project Dashboard
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
    <style>
        /* Modern Dark Theme */
        .glass-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(51, 65, 85, 0.95) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        /* Project Header */
        .project-header {
            background: linear-gradient(135deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 50%, var(--bs-ui-info) 100%);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(139, 92, 246, 0.4);
        }

        .project-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, var(--bs-surface-800) 0%, var(--bs-surface-600) 100%);
            border-radius: 16px;
            padding: 25px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 50px rgba(139, 92, 246, 0.3);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .stat-icon.purple { background: linear-gradient(135deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 100%); }
        .stat-icon.blue { background: linear-gradient(135deg, var(--bs-ui-info) 0%, var(--bs-ui-info-dark) 100%); }
        .stat-icon.green { background: linear-gradient(135deg, var(--bs-ui-success) 0%, var(--bs-ui-success-dark) 100%); }
        .stat-icon.orange { background: linear-gradient(135deg, var(--bs-ui-warning) 0%, var(--bs-ui-warning) 100%); }
        .stat-icon.red { background: linear-gradient(135deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%); }

        .stat-label {
            color: var(--bs-surface-400);
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            color: var(--bs-white, #fff);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        /* Modern Tabs */
        .modern-tabs {
            background: linear-gradient(135deg, var(--bs-surface-800) 0%, var(--bs-surface-600) 100%);
            border-radius: 16px;
            padding: 8px;
            margin-bottom: 30px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .modern-tab {
            flex: 1;
            min-width: 140px;
            padding: 15px 20px;
            background: transparent;
            border: none;
            border-radius: 12px;
            color: var(--bs-surface-400);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .modern-tab:hover {
            color: var(--bs-white, #fff);
            background: rgba(139, 92, 246, 0.1);
        }

        .modern-tab.active {
            color: var(--bs-white, #fff);
            background: linear-gradient(135deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 100%);
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.4);
        }

        .tab-content-panel {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Kanban Board */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            padding: 10px;
        }

        .kanban-column {
            background: linear-gradient(135deg, var(--bs-surface-800) 0%, var(--bs-surface-600) 100%);
            border-radius: 12px;
            padding: 20px;
            min-height: 500px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .kanban-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid;
        }

        .kanban-task {
            background: rgba(15, 23, 42, 0.8);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: grab;
            border-left: 3px solid;
            transition: all 0.2s ease;
        }

        .kanban-task:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .kanban-task:active {
            cursor: grabbing;
        }

        .kanban-task.dragging {
            opacity: 0.5;
        }

        /* Gantt Container */
        .gantt-container {
            background: linear-gradient(135deg, var(--bs-surface-800) 0%, var(--bs-surface-600) 100%);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            overflow-x: auto;
        }

        /* Risk Matrix */
        .risk-matrix-grid {
            display: grid;
            grid-template-columns: 80px repeat(5, 1fr);
            grid-template-rows: 50px repeat(5, 80px);
            gap: 2px;
            background: rgba(255,255,255,0.05);
            padding: 2px;
            border-radius: 12px;
        }

        .risk-cell {
            background: linear-gradient(135deg, var(--bs-surface-800) 0%, var(--bs-surface-600) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--bs-surface-400);
            border-radius: 4px;
        }

        .risk-cell.header {
            background: linear-gradient(135deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 100%);
            color: var(--bs-white, #fff);
        }

        .risk-cell.data {
            font-size: 1.5rem;
            color: var(--bs-white, #fff);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .risk-cell.data:hover {
            transform: scale(1.1);
            z-index: 10;
        }

        .risk-critical { background: rgba(239, 68, 68, 0.2) !important; }
        .risk-high { background: rgba(245, 158, 11, 0.2) !important; }
        .risk-medium { background: rgba(59, 130, 246, 0.2) !important; }
        .risk-low { background: rgba(16, 185, 129, 0.2) !important; }

        /* WBS Tree */
        .wbs-tree {
            padding: 20px;
        }

        .wbs-item {
            background: rgba(15, 23, 42, 0.6);
            border-left: 3px solid var(--bs-ui-purple);
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .wbs-item:hover {
            background: rgba(15, 23, 42, 0.9);
            transform: translateX(5px);
        }

        .wbs-children {
            margin-left: 30px;
            border-left: 2px dashed rgba(139, 92, 246, 0.3);
            padding-left: 20px;
        }

        /* Burndown Chart */
        .burndown-chart {
            width: 100%;
            height: 400px;
        }

        /* Sprint Card */
        .sprint-card {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Document Item */
        .document-item {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .document-item:hover {
            background: rgba(15, 23, 42, 0.9);
            transform: translateX(5px);
        }

        /* Comment Item */
        .comment-item {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid var(--bs-ui-indigo);
        }

        /* RACI Matrix */
        .raci-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .raci-table th {
            background: linear-gradient(135deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 100%);
            color: var(--bs-white, #fff);
            padding: 12px;
            font-weight: 600;
            text-align: left;
        }

        .raci-table td {
            background: rgba(15, 23, 42, 0.6);
            color: var(--bs-surface-400);
            padding: 12px;
        }

        .raci-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            margin: 2px;
        }

        .raci-r { background: var(--bs-ui-info); color: var(--bs-white, #fff); }
        .raci-a { background: var(--bs-ui-success); color: var(--bs-white, #fff); }
        .raci-c { background: var(--bs-ui-warning); color: var(--bs-white, #fff); }
        .raci-i { background: var(--bs-surface-600); color: var(--bs-white, #fff); }

        /* Health Indicator */
        .health-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 10px currentColor;
        }

        .health-green { background: var(--bs-ui-success); color: var(--bs-ui-success); }
        .health-yellow { background: var(--bs-ui-warning); color: var(--bs-ui-warning); }
        .health-red { background: var(--bs-ui-danger); color: var(--bs-ui-danger); }

        /* Progress Bar */
        .progress-modern {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-modern {
            height: 100%;
            background: linear-gradient(90deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 100%);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        /* Modal Dark Theme */
        .modal-content {
            background: linear-gradient(135deg, var(--bs-surface-800) 0%, var(--bs-surface-600) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--bs-white, #fff);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-control, .form-select, .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--bs-white, #fff);
        }

        .form-label {
            color: var(--bs-surface-400);
            font-weight: 500;
        }

        /* Badge Styles */
        .priority-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-urgent { background: var(--bs-ui-danger); color: var(--bs-white, #fff); }
        .priority-high { background: var(--bs-ui-warning); color: var(--bs-white, #fff); }
        .priority-medium { background: var(--bs-ui-info); color: var(--bs-white, #fff); }
        .priority-low { background: var(--bs-ui-success); color: var(--bs-white, #fff); }

        /* Methodology Badge */
        .method-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .method-agile { background: var(--bs-surface-50); color: var(--bs-ui-purple); }
        .method-waterfall { background: var(--bs-surface-50); color: var(--bs-ui-info-dark); }
        .method-hybrid { background: var(--bs-surface-50); color: var(--bs-ui-danger); }
        .method-kanban { background: var(--bs-surface-50); color: var(--bs-ui-success-dark); }

        /* Text Colors */
        .text-muted-dark {
            color: var(--bs-surface-400) !important;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('epms.index') }}">EPMS</a>
        @endslot
        @slot('title')
            {{ $project->name }}
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Project Header -->
    <div class="project-header">
        <div class="row align-items-center position-relative">
            <div class="col-md-8">
                <h2 class="mb-2 text-white position-relative u-z-1">
                    <i class="bx bx-briefcase me-3"></i>{{ $project->name }}
                </h2>
                <p class="mb-3 text-white-50 position-relative u-z-1">{{ $project->description }}</p>
                <div class="d-flex gap-4 flex-wrap position-relative u-z-1">
                    <span class="text-white"><i class="bx bx-building me-2"></i>{{ $project->client_name }}</span>
                    <span class="text-white"><i class="bx bx-calendar me-2"></i>{{ $project->start_date->format('M d, Y') }} - {{ $project->deadline->format('M d, Y') }}</span>
                    <span class="method-badge method-{{ $project->methodology }}">{{ ucfirst($project->methodology) }}</span>
                    <span class="priority-badge priority-{{ $project->priority }}">{{ ucfirst($project->priority) }}</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 position-relative u-z-1">
                <div class="btn-group" role="group">
                    @canEditModule('epms')
                    <a href="{{ route('epms.edit', $project) }}" class="btn btn-light">
                        <i class="bx bx-edit me-1"></i> Edit
                    </a>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="bx bx-plus me-1"></i> Task
                    </button>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                        <i class="bx bx-flag me-1"></i> Milestone
                    </button>
                    @endcanEditModule
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="bx bx-task text-white"></i>
                </div>
                <div class="stat-label">Project Progress</div>
                <div class="stat-value">{{ number_format($project->progress_percentage, 1) }}%</div>
                <div class="text-muted-dark small">{{ $project->completed_tasks }}/{{ $project->total_tasks }} tasks</div>
                <div class="progress-modern mt-3">
                    <div class="progress-bar-modern" style="width: {{ $project->progress_percentage }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon {{ $project->health_score === 'green' ? 'green' : ($project->health_score === 'yellow' ? 'orange' : 'red') }}">
                    <i class="bx bx-heart text-white"></i>
                </div>
                <div class="stat-label">Health Score</div>
                <div class="stat-value u-fs-150">
                    <span class="health-indicator health-{{ $project->health_score }}"></span>
                    @if($project->health_score === 'green') On Track
                    @elseif($project->health_score === 'yellow') At Risk
                    @else Critical
                    @endif
                </div>
                <div class="text-muted-dark small">
                    {{ $project->days_remaining > 0 ? $project->days_remaining . ' days left' : abs($project->days_remaining) . ' days overdue' }}
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="bx bx-group text-white"></i>
                </div>
                <div class="stat-label">Team Size</div>
                <div class="stat-value">{{ $project->team_size }}</div>
                <div class="text-muted-dark small">Active members</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon {{ $project->active_risks_count > 0 ? 'red' : 'green' }}">
                    <i class="bx bx-shield text-white"></i>
                </div>
                <div class="stat-label">Active Risks</div>
                <div class="stat-value">{{ $project->active_risks_count }}</div>
                <div class="text-muted-dark small">{{ $project->critical_risks_count }} critical</div>
            </div>
        </div>
    </div>

    <!-- Modern Tabs -->
    <div class="modern-tabs">
        <button class="modern-tab active" onclick="switchTab('overview')">
            <i class="bx bx-bar-chart-alt-2 me-2"></i>Overview
        </button>
        <button class="modern-tab" onclick="switchTab('kanban')">
            <i class="bx bx-columns me-2"></i>Kanban
        </button>
        <button class="modern-tab" onclick="switchTab('gantt')">
            <i class="bx bx-chart me-2"></i>Gantt
        </button>
        <button class="modern-tab" onclick="switchTab('wbs')">
            <i class="bx bx-sitemap me-2"></i>WBS
        </button>
        <button class="modern-tab" onclick="switchTab('risks')">
            <i class="bx bx-shield me-2"></i>Risks
        </button>
        <button class="modern-tab" onclick="switchTab('team')">
            <i class="bx bx-user me-2"></i>Team
        </button>
        <button class="modern-tab" onclick="switchTab('documents')">
            <i class="bx bx-file me-2"></i>Docs
        </button>
    </div>

    <!-- Tab: Overview -->
    <div id="tab-overview" class="tab-content-panel active">
        <div class="row">
            <!-- Left Column: Charts & Sprint -->
            <div class="col-lg-8">
                @if($activeSprint)
                    <div class="glass-card p-4 mb-4">
                        <h5 class="mb-3 text-white">
                            <i class="bx bx-run me-2"></i>Active Sprint: {{ $activeSprint->name }}
                        </h5>
                        <div class="sprint-card">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-white mb-2"><strong>Goal:</strong> {{ $activeSprint->goal ?? 'N/A' }}</p>
                                    <p class="text-muted-dark mb-2">
                                        <i class="bx bx-calendar me-2"></i>
                                        {{ $activeSprint->start_date->format('M d') }} - {{ $activeSprint->end_date->format('M d') }}
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <h3 class="text-white">{{ $activeSprint->completed_points }}/{{ $activeSprint->capacity_points }}</h3>
                                    <p class="text-muted-dark mb-0">Story Points</p>
                                </div>
                            </div>
                            @if(!empty($burndownData))
                                <canvas id="burndownChart" class="mt-4" height="100"></canvas>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Milestones & Tasks Summary -->
                <div class="glass-card p-4">
                    <h5 class="mb-4 text-white"><i class="bx bx-list-ul me-2"></i>Milestones & Tasks</h5>
                    @forelse($project->milestones as $milestone)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="text-white mb-1">
 <i class="bx bx-flag me-2 text-ui-warning" ></i>{{ $milestone->name }}
                                    </h6>
                                    <small class="text-muted-dark">Due: {{ $milestone->due_date->format('M d, Y') }}</small>
                                </div>
                                <span class="badge bg-{{ $milestone->status === Statuses::EPMS_COMPLETED ? 'success' : ($milestone->status === Statuses::EPMS_MILESTONE_MISSED ? 'danger' : 'warning') }}">
                                    {{ ucfirst($milestone->status) }}
                                </span>
                            </div>
                            @php
                                $milestoneTasks = $project->tasks->where('milestone_id', $milestone->id);
                            @endphp
                            @if($milestoneTasks->count() > 0)
                                <div class="ms-4">
                                    @foreach($milestoneTasks->take(3) as $task)
 <div class="d-flex justify-content-between align-items-center mb-2 p-2 u-rounded-8" style="background: rgba(15,23,42,0.6)">
                                            <span class="text-white small">{{ $task->name }}</span>
                                            <span class="badge bg-{{ $task->status === Statuses::EPMS_COMPLETED ? 'success' : 'secondary' }}">
                                                {{ $task->progress }}%
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($milestoneTasks->count() > 3)
                                        <small class="text-muted-dark">+ {{ $milestoneTasks->count() - 3 }} more tasks</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bx bx-flag text-muted-dark" style="font-size: 3rem;"></i>
                            <p class="text-muted-dark mt-3">No milestones yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: AI Plan & Activity -->
            <div class="col-lg-4">
                @if($aiConfigured)
                    <div class="glass-card p-4 mb-4">
                        <h5 class="mb-3 text-white"><i class="bx bx-brain me-2"></i>AI Planner</h5>
                        @if($project->ai_plan)
                            <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3);">
                                <i class="bx bx-check-circle me-2"></i>
                                <span class="text-white">AI plan applied</span>
                            </div>
                            <small class="text-muted-dark">Prompt: {{ Str::limit($project->ai_prompt, 100) }}</small>
                        @else
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#aiPlanModal">
                                <i class="bx bx-bulb me-2"></i>Generate AI Plan
                            </button>
                        @endif
                    </div>
                @endif

                <!-- Budget Overview -->
                @if($project->budget)
                    <div class="glass-card p-4 mb-4">
                        <h5 class="mb-3 text-white"><i class="bx bx-dollar me-2"></i>Budget</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted-dark">Total Budget:</span>
                            <span class="text-white fw-bold">{{ $project->currency }} {{ number_format($project->budget, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted-dark">Spent:</span>
                            <span class="text-danger fw-bold">{{ $project->currency }} {{ number_format($project->budget_spent, 2) }}</span>
                        </div>
                        <div class="progress-modern">
                            <div class="progress-bar-modern" style="width: {{ $project->budget_utilization }}%; background: linear-gradient(90deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%);"></div>
                        </div>
                        <small class="text-muted-dark mt-2 d-block">{{ number_format($project->budget_utilization, 1) }}% utilized</small>
                    </div>
                @endif

                <!-- Recent Comments -->
                <div class="glass-card p-4">
                    <h5 class="mb-3 text-white"><i class="bx bx-comment me-2"></i>Recent Comments</h5>
                    @forelse($project->comments->take(3) as $comment)
                        <div class="comment-item">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="text-white mb-1">{{ $comment->user->name }}</h6>
                                    <p class="text-muted-dark mb-0 small">{{ $comment->body }}</p>
                                    <small class="text-muted-dark">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted-dark text-center">No comments yet</p>
                    @endforelse
                    <button class="btn btn-sm btn-outline-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#addCommentModal">
                        <i class="bx bx-plus me-2"></i>Add Comment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Kanban Board -->
    <div id="tab-kanban" class="tab-content-panel">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 text-white"><i class="bx bx-columns me-2"></i>Kanban Board</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bx bx-plus me-2"></i>Add Task
                </button>
            </div>

            <div class="kanban-board">
                @foreach($kanbanBoard as $columnKey => $column)
                    <div class="kanban-column" data-column="{{ $columnKey }}">
                        <div class="kanban-header" style="border-color: {{ $column['color'] }};">
                            <div>
                                <i class="bx {{ $column['icon'] }} me-2" style="color: {{ $column['color'] }};"></i>
                                <span class="text-white fw-bold">{{ $column['label'] }}</span>
                            </div>
                            <span class="badge" style="background: {{ $column['color'] }};">{{ $column['tasks']->count() }}</span>
                        </div>
                        <div class="kanban-tasks" data-column="{{ $columnKey }}">
                            @foreach($column['tasks'] as $task)
                                <div class="kanban-task" 
                                     data-task-id="{{ $task->id }}" 
                                     style="border-left-color: {{ $task->color ?? $column['color'] }};">
                                    <h6 class="text-white mb-2">{{ $task->name }}</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="priority-badge priority-{{ $task->priority }}">{{ $task->priority }}</span>
                                        @if($task->story_points)
                                            <span class="badge bg-info">{{ $task->story_points }} pts</span>
                                        @endif
                                    </div>
                                    @if($task->assignedUser)
                                        <small class="text-muted-dark d-block">
                                            <i class="bx bx-user me-1"></i>{{ $task->assignedUser->name }}
                                        </small>
                                    @endif
                                    @if($task->label)
                                        <small class="text-muted-dark d-block mt-1">
                                            <i class="bx bx-tag me-1"></i>{{ $task->label }}
                                        </small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tab: Gantt Chart -->
    <div id="tab-gantt" class="tab-content-panel">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 text-white"><i class="bx bx-chart me-2"></i>Gantt Chart</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-light" onclick="changeGanttView('Day')">Day</button>
                    <button type="button" class="btn btn-outline-light active" onclick="changeGanttView('Week')">Week</button>
                    <button type="button" class="btn btn-outline-light" onclick="changeGanttView('Month')">Month</button>
                </div>
            </div>
            <div class="gantt-container">
                <svg id="gantt"></svg>
            </div>
        </div>
    </div>

    <!-- Tab: WBS -->
    <div id="tab-wbs" class="tab-content-panel">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 text-white"><i class="bx bx-sitemap me-2"></i>Work Breakdown Structure</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWbsModal">
                    <i class="bx bx-plus me-2"></i>Add Item
                </button>
            </div>

            <div class="wbs-tree">
                @forelse($project->wbsRootItems as $wbsItem)
                    @include('admin.epms.partials.wbs-item', ['item' => $wbsItem, 'level' => 0])
                @empty
                    <div class="text-center py-5">
 <i class="bx bx-sitemap text-muted-dark u-fs-4" ></i>
                        <p class="text-muted-dark mt-3">No WBS items yet. Start building your work breakdown structure.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tab: Risks -->
    <div id="tab-risks" class="tab-content-panel">
        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 text-white"><i class="bx bx-shield me-2"></i>Risk Register</h5>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addRiskModal">
                            <i class="bx bx-plus me-2"></i>Add Risk
                        </button>
                    </div>

                    @forelse($project->risks->sortByDesc('severity_score') as $risk)
 <div class="mb-3 p-3 u-rounded-12" style="background: rgba(15,23,42,0.6); border-left: 4px solid {{ $risk->severity_level === 'critical' ? 'var(--bs-ui-danger)' : ($risk->severity_level === 'high' ? 'var(--bs-ui-warning)' : 'var(--bs-ui-info)') }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="text-white mb-1">{{ $risk->title }}</h6>
                                    <p class="text-muted-dark mb-2 small">{{ $risk->description }}</p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <span class="badge" style="background: {{ $risk->severity_level === 'critical' ? 'var(--bs-ui-danger)' : ($risk->severity_level === 'high' ? 'var(--bs-ui-warning)' : 'var(--bs-ui-info)') }};">
                                            {{ ucfirst($risk->severity_level) }} (Score: {{ $risk->severity_score }})
                                        </span>
                                        <span class="badge bg-secondary">{{ ucfirst($risk->category) }}</span>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $risk->probability)) }}</span>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <span class="badge bg-{{ $risk->status === 'resolved' ? 'success' : ($risk->status === 'mitigating' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($risk->status) }}
                                    </span>
                                </div>
                            </div>
                            @if($risk->mitigation_plan)
 <div class="mt-3 p-2 u-rounded-8" style="background: rgba(139,92,246,0.1)">
                                    <small class="text-muted-dark"><strong>Mitigation:</strong> {{ $risk->mitigation_plan }}</small>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5">
 <i class="bx bx-shield text-muted-dark u-fs-4" ></i>
                            <p class="text-muted-dark mt-3">No risks identified yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h5 class="mb-4 text-white">Risk Matrix (5x5)</h5>
                    <div class="risk-matrix-grid">
                        <!-- Headers -->
                        <div class="risk-cell"></div>
                        <div class="risk-cell header">Very Low</div>
                        <div class="risk-cell header">Low</div>
                        <div class="risk-cell header">Medium</div>
                        <div class="risk-cell header">High</div>
                        <div class="risk-cell header">Very High</div>

                        <!-- Rows (Very High Probability to Very Low) -->
                        @php
                            $probLevels = ['very_high', 'high', 'medium', 'low', 'very_low'];
                            $impactLevels = ['very_low', 'low', 'medium', 'high', 'very_high'];
                        @endphp
                        @foreach(array_reverse($probLevels) as $prob)
                            <div class="risk-cell header">{{ ucfirst(str_replace('_', ' ', $prob)) }}</div>
                            @foreach($impactLevels as $impact)
                                @php
                                    $key = $prob . '_' . $impact;
                                    $count = $riskMatrix[$key] ?? 0;
                                    $score = \App\Models\EPMSRisk::SCORE_MAP[$prob] * \App\Models\EPMSRisk::SCORE_MAP[$impact];
                                    $cellClass = $score >= 20 ? 'risk-critical' : ($score >= 12 ? 'risk-high' : ($score >= 6 ? 'risk-medium' : 'risk-low'));
                                @endphp
                                <div class="risk-cell data {{ $cellClass }}" title="{{ $count }} risks">
                                    {{ $count > 0 ? $count : '' }}
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Team & RACI -->
    <div id="tab-team" class="tab-content-panel">
        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 text-white"><i class="bx bx-group me-2"></i>RACI Matrix</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="bx bx-user-plus me-2"></i>Add Member
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="raci-table">
                            <thead>
                                <tr>
                                    <th>Team Member</th>
                                    <th>Role</th>
                                    <th>RACI</th>
                                    <th>Tasks Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->members as $member)
                                    <tr>
                                        <td class="text-white">{{ $member->user->name }}</td>
                                        <td>{{ $member->project_role ?? 'N/A' }}</td>
                                        <td>
                                            <span class="raci-badge raci-{{ strtolower(substr($member->raci_role, 0, 1)) }}">
                                                {{ strtoupper(substr($member->raci_role, 0, 1)) }}
                                            </span>
                                            {{ ucfirst($member->raci_role) }}
                                        </td>
                                        <td>
                                            @php
                                                $memberTasks = $project->tasks->where('assigned_to', $member->user_id)->count();
                                            @endphp
                                            <span class="badge bg-primary">{{ $memberTasks }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted-dark py-4">No team members assigned</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h5 class="mb-4 text-white">Resource Workload</h5>
                    @forelse($resourceWorkload as $resource)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="text-white mb-0">{{ $resource['user']->name }}</h6>
                                    <small class="text-muted-dark">{{ $resource['role'] }}</small>
                                </div>
                                <span class="badge bg-{{ $resource['utilization'] > 80 ? 'danger' : ($resource['utilization'] > 50 ? 'warning' : 'success') }}">
                                    {{ number_format($resource['utilization'], 0) }}%
                                </span>
                            </div>
                            <div class="progress-modern">
                                <div class="progress-bar-modern" style="width: {{ $resource['utilization'] }}%; background: {{ $resource['utilization'] > 80 ? 'linear-gradient(90deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%)' : 'linear-gradient(90deg, var(--bs-ui-purple) 0%, var(--bs-ui-indigo) 100%)' }};"></div>
                            </div>
                            <small class="text-muted-dark mt-1 d-block">{{ $resource['tasks'] }} tasks, {{ $resource['hours'] }}h estimated</small>
                        </div>
                    @empty
                        <p class="text-muted-dark text-center">No resource data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Documents -->
    <div id="tab-documents" class="tab-content-panel">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 text-white"><i class="bx bx-file me-2"></i>Project Documents</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                    <i class="bx bx-upload me-2"></i>Upload Document
                </button>
            </div>

            @forelse($project->documents as $doc)
                <div class="document-item">
                    <div class="d-flex align-items-center">
 <i class="bx bx-file text-primary me-3 u-fs-2" ></i>
                        <div>
                            <h6 class="text-white mb-0">{{ $doc->name }}</h6>
                            <small class="text-muted-dark">
                                {{ $doc->file_type }} • {{ $doc->formatted_size }} • Uploaded by {{ $doc->uploader->name }} • {{ $doc->created_at->format('M d, Y') }}
                            </small>
                        </div>
                    </div>
                    <a href="{{ route('epms.documents.download', [$project, $doc]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-download"></i>
                    </a>
                </div>
            @empty
                <div class="text-center py-5">
 <i class="bx bx-file text-muted-dark u-fs-4" ></i>
                    <p class="text-muted-dark mt-3">No documents uploaded yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add New Task</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.tasks.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Task Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Milestone</label>
                                <select class="form-select" name="milestone_id">
                                    <option value="">No Milestone</option>
                                    @foreach($project->milestones as $milestone)
                                        <option value="{{ $milestone->id }}">{{ $milestone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date *</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date *</label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Assign To</label>
                                <select class="form-select" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($teamMembers as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Priority *</label>
                                <select class="form-select" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sprint</label>
                                <select class="form-select" name="sprint_id">
                                    <option value="">No Sprint</option>
                                    @foreach($project->sprints as $sprint)
                                        <option value="{{ $sprint->id }}">{{ $sprint->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" name="estimated_hours" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Story Points</label>
                                <input type="number" class="form-control" name="story_points" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Label/Tags</label>
                            <input type="text" class="form-control" name="label" placeholder="e.g., Frontend, Backend, Bug">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Milestone Modal -->
    <div class="modal fade" id="addMilestoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add New Milestone</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.milestones.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Milestone Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date *</label>
                            <input type="date" class="form-control" name="due_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Add Milestone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Risk Modal -->
    <div class="modal fade" id="addRiskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add Risk to Register</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.risks.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Risk Title *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Probability *</label>
                                <select class="form-select" name="probability" required>
                                    <option value="very_low">Very Low</option>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="very_high">Very High</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Impact *</label>
                                <select class="form-select" name="impact" required>
                                    <option value="very_low">Very Low</option>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="very_high">Very High</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="technical">Technical</option>
                                    <option value="schedule">Schedule</option>
                                    <option value="budget">Budget</option>
                                    <option value="resource">Resource</option>
                                    <option value="scope">Scope</option>
                                    <option value="quality">Quality</option>
                                    <option value="external">External</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mitigation Plan</label>
                            <textarea class="form-control" name="mitigation_plan" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contingency Plan</label>
                            <textarea class="form-control" name="contingency_plan" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Risk Owner</label>
                            <select class="form-select" name="owner_id">
                                <option value="">Unassigned</option>
                                @foreach($teamMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Add Risk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add Team Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.members.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select User *</label>
                            <select class="form-select" name="user_id" required>
                                <option value="">Choose...</option>
                                @foreach($teamMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Project Role</label>
                            <input type="text" class="form-control" name="project_role" placeholder="e.g., Frontend Developer, QA Lead">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">RACI Role *</label>
                            <select class="form-select" name="raci_role" required>
                                <option value="responsible">Responsible (R) - Does the work</option>
                                <option value="accountable">Accountable (A) - Ultimately answerable</option>
                                <option value="consulted">Consulted (C) - Provides input</option>
                                <option value="informed">Informed (I) - Kept updated</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Document Modal -->
    <div class="modal fade" id="addDocumentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Upload Document</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.documents.store', $project) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Document Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File * (Max 20MB)</label>
                            <input type="file" class="form-control" name="file" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" placeholder="e.g., Requirements, Design, Contract">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Related Task (Optional)</label>
                            <select class="form-select" name="task_id">
                                <option value="">No Task</option>
                                @foreach($project->tasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Comment Modal -->
    <div class="modal fade" id="addCommentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add Comment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.comments.store', $project) }}" method="POST" id="commentForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Comment *</label>
                            <textarea class="form-control" name="body" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Related Task (Optional)</label>
                            <select class="form-select" name="task_id">
                                <option value="">General Project Comment</option>
                                @foreach($project->tasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add WBS Item Modal -->
    <div class="modal fade" id="addWbsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add WBS Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.wbs.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Item Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Parent Item</label>
                            <select class="form-select" name="parent_id">
                                <option value="">Root Level</option>
                                @foreach($project->wbsItems as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Level *</label>
                            <select class="form-select" name="level" required>
                                <option value="phase">Phase</option>
                                <option value="deliverable">Deliverable</option>
                                <option value="work_package" selected>Work Package</option>
                                <option value="activity">Activity</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" name="estimated_hours" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimated Cost</label>
                                <input type="number" step="0.01" class="form-control" name="estimated_cost" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add WBS Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AI Plan Modal -->
    @if($aiConfigured && !$project->ai_plan)
    <div class="modal fade" id="aiPlanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white"><i class="bx bx-brain me-2"></i>AI Project Planner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" style="background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.3);">
                        <i class="bx bx-info-circle me-2"></i>
                        <span class="text-white">Describe your project requirements and AI will generate a complete plan with tasks, milestones, risks, and WBS.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Requirements Prompt *</label>
                        <textarea class="form-control" id="aiPrompt" rows="6" placeholder="Example: Build a responsive e-commerce website with user authentication, product catalog, shopping cart, payment gateway integration, and admin dashboard. Target completion in 3 months with a team of 4 developers."></textarea>
                    </div>

                    <div id="aiPlanResult" class="d-none">
                        <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3);">
                            <i class="bx bx-check-circle me-2"></i>
                            <span class="text-white">AI plan generated successfully! Review and apply to project.</span>
                        </div>
 <div class="u-overflow-y-auto u-rounded-12 u-max-h-400 p-4" id="aiPlanData" style="background: rgba(15,23,42,0.6)">
                            <pre class="text-white mb-0" style="white-space: pre-wrap;"></pre>
                        </div>
                    </div>

                    <div id="aiPlanError" class="alert alert-danger d-none" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3);">
                        <i class="bx bx-error me-2"></i>
                        <span class="text-white" id="aiErrorText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="generateAiPlanBtn">
                        <i class="bx bx-bulb me-2"></i>Generate Plan
                    </button>
                    <button type="button" class="btn btn-success d-none" id="applyAiPlanBtn">
                        <i class="bx bx-check me-2"></i>Apply to Project
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Tab Switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            document.querySelectorAll('.modern-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Kanban Drag & Drop
        document.addEventListener('DOMContentLoaded', function() {
            const kanbanColumns = document.querySelectorAll('.kanban-tasks');
            
            kanbanColumns.forEach(column => {
                new Sortable(column, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'dragging',
                    onEnd: function(evt) {
                        const taskId = evt.item.dataset.taskId;
                        const newColumn = evt.to.dataset.column;
                        const newOrder = evt.newIndex;

                        // AJAX call to update task
                        fetch(`{{ route('epms.show', $project) }}/tasks/${taskId}/move`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                kanban_column: newColumn,
                                kanban_order: newOrder
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Task moved successfully');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    }
                });
            });
        });

        // Gantt Chart
        let ganttChart;
        const ganttTasks = @json($ganttTasks);
        const ganttMilestones = @json($ganttMilestones);
        const allGanttItems = [...ganttMilestones, ...ganttTasks];

        if (allGanttItems.length > 0) {
            ganttChart = new Gantt("#gantt", allGanttItems, {
                view_mode: 'Week',
                header_height: 50,
                column_width: 30,
                step: 24,
                bar_height: 20,
                bar_corner_radius: 3,
                arrow_curve: 5,
                padding: 18,
                date_format: 'YYYY-MM-DD',
                language: 'en',
                on_click: function (task) {
                    console.log('Task clicked:', task);
                },
                on_date_change: function(task, start, end) {
                    const taskId = task.id.replace('task-', '');
                    if (task.id.startsWith('task-')) {
                        updateTaskDates(taskId, start, end);
                    }
                },
                on_progress_change: function(task, progress) {
                    const taskId = task.id.replace('task-', '');
                    if (task.id.startsWith('task-')) {
                        updateTaskProgress(taskId, progress);
                    }
                }
            });
        }

        function changeGanttView(mode) {
            if (ganttChart) {
                ganttChart.change_view_mode(mode);
            }
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function updateTaskDates(taskId, start, end) {
            fetch(`{{ route('epms.tasks.update-dates', [$project, ':taskId']) }}`.replace(':taskId', taskId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    start_date: start.toISOString().split('T')[0],
                    end_date: end.toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function updateTaskProgress(taskId, progress) {
            fetch(`{{ route('epms.tasks.update-status', [$project, ':taskId']) }}`.replace(':taskId', taskId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    progress: progress,
                    status: progress === 100 ? 'completed' : 'in-progress'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Burndown Chart
        @if($activeSprint && !empty($burndownData))
        const burndownCtx = document.getElementById('burndownChart');
        if (burndownCtx) {
            new Chart(burndownCtx, {
                type: 'line',
                data: {
                    labels: @json(array_keys($burndownData)),
                    datasets: [{
                        label: 'Ideal Burndown',
                        data: @json(array_column(array_values($burndownData), 'ideal')),
                        borderColor: themeColors.surface400,
                        borderDash: [5, 5],
                        tension: 0.1
                    }, {
                        label: 'Actual Progress',
                        data: @json(array_column(array_values($burndownData), 'actual')),
                        borderColor: themeColors.purple,
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: { color: '#fff' }
                        }
                    },
                    scales: {
                        x: { 
                            ticks: { color: themeColors.surface400 },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        },
                        y: { 
                            ticks: { color: themeColors.surface400 },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        @endif

        // AI Plan Generation
        @if($aiConfigured && !$project->ai_plan)
        let currentPlanId = null;

        document.getElementById('generateAiPlanBtn')?.addEventListener('click', function() {
            const prompt = document.getElementById('aiPrompt').value;
            if (!prompt || prompt.length < 20) {
                alert('Please provide a detailed prompt (minimum 20 characters)');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';

            document.getElementById('aiPlanResult').classList.add('d-none');
            document.getElementById('aiPlanError').classList.add('d-none');

            fetch(`{{ route('epms.ai.generate-for-project', $project) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ prompt: prompt })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-bulb me-2"></i>Generate Plan';

                if (data.success) {
                    currentPlanId = data.plan_id;
                    document.getElementById('aiPlanResult').classList.remove('d-none');
                    document.getElementById('aiPlanData').querySelector('pre').textContent = JSON.stringify(data.plan, null, 2);
                    document.getElementById('applyAiPlanBtn').classList.remove('d-none');
                } else {
                    document.getElementById('aiPlanError').classList.remove('d-none');
                    document.getElementById('aiErrorText').textContent = data.error || 'Failed to generate plan';
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-bulb me-2"></i>Generate Plan';
                document.getElementById('aiPlanError').classList.remove('d-none');
                document.getElementById('aiErrorText').textContent = 'Network error: ' + error.message;
            });
        });

        document.getElementById('applyAiPlanBtn')?.addEventListener('click', function() {
            if (!currentPlanId) {
                alert('No plan to apply');
                return;
            }

            if (!confirm('This will create milestones, tasks, risks, WBS items, and sprints from the AI plan. Continue?')) {
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Applying...';

            fetch(`{{ route('epms.ai.apply', $project) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ plan_id: currentPlanId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('AI plan applied successfully!');
                    location.reload();
                } else {
                    alert('Error applying plan: ' + (data.error || 'Unknown error'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-check me-2"></i>Apply to Project';
                }
            })
            .catch(error => {
                alert('Network error: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-check me-2"></i>Apply to Project';
            });
        });
        @endif

        // Comment Form AJAX Submission
        document.getElementById('commentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    </script>

    <style>
        /* Gantt Dark Theme Styles */
        .gantt .bar-milestone {
            fill: var(--bs-ui-warning) !important;
            stroke: var(--bs-ui-warning) !important;
        }
        .gantt .bar-completed {
            fill: var(--bs-ui-success) !important;
            stroke: var(--bs-ui-success-dark) !important;
        }
        .gantt .bar {
            fill: var(--bs-ui-purple) !important;
            stroke: var(--bs-ui-purple) !important;
        }
        .gantt .bar-progress {
            fill: var(--bs-ui-indigo) !important;
        }
        .gantt .bar-label {
            fill: var(--bs-white, #fff) !important;
            font-weight: 600;
        }
        .gantt .grid-header {
            fill: rgba(255, 255, 255, 0.05) !important;
        }
        .gantt .grid-row {
            fill: transparent !important;
        }
        .gantt .row-line {
            stroke: rgba(255, 255, 255, 0.05) !important;
        }
        .gantt .tick {
            stroke: rgba(255, 255, 255, 0.1) !important;
        }
        .gantt .lower-text, .gantt .upper-text {
            fill: var(--bs-surface-400) !important;
            font-weight: 500;
        }
        .gantt .today-highlight {
            fill: rgba(139, 92, 246, 0.1) !important;
        }
    </style>
@endsection
