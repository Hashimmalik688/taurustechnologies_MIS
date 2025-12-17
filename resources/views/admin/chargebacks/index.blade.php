@extends('layouts.master')

@section('title')
    Chargebacks Management
@endsection

@section('css')
<style>
    .chargeback-card {
        transition: all 0.3s ease;
    }
    .chargeback-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .period-filter .btn {
        min-width: 120px;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Chargebacks
        @endslot
        @slot('title')
            Management
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card chargeback-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                    <i class="bx bx-error"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Chargebacks</p>
                            <h4 class="mb-0">{{ $total_count }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card chargeback-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                    <i class="bx bx-dollar-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Amount</p>
                            <h4 class="mb-0">${{ number_format($total_amount, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chargebacks Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Chargebacks List</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('chargebacks.index') }}" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Search by name, phone, carrier, closer..." 
                                       value="{{ $search }}">
                            </div>
                            <div class="col-md-2">
                                <select name="month" class="form-select form-select-sm">
                                    <option value="">All Months</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="year" class="form-select form-select-sm">
                                    <option value="">All Years</option>
                                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bx bx-search-alt me-1"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('chargebacks.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bx bx-reset me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sale Date</th>
                                    <th>Customer</th>
                                    <th>Closer</th>
                                    <th>Agent Assigned</th>
                                    <th>Carrier</th>
                                    <th>Chargeback Amount</th>
                                    <th>Comments</th>
                                    <th>Manager Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chargebacks as $lead)
                                    <tr>
                                        <td>{{ $lead->sale_date ? $lead->sale_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <strong>{{ $lead->cn_name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $lead->phone_number ?? '' }}</small>
                                        </td>
                                        <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                        <td>{{ $lead->managedBy->name ?? 'Unassigned' }}</td>
                                        <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-danger fs-6">
                                                ${{ number_format($lead->monthly_premium ?? 0, 2) }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($lead->comments ?? 'No reason provided', 50) }}</td>
                                        <td>
                                            <div class="p-2 bg-light rounded text-muted small">{{ $lead->manager_reason ?? 'No comments' }}</div>
                                        </td>
                                        <td>
                                            <a href="{{ route('leads.show', $lead->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="View Lead Details">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0">No chargebacks found for the selected period</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($chargebacks->count() > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total:</td>
                                        <td>
                                            <span class="badge bg-danger fs-6">
                                                ${{ number_format($total_amount, 2) }}
                                            </span>
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $chargebacks->appends(['search' => $search, 'month' => $month, 'year' => $year])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
