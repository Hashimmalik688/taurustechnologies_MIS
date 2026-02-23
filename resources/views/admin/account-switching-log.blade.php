@extends('layouts.master')

@section('title', 'Account Switch Log')

@section('css')
@include('partials.pipeline-dashboard-styles')
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

/* ── Fingerprint code ── */
.fp-code{font-family:'Fira Code','Consolas',monospace;font-size:.65rem;padding:.15rem .4rem;background:rgba(212,175,55,.06);border:1px solid rgba(212,175,55,.1);border-radius:6px;color:#b89730;letter-spacing:.3px;word-break:break-all}

/* ── Info banner ── */
.info-bar{display:flex;align-items:center;gap:.4rem;padding:.45rem .7rem;background:rgba(212,175,55,.04);border:1px solid rgba(212,175,55,.08);border-radius:.5rem;font-size:.7rem;color:var(--bs-surface-500);margin-bottom:.65rem}
.info-bar i{color:#d4af37;font-size:.85rem;flex-shrink:0}

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
    <h5><i class="bx bx-transfer-alt"></i> Account Switch Log <span class="ph-sub">Device activity monitoring</span></h5>
</div>

<!-- Tab Pills -->
<div class="tab-row">
    <a href="{{ route('admin.account-switching-log') }}" class="tab-pill active">
        <i class="bx bx-shield-x"></i> Suspicious Devices
    </a>
    <a href="{{ route('admin.audit-logs.index') }}" class="tab-pill">
        <i class="bx bx-list-ul"></i> All Activity Logs
    </a>
</div>

<!-- Info Bar -->
<div class="info-bar">
    <i class="bx bx-info-circle"></i>
    Devices used by multiple users to log in. This indicates account sharing or suspicious activity on the same PC.
</div>

<!-- Table -->
<div class="ex-card sec-card">
    <div class="sec-hdr">
        <h6><i class="bx bx-devices"></i> Suspicious Device Activity</h6>
        <span class="badge-count">{{ count($logs) }}</span>
    </div>
    <div class="sec-body" style="padding:.5rem">
        <div class="table-responsive">
            <table class="table ex-tbl mb-0" id="switchLogTable">
                <thead>
                    <tr>
                        <th>Device</th>
                        <th>Fingerprint</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>IP Address</th>
                        <th>Login Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:.35rem">
                                <i class="bx bx-desktop" style="font-size:.85rem;color:var(--bs-surface-400)"></i>
                                <span style="font-size:.72rem;font-weight:600">{{ $log->device_name ?: 'Unknown Device' }}</span>
                            </div>
                        </td>
                        <td><span class="fp-code">{{ $log->device_fingerprint }}</span></td>
                        <td>
                            @if($log->user)
                            <span class="v-badge" style="font-size:.65rem">{{ $log->user->name }}</span>
                            @else
                            <span style="font-size:.7rem;color:var(--bs-surface-400)">Unknown</span>
                            @endif
                        </td>
                        <td style="font-size:.7rem">{{ $log->user ? $log->user->email : $log->user_email }}</td>
                        <td><span class="s-pill" style="font-size:.62rem">{{ $log->ip_address }}</span></td>
                        <td style="font-size:.68rem;color:var(--bs-surface-500)">
                            <i class="bx bx-time" style="font-size:.72rem;vertical-align:middle;opacity:.5"></i>
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2rem;color:var(--bs-surface-400)">
                            <i class="bx bx-check-shield" style="font-size:1.5rem;display:block;margin-bottom:.3rem;opacity:.3"></i>
                            <span style="font-size:.75rem">No suspicious account switching detected</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function(){
    if($('#switchLogTable tbody tr').length > 1 || ($('#switchLogTable tbody tr').length === 1 && !$('#switchLogTable tbody tr td[colspan]').length)) {
        $('#switchLogTable').DataTable({
            order: [[5, 'desc']],
            pageLength: 25,
            language: {
                search: '',
                searchPlaceholder: 'Search logs...',
                emptyTable: 'No suspicious activity detected'
            }
        });
    }
});
</script>
@endsection
