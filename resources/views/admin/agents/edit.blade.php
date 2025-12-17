@extends('layouts.master')

@section('title')
    Edit Agent
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Agents
        @endslot
        @slot('title')
            Edit Agent
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-account-edit me-2"></i>
                        Edit Agent Information
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('agents.update', $user->id) }}" id="agentForm">
                        @csrf
                        @method('PUT')

                        {{-- Basic Information Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    Basic Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">
                                        <i class="mdi mdi-account me-1"></i>
                                        Full Name
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}"
                                        placeholder="Enter full name" required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label required">
                                        <i class="mdi mdi-email me-1"></i>
                                        Email Address
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}"
                                        placeholder="Enter email address" required>
                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="mdi mdi-lock me-1"></i>
                                        Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Leave blank to keep current password">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('password')">
                                            <i class="mdi mdi-eye" id="password-icon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <small class="text-muted">Leave blank to keep current password. Minimum 8 characters if changing.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label required">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        State
                                    </label>
                                    <select id="state" name="state"
                                        class="form-select @error('state') is-invalid @enderror" required>
                                        <option value="">Select state...</option>
                                        @php
                                            $states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];
                                            $currentState = old('state', $user->userDetail->state ?? '');
                                        @endphp
                                        @foreach($states as $state)
                                            <option value="{{ $state }}" {{ $currentState == $state ? 'selected' : '' }}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label required">
                                        <i class="mdi mdi-home me-1"></i>
                                        Address
                                    </label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                        placeholder="Enter complete address" required>{{ old('address', $user->userDetail->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Active States Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-map-marker-multiple me-1"></i>
                                    Active States
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="active_states" class="form-label">
                                        <i class="mdi mdi-checkbox-multiple-marked me-1"></i>
                                        Select Active States
                                    </label>
                                    @php
                                        $activeStatesArray = old('active_states', json_decode($user->userDetail->active_states ?? '[]', true) ?? []);
                                    @endphp
                                    <select id="active_states" name="active_states[]"
                                        class="form-select select2-multiple @error('active_states') is-invalid @enderror"
                                        multiple="multiple" data-placeholder="Choose active states...">
                                        @foreach($states as $state)
                                            <option value="{{ $state }}"
                                                {{ in_array($state, $activeStatesArray) ? 'selected' : '' }}>
                                                {{ $state }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('active_states')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <small class="text-muted">Select all states where this agent will be active</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Carriers Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-truck me-1"></i>
                                    Carriers
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="carriers" class="form-label">
                                        <i class="mdi mdi-plus-circle me-1"></i>
                                        Manage Carriers
                                    </label>
                                    @php
                                        $carriersArray = old('carriers', json_decode($user->userDetail->carriers ?? '[]', true) ?? []);
                                        if (empty($carriersArray)) {
                                            $carriersArray = [''];
                                        }
                                    @endphp
                                    <div id="carriers-container" class="border rounded p-3 bg-light">
                                        @foreach($carriersArray as $index => $carrier)
                                            <div class="carrier-input-group mb-2">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="mdi mdi-truck"></i></span>
                                                    <input type="text" class="form-control" name="carriers[]"
                                                        placeholder="Enter carrier name" value="{{ $carrier }}">
                                                    <button class="btn btn-outline-danger" type="button"
                                                        onclick="removeCarrier(this)">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                        onclick="addCarrier()">
                                        <i class="mdi mdi-plus me-1"></i>
                                        Add Another Carrier
                                    </button>
                                    @error('carriers')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <small class="text-muted">Add all carriers this agent will work with</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Section --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i>
                                        Update Agent
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for multiple select
            $('.select2-multiple').select2({
                placeholder: "Choose active states...",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap-5'
            });
        });

        // Function to toggle password visibility
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(inputId + '-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('mdi-eye');
                passwordIcon.classList.add('mdi-eye-off');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('mdi-eye-off');
                passwordIcon.classList.add('mdi-eye');
            }
        }

        // Function to add new carrier input
        function addCarrier() {
            const container = document.getElementById('carriers-container');
            const newCarrierGroup = document.createElement('div');
            newCarrierGroup.className = 'carrier-input-group mb-2';

            newCarrierGroup.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text"><i class="mdi mdi-truck"></i></span>
                    <input type="text"
                           class="form-control"
                           name="carriers[]"
                           placeholder="Enter carrier name">
                    <button class="btn btn-outline-danger" type="button" onclick="removeCarrier(this)">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            `;

            container.appendChild(newCarrierGroup);
        }

        // Function to remove carrier input
        function removeCarrier(button) {
            const carrierGroups = document.querySelectorAll('.carrier-input-group');
            if (carrierGroups.length > 1) {
                button.closest('.carrier-input-group').remove();
            } else {
                // If it's the last one, just clear the input
                const input = button.closest('.carrier-input-group').querySelector('input');
                input.value = '';
            }
        }

        // Form validation
        document.getElementById('agentForm').addEventListener('submit', function(e) {
            const activeStates = document.getElementById('active_states').selectedOptions;

            // Validate at least one active state is selected
            if (activeStates.length === 0) {
                e.preventDefault();
                alert('Please select at least one active state.');
                return false;
            }
        });
    </script>
@endsection

@section('css')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <style>
        .required::after {
            content: " *";
            color: red;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            min-height: 38px !important;
        }

        .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd !important;
            border: 1px solid #0d6efd !important;
            color: white !important;
            border-radius: 0.25rem !important;
        }

        .carrier-input-group {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .text-primary {
            color: #667eea !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection
