@extends('layouts.master')

@section('title')
    View Account - {{ $account->account_name }}
@endsection

@section('css')
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .detail-label {
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: #cbd5e1;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .balance-display {
            font-size: 2rem;
            font-weight: 700;
            color: #d4af37;
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

        .gold-gradient-btn {
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            border: none;
            color: #0f172a;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
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
            padding: 0.75rem 1.5rem;
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

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
        }

        .status-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Chart of Accounts
        @endslot
        @slot('title')
            View Account
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="bx bx-book-bookmark"></i>
                {{ $account->account_code }} - {{ $account->account_name }}
            </h2>

            <div class="glassmorphism-card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="section-header mb-0">
                            <i class="bx bx-info-circle"></i>
                            Account Details
                        </h5>
                        <div>
                            <a href="{{ route('chart-of-accounts.edit', $account->id) }}" class="btn gold-gradient-btn me-2">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                            <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary-custom">
                                <i class="bx bx-arrow-back"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Account Code</div>
                            <div class="detail-value">{{ $account->account_code }}</div>

                            <div class="detail-label">Account Name</div>
                            <div class="detail-value">{{ $account->account_name }}</div>

                            <div class="detail-label">Account Type</div>
                            <div class="detail-value">{{ $account->account_type }}</div>

                            <div class="detail-label">Account Category</div>
                            <div class="detail-value">{{ $account->account_category ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-label">Parent Account</div>
                            <div class="detail-value">
                                @if($account->parentAccount)
                                    <a href="{{ route('chart-of-accounts.show', $account->parentAccount->id) }}" style="color: #d4af37;">
                                        {{ $account->parentAccount->account_code }} - {{ $account->parentAccount->account_name }}
                                    </a>
                                @else
                                    Top Level Account
                                @endif
                            </div>

                            <div class="detail-label">Opening Balance</div>
                            <div class="detail-value">${{ number_format($account->opening_balance, 2) }}</div>

                            <div class="detail-label">Current Balance</div>
                            <div class="detail-value">
                                <span class="balance-display">${{ number_format($account->current_balance, 2) }}</span>
                            </div>

                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge {{ $account->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        @if($account->description)
                            <div class="col-md-12">
                                <div class="detail-label">Description</div>
                                <div class="detail-value">{{ $account->description }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($account->childAccounts->count() > 0)
                <div class="glassmorphism-card">
                    <div class="card-body">
                        <h5 class="section-header">
                            <i class="bx bx-folder-open"></i>
                            Sub-Accounts ({{ $account->childAccounts->count() }})
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-dark-custom table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th>Type</th>
                                        <th>Current Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($account->childAccounts as $child)
                                        <tr>
                                            <td>{{ $child->account_code }}</td>
                                            <td>{{ $child->account_name }}</td>
                                            <td>{{ $child->account_type }}</td>
                                            <td>${{ number_format($child->current_balance, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $child->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $child->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('chart-of-accounts.show', $child->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
