@extends('layouts.master')

@section('title', 'Salary Components - Basic & Bonus Sheets')

@section('css')
<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .component-badge-basic {
        background: #0d6efd;
        color: white;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .component-badge-bonus {
        background: #198754;
        color: white;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .status-badge {
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .status-calculated { background: #ffc107; color: black; }
    .status-approved { background: #17a2b8; color: white; }
    .status-paid { background: #28a745; color: white; }
    .status-draft { background: #6c757d; color: white; }
    .payment-date {
        background: #e7f3ff;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .amount-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .amount-row .label {
        font-weight: 500;
        color: #6b7280;
    }
    .amount-row .value {
        font-weight: 600;
        color: #111827;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1" style="color: #d4af37;">
                        <i class="bx bx-wallet me-2"></i>
                        Salary Components (Basic & Bonus Sheets)
                    </h1>
                    <p class="text-muted">Two-payment salary structure: Basic on 10th, Bonus on 20th</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('salary.components') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select name="employee" class="form-select">
                        <option value="">-- All Employees --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @if(request('employee') == $emp->id) selected @endif>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">-- All Months --</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @if(request('month') == $i) selected @endif>{{ Carbon\Carbon::create()->month($i)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <option value="">-- All Years --</option>
                        @for($i = now()->year; $i >= 2020; $i--)
                            <option value="{{ $i }}" @if(request('year') == $i) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Component</label>
                    <select name="component_type" class="form-select">
                        <option value="">-- All Components --</option>
                        <option value="basic" @if(request('component_type') == 'basic') selected @endif>Basic Salary</option>
                        <option value="bonus" @if(request('component_type') == 'bonus') selected @endif>Bonus Salary</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- All Status --</option>
                        <option value="calculated" @if(request('status') == 'calculated') selected @endif>Calculated</option>
                        <option value="approved" @if(request('status') == 'approved') selected @endif>Approved</option>
                        <option value="paid" @if(request('status') == 'paid') selected @endif>Paid</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Components Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="bx bx-table me-2" style="color: #d4af37;"></i> Salary Components</h5>
        </div>
        <div class="card-body">
            @if($components->isEmpty())
                <div class="text-center py-5" style="opacity: 0.6;">
                    <i class="bx bx-inbox" style="font-size: 4rem; color: #d4af37;"></i>
                    <h5 class="mt-3 text-muted">No Salary Components Found</h5>
                    <p class="text-muted">Calculate salaries to generate basic and bonus salary sheets</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Type</th>
                                <th>Payment Date</th>
                                <th>Calculated</th>
                                <th>Approved</th>
                                <th>Net Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($components as $component)
                            <tr>
                                <td>
                                    <strong>{{ $component->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $component->user->email }}</small>
                                </td>
                                <td>
                                    <strong>{{ $component->month_name }}</strong><br>
                                    <small class="text-muted">{{ $component->salary_year }}</small>
                                </td>
                                <td>
                                    @if($component->component_type === 'basic')
                                        <span class="component-badge-basic">
                                            <i class="bx bx-money me-1"></i>Basic Salary
                                        </span>
                                    @else
                                        <span class="component-badge-bonus">
                                            <i class="bx bx-gift me-1"></i>Bonus Salary
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="payment-date">
                                        <i class="bx bx-calendar me-1"></i>{{ $component->payment_date->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <strong>Rs{{ number_format($component->calculated_amount, 2) }}</strong>
                                    @if($component->deductions > 0)
                                        <br><small class="text-danger">- Rs{{ number_format($component->deductions, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($component->approved_amount)
                                        <strong>Rs{{ number_format($component->approved_amount, 2) }}</strong>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: #10b981;">Rs{{ number_format($component->net_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $component->status }}">
                                        {{ ucfirst($component->status) }}
                                    </span>
                                    @if($component->paid_at)
                                        <br><small class="text-muted">{{ $component->paid_at->format('d M Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('salary.component.show', $component->id) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="bx bx-eye"></i>
                                        </a>
                                        
                                        @if($component->status === 'calculated')
                                            <form action="{{ route('salary.component.approve', $component->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this salary component?')">
                                                    <i class="bx bx-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($component->status === 'approved')
                                            <form action="{{ route('salary.component.mark-paid', $component->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" title="Mark as Paid" onclick="return confirm('Mark this salary component as paid?')">
                                                    <i class="bx bx-money"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('salary.component.payslip', $component->id) }}" class="btn btn-sm btn-warning" title="Download Payslip">
                                            <i class="bx bx-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bx bx-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="text-muted mt-2">No components match your filters</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-4">
                    {{ $components->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
@endsection
