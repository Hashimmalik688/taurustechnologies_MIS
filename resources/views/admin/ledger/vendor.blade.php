@extends('layouts.master')

@section('title')
    Vendor Ledger
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .vendor-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(15, 23, 42, 0.8) 100%);
            padding: 2rem;
            border-radius: 16px 16px 0 0;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }

        .vendor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
        }

        .vendor-info h3 {
            color: #d4af37;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .vendor-info p {
            color: #cbd5e1;
            margin-bottom: 0;
        }

        .balance-display {
            text-align: right;
        }

        .balance-label {
            color: #94a3b8;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .balance-amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: #10b981;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-box {
            background: rgba(15, 23, 42, 0.6);
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .stat-credit { color: #10b981; }
        .stat-debit { color: #ef4444; }
        .stat-count { color: #3b82f6; }

        .table-dark-custom {
            color: #cbd5e1;
        }

        .table-dark-custom thead th {
            background: rgba(15, 23, 42, 0.8);
            color: #d4af37;
            border-color: rgba(212, 175, 55, 0.2);
            font-weight: 600;
        }

        .table-dark-custom tbody td {
            border-color: rgba(212, 175, 55, 0.1);
        }

        .table-dark-custom tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .badge-credit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-debit {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
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

        .filter-section {
            background: rgba(15, 23, 42, 0.4);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #cbd5e1;
            border-radius: 8px;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: #d4af37;
            color: #cbd5e1;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Ledger
        @endslot
        @slot('title')
            Vendor Ledger
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="glassmorphism-card mb-4">
                <div class="vendor-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="vendor-avatar">JS</div>
                            <div class="vendor-info">
                                <h3>John Smith</h3>
                                <p>Smith Insurance Co.</p>
                                <span class="badge" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">US Agent</span>
                                <span class="badge ms-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">Active</span>
                            </div>
                        </div>
                        <div class="balance-display">
                            <div class="balance-label">Current Balance</div>
                            <div class="balance-amount">$12,450.00</div>
                        </div>
                    </div>

                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-value stat-credit">$45,230.00</div>
                            <div class="stat-label">Total Credits</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value stat-debit">$32,780.00</div>
                            <div class="stat-label">Total Debits</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value stat-count">156</div>
                            <div class="stat-label">Transactions</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="filter-section">
                        <form class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label" style="color: #cbd5e1; font-size: 0.875rem;">From Date</label>
                                <input type="text" class="form-control" id="date_from" placeholder="Select date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="color: #cbd5e1; font-size: 0.875rem;">To Date</label>
                                <input type="text" class="form-control" id="date_to" placeholder="Select date">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="color: #cbd5e1; font-size: 0.875rem;">Type</label>
                                <select class="form-select">
                                    <option value="">All Types</option>
                                    <option value="credit">Credit</option>
                                    <option value="debit">Debit</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-weight: 600;">
                                    <i class="mdi mdi-filter me-2"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button class="export-btn w-100">
                                    <i class="mdi mdi-file-excel me-2"></i>Export
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="vendorLedgerTable" class="table table-dark-custom table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2025-09-28</td>
                                    <td><span class="badge-credit">Credit</span></td>
                                    <td>Commission</td>
                                    <td>INV-001234</td>
                                    <td>Policy sale commission</td>
                                    <td style="color: #10b981; font-weight: 600;">+$1,250.00</td>
                                    <td style="color: #d4af37; font-weight: 600;">$12,450.00</td>
                                    <td>
                                        <a href="{{ route('ledger.show', 1) }}" class="btn btn-sm" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2025-09-25</td>
                                    <td><span class="badge-debit">Debit</span></td>
                                    <td>Payment</td>
                                    <td>PAY-005678</td>
                                    <td>Monthly payment</td>
                                    <td style="color: #ef4444; font-weight: 600;">-$500.00</td>
                                    <td style="color: #d4af37; font-weight: 600;">$11,200.00</td>
                                    <td>
                                        <a href="{{ route('ledger.show', 2) }}" class="btn btn-sm" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2025-09-20</td>
                                    <td><span class="badge-credit">Credit</span></td>
                                    <td>Commission</td>
                                    <td>INV-001200</td>
                                    <td>Large policy sale</td>
                                    <td style="color: #10b981; font-weight: 600;">+$2,100.00</td>
                                    <td style="color: #d4af37; font-weight: 600;">$11,700.00</td>
                                    <td>
                                        <a href="{{ route('ledger.show', 3) }}" class="btn btn-sm" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            $('#vendorLedgerTable').DataTable({
                pageLength: 10,
                order: [[0, 'desc']]
            });

            flatpickr("#date_from", { dateFormat: "Y-m-d" });
            flatpickr("#date_to", { dateFormat: "Y-m-d" });
        });
    </script>
@endsection
