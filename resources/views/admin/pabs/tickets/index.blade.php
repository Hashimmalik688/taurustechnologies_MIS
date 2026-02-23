@extends('layouts.master')

@section('title', 'PABS - Support Tickets')

@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<style>
    .tk-status{display:inline-block;padding:.22rem .7rem;border-radius:20px;font-size:.72rem;font-weight:600;letter-spacing:.3px}
    .tk-open{background:rgba(59,130,246,.12);color:#2563eb}
    .tk-in-progress,.tk-in_progress{background:rgba(139,92,246,.12);color:#7c3aed}
    .tk-on-hold,.tk-on_hold{background:rgba(245,158,11,.12);color:#d97706}
    .tk-resolved{background:rgba(16,185,129,.12);color:#059669}
    .tk-closed{background:rgba(107,114,128,.15);color:#6b7280}
    .pr-high{color:#ef4444;font-weight:600}
    .pr-medium{color:#f59e0b;font-weight:600}
    .pr-low{color:#10b981;font-weight:600}
    .sec-tag{display:inline-block;padding:.18rem .55rem;border-radius:14px;font-size:.7rem;background:rgba(99,102,241,.1);color:#6366f1;font-weight:500}
    .tk-code{font-weight:700;color:var(--bs-body-color);font-size:.82rem;letter-spacing:.4px}
    .tk-subject{color:var(--bs-body-color);text-decoration:none;font-weight:500;font-size:.82rem;transition:color .2s}
    .tk-subject:hover{color:#b8860b}
    .tk-meta{font-size:.72rem;color:#9ca3af}
    .app-badge{display:inline-block;padding:.18rem .55rem;border-radius:14px;font-size:.7rem;font-weight:600}
    .app-pending{background:rgba(245,158,11,.12);color:#d97706}
    .app-approved{background:rgba(16,185,129,.12);color:#059669}
    .app-rejected{background:rgba(239,68,68,.12);color:#ef4444}
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPI Row --}}
    <div class="kpi-row" style="grid-template-columns:repeat(6,1fr)">
        <div class="kpi-card k-blue">
            <div class="kpi-icon"><i class="bx bx-list-check"></i></div>
            <div class="kpi-label">Total Tickets</div>
            <div class="kpi-value">{{ $kpis['total_tickets'] }}</div>
        </div>
        <div class="kpi-card k-teal">
            <div class="kpi-icon"><i class="bx bx-folder-open"></i></div>
            <div class="kpi-label">Open</div>
            <div class="kpi-value">{{ $kpis['open_tickets'] }}</div>
        </div>
        <div class="kpi-card k-green">
            <div class="kpi-icon"><i class="bx bx-check-circle"></i></div>
            <div class="kpi-label">Closed</div>
            <div class="kpi-value">{{ $kpis['closed_tickets'] }}</div>
        </div>
        <div class="kpi-card k-red">
            <div class="kpi-icon"><i class="bx bx-error-circle"></i></div>
            <div class="kpi-label">High Priority</div>
            <div class="kpi-value">{{ $kpis['high_priority'] }}</div>
        </div>
        <div class="kpi-card k-warn">
            <div class="kpi-icon"><i class="bx bx-minus-circle"></i></div>
            <div class="kpi-label">Medium</div>
            <div class="kpi-value">{{ $kpis['medium_priority'] }}</div>
        </div>
        <div class="kpi-card k-gold">
            <div class="kpi-icon"><i class="bx bx-down-arrow-alt"></i></div>
            <div class="kpi-label">Low</div>
            <div class="kpi-value">{{ $kpis['low_priority'] }}</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" id="filterForm">
        <div class="pipe-filter-bar" style="margin-bottom:1.2rem">
            <select name="section_id" class="pipe-pill crm-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Sections</option>
                @foreach($sections as $id => $name)
                    <option value="{{ $id }}" {{ request('section_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="status" class="pipe-pill crm-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Status</option>
                <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>Open</option>
                <option value="IN PROGRESS" {{ request('status') == 'IN PROGRESS' ? 'selected' : '' }}>In Progress</option>
                <option value="ON HOLD" {{ request('status') == 'ON HOLD' ? 'selected' : '' }}>On Hold</option>
                <option value="RESOLVED" {{ request('status') == 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                <option value="CLOSED" {{ request('status') == 'CLOSED' ? 'selected' : '' }}>Closed</option>
            </select>
            <input type="text" name="search" class="pipe-pill" placeholder="🔍 Search code or subject…" value="{{ request('search') }}" style="min-width:200px">
            <button type="submit" class="act-btn a-primary" style="margin-left:auto"><i class="bx bx-filter-alt"></i> Filter</button>
            <a href="{{ route('pabs.tickets.create') }}" class="act-btn a-success"><i class="bx bx-plus"></i> New Ticket</a>
        </div>
    </form>

    {{-- Tickets Table --}}
    <div class="sec-card">
        <div class="sec-hdr"><i class="bx bx-support" style="color:#b8860b"></i> Support Tickets</div>
        <div class="sec-body" style="padding:0;overflow-x:auto">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th style="width:11%">Code</th>
                        <th style="width:22%">Subject</th>
                        <th style="width:10%">Section</th>
                        <th style="width:10%">Status</th>
                        <th style="width:9%">Approval</th>
                        <th style="width:9%">Priority</th>
                        <th style="width:12%">Created By</th>
                        <th style="width:10%">Assigned</th>
                        <th style="width:7%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td><span class="tk-code">{{ $ticket->ticket_code }}</span></td>
                            <td>
                                <a href="{{ route('pabs.tickets.show', $ticket) }}" class="tk-subject">
                                    {{ Str::limit($ticket->subject, 30) }}
                                </a>
                            </td>
                            <td><span class="sec-tag">{{ $sections[$ticket->section_id] ?? 'N/A' }}</span></td>
                            <td>
                                @php $slug = Str::slug($ticket->status) @endphp
                                <span class="tk-status tk-{{ $slug }}">{{ $ticket->status }}</span>
                            </td>
                            <td>
                                @php
                                    $apCls = match($ticket->approval_status) {
                                        'APPROVED' => 'app-approved',
                                        'REJECTED' => 'app-rejected',
                                        default    => 'app-pending',
                                    };
                                @endphp
                                <span class="app-badge {{ $apCls }}">{{ $ticket->approval_status }}</span>
                            </td>
                            <td><span class="pr-{{ Str::lower($ticket->priority) }}">{{ $ticket->priority }}</span></td>
                            <td><span class="tk-meta">{{ $ticket->creator->name }}</span></td>
                            <td>
                                @if($ticket->assignee)
                                    <span class="tk-meta">{{ $ticket->assignee->name }}</span>
                                @else
                                    <span class="tk-meta" style="opacity:.5">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pabs.tickets.show', $ticket) }}" class="act-btn a-primary" title="View">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:2.5rem;color:#9ca3af">
                                <i class="bx bx-inbox" style="font-size:2rem;display:block;margin-bottom:.4rem"></i>
                                No tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
        <div style="margin-top:1rem">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script>
$(function(){
    $('.crm-select').select2({minimumResultsForSearch:10,width:'style'}).on('change',function(){
        document.getElementById('filterForm').submit();
    });
});
</script>
@endsection
