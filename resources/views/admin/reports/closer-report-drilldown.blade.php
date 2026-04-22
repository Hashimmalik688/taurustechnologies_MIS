@extends('layouts.master')

@section('title') {{ $closerName }} - Lead Details @endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        /* KPI cards */
        .cr-kpi-row { display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem }
        .cr-kpi { display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:92px;padding:.6rem .7rem;border-radius:.5rem;border:1px solid rgba(0,0,0,.06);background:var(--bs-card-bg);text-align:center;flex:1 }
        .cr-kpi-val { font-size:1.2rem;font-weight:800;line-height:1 }
        .cr-kpi-lbl { font-size:.6rem;font-weight:600;color:var(--bs-surface-400);margin-top:.2rem;white-space:nowrap }

        /* Filter bar */
        .cr-filter-bar { display:flex;flex-wrap:wrap;align-items:flex-end;gap:.5rem;margin-bottom:.7rem }
        .cr-filter-group { display:flex;flex-direction:column;gap:.15rem }
        .cr-filter-lbl { font-size:.62rem;font-weight:700;color:var(--bs-surface-500);text-transform:uppercase;letter-spacing:.4px }
        .cr-filter-ctrl { font-size:.72rem;padding:.3rem .55rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:var(--bs-card-bg);color:var(--bs-surface-700);min-width:140px }
        .cr-filter-ctrl:focus { outline:none;border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12) }

        /* Lead table */
        .lead-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.72rem }
        .lead-table thead th {
            padding:.5rem .6rem;font-size:.62rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.45px;color:var(--bs-surface-500,#64748b);
            background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
            white-space:nowrap;position:sticky;top:0;z-index:2;
        }
        .lead-table tbody td { padding:.5rem .6rem;border-bottom:1px solid rgba(0,0,0,.035);vertical-align:middle }
        .lead-table tbody tr:hover td { background:rgba(212,175,55,.04) }

        .lead-name { font-weight:700;color:#0369a1 }
        .lead-phone { color:var(--bs-surface-600);font-variant-numeric:tabular-nums }

        /* Status badges */
        .status-badge { font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:6px;white-space:nowrap;display:inline-block }
        .status-approved { background:rgba(5,150,105,.1);color:#059669 }
        .status-declined { background:rgba(220,38,38,.12);color:#dc2626 }
        .status-pending { background:rgba(245,158,11,.1);color:#d97706 }
        .status-paid { background:rgba(124,58,237,.1);color:#7c3aed }
        .status-chargeback { background:rgba(234,88,12,.1);color:#ea580c }

        /* Pagination */
        .pagination { display:flex;gap:.3rem;justify-content:center;margin-top:1rem;flex-wrap:wrap }
        .page-link { padding:.35rem .6rem;border:1px solid rgba(0,0,0,.1);border-radius:6px;background:var(--bs-card-bg);color:var(--bs-surface-700);text-decoration:none;font-size:.72rem }
        .page-link:hover { background:rgba(212,175,55,.08);border-color:#d4af37 }
        .page-link.active { background:#d4af37;color:#fff;border-color:#d4af37 }
        .page-link.disabled { opacity:.5;pointer-events:none }
    </style>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-user"></i> {{ $closerName }}
            <span class="rp-sub">Lead Details</span>
        </h5>
        <a href="{{ route('settings.reports.closer-report', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
           class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Back to Report
        </a>
    </div>

    {{-- KPI Row --}}
    <div class="cr-kpi-row">
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#0369a1">{{ $stats['sales_count'] }}</div>
            <div class="cr-kpi-lbl">Total Sales</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#059669">{{ $stats['approved_count'] }}</div>
            <div class="cr-kpi-lbl">Approved</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#dc2626">{{ $stats['declined_count'] }}</div>
            <div class="cr-kpi-lbl">Declined</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#7c3aed">{{ $stats['paid_count'] }}</div>
            <div class="cr-kpi-lbl">Paid</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#ea580c">{{ $stats['chargeback_count'] }}</div>
            <div class="cr-kpi-lbl">Chargebacks</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="ex-card sec-card" style="margin-bottom:.7rem">
        <div class="sec-body" style="padding:.75rem">
            <form method="GET" action="{{ route('settings.reports.closer-report.drilldown') }}" class="cr-filter-bar">
                <input type="hidden" name="closer_name" value="{{ $closerName }}">
                
                <div class="cr-filter-group">
                    <span class="cr-filter-lbl">Date From</span>
                    <input type="date" name="date_from" class="cr-filter-ctrl"
                           value="{{ $dateFrom ?? '' }}" style="min-width:140px">
                </div>
                <div class="cr-filter-group">
                    <span class="cr-filter-lbl">Date To</span>
                    <input type="date" name="date_to" class="cr-filter-ctrl"
                           value="{{ $dateTo ?? '' }}" style="min-width:140px">
                </div>
                <div class="cr-filter-group">
                    <span class="cr-filter-lbl">Status</span>
                    <select name="status" class="cr-filter-ctrl">
                        <option value="">All Sales</option>
                        <option value="sales" {{ $statusFilter === 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="declined" {{ $statusFilter === 'declined' ? 'selected' : '' }}>Declined</option>
                        <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="chargeback" {{ $statusFilter === 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                    </select>
                </div>
                <button type="submit" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem;margin-top:1.25rem">
                    <i class="bx bx-filter"></i> Filter
                </button>
                @if($dateFrom || $dateTo || $statusFilter)
                    <a href="{{ route('settings.reports.closer-report.drilldown', ['closer_name' => $closerName]) }}" 
                       class="act-btn a-secondary" style="font-size:.72rem;padding:.3rem .65rem;margin-top:1.25rem">
                        <i class="bx bx-x"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Leads Table --}}
    <div class="ex-card sec-card">
        <div class="sec-header" style="padding:.65rem .75rem">
            <h6 class="sec-title" style="font-size:.8rem">
                <i class="bx bx-list-ul"></i> Leads
                <span style="font-weight:400;color:var(--bs-surface-400);margin-left:.3rem">
                    ({{ $leads->total() }} total)
                </span>
            </h6>
        </div>
        <div class="sec-body" style="padding:0;overflow-x:auto">
            @if($leads->count() > 0)
                <table class="lead-table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Carrier</th>
                            <th>Sale Date</th>
                            <th>Premium</th>
                            <th>Submission</th>
                            <th>Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $lead)
                            <tr>
                                <td class="lead-name">{{ $lead->cn_name }}</td>
                                <td class="lead-phone">{{ $lead->phone_number }}</td>
                                <td>{{ $lead->carrier_name ?? '—' }}</td>
                                <td>{{ $lead->sale_date ? $lead->sale_date->format('M d, Y') : '—' }}</td>
                                <td>${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                <td>
                                    @if($lead->submission_status === 'approved')
                                        <span class="status-badge status-approved">Approved</span>
                                    @elseif($lead->submission_status === 'declined')
                                        <span class="status-badge status-declined">Declined</span>
                                    @else
                                        <span class="status-badge status-pending">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lead->paid_at)
                                        <span class="status-badge status-paid">Paid</span>
                                    @else
                                        <span style="color:var(--bs-surface-400)">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('leads.show', $lead->id) }}" class="act-btn a-primary" 
                                       style="font-size:.65rem;padding:.2rem .45rem" target="_blank">
                                        <i class="bx bx-show"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding:2rem;text-align:center;color:var(--bs-surface-400)">
                    <i class="bx bx-info-circle" style="font-size:2rem"></i>
                    <p style="margin-top:.5rem">No leads found matching the selected filters</p>
                </div>
            @endif
        </div>
        
        @if($leads->hasPages())
            <div class="sec-footer" style="padding:.75rem">
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($leads->onFirstPage())
                        <span class="page-link disabled">‹ Previous</span>
                    @else
                        <a href="{{ $leads->previousPageUrl() }}" class="page-link">‹ Previous</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($leads->getUrlRange(1, $leads->lastPage()) as $page => $url)
                        @if ($page == $leads->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($leads->hasMorePages())
                        <a href="{{ $leads->nextPageUrl() }}" class="page-link">Next ›</a>
                    @else
                        <span class="page-link disabled">Next ›</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection
