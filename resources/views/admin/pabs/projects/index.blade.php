@extends('layouts.master')

@section('title', 'PABS - Projects')

@section('css')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-draft { background-color: #e9ecef; color: #495057; }
    .status-scoping { background-color: #cfe2ff; color: #084298; }
    .status-quoting { background-color: #d1ecf1; color: #055160; }
    .status-pending-approval { background-color: #fff3cd; color: #664d03; }
    .status-budget-allocated { background-color: #d1e7dd; color: #0f5132; }
    .status-in-progress { background-color: #cff4fc; color: #055160; }
    .status-completed { background-color: #d1e7dd; color: #0f5132; }
    .status-archived { background-color: #e2e3e5; color: #41464b; }

    .section-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.2rem;
        font-size: 0.75rem;
        background-color: #f0f0f0;
        color: #333;
    }

    .priority-high { color: #dc3545; font-weight: 600; }
    .priority-medium { color: #fd7e14; font-weight: 600; }
    .priority-low { color: #28a745; font-weight: 600; }

    .variance-flag {
        display: inline-block;
        background-color: #dc3545;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.2rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @component('components.breadcrumb')
        @slot('title') PABS - Project Authorization & Budget System @endslot
    @endcomponent

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter & Create Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="card-title">Projects</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('pabs.projects.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> New Project
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="section_id" class="form-select form-select-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $id => $name)
                            <option value="{{ $id }}" {{ request('section_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                        <option value="SCOPING" {{ request('status') == 'SCOPING' ? 'selected' : '' }}>Scoping</option>
                        <option value="QUOTING" {{ request('status') == 'QUOTING' ? 'selected' : '' }}>Quoting</option>
                        <option value="PENDING APPROVAL" {{ request('status') == 'PENDING APPROVAL' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="BUDGET ALLOCATED" {{ request('status') == 'BUDGET ALLOCATED' ? 'selected' : '' }}>Budget Allocated</option>
                        <option value="IN PROGRESS" {{ request('status') == 'IN PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search code or name" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="12%">Code</th>
                        <th width="18%">Project Name</th>
                        <th width="12%">Section</th>
                        <th width="10%">Status</th>
                        <th width="10%">Priority</th>
                        <th width="12%">Approved Budget</th>
                        <th width="8%">Variance</th>
                        <th width="10%">Created By</th>
                        <th width="8%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>
                                <strong>{{ $project->project_code }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('pabs.projects.show', $project) }}" class="text-decoration-none">
                                    {{ Str::limit($project->project_name, 25) }}
                                </a>
                            </td>
                            <td>
                                <span class="section-badge">
                                    {{ $sections[$project->section_id] ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ Str::lower(str_replace(' ', '-', $project->status)) }}">
                                    {{ $project->status }}
                                </span>
                            </td>
                            <td>
                                @if($project->priority)
                                    <span class="priority-{{ Str::lower($project->priority) }}">
                                        {{ $project->priority }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($project->approved_budget)
                                    ${{ number_format($project->approved_budget, 2) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($project->variance_flagged)
                                    <span class="variance-flag">FLAGGED</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $project->creator->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pabs.projects.show', $project) }}" class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="bx bx-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <span class="text-muted">No projects found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="mt-3">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Auto-submit filter form on select change
    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endsection
