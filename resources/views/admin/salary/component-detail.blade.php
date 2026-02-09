@extends('layouts.master')

@section('title', 'Salary Component Details')

@section('css')
<style>
    .detail-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }
    .detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }
    .detail-field {
        border-left: 4px solid #d4af37;
        padding-left: 1rem;
    }
    .detail-field-label {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    .detail-field-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
    }
    .breakdown-table {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    .breakdown-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .breakdown-row:last-child {
        border-bottom: none;
    }
    .breakdown-label {
        color: #6b7280;
        font-weight: 500;
    }
    .breakdown-value {
        font-weight: 600;
        color: #111827;
    }
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .status-calculated { background: #ffc107; color: black; }
    .status-approved { background: #17a2b8; color: white; }
    .status-paid { background: #28a745; color: white; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb & Back Button -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('salary.components') }}" class="btn btn-outline-secondary mb-3">
                <i class="bx bx-arrow-back me-2"></i>Back to Components
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="detail-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="mb-1">{{ $component->user->name }}</h1>
                <p class="mb-0 opacity-75">{{ $component->user->email }}</p>
            </div>
            <div class="text-end">
                <span class="status-badge status-{{ $component->status }}">
                    {{ ucfirst($component->status) }}
                </span>
                <p class="mb-0 mt-2 opacity-75">
                    <i class="bx bx-calendar me-1"></i>
                    {{ $component->month_name }} {{ $component->salary_year }}
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <!-- Component Type & Payment Date -->
            <div class="detail-card">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="detail-field">
                            <div class="detail-field-label">Component Type</div>
                            <div class="detail-field-value">
                                @if($component->component_type === 'basic')
                                    <i class="bx bx-money me-2" style="color: #0d6efd;"></i>Basic Salary
                                @else
                                    <i class="bx bx-gift me-2" style="color: #198754;"></i>Bonus Salary
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-field">
                            <div class="detail-field-label">Payment Date</div>
                            <div class="detail-field-value">{{ $component->payment_date->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Breakdown -->
            <div class="detail-card">
                <h5 class="mb-4" style="color: #d4af37;">
                    <i class="bx bx-calculator me-2"></i>Financial Summary
                </h5>

                @if($component->component_type === 'basic')
                    <!-- BASIC SALARY BREAKDOWN -->
                    <div class="breakdown-table">
                        <div class="breakdown-row">
                            <span class="breakdown-label">Basic Salary</span>
                            <span class="breakdown-value">Rs {{ number_format($component->basic_salary, 2) }}</span>
                        </div>
                        
                        @if($component->attendance_bonus > 0)
                        <div class="breakdown-row">
                            <span class="breakdown-label">Attendance/Punctuality Bonus</span>
                            <span class="breakdown-value" style="color: #10b981;">+ Rs {{ number_format($component->attendance_bonus, 2) }}</span>
                        </div>
                        @endif
                        
                        <hr class="my-3">
                        
                        <div class="breakdown-row" style="font-size: 1.1rem;">
                            <span class="breakdown-label" style="font-weight: 700;">Calculated Amount</span>
                            <span class="breakdown-value" style="color: #667eea; font-size: 1.25rem;">Rs {{ number_format($component->calculated_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Attendance Details -->
                    <h6 class="mt-4 mb-3" style="color: #6b7280;">Attendance Information</h6>
                    <div class="breakdown-table">
                        <div class="breakdown-row">
                            <span class="breakdown-label">Working Days</span>
                            <span class="breakdown-value">{{ $component->working_days ?? 22 }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Present Days</span>
                            <span class="breakdown-value">{{ $component->present_days }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Leave Days (Full)</span>
                            <span class="breakdown-value">{{ $component->leave_days }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Half Days</span>
                            <span class="breakdown-value">{{ $component->half_days ?? 0 }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Late Days</span>
                            <span class="breakdown-value">{{ $component->late_days ?? 0 }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Daily Salary Rate</span>
                            <span class="breakdown-value">Rs {{ number_format($component->daily_salary, 2) }}</span>
                        </div>
                        
                        @if($component->attendance_bonus > 0)
                        <div class="breakdown-row">
                            <span class="breakdown-label">Punctuality Bonus Earned</span>
                            <span class="breakdown-value" style="color: #10b981;">Rs {{ number_format($component->attendance_bonus, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($component->attendance_deduction < 0)
                        <div class="breakdown-row">
                            <span class="breakdown-label">Attendance Deduction</span>
                            <span class="breakdown-value" style="color: #ef4444;">Rs {{ number_format(abs($component->attendance_deduction), 2) }}</span>
                        </div>
                        @endif
                    </div>

                @else
                    <!-- BONUS SALARY BREAKDOWN -->
                    <div class="breakdown-table">
                        @if($component->actual_sales !== null)
                        <div class="breakdown-row">
                            <span class="breakdown-label">Target Sales</span>
                            <span class="breakdown-value">{{ $component->target_sales }} units</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Actual Sales</span>
                            <span class="breakdown-value">{{ $component->actual_sales }} units</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Chargebacks</span>
                            <span class="breakdown-value">{{ $component->chargeback_count }} units</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Net Approved Sales</span>
                            <span class="breakdown-value">{{ $component->net_approved_sales }} units</span>
                        </div>
                        
                        @if($component->net_approved_sales >= $component->target_sales)
                        <div class="breakdown-row">
                            <span class="breakdown-label">Extra Sales (Above Target)</span>
                            <span class="breakdown-value">{{ $component->extra_sales }} units</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Bonus Per Extra Sale</span>
                            <span class="breakdown-value">Rs {{ number_format($component->bonus_per_extra_sale, 2) }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Sales Bonus</span>
                            <span class="breakdown-value" style="color: #10b981;">Rs {{ number_format($component->calculated_amount, 2) }}</span>
                        </div>
                        @else
                        <div class="breakdown-row">
                            <span class="breakdown-label">Status</span>
                            <span class="breakdown-value" style="color: #ef4444;">Below Target - No Bonus</span>
                        </div>
                        @endif
                        
                        <hr class="my-3">
                        
                        <div class="breakdown-row" style="font-size: 1.1rem;">
                            <span class="breakdown-label" style="font-weight: 700;">Bonus Amount</span>
                            <span class="breakdown-value" style="color: #198754; font-size: 1.25rem;">Rs {{ number_format($component->calculated_amount, 2) }}</span>
                        </div>
                        @endif
                    </div>
                @endif

                <!-- Deductions -->
                @if($component->deductions > 0)
                <h6 class="mt-4 mb-3" style="color: #6b7280;">Deductions</h6>
                <div class="breakdown-table">
                    @if($component->dock_deductions > 0)
                    <div class="breakdown-row">
                        <span class="breakdown-label">Dock Deductions</span>
                        <span class="breakdown-value" style="color: #ef4444;">Rs {{ number_format($component->dock_deductions, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($component->manual_deductions > 0)
                    <div class="breakdown-row">
                        <span class="breakdown-label">Manual Deductions</span>
                        <span class="breakdown-value" style="color: #ef4444;">Rs {{ number_format($component->manual_deductions, 2) }}</span>
                    </div>
                    @endif
                    
                    <hr class="my-3">
                    
                    <div class="breakdown-row">
                        <span class="breakdown-label" style="font-weight: 700;">Total Deductions</span>
                        <span class="breakdown-value" style="color: #ef4444;">Rs {{ number_format($component->deductions, 2) }}</span>
                    </div>
                </div>
                @endif

                <!-- Final Amount -->
                <div class="detail-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; margin-top: 2rem;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-field-label" style="color: rgba(255,255,255,0.8);">Calculated Amount</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">Rs {{ number_format($component->calculated_amount, 2) }}</div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="detail-field-label" style="color: rgba(255,255,255,0.8);">Net Amount</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">Rs {{ number_format($component->net_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($component->notes)
            <div class="detail-card">
                <h6 class="mb-3" style="color: #6b7280;">
                    <i class="bx bx-note me-2"></i>Notes
                </h6>
                <p class="text-muted mb-0">{{ $component->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Approval Info -->
            <div class="detail-card">
                <h6 class="mb-3" style="color: #d4af37;">
                    <i class="bx bx-info-circle me-2"></i>Workflow Status
                </h6>
                
                <div class="breakdown-table">
                    <div class="breakdown-row">
                        <span class="breakdown-label">Current Status</span>
                        <span class="breakdown-value">
                            <span class="status-badge status-{{ $component->status }}">
                                {{ ucfirst($component->status) }}
                            </span>
                        </span>
                    </div>
                    
                    @if($component->calculated_at)
                    <div class="breakdown-row">
                        <span class="breakdown-label">Calculated On</span>
                        <span class="breakdown-value" style="font-size: 0.9rem;">{{ $component->calculated_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @endif
                    
                    @if($component->approved_at)
                    <div class="breakdown-row">
                        <span class="breakdown-label">Approved On</span>
                        <span class="breakdown-value" style="font-size: 0.9rem;">{{ $component->approved_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @endif
                    
                    @if($component->paid_at)
                    <div class="breakdown-row">
                        <span class="breakdown-label">Paid On</span>
                        <span class="breakdown-value" style="font-size: 0.9rem;">{{ $component->paid_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="detail-card">
                <h6 class="mb-3" style="color: #d4af37;">
                    <i class="bx bx-cog me-2"></i>Actions
                </h6>
                
                <div class="d-grid gap-2">
                    @if($component->status === 'calculated')
                        <form action="{{ route('salary.component.approve', $component->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Approve this salary component?')">
                                <i class="bx bx-check me-2"></i>Approve
                            </button>
                        </form>
                    @endif
                    
                    @if($component->status === 'approved')
                        <form action="{{ route('salary.component.mark-paid', $component->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100" onclick="return confirm('Mark this salary component as paid?')">
                                <i class="bx bx-money me-2"></i>Mark as Paid
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('salary.component.payslip', $component->id) }}" class="btn btn-warning btn-sm w-100">
                        <i class="bx bx-download me-2"></i>Download Payslip
                    </a>
                    
                    <a href="{{ route('salary.components') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bx bx-x me-2"></i>Close
                    </a>
                </div>
            </div>

            <!-- Timeline -->
            <div class="detail-card">
                <h6 class="mb-3" style="color: #d4af37;">
                    <i class="bx bx-time me-2"></i>Timeline
                </h6>
                
                <div class="timeline">
                    <div class="timeline-item" style="margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: flex-start;">
                            <div style="
                                width: 24px;
                                height: 24px;
                                background: #d4af37;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-weight: bold;
                                margin-right: 1rem;
                                flex-shrink: 0;
                            ">
                                ✓
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">Calculated</div>
                                <small class="text-muted">{{ $component->calculated_at ? $component->calculated_at->format('d M Y, h:i A') : 'Pending' }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item" style="margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: flex-start;">
                            <div style="
                                width: 24px;
                                height: 24px;
                                background: {{ $component->approved_at ? '#10b981' : '#d1d5db' }};
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-weight: bold;
                                margin-right: 1rem;
                                flex-shrink: 0;
                            ">
                                ✓
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">Approved</div>
                                <small class="text-muted">{{ $component->approved_at ? $component->approved_at->format('d M Y, h:i A') : 'Pending' }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div style="display: flex; align-items: flex-start;">
                            <div style="
                                width: 24px;
                                height: 24px;
                                background: {{ $component->paid_at ? '#0d6efd' : '#d1d5db' }};
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-weight: bold;
                                margin-right: 1rem;
                                flex-shrink: 0;
                            ">
                                ✓
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">Paid</div>
                                <small class="text-muted">{{ $component->paid_at ? $component->paid_at->format('d M Y, h:i A') : 'Pending' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
