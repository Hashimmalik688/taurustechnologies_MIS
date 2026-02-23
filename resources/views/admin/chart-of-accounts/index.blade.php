@extends('layouts.master')

@section('title', 'Chart of Accounts')

@section('css')
<link href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<style>
    .coa-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .75rem; margin-bottom: .75rem;
    }
    .coa-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .coa-hdr h4 i { color: #d4af37; font-size: 1.25rem; }
    .coa-hdr p { margin: 2px 0 0; font-size: .72rem; color: var(--bs-surface-500); }
    .coa-chart-wrap { min-height: 220px; }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid rgba(0,0,0,.08); border-radius: 22px;
        padding: .3rem .65rem; font-size: .72rem; width: 200px;
    }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,.12); outline: none; }
    .dataTables_wrapper .dataTables_length label { font-size: .72rem; font-weight: 600; }
    .dataTables_wrapper .dataTables_length select { border: 1px solid rgba(0,0,0,.08); border-radius: .3rem; padding: .2rem 1.2rem .2rem .4rem; font-size: .72rem; }
    .dataTables_wrapper .dataTables_info { font-size: .68rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding: .25rem .5rem; font-size: .68rem; border-radius: .3rem; border: 1px solid rgba(0,0,0,.06); margin: 0 1px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: linear-gradient(135deg, #d4af37, #e8c84a) !important; color: #fff !important; border-color: #d4af37 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) { background: rgba(212,175,55,.08) !important; border-color: #d4af37 !important; color: #b89730 !important; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="coa-hdr">
        <div>
            <h4><i class="bx bx-book-open"></i> Chart of Accounts</h4>
            <p>Manage account codes, types, categories &amp; balances</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @canEditModule('chart-of-accounts')
            <a href="{{ route('chart-of-accounts.create') }}" class="act-btn a-primary"><i class="bx bx-plus"></i> Add Account</a>
            @endcanEditModule
        </div>
    </div>

    @php
        $totalAccounts = $accounts->count() ?? 0;
        $activeAccounts = is_countable($accounts) ? $accounts->where('is_active', true)->count() : 0;
        $inactiveAccounts = is_countable($accounts) ? $accounts->where('is_active', false)->count() : 0;
        $typeCounts = is_countable($accounts) ? $accounts->groupBy('account_type')->map->count() : collect();
    @endphp

    <div class="kpi-row">
        <div class="kpi-card k-gold"><span class="k-icon"><i class="bx bx-book-open"></i></span><span class="k-val">{{ $totalAccounts }}</span><span class="k-lbl">Total Accounts</span></div>
        <div class="kpi-card k-green"><span class="k-icon"><i class="bx bx-check-circle"></i></span><span class="k-val">{{ $activeAccounts }}</span><span class="k-lbl">Active</span></div>
        <div class="kpi-card k-red"><span class="k-icon"><i class="bx bx-x-circle"></i></span><span class="k-val">{{ $inactiveAccounts }}</span><span class="k-lbl">Inactive</span></div>
        @foreach($typeCounts->take(4) as $type => $count)
        <div class="kpi-card k-{{ ['blue','teal','purple','warn'][$loop->index] ?? 'gray' }}"><span class="k-icon"><i class="bx bx-folder"></i></span><span class="k-val">{{ $count }}</span><span class="k-lbl">{{ Str::limit($type, 12) }}</span></div>
        @endforeach
    </div>

    <div class="grid-2 mb-3">
        <div class="ex-card" style="padding: .75rem;">
            <div class="sec-hdr" style="border: none; padding-bottom: .35rem;"><h6><i class="bx bx-pie-chart-alt-2"></i> Accounts by Type</h6></div>
            <div id="accountTypeChart" class="coa-chart-wrap"></div>
        </div>
        <div class="ex-card" style="padding: .75rem;">
            <div class="sec-hdr" style="border: none; padding-bottom: .35rem;"><h6><i class="bx bx-bar-chart-alt-2"></i> Active vs Inactive</h6></div>
            <div id="accountStatusChart" class="coa-chart-wrap"></div>
        </div>
    </div>

    <div class="ex-card pipe-filter-bar">
        <span class="pipe-pill-lbl"><i class="bx bx-filter-alt"></i> Filters</span>
        <select id="filterType" class="pipe-pill crm-select">
            <option value="">All Types</option>
            @foreach($accountTypes as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
        <select id="filterStatus" class="pipe-pill crm-select">
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
        <button id="applyFilters" class="pipe-pill-apply"><i class="bx bx-check"></i> Apply</button>
    </div>

    <div class="ex-card sec-card">
        <div class="sec-hdr"><h6><i class="bx bx-table"></i> All Accounts</h6></div>
        <div class="sec-body">
            <div class="table-responsive">
                <table id="accountsTable" class="ex-tbl">
                    <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Type</th><th>Category</th><th>Parent</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('.crm-select').select2({minimumResultsForSearch:10,width:'style'});
    var table = $('#accountsTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: "{{ route('chart-of-accounts.index') }}", data: function(d) { d.account_type = $('#filterType').val(); d.is_active = $('#filterStatus').val(); } },
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
        order: [[1, 'asc']], pageLength: 25, responsive: false,
        language: { search: "_INPUT_", searchPlaceholder: "Search accounts...", lengthMenu: "Show _MENU_ per page", info: "Showing _START_ to _END_ of _TOTAL_", infoEmpty: "No accounts", infoFiltered: "(filtered from _MAX_)", processing: '<div class="spinner-border spinner-border-sm text-warning"></div>', emptyTable: "No accounts found" }
    });
    $('#applyFilters').on('click', function() { table.draw(); });
    $('#filterType, #filterStatus').on('change', function() { table.draw(); });

    @php $typeLabels = $typeCounts->keys()->toArray(); $typeValues = $typeCounts->values()->toArray(); @endphp
    new ApexCharts(document.querySelector("#accountTypeChart"), {
        series: @json($typeValues), labels: @json($typeLabels),
        chart: { type: 'donut', height: 210, fontFamily: 'inherit' },
        colors: ['#d4af37','#556ee6','#34c38f','#f46a6a','#50a5f1','#7c69ef','#f1b44c'],
        plotOptions: { pie: { donut: { size: '62%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '11px', fontWeight: 700, color: '#b89730' } } } } },
        legend: { position: 'bottom', fontSize: '10px', markers: { width: 8, height: 8, radius: 2 } }, dataLabels: { enabled: false }, stroke: { width: 1 }
    }).render();

    new ApexCharts(document.querySelector("#accountStatusChart"), {
        series: [{{ $activeAccounts }}, {{ $inactiveAccounts }}], labels: ['Active', 'Inactive'],
        chart: { type: 'donut', height: 210, fontFamily: 'inherit' },
        colors: ['#34c38f','#f46a6a'],
        plotOptions: { pie: { donut: { size: '62%', labels: { show: true, total: { show: true, label: 'Accounts', fontSize: '11px', fontWeight: 700, color: '#b89730' } } } } },
        legend: { position: 'bottom', fontSize: '10px', markers: { width: 8, height: 8, radius: 2 } }, dataLabels: { enabled: false }, stroke: { width: 1 }
    }).render();
});
</script>
@endsection
