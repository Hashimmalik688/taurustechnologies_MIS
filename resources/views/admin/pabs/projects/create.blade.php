@extends('layouts.master')

@section('title', 'Create Project - PABS')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @component('components.breadcrumb')
        @slot('title') Create New Project @endslot
    @endcomponent

    <!-- Create Project Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Step 1: Initiation (The Request)</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pabs.projects.store') }}" method="POST">
                        @csrf

                        <!-- Section -->
                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                                <option value="">-- Select Section --</option>
                                @foreach($sections as $id => $name)
                                    <option value="{{ $id }}" {{ old('section_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Project Name -->
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Job Title / Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="project_name" class="form-control @error('project_name') is-invalid @enderror" 
                                   placeholder="e.g., Office Renovation - 3rd Floor" value="{{ old('project_name') }}" required>
                            @error('project_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Problem Statement / Justification <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Describe the problem and why this project is needed..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Total Budget (Optional) -->
                        <div class="mb-3">
                            <label for="total_budget" class="form-label">Estimated Total Budget</label>
                            <input type="number" name="total_budget" id="total_budget" class="form-control @error('total_budget') is-invalid @enderror" 
                                   placeholder="0.00" step="0.01" min="0" value="{{ old('total_budget') }}">
                            @error('total_budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('pabs.projects.index') }}" class="btn btn-secondary w-100">Cancel</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">Create Project</button>
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
                    <h6 class="card-title mb-0">Workflow Overview</h6>
                </div>
                <div class="card-body">
                    <ol class="small ps-3">
                        <li><strong>Initiation (DRAFT)</strong> - Submit request with job title and justification</li>
                        <li><strong>Scoping</strong> - Lead conducts survey and uploads requirement documents</li>
                        <li><strong>Quoting</strong> - Procurement enters market rates from 3 vendors</li>
                        <li><strong>Approval</strong> - CEO reviews and approves with budget and deadline</li>
                        <li><strong>Allocation</strong> - Accounts confirms funds are available</li>
                        <li><strong>Execution (In Progress)</strong> - Work is completed with progress updates</li>
                        <li><strong>Completion</strong> - Submit actual cost and final notes; variance flagged if needed</li>
                    </ol>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">11 Organizational Domains</h6>
                </div>
                <div class="card-body">
                    <small>
                        <ul class="ps-3 mb-0">
                            @foreach($sections as $id => $name)
                                <li>{{ $name }}</li>
                            @endforeach
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
