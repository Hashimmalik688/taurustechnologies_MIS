@extends('layouts.master')

@section('title')
    Add Chart of Account
@endsection

@section('css')
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

        .form-check-input:checked {
            background-color: #d4af37;
            border-color: #d4af37;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Chart of Accounts
        @endslot
        @slot('title')
            Add Account
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="bx bx-book-add"></i>
                Add Chart of Account
            </h2>

            <form action="{{ route('chart-of-accounts.store') }}" method="POST">
                @csrf

                <div class="glassmorphism-card mb-4">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="bx bx-info-circle"></i>
                            Account Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_code" class="form-label required">Account Code</label>
                                    <input type="text" class="form-control @error('account_code') is-invalid @enderror" 
                                           id="account_code" name="account_code" value="{{ old('account_code') }}" 
                                           placeholder="e.g., 1000, 2000, 3000" required>
                                    @error('account_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_name" class="form-label required">Account Name</label>
                                    <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                           id="account_name" name="account_name" value="{{ old('account_name') }}" 
                                           placeholder="e.g., Cash in Bank" required>
                                    @error('account_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_type" class="form-label required">Account Type</label>
                                    <select class="form-select @error('account_type') is-invalid @enderror" 
                                            id="account_type" name="account_type" required>
                                        <option value="">Select Account Type</option>
                                        @foreach($accountTypes as $type)
                                            <option value="{{ $type }}" {{ old('account_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_category" class="form-label">Account Category</label>
                                    <select class="form-select @error('account_category') is-invalid @enderror" 
                                            id="account_category" name="account_category">
                                        <option value="">Select Category (Optional)</option>
                                        @foreach($accountCategories as $category)
                                            <option value="{{ $category }}" {{ old('account_category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parent_account_id" class="form-label">Parent Account</label>
                                    <select class="form-select select2 @error('parent_account_id') is-invalid @enderror" 
                                            id="parent_account_id" name="parent_account_id">
                                        <option value="">None (Top Level Account)</option>
                                        @foreach($parentAccounts as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_account_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->account_code }} - {{ $parent->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="opening_balance" class="form-label">Opening Balance</label>
                                    <input type="number" step="0.01" class="form-control @error('opening_balance') is-invalid @enderror" 
                                           id="opening_balance" name="opening_balance" value="{{ old('opening_balance', 0) }}" 
                                           placeholder="0.00">
                                    @error('opening_balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Enter account description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active" style="color: #cbd5e1;">
                                        Account is Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="text-end">
                            <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary-custom me-2">
                                <i class="bx bx-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn gold-gradient-btn">
                                <i class="bx bx-save"></i> Save Account
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'default',
                width: '100%'
            });
        });
    </script>
@endsection
