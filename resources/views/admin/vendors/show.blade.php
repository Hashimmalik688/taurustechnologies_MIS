<?php
@extends('layouts.master')

@section('title')
    Vendor Details
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    {{-- styling moved to public/css/admin-ui.css --}}
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Vendors
        @endslot
        @slot('title')
            Vendor Details
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-soft-success" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <h2 class="text-gold fw-bold mb-3">
                <i class="mdi mdi-account-details me-2"></i>
                Vendor Details
            </h2>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Vendor Details -->
        <div class="col-lg-4">
            <div class="glassmorphism-card mb-4">
                <div class="card-body text-center">
                    <div class="vendor-avatar mx-auto img-rounded-md d-flex align-items-center justify-content-center" style="width:100px;height:100px;border-radius:50%;font-size:2rem;font-weight:700;margin-bottom:1rem;background:linear-gradient(135deg,#d4af37 0%,#b8941f 100%);color:#0f172a;">
                        {{ strtoupper(substr($vendor->name ?? 'JS', 0, 2)) }}
                    </div>
                    <h3 class="text-gold fw-semibold">{{ $vendor->name ?? 'John Smith' }}</h3>
                    <p class="text-small-muted">{{ $vendor->company_name ?? 'Smith Insurance Co.' }}</p>

                    <div class="mb-3">
                        @if(($vendor->vendor_type ?? 'us_agent') == 'us_agent')
                            <span class="badge-paid">US Agent</span>
                        @elseif(($vendor->vendor_type ?? '') == 'vendor')
                            <span class="badge-paid">Vendor</span>
                        @else
                            <span class="badge-pending">Supplier</span>
                        @endif

                        @if(($vendor->status ?? 'active') == 'active')
                            <span class="badge-paid ms-2">Active</span>
                        @elseif(($vendor->status ?? '') == 'inactive')
                            <span class="badge-late ms-2">Inactive</span>
                        @else
                            <span class="badge-pending ms-2">Suspended</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2 justify-content-center mb-3">
                        <a href="{{ route('vendors.edit', $vendor->id ?? 1) }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-pencil me-1"></i> Edit
                        </a>
                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="mdi mdi-delete me-1"></i> Delete
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-small-muted"><i class="mdi mdi-email me-2"></i>Email</span>
                        <span class="fw-semibold">{{ $vendor->email ?? 'john@smithins.com' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-small-muted"><i class="mdi mdi-phone me-2"></i>Phone</span>
                        <span class="fw-semibold">{{ $vendor->phone ?? '(555) 123-4567' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-small-muted"><i class="mdi mdi-map-marker me-2"></i>Address</span>
                        <span class="fw-semibold">{{ $vendor->city ?? 'New York' }}, {{ $vendor->state ?? 'NY' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-small-muted"><i class="mdi mdi-percent me-2"></i>Commission</span>
                        <span class="fw-semibold">{{ $vendor->commission_rate ?? '15' }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-small-muted"><i class="mdi mdi-calendar me-2"></i>Payment Terms</span>
                        <span class="fw-semibold">{{ $vendor->payment_terms ?? 'Net 30' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Financial Summary & Transactions -->
        <div class="col-lg-8">
            <!-- Financial Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value green">$12,450</div>
                        <div class="metric-title">Total Credits</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value red">$8,230</div>
                        <div class="metric-title">Total Debits</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value gold">$4,220</div>
                        <div class="metric-title">Current Balance</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card text-center">
                        <div class="metric-value">24</div>
                        <div class="metric-title">Transactions</div>
                    </div>
                </div>
            </div>

            <!-- Recent Ledger Entries -->
            <div class="glassmorphism-card">
                <div class="card-body">
                    <h5 class="text-gold fw-semibold mb-3">Recent Ledger Entries</h5>

                    <div class="table-wrapper">
                        <table class="table table-sm table-striped locked-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2025-09-28</td>
                                    <td><span class="badge-paid">Credit</span></td>
                                    <td>Commission</td>
                                    <td>INV-001234</td>
                                    <td class="text-positive fw-semibold">+$1,250.00</td>
                                </tr>
                                <tr>
                                    <td>2025-09-25</td>
                                    <td><span class="badge-late">Debit</span></td>
                                    <td>Payment</td>
                                    <td>PAY-005678</td>
                                    <td class="text-negative fw-semibold">-$500.00</td>
                                </tr>
                                <tr>
                                    <td>2025-09-20</td>
                                    <td><span class="badge-paid">Credit</span></td>
                                    <td>Sale Commission</td>
                                    <td>INV-001200</td>
                                    <td class="text-positive fw-semibold">+$2,100.00</td>
                                </tr>
                                <tr>
                                    <td>2025-09-15</td>
                                    <td><span class="badge-paid">Credit</span></td>
                                    <td>Referral Bonus</td>
                                    <td>REF-000456</td>
                                    <td class="text-positive fw-semibold">+$750.00</td>
                                </tr>
                                <tr>
                                    <td>2025-09-10</td>
                                    <td><span class="badge-late">Debit</span></td>
                                    <td>Adjustment</td>
                                    <td>ADJ-000123</td>
                                    <td class="text-negative fw-semibold">-$350.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('ledger.vendor', $vendor->id ?? 1) }}" class="text-gold fw-semibold">
                            View All Transactions <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glassmorphism-card">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title text-gold">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-small-muted">
                    Are you sure you want to delete vendor <strong class="text-gold">{{ $vendor->name ?? 'John Smith' }}</strong>?
                    This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('vendors.destroy', $vendor->id ?? 1) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection