@extends('layouts.master')

@section('title')
    Edit Insurance Carrier
@endsection

@section('css')
<style>
    .bracket-row {
        border-left: 3px solid #007bff;
        padding-left: 15px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
    }
    .bracket-row .remove-bracket {
        cursor: pointer;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.insurance-carriers.index') }}">Insurance Carriers</a>
        @endslot
        @slot('title')
            Edit {{ $insuranceCarrier->name }}
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Insurance Carrier</h4>

                    <form action="{{ route('admin.insurance-carriers.update', $insuranceCarrier->id) }}" method="POST" id="carrierForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Carrier Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Carrier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $insuranceCarrier->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">e.g., American Amicable, Foresters</small>
                            </div>

                            <!-- Payment Module -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_module" class="form-label">Payment Module <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_module') is-invalid @enderror" 
                                        id="payment_module" name="payment_module" required>
                                    <option value="on_draft" {{ old('payment_module', $insuranceCarrier->payment_module) == 'on_draft' ? 'selected' : '' }}>On Draft</option>
                                    <option value="on_issue" {{ old('payment_module', $insuranceCarrier->payment_module) == 'on_issue' ? 'selected' : '' }}>On Issue</option>
                                    <option value="as_earned" {{ old('payment_module', $insuranceCarrier->payment_module) == 'as_earned' ? 'selected' : '' }}>As Earned</option>
                                </select>
                                @error('payment_module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How the company gets paid</small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Phone -->
                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $insuranceCarrier->phone) }}" 
                                       placeholder="(555) 123-4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SSN Last 4 -->
                            <div class="col-md-4 mb-3">
                                <label for="ssn_last4" class="form-label">Last 4 of SSN</label>
                                <input type="text" class="form-control @error('ssn_last4') is-invalid @enderror" 
                                       id="ssn_last4" name="ssn_last4" value="{{ old('ssn_last4', $insuranceCarrier->ssn_last4) }}" 
                                       maxlength="4" pattern="[0-9]{4}" placeholder="1234">
                                @error('ssn_last4')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Plan Types -->
                            <div class="col-md-4 mb-3">
                                <label for="plan_types" class="form-label">Plan Types</label>
                                <input type="text" class="form-control @error('plan_types') is-invalid @enderror" 
                                       id="plan_types" name="plan_types" 
                                       value="{{ old('plan_types', is_array($insuranceCarrier->plan_types) ? implode(', ', $insuranceCarrier->plan_types) : $insuranceCarrier->plan_types) }}" 
                                       placeholder="Term, Whole Life, Universal">
                                @error('plan_types')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Comma-separated list</small>
                            </div>
                        </div>

                        <!-- Commission Brackets Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Age-Based Commission Brackets</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addBracket">
                                    <i class="bx bx-plus me-1"></i>Add Bracket
                                </button>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Commission Formula:</strong> Monthly Premium × 9 months × Commission %
                            </div>

                            <div id="brackets-container">
                                @foreach($insuranceCarrier->commissionBrackets as $index => $bracket)
                                <div class="bracket-row" data-index="{{ $index }}">
                                    <input type="hidden" name="brackets[{{ $index }}][id]" value="{{ $bracket->id }}">
                                    <div class="row align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label">Min Age</label>
                                            <input type="number" name="brackets[{{ $index }}][age_min]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $bracket->age_min }}" 
                                                   min="0" max="120" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Max Age</label>
                                            <input type="number" name="brackets[{{ $index }}][age_max]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $bracket->age_max }}" 
                                                   min="0" max="120" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Commission %</label>
                                            <input type="number" name="brackets[{{ $index }}][commission_percentage]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $bracket->commission_percentage }}" 
                                                   step="0.01" min="0" max="100" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Notes</label>
                                            <input type="text" name="brackets[{{ $index }}][notes]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $bracket->notes }}" 
                                                   placeholder="Optional">
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-sm btn-danger remove-bracket">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Fallback Base Commission -->
                        <div class="mb-3">
                            <label for="base_commission_percentage" class="form-label">
                                Fallback Base Commission % 
                                <small class="text-muted">(used when age doesn't match any bracket)</small>
                            </label>
                            <input type="number" step="0.01" min="0" max="100" 
                                   class="form-control @error('base_commission_percentage') is-invalid @enderror" 
                                   id="base_commission_percentage" name="base_commission_percentage" 
                                   value="{{ old('base_commission_percentage', $insuranceCarrier->base_commission_percentage) }}">
                            @error('base_commission_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Calculation Notes -->
                        <div class="mb-3">
                            <label for="calculation_notes" class="form-label">Additional Calculation Notes</label>
                            <textarea class="form-control @error('calculation_notes') is-invalid @enderror" 
                                      id="calculation_notes" name="calculation_notes" rows="3" 
                                      placeholder="Any special rules, exceptions, or additional context">{{ old('calculation_notes', $insuranceCarrier->calculation_notes) }}</textarea>
                            @error('calculation_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $insuranceCarrier->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Visible in dropdowns)
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            This carrier is currently associated with <strong>{{ $insuranceCarrier->leads()->count() }}</strong> lead(s).
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="bx bx-save font-size-16 align-middle me-1"></i> Update Carrier
                            </button>
                            <a href="{{ route('admin.insurance-carriers.index') }}" class="btn btn-secondary waves-effect">
                                <i class="bx bx-x font-size-16 align-middle me-1"></i> Cancel
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
let bracketIndex = {{ $insuranceCarrier->commissionBrackets->count() }};

document.getElementById('addBracket').addEventListener('click', function() {
    const container = document.getElementById('brackets-container');
    const newBracket = document.createElement('div');
    newBracket.className = 'bracket-row';
    newBracket.setAttribute('data-index', bracketIndex);
    newBracket.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Min Age</label>
                <input type="number" name="brackets[${bracketIndex}][age_min]" 
                       class="form-control form-control-sm" 
                       min="0" max="120" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Max Age</label>
                <input type="number" name="brackets[${bracketIndex}][age_max]" 
                       class="form-control form-control-sm" 
                       min="0" max="120" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Commission %</label>
                <input type="number" name="brackets[${bracketIndex}][commission_percentage]" 
                       class="form-control form-control-sm" 
                       step="0.01" min="0" max="100" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Notes</label>
                <input type="text" name="brackets[${bracketIndex}][notes]" 
                       class="form-control form-control-sm" 
                       placeholder="Optional">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-danger remove-bracket">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newBracket);
    bracketIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.closest('.remove-bracket')) {
        if (confirm('Are you sure you want to remove this bracket?')) {
            e.target.closest('.bracket-row').remove();
        }
    }
});
</script>
@endsection
