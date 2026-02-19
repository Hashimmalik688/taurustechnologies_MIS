@extends('layouts.master')

@section('title') Edit Project - {{ $project->name }} @endsection

@section('css')
<style>
    .form-section { background: #fff; border-radius: 16px; padding: 30px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #eef0f3; }
    .form-section h5 { color: #1a1a2e; font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid var(--bs-print-bg-alt); }
    .form-section h5 i { color: var(--bs-gradient-start); margin-right: 8px; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') <a href="{{ route('epms.index') }}">EPMS</a> @endslot
        @slot('title') Edit: {{ $project->name }} @endslot
    @endcomponent

    <form action="{{ route('epms.update', $project) }}" method="POST">
        @csrf @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="form-section">
                    <h5><i class="bx bx-briefcase-alt"></i> Project Information</h5>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Project Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $project->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">Select</option>
                                @foreach(['Web Application','Mobile App','API/Backend','Desktop App','Data/Analytics','AI/ML','DevOps','Other'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $project->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Objectives</label>
                            <textarea class="form-control" name="objectives" rows="2">{{ old('objectives', $project->objectives) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tech Stack</label>
                            <input type="text" class="form-control" name="tech_stack" value="{{ old('tech_stack', $project->tech_stack) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Repository URL</label>
                            <input type="url" class="form-control" name="repository_url" value="{{ old('repository_url', $project->repository_url) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-section">
                    <h5><i class="bx bx-cog"></i> Settings</h5>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            @foreach(['planning','in-progress','on-hold','completed','cancelled'] as $s)
                                <option value="{{ $s }}" {{ old('status', $project->status) == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Methodology *</label>
                        <select class="form-select" name="methodology" required>
                            @foreach(['agile','kanban','waterfall','hybrid'] as $m)
                                <option value="{{ $m }}" {{ old('methodology', $project->methodology) == $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority *</label>
                        <select class="form-select" name="priority" required>
                            @foreach(['low','medium','high','critical'] as $p)
                                <option value="{{ $p }}" {{ old('priority', $project->priority) == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Deadline *</label>
                            <input type="date" class="form-control" name="deadline" value="{{ old('deadline', $project->deadline->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Currency *</label>
                            <select class="form-select" name="currency" required>
                                <option value="PKR" {{ old('currency', $project->currency) == 'PKR' ? 'selected' : '' }}>PKR</option>
                                <option value="USD" {{ old('currency', $project->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Budget</label>
                            <input type="number" class="form-control" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project Manager</label>
                        <select class="form-select" name="project_manager_id">
                            <option value="">Select PM</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('project_manager_id', $project->project_manager_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-fill py-3" style="border-radius: 12px; font-weight: 600;">
                        <i class="bx bx-check me-1"></i> Update Project
                    </button>
                    <a href="{{ route('epms.show', $project) }}" class="btn btn-outline-secondary py-3" style="border-radius: 12px;">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
