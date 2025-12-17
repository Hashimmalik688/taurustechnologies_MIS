{{-- File: resources/views/salary/employees.blade.php --}}

@extends('layouts.master')

@section('title', 'Employee Salary Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users-cog me-2"></i>
                            Employee Salary Settings
                        </h3>
                        <div class="d-flex gap-2">
                            <a href="{{ route('salary.index') }}" class="btn btn-primary">
                                <i class="fas fa-calculator me-1"></i>
                                Calculate Salaries
                            </a>
                            <button class="btn btn-outline-success" onclick="bulkUpdateModal()">
                                <i class="fas fa-edit me-1"></i>
                                Bulk Update
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Search and Filter --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-light">
                                    <div class="card-body">
                                        <form method="GET" action="{{ route('salary.employees') }}" class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Search Employee</label>
                                                <input type="text" name="search" class="form-control"
                                                    value="{{ request('search') }}"
                                                    placeholder="Search by name or email...">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Salary Range</label>
                                                <select name="salary_range" class="form-select">
                                                    <option value="">All Ranges</option>
                                                    <option value="0-25000"
                                                        {{ request('salary_range') == '0-25000' ? 'selected' : '' }}>Rs0 -
                                                        Rs25,000</option>
                                                    <option value="25000-50000"
                                                        {{ request('salary_range') == '25000-50000' ? 'selected' : '' }}>
                                                        Rs25,000 - Rs50,000</option>
                                                    <option value="50000-100000"
                                                        {{ request('salary_range') == '50000-100000' ? 'selected' : '' }}>
                                                        Rs50,000 - Rs100,000</option>
                                                    <option value="100000+"
                                                        {{ request('salary_range') == '100000+' ? 'selected' : '' }}>
                                                        Rs100,000+</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Target Sales</label>
                                                <select name="target_range" class="form-select">
                                                    <option value="">All Targets</option>
                                                    <option value="0-10"
                                                        {{ request('target_range') == '0-10' ? 'selected' : '' }}>0 - 10
                                                        sales</option>
                                                    <option value="10-20"
                                                        {{ request('target_range') == '10-20' ? 'selected' : '' }}>10 - 20
                                                        sales</option>
                                                    <option value="20-50"
                                                        {{ request('target_range') == '20-50' ? 'selected' : '' }}>20 - 50
                                                        sales</option>
                                                    <option value="50+"
                                                        {{ request('target_range') == '50+' ? 'selected' : '' }}>50+ sales
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    <a href="{{ route('salary.employees') }}"
                                                        class="btn btn-outline-secondary">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Summary Cards --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">{{ $employees->total() }}</h5>
                                        <p class="card-text small">Total Employees</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-success">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">
                                            Rs{{ number_format($employees->sum('basic_salary'), 0) }}
                                        </h5>
                                        <p class="card-text small">Total Basic Salaries</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-info">
                                    <div class="card-body">
                                        <h5 class="card-title text-info">
                                            {{ $employees->avg('target_sales') ? number_format($employees->avg('target_sales'), 1) : '0' }}
                                        </h5>
                                        <p class="card-text small">Average Target Sales</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-warning">
                                    <div class="card-body">
                                        <h5 class="card-title text-warning">
                                            Rs{{ number_format($employees->avg('bonus_per_extra_sale'), 0) }}
                                        </h5>
                                        <p class="card-text small">Average Bonus/Sale</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Employees Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Employee</th>
                                        <th>Type</th>
                                        <th>Basic Salary</th>
                                        <th>Target Sales</th>
                                        <th>Bonus/Extra Sale</th>
                                        <th>Punctuality Bonus</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr class="employee-row" data-employee-id="{{ $employee->id }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input employee-checkbox"
                                                    value="{{ $employee->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <img src="{{ $employee->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($employee->name) }}"
                                                            alt="Avatar" class="rounded-circle"
                                                            style="width: 40px; height: 40px;">
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $employee->name }}</div>
                                                        <small class="text-muted">{{ $employee->email }}</small>
                                                        @if ($employee->roles->isNotEmpty())
                                                            <br>
                                                            @foreach($employee->roles as $role)
                                                                <small class="badge bg-primary me-1">{{ $role->name }}</small>
                                                            @endforeach
                                                        @endif
                                                        @if ($employee->department)
                                                            <br><small
                                                                class="badge bg-light text-dark">{{ $employee->department }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($employee->is_sales_employee)
                                                    <span class="badge bg-success">Sales</span>
                                                @else
                                                    <span class="badge bg-secondary">Non-Sales</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary fs-6">
                                                    Rs{{ number_format($employee->basic_salary, 0) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($employee->is_sales_employee)
                                                    <span class="badge bg-info fs-6">
                                                        {{ $employee->target_sales ?? 20 }} sales
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($employee->is_sales_employee)
                                                    <span class="fw-bold text-success">
                                                        Rs{{ number_format($employee->bonus_per_extra_sale ?? 0, 0) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>?? 20 }}, {{ $employee->bonus_per_extra_sale ?? 0 }}, {{ $employee->punctuality_bonus ?? 0 }}, {{ $employee->is_sales_employee ? 'true' : 'false'
                                            <td>
                                                <span class="fw-bold text-warning">
                                                    Rs{{ number_format($employee->punctuality_bonus ?? 0, 0) }}
                                                </span>
                                                @if($employee->punctuality_bonus > 0)
                                                    <br><small class="text-muted">If qualified</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $employee->updated_at->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <butt9n class="btn btn-sm btn-outline-primary"
                                                        onclick="editEmployee({{ $employee->id }}, '{{ $employee->name }}', {{ $employee->basic_salary }}, {{ $employee->target_sales }}, {{ $employee->bonus_per_extra_sale }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info"
                                                        onclick="viewSalaryHistory({{ $employee->id }})">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success"
                                                        onclick="quickCalculate({{ $employee->id }})">
                                                        <i class="fas fa-calculator"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-users fa-3x mb-3"></i>
                                                    <p class="mb-0">No employees found with configured salary.</p>
                                                    <small>Make sure users have basic salary and employment_status = 'active' set.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if ($employees->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $employees->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Employee Modal --}}
    <div class="modal fade" id="editEmployeeModal" tabindex="-1">
        <div class="modal-dialog modmb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_sales_employee" name="is_sales_employee" checked>
                                <label class="form-check-label" for="is_sales_employee">
                                    <strong>Sales Employee</strong> (Has sales targets and bonuses)
                                </label>
                            </div>
                            <small class="form-text text-muted">Uncheck for non-sales employees (HR, Admin, etc.)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="basic_salary" class="form-label">Basic Salary (Rs) *</label>
                                    <input type="number" id="basic_salary" name="basic_salary" class="form-control"
                                        step="0.01" min="0" required>
                                    <div class="form-text">Monthly basic salary amount (Required)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="punctuality_bonus" class="form-label">Punctuality Bonus (Rs)</label>
                                    <input type="number" id="punctuality_bonus" name="punctuality_bonus" class="form-control"
                                        step="0.01" min="0" value="0">
                                    <div class="form-text">Bonus if 0 offs, <2 half days, <4 late arrivals</div>
                                </div>
                            </div>
                        </div>

                        <div id="sales_fields">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i><strong>Sales-Specific Settings</strong>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="target_sales" class="form-label">Target Sales</label>
                                        <input type="number" id="target_sales" name="target_sales" class="form-control"
                                            min="0" value="20">
                                        <div class="form-text">Monthly sales target (default: 20)</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bonus_per_extra_sale" class="form-label">Bonus per Extra Sale (Rs)</label>
                                        <input type="number" id="bonus_per_extra_sale" name="bonus_per_extra_sale"
                                            class="form-control" step="0.01" min="0" value="0">
                                        <div class="form-text">Bonus for each sale above target</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-calculator me-2"></i>Salary Calculation Rules</h6>
                                    <ul class="mb-0 small">
                                        <li><strong>Working Days:</strong> 22 days per month</li>
                                        <li><strong>Daily Salary:</strong> Basic Salary ÷ 22</li>
                                        <li><strong>Leave Deduction:</strong> Full day = 1 day salary, Half day = 0.5 day salary</li>
                                        <li><strong>Punctuality Rules:</strong> No bonus if ≥1 off OR ≥2 half days OR ≥4 late arrivals (after 7:15 AM)</li>
                                        <li><strong>Sales Bonus:</strong> Only paid for sales above target (if sales employee)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-calculator me-2"></i>Example Calculation</h6>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <strong>Basic Salary</strong>
                                            <div id="preview_basic">Rs0</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Punctuality Bonus</strong>
                                            <div id="preview_punctuality">Rs0</div>
                                        </div>
                                        <div class="col-md-3" id="preview_sales_section">
                                            <strong>Sales Bonus (10 extra)</strong>
                                            <div id="preview_bonus">Rs0</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Max Total
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-calculator me-2"></i>Calculation Preview</h6>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <strong>Basic Salary</strong>
                                            <div id="preview_basic">Rs0</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Target Achievement</strong>
                                            <div id="preview_target">0 sales</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Bonus (10 extra)</strong>
                                            <div id="preview_bonus">Rs0</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total (10 extra)</strong>
                                            <div id="preview_total">Rs0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bulk Update Modal --}}
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Update Salary Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulkUpdateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This will update selected employees. Leave fields blank to keep current values.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Update Type</label>
                            <select id="bulk_update_type" class="form-select" required>
                                <option value="">Select update type</option>
                                <option value="percentage">Percentage Increase</option>
                                <option value="fixed">Fixed Amount Increase</option>
                                <option value="set">Set Specific Values</option>
                            </select>
                        </div>

                        <div id="percentage_fields" class="d-none">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Salary Increase (%)</label>
                                    <input type="number" name="salary_percentage" class="form-control" step="0.1"
                                        min="0" max="100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Target Increase (%)</label>
                                    <input type="number" name="target_percentage" class="form-control" step="0.1"
                                        min="0" max="100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Bonus Increase (%)</label>
                                    <input type="number" name="bonus_percentage" class="form-control" step="0.1"
                                        min="0" max="100">
                                </div>
                            </div>, punctualityBonus, isSalesEmployee) {
            document.getElementById('employee_id').value = id;
            document.getElementById('employee_name').value = name;
            document.getElementById('basic_salary').value = basicSalary;
            document.getElementById('target_sales').value = targetSales;
            document.getElementById('bonus_per_extra_sale').value = bonusPerSale;
            document.getElementById('punctuality_bonus').value = punctualityBonus;
            document.getElementById('is_sales_employee').checked = isSalesEmployee;

            document.getElementById('editEmployeeForm').action = `/salary/employees/${id}`;

            toggleSalesFields();
            updatePreview();
            new bootstrap.Modal(document.getElementById('editEmployeeModal')).show();
        }

        // Toggle sales fields visibility
        function toggleSalesFields() {
            const isSalesEmployee = document.getElementById('is_sales_employee').checked;
            document.getElementById('sales_fields').style.display = isSalesEmployee ? 'block' : 'none';
            document.getElementById('preview_sales_section').style.display = isSalesEmployee ? 'block' : 'none';
            updatePreview();
        }

        // Update calculation preview
        function updatePreview() {
            const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
            const targetSales = parseInt(document.getElementById('target_sales').value) || 20;
            const bonusPerSale = parseFloat(document.getElementById('bonus_per_extra_sale').value) || 0;
            const punctualityBonus = parseFloat(document.getElementById('punctuality_bonus').value) || 0;
            const isSalesEmployee = document.getElementById('is_sales_employee').checked;

            const bonusFor10Extra = isSalesEmployee ? (bonusPerSale * 10) : 0;
            const totalMax = basicSalary + bonusFor10Extra + punctualityBonus;

            document.getElementById('preview_basic').textContent = 'Rs' + basicSalary.toLocaleString();
            document.getElementById('preview_punctuality').textContent = 'Rs' + punctualityBonus.toLocaleString();
            document.getElementById('preview_bonus').textContent = 'Rs' + bonusFor10Extra.toLocaleString();
            document.getElementById('preview_total').textContent = 'Rs' + totalMax
                                    <input type="number" name="salary_set" class="form-control" step="0.01"
                                        min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Set Target</label>
                                    <input type="number" name="target_set" class="form-control" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Set Bonus (Rs)</label>
                                    <input type="number" name="bonus_set" class="form-control" step="0.01"
                                        min="0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div id="selected_employees_info" class="text-muted">
                                No employees selected
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Selected</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Edit employee functionality
        function editEmployee(id, name, basicSalary, targetSales, bonusPerSale) {
            document.getElementById('employee_id').value = id;
            document.getElementById('employee_name').value = name;
            document.getElementById('basic_salary').value = basicSalary;
            document.getElementById('target_sales').value = targetSales;
            document.getElementById('bonus_per_extra_sale').value = bonusPerSale;

            document.getElementById('editEmployeeForm').action = `/salary/employees/${id}`;

            updatePreview();
            new bootstrap.Modal(document.getElementById('editEmployeeModal')).show();
        }

        // Update calculation preview
        document.getElementById('punctuality_bonus').addEventListener('input', updatePreview);
        document.getElementById('is_sales_employee').addEventListener('change', toggleSalesFields);
        function updatePreview() {
            const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
            const targetSales = parseInt(document.getElementById('target_sales').value) || 0;
            const bonusPerSale = parseFloat(document.getElementById('bonus_per_extra_sale').value) || 0;

            const bonusFor10Extra = bonusPerSale * 10;
            const totalWith10Extra = basicSalary + bonusFor10Extra;

            document.getElementById('preview_basic').textContent = 'Rs' + basicSalary.toLocaleString();
            document.getElementById('preview_target').textContent = targetSales + ' sales';
            document.getElementById('preview_bonus').textContent = 'Rs' + bonusFor10Extra.toLocaleString();
            document.getElementById('preview_total').textContent = 'Rs' + totalWith10Extra.toLocaleString();
        }

        // Bulk update functionality
        function bulkUpdateModal() {
            const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');
            if (selectedEmployees.length === 0) {
                alert('Please select at least one employee to update.');
                return;
            }

            updateSelectedEmployeesInfo();
            new bootstrap.Modal(document.getElementById('bulkUpdateModal')).show();
        }

        function updateSelectedEmployeesInfo() {
            const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');
            const count = selectedEmployees.length;
            document.getElementById('selected_employees_info').textContent =
                `${count} employee${count > 1 ? 's' : ''} selected for bulk update`;
        }

        // View salary history
        function viewSalaryHistory(employeeId) {
            window.location.href = `/salary/records?employee=${employeeId}`;
        }

        // Quick calculate current month
        function quickCalculate(employeeId) {
            if (confirm('Calculate salary for this employee for the current month?')) {
                // Create a form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/salary/calculate';

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="month" value="${new Date().getMonth() + 1}">
            <input type="hidden" name="year" value="${new Date().getFullYear()}">
            <input type="hidden" name="user_ids[]" value="${employeeId}">
        `;

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Update preview on input change
        document.getElementById('basic_salary').addEventListener('input', updatePreview);
        document.getElementById('target_sales').addEventListener('input', updatePreview);
        document.getElementById('bonus_per_extra_sale').addEventListener('input', updatePreview);

        // Bulk update type change
        document.getElementById('bulk_update_type').addEventListener('change', function() {
            const value = this.value;
            document.getElementById('percentage_fields').classList.toggle('d-none', value !== 'percentage');
            document.getElementById('fixed_fields').classList.toggle('d-none', value !== 'fixed');
            document.getElementById('set_fields').classList.toggle('d-none', value !== 'set');
        });

        // Form submissions
        document.getElementById('editEmployeeForm').addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to update this employee\'s salary settings?')) {
                e.preventDefault();
            }
        });

        document.getElementById('bulkUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');
            if (selectedEmployees.length === 0) {
                alert('Please select at least one employee to update.');
                return;
            }

            if (confirm(`Are you sure you want to update ${selectedEmployees.length} employee(s)?`)) {
                // Process bulk update via AJAX
                const formData = new FormData(this);

                // Add selected employee IDs
                selectedEmployees.forEach(checkbox => {
                    formData.append('employee_ids[]', checkbox.value);
                });

                fetch('/salary/employees/bulk-update', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error updating employees: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating employees');
                    });
            }
        });
    </script>
@endsection
