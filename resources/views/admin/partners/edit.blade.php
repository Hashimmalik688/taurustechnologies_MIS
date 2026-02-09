@extends('layouts.master')

@section('title')
    Edit Partner
@endsection

@section('css')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet">

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

        .carrier-state-section {
            border: 2px solid #e9ecef;
            padding: 20px;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }

        .state-settlement-row {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
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

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.partners.index') }}">Partners</a>
        @endslot
        @slot('title')
            Edit Partner
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
                        Edit Partner Information
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.partners.update', $partner->id) }}" id="partnerForm">
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">
                                        <i class="mdi mdi-account me-1"></i>
                                        Partner Name
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $partner->name) }}"
                                        placeholder="Enter partner name" required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="code" class="form-label required">
                                        <i class="mdi mdi-barcode me-1"></i>
                                        Partner Code
                                    </label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                        id="code" name="code" value="{{ old('code', $partner->code) }}"
                                        placeholder="E.g., E-1, Y-1, F-1" required>
                                    @error('code')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="mdi mdi-email me-1"></i>
                                        Email Address
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $partner->email) }}"
                                        placeholder="Enter email address">
                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="mdi mdi-phone me-1"></i>
                                        Phone Number
                                    </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $partner->phone) }}"
                                        placeholder="(555) 123-4567">
                                    @error('phone')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ssn_last4" class="form-label">
                                        <i class="mdi mdi-lock me-1"></i>
                                        Last 4 of SSN
                                    </label>
                                    <input type="text" class="form-control @error('ssn_last4') is-invalid @enderror"
                                        id="ssn_last4" name="ssn_last4" value="{{ old('ssn_last4', $partner->ssn_last4) }}"
                                        maxlength="4" pattern="[0-9]{4}" placeholder="1234">
                                    @error('ssn_last4')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
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
                                        value="{{ old('our_commission_percentage', $partner->our_commission_percentage ?? 0) }}"
                                        min="0" max="100" step="0.01" placeholder="e.g., 15.00">
                                    <small class="text-muted">Percentage of total revenue that partner owes us</small>
                                    @error('our_commission_percentage')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch" style="margin-top: 32px;">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $partner->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            Active Partner
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Password Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-lock-outline me-1"></i>
                                    Password Management
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="mdi mdi-key me-1"></i>
                                        Current Password Status
                                    </label>
                                    <div class="alert alert-{{ $partner->password ? 'success' : 'warning' }} mb-0">
                                        @if($partner->password)
                                            <i class="mdi mdi-check-circle me-2"></i>
                                            Password is set
                                        @else
                                            <i class="mdi mdi-alert me-2"></i>
                                            No password set
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="mdi mdi-lock-reset me-1"></i>
                                        New Password
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password" 
                                           name="password"
                                           placeholder="Enter new password (min 8 characters)">
                                    <small class="text-muted">Leave blank to keep current password</small>
                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="mdi mdi-lock-check me-1"></i>
                                        Confirm New Password
                                    </label>
                                    <input type="password" 
                                           class="form-control"
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="Confirm new password">
                                    <small class="text-muted">Must match new password</small>
                                </div>
                            </div>
                        </div>

                        {{-- Carriers & States Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-briefcase-variant me-1"></i>
                                    Carriers & Licensed States
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information me-2"></i>
                                    For each carrier, select the states where this partner is licensed and specify their settlement percentages (Level %, Graded %, GI %, Modified %).
                                </div>
                            </div>
                        </div>

                        @include('admin.partners.partials.carrier-states', [
                            'insuranceCarriers' => $insuranceCarriers,
                            'partnerCarrierStates' => $partnerCarrierStates ?? collect()
                        ])

                        {{-- Submit Section --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.partners.index') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i>
                                        Update Partner
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
            // Initialize Select2 for all state selects
            $('.select2-multiple').select2({
                placeholder: "Select states...",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap-5'
            });

            // Show carrier sections that have existing states
            @foreach($insuranceCarriers as $carrier)
                @if(isset($partnerCarrierStates[$carrier->id]) && $partnerCarrierStates[$carrier->id]->isNotEmpty())
                    $('#carrier-state-section-{{ $carrier->id }}').removeClass('d-none');
                @endif
            @endforeach
        });

        // Update state display when states are selected/deselected (no longer creates individual fields)
        function updateStateSettlementFields(carrierId) {
            const selectElement = document.getElementById('carrier_states_' + carrierId);
            const selectedStates = Array.from(selectElement.selectedOptions).map(option => option.value);
            const selectedStatesDiv = document.getElementById('selected-states-' + carrierId);
            
            // Update selected states display
            if (selectedStatesDiv && selectedStates.length > 0) {
                selectedStatesDiv.innerHTML = `
                    <div class="alert alert-light">
                        <strong>Licensed States:</strong>
                        ${selectedStates.map(state => `<span class="badge bg-primary me-1">${state}</span>`).join('')}
                    </div>
                `;
            } else if (selectedStatesDiv) {
                selectedStatesDiv.innerHTML = '';
            }

            // Show/hide section based on whether states are selected
            const section = document.getElementById('carrier-state-section-' + carrierId);
            if (selectedStates.length > 0) {
                section.classList.remove('d-none');
            } else {
                section.classList.add('d-none');
            }
        }

        // Toggle all carriers
        function toggleAllCarriers() {
            const sections = document.querySelectorAll('.carrier-state-section');
            sections.forEach(section => {
                section.classList.toggle('d-none');
            });
        }

        // Remove carrier section
        function removeCarrierSection(carrierId) {
            if (confirm('Are you sure you want to remove this carrier from this partner? All state assignments and settlement percentages will be cleared.')) {
                const section = document.getElementById('carrier-state-section-' + carrierId);
                
                // Remove all form inputs for this carrier to prevent submission
                const selectElement = document.getElementById('carrier_states_' + carrierId);
                const settlementInputs = section.querySelectorAll('input[name^="settlement_"][name*="[' + carrierId + ']"]');
                
                // Remove the select element
                if (selectElement) {
                    selectElement.remove();
                }
                
                // Remove all settlement input fields
                settlementInputs.forEach(input => {
                    input.remove();
                });
                
                // Hide the entire section
                section.style.display = 'none';
                
                // Mark as removed for visual feedback
                section.setAttribute('data-removed', 'true');
                
                console.log('Removed carrier ' + carrierId + ' from form submission');
            }
        }

        // Open modal to create new carrier
        function openCreateCarrierModal() {
            const width = 900;
            const height = 700;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;
            
            const carrierWindow = window.open(
                '{{ route("admin.insurance-carriers.create") }}?modal=1',
                'CreateCarrier',
                `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
            );
            
            // Listen for message from child window
            window.addEventListener('message', function(event) {
                if (event.data.type === 'carrierCreated') {
                    // Reload the page to show the new carrier
                    location.reload();
                }
            });
        }
    </script>
@endsection
