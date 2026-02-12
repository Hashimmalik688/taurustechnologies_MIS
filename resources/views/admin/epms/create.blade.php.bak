@extends('layouts.master')

@section('title')
    Create New Project
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('epms.index') }}">EPMS</a>
        @endslot
        @slot('title')
            Create Project
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="bx bx-briefcase-alt text-warning me-2"></i>New Project Setup
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('epms.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Project Information -->
                            <div class="col-lg-6">
                                <h6 class="text-uppercase text-muted mb-3">Project Information</h6>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Project Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
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
                                            <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_manager_id')
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
                                           id="client_name" name="client_name" value="{{ old('client_name') }}" required>
                                    @error('client_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="client_email" class="form-label">Client Email</label>
                                    <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                           id="client_email" name="client_email" value="{{ old('client_email') }}">
                                    @error('client_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="client_phone" class="form-label">Client Phone</label>
                                    <input type="text" class="form-control @error('client_phone') is-invalid @enderror" 
                                           id="client_phone" name="client_phone" value="{{ old('client_phone') }}">
                                    @error('client_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="region" class="form-label required">Region</label>
                                    <select class="form-select @error('region') is-invalid @enderror" 
                                            id="region" name="region" required>
                                        <option value="US" {{ old('region') == 'US' ? 'selected' : '' }}>🇺🇸 United States</option>
                                        <option value="PK" {{ old('region') == 'PK' ? 'selected' : '' }}>🇵🇰 Pakistan</option>
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
                                        <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="PKR" {{ old('currency') == 'PKR' ? 'selected' : '' }}>PKR (₨)</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="contract_value" class="form-label required">Total Contract Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="currency-symbol">$</span>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('contract_value') is-invalid @enderror" 
                                               id="contract_value" name="contract_value" 
                                               value="{{ old('contract_value') }}" required>
                                        @error('contract_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Enter the fixed-price contract amount</small>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="col-lg-6">
                                <h6 class="text-uppercase text-muted mb-3">Project Timeline</h6>
                                
                                <div class="mb-3">
                                    <label for="start_date" class="form-label required">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="deadline" class="form-label required">Project Deadline</label>
                                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                           id="deadline" name="deadline" value="{{ old('deadline') }}" required>
                                    @error('deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <small>You can add milestones, tasks, and external costs after creating the project.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-uppercase text-muted mb-3">Initial Milestones (Optional)</h6>
                                <p class="text-muted small">Add key milestones now, or add them later from the project dashboard</p>
                                
                                <div id="milestones-container">
                                    <div class="milestone-entry mb-3 p-3 border rounded">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label class="form-label">Milestone Name</label>
                                                <input type="text" class="form-control" name="milestones[0][name]" placeholder="e.g., Design Phase Complete">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Due Date</label>
                                                <input type="date" class="form-control" name="milestones[0][due_date]">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Description</label>
                                                <input type="text" class="form-control" name="milestones[0][description]" placeholder="Optional details">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMilestone()">
                                    <i class="fas fa-plus me-1"></i> Add Another Milestone
                                </button>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Note:</strong> After creating the project, you'll be taken to the interactive dashboard where you can:
                                    <ul class="mb-0 mt-2">
                                        <li>Add detailed tasks with dependencies</li>
                                        <li>Assign team members</li>
                                        <li>Track external costs</li>
                                        <li>Monitor progress with the Gantt chart</li>
                                        <li>View real-time analytics</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="fas fa-check me-1"></i> Create Project
                            </button>
                            <a href="{{ route('epms.index') }}" class="btn btn-secondary waves-effect">
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
        let milestoneCount = 1;

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

        // Add milestone functionality
        function addMilestone() {
            const container = document.getElementById('milestones-container');
            const newMilestone = `
                <div class="milestone-entry mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label">Milestone Name</label>
                            <input type="text" class="form-control" name="milestones[${milestoneCount}][name]" placeholder="e.g., Development Phase Complete">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="milestones[${milestoneCount}][due_date]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="milestones[${milestoneCount}][description]" placeholder="Optional">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.milestone-entry').remove()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newMilestone);
            milestoneCount++;
        }
    </script>
@endsection
