@extends('layouts.master')

@section('title', 'Payroll')

@section('css')
<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .payroll-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #667eea;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .payroll-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
    .payroll-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
        color: #1f2937;
    }
    .payroll-card .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .payroll-card small {
        color: #9ca3af;
        font-size: 0.875rem;
    }
    .payroll-card.card-primary {
        border-left-color: #667eea;
    }
    .payroll-card.card-primary .stat-value {
        color: #667eea;
    }
    .payroll-card.card-success {
        border-left-color: #10b981;
    }
    .payroll-card.card-success .stat-value {
        color: #10b981;
    }
    .payroll-card.card-info {
        border-left-color: #3b82f6;
    }
    .payroll-card.card-info .stat-value {
        color: #3b82f6;
    }
    .payroll-card.card-warning {
        border-left-color: #f59e0b;
    }
    .payroll-card.card-warning .stat-value {
        color: #f59e0b;
    }
    .table-wrapper {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }
    .table-header {
        background: #f8f9fa;
        padding: 1.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .payroll-table {
        font-size: 0.95rem;
    }
    .payroll-table thead th {
        background: #f8f9fa;
        color: #333;
        font-weight: 600;
        border-bottom: 2px solid #d4af37;
        padding: 12px 8px;
        text-align: center;
        white-space: nowrap;
    }
    .payroll-table tbody td {
        padding: 12px 8px;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
    }
    .payroll-table tbody td:first-child {
        text-align: left;
        font-weight: 500;
    }
    .payroll-table tbody tr:hover {
        background: #f9fafb;
    }
    .amount {
        font-weight: 600;
        color: #667eea;
    }
    .badge-qualified {
        background: #10b981;
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-not-qualified {
        background: #ef4444;
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .filter-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    /* Additional utility classes */
    .text-gold {
        color: #d4af37 !important;
    }
    .payroll-info-alert {
        background: #f0f9ff;
        color: #1e40af;
    }
    .payroll-code {
        background: rgba(59, 130, 246, 0.1);
        padding: 2px 6px;
        border-radius: 3px;
        color: #1e40af;
    }
    .payroll-working-days-input {
        font-size: 1.5rem;
        color: #d4af37;
    }
    .payroll-breakdown-alert {
        background: #f8f9fa;
        border-color: #6c757d !important;
    }
    .payroll-table-footer {
        background: #f8f9fa;
    }
    .alert-divider {
        border-color: rgba(59, 130, 246, 0.2);
    }

    /* =================== DARK THEME FIXES =================== */
    [data-theme="dark"] .payroll-card {
        background: var(--bg-card, #1f1f1f) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5) !important;
    }
    [data-theme="dark"] .payroll-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.7) !important;
    }
    [data-theme="dark"] .payroll-card .stat-value {
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .payroll-card .stat-label {
        color: var(--text-secondary, #b0b0b0) !important;
    }
    [data-theme="dark"] .payroll-card small {
        color: var(--text-muted, #737373) !important;
    }
    [data-theme="dark"] .table-wrapper {
        background: var(--bg-card, #1f1f1f) !important;
    }
    [data-theme="dark"] .table-header {
        background: var(--bg-tertiary, #2d2d2d) !important;
        border-bottom-color: var(--border-color, #333333) !important;
    }
    [data-theme="dark"] .payroll-table thead th {
        background: var(--bg-tertiary, #2d2d2d) !important;
        color: var(--text-primary, #e5e5e5) !important;
        border-bottom-color: #d4af37 !important;
    }
    [data-theme="dark"] .payroll-table tbody td {
        border-bottom-color: var(--border-color, #333333) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .payroll-table tbody tr {
        background: var(--bg-card, #1f1f1f) !important;
    }
    [data-theme="dark"] .payroll-table tbody tr:hover {
        background: var(--bg-tertiary, #2d2d2d) !important;
    }
    [data-theme="dark"] .payroll-table tfoot {
        background: var(--bg-tertiary, #2d2d2d) !important;
    }
    [data-theme="dark"] .filter-section {
        background: var(--bg-card, #1f1f1f) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5) !important;
    }
    [data-theme="dark"] .card.border-0 {
        background: var(--bg-card, #1f1f1f) !important;
    }
    [data-theme="dark"] .card-body {
        background: var(--bg-card, #1f1f1f) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .card-header {
        background: var(--bg-tertiary, #2d2d2d) !important;
        color: var(--text-primary, #e5e5e5) !important;
        border-bottom-color: var(--border-color, #333333) !important;
    }
    [data-theme="dark"] .alert {
        background: var(--bg-tertiary, #2d2d2d) !important;
        color: var(--text-primary, #e5e5e5) !important;
        border-color: var(--border-color, #333333) !important;
    }
    [data-theme="dark"] .alert strong,
    [data-theme="dark"] .alert p,
    [data-theme="dark"] .alert h5 {
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .alert code {
        background: rgba(59, 130, 246, 0.2) !important;
        color: #60a5fa !important;
    }
    [data-theme="dark"] .modal-content {
        background: var(--bg-card, #1f1f1f) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .modal-header {
        background: var(--bg-tertiary, #2d2d2d) !important;
        border-bottom-color: var(--border-color, #333333) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .modal-body {
        background: var(--bg-card, #1f1f1f) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .modal-footer {
        background: var(--bg-tertiary, #2d2d2d) !important;
        border-top-color: var(--border-color, #333333) !important;
    }
    [data-theme="dark"] .modal-title {
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .form-label {
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .text-muted,
    [data-theme="dark"] small.text-muted {
        color: var(--text-muted, #737373) !important;
    }
    [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3,
    [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6 {
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] p {
        color: var(--text-secondary, #b0b0b0) !important;
    }
    [data-theme="dark"] label {
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .payroll-info-alert {
        background: var(--bg-tertiary, #2d2d2d) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .payroll-code {
        background: rgba(59, 130, 246, 0.2) !important;
        color: #60a5fa !important;
    }
    [data-theme="dark"] .payroll-working-days-input {
        background: var(--bg-tertiary, #2d2d2d) !important;
        color: #d4af37 !important;
        border-color: var(--border-color, #333333) !important;
    }
    [data-theme="dark"] .payroll-breakdown-alert {
        background: var(--bg-tertiary, #2d2d2d) !important;
        border-color: var(--border-color, #333333) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .payroll-table-footer {
        background: var(--bg-tertiary, #2d2d2d) !important;
        color: var(--text-primary, #e5e5e5) !important;
    }
    [data-theme="dark"] .alert-divider {
        border-color: var(--border-color, #333333) !important;
    }
    [data-theme="dark"] .text-gold {
        color: #d4af37 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1" style="color: #d4af37;">
                <i class="bx bx-receipt me-2"></i>Payroll
            </h1>
            <p class="text-muted">Monthly salary overview for all employees</p>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error me-2"></i>
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="payroll-card card-primary">
            <div class="stat-label">Total Employees</div>
            <div class="stat-value">{{ $employees->count() + $manualEntries->count() }}</div>
            <small>Active: {{ $employees->where('status', 'Active')->count() }} | Manual: {{ $manualEntries->count() }}</small>
        </div>
        <div class="payroll-card card-warning">
            <div class="stat-label">Total Basic Salary</div>
            <div class="stat-value">Rs {{ number_format($totalBasicSalary, 0) }}</div>
            <small>Monthly payroll</small>
        </div>
        <div class="payroll-card card-success">
            <div class="stat-label">Qualified for Bonus</div>
            <div class="stat-value">{{ $qualifiedForBonus }}</div>
            <small>Perfect attendance</small>
        </div>
        <div class="payroll-card card-info">
            <div class="stat-label">Total Bonus</div>
            <div class="stat-value">Rs {{ number_format($totalBonus, 0) }}</div>
            <small>Bonuses payable</small>
        </div>
    </div>

    <!-- Important Info Alert -->
    <div class="alert border-0 shadow-sm payroll-info-alert" style="border-left: 4px solid #3b82f6 !important;">
        <div class="d-flex align-items-start">
            <i class="bx bx-info-circle" style="font-size: 2rem; margin-right: 1rem; color: #3b82f6;"></i>
            <div>
                <h5 class="alert-heading mb-2"><strong>📅 Payroll Period: {{ $periodDisplay }}</strong></h5>
                <p class="mb-2"><strong>Current Selection:</strong> {{ Carbon\Carbon::create()->month((int) request('month', now()->month))->format('F') }} {{ request('year', now()->year) }} payroll covers <strong>{{ $startDate->format('M d, Y') }}</strong> to <strong>{{ $endDate->format('M d, Y') }}</strong></p>
                <p class="mb-2"><strong>💰 Payment Date:</strong> Salary paid 15 days after period ends (approx. {{ $endDate->copy()->addDays(15)->format('M d, Y') }})</p>
                <hr style="margin: 0.75rem 0;" class="alert-divider">
                <p class="mb-2"><strong>⚡ Real-time Calculation:</strong> All figures (attendance, sales, docks) are calculated <u>live</u> for the date range above.</p>
                <p class="mb-2"><strong>📊 Why figures change:</strong> Data updates daily until payroll is finalized and saved.</p>
                <p class="mb-0"><strong>💾 Data Locking:</strong> When you save payroll, it creates a <code class="payroll-code">salary_records</code> entry. Next month's changes won't affect saved records.</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('payroll.index') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Month</label>
                <select name="month" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" @if($month == $i) selected @endif>
                            {{ Carbon\Carbon::create()->month((int) $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Year</label>
                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" @if($year == $y) selected @endif>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bx bx-filter me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Working Days Setting (Applies to All Employees) -->
    <div class="card border-0 shadow-sm mb-3 payroll-working-days-card" style="border-left: 4px solid #d4af37 !important;">
        <div class="card-body">
            <form method="POST" action="{{ route('payroll.working-days.update') }}" class="row align-items-end g-3">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <div class="col-md-8">
                    <h6 class="mb-2 text-gold">
                        <i class="bx bx-calendar-check me-2"></i>Total Working Days for {{ Carbon\Carbon::create()->month((int) $month)->format('F') }} {{ $year }}
                    </h6>
                    <p class="text-muted small mb-0">This setting applies to ALL employees for punctuality calculation. Set once for the entire month.</p>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Working Days</label>
                    <input type="number" name="working_days" class="form-control form-control-lg text-center fw-bold payroll-working-days-input" 
                           value="{{ $totalWorkingDays }}" min="1" max="31" required @if(!auth()->user()->hasAnyRole(['CEO', 'Super Admin', 'Co-ordinator'])) readonly @endif>
                </div>
                @if(auth()->user()->hasAnyRole(['CEO', 'Super Admin', 'Co-ordinator']))
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bx bx-save me-1"></i>Update
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-table me-2 text-gold"></i>Payroll Details</h5>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted me-2">Date Range: {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}</small>
                    <input type="text" id="payrollSearch" class="form-control form-control-sm" placeholder="Search employees..." style="width: 200px;">
                    @if(auth()->user()->hasAnyRole(['CEO', 'Super Admin', 'Co-ordinator']))
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addManualEntryModal">
                        <i class="bx bx-plus me-1"></i>Add Manual Entry
                    </button>
                    @endif
                    <a href="{{ route('payroll.print', ['month' => request('month', now()->month), 'year' => request('year', now()->year)]) }}" class="btn btn-sm btn-success" target="_blank">
                        <i class="bx bx-printer me-1"></i>Print
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover payroll-table mb-0">
                    <thead>
                        <tr>
                            <th>Sr #</th>
                            <th>Employee Name</th>
                            <th>Join Date</th>
                            <th>Basic Salary</th>
                            <th>Per Day Wage</th>
                            <th>Punctuality (P)</th>
                            <th>Total</th>
                            <th>Full Days</th>
                            <th>Half Days</th>
                            <th>Late Count</th>
                            <th>Qualified</th>
                            <th>Dock Amount</th>
                            <th>Other Deductions</th>
                            <th>Net Salary</th>
                            <th>Advance</th>
                            <th>Payable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Initialize totals
                            $totalPunctuality = 0;
                            $totalGross = 0;
                            $totalDockAmount = 0;
                            $totalOtherDeductions = 0;
                            $totalNetSalary = 0;
                            $totalAdvance = 0;
                            $totalPayable = 0;
                        @endphp
                        @forelse($employees as $index => $employee)
                        @php
                            // Keep basic salary unchanged for display
                            $basicSalary = $employee->basic_salary ?? 0;
                            
                            // Get join date
                            $joinDate = $employee->userDetail && $employee->userDetail->join_date ? \Carbon\Carbon::parse($employee->userDetail->join_date)->format('d M Y') : 'N/A';
                            $joiningDate = $employee->userDetail && $employee->userDetail->join_date ? \Carbon\Carbon::parse($employee->userDetail->join_date) : null;
                            
                            // Use total working days passed from controller (respects manual override if set)
                            $workingDaysInPeriod = $totalWorkingDays;
                            
                            // Calculate per-day wage
                            $perDayWage = $basicSalary / max($workingDaysInPeriod, 1);
                            
                            // Get eligible days from manual fields (full_days + half_days, both count as 1.0)
                            $fullDays = $employee->full_days ?? 0;
                            $halfDays = $employee->half_days ?? 0;
                            $lateDays = $employee->late_days ?? 0;
                            $eligibleDays = $fullDays + $halfDays;
                            
                            // Handle join date: cap eligible days or set to 0 if joined after period
                            if ($joiningDate) {
                                // If joined after the payroll period ends, eligible days = 0
                                if ($joiningDate->gt($endDate)) {
                                    $eligibleDays = 0;
                                }
                                // If joined within the period, cap at working days from join date
                                elseif ($joiningDate->between($startDate, $endDate)) {
                                    $maxAllowedDays = 0;
                                    $current = $joiningDate->copy();
                                    while ($current->lte($endDate)) {
                                        if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                                            $maxAllowedDays++;
                                        }
                                        $current->addDay();
                                    }
                                    $eligibleDays = min($eligibleDays, $maxAllowedDays);
                                }
                            }
                            
                            // Calculate earned salary based on eligible days worked
                            $earnedSalary = $eligibleDays * $perDayWage;
                            
                            // Punctuality Auto-Detection Logic
                            // 2+ half days = automatic disqualification (2 half days = 1 absent)
                            // 4+ late arrivals = automatic disqualification
                            // 1 half day is acceptable if full days >= (total working days - 1)
                            $isQualified = true;
                            
                            if ($halfDays >= 2) {
                                $isQualified = false;
                            } elseif ($lateDays >= 4) {
                                $isQualified = false;
                            } else {
                                // Check if full days requirement is met
                                if ($halfDays == 1) {
                                    // 1 half day: need at least (total working days - 1) full days
                                    $requiredFullDays = $workingDaysInPeriod - 1;
                                    $isQualified = ($fullDays >= $requiredFullDays);
                                } elseif ($halfDays == 0) {
                                    // No half days: need at least (total working days) full days
                                    $requiredFullDays = $workingDaysInPeriod;
                                    $isQualified = ($fullDays >= $requiredFullDays);
                                }
                            }
                            
                            // Apply punctuality bonus based on qualification
                            $punctualityBonus = 0;
                            if ($isQualified && $employee->punctuality_bonus && $employee->punctuality_bonus > 0) {
                                $punctualityBonus = $employee->punctuality_bonus;
                            }
                            
                            // Check for override punctuality bonus (overrides auto-calculation)
                            if ($employee->override_punctuality_bonus && $employee->override_punctuality_bonus > 0) {
                                $punctualityBonus = $employee->override_punctuality_bonus;
                                $isQualified = true; // Override makes them qualified
                            }
                            
                            // Calculate total
                            $total = $earnedSalary + $punctualityBonus;
                            
                            // Calculate bonus (if sales employee and target exceeded)
                            $bonus = 0;
                            if ($employee->is_sales_employee) {
                                // Count sales this month
                                $actualSales = \App\Models\Lead::where(function($q) use ($employee) {
                                    $q->where('managed_by', $employee->id)
                                      ->orWhere('closer_name', $employee->name);
                                })
                                ->where('status', 'accepted')
                                ->whereBetween('sale_date', [$startDate, $endDate])
                                ->count();
                                
                                $target = $employee->target_sales ?? 20;
                                $bonusPerSale = $employee->bonus_per_extra_sale ?? 0;
                                
                                if ($actualSales > $target) {
                                    $bonus = ($actualSales - $target) * $bonusPerSale;
                                }
                            }
                            
                            // Additional allowances
                            $otherAllowances = $employee->other_allowances ?? 0;
                            
                            // Get dock amount for payroll period (26th to 25th date range - INCLUSIVE of both dates)
                            // Example for Jan 2026: Dec 26, 2025 to Jan 25, 2026
                            $dockAmount = \App\Models\DockRecord::where('user_id', $employee->id)
                                ->whereDate('dock_date', '>=', $startDate->format('Y-m-d'))
                                ->whereDate('dock_date', '<=', $endDate->format('Y-m-d'))
                                ->where('status', 'active')
                                ->sum('amount');
                            
                            $grossSalary = $total + $bonus + $otherAllowances;
                            $taxDeduction = $employee->tax_deduction ?? 0;
                            $otherDeductions = $employee->other_deductions ?? 0;
                            $totalDeductions = $taxDeduction + $otherDeductions + $dockAmount;
                            $netSalary = $grossSalary - $totalDeductions;
                            $advance = $employee->salary_advance ?? 0;
                            $payable = $netSalary - $advance;
                            
                            // Accumulate totals
                            $totalPunctuality += $punctualityBonus;
                            $totalGross += $total;
                            $totalDockAmount += $dockAmount;
                            $totalOtherDeductions += ($taxDeduction + $otherDeductions);
                            $totalNetSalary += $netSalary;
                            $totalAdvance += $advance;
                            $totalPayable += $payable;
                        @endphp
                        <tr>
                            <td><strong>{{ $index + 1 }}</strong></td>
                            <td><strong>{{ $employee->name }}</strong></td>
                            <td>{{ $joinDate }}</td>
                            <td><span class="amount">{{ number_format($basicSalary, 2) }}</span></td>
                            <td><span class="amount text-muted" style="font-size: 0.85rem;">{{ number_format($perDayWage, 2) }}</span></td>
                            <td><span class="amount">{{ number_format($punctualityBonus, 2) }}</span></td>
                            <td><span class="amount">{{ number_format($total, 2) }}</span></td>
                            <td><span class="badge bg-success" style="font-size: 0.9rem;">{{ $fullDays }}</span></td>
                            <td><span class="badge bg-warning text-dark" style="font-size: 0.9rem;">{{ $halfDays }}</span></td>
                            <td><span class="badge bg-info" style="font-size: 0.9rem;">{{ $lateDays }}</span></td>
                            <td>
                                <span class="badge" style="background-color: {{ $isQualified ? '#10b981' : '#ef4444' }};">
                                    {{ $isQualified ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td><span class="amount" style="color: #ef4444;">{{ number_format($dockAmount, 2) }}</span></td>
                            <td><span class="amount" style="color: #ef4444;">{{ number_format($taxDeduction + $otherDeductions, 2) }}</span></td>
                            <td><span class="amount" style="color: #667eea;">{{ number_format($netSalary, 2) }}</span></td>
                            <td><span class="amount" style="color: #ff6b6b;">{{ number_format($advance, 2) }}</span></td>
                            <td>
                                <strong style="color: #10b981; font-size: 1.1rem;">
                                    {{ number_format($payable, 2) }}
                                </strong>
                            </td>
                            <td>
                                @if(auth()->user()->hasAnyRole(['CEO', 'Super Admin', 'Co-ordinator']))
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPayrollModal{{ $employee->id }}" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Advanced Edit Modal -->
                        <div class="modal fade" id="editPayrollModal{{ $employee->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('payroll.update', $employee->id) }}">
                                        @csrf
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Payroll - {{ $employee->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                            <div class="row">
                                                <!-- Left Column -->
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-dollar-circle me-2"></i>Salary Information</h6>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Basic Salary (Rs)</label>
                                                        <input type="number" name="basic_salary" step="0.01" class="form-control form-control-lg" value="{{ $basicSalary }}" required>
                                                        <small class="text-muted">Employee's base monthly salary</small>
                                                    </div>

                                                    <h6 class="fw-bold text-primary mb-3 mt-4"><i class="bx bx-calendar-check me-2"></i>Attendance</h6>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Punctuality Bonus (Rs)</label>
                                                        <input type="number" name="punctuality_bonus" step="0.01" class="form-control" value="{{ $employee->punctuality_bonus ?? 0 }}">
                                                        <small class="text-muted">Base punctuality bonus amount (only if qualified)</small>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Full Days</label>
                                                            <input type="number" name="full_days" class="form-control form-control-lg" 
                                                                   value="{{ $fullDays }}" min="0" max="31" required>
                                                            <small class="text-muted">Number of complete working days</small>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Half Days</label>
                                                            <input type="number" name="half_days" class="form-control form-control-lg" 
                                                                   value="{{ $halfDays }}" min="0" max="31" required>
                                                            <small class="text-muted">2 half days = 1 absent</small>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Late Count</label>
                                                            <input type="number" name="late_days" class="form-control form-control-lg" 
                                                                   value="{{ $lateDays }}" min="0" max="31" required>
                                                            <small class="text-muted">Late arrivals from full days</small>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="current_month" value="{{ $month }}">
                                                    <input type="hidden" name="current_year" value="{{ $year }}">

                                                    <div class="alert alert-info">
                                                        <strong>📊 Punctuality Auto-Detection:</strong>
                                                        <ul class="mb-0 small">
                                                            <li>Total working days: <strong>{{ $totalWorkingDays }} days</strong></li>
                                                            <li>2+ half days = automatic disqualification (2 half days = 1 absent)</li>
                                                            <li><strong>4+ late arrivals (from full days) = automatic disqualification</strong></li>
                                                            <li>1 half day is acceptable if you have at least {{ $totalWorkingDays - 1 }} full days</li>
                                                            <li>Example: {{ $totalWorkingDays }} full + 0 half + 0-3 late = ✅ YES punctuality</li>
                                                            <li>Example: {{ $totalWorkingDays - 1 }} full + 1 half + 0-3 late = ✅ YES punctuality</li>
                                                            <li>Example: {{ $totalWorkingDays - 2 }} full + 2 half = ❌ NO punctuality</li>
                                                            <li>Example: Any days + 4+ late = ❌ NO punctuality</li>
                                                        </ul>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Override Punctuality Bonus (Rs)</label>
                                                        <input type="number" name="override_punctuality_bonus" step="0.01" class="form-control" value="{{ $employee->override_punctuality_bonus ?? 0 }}" placeholder="Leave blank to use default">
                                                        <small class="text-muted">Leave blank to use default Rs {{ number_format($punctualityBonus, 2) }}</small>
                                                    </div>
                                                </div>

                                                <!-- Right Column -->
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-money me-2"></i>Deductions & Allowances</h6>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Tax Deduction (Rs)</label>
                                                        <input type="number" name="tax_deduction" step="0.01" class="form-control form-control-md" value="{{ $employee->tax_deduction ?? 0 }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Other Deductions (Rs)</label>
                                                        <input type="number" name="other_deductions" step="0.01" class="form-control" value="{{ $employee->other_deductions ?? 0 }}" placeholder="Additional deductions if any">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Other Allowances (Rs)</label>
                                                        <input type="number" name="other_allowances" step="0.01" class="form-control" value="{{ $employee->other_allowances ?? 0 }}" placeholder="Bonuses, incentives, etc.">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Salary Advance (Rs)</label>
                                                        <input type="number" name="salary_advance" step="0.01" class="form-control form-control-md" value="{{ $employee->salary_advance ?? 0 }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Calculation Summary -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-calculator me-2"></i>Salary Breakdown</h6>
                                                    <div class="alert border payroll-breakdown-alert">
                                                        <div class="row text-sm">
                                                            <div class="col-md-6">
                                                                <small><strong>Basic Salary:</strong> Rs {{ number_format($basicSalary, 2) }}</small><br>
                                                                <small><strong>Punctuality Bonus:</strong> Rs {{ number_format($punctualityBonus, 2) }}</small><br>
                                                                <small><strong>Sales Bonus:</strong> Rs {{ number_format($bonus, 2) }}</small><br>
                                                                <small style="color: #10b981;"><strong>Other Allowances:</strong> Rs <span id="otherAllow{{ $employee->id }}">{{ number_format($otherAllowances, 2) }}</span></small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <small style="color: #667eea;"><strong>Gross Salary:</strong> Rs {{ number_format($grossSalary, 2) }}</small><br>
                                                                <small style="color: #ef4444;"><strong>Total Deductions:</strong> Rs <span id="totalDed{{ $employee->id }}">{{ number_format($totalDeductions, 2) }}</span></small><br>
                                                                <small style="color: #ff6b6b;"><strong>Advance:</strong> Rs <span id="advanceAmt{{ $employee->id }}">{{ number_format($advance, 2) }}</span></small><br>
                                                                <small style="color: #10b981; font-weight: bold; font-size: 1.1rem;"><strong>Final Payable:</strong> Rs <span id="finalPayable{{ $employee->id }}">{{ number_format($payable, 2) }}</span></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Notes -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-note me-2"></i>Payroll Notes</h6>
                                                    <textarea name="payroll_notes" class="form-control" rows="3" maxlength="500" placeholder="Add any notes for payroll processing...">{{ $employee->payroll_notes ?? '' }}</textarea>
                                                    <small class="text-muted">Max 500 characters</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save me-1"></i>Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bx bx-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-3">No employees found</p>
                            </td>
                        </tr>
                        @endforelse

                        {{-- Manual Payroll Entries (for non-system users) --}}
                        @if($manualEntries->isNotEmpty())
                        @foreach($manualEntries as $index => $entry)
                        @php
                            $basicSalary = $entry->basic_salary ?? 0;
                            $joinDate = $entry->join_date ? \Carbon\Carbon::parse($entry->join_date)->format('d M Y') : 'N/A';
                            $perDayWage = $basicSalary / max($totalWorkingDays, 1);
                            $fullDays = $entry->full_days ?? 0;
                            $halfDays = $entry->half_days ?? 0;
                            $lateDays = $entry->late_days ?? 0;
                            $eligibleDays = $fullDays + $halfDays;
                            $earnedSalary = $eligibleDays * $perDayWage;
                            
                            $punctualityBonus = ($entry->is_qualified && $entry->punctuality_bonus) ? $entry->punctuality_bonus : 0;
                            $total = $earnedSalary + $punctualityBonus;
                            $dockAmount = $entry->dock_amount ?? 0;
                            $otherDeductions = $entry->other_deductions ?? 0;
                            $otherAllowances = $entry->other_allowances ?? 0;
                            $netSalary = $total + $otherAllowances - $dockAmount - $otherDeductions;
                            $salaryAdvance = $entry->salary_advance ?? 0;
                            $payable = $netSalary - $salaryAdvance;
                            
                            // Add to totals
                            $totalPunctuality += $punctualityBonus;
                            $totalGross += $total;
                            $totalDockAmount += $dockAmount;
                            $totalOtherDeductions += $otherDeductions;
                            $totalNetSalary += $netSalary;
                            $totalAdvance += $salaryAdvance;
                            $totalPayable += $payable;
                        @endphp
                        <tr style="background-color: #fff3cd; border-left: 3px solid #ffc107;">
                            <td>{{ $employees->count() + $loop->iteration }}</td>
                            <td>
                                {{ $entry->employee_name }}
                                <span class="badge bg-warning text-dark ms-1" title="Manual Entry">M</span>
                            </td>
                            <td>{{ $joinDate }}</td>
                            <td>{{ number_format($basicSalary, 2) }}</td>
                            <td>{{ number_format($perDayWage, 2) }}</td>
                            <td>{{ number_format($punctualityBonus, 2) }}</td>
                            <td><strong>{{ number_format($total, 2) }}</strong></td>
                            <td><span class="badge bg-success">{{ $fullDays }}</span></td>
                            <td><span class="badge bg-warning text-dark">{{ $halfDays }}</span></td>
                            <td><span class="badge bg-danger">{{ $lateDays }}</span></td>
                            <td>
                                @if($entry->is_qualified)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td style="color: #ef4444;">{{ number_format($dockAmount, 2) }}</td>
                            <td style="color: #ef4444;">{{ number_format($otherDeductions, 2) }}</td>
                            <td><strong style="color: #667eea;">{{ number_format($netSalary, 2) }}</strong></td>
                            <td style="color: #ff6b6b;">{{ number_format($salaryAdvance, 2) }}</td>
                            <td><strong style="color: #10b981; font-size: 1.1rem;">{{ number_format($payable, 2) }}</strong></td>
                            <td>
                                @if(auth()->user()->hasAnyRole(['CEO', 'Super Admin', 'Co-ordinator']))
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editManualEntryModal{{ $entry->id }}" title="Edit Manual Entry">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete manual entry for {{ $entry->employee_name }}?')) { document.getElementById('delete-manual-{{ $entry->id }}').submit(); }" title="Delete Manual Entry">
                                    <i class="bx bx-trash"></i>
                                </button>
                                <form id="delete-manual-{{ $entry->id }}" action="{{ route('payroll.manual.destroy', $entry->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </td>
                        </tr>

                        {{-- Edit Manual Entry Modal --}}
                        <div class="modal fade" id="editManualEntryModal{{ $entry->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('payroll.manual.update', $entry->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title"><i class="bx bx-edit me-2"></i>Edit Manual Entry - {{ $entry->employee_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Employee Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="employee_name" class="form-control" value="{{ $entry->employee_name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Join Date</label>
                                                        <input type="date" name="join_date" class="form-control" value="{{ $entry->join_date ? $entry->join_date->format('Y-m-d') : '' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Basic Salary (Rs) <span class="text-danger">*</span></label>
                                                        <input type="number" name="basic_salary" step="0.01" class="form-control" value="{{ $entry->basic_salary }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Punctuality Bonus (Rs)</label>
                                                        <input type="number" name="punctuality_bonus" step="0.01" class="form-control" value="{{ $entry->punctuality_bonus }}">
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-4">
                                                            <label class="form-label">Full Days</label>
                                                            <input type="number" name="full_days" class="form-control" value="{{ $entry->full_days }}" min="0">
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="form-label">Half Days</label>
                                                            <input type="number" name="half_days" class="form-control" value="{{ $entry->half_days }}" min="0">
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="form-label">Late Days</label>
                                                            <input type="number" name="late_days" class="form-control" value="{{ $entry->late_days }}" min="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Qualified for Punctuality</label>
                                                        <select name="is_qualified" class="form-select">
                                                            <option value="0" {{ !$entry->is_qualified ? 'selected' : '' }}>No</option>
                                                            <option value="1" {{ $entry->is_qualified ? 'selected' : '' }}>Yes</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Dock Amount (Rs)</label>
                                                        <input type="number" name="dock_amount" step="0.01" class="form-control" value="{{ $entry->dock_amount }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Other Deductions (Rs)</label>
                                                        <input type="number" name="other_deductions" step="0.01" class="form-control" value="{{ $entry->other_deductions }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Other Allowances (Rs)</label>
                                                        <input type="number" name="other_allowances" step="0.01" class="form-control" value="{{ $entry->other_allowances }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Salary Advance (Rs)</label>
                                                        <input type="number" name="salary_advance" step="0.01" class="form-control" value="{{ $entry->salary_advance }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Notes</label>
                                                        <textarea name="notes" class="form-control" rows="3">{{ $entry->notes }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Entry</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </tbody>
                    @if($employees->isNotEmpty() || $manualEntries->isNotEmpty())
                    <tfoot class="payroll-table-footer" style="font-weight: 700; font-size: 1rem;">
                        <tr>
                            <td colspan="3" class="text-end pe-3"><strong>TOTAL:</strong></td>
                            <td class="amount">{{ number_format($totalBasicSalary, 2) }}</td>
                            <td></td> <!-- Per Day Wage - no total needed -->
                            <td class="amount">{{ number_format($totalPunctuality, 2) }}</td>
                            <td class="amount">{{ number_format($totalGross, 2) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="amount" style="color: #ef4444;">{{ number_format($totalDockAmount, 2) }}</td>
                            <td class="amount" style="color: #ef4444;">{{ number_format($totalOtherDeductions, 2) }}</td>
                            <td class="amount" style="color: #667eea;">{{ number_format($totalNetSalary, 2) }}</td>
                            <td class="amount" style="color: #ff6b6b;">{{ number_format($totalAdvance, 2) }}</td>
                            <td class="amount" style="color: #10b981;">{{ number_format($totalPayable, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Manual Entry Modal --}}
<div class="modal fade" id="addManualEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('payroll.manual.store') }}">
                @csrf
                <input type="hidden" name="payroll_month" value="{{ $month }}">
                <input type="hidden" name="payroll_year" value="{{ $year }}">
                <div class="modal-header bg-primary text-white border-bottom">
                    <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i>Add Manual Payroll Entry</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Manual Entry:</strong> Use this for ex-employees or individuals without MIS accounts. All calculations follow the same formula as regular employees.
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3"><i class="bx bx-user me-2"></i>Employee Information</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee Name <span class="text-danger">*</span></label>
                                <input type="text" name="employee_name" class="form-control" placeholder="Enter full name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Join Date</label>
                                <input type="date" name="join_date" class="form-control">
                                <small class="text-muted">Optional: Used for calculating eligible working days</small>
                            </div>

                            <h6 class="fw-bold text-primary mb-3 mt-4"><i class="bx bx-dollar-circle me-2"></i>Salary Information</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Basic Salary (Rs) <span class="text-danger">*</span></label>
                                <input type="number" name="basic_salary" step="0.01" class="form-control" placeholder="e.g., 40000.00" required>
                                <small class="text-muted">Monthly base salary</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Punctuality Bonus (Rs)</label>
                                <input type="number" name="punctuality_bonus" step="0.01" class="form-control" placeholder="e.g., 5000.00" value="5000">
                                <small class="text-muted">Default: 5,000 (only if qualified)</small>
                            </div>

                            <h6 class="fw-bold text-primary mb-3 mt-4"><i class="bx bx-calendar-check me-2"></i>Attendance</h6>
                            <div class="row mb-3">
                                <div class="col-4">
                                    <label class="form-label fw-bold">Full Days</label>
                                    <input type="number" name="full_days" class="form-control" value="0" min="0" max="31">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold">Half Days</label>
                                    <input type="number" name="half_days" class="form-control" value="0" min="0" max="31">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold">Late Days</label>
                                    <input type="number" name="late_days" class="form-control" value="0" min="0" max="31">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Qualified for Punctuality Bonus?</label>
                                <select name="is_qualified" class="form-select">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                                <small class="text-muted">Auto-calculated based on: full days ≥ (working days - 1), half days ≤ 1, late days < 4</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3"><i class="bx bx-calculator me-2"></i>Deductions & Allowances</h6>
                            <div class="mb-3">
                                <label class="form-label">Dock Amount (Rs)</label>
                                <input type="number" name="dock_amount" step="0.01" class="form-control" placeholder="0.00" value="0">
                                <small class="text-muted">Disciplinary or penalty deductions</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Other Deductions (Rs)</label>
                                <input type="number" name="other_deductions" step="0.01" class="form-control" placeholder="0.00" value="0">
                                <small class="text-muted">Tax, loans, etc.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Other Allowances (Rs)</label>
                                <input type="number" name="other_allowances" step="0.01" class="form-control" placeholder="0.00" value="0">
                                <small class="text-muted">Bonuses, incentives, etc.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Salary Advance (Rs)</label>
                                <input type="number" name="salary_advance" step="0.01" class="form-control" placeholder="0.00" value="0">
                                <small class="text-muted">Deducted from final payable amount</small>
                            </div>

                            <h6 class="fw-bold text-primary mb-3 mt-4"><i class="bx bx-note me-2"></i>Additional Notes</h6>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Any additional information about this payroll entry..."></textarea>
                                <small class="text-muted">Optional: Reason for manual entry, payment method, etc.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    // Payroll table search functionality
    document.getElementById('payrollSearch').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('.payroll-table tbody tr');
        
        tableRows.forEach(row => {
            const employeeName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            if (employeeName.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
<style media="print">
    .filter-section, .stats-grid, .card-header .btn, .page-title, .breadcrumb {
        display: none !important;
    }
    .table {
        font-size: 0.9rem;
    }
</style>
@endsection
