@extends('layouts.master')

@section('title')
    {{ $project->name }} - Dashboard
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.css">
    <style>
        /* Dark Theme Background */
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
        }

        /* Glass Morphism Card Base */
        .glass-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(51, 65, 85, 0.95) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.5);
            border-color: rgba(139, 92, 246, 0.3);
        }

        /* Project Header */
        .project-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #3b82f6 100%);
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

        .project-header h2 {
            color: #fff;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .project-header p {
            color: rgba(255,255,255,0.9);
        }

        /* Stat Cards with Gradient Accents */
        .stat-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
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
            background: linear-gradient(180deg, #8b5cf6 0%, #6366f1 100%);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 50px rgba(139, 92, 246, 0.3);
            border-color: rgba(139, 92, 246, 0.4);
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

        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%); }
        .stat-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .stat-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-icon.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        .stat-label {
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            color: #fff;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .stat-trend {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Modern Tabs */
        .modern-tabs {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
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
            min-width: 150px;
            padding: 15px 20px;
            background: transparent;
            border: none;
            border-radius: 12px;
            color: #94a3b8;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .modern-tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modern-tab:hover::before {
            opacity: 0.1;
        }

        .modern-tab.active {
            color: #fff;
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.4);
        }

        .modern-tab i {
            margin-right: 8px;
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

        /* Enhanced Gantt Container */
        .gantt-container {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            overflow-x: auto;
        }

        .gantt-container svg {
            filter: drop-shadow(0 4px 20px rgba(0,0,0,0.3));
        }

        /* Health Indicator */
        .health-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 10px currentColor;
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .health-green { background: #10b981; color: #10b981; }
        .health-yellow { background: #f59e0b; color: #f59e0b; }
        .health-red { background: #ef4444; color: #ef4444; }

        /* Resource Workload Bars */
        .resource-item {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .resource-item:hover {
            border-color: rgba(139, 92, 246, 0.3);
            transform: translateX(5px);
        }

        .workload-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .workload-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .workload-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .workload-low { background: linear-gradient(90deg, #10b981 0%, #059669 100%); }
        .workload-medium { background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); }
        .workload-high { background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); }

        /* Activity Timeline */
        .activity-timeline {
            position: relative;
            padding-left: 40px;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #8b5cf6 0%, #6366f1 50%, #3b82f6 100%);
        }

        .activity-item {
            position: relative;
            margin-bottom: 25px;
            padding: 15px 20px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            border-color: rgba(139, 92, 246, 0.3);
            transform: translateX(5px);
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -33px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.6);
            border: 2px solid #1e293b;
        }

        /* Risk Items */
        .risk-item {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .risk-item:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .risk-critical { border-left-color: #ef4444; }
        .risk-high { border-left-color: #f59e0b; }
        .risk-medium { border-left-color: #3b82f6; }
        .risk-low { border-left-color: #10b981; }

        .risk-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .risk-badge.critical { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .risk-badge.high { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .risk-badge.medium { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .risk-badge.low { background: rgba(16, 185, 129, 0.2); color: #10b981; }

        /* Task Cards */
        .task-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .task-card:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .task-priority-urgent { border-left-color: #ef4444; }
        .task-priority-high { border-left-color: #f59e0b; }
        .task-priority-medium { border-left-color: #3b82f6; }
        .task-priority-low { border-left-color: #10b981; }

        /* Progress Bar Modern */
        .progress-modern {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-modern {
            height: 100%;
            background: linear-gradient(90deg, #8b5cf6 0%, #6366f1 100%);
            border-radius: 4px;
            position: relative;
            transition: width 0.5s ease;
        }

        .progress-bar-modern::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        /* Milestone Badge */
        .milestone-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Currency Risk Card */
        .currency-risk-card {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(239, 68, 68, 0.3);
        }

        /* Timezone Widget */
        .timezone-widget {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 15px;
        }

        .timezone-widget h4 {
            color: #fff;
            font-weight: 700;
        }

        .timezone-widget p {
            color: #94a3b8;
        }

        /* Modal Dark Theme */
        .modal-content {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #8b5cf6;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
        }

        .form-label {
            color: #94a3b8;
            font-weight: 500;
        }

        /* Button Enhancements */
        .btn-light {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-light:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        /* Progress Circle SVG */
        .progress-circle {
            position: relative;
            width: 60px;
            height: 60px;
        }

        .progress-circle svg {
            transform: rotate(-90deg);
        }

        /* Text Colors */
        .text-muted-dark {
            color: #94a3b8 !important;
        }

        /* Card Headers */
        .card-header-dark {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        /* Scrollbar Dark Theme */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        }

        /* Table Dark Theme */
        .table {
            color: #94a3b8;
        }

        .table thead th {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .table td {
            border-color: rgba(255, 255, 255, 0.05);
        }

        /* Badge Enhancements */
        .badge {
            padding: 6px 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Gantt View Buttons */
        .btn-group-gantt .btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }

        .btn-group-gantt .btn.active,
        .btn-group-gantt .btn:hover {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            border-color: #8b5cf6;
            color: #fff;
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
            <i class="mdi mdi-check-all me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Project Header with Gradient -->
    <div class="project-header">
        <div class="row align-items-center position-relative">
            <div class="col-md-8">
                <h2 class="mb-2"><i class="fas fa-project-diagram me-3"></i>{{ $project->name }}</h2>
                <p class="mb-3 opacity-90">{{ $project->description }}</p>
                <div class="d-flex gap-4 flex-wrap">
                    <span><i class="fas fa-user-tie me-2"></i>{{ $project->client_name }}</span>
                    <span><i class="fas fa-globe me-2"></i>{{ $project->region }}</span>
                    <span><i class="far fa-calendar-alt me-2"></i>{{ $project->deadline->format('M d, Y') }}</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 position-relative">
                <div class="btn-group" role="group">
                    <a href="{{ route('epms.edit', $project) }}" class="btn btn-light">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="fas fa-plus me-1"></i> Task
                    </button>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                        <i class="fas fa-flag me-1"></i> Milestone
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stat Cards -->
    <div class="row mb-4">
        <!-- Progress Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-tasks text-white"></i>
                </div>
                <div class="stat-label">Project Progress</div>
                <div class="stat-value">{{ number_format($project->progress_percentage, 1) }}%</div>
                <div class="stat-trend">
                    <small class="text-muted-dark">{{ $project->completed_tasks }}/{{ $project->total_tasks }} tasks completed</small>
                </div>
                <div class="progress-modern mt-3">
                    <div class="progress-bar-modern" style="width: {{ $project->progress_percentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Health Score Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon {{ $project->health_score === 'green' ? 'green' : ($project->health_score === 'yellow' ? 'orange' : 'red') }}">
                    <i class="fas fa-heartbeat text-white"></i>
                </div>
                <div class="stat-label">Health Score</div>
                <div class="stat-value" style="font-size: 1.5rem;">
                    <span class="health-indicator health-{{ $project->health_score }}"></span>
                    @if($project->health_score === 'green') On Track
                    @elseif($project->health_score === 'yellow') At Risk
                    @else Delayed
                    @endif
                </div>
                <div class="stat-trend">
                    <small class="text-muted-dark">
                        @if($project->days_remaining > 0)
                            {{ $project->days_remaining }} days remaining
                        @else
                            {{ abs($project->days_remaining) }} days overdue
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- Velocity Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-tachometer-alt text-white"></i>
                </div>
                <div class="stat-label">Project Velocity</div>
                <div class="stat-value">{{ number_format($project->project_velocity, 2) }}</div>
                <div class="stat-trend">
                    <small class="text-muted-dark">tasks per day</small>
                    @if($project->estimated_completion_date)
                        <br><small class="text-info"><i class="fas fa-calendar-check me-1"></i>{{ $project->estimated_completion_date->format('M d') }}</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Scope Creep Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div class="stat-label">Scope Creep</div>
                <div class="stat-value">{{ $project->scope_creep_count }}</div>
                <div class="stat-trend">
                    <small class="text-muted-dark">revision tasks</small>
                    @if($project->total_tasks > 0)
                        <br><small class="text-warning">{{ number_format(($project->revision_tasks / $project->total_tasks) * 100, 1) }}% of total</small>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Modern Tab Navigation -->
    <div class="modern-tabs">
        <button class="modern-tab active" onclick="switchTab('gantt')">
            <i class="fas fa-chart-bar"></i> Gantt Chart
        </button>
        <button class="modern-tab" onclick="switchTab('tasks')">
            <i class="fas fa-tasks"></i> Tasks & Milestones
        </button>
        <button class="modern-tab" onclick="switchTab('resources')">
            <i class="fas fa-users"></i> Team Resources
        </button>
        <button class="modern-tab" onclick="switchTab('financials')">
            <i class="fas fa-dollar-sign"></i> Financials
        </button>
        <button class="modern-tab" onclick="switchTab('risks')">
            <i class="fas fa-shield-alt"></i> Risks
        </button>
        <button class="modern-tab" onclick="switchTab('activity')">
            <i class="fas fa-stream"></i> Activity Feed
        </button>
    </div>

    <!-- Tab Content: Gantt Chart -->
    <div id="tab-gantt" class="tab-content-panel active">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 text-white"><i class="fas fa-chart-gantt me-2"></i>Interactive Gantt Chart</h5>
                <div class="btn-group btn-group-sm btn-group-gantt" role="group">
                    <button type="button" class="btn" onclick="changeGanttView('Day')">Day</button>
                    <button type="button" class="btn active" onclick="changeGanttView('Week')">Week</button>
                    <button type="button" class="btn" onclick="changeGanttView('Month')">Month</button>
                </div>
            </div>
            <div class="gantt-container">
                <svg id="gantt"></svg>
            </div>
            <div class="mt-4 d-flex gap-4 flex-wrap">
                <span class="text-muted-dark"><i class="fas fa-diamond text-warning me-2"></i>Milestones</span>
                <span class="text-muted-dark"><span style="display:inline-block;width:14px;height:14px;background:#8b5cf6;border-radius:3px;margin-right:8px;"></span>Tasks</span>
                <span class="text-muted-dark"><span style="display:inline-block;width:14px;height:14px;background:#10b981;border-radius:3px;margin-right:8px;"></span>Completed</span>
                <span class="text-muted-dark"><span style="display:inline-block;width:20px;height:2px;background:#94a3b8;display:inline-block;margin-right:8px;"></span>Dependencies</span>
            </div>
        </div>
    </div>

    <!-- Tab Content: Tasks & Milestones -->
    <div id="tab-tasks" class="tab-content-panel">
        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <h5 class="mb-4 text-white"><i class="fas fa-list-check me-2"></i>All Tasks</h5>
                    @forelse($project->tasks->sortBy('order') as $task)
                        <div class="task-card task-priority-{{ $task->priority }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-white">
                                        {{ $task->name }}
                                        @if($task->task_type === 'revision')
                                            <span class="badge bg-warning ms-2">Revision</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted-dark">
                                        <i class="far fa-calendar me-1"></i>{{ $task->start_date->format('M d') }} - {{ $task->end_date->format('M d') }}
                                        @if($task->assignedUser)
                                            <i class="fas fa-user ms-3 me-1"></i>{{ $task->assignedUser->name }}
                                        @endif
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div>
                                        <div class="progress-modern" style="width: 100px;">
                                            <div class="progress-bar-modern" style="width: {{ $task->progress }}%"></div>
                                        </div>
                                        <small class="text-muted-dark">{{ $task->progress }}%</small>
                                    </div>
                                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in-progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-inbox text-muted-dark" style="font-size: 3rem;"></i>
                            <p class="text-muted-dark mt-3">No tasks yet. Add tasks to start tracking progress.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h5 class="mb-4 text-white"><i class="fas fa-flag me-2"></i>Milestones</h5>
                    @forelse($project->milestones as $milestone)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <h6 class="mb-1 text-white">{{ $milestone->name }}</h6>
                                <small class="text-muted-dark"><i class="far fa-calendar me-1"></i>{{ $milestone->due_date->format('M d, Y') }}</small>
                            </div>
                            <span class="milestone-badge bg-{{ $milestone->status === 'completed' ? 'success' : ($milestone->status === 'missed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($milestone->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-flag text-muted-dark" style="font-size: 2rem;"></i>
                            <p class="text-muted-dark mt-3 mb-0">No milestones set.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Team Resources -->
    <div id="tab-resources" class="tab-content-panel">
        <div class="glass-card p-4">
            <h5 class="mb-4 text-white"><i class="fas fa-users-gear me-2"></i>Team Workload Distribution</h5>
            
            <div class="row">
                @forelse($teamMembers as $member)
                    @php
                        $memberTasks = $project->tasks->where('assigned_to', $member->id);
                        $totalHours = $memberTasks->sum('estimated_hours') ?? 0;
                        $completedHours = $memberTasks->where('status', 'completed')->sum('estimated_hours') ?? 0;
                        $workloadPercentage = $totalHours > 0 ? min(($totalHours / 40) * 100, 100) : 0; // 40 hours baseline
                        $workloadClass = $workloadPercentage > 80 ? 'high' : ($workloadPercentage > 50 ? 'medium' : 'low');
                    @endphp
                    <div class="col-md-6 mb-3">
                        <div class="resource-item">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0 text-white">{{ $member->name }}</h6>
                                    <small class="text-muted-dark">{{ $memberTasks->count() }} tasks assigned</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $workloadClass === 'high' ? 'danger' : ($workloadClass === 'medium' ? 'warning' : 'success') }}">
                                        {{ number_format($workloadPercentage, 0) }}% Load
                                    </span>
                                </div>
                            </div>
                            <div class="workload-bar mb-2">
                                <div class="workload-fill workload-{{ $workloadClass }}" style="width: {{ $workloadPercentage }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted-dark"><i class="far fa-clock me-1"></i>{{ $totalHours }}h total</small>
                                <small class="text-success"><i class="fas fa-check me-1"></i>{{ $completedHours }}h completed</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-users text-muted-dark" style="font-size: 3rem;"></i>
                        <p class="text-muted-dark mt-3">No team members assigned.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tab Content: Financials -->
    <div id="tab-financials" class="tab-content-panel">
        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <h5 class="mb-4 text-white"><i class="fas fa-chart-line me-2"></i>Profitability Breakdown</h5>
                    
                    <div class="row text-center mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="p-3" style="border-right: 1px solid rgba(255,255,255,0.1);">
                                <small class="text-muted-dark d-block mb-2">Contract Value</small>
                                <h3 class="text-white mb-0">{{ $project->currency }} {{ number_format($project->contract_value, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3" style="border-right: 1px solid rgba(255,255,255,0.1);">
                                <small class="text-muted-dark d-block mb-2">External Costs</small>
                                <h3 class="text-danger mb-2">{{ $project->currency }} {{ number_format($project->external_costs, 2) }}</h3>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCostModal">
                                    <i class="fas fa-plus me-1"></i> Add Cost
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3">
                                <small class="text-muted-dark d-block mb-2">Gross Profit</small>
                                <h3 class="text-success mb-2">{{ $project->currency }} {{ number_format($project->gross_profit, 2) }}</h3>
                                <span class="badge bg-success">{{ number_format($project->margin_percentage, 1) }}% Margin</span>
                            </div>
                        </div>
                    </div>

                    @if($project->externalCosts->count() > 0)
                        <hr style="border-color: rgba(255,255,255,0.1);">
                        <h6 class="mb-3 text-white">Cost Breakdown</h6>
                        <div class="table-responsive">
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Vendor</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->externalCosts as $cost)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ ucfirst($cost->cost_type) }}</span></td>
                                            <td class="text-white">{{ $cost->name }}</td>
                                            <td>{{ $cost->vendor_name ?? 'N/A' }}</td>
                                            <td class="text-end text-danger fw-bold">{{ $cost->currency }} {{ number_format($cost->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt text-muted-dark" style="font-size: 3rem;"></i>
                            <p class="text-muted-dark mt-3">No external costs recorded.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                @if($project->region === 'US')
                    <div class="glass-card p-4 mb-3">
                        <h5 class="mb-3 text-white"><i class="fas fa-clock me-2"></i>Client Timezone</h5>
                        <div class="timezone-widget">
                            <h4 id="clientTime" class="mb-2 text-white">--:--:--</h4>
                            <p class="mb-0">Eastern Time (EST)</p>
                        </div>
                        <div class="timezone-widget">
                            <h4 id="localTime" class="mb-2 text-white">--:--:--</h4>
                            <p class="mb-0">Pakistan Time (PKT)</p>
                        </div>
                    </div>

                    @if($currencyRisk)
                        <div class="currency-risk-card">
                            <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Currency Risk Alert</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Current Rate:</span>
                                <strong>{{ $currencyRisk['current_rate'] }} PKR/USD</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Last Week:</span>
                                <span>{{ $currencyRisk['last_week_rate'] }} PKR/USD</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Change:</span>
                                <span class="{{ $currencyRisk['change_percentage'] < 0 ? 'text-success' : 'text-warning' }}">
                                    <i class="fas fa-{{ $currencyRisk['change_percentage'] < 0 ? 'arrow-down' : 'arrow-up' }} me-1"></i>
                                    {{ abs($currencyRisk['change_percentage']) }}%
                                </span>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="glass-card p-4">
                        <h5 class="mb-3 text-white"><i class="fas fa-map-marker-alt me-2"></i>Local Project</h5>
                        <div class="alert alert-success mb-0" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3);">
                            <i class="fas fa-check-circle me-2"></i>
                            <span class="text-white">Pakistan-based project with no currency risk.</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab Content: Risks -->
    <div id="tab-risks" class="tab-content-panel">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 text-white"><i class="fas fa-shield-alt me-2"></i>Risk Register</h5>
                <button class="btn btn-sm btn-danger" onclick="alert('Add Risk feature - coming soon')">
                    <i class="fas fa-plus me-1"></i> Add Risk
                </button>
            </div>

            <!-- Sample Risk Items -->
            <div class="risk-item risk-critical">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="text-white mb-1">Deadline Pressure</h6>
                        <p class="text-muted-dark mb-2 small">Tight timeline may impact quality if scope increases.</p>
                        <span class="risk-badge critical">Critical</span>
                        <span class="badge bg-secondary ms-2">Schedule</span>
                    </div>
                    <span class="text-muted-dark small">Probability: 70%</span>
                </div>
                <div class="mt-3">
                    <small class="text-muted-dark"><strong>Mitigation:</strong> Regular scope reviews and client communication</small>
                </div>
            </div>

            @if($project->region === 'US' && $currencyRisk && abs($currencyRisk['change_percentage']) > 2)
                <div class="risk-item risk-high">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="text-white mb-1">Currency Exchange Rate Volatility</h6>
                            <p class="text-muted-dark mb-2 small">USD/PKR rate changed {{ $currencyRisk['change_percentage'] }}% this week.</p>
                            <span class="risk-badge high">High</span>
                            <span class="badge bg-secondary ms-2">Financial</span>
                        </div>
                        <span class="text-muted-dark small">Impact: {{ abs($currencyRisk['change_percentage']) > 5 ? 'High' : 'Medium' }}</span>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted-dark"><strong>Mitigation:</strong> Consider contract amendments or fixed rate agreements</small>
                    </div>
                </div>
            @endif

            @if($project->scope_creep_count > 5)
                <div class="risk-item risk-high">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="text-white mb-1">Scope Creep Detected</h6>
                            <p class="text-muted-dark mb-2 small">{{ $project->scope_creep_count }} revision tasks added, {{ number_format(($project->revision_tasks / $project->total_tasks) * 100, 1) }}% of total work.</p>
                            <span class="risk-badge high">High</span>
                            <span class="badge bg-secondary ms-2">Scope</span>
                        </div>
                        <span class="text-muted-dark small">Trend: Increasing</span>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted-dark"><strong>Mitigation:</strong> Implement change request process and client approval workflow</small>
                    </div>
                </div>
            @endif

            @if($project->health_score === 'red' || $project->days_remaining < 0)
                <div class="risk-item risk-critical">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="text-white mb-1">Project Behind Schedule</h6>
                            <p class="text-muted-dark mb-2 small">Project is {{ abs($project->days_remaining) }} days overdue with {{ number_format(100 - $project->progress_percentage, 1) }}% work remaining.</p>
                            <span class="risk-badge critical">Critical</span>
                            <span class="badge bg-secondary ms-2">Schedule</span>
                        </div>
                        <span class="text-muted-dark small">Status: Active</span>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted-dark"><strong>Action Required:</strong> Resource reallocation and deadline negotiation needed</small>
                    </div>
                </div>
            @endif

            <div class="risk-item risk-medium">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="text-white mb-1">Resource Availability</h6>
                        <p class="text-muted-dark mb-2 small">Key team members may have competing priorities.</p>
                        <span class="risk-badge medium">Medium</span>
                        <span class="badge bg-secondary ms-2">Resources</span>
                    </div>
                    <span class="text-muted-dark small">Probability: 40%</span>
                </div>
                <div class="mt-3">
                    <small class="text-muted-dark"><strong>Mitigation:</strong> Cross-training and backup resource identification</small>
                </div>
            </div>

            <div class="risk-item risk-low">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="text-white mb-1">Technology Dependencies</h6>
                        <p class="text-muted-dark mb-2 small">Third-party API integrations may experience downtime.</p>
                        <span class="risk-badge low">Low</span>
                        <span class="badge bg-secondary ms-2">Technical</span>
                    </div>
                    <span class="text-muted-dark small">Impact: Low</span>
                </div>
                <div class="mt-3">
                    <small class="text-muted-dark"><strong>Mitigation:</strong> Implement fallback mechanisms and caching</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Activity Feed -->
    <div id="tab-activity" class="tab-content-panel">
        <div class="glass-card p-4">
            <h5 class="mb-4 text-white"><i class="fas fa-history me-2"></i>Recent Activity</h5>
            
            <div class="activity-timeline">
                @foreach($project->tasks->sortByDesc('updated_at')->take(10) as $task)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white mb-1">
                                    @if($task->status === 'completed')
                                        <i class="fas fa-check-circle text-success me-2"></i>Task Completed
                                    @elseif($task->status === 'in-progress')
                                        <i class="fas fa-spinner text-primary me-2"></i>Task In Progress
                                    @else
                                        <i class="fas fa-circle text-secondary me-2"></i>Task Created
                                    @endif
                                </h6>
                                <p class="text-white mb-1">{{ $task->name }}</p>
                                <small class="text-muted-dark">
                                    @if($task->assignedUser)
                                        by {{ $task->assignedUser->name }}
                                    @endif
                                </small>
                            </div>
                            <small class="text-muted-dark">{{ $task->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach

                @forelse($project->milestones->sortByDesc('updated_at')->take(5) as $milestone)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white mb-1">
                                    <i class="fas fa-flag text-warning me-2"></i>Milestone {{ ucfirst($milestone->status) }}
                                </h6>
                                <p class="text-white mb-1">{{ $milestone->name }}</p>
                                <small class="text-muted-dark">Due: {{ $milestone->due_date->format('M d, Y') }}</small>
                            </div>
                            <small class="text-muted-dark">{{ $milestone->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                @endforelse

                @if($project->tasks->count() === 0 && $project->milestones->count() === 0)
                    <div class="text-center py-5">
                        <i class="fas fa-inbox text-muted-dark" style="font-size: 3rem;"></i>
                        <p class="text-muted-dark mt-3">No activity yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.tasks.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Task Name</label>
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
                                <label class="form-label required">Start Date</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">End Date</label>
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
                                <label class="form-label required">Priority</label>
                                <select class="form-select" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Task Type</label>
                                <select class="form-select" name="task_type" required>
                                    <option value="standard" selected>Standard</option>
                                    <option value="revision">Revision</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimated Hours</label>
                            <input type="number" class="form-control" name="estimated_hours" min="0">
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
                    <h5 class="modal-title">Add New Milestone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.milestones.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Milestone Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Due Date</label>
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

    <!-- Add External Cost Modal -->
    <div class="modal fade" id="addCostModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add External Cost</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('epms.costs.store', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Cost Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Cost Type</label>
                                <select class="form-select" name="cost_type" required>
                                    <option value="asset">Asset</option>
                                    <option value="api">API</option>
                                    <option value="subcontractor">Subcontractor</option>
                                    <option value="software">Software</option>
                                    <option value="hardware">Hardware</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vendor Name</label>
                                <input type="text" class="form-control" name="vendor_name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Amount</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Currency</label>
                                <select class="form-select" name="currency" required>
                                    <option value="USD">USD</option>
                                    <option value="PKR">PKR</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Incurred Date</label>
                            <input type="date" class="form-control" name="incurred_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Cost</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>
    <script>
        // Tab Switching Function
        function switchTab(tabName) {
            // Hide all tab content
            document.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.modern-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById('tab-' + tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        // Timezone clocks for US clients
        @if($project->region === 'US')
            function updateClocks() {
                const now = new Date();
                
                // Client time (EST)
                const estTime = new Date(now.toLocaleString('en-US', { timeZone: 'America/New_York' }));
                const clientTimeEl = document.getElementById('clientTime');
                if (clientTimeEl) {
                    clientTimeEl.textContent = estTime.toLocaleTimeString('en-US');
                }
                
                // Local time (PKT)
                const pktTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Karachi' }));
                const localTimeEl = document.getElementById('localTime');
                if (localTimeEl) {
                    localTimeEl.textContent = pktTime.toLocaleTimeString('en-US');
                }
            }
            
            updateClocks();
            setInterval(updateClocks, 1000);
        @endif

        // Gantt Chart Implementation
        let ganttChart;
        let currentView = 'Week';

        const tasks = @json($ganttTasks);
        const milestones = @json($ganttMilestones);

        const allItems = [...milestones, ...tasks];

        if (allItems.length > 0) {
            ganttChart = new Gantt("#gantt", allItems, {
                view_mode: currentView,
                header_height: 50,
                column_width: 30,
                step: 24,
                bar_height: 20,
                bar_corner_radius: 3,
                arrow_curve: 5,
                padding: 18,
                view_modes: ['Day', 'Week', 'Month'],
                date_format: 'YYYY-MM-DD',
                language: 'en',
                on_click: function (task) {
                    console.log('Task clicked:', task);
                },
                on_date_change: function(task, start, end) {
                    // Update task dates via AJAX
                    const taskId = task.id.replace('task-', '');
                    if (task.id.startsWith('task-')) {
                        updateTaskDates(taskId, start, end);
                    }
                },
                on_progress_change: function(task, progress) {
                    // Update task progress via AJAX
                    const taskId = task.id.replace('task-', '');
                    if (task.id.startsWith('task-')) {
                        updateTaskProgress(taskId, progress);
                    }
                }
            });
        }

        function changeGanttView(mode) {
            currentView = mode;
            if (ganttChart) {
                ganttChart.change_view_mode(mode);
            }
            
            // Update button states
            document.querySelectorAll('.btn-group-gantt button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function updateTaskDates(taskId, start, end) {
            fetch(`{{ route('epms.show', $project) }}/tasks/${taskId}/dates`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    start_date: start.toISOString().split('T')[0],
                    end_date: end.toISOString().split('T')[0]
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Reload page to show cascaded changes
                      location.reload();
                  }
              });
        }

        function updateTaskProgress(taskId, progress) {
            fetch(`{{ route('epms.show', $project) }}/tasks/${taskId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    progress: progress,
                    status: progress === 100 ? 'completed' : 'in-progress'
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      location.reload();
                  }
              });
        }

        // Add smooth scroll behavior
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards on load
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>

    <style>
        /* Enhanced Gantt chart dark theme styles */
        .gantt .bar-milestone {
            fill: #f59e0b !important;
            stroke: #d97706 !important;
        }
        .gantt .bar-completed {
            fill: #10b981 !important;
            stroke: #059669 !important;
        }
        .gantt .bar-urgent {
            fill: #ef4444 !important;
            stroke: #dc2626 !important;
        }
        .gantt .bar {
            fill: #8b5cf6 !important;
            stroke: #7c3aed !important;
        }
        .gantt .bar-progress {
            fill: #6366f1 !important;
        }
        .gantt .bar-label {
            fill: #fff !important;
            font-weight: 600;
        }
        .gantt-container svg {
            background: transparent !important;
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
            fill: #94a3b8 !important;
            font-weight: 500;
        }
        .gantt .today-highlight {
            fill: rgba(139, 92, 246, 0.1) !important;
        }
    </style>
@endsection
