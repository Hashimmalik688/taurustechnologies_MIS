@extends('layouts.master')

@section('title')
    Add Ledger Entry
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
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

        .form-control, .form-select, textarea {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem;
        }

        .form-control:focus, .form-select:focus, textarea:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: #d4af37;
            color: #cbd5e1;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
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

        .type-selector {
            display: flex;
            gap: 1rem;
        }

        .type-radio {
            flex: 1;
        }

        .type-radio input[type="radio"] {
            display: none;
        }

        .type-radio label {
            display: block;
            padding: 1rem;
            text-align: center;
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #cbd5e1;
            font-weight: 600;
        }

        .type-radio input[type="radio"]:checked + label {
            border-color: #d4af37;
            background: rgba(212, 175, 55, 0.1);
            color: #d4af37;
        }

        .type-radio label:hover {
            border-color: #d4af37;
            background: rgba(212, 175, 55, 0.05);
        }

        .currency-input-group {
            position: relative;
        }

        .currency-input-group::before {
            content: '$';
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #d4af37;
            font-weight: 600;
            pointer-events: none;
        }

        .currency-input-group .form-control {
            padding-left: 2rem;
        }

        .select2-container--default .select2-selection--single {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #cbd5e1;
            height: 45px;
            padding: 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #cbd5e1;
            line-height: 30px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Ledger
        @endslot
        @slot('title')
            Add Entry
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-book-plus"></i>
                Add Ledger Entry
            </h2>

            <form action="{{ route('ledger.store') }}" method="POST">
                @csrf

                <div class="glassmorphism-card mb-4">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="mdi mdi-information"></i>
                            Transaction Details
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vendor_id" class="form-label required">Vendor</label>
                                    <select class="form-select select2" id="vendor_id" name="vendor_id" required>
                                        <option value="">Select Vendor</option>
                                        <option value="1">John Smith - Smith Insurance Co.</option>
                                        <option value="2">Sarah Johnson - Johnson & Associates</option>
                                        <option value="3">Michael Brown - Brown Supplies Ltd.</option>
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lead_id" class="form-label">Related Lead (Optional)</label>
                                    <select class="form-select select2" id="lead_id" name="lead_id">
                                        <option value="">Select Lead (if applicable)</option>
                                        <option value="1">Lead #1 - John Doe</option>
                                        <option value="2">Lead #2 - Jane Smith</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_date" class="form-label required">Transaction Date</label>
                                    <input type="text" class="form-control" id="transaction_date" name="transaction_date"
                                           placeholder="Select date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                    @error('transaction_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Transaction Type</label>
                                    <div class="type-selector">
                                        <div class="type-radio">
                                            <input type="radio" id="type_debit" name="type" value="debit" required>
                                            <label for="type_debit">
                                                <i class="mdi mdi-minus-circle me-2" style="color: #ef4444;"></i>
                                                Debit
                                            </label>
                                        </div>
                                        <div class="type-radio">
                                            <input type="radio" id="type_credit" name="type" value="credit" checked required>
                                            <label for="type_credit">
                                                <i class="mdi mdi-plus-circle me-2" style="color: #10b981;"></i>
                                                Credit
                                            </label>
                                        </div>
                                    </div>
                                    @error('type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label required">Amount</label>
                                    <div class="currency-input-group">
                                        <input type="number" step="0.01" min="0" class="form-control"
                                               id="amount" name="amount" placeholder="0.00"
                                               value="{{ old('amount') }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label required">Category</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="commission">Commission</option>
                                        <option value="payment">Payment</option>
                                        <option value="refund">Refund</option>
                                        <option value="expense">Expense</option>
                                        <option value="bonus">Bonus</option>
                                        <option value="adjustment">Adjustment</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" id="reference_number" name="reference_number"
                                           placeholder="e.g., INV-001234, PAY-005678" value="{{ old('reference_number') }}">
                                    @error('reference_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"
                                              placeholder="Enter transaction description or notes...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mb-4">
                    <a href="{{ route('ledger.index') }}" class="btn-secondary-custom">
                        <i class="mdi mdi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="gold-gradient-btn">
                        <i class="mdi mdi-content-save me-2"></i>Create Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'default',
                width: '100%'
            });

            // Initialize date picker
            flatpickr("#transaction_date", {
                dateFormat: "Y-m-d",
                defaultDate: "today"
            });
        });
    </script>
@endsection
