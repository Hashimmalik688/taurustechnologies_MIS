@extends('layouts.master')

@section('title')
    Add New Partner
@endsection

@section('css')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
        /* ===== Animated Background ===== */
        .partners-animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: float 15s infinite ease-in-out;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: -200px;
            right: -200px;
        }

        .orb-2 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #f5af19, #f12711);
            bottom: -175px;
            left: -175px;
            animation-delay: 7s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }

        .required::after {
            content: " *";
            color: #ee0979;
            font-weight: bold;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px 16px 0 0 !important;
            padding: 1.5rem;
            border: none;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.625rem 0.875rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-secondary {
            border-radius: 12px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            border: 2px solid #e9ecef !important;
            border-radius: 10px !important;
            min-height: 42px !important;
            transition: all 0.3s ease !important;
        }

        .select2-container--focus .select2-selection {
            border-color: #667eea !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15) !important;
        }

        .select2-selection--multiple .select2-selection__choice {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            border: none !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 0.25rem 0.5rem !important;
        }

        .carrier-state-section {
            border: 2px solid #e9ecef;
            padding: 1.5rem;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.03), rgba(118, 75, 162, 0.03));
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .carrier-state-section:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
        }

        .carrier-state-section h6 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .state-settlement-row {
            background-color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .state-settlement-row:hover {
            border-color: #667eea;
            transform: translateX(5px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.15), rgba(56, 239, 125, 0.15));
            color: #11998e;
            border-left: 4px solid #11998e;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(238, 9, 121, 0.15), rgba(255, 106, 0, 0.15));
            color: #ee0979;
            border-left: 4px solid #ee0979;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.partners.index') }}">Partners</a>
        @endslot
        @slot('title')
            Add New Partner
        @endslot
    @endcomponent

    <!-- Animated Background -->
    <div class="partners-animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card glass-card">
                <div class="card-header">
                    <h5>
                        <i class="mdi mdi-account-plus me-2"></i>
                        Create New Partner
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.partners.store') }}" method="POST">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Partner Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label required">Partner Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code') }}" maxlength="10" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Unique identifier for the partner (max 10 characters)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ssn_last4" class="form-label">Last 4 SSN</label>
                                    <input type="text" class="form-control @error('ssn_last4') is-invalid @enderror"
                                           id="ssn_last4" name="ssn_last4" value="{{ old('ssn_last4') }}" 
                                           maxlength="4" pattern="[0-9]{4}">
                                    @error('ssn_last4')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">4 digits only</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="our_commission_percentage" class="form-label">
                                        <i class="mdi mdi-percent me-1"></i>
                                        Our Commission Percentage
                                    </label>
                                    <input type="number" class="form-control @error('our_commission_percentage') is-invalid @enderror"
                                        id="our_commission_percentage" name="our_commission_percentage" 
                                        value="{{ old('our_commission_percentage', 0) }}"
                                        min="0" max="100" step="0.01" placeholder="e.g., 15.00">
                                    @error('our_commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Percentage of total revenue that partner owes us</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Status
                                    </label>
                                </div>
                                <small class="text-muted">Inactive partners cannot be assigned to leads</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Carrier & State Assignments -->
                        <h5 class="mb-3">
                            <i class="mdi mdi-briefcase-outline me-2"></i>
                            Carrier & State Assignments
                        </h5>
                        <p class="text-muted mb-4">Assign insurance carriers and states to this partner</p>

                        <div id="carrier-states-container">
                            @foreach($insuranceCarriers as $carrier)
                                <div class="carrier-state-section">
                                    <h6>
                                        <i class="mdi mdi-shield-check me-2"></i>
                                        {{ $carrier->name }}
                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label">Select States</label>
                                        <select name="carrier_states[{{ $carrier->id }}][]" 
                                                class="form-select state-select" 
                                                multiple="multiple" 
                                                data-carrier-id="{{ $carrier->id }}">
                                            @foreach(config('app.us_states', [
                                                'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA',
                                                'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD',
                                                'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ',
                                                'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC',
                                                'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'
                                            ]) as $state)
                                                <option value="{{ $state }}">{{ $state }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Select multiple states where partner is licensed</small>
                                    </div>

                                    <!-- Settlement percentages will be added dynamically -->
                                    <div class="settlement-inputs-container" data-carrier-id="{{ $carrier->id }}">
                                        <!-- Dynamic inputs will appear here -->
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="mdi mdi-content-save me-1"></i>Create Partner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for state selection
            $('.state-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select states...',
                allowClear: true
            });

            // When states are selected, create settlement input fields
            $('.state-select').on('change', function() {
                const carrierId = $(this).data('carrier-id');
                const selectedStates = $(this).val() || [];
                const container = $(`.settlement-inputs-container[data-carrier-id="${carrierId}"]`);
                
                // Clear existing inputs
                container.empty();

                if (selectedStates.length > 0) {
                    selectedStates.forEach(state => {
                        const stateRow = `
                            <div class="state-settlement-row">
                                <h6 class="mb-3"><strong>${state}</strong> - Settlement Percentages</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Level %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_level[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Graded %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_graded[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">GI %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_gi[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Modified %</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control" 
                                               name="settlement_modified[${carrierId}][${state}]" 
                                               placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(stateRow);
                    });
                }
            });
        });
    </script>
@endsection
