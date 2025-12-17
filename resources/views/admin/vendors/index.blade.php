@extends('layouts.master')

@section('title')
    Vendors List
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

        .table-dark-custom tbody tr {
            transition: all 0.2s ease;
        }

        .table-dark-custom tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .badge-us-agent {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-vendor {
            background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-supplier {
            background: linear-gradient(135deg, #f97316 0%, #c2410c 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-inactive {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-suspended {
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
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
        }

        .action-btn-view:hover {
            background: rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }

        .action-btn-edit {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .action-btn-edit:hover {
            background: rgba(34, 197, 94, 0.3);
            transform: translateY(-2px);
        }

        .action-btn-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .action-btn-delete:hover {
            background: rgba(239, 68, 68, 0.3);
            transform: translateY(-2px);
        }

        .search-filter-section {
            background: rgba(15, 23, 42, 0.6);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(212, 175, 55, 0.2);
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
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Vendors
        @endslot
        @slot('title')
            All Vendors
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-soft-danger" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-account-group"></i>
                Vendor Management
            </h2>

            <div class="glassmorphism-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title text-gold" style="margin: 0;">All Vendors</h5>
                            <p class="text-muted mb-0 text-small-muted">Manage your vendors, US agents, and suppliers</p>
                        </div>
                        <div>
                            <a href="{{ route('vendors.create') }}" class="gold-gradient-btn">
                                <i class="mdi mdi-plus-circle me-2"></i> Add New Vendor
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="vendorsTable" class="table table-dark-custom table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Sample data for demonstration --}}
                                <tr>
                                    <td>1</td>
                                    <td>John Smith</td>
                                    <td>Smith Insurance Co.</td>
                                    <td>john@smithins.com</td>
                                    <td>(555) 123-4567</td>
                                    <td><span class="badge-us-agent">US Agent</span></td>
                                    <td><span class="badge-active">Active</span></td>
                                    <td class="text-positive">$12,450.00</td>
                                    <td>
                                        <a href="{{ route('vendors.show', 1) }}" class="action-btn action-btn-view">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('vendors.edit', 1) }}" class="action-btn action-btn-edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button class="action-btn action-btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal1">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Sarah Johnson</td>
                                    <td>Johnson & Associates</td>
                                    <td>sarah@johnson.com</td>
                                    <td>(555) 987-6543</td>
                                    <td><span class="badge-vendor">Vendor</span></td>
                                    <td><span class="badge-active">Active</span></td>
                                    <td class="text-positive">$8,230.50</td>
                                    <td>
                                        <a href="{{ route('vendors.show', 2) }}" class="action-btn action-btn-view">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('vendors.edit', 2) }}" class="action-btn action-btn-edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button class="action-btn action-btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal2">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Michael Brown</td>
                                    <td>Brown Supplies Ltd.</td>
                                    <td>mike@brownsupplies.com</td>
                                    <td>(555) 456-7890</td>
                                    <td><span class="badge-supplier">Supplier</span></td>
                                    <td><span class="badge-suspended">Suspended</span></td>
                                    <td class="text-negative">-$1,500.00</td>
                                    <td>
                                        <a href="{{ route('vendors.show', 3) }}" class="action-btn action-btn-view">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('vendors.edit', 3) }}" class="action-btn action-btn-edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button class="action-btn action-btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal3">
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
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#vendorsTable').DataTable({
                pageLength: 10,
                order: [[0, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search vendors..."
                }
            });
        });
    </script>
@endsection
