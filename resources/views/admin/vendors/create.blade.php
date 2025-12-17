@extends('layouts.master')

@section('title')
    Create Vendor
@endsection

@section('css')
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .glassmorphism-card:hover {
            border-color: rgba(212, 175, 55, 0.4);
            box-shadow: 0 12px 48px rgba(212, 175, 55, 0.15);
        }

        .section-header {
            color: #d4af37;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            color: #cbd5e1;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: " *";
            color: #ef4444;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: #d4af37;
            color: #cbd5e1;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .form-select option {
            background: #0f172a;
            color: #cbd5e1;
        }

        .gold-gradient-btn {
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            border: none;
            color: #0f172a;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .gold-gradient-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
            color: #0f172a;
        }

        .btn-secondary-custom {
            background: rgba(100, 116, 139, 0.3);
            border: 1px solid rgba(100, 116, 139, 0.5);
            color: #cbd5e1;
            font-weight: 500;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: rgba(100, 116, 139, 0.5);
            border-color: rgba(100, 116, 139, 0.7);
            color: #cbd5e1;
        }

        .page-header {
            color: #d4af37;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .invalid-feedback {
            color: #fca5a5;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .commission-input-group {
            position: relative;
        }

        .commission-input-group .form-control {
            padding-right: 2.5rem;
        }

        .commission-input-group::after {
            content: '%';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #d4af37;
            font-weight: 600;
            pointer-events: none;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Vendors
        @endslot
        @slot('title')
            Create New Vendor
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-account-plus"></i>
                Create New Vendor
            </h2>

            <form action="{{ route('vendors.store') }}" method="POST">
                @csrf

                <div class="glassmorphism-card mb-4">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="mdi mdi-information"></i>
                            Basic Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Vendor Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" placeholder="Enter vendor name"
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name" name="company_name" placeholder="Enter company name"
                                           value="{{ old('company_name') }}">
                                    @error('company_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vendor_type" class="form-label required">Vendor Type</label>
                                    <select class="form-select @error('vendor_type') is-invalid @enderror"
                                            id="vendor_type" name="vendor_type" required>
                                        <option value="">Select Type</option>
                                        <option value="us_agent" {{ old('vendor_type') == 'us_agent' ? 'selected' : '' }}>US Agent</option>
                                        <option value="vendor" {{ old('vendor_type') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                        <option value="supplier" {{ old('vendor_type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                    </select>
                                    @error('vendor_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glassmorphism-card mb-4">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="mdi mdi-phone"></i>
                            Contact Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label required">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" placeholder="vendor@example.com"
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label required">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" placeholder="(555) 123-4567"
                                           value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address" name="address" rows="3"
                                              placeholder="Enter full address">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city" placeholder="City"
                                           value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                           id="state" name="state" placeholder="State"
                                           value="{{ old('state') }}">
                                    @error('state')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="zip_code" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                                           id="zip_code" name="zip_code" placeholder="12345"
                                           value="{{ old('zip_code') }}">
                                    @error('zip_code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glassmorphism-card mb-4">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="mdi mdi-cash"></i>
                            Financial Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="commission_rate" class="form-label">Commission Rate</label>
                                    <div class="commission-input-group">
                                        <input type="number" step="0.01" min="0" max="100"
                                               class="form-control @error('commission_rate') is-invalid @enderror"
                                               id="commission_rate" name="commission_rate"
                                               placeholder="10.00" value="{{ old('commission_rate') }}">
                                    </div>
                                    @error('commission_rate')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_terms" class="form-label">Payment Terms</label>
                                    <input type="text" class="form-control @error('payment_terms') is-invalid @enderror"
                                           id="payment_terms" name="payment_terms"
                                           placeholder="e.g., Net 30, Upon Delivery"
                                           value="{{ old('payment_terms') }}">
                                    @error('payment_terms')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_id" class="form-label">Tax ID / EIN</label>
                                    <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                           id="tax_id" name="tax_id" placeholder="XX-XXXXXXX"
                                           value="{{ old('tax_id') }}">
                                    @error('tax_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_account" class="form-label">Bank Account</label>
                                    <input type="text" class="form-control @error('bank_account') is-invalid @enderror"
                                           id="bank_account" name="bank_account"
                                           placeholder="Account number"
                                           value="{{ old('bank_account') }}">
                                    @error('bank_account')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glassmorphism-card mb-4">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="mdi mdi-note-text"></i>
                            Additional Notes
                        </h5>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="4"
                                              placeholder="Add any additional notes or comments...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mb-4">
                    <a href="{{ route('vendors.index') }}" class="btn-secondary-custom">
                        <i class="mdi mdi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="gold-gradient-btn">
                        <i class="mdi mdi-content-save me-2"></i>Create Vendor
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = '(' + value;
                } else if (value.length <= 6) {
                    value = '(' + value.slice(0, 3) + ') ' + value.slice(3);
                } else {
                    value = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            e.target.value = value;
        });
    </script>
@endsection
