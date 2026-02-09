@extends('layouts.master')

@section('title')
    Revenue Analytics
@endsection

@section('css')
<style>
    i[class*="mdi"] {
        color: inherit !important;
    }

    .text-success i[class*="mdi"],
    i[class*="mdi"].text-success {
        color: #198754 !important;
    }

    .text-warning i[class*="mdi"],
    i[class*="mdi"].text-warning {
        color: #ffc107 !important;
    }

    .text-danger i[class*="mdi"],
    i[class*="mdi"].text-danger {
        color: #dc3545 !important;
    }

    .text-secondary i[class*="mdi"],
    i[class*="mdi"].text-secondary {
        color: #6c757d !important;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Revenue Analytics
        @endslot
        @slot('title')
            Dashboard
        @endslot
    @endcomponent

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="mdi mdi-chart-line me-2"></i>Revenue Analytics
            </h2>
            <p class="text-muted">Complete revenue breakdown by verification status</p>
        </div>
    </div>

    <!-- Revenue Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <p class="text-muted small mb-1">Good Revenue</p>
                    <h4 class="text-success fw-bold mb-2">${{ number_format($good_revenue, 2) }}</h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $good_count }} sales</small>
                        <small class="text-success fw-semibold">{{ number_format($good_revenue_percentage, 1) }}%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $good_revenue_percentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <p class="text-muted small mb-1">Average Revenue</p>
                    <h4 class="text-warning fw-bold mb-2">${{ number_format($average_revenue, 2) }}</h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $average_count }} sales</small>
                        <small class="text-warning fw-semibold">{{ number_format($average_revenue_percentage, 1) }}%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $average_revenue_percentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-danger">
                <div class="card-body">
                    <p class="text-muted small mb-1">Bad Revenue</p>
                    <h4 class="text-danger fw-bold mb-2">${{ number_format($bad_revenue, 2) }}</h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $bad_count }} sales</small>
                        <small class="text-danger fw-semibold">{{ number_format($bad_revenue_percentage, 1) }}%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $bad_revenue_percentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <p class="text-muted small mb-1">Unverified Revenue</p>
                    <h4 class="text-info fw-bold mb-2">${{ number_format($unverified_revenue, 2) }}</h4>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $unverified_count }} sales</small>
                        <small class="text-info fw-semibold">{{ number_format($unverified_revenue_percentage, 1) }}%</small>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $unverified_revenue_percentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #d4af37 0%, #b8a000 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50">Total Issued Revenue</p>
                            <h2 class="fw-bold mb-0">${{ number_format($total_revenue, 2) }}</h2>
                            <p class="mb-0 text-white-50">{{ $total_count }} Total Sales</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-1">Average Per Sale</p>
                            <h4 class="fw-bold">${{ number_format($total_count > 0 ? $total_revenue / $total_count : 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ratios & Percentages -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-percent me-2"></i>Sales Ratio by Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Good Sales</span>
                            <span class="text-success fw-bold">{{ number_format($good_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $good_percentage }}%;" title="{{ $good_count }} sales">{{ $good_count }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Average Sales</span>
                            <span class="text-warning fw-bold">{{ number_format($average_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $average_percentage }}%;" title="{{ $average_count }} sales">{{ $average_count }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Bad Sales</span>
                            <span class="text-danger fw-bold">{{ number_format($bad_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $bad_percentage }}%;" title="{{ $bad_count }} sales">{{ $bad_count }}</div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Unverified</span>
                            <span class="text-secondary fw-bold">{{ number_format($unverified_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $unverified_percentage }}%;" title="{{ $unverified_count }} sales">{{ $unverified_count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-chart-pie me-2"></i>Revenue Share by Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Good Revenue</span>
                            <span class="text-success fw-bold">{{ number_format($good_revenue_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $good_revenue_percentage }}%;">${{ number_format($good_revenue, 0) }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Average Revenue</span>
                            <span class="text-warning fw-bold">{{ number_format($average_revenue_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $average_revenue_percentage }}%;">${{ number_format($average_revenue, 0) }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Bad Revenue</span>
                            <span class="text-danger fw-bold">{{ number_format($bad_revenue_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $bad_revenue_percentage }}%;">${{ number_format($bad_revenue, 0) }}</div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Unverified Revenue</span>
                            <span class="text-secondary fw-bold">{{ number_format($unverified_revenue_percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $unverified_revenue_percentage }}%;">${{ number_format($unverified_revenue, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Quality Ratio (Good Sales)</p>
                    <h3 class="text-success fw-bold mb-0">{{ number_format($good_percentage, 1) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Bad Sales Ratio</p>
                    <h3 class="text-danger fw-bold mb-0">{{ number_format($bad_percentage, 1) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Good Revenue Ratio</p>
                    <h3 class="text-success fw-bold mb-0">{{ number_format($good_revenue_percentage, 1) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-2">Bad Revenue Ratio</p>
                    <h3 class="text-danger fw-bold mb-0">{{ number_format($bad_revenue_percentage, 1) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifier Forms Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-check-circle-outline me-2"></i>Verifier Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Verifier Name</th>
                                    <th class="text-center">Total Submitted</th>
                                    <th class="text-center">Transferred</th>
                                    <th class="text-center">Transfer Rate</th>
                                    <th class="text-center">Pending Callbacks</th>
                                    <th class="text-center">Declined Calls</th>
                                    <th class="text-center">Marked as Sale</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($verifier_stats as $verifier)
                                    <tr>
                                        <td class="fw-semibold">{{ $verifier['name'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $verifier['total_submitted'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $verifier['transferred'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <div class="progress" style="width: 100px; height: 20px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $verifier['transfer_rate'] }}%;" title="{{ number_format($verifier['transfer_rate'], 1) }}%">
                                                        @if($verifier['transfer_rate'] > 20)
                                                            <small class="text-white fw-semibold">{{ number_format($verifier['transfer_rate'], 1) }}%</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $verifier['pending_callbacks'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $verifier['declined_calls'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $verifier['marked_as_sale'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No verifiers available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
