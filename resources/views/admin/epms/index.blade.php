@extends('layouts.master')

@section('title')
    Project Management System
@endsection

@section('css')
    <style>
        .epms-hero {
            background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
            border-radius: 20px; padding: 40px; margin-bottom: 30px;
            position: relative; overflow: hidden;
        }
        .epms-hero::before {
            content: ''; position: absolute; top: -80%; right: -30%; width: 60%; height: 250%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        }
        .epms-hero h2 { color: #fff; font-weight: 700; position: relative; z-index: 1; }
        .epms-hero p { color: rgba(255,255,255,0.85); position: relative; z-index: 1; }

        .stat-card-epms {
            background: #fff; border-radius: 16px; padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid var(--bs-print-bg-alt);
            transition: all 0.3s ease; height: 100%;
        }
        .stat-card-epms:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
        .stat-card-epms .stat-icon {
            width: 55px; height: 55px; border-radius: 14px; display: flex;
            align-items: center; justify-content: center; font-size: 24px; color: #fff; margin-bottom: 15px;
        }
        .stat-card-epms .stat-value { font-size: 2rem; font-weight: 700; color: #1a1a2e; }
        .stat-card-epms .stat-label { color: var(--bs-status-default); font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }

        .project-card-v2 {
            background: #fff; border-radius: 16px; border: 1px solid #eef0f3;
            transition: all 0.3s ease; overflow: hidden;
        }
        .project-card-v2:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .project-card-v2 .card-header-strip { height: 6px; }
        .project-card-v2 .card-body { padding: 25px; }
        .project-card-v2 .project-name { font-size: 1.1rem; font-weight: 600; color: #1a1a2e; text-decoration: none; }
        .project-card-v2 .project-name:hover { color: var(--bs-gradient-start); }

        .priority-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .priority-critical { background: #fee2e2; color: var(--bs-ui-danger-dark); }
        .priority-high { background: #fef3c7; color: #d97706; }
        .priority-medium { background: #dbeafe; color: #2563eb; }
        .priority-low { background: #dcfce7; color: #16a34a; }

        .methodology-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .method-agile { background: #ede9fe; color: #7c3aed; }
        .method-waterfall { background: #e0f2fe; color: #0284c7; }
        .method-hybrid { background: #fce7f3; color: #db2777; }
        .method-kanban { background: #ecfdf5; color: var(--bs-ui-success-dark); }

        .health-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
        .health-dot.green { background: var(--bs-ui-success); box-shadow: 0 0 8px rgba(16,185,129,0.5); }
        .health-dot.yellow { background: var(--bs-ui-warning); box-shadow: 0 0 8px rgba(245,158,11,0.5); }
        .health-dot.red { background: var(--bs-ui-danger); box-shadow: 0 0 8px rgba(239,68,68,0.5); }

        .progress-bar-modern { height: 6px; border-radius: 3px; background: var(--bs-print-bg-alt); }
        .progress-bar-modern .progress-bar { border-radius: 3px; background: linear-gradient(90deg, var(--bs-gradient-start), var(--bs-gradient-end)); }

        .ai-banner {
            background: linear-gradient(135deg, var(--bs-surface-900) 0%, var(--bs-surface-800) 100%);
            border-radius: 16px; padding: 30px; color: #fff;
            position: relative; overflow: hidden;
        }
        .ai-banner::before { content: '🤖'; position: absolute; right: 30px; top: 50%; transform: translateY(-50%); font-size: 80px; opacity: 0.15; }
        .ai-banner h5 { color: #a78bfa; font-weight: 700; }
        .ai-banner p { color: var(--bs-surface-400); }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') EPMS @endslot
        @slot('title') Project Management Dashboard @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Hero Section -->
    <div class="epms-hero">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="bx bx-rocket me-2"></i>Taurus Project Management</h2>
                <p class="mb-0 fs-5">Plan, track, and deliver projects with AI-powered insights</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('epms.create') }}" class="btn btn-light btn-lg px-4">
                    <i class="bx bx-plus me-1"></i> New Project
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="stat-card-epms">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));"><i class="bx bx-briefcase-alt-2"></i></div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Projects</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="stat-card-epms">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--bs-ui-success), var(--bs-ui-success-dark));"><i class="bx bx-play-circle"></i></div>
                <div class="stat-value">{{ $stats['active'] }}</div>
                <div class="stat-label">Active</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="stat-card-epms">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--bs-ui-info), #2563eb);"><i class="bx bx-edit-alt"></i></div>
                <div class="stat-value">{{ $stats['planning'] }}</div>
                <div class="stat-label">Planning</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="stat-card-epms">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--bs-ui-warning), #d97706);"><i class="bx bx-check-circle"></i></div>
                <div class="stat-value">{{ $stats['completed'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="stat-card-epms">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--bs-ui-purple), #7c3aed);"><i class="bx bx-task"></i></div>
                <div class="stat-value">{{ $stats['total_tasks'] }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="stat-card-epms">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--bs-ui-danger), var(--bs-ui-danger-dark));"><i class="bx bx-error-circle"></i></div>
                <div class="stat-value">{{ $stats['critical_risks'] }}</div>
                <div class="stat-label">Critical Risks</div>
            </div>
        </div>
    </div>

    <!-- AI Banner -->
    <div class="ai-banner mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5><i class="bx bx-brain me-2"></i>AI Project Planner</h5>
                <p class="mb-0">Describe your project and let AI generate a complete plan with milestones, tasks, WBS, risk analysis, and sprint planning.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('epms.create') }}" class="btn btn-outline-light">
                    <i class="bx bx-magic-wand me-1"></i> Launch AI Planner
                </a>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="row">
        @forelse($projects as $project)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="project-card-v2">
                    <div class="card-header-strip" style="background: linear-gradient(90deg,
                        {{ $project->priority === 'critical' ? 'var(--bs-ui-danger), var(--bs-ui-danger-dark)' :
                           ($project->priority === 'high' ? 'var(--bs-ui-warning), #d97706' :
                           ($project->priority === 'medium' ? 'var(--bs-ui-info), #2563eb' : 'var(--bs-ui-success), var(--bs-ui-success-dark)')) }});"></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <a href="{{ route('epms.show', $project) }}" class="project-name">{{ $project->name }}</a>
                            <span class="health-dot {{ $project->health_score }}"></span>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <span class="methodology-badge method-{{ $project->methodology ?? 'agile' }}">{{ ucfirst($project->methodology ?? 'Agile') }}</span>
                            <span class="priority-badge priority-{{ $project->priority ?? 'medium' }}">{{ ucfirst($project->priority ?? 'Medium') }}</span>
                        </div>
                        @if($project->description)
                            <p class="text-muted small mb-3">{{ Str::limit($project->description, 100) }}</p>
                        @endif
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="fw-semibold">{{ $project->progress_percentage }}%</small>
                            </div>
                            <div class="progress progress-bar-modern">
                                <div class="progress-bar" style="width: {{ $project->progress_percentage }}%"></div>
                            </div>
                        </div>
                        <div class="row text-center mb-3">
                            <div class="col-4"><div class="fw-bold text-primary">{{ $project->total_tasks }}</div><small class="text-muted">Tasks</small></div>
                            <div class="col-4"><div class="fw-bold text-success">{{ $project->completed_tasks }}</div><small class="text-muted">Done</small></div>
                            <div class="col-4"><div class="fw-bold {{ $project->days_remaining < 0 ? 'text-danger' : 'text-info' }}">{{ abs($project->days_remaining) }}</div><small class="text-muted">{{ $project->days_remaining < 0 ? 'Overdue' : 'Days Left' }}</small></div>
                        </div>
                        @if($project->budget > 0)
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Budget</span>
                                <span class="fw-semibold">{{ $project->currency }} {{ number_format($project->budget, 0) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div><i class="bx bx-user text-muted me-1"></i><small class="text-muted">{{ $project->projectManager->name ?? 'Unassigned' }}</small></div>
                            <small class="text-muted">{{ $project->deadline->format('M d, Y') }}</small>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('epms.show', $project) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bx bx-show"></i> View</a>
                            <a href="{{ route('epms.edit', $project) }}" class="btn btn-sm btn-outline-secondary flex-fill"><i class="bx bx-edit"></i> Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bx bx-folder-open display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">No projects yet</h4>
                <p class="text-muted">Create your first project to get started.</p>
                <a href="{{ route('epms.create') }}" class="btn btn-primary mt-2"><i class="bx bx-plus me-1"></i> Create First Project</a>
            </div>
        @endforelse
    </div>
@endsection
