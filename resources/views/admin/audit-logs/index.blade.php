@extends('layouts.master')

@section('title', 'All Activity Logs')

@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.sl-filter-assets')
<style>
/* ── Page header ── */
.page-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem}
.page-hdr h5{margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
.page-hdr h5 i{color:var(--bs-gold,#d4af37)}
.page-hdr .ph-sub{font-size:.72rem;color:var(--bs-surface-500);margin-left:.15rem}

/* ── Tab pills ── */
.tab-row{display:flex;gap:.35rem;margin-bottom:.65rem;flex-wrap:wrap}
.tab-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:20px;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid var(--bs-surface-200);color:var(--bs-surface-500);background:transparent;transition:all .15s;cursor:pointer}
.tab-pill:hover{border-color:rgba(212,175,55,.3);color:#b89730}
.tab-pill.active{background:linear-gradient(135deg,#d4af37,#c9a227);color:#fff;border-color:transparent;box-shadow:0 2px 8px rgba(212,175,55,.25)}
.tab-pill i{font-size:.85rem}

/* ── IP code ── */
.ip-code{font-family:'Fira Code','Consolas',monospace;font-size:.65rem;padding:.15rem .4rem;background:rgba(212,175,55,.06);border:1px solid rgba(212,175,55,.1);border-radius:6px;color:#b89730;letter-spacing:.3px}

/* ── Action pill ── */
.action-pill{display:inline-flex;align-items:center;gap:.2rem;padding:.15rem .45rem;border-radius:10px;font-size:.62rem;font-weight:600;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.12);color:#3b82f6}

/* ── DataTable override ── */
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:linear-gradient(135deg,#d4af37,#c9a227)!important;color:#fff!important;border:none!important;border-radius:6px!important;font-weight:600}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover{background:rgba(212,175,55,.08)!important;color:#b89730!important;border:1px solid rgba(212,175,55,.15)!important;border-radius:6px!important}
.dataTables_wrapper .dataTables_filter input{border-radius:12px!important;border:1px solid var(--bs-surface-200)!important;font-size:.72rem!important;padding:.35rem .6rem!important}
.dataTables_wrapper .dataTables_filter input:focus{border-color:#d4af37!important;box-shadow:0 0 0 3px rgba(212,175,55,.1)!important}
.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_length label{font-size:.68rem!important;color:var(--bs-surface-500)!important}
.dataTables_wrapper .dataTables_length select{border-radius:8px!important;font-size:.68rem!important;border:1px solid var(--bs-surface-200)!important}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-hdr">
    <h5><i class="bx bx-transfer-alt"></i> Account Switch Log <span class="ph-sub">All activity audit trail</span></h5>
    <a href="{{ route('admin.audit-logs.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="act-btn a-success" style="text-decoration:none">
        <i class="bx bx-download"></i> Export CSV
    </a>
</div>

<!-- Tab Pills -->
<div class="tab-row">
    <a href="{{ route('admin.account-switching-log') }}" class="tab-pill">
        <i class="bx bx-shield-x"></i> Suspicious Devices
    </a>
    <a href="{{ route('admin.audit-logs.index') }}" class="tab-pill active">
        <i class="bx bx-list-ul"></i> All Activity Logs
    </a>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('admin.audit-logs.index') }}">
    <div class="pipe-filter-bar" style="margin-bottom:.65rem">
        <select name="action" class="sl-pill-select" data-placeholder="All Actions">
            <option value="">All Actions</option>
            @foreach ($actions as $act)
                <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $act)) }}
                </option>
            @endforeach
        </select>

        <input type="text" name="date_from" class="sl-pill-date" placeholder="From date" value="{{ request('date_from') }}" autocomplete="off">
        <input type="text" name="date_to" class="sl-pill-date" placeholder="To date" value="{{ request('date_to') }}" autocomplete="off">

        <button type="submit" class="pipe-pill-apply"><i class="bx bx-filter-alt"></i> Apply</button>
        <a href="{{ route('admin.audit-logs.index') }}" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
    </div>
</form>

<!-- Audit Logs Table -->
<div class="ex-card sec-card">
    <div class="sec-hdr">
        <h6><i class="bx bx-list-check"></i> Activity Logs</h6>
        <span class="badge-count">{{ $auditLogs->total() }}</span>
    </div>
    <div class="sec-body" style="padding:.5rem">
        <div class="table-responsive">
            <table class="table ex-tbl mb-0" id="auditLogTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>IP Address</th>
                        <th>Date / Time</th>
                        <th style="width:60px">View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                    <tr>
                        <td style="font-size:.7rem;font-weight:700;color:var(--bs-surface-400)">#{{ $log->id }}</td>
                        <td>
                            @if ($log->user)
                                <span class="v-badge" style="font-size:.62rem">{{ $log->user->email }}</span>
                            @else
                                <span style="font-size:.68rem;color:var(--bs-surface-400)">{{ $log->user_email ?? 'System' }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="action-pill">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                        </td>
                        <td>
                            @if ($log->model)
                                <span style="font-size:.7rem;font-weight:600">{{ class_basename($log->model) }}</span>
                                @if ($log->model_id)
                                    <span style="font-size:.6rem;color:var(--bs-surface-400);margin-left:.15rem">#{{ $log->model_id }}</span>
                                @endif
                            @else
                                <span style="font-size:.68rem;color:var(--bs-surface-400)">—</span>
                            @endif
                        </td>
                        <td><span class="ip-code">{{ $log->ip_address ?? '—' }}</span></td>
                        <td style="font-size:.68rem;color:var(--bs-surface-500)">
                            <i class="bx bx-time" style="font-size:.72rem;vertical-align:middle;opacity:.5"></i>
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="act-btn a-primary" title="View Details">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2rem;color:var(--bs-surface-400)">
                            <i class="bx bx-search-alt" style="font-size:1.5rem;display:block;margin-bottom:.3rem;opacity:.3"></i>
                            <span style="font-size:.75rem">No audit logs found matching your filters</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($auditLogs->hasPages())
        <div style="display:flex;justify-content:center;padding:.65rem .5rem .35rem">
            {{ $auditLogs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function(){
    if($('#auditLogTable tbody tr').length > 1 || ($('#auditLogTable tbody tr').length === 1 && !$('#auditLogTable tbody tr td[colspan]').length)) {
        $('#auditLogTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            paging: false,
            info: false,
            language: {
                search: '',
                searchPlaceholder: 'Search logs...',
                emptyTable: 'No audit logs found'
            }
        });
    }
});
</script>
@endsection
