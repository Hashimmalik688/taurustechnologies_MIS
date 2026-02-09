@extends('layouts.master')

@section('title', 'Create Ticket - PABS')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @component('components.breadcrumb')
        @slot('title') Create New Support Ticket @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Submit Support Ticket</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pabs.tickets.store') }}" method="POST">
                        @csrf

                        <!-- Section -->
                        <div class="mb-3">
                            <label for="section_id" class="form-label">Department / Section <span class="text-danger">*</span></label>
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

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" 
                                   placeholder="Brief description of the issue" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Provide detailed information about the issue..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="MEDIUM" selected>Medium</option>
                                <option value="HIGH">High</option>
                                <option value="LOW">Low</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assign To -->
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assign To <span class="text-danger">*</span></label>
                            <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                                <option value="">-- Select User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estimated Budget -->
                        <div class="mb-3">
                            <label for="total_cost" class="form-label">Estimated Budget (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" name="total_cost" id="total_cost" class="form-control @error('total_cost') is-invalid @enderror" 
                                       placeholder="0.00" step="0.01" min="0" value="{{ old('total_cost') }}">
                            </div>
                            @error('total_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quote Amount -->
                        <div class="mb-3">
                            <label for="quote_amount" class="form-label">Quote/Amount (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" name="quote_amount" id="quote_amount" class="form-control @error('quote_amount') is-invalid @enderror" 
                                       placeholder="0.00" step="0.01" min="0" value="{{ old('quote_amount') }}">
                            </div>
                            @error('quote_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('pabs.tickets.index') }}" class="btn btn-secondary w-100">Cancel</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">Submit Ticket</button>
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
                    <h6 class="card-title mb-0">Ticket System Info</h6>
                </div>
                <div class="card-body small">
                    <p><strong>What is this?</strong></p>
                    <p>Use this system to report issues, request support, or coordinate work related to any departmental function.</p>
                    
                    <p><strong>Priority Levels:</strong></p>
                    <ul class="ps-3 mb-3">
                        <li><strong>High:</strong> Urgent, blocking work</li>
                        <li><strong>Medium:</strong> Standard request</li>
                        <li><strong>Low:</strong> Non-urgent, can wait</li>
                    </ul>

                    <p><strong>Sections:</strong></p>
                    <ul class="ps-3 mb-0">
                        @foreach($sections as $id => $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
