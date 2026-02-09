@extends('layouts.master')

@section('title', 'Edit Project - ' . $project->project_code)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @component('components.breadcrumb')
        @slot('title') Edit Project @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Project Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pabs.projects.update', $project) }}" method="PUT">
                        @csrf
                        @method('PUT')

                        <!-- Section (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control" value="{{ $sections[$project->section_id] ?? 'N/A' }}" disabled>
                        </div>

                        <!-- Project Name -->
                        <div class="mb-3">
                            <label class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" class="form-control @error('project_name') is-invalid @enderror" 
                                   value="{{ old('project_name', $project->project_name) }}" required>
                            @error('project_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Total Budget -->
                        <div class="mb-3">
                            <label class="form-label">Estimated Total Budget</label>
                            <input type="number" name="total_budget" class="form-control @error('total_budget') is-invalid @enderror" 
                                   placeholder="0.00" step="0.01" min="0" value="{{ old('total_budget', $project->total_budget) }}">
                            @error('total_budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('pabs.projects.show', $project) }}" class="btn btn-secondary w-100">Cancel</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">Update Project</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="card-title mb-0">Project Status</h6>
                </div>
                <div class="card-body small">
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Code:</div>
                        <div class="col-7"><strong>{{ $project->project_code }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Status:</div>
                        <div class="col-7"><strong>{{ $project->status }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Created:</div>
                        <div class="col-7">{{ $project->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-5 text-muted">Created By:</div>
                        <div class="col-7">{{ $project->creator->name }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
