@extends('layouts.master')

@section('title')
    Edit Project - {{ $project->name }}
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('epms.index') }}">EPMS</a>
        @endslot
        @slot('li_2')
            <a href="{{ route('epms.show', $project) }}">{{ $project->name }}</a>
        @endslot
        @slot('title')
            Edit Project
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="bx bx-edit text-warning me-2"></i>Edit Project
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('epms.update', $project) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Project Information -->
                            <div class="col-lg-6">
                                <h6 class="text-uppercase text-muted mb-3">Project Information</h6>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Project Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $project->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description', $project->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="project_manager_id" class="form-label">Project Manager</label>
                                    <select class="form-select @error('project_manager_id') is-invalid @enderror" 
                                            id="project_manager_id" name="project_manager_id">
                                        <option value="">Select Project Manager</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ old('project_manager_id', $project->project_manager_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label required">Project Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                                        <option value="in-progress" {{ old('status', $project->status) == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="on-hold" {{ old('status', $project->status) == 'on-hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Client Information -->
                            <div class="col-lg-6">
                                <h6 class="text-uppercase text-muted mb-3">Client Information</h6>
                                
                                <div class="mb-3">
                                    <label for="client_name" class="form-label required">Client Name</label>
                                    <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                           id="client_name" name="client_name" value="{{ old('client_name', $project->client_name) }}" required>
                                    @error('client_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="client_email" class="form-label">Client Email</label>
                                    <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                           id="client_email" name="client_email" value="{{ old('client_email', $project->client_email) }}">
                                    @error('client_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="client_phone" class="form-label">Client Phone</label>
                                    <input type="text" class="form-control @error('client_phone') is-invalid @enderror" 
                                           id="client_phone" name="client_phone" value="{{ old('client_phone', $project->client_phone) }}">
                                    @error('client_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="region" class="form-label required">Region</label>
                                    <select class="form-select @error('region') is-invalid @enderror" 
                                            id="region" name="region" required>
                                        <option value="US" {{ old('region', $project->region) == 'US' ? 'selected' : '' }}>🇺🇸 United States</option>
                                        <option value="PK" {{ old('region', $project->region) == 'PK' ? 'selected' : '' }}>🇵🇰 Pakistan</option>
                                    </select>
                                    @error('region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <!-- Financial Details -->
                            <div class="col-lg-6">
                                <h6 class="text-uppercase text-muted mb-3">Financial Details</h6>
                                
                                <div class="mb-3">
                                    <label for="currency" class="form-label required">Currency</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" 
                                            id="currency" name="currency" required>
                                        <option value="USD" {{ old('currency', $project->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="PKR" {{ old('currency', $project->currency) == 'PKR' ? 'selected' : '' }}>PKR (₨)</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="contract_value" class="form-label required">Total Contract Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="currency-symbol">{{ $project->currency === 'USD' ? '$' : '₨' }}</span>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('contract_value') is-invalid @enderror" 
                                               id="contract_value" name="contract_value" 
                                               value="{{ old('contract_value', $project->contract_value) }}" required>
                                        @error('contract_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <strong>Current External Costs:</strong> {{ $project->currency }} {{ number_format($project->external_costs, 2) }}<br>
                                    <strong>Gross Profit:</strong> {{ $project->currency }} {{ number_format($project->gross_profit, 2) }}<br>
                                    <strong>Margin:</strong> {{ number_format($project->margin_percentage, 1) }}%
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="col-lg-6">
                                <h6 class="text-uppercase text-muted mb-3">Project Timeline</h6>
                                
                                <div class="mb-3">
                                    <label for="start_date" class="form-label required">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="deadline" class="form-label required">Project Deadline</label>
                                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                           id="deadline" name="deadline" value="{{ old('deadline', $project->deadline->format('Y-m-d')) }}" required>
                                    @error('deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-warning">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <small>Changing the deadline will not automatically adjust task dates. Please review the Gantt chart after saving.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="fas fa-save me-1"></i> Update Project
                            </button>
                            <a href="{{ route('epms.show', $project) }}" class="btn btn-secondary waves-effect">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Update currency symbol based on selection
        document.getElementById('currency').addEventListener('change', function() {
            const symbol = this.value === 'USD' ? '$' : '₨';
            document.getElementById('currency-symbol').textContent = symbol;
        });

        // Validate deadline is after start date
        document.getElementById('start_date').addEventListener('change', updateMinDeadline);
        function updateMinDeadline() {
            const startDate = document.getElementById('start_date').value;
            if (startDate) {
                document.getElementById('deadline').min = startDate;
            }
        }
        updateMinDeadline();
    </script>
@endsection
