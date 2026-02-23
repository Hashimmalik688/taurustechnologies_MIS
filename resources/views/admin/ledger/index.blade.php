@extends('layouts.master')
@section('title', 'Ledger Management')
@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .form-page-hdr p{margin:2px 0 0;font-size:.72rem;color:var(--bs-surface-500)}
    .cat-pill{display:inline-block;padding:.15rem .55rem;border-radius:22px;font-size:.65rem;font-weight:700;color:#fff}
    .cat-commission{background:linear-gradient(135deg,#7c3aed,#6d28d9)}.cat-payment{background:linear-gradient(135deg,#0ea5e9,#0284c7)}
    .cat-refund{background:linear-gradient(135deg,#f59e0b,#d97706)}.cat-expense{background:linear-gradient(135deg,#ef4444,#dc2626)}
    .cat-bonus{background:linear-gradient(135deg,#10b981,#059669)}.cat-adjustment{background:linear-gradient(135deg,#64748b,#475569)}
    .cat-other{background:linear-gradient(135deg,#94a3b8,#64748b)}
    .dataTables_wrapper .dataTables_filter input{border:1px solid rgba(0,0,0,.08);border-radius:22px;padding:.3rem .75rem;font-size:.75rem;width:200px}
    .dataTables_wrapper .dataTables_filter input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none}
    .dataTables_wrapper .dataTables_length select{border:1px solid rgba(0,0,0,.08);border-radius:.3rem;padding:.2rem 1.2rem .2rem .4rem;font-size:.72rem}
    .dataTables_wrapper .dataTables_info{font-size:.68rem}
    .dataTables_wrapper .dataTables_paginate .paginate_button{padding:.25rem .5rem;font-size:.68rem;border-radius:.3rem;border:1px solid rgba(0,0,0,.06);margin:0 1px}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{background:linear-gradient(135deg,#d4af37,#e8c84a) !important;color:#fff !important;border-color:#d4af37 !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current){background:rgba(212,175,55,.08) !important;border-color:#d4af37 !important;color:#b89730 !important}
</style>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.78rem;border-radius:12px">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-book-open"></i> Ledger Management</h4>
            <p>All financial transactions</p>
        </div>
        <a href="{{ route('ledger.create') }}" class="act-btn a-primary"><i class="bx bx-plus"></i> Add Entry</a>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-row">
        <div class="kpi-card k-green">
            <span class="k-icon"><i class="bx bx-trending-up"></i></span>
            <span class="k-val">${{ number_format($totalCredits, 2) }}</span>
            <span class="k-lbl">Total Credits</span>
        </div>
        <div class="kpi-card k-red">
            <span class="k-icon"><i class="bx bx-trending-down"></i></span>
            <span class="k-val">${{ number_format($totalDebits, 2) }}</span>
            <span class="k-lbl">Total Debits</span>
        </div>
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-wallet"></i></span>
            <span class="k-val">${{ number_format($netBalance, 2) }}</span>
            <span class="k-lbl">Net Balance</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="pipe-filter-bar mb-2">
        <div style="min-width:130px">
            <input type="text" class="pipe-pill crm-date" id="date_from" placeholder="From date" autocomplete="off" style="min-width:130px">
        </div>
        <div style="min-width:130px">
            <input type="text" class="pipe-pill crm-date" id="date_to" placeholder="To date" autocomplete="off" style="min-width:130px">
        </div>
        <select id="filterType" class="pipe-pill crm-select">
            <option value="">All Types</option>
            <option value="debit">Debit</option>
            <option value="credit">Credit</option>
        </select>
        <select id="filterCategory" class="pipe-pill crm-select">
            <option value="">All Categories</option>
            <option value="commission">Commission</option>
            <option value="payment">Payment</option>
            <option value="refund">Refund</option>
            <option value="expense">Expense</option>
        </select>
        <button id="applyFilters" class="act-btn a-primary" style="margin-left:auto"><i class="bx bx-filter-alt"></i> Filter</button>
    </div>

    {{-- Entries Table --}}
    <div class="sec-card">
        <div class="sec-hdr"><i class="bx bx-list-ul" style="color:#b8860b"></i> All Ledger Entries</div>
        <div class="sec-body" style="padding:0">
            <div class="table-responsive">
                <table id="ledgerTable" class="ex-tbl w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th style="width:70px">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(function(){
    $('.crm-select').select2({minimumResultsForSearch:10,width:'style'});
    $('.crm-date').datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,clearBtn:true});
    var table = $('#ledgerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('ledger.index') }}",
            data: function(d) {
                d.type = $('#filterType').val();
                d.category = $('#filterCategory').val();
                d.start_date = $('#date_from').val();
                d.end_date = $('#date_to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'formatted_date', name: 'transaction_date' },
            { data: 'type_badge', name: 'type' },
            { data: 'category_badge', name: 'category' },
            { data: 'formatted_amount', name: 'amount' },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search entries...",
            lengthMenu: "Show _MENU_ per page",
            info: "Showing _START_ to _END_ of _TOTAL_",
            infoEmpty: "No entries",
            processing: '<div class="spinner-border spinner-border-sm text-warning"></div>',
            emptyTable: "No ledger entries found"
        }
    });

    $('#applyFilters').on('click', function(){ table.draw(); });
    $('#filterType, #filterCategory').on('change', function(){ table.draw(); });
    $('#date_from, #date_to').on('changeDate', function(){ table.draw(); });
});
</script>
@endsection
