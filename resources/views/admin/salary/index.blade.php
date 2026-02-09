@extends('layouts.master')

@section('title', 'Calculate Salaries')

@section('css')
<style>
    .salary-card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .salary-card:hover {
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.15);
    }
    .gold-border { border-left: 4px solid #d4af37 !important; }
    .employee-row {
        transition: all 0.2s ease;
    }
    .employee-row:hover {
        background-color: #f8f9fa !important;
    }
    .badge-gold {
        background-color: #d4af37;
        color: #1a1a1a;
    }
    .stat-card {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .table-salary thead th {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #d4af37;
        font-weight: 600;
        border: none;
        padding: 12px 8px;
        font-size: 0.875rem;
    }
    .table-salary tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }
    .table-salary tbody td {
        padding: 12px 8px;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1" style="color: #1a1a1a; font-weight: 600;">
                            <i class="bx bx-calculator me-2" style="color: #d4af37;"></i>
                            Calculate Monthly Salaries
                        </h4>
                        <p class="text-muted mb-0">Process salary calculations for {{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }} {{ $currentYear }}</p>
                    </div>
                    <a href="{{ route('salary.records') }}" class="btn btn-outline-dark">
                        <i class="bx bx-file me-1"></i> View All Records
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('salary.calculate') }}" method="POST" id="salaryCalculationForm">
            @csrf

            <!-- Period Selection & Quick Stats -->
            <div class="row mb-3">
                <!-- Period Selection -->
                <div class="col-md-4">
                    <div class="card salary-card gold-border">
                        <div class="card-body">
                            <h6 class="card-title mb-3" style="color: #1a1a1a; font-weight: 600;">
                                <i class="bx bx-calendar me-2" style="color: #d4af37;"></i>
                                Calculation Period
                            </h6>
                            <div class="row g-2">
                                <div class="col-7">
                                    <label class="form-label text-muted small">Month</label>
                                    <select name="month" id="month" class="form-select form-select-sm" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-5">
                                    <label class="form-label text-muted small">Year</label>
                                    <select name="year" id="year" class="form-select form-select-sm" required>
                                        @for ($i = date('Y'); $i >= date('Y') - 2; $i--)
                                            <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="col-md-8">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small mb-1">Total Employees</div>
                                    <h5 class="mb-0" style="color: #1a1a1a; font-weight: 700;">{{ $employees->count() }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small mb-1">Calculated</div>
                                    <h5 class="mb-0 text-success" style="font-weight: 700;">{{ $existingRecords->count() }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small mb-1">Total Basic</div>
                                    <h6 class="mb-0" style="color: #d4af37; font-weight: 700;">Rs{{ number_format($employees->sum('basic_salary'), 0) }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small mb-1">Selected</div>
                                    <h5 class="mb-0 text-primary" style="font-weight: 700;" id="selectedCount">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card salary-card mb-3">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="selectAllEmployees()">
                                <i class="bx bx-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="unselectAllEmployees()">
                                <i class="bx bx-square"></i> Clear All
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="refreshSalesData()">
                                <i class="bx bx-refresh"></i> Refresh Sales
                            </button>
                        </div>
                        <button type="submit" class="btn btn-success" id="calculateBtn" disabled>
                            <i class="bx bx-calculator me-1"></i>
                            Calculate Selected Salaries
                        </button>
                    </div>
                </div>
            </div>

            <!-- Employee Selection Table -->
            <div class="card salary-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-salary mb-0">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th style="min-width: 200px;">Employee</th>
                                    <th>Basic</th>
                                    <th>Target</th>
                                    <th>Actual</th>
                                    <th>Extra</th>
                                    <th>Bonus/Sale</th>
                                    <th>Total Bonus</th>
                                    <th>Gross Salary</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    @php
                                        $existingRecord = $existingRecords->get($employee->id);
                                        $actualSales = \App\Models\Lead::where('closer_name', $employee->name)
                                            ->whereIn('status', ['accepted', 'underwritten'])
                                            ->whereMonth('created_at', $currentMonth)
                                            ->whereYear('created_at', $currentYear)
                                            ->count();
                                        $extraSales = max(0, $actualSales - ($employee->target_sales ?? 0));
                                        $totalBonus = $extraSales * ($employee->bonus_per_extra_sale ?? 0);
                                        $grossSalary = ($employee->basic_salary ?? 0) + $totalBonus;
                                    @endphp
                                    <tr class="employee-row {{ $existingRecord ? 'table-light' : '' }}">
                                        <td>
                                            <input type="checkbox" name="user_ids[]" value="{{ $employee->id }}"
                                                class="form-check-input employee-checkbox"
                                                {{ $existingRecord ? 'checked' : '' }}
                                                {{ !$employee->basic_salary || $employee->basic_salary <= 0 ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle" style="background-color: #d4af37; color: #1a1a1a; font-weight: 600;">
                                                        {{ substr($employee->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600; color: #1a1a1a; font-size: 0.875rem;">{{ $employee->name }}</div>
                                                    <small class="text-muted">{{ $employee->email }}</small>
                                                    @if ($employee->roles->isNotEmpty())
                                                        <div class="mt-1">
                                                            @foreach($employee->roles as $role)
                                                                <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $role->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if (!$employee->basic_salary || $employee->basic_salary <= 0)
                                                        <div class="mt-1">
                                                            <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">⚠️ Configure Salary</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($employee->basic_salary && $employee->basic_salary > 0)
                                                <span style="font-weight: 600; color: #1a1a1a;">
                                                    Rs{{ number_format($employee->basic_salary ?? 0, 0) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->is_sales_employee)
                                                <span class="badge bg-primary">{{ $employee->target_sales ?? 0 }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->is_sales_employee)
                                                <span class="badge {{ $actualSales >= ($employee->target_sales ?? 0) ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $actualSales }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->is_sales_employee && $extraSales > 0)
                                                <span class="badge bg-success">+{{ $extraSales }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->is_sales_employee)
                                                <span style="color: #059669; font-weight: 600;">
                                                    Rs{{ number_format($employee->bonus_per_extra_sale ?? 0, 0) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($totalBonus > 0)
                                                <span style="color: #059669; font-weight: 600;">
                                                    Rs{{ number_format($totalBonus, 0) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #1a1a1a; font-size: 0.95rem;">
                                                Rs{{ number_format($grossSalary, 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($existingRecord)
                                                @if($existingRecord->status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($existingRecord->status == 'approved')
                                                    <span class="badge bg-info">Approved</span>
                                                @elseif($existingRecord->status == 'calculated')
                                                    <span class="badge bg-warning">Calculated</span>
                                                @else
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="openSalarySettings({{ $employee->id }}, '{{ addslashes($employee->name) }}', {{ $employee->basic_salary ?? 0 }}, {{ $employee->target_sales ?? 20 }}, {{ $employee->bonus_per_extra_sale ?? 0 }}, {{ $employee->punctuality_bonus ?? 0 }}, {{ $employee->fine_per_absence ?? 0 }}, {{ $employee->fine_per_late ?? 0 }}, '{{ $employee->salary_start_date ?? '' }}', '{{ $employee->salary_end_date ?? '' }}', {{ $employee->payday_date ?? 5 }}, {{ $employee->is_sales_employee ? 'true' : 'false' }})"
                                                title="Salary Settings">
                                                <i class="bx bx-cog"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bx bx-user-x" style="font-size: 3rem; opacity: 0.3;"></i>
                                                <p class="mt-2 mb-0">No employees found.</p>
                                                <small>Add users from User Management.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>

        <!-- Salary Settings Modal -->
        <div class="modal fade" id="salarySettingsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);">
                        <h5 class="modal-title" style="color: #d4af37;">
                            <i class="bx bx-cog me-2"></i>
                            Salary Settings - <span id="settingsEmployeeName"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="salarySettingsForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" id="settings_user_id">

                            <!-- Employee Type -->
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="settings_is_sales_employee" name="is_sales_employee" checked>
                                    <label class="form-check-label fw-bold" for="settings_is_sales_employee">
                                        Sales Employee (Has sales targets and bonuses)
                                    </label>
                                </div>
                            </div>

                            <!-- Basic Salary -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3" style="color: #1a1a1a;">💰 Base Salary</h6>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Basic Salary (Rs) *</label>
                                    <input type="number" id="settings_basic_salary" name="basic_salary" class="form-control" step="0.01" min="0" required>
                                    <small class="text-muted">Monthly base salary amount</small>
                                </div>
                            </div>

                            <!-- Sales Settings -->
                            <div id="settings_sales_section">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h6 class="border-bottom pb-2 mb-3" style="color: #1a1a1a;">📊 Sales & Bonus</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Target Sales</label>
                                        <input type="number" id="settings_target_sales" name="target_sales" class="form-control" min="0" value="20">
                                        <small class="text-muted">Monthly sales target (default: 20)</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bonus per Extra Sale (Rs)</label>
                                        <input type="number" id="settings_bonus_per_extra_sale" name="bonus_per_extra_sale" class="form-control" step="0.01" min="0" value="0">
                                        <small class="text-muted">Bonus for each sale above target</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Punctuality & Fines -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3" style="color: #1a1a1a;">⏰ Punctuality & Fines (Dock)</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Punctuality Bonus (Rs)</label>
                                    <input type="number" id="settings_punctuality_bonus" name="punctuality_bonus" class="form-control" step="0.01" min="0" value="0">
                                    <small class="text-muted">If 0 offs, <2 half days, <4 late days</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fine per Absence (Rs)</label>
                                    <input type="number" id="settings_fine_per_absence" name="fine_per_absence" class="form-control" step="0.01" min="0" value="0">
                                    <small class="text-muted">Fine/dock per leave/absence</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fine per Late (Rs)</label>
                                    <input type="number" id="settings_fine_per_late" name="fine_per_late" class="form-control" step="0.01" min="0" value="0">
                                    <small class="text-muted">Fine/dock per late arrival</small>
                                </div>
                            </div>

                            <!-- Salary Period -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3" style="color: #1a1a1a;">📅 Salary Period</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" id="settings_salary_start_date" name="salary_start_date" class="form-control">
                                    <small class="text-muted">When salary starts</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">End Date</label>
                                    <input type="date" id="settings_salary_end_date" name="salary_end_date" class="form-control">
                                    <small class="text-muted">When salary ends (optional)</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Payday (Day of Month)</label>
                                    <input type="number" id="settings_payday_date" name="payday_date" class="form-control" min="1" max="31" value="5">
                                    <small class="text-muted">Salary payment day (1-31)</small>
                                </div>
                            </div>

                            <!-- Info Alert -->
                            <div class="alert alert-info">
                                <strong>💡 Calculation Rules:</strong>
                                <ul class="mb-0 small">
                                    <li>Working Days: 22 per month</li>
                                    <li>Daily Salary = Basic Salary ÷ 22</li>
                                    <li>Deductions: Leave = 1 day, Half day = 0.5 day</li>
                                    <li>Punctuality Bonus: Disqualified if ≥1 off OR ≥2 half days OR ≥4 late arrivals</li>
                                    <li>Extra fines are applied on top of deductions</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
        const calculateBtn = document.getElementById('calculateBtn');
        const selectedCount = document.getElementById('selectedCount');

        // Select all functionality
        selectAll.addEventListener('change', function() {
            employeeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox change
        employeeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateSelectedCount();
            });
        });

        function updateSelectAllState() {
            const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
            selectAll.checked = checkedCount === employeeCheckboxes.length && checkedCount > 0;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < employeeCheckboxes.length;
        }

        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
            selectedCount.textContent = checkedCount;
            calculateBtn.disabled = checkedCount === 0;
        }

        // Initialize
        updateSelectAllState();
        updateSelectedCount();
    });

    function selectAllEmployees() {
        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
        document.getElementById('selectAll').checked = true;
        document.getElementById('selectedCount').textContent = document.querySelectorAll('.employee-checkbox').length;
        document.getElementById('calculateBtn').disabled = false;
    }

    function unselectAllEmployees() {
        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        document.getElementById('selectedCount').textContent = '0';
        document.getElementById('calculateBtn').disabled = true;
    }

    function refreshSalesData() {
        location.reload();
    }

    // Open salary settings modal
    function openSalarySettings(userId, name, basicSalary, targetSales, bonusPerSale, punctualityBonus, finePerAbsence, finePerLate, startDate, endDate, paydayDate, isSalesEmployee) {
        document.getElementById('settings_user_id').value = userId;
        document.getElementById('settingsEmployeeName').textContent = name;
        document.getElementById('settings_basic_salary').value = basicSalary;
        document.getElementById('settings_target_sales').value = targetSales;
        document.getElementById('settings_bonus_per_extra_sale').value = bonusPerSale;
        document.getElementById('settings_punctuality_bonus').value = punctualityBonus;
        document.getElementById('settings_fine_per_absence').value = finePerAbsence;
        document.getElementById('settings_fine_per_late').value = finePerLate;
        document.getElementById('settings_salary_start_date').value = startDate;
        document.getElementById('settings_salary_end_date').value = endDate;
        document.getElementById('settings_payday_date').value = paydayDate;
        document.getElementById('settings_is_sales_employee').checked = isSalesEmployee;

        // Set form action
        document.getElementById('salarySettingsForm').action = `/salary/employees/${userId}`;

        // Toggle sales section
        toggleSalesSection();

        // Show modal
        new bootstrap.Modal(document.getElementById('salarySettingsModal')).show();
    }

    // Toggle sales section visibility
    function toggleSalesSection() {
        const isSales = document.getElementById('settings_is_sales_employee').checked;
        document.getElementById('settings_sales_section').style.display = isSales ? 'block' : 'none';
    }

    // Listen to sales employee toggle
    document.getElementById('settings_is_sales_employee')?.addEventListener('change', toggleSalesSection);

</script>
@endsection