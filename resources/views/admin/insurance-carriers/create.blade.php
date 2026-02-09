@extends('layouts.master')

@section('title')
    Add Insurance Carrier
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
            Add New Carrier
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Create New Insurance Carrier</h4>

                    <form action="{{ route('admin.insurance-carriers.store') }}" method="POST" id="carrierForm">
                        @csrf

                        <div class="row">
                            <!-- Carrier Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Carrier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
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
                                    <option value="on_draft" {{ old('payment_module', 'on_draft') == 'on_draft' ? 'selected' : '' }}>On Draft</option>
                                    <option value="on_issue" {{ old('payment_module') == 'on_issue' ? 'selected' : '' }}>On Issue</option>
                                    <option value="as_earned" {{ old('payment_module') == 'as_earned' ? 'selected' : '' }}>As Earned</option>
                                </select>
                                @error('payment_module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How the company gets paid</small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Plan Types -->
                            <div class="col-md-12 mb-3">
                                <label for="plan_types" class="form-label">Plan Types</label>
                                <input type="text" class="form-control @error('plan_types') is-invalid @enderror" 
                                       id="plan_types" name="plan_types" value="{{ old('plan_types', 'G.I, Graded, Level, Modified') }}" 
                                       placeholder="G.I, Graded, Level, Modified">
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
                                <br><small>Example: $100/month × 9 × 75% = $675 total commission</small>
                            </div>

                            <div id="brackets-container">
                                <!-- Dynamically added brackets will appear here -->
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
                                   value="{{ old('base_commission_percentage') }}">
                            @error('base_commission_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Calculation Notes -->
                        <div class="mb-3">
                            <label for="calculation_notes" class="form-label">Additional Calculation Notes</label>
                            <textarea class="form-control @error('calculation_notes') is-invalid @enderror" 
                                      id="calculation_notes" name="calculation_notes" rows="3" 
                                      placeholder="Any special rules, exceptions, or additional context">{{ old('calculation_notes') }}</textarea>
                            @error('calculation_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Visible in dropdowns)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="bx bx-save font-size-16 align-middle me-1"></i> Create Carrier
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
let bracketIndex = 0;

// Check if opened in modal mode
const urlParams = new URLSearchParams(window.location.search);
const isModal = urlParams.get('modal') === '1';

if (isModal) {
    // Hide breadcrumb in modal mode
    const breadcrumb = document.querySelector('.page-content > .container-fluid > .row:first-child');
    if (breadcrumb) breadcrumb.style.display = 'none';
}

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

// Handle form submission in modal mode
if (isModal) {
    const form = document.getElementById('carrierForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Notify parent window
                if (window.opener) {
                    window.opener.postMessage({
                        type: 'carrierCreated',
                        carrier: data.carrier
                    }, '*');
                }
                
                // Show success message
                alert('Carrier created successfully! The page will refresh.');
                
                // Close modal
                window.close();
            } else {
                alert('Error: ' + (data.message || 'Failed to create carrier'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the carrier.');
        });
    });
}
</script>
@endsection
