@extends('layouts.master')

@section('title')
    My Dashboard
@endsection

@section('css')
    <link href="{{ URL::asset('css/light-theme.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* Modern card hover effects */
        .card {
            transition: all 0.3s ease;
        }
        
        .card.shadow-sm:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-2px);
        }

        /* Smooth counter animation */
        .counter-value {
            display: inline-block;
            transition: color 0.3s ease;
        }

        /* Custom badge styles */
        .badge.bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        /* Table row hover */
        .table-hover tbody tr {
            transition: background-color 0.2s ease;
        }

        /* Avatar gradient */
        .avatar-title.bg-gradient {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
        }

        /* Responsive gap adjustments */
        @media (max-width: 768px) {
            .gap-2 {
                gap: 0.5rem !important;
            }
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Ravens
        @endslot
        @slot('title')
            My Dashboard
        @endslot
    @endcomponent

    <!-- Quick Action Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('ravens.calling') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-phone me-1"></i> Start Calling
                </a>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-time me-1"></i> My Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3">
        <!-- Dialed -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Dialed Today</p>
                            <h3 class="mb-0 fw-bold">
                                <span class="counter-value" data-target="{{ $stats['dialed_today'] ?? 0 }}">{{ $stats['dialed_today'] ?? 0 }}</span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-primary bg-gradient rounded-3">
                                <i class="bx bx-phone fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calls Connected -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Connected</p>
                            <h3 class="mb-0 fw-bold">
                                <span class="counter-value" data-target="{{ $stats['calls_connected'] ?? 0 }}">{{ $stats['calls_connected'] ?? 0 }}</span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-info bg-gradient rounded-3">
                                <i class="bx bx-phone-call fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Today -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sales Today</p>
                            <h3 class="mb-0 fw-bold text-success">
                                <span class="counter-value" data-target="{{ $stats['sales_today'] ?? 0 }}">{{ $stats['sales_today'] ?? 0 }}</span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-success bg-gradient rounded-3">
                                <i class="bx bx-check-circle fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MTD Sale -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">MTD Sales</p>
                            <h3 class="mb-0 fw-bold" style="color: var(--gold, #d4af37);">
                                <span class="counter-value" data-target="{{ $stats['mtd_sales'] ?? 0 }}">{{ $stats['mtd_sales'] ?? 0 }}</span>
                            </h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title rounded-3" style="background: linear-gradient(135deg, #d4af37 0%, #f9d670 100%);">
                                <i class="bx bx-trophy fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Sales Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bx bx-dollar-circle text-success me-2"></i>My Sales Records
                        </h5>
                        <span class="badge bg-success-subtle text-success" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                            Total: {{ $mySales->total() ?? 0 }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    @if(isset($mySales) && $mySales->count() > 0)
                        <!-- Sales Summary Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <div class="card border border-success-subtle bg-success-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-success mb-1 fw-bold">{{ $mySales->where('status', 'accepted')->count() }}</h4>
                                        <p class="mb-0 text-success small">Accepted</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card border border-info-subtle bg-info-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-info mb-1 fw-bold">{{ $mySales->where('status', 'underwritten')->count() }}</h4>
                                        <p class="mb-0 text-info small">Underwritten</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card border border-warning-subtle bg-warning-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-warning mb-1 fw-bold">{{ $mySales->where('status', 'pending')->count() }}</h4>
                                        <p class="mb-0 text-warning small">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card border border-danger-subtle bg-danger-subtle mb-0">
                                    <div class="card-body text-center py-3">
                                        <h4 class="text-danger mb-1 fw-bold">{{ $mySales->where('status', 'declined')->count() }}</h4>
                                        <p class="mb-0 text-danger small">Declined</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="50">#</th>
                                        <th>Customer</th>
                                        <th>Sale Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Coverage</th>
                                        <th class="text-end">Premium</th>
                                        <th>Carrier</th>
                                        <th class="text-center" width="80">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mySales as $index => $sale)
                                        <tr>
                                            <td class="text-center text-muted">{{ $mySales->firstItem() + $index }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $sale->cn_name ?? 'N/A' }}</div>
                                                @if($sale->phone_number)
                                                    <small class="text-muted">{{ $sale->phone_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bx bx-calendar me-1"></i>
                                                    {{ $sale->sale_at ? $sale->sale_at->format('M d, Y') : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'accepted' => 'success',
                                                        'underwritten' => 'info',
                                                        'pending' => 'warning',
                                                        'declined' => 'danger',
                                                        'chargeback' => 'dark',
                                                    ];
                                                    $color = $statusColors[$sale->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst($sale->status) }}</span>
                                                @if($sale->qa_status)
                                                    <br><small class="text-muted">QA: {{ $sale->qa_status }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($sale->coverage_amount)
                                                    <span class="fw-semibold">${{ number_format($sale->coverage_amount, 0) }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($sale->monthly_premium)
                                                    <span class="fw-semibold">${{ number_format($sale->monthly_premium, 2) }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $sale->carrier_name ?? 'N/A' }}</small></td>
                                            <td class="text-center">
                                                <a href="{{ route('sales.index') }}?search={{ $sale->phone_number }}" 
                                                   class="btn btn-sm btn-light" 
                                                   title="View in Sales">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="text-muted small">
                                Showing {{ $mySales->firstItem() }} to {{ $mySales->lastItem() }} of {{ $mySales->total() }}
                            </div>
                            <div>{{ $mySales->links() }}</div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-xl mx-auto mb-3">
                                <div class="avatar-title bg-light rounded-circle">
                                    <i class="bx bx-package display-4 text-muted"></i>
                                </div>
                            </div>
                            <h5 class="text-muted">No sales yet</h5>
                            <p class="text-muted mb-3">Start calling leads to make your first sale!</p>
                            <a href="{{ route('ravens.calling') }}" class="btn btn-primary">
                                <i class="bx bx-phone-call me-1"></i> Start Calling
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Counter animation
        document.querySelectorAll('.counter-value').forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const updateCounter = () => {
                const current = +counter.innerText;
                const increment = target / 50;

                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.innerText = target;
                }
            };
            updateCounter();
        });
    </script>
@endsection
