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
    .cat-salary{background:linear-gradient(135deg,#f59e0b,#b45309)}.cat-other{background:linear-gradient(135deg,#94a3b8,#64748b)}
    .dataTables_wrapper .dataTables_filter input{border:1px solid rgba(0,0,0,.08);border-radius:22px;padding:.3rem .75rem;font-size:.75rem;width:200px}
    .dataTables_wrapper .dataTables_filter input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none}
    .dataTables_wrapper .dataTables_length select{border:1px solid rgba(0,0,0,.08);border-radius:.3rem;padding:.2rem 1.2rem .2rem .4rem;font-size:.72rem}
    .dataTables_wrapper .dataTables_info{font-size:.68rem}
    .dataTables_wrapper .dataTables_paginate .paginate_button{padding:.25rem .5rem;font-size:.68rem;border-radius:.3rem;border:1px solid rgba(0,0,0,.06);margin:0 1px}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{background:linear-gradient(135deg,#d4af37,#e8c84a) !important;color:#fff !important;border-color:#d4af37 !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current){background:rgba(212,175,55,.08) !important;border-color:#d4af37 !important;color:#b89730 !important}
    /* Journal revenue section */
    .section-divider{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-surface-400);margin:.85rem 0 .4rem;display:flex;align-items:center;gap:.4rem;}
    .section-divider::after{content:'';flex:1;height:1px;background:rgba(0,0,0,.06);}
    .journal-row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem;}
    .j-card{flex:1 1 90px;min-width:80px;padding:.6rem .65rem;border-radius:.55rem;text-align:center;border:1px solid rgba(255,255,255,.06);position:relative;overflow:hidden;}
    .j-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
    .j-card .j-val{font-size:1.2rem;font-weight:700;line-height:1;}
    .j-card .j-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem;}
    .j-card.j-sale{background:rgba(16,185,129,.06)} .j-card.j-sale::before{background:linear-gradient(90deg,#10b981,#6eddb8)} .j-card.j-sale .j-val{color:#059669}
    .j-card.j-cb{background:rgba(239,68,68,.06)} .j-card.j-cb::before{background:linear-gradient(90deg,#ef4444,#f87171)} .j-card.j-cb .j-val{color:#dc2626}
    .j-card.j-net{background:rgba(212,175,55,.06)} .j-card.j-net::before{background:linear-gradient(90deg,#d4af37,#e8c84a)} .j-card.j-net .j-val{color:#b89730}
    .j-card.j-month{background:rgba(85,110,230,.06)} .j-card.j-month::before{background:linear-gradient(90deg,#556ee6,#8b9cfa)} .j-card.j-month .j-val{color:#556ee6}
    .j-card.j-unposted{background:rgba(249,115,22,.07)} .j-card.j-unposted::before{background:linear-gradient(90deg,#f97316,#fb923c)} .j-card.j-unposted .j-val{color:#c2410c}
    /* Quick links */
    .ql-row{display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:.65rem;}
    .ql-btn{display:inline-flex;align-items:center;gap:.3rem;padding:.32rem .7rem;border-radius:.4rem;font-size:.7rem;font-weight:600;border:1px solid transparent;text-decoration:none;cursor:pointer;transition:all .15s;}
    /* Recent sales table */
    .recent-tbl{width:100%;font-size:.72rem;border-collapse:collapse;}
    .recent-tbl thead th{padding:.3rem .6rem;font-weight:600;font-size:.66rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);white-space:nowrap;border-bottom:1px solid rgba(0,0,0,.07);}
    .recent-tbl tbody td{padding:.38rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.04);}
    .recent-tbl tbody tr:last-child td{border-bottom:none;}
    .je-badge{display:inline-block;padding:.12rem .42rem;border-radius:.3rem;font-size:.6rem;font-weight:700;background:rgba(99,102,241,.12);color:#4338ca;border:1px solid rgba(99,102,241,.25);}
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
            <h4><i class="bx bx-book-open"></i> Ledger</h4>
            <p>Financial tracking — manual entries &amp; sales revenue from Paid Sales</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ledger.create') }}" class="act-btn a-primary"><i class="bx bx-plus"></i> Add Entry</a>
            <a href="{{ route('ledger.summary') }}" class="act-btn a-info"><i class="bx bx-bar-chart-alt-2"></i> Summary</a>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="ql-row">
        <a href="{{ route('admin.accounting.dashboard') }}" class="ql-btn" style="background:rgba(212,175,55,.1);color:#b89730;border-color:rgba(212,175,55,.25);">
            <i class="bx bx-line-chart"></i> Accounting Dashboard
        </a>
        <a href="{{ route('admin.accounting.sales-ledger') }}" class="ql-btn" style="background:rgba(16,185,129,.1);color:#059669;border-color:rgba(16,185,129,.25);">
            <i class="bx bx-list-check"></i> Sales Ledger
        </a>
        <a href="{{ route('admin.accounting.partner-ledger') }}" class="ql-btn" style="background:rgba(85,110,230,.1);color:#4338ca;border-color:rgba(85,110,230,.25);">
            <i class="bx bx-user-circle"></i> Partner Ledger
        </a>
        <a href="{{ route('paid-sales.index') }}" class="ql-btn" style="background:rgba(114,46,209,.1);color:#722ed1;border-color:rgba(114,46,209,.25);">
            <i class="bx bx-badge-check"></i> Paid Sales
            @if($unpostedPaidSales > 0)
                <span style="background:#c2410c;color:#fff;font-size:.55rem;padding:.1rem .35rem;border-radius:22px;font-weight:700;">{{ $unpostedPaidSales }} unposted</span>
            @endif
        </a>
        <a href="{{ route('admin.accounting.journal.index') }}" class="ql-btn" style="background:rgba(15,23,42,.08);color:var(--bs-body-color);border-color:rgba(0,0,0,.1);">
            <i class="bx bx-book-bookmark"></i> Journal
        </a>
    </div>

    {{-- ── Manual Ledger Entries KPIs ── --}}
    <div class="section-divider"><i class="bx bx-edit-alt"></i> Manual Ledger Entries</div>
    <div class="kpi-row">
        <div class="kpi-card k-green">
            <span class="k-icon"><i class="bx bx-trending-up"></i></span>
            <span class="k-val">${{ number_format($totalCredits, 2) }}</span>
            <span class="k-lbl">Credits</span>
        </div>
        <div class="kpi-card k-red">
            <span class="k-icon"><i class="bx bx-trending-down"></i></span>
            <span class="k-val">${{ number_format($totalDebits, 2) }}</span>
            <span class="k-lbl">Debits</span>
        </div>
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-wallet"></i></span>
            <span class="k-val" @if($netBalance < 0) style="color:#ef4444" @endif>
                ${{ number_format(abs($netBalance), 2) }}{{ $netBalance < 0 ? ' (DR)' : ' (CR)' }}
            </span>
            <span class="k-lbl">Net Balance</span>
        </div>
    </div>

    {{-- ── Sales Revenue from Accounting Journal ── --}}
    <div class="section-divider"><i class="bx bx-dollar-circle"></i> Sales Revenue — Accounting Journal</div>
    <div class="journal-row">
        <div class="j-card j-sale">
            <div class="j-val">${{ number_format($journalSalesTotal, 2) }}</div>
            <div class="j-lbl">Total Sales ({{ $journalSalesCount }})</div>
        </div>
        <div class="j-card j-cb">
            <div class="j-val">${{ number_format($journalChargebacksTotal, 2) }}</div>
            <div class="j-lbl">Chargebacks</div>
        </div>
        <div class="j-card j-net">
            <div class="j-val">${{ number_format($journalNetRevenue, 2) }}</div>
            <div class="j-lbl">Net Revenue</div>
        </div>
        <div class="j-card j-month">
            <div class="j-val">${{ number_format($journalSalesThisMonth, 2) }}</div>
            <div class="j-lbl">This Month</div>
        </div>
        @if($unpostedPaidSales > 0)
        <div class="j-card j-unposted">
            <div class="j-val">{{ $unpostedPaidSales }}</div>
            <div class="j-lbl">Unposted Sales</div>
        </div>
        @endif
    </div>

    {{-- Recent Journal Sales --}}
    @if($recentJournalSales->count() > 0)
    <div class="sec-card mb-3">
        <div class="sec-hdr" style="justify-content:space-between;">
            <h6 style="margin:0;font-size:.78rem;font-weight:600;"><i class="bx bx-transfer-alt me-1" style="color:#10b981"></i> Recent Sales Posted to Ledger</h6>
            <a href="{{ route('admin.accounting.journal.index') }}" style="font-size:.66rem;color:var(--bs-surface-400);text-decoration:none;">View All &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="recent-tbl">
                <thead>
                    <tr>
                        <th>Entry #</th>
                        <th>Date</th>
                        <th>Insured</th>
                        <th>Our Share</th>
                        <th>Gross Comm.</th>
                        <th>Posted By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentJournalSales as $je)
                    <tr>
                        <td><span class="je-badge">{{ $je->entry_number }}</span></td>
                        <td style="color:var(--bs-surface-500);">{{ $je->entry_date->format('M d, Y') }}</td>
                        <td>
                            {{ $je->insured_name ?? ($je->lead->cn_name ?? '—') }}
                            @if($je->lead_id)
                                <a href="{{ route('issuance.show', $je->lead_id) }}" style="font-size:.6rem;color:var(--bs-surface-400);display:block;text-decoration:none;">Lead #{{ $je->lead_id }}</a>
                            @endif
                        </td>
                        <td style="font-weight:700;color:#059669;">${{ number_format($je->total_debit, 2) }}</td>
                        <td style="color:var(--bs-surface-500);">{{ $je->gross_amount ? '$'.number_format($je->gross_amount, 2) : '—' }}</td>
                        <td style="font-size:.68rem;">{{ $je->creator->name ?? 'System' }}</td>
                        <td>
                            <a href="{{ route('admin.accounting.journal.show', $je->id) }}" class="act-btn a-info" style="padding:.15rem .4rem;font-size:.62rem;">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Manual Entries Table ── --}}
    <div class="section-divider"><i class="bx bx-list-ul"></i> All Manual Entries</div>

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
            <option value="bonus">Bonus</option>
            <option value="salary">Salary</option>
            <option value="adjustment">Adjustment</option>
        </select>
        <button id="applyFilters" class="act-btn a-primary" style="margin-left:auto"><i class="bx bx-filter-alt"></i> Filter</button>
    </div>

    {{-- Entries Table --}}
    <div class="sec-card">
        <div class="sec-hdr"><i class="bx bx-list-ul" style="color:#b8860b"></i> Manual Ledger Entries</div>
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
                            <th>Recorded By</th>
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
            { data: 'recorded_by', name: 'recorded_by', orderable: false, searchable: false },
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
