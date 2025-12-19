@extends('layouts.master')

@section('title')
    Create Agent
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Agents
        @endslot
        @slot('title')
            Create Agent
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
                        <i class="mdi mdi-account-plus me-2"></i>
                        Agent Information
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('agents.store') }}" id="agentForm">
                        @csrf

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
                                        id="name" name="name" value="{{ old('name') }}"
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
                                        id="email" name="email" value="{{ old('email') }}"
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
                                    <label for="password" class="form-label required">
                                        <i class="mdi mdi-lock me-1"></i>
                                        Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Enter password" required>
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
                                        <small class="text-muted">Password must be at least 8 characters long</small>
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
                                        <option value="Alabama" {{ old('state') == 'Alabama' ? 'selected' : '' }}>Alabama
                                        </option>
                                        <option value="Alaska" {{ old('state') == 'Alaska' ? 'selected' : '' }}>Alaska
                                        </option>
                                        <option value="Arizona" {{ old('state') == 'Arizona' ? 'selected' : '' }}>Arizona
                                        </option>
                                        <option value="Arkansas" {{ old('state') == 'Arkansas' ? 'selected' : '' }}>
                                            Arkansas</option>
                                        <option value="California" {{ old('state') == 'California' ? 'selected' : '' }}>
                                            California</option>
                                        <option value="Colorado" {{ old('state') == 'Colorado' ? 'selected' : '' }}>
                                            Colorado</option>
                                        <option value="Connecticut" {{ old('state') == 'Connecticut' ? 'selected' : '' }}>
                                            Connecticut</option>
                                        <option value="Delaware" {{ old('state') == 'Delaware' ? 'selected' : '' }}>
                                            Delaware</option>
                                        <option value="Florida" {{ old('state') == 'Florida' ? 'selected' : '' }}>Florida
                                        </option>
                                        <option value="Georgia" {{ old('state') == 'Georgia' ? 'selected' : '' }}>Georgia
                                        </option>
                                        <option value="Hawaii" {{ old('state') == 'Hawaii' ? 'selected' : '' }}>Hawaii
                                        </option>
                                        <option value="Idaho" {{ old('state') == 'Idaho' ? 'selected' : '' }}>Idaho
                                        </option>
                                        <option value="Illinois" {{ old('state') == 'Illinois' ? 'selected' : '' }}>
                                            Illinois</option>
                                        <option value="Indiana" {{ old('state') == 'Indiana' ? 'selected' : '' }}>Indiana
                                        </option>
                                        <option value="Iowa" {{ old('state') == 'Iowa' ? 'selected' : '' }}>Iowa</option>
                                        <option value="Kansas" {{ old('state') == 'Kansas' ? 'selected' : '' }}>Kansas
                                        </option>
                                        <option value="Kentucky" {{ old('state') == 'Kentucky' ? 'selected' : '' }}>
                                            Kentucky</option>
                                        <option value="Louisiana" {{ old('state') == 'Louisiana' ? 'selected' : '' }}>
                                            Louisiana</option>
                                        <option value="Maine" {{ old('state') == 'Maine' ? 'selected' : '' }}>Maine
                                        </option>
                                        <option value="Maryland" {{ old('state') == 'Maryland' ? 'selected' : '' }}>
                                            Maryland</option>
                                        <option value="Massachusetts"
                                            {{ old('state') == 'Massachusetts' ? 'selected' : '' }}>Massachusetts</option>
                                        <option value="Michigan" {{ old('state') == 'Michigan' ? 'selected' : '' }}>
                                            Michigan</option>
                                        <option value="Minnesota" {{ old('state') == 'Minnesota' ? 'selected' : '' }}>
                                            Minnesota</option>
                                        <option value="Mississippi" {{ old('state') == 'Mississippi' ? 'selected' : '' }}>
                                            Mississippi</option>
                                        <option value="Missouri" {{ old('state') == 'Missouri' ? 'selected' : '' }}>
                                            Missouri</option>
                                        <option value="Montana" {{ old('state') == 'Montana' ? 'selected' : '' }}>Montana
                                        </option>
                                        <option value="Nebraska" {{ old('state') == 'Nebraska' ? 'selected' : '' }}>
                                            Nebraska</option>
                                        <option value="Nevada" {{ old('state') == 'Nevada' ? 'selected' : '' }}>Nevada
                                        </option>
                                        <option value="New Hampshire"
                                            {{ old('state') == 'New Hampshire' ? 'selected' : '' }}>New Hampshire</option>
                                        <option value="New Jersey" {{ old('state') == 'New Jersey' ? 'selected' : '' }}>
                                            New Jersey</option>
                                        <option value="New Mexico" {{ old('state') == 'New Mexico' ? 'selected' : '' }}>
                                            New Mexico</option>
                                        <option value="New York" {{ old('state') == 'New York' ? 'selected' : '' }}>New
                                            York</option>
                                        <option value="North Carolina"
                                            {{ old('state') == 'North Carolina' ? 'selected' : '' }}>North Carolina
                                        </option>
                                        <option value="North Dakota"
                                            {{ old('state') == 'North Dakota' ? 'selected' : '' }}>North Dakota</option>
                                        <option value="Ohio" {{ old('state') == 'Ohio' ? 'selected' : '' }}>Ohio
                                        </option>
                                        <option value="Oklahoma" {{ old('state') == 'Oklahoma' ? 'selected' : '' }}>
                                            Oklahoma</option>
                                        <option value="Oregon" {{ old('state') == 'Oregon' ? 'selected' : '' }}>Oregon
                                        </option>
                                        <option value="Pennsylvania"
                                            {{ old('state') == 'Pennsylvania' ? 'selected' : '' }}>Pennsylvania</option>
                                        <option value="Rhode Island"
                                            {{ old('state') == 'Rhode Island' ? 'selected' : '' }}>Rhode Island</option>
                                        <option value="South Carolina"
                                            {{ old('state') == 'South Carolina' ? 'selected' : '' }}>South Carolina
                                        </option>
                                        <option value="South Dakota"
                                            {{ old('state') == 'South Dakota' ? 'selected' : '' }}>South Dakota</option>
                                        <option value="Tennessee" {{ old('state') == 'Tennessee' ? 'selected' : '' }}>
                                            Tennessee</option>
                                        <option value="Texas" {{ old('state') == 'Texas' ? 'selected' : '' }}>Texas
                                        </option>
                                        <option value="Utah" {{ old('state') == 'Utah' ? 'selected' : '' }}>Utah
                                        </option>
                                        <option value="Vermont" {{ old('state') == 'Vermont' ? 'selected' : '' }}>Vermont
                                        </option>
                                        <option value="Virginia" {{ old('state') == 'Virginia' ? 'selected' : '' }}>
                                            Virginia</option>
                                        <option value="Washington" {{ old('state') == 'Washington' ? 'selected' : '' }}>
                                            Washington</option>
                                        <option value="West Virginia"
                                            {{ old('state') == 'West Virginia' ? 'selected' : '' }}>West Virginia</option>
                                        <option value="Wisconsin" {{ old('state') == 'Wisconsin' ? 'selected' : '' }}>
                                            Wisconsin</option>
                                        <option value="Wyoming" {{ old('state') == 'Wyoming' ? 'selected' : '' }}>Wyoming
                                        </option>
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="mdi mdi-phone me-1"></i>
                                        Phone Number
                                    </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}"
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
                                        <i class="mdi mdi-shield-key me-1"></i>
                                        Last 4 of SSN
                                    </label>
                                    <input type="text" class="form-control @error('ssn_last4') is-invalid @enderror"
                                        id="ssn_last4" name="ssn_last4" value="{{ old('ssn_last4') }}"
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
                                    <label for="dob" class="form-label">
                                        <i class="mdi mdi-calendar me-1"></i>
                                        Date of Birth
                                    </label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                        id="dob" name="dob" value="{{ old('dob') }}">
                                    @error('dob')
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
                                        placeholder="Enter complete address" required>{{ old('address') }}</textarea>
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
                                    <select id="active_states" name="active_states[]"
                                        class="form-select select2-multiple @error('active_states') is-invalid @enderror"
                                        multiple="multiple" data-placeholder="Choose active states...">
                                        @php
                                            $states = [
                                                'Alabama',
                                                'Alaska',
                                                'Arizona',
                                                'Arkansas',
                                                'California',
                                                'Colorado',
                                                'Connecticut',
                                                'Delaware',
                                                'Florida',
                                                'Georgia',
                                                'Hawaii',
                                                'Idaho',
                                                'Illinois',
                                                'Indiana',
                                                'Iowa',
                                                'Kansas',
                                                'Kentucky',
                                                'Louisiana',
                                                'Maine',
                                                'Maryland',
                                                'Massachusetts',
                                                'Michigan',
                                                'Minnesota',
                                                'Mississippi',
                                                'Missouri',
                                                'Montana',
                                                'Nebraska',
                                                'Nevada',
                                                'New Hampshire',
                                                'New Jersey',
                                                'New Mexico',
                                                'New York',
                                                'North Carolina',
                                                'North Dakota',
                                                'Ohio',
                                                'Oklahoma',
                                                'Oregon',
                                                'Pennsylvania',
                                                'Rhode Island',
                                                'South Carolina',
                                                'South Dakota',
                                                'Tennessee',
                                                'Texas',
                                                'Utah',
                                                'Vermont',
                                                'Virginia',
                                                'Washington',
                                                'West Virginia',
                                                'Wisconsin',
                                                'Wyoming',
                                            ];
                                            $oldActiveStates = old('active_states', []);
                                        @endphp
                                        @foreach ($states as $state)
                                            <option value="{{ $state }}"
                                                {{ in_array($state, $oldActiveStates) ? 'selected' : '' }}>
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

                        {{-- Carriers & Commission Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="mdi mdi-briefcase me-1"></i>
                                    Insurance Carriers & Commission Rates
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information me-2"></i>
                                        Select carriers this agent will work with and set individual commission percentages for each.
                                    </div>
                                    
                                    @if(isset($insuranceCarriers) && $insuranceCarriers->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">
                                                            <input type="checkbox" id="selectAllCarriers" class="form-check-input">
                                                        </th>
                                                        <th width="35%">Carrier Name</th>
                                                        <th width="20%">Payment Module</th>
                                                        <th width="20%">Base Commission %</th>
                                                        <th width="20%">Agent Commission %</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($insuranceCarriers as $carrier)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="selected_carriers[]" 
                                                                   value="{{ $carrier->id }}" 
                                                                   class="form-check-input carrier-checkbox"
                                                                   onchange="toggleCommissionInput(this, {{ $carrier->id }})">
                                                        </td>
                                                        <td>
                                                            <strong>{{ $carrier->name }}</strong>
                                                            @if($carrier->phone)
                                                                <br><small class="text-muted"><i class="mdi mdi-phone"></i> {{ $carrier->phone }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                {{ ucwords(str_replace('_', ' ', $carrier->payment_module)) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $carrier->base_commission_percentage }}%</span>
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   name="carrier_commissions[{{ $carrier->id }}]" 
                                                                   id="commission_{{ $carrier->id }}"
                                                                   class="form-control form-control-sm commission-input" 
                                                                   placeholder="{{ $carrier->base_commission_percentage }}"
                                                                   step="0.01" 
                                                                   min="0" 
                                                                   max="100"
                                                                   disabled>
                                                            <small class="text-muted">Leave blank to use base rate</small>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="mdi mdi-alert me-2"></i>
                                            No insurance carriers available. Add a new carrier below or contact administrator.
                                        </div>
                                        
                                        {{-- Quick Add Carrier Section --}}
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="mdi mdi-plus-circle me-2"></i>Quick Add Insurance Carrier</h6>
                                            </div>
                                            <div class="card-body">
                                                <form id="quickAddCarrierForm" onsubmit="addCarrier(event)">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Carrier Name *</label>
                                                                <input type="text" class="form-control" id="carrier_name" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Base Commission %</label>
                                                                <input type="number" class="form-control" id="base_commission" step="0.01" min="0" max="100" placeholder="85.00">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Age Min</label>
                                                                <input type="number" class="form-control" id="age_min" placeholder="18">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Age Max</label>
                                                                <input type="number" class="form-control" id="age_max" placeholder="80">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="mb-3">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="submit" class="btn btn-success d-block w-100">
                                                                    <i class="mdi mdi-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Submit Section --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="mdi mdi-refresh me-1"></i>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i>
                                        Create Agent
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

        // Function to reset form
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                document.getElementById('agentForm').reset();
                $('.select2-multiple').val(null).trigger('change');

                // Reset carriers to just one input
                const container = document.getElementById('carriers-container');
                container.innerHTML = `
                    <div class="carrier-input-group mb-2">
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
                    </div>
                `;
            }
        }

        // Form validation
        document.getElementById('agentForm').addEventListener('submit', function(e) {
            const activeStates = document.getElementById('active_states').selectedOptions;
            const carriers = document.querySelectorAll('input[name="carriers[]"]');
            const filledCarriers = Array.from(carriers).filter(input => input.value.trim() !== '');

            // Validate at least one active state is selected
            if (activeStates.length === 0) {
                e.preventDefault();
                alert('Please select at least one active state.');
                return false;
            }

            // Validate at least one carrier is filled
            if (filledCarriers.length === 0) {
                e.preventDefault();
                alert('Please add at least one carrier.');
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

        // Toggle commission input when carrier checkbox is checked/unchecked
        function toggleCommissionInput(checkbox, carrierId) {
            const commissionInput = document.getElementById('commission_' + carrierId);
            commissionInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                commissionInput.value = '';
            }
        }

        // Select all carriers
        document.getElementById('selectAllCarriers')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.carrier-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                const carrierId = checkbox.value;
                toggleCommissionInput(checkbox, carrierId);
            });
        });

        // Quick Add Carrier Function
        async function addCarrier(event) {
            event.preventDefault();
            
            const name = document.getElementById('carrier_name').value;
            const baseCommission = document.getElementById('base_commission').value || 85;
            const ageMin = document.getElementById('age_min').value || 18;
            const ageMax = document.getElementById('age_max').value || 80;
            
            if (!name.trim()) {
                alert('Please enter carrier name');
                return;
            }
            
            try {
                const response = await fetch('/api/carriers/quick-add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: name,
                        base_commission_percentage: baseCommission,
                        age_min: ageMin,
                        age_max: ageMax,
                        is_active: true,
                        plan_types: ['Term', 'Whole Life']
                    })
                });
                
                if (response.ok) {
                    alert('Carrier added successfully! Please refresh the page.');
                    document.getElementById('quickAddCarrierForm').reset();
                    // Optionally reload the page or update the carriers list
                    location.reload();
                } else {
                    alert('Error adding carrier. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error adding carrier. Please check your connection.');
            }
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
