<?php $__env->startSection('title'); ?>
    Ledger Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .glassmorphism-card:hover {
            border-color: rgba(212, 175, 55, 0.4);
            box-shadow: 0 12px 48px rgba(212, 175, 55, 0.15);
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

        .filter-panel {
            background: rgba(15, 23, 42, 0.6);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .form-label {
            color: #cbd5e1;
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #cbd5e1;
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: #d4af37;
            color: #cbd5e1;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        .form-select option {
            background: #0f172a;
            color: #cbd5e1;
        }

        .balance-summary {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .balance-card {
            flex: 1;
            background: rgba(15, 23, 42, 0.6);
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            text-align: center;
            transition: all 0.3s ease;
        }

        .balance-card:hover {
            transform: translateY(-4px);
            border-color: rgba(212, 175, 55, 0.4);
        }

        .balance-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .balance-label {
            color: #94a3b8;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .balance-credit {
            color: #10b981;
        }

        .balance-debit {
            color: #ef4444;
        }

        .balance-net {
            color: #d4af37;
        }

        .dataTables_wrapper {
            color: #cbd5e1;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #cbd5e1;
            border-radius: 6px;
            padding: 0.5rem;
        }

        .table-dark-custom {
            color: #cbd5e1;
        }

        .table-dark-custom thead th {
            background: rgba(15, 23, 42, 0.8);
            color: #d4af37;
            border-color: rgba(212, 175, 55, 0.2);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .table-dark-custom tbody td {
            border-color: rgba(212, 175, 55, 0.1);
            vertical-align: middle;
        }

        .table-dark-custom tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .badge-debit {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-credit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-category {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }

        .category-commission { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }
        .category-payment { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .category-refund { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .category-expense { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .category-other { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            border: none;
            transition: all 0.2s ease;
            margin: 0 0.2rem;
        }

        .action-btn-view {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .action-btn-view:hover {
            background: rgba(59, 130, 246, 0.3);
        }

        .action-btn-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .action-btn-delete:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .export-btn {
            background: rgba(100, 116, 139, 0.3);
            border: 1px solid rgba(100, 116, 139, 0.5);
            color: #cbd5e1;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-left: 0.5rem;
        }

        .export-btn:hover {
            background: rgba(100, 116, 139, 0.5);
            color: #cbd5e1;
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

        .btn-filter {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-reset {
            background: rgba(100, 116, 139, 0.3);
            border: 1px solid rgba(100, 116, 139, 0.5);
            color: #cbd5e1;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
        }

        .btn-reset:hover {
            background: rgba(100, 116, 139, 0.5);
            color: #cbd5e1;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Ledger
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            All Entries
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #6ee7b7;">
            <i class="mdi mdi-check-circle me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-book-open-page-variant"></i>
                Ledger Management
            </h2>

            <!-- Balance Summary -->
            <div class="balance-summary">
                <div class="balance-card">
                    <div class="balance-value balance-credit">$45,230.00</div>
                    <div class="balance-label">Total Credits</div>
                </div>
                <div class="balance-card">
                    <div class="balance-value balance-debit">$28,450.00</div>
                    <div class="balance-label">Total Debits</div>
                </div>
                <div class="balance-card">
                    <div class="balance-value balance-net">$16,780.00</div>
                    <div class="balance-label">Net Balance</div>
                </div>
            </div>

            <!-- Advanced Filter Panel -->
            <div class="filter-panel">
                <form action="<?php echo e(route('ledger.index')); ?>" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select class="form-select" id="vendor_id" name="vendor_id">
                                <option value="">All Vendors</option>
                                <option value="1">John Smith</option>
                                <option value="2">Sarah Johnson</option>
                                <option value="3">Michael Brown</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="text" class="form-control" id="date_from" name="date_from" placeholder="Select date">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="text" class="form-control" id="date_to" name="date_to" placeholder="Select date">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <option value="commission">Commission</option>
                                <option value="payment">Payment</option>
                                <option value="refund">Refund</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-filter w-100">
                                <i class="mdi mdi-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="glassmorphism-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title" style="color: #d4af37; margin: 0;">All Ledger Entries</h5>
                            <p class="text-muted mb-0" style="color: #94a3b8;">Manage all financial transactions</p>
                        </div>
                        <div>
                            <button class="export-btn">
                                <i class="mdi mdi-file-excel me-2"></i>Export CSV
                            </button>
                            <button class="export-btn">
                                <i class="mdi mdi-file-pdf me-2"></i>Export PDF
                            </button>
                            <a href="<?php echo e(route('ledger.create')); ?>" class="gold-gradient-btn">
                                <i class="mdi mdi-plus-circle me-2"></i>Add Entry
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="ledgerTable" class="table table-dark-custom table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Vendor</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2025-09-28</td>
                                    <td>John Smith</td>
                                    <td><span class="badge-credit">Credit</span></td>
                                    <td><span class="badge-category category-commission">Commission</span></td>
                                    <td style="color: #10b981; font-weight: 600;">+$1,250.00</td>
                                    <td>INV-001234</td>
                                    <td>Policy sale commission</td>
                                    <td>
                                        <a href="<?php echo e(route('ledger.show', 1)); ?>" class="action-btn action-btn-view">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <button class="action-btn action-btn-delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>2025-09-25</td>
                                    <td>Sarah Johnson</td>
                                    <td><span class="badge-debit">Debit</span></td>
                                    <td><span class="badge-category category-payment">Payment</span></td>
                                    <td style="color: #ef4444; font-weight: 600;">-$500.00</td>
                                    <td>PAY-005678</td>
                                    <td>Monthly payment to vendor</td>
                                    <td>
                                        <a href="<?php echo e(route('ledger.show', 2)); ?>" class="action-btn action-btn-view">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <button class="action-btn action-btn-delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>2025-09-20</td>
                                    <td>John Smith</td>
                                    <td><span class="badge-credit">Credit</span></td>
                                    <td><span class="badge-category category-commission">Commission</span></td>
                                    <td style="color: #10b981; font-weight: 600;">+$2,100.00</td>
                                    <td>INV-001200</td>
                                    <td>Large policy sale</td>
                                    <td>
                                        <a href="<?php echo e(route('ledger.show', 3)); ?>" class="action-btn action-btn-view">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <button class="action-btn action-btn-delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>2025-09-18</td>
                                    <td>Michael Brown</td>
                                    <td><span class="badge-debit">Debit</span></td>
                                    <td><span class="badge-category category-expense">Expense</span></td>
                                    <td style="color: #ef4444; font-weight: 600;">-$350.00</td>
                                    <td>EXP-000891</td>
                                    <td>Office supplies</td>
                                    <td>
                                        <a href="<?php echo e(route('ledger.show', 4)); ?>" class="action-btn action-btn-view">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <button class="action-btn action-btn-delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            $('#ledgerTable').DataTable({
                pageLength: 10,
                order: [[0, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search entries..."
                }
            });

            // Initialize date pickers
            flatpickr("#date_from", {
                dateFormat: "Y-m-d",
            });

            flatpickr("#date_to", {
                dateFormat: "Y-m-d",
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/admin/ledger/index.blade.php ENDPATH**/ ?>