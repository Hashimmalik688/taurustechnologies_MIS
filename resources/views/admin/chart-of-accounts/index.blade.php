@extends('layouts.master')

@section('title')
    Chart of Accounts
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .action-btn-view:hover {
            background: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }

        .action-btn-edit {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .action-btn-edit:hover {
            background: rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }

        .page-title-box {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
            border-left: 4px solid #d4af37;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .page-title {
            color: #d4af37;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0" style="color: #94a3b8;">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}" style="color: #cbd5e1;">Dashboard</a></li>
                            <li class="breadcrumb-item" style="color: #d4af37;">Finance and Accounts</li>
                            <li class="breadcrumb-item active" style="color: #d4af37;">Chart of Accounts</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Chart of Accounts</h4>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-12">
                <!-- Filter Panel -->
                <div class="filter-panel">
                    <form id="filterForm" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="account_type" class="form-label">Account Type</label>
                                <select name="account_type" id="account_type" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($accountTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="is_active" class="form-label">Status</label>
                                <select name="is_active" id="is_active" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" id="filterBtn" class="btn gold-gradient-btn w-100">
                                    <i class="bx bx-filter-alt"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Accounts Table Card -->
                <div class="card glassmorphism-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title" style="color: #d4af37; margin: 0;">Chart of Accounts</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('chart-of-accounts.create') }}" class="gold-gradient-btn">
                                    <i class="bx bx-plus-circle"></i> Add Account
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="accountsTable" class="table table-dark-custom table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Parent Account</th>
                                        <th>Current Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be populated via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#accountsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('chart-of-accounts.index') }}",
                    data: function(d) {
                        d.account_type = $('#account_type').val();
                        d.is_active = $('#is_active').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'account_code', name: 'account_code' },
                    { data: 'account_name', name: 'account_name' },
                    { data: 'account_type', name: 'account_type' },
                    { data: 'account_category', name: 'account_category' },
                    { data: 'parent_account_name', name: 'parent_account_name' },
                    { data: 'balance_formatted', name: 'current_balance' },
                    { data: 'status', name: 'is_active' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']],
                pageLength: 25,
                language: {
                    processing: '<div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: "No accounts found",
                    zeroRecords: "No matching accounts found"
                }
            });

            // Filter button click
            $('#filterBtn').on('click', function() {
                table.draw();
            });

            // Reset filters on change
            $('#account_type, #is_active').on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection
