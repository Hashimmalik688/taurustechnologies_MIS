@extends('layouts.master')

@section('title') Closer Performance Report @endsection

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
        .cr-kpi-val { font-size:1.3rem;font-weight:800;line-height:1 }
        .cr-kpi-lbl { font-size:.6rem;font-weight:600;color:var(--bs-surface-400);margin-top:.2rem;white-space:nowrap }

        /* Table */
        .cr-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.75rem }
        .cr-table thead th {
            padding:.5rem .6rem;font-size:.62rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.45px;color:var(--bs-surface-500,#64748b);
            background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
            white-space:nowrap;position:sticky;top:0;z-index:2;text-align:center;
        }
        .cr-table thead th:first-child { text-align:left }
        .cr-table tbody td { padding:.5rem .6rem;border-bottom:1px solid rgba(0,0,0,.035);vertical-align:middle;text-align:center;font-weight:600;font-variant-numeric:tabular-nums }
        .cr-table tbody td:first-child { text-align:left }
        .cr-table tbody tr:hover td { background:rgba(212,175,55,.04) }
        .cr-table tfoot td { padding:.6rem .6rem;border-top:2px solid rgba(0,0,0,.08);font-weight:800;text-align:center;font-variant-numeric:tabular-nums }
        .cr-table tfoot td:first-child { text-align:left }

        .cr-closer-name { font-weight:700;color:#0369a1;cursor:pointer;text-decoration:none;display:inline-block }
        .cr-closer-name:hover { color:#0284c7;text-decoration:underline }

        /* Filter bar */
        .cr-filter-bar { display:flex;flex-wrap:wrap;align-items:flex-end;gap:.5rem;margin-bottom:.7rem }
        .cr-filter-group { display:flex;flex-direction:column;gap:.15rem }
        .cr-filter-lbl { font-size:.62rem;font-weight:700;color:var(--bs-surface-500);text-transform:uppercase;letter-spacing:.4px }
        .cr-filter-ctrl { font-size:.72rem;padding:.3rem .55rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:var(--bs-card-bg);color:var(--bs-surface-700);min-width:140px }
        .cr-filter-ctrl:focus { outline:none;border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12) }

        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .cr-table thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;border-bottom-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .cr-table tbody td {
            color:#e2e8f0;border-bottom-color:rgba(255,255,255,.04);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .cr-table tfoot td {
            color:#e2e8f0;border-top-color:rgba(255,255,255,.1);
        }
    </style>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-user-check"></i> Closer Performance Report
            <span class="rp-sub">Sales • Approved • Declined • Paid • Chargeback</span>
        </h5>
        <a href="{{ route('settings.reports.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>

    {{-- Filters --}}
    <div class="ex-card sec-card" style="margin-bottom:.7rem">
        <div class="sec-body" style="padding:.75rem">
            <form method="GET" action="{{ route('settings.reports.closer-report') }}" class="cr-filter-bar">
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
                <button type="submit" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem;margin-top:1.25rem">
                    <i class="bx bx-filter"></i> Filter
                </button>
                @if($dateFrom || $dateTo)
                    <a href="{{ route('settings.reports.closer-report') }}" class="act-btn a-secondary" style="font-size:.72rem;padding:.3rem .65rem;margin-top:1.25rem">
                        <i class="bx bx-x"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="cr-kpi-row">
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#0369a1">{{ $totals['sales_count'] }}</div>
            <div class="cr-kpi-lbl">Total Sales</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#059669">{{ $totals['approved_count'] }}</div>
            <div class="cr-kpi-lbl">Approved</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#dc2626">{{ $totals['declined_count'] }}</div>
            <div class="cr-kpi-lbl">Declined</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#7c3aed">{{ $totals['paid_count'] }}</div>
            <div class="cr-kpi-lbl">Paid</div>
        </div>
        <div class="cr-kpi">
            <div class="cr-kpi-val" style="color:#ea580c">{{ $totals['chargeback_count'] }}</div>
            <div class="cr-kpi-lbl">Chargebacks</div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="ex-card sec-card">
        <div class="sec-header" style="padding:.65rem .75rem">
            <h6 class="sec-title" style="font-size:.8rem">
                <i class="bx bx-table"></i> Performance by Closer
                <span style="font-weight:400;color:var(--bs-surface-400);margin-left:.3rem">
                    ({{ $closerStats->count() }} closers)
                </span>
            </h6>
        </div>
        <div class="sec-body" style="padding:0;overflow-x:auto">
            @if($closerStats->count() > 0)
                <table class="cr-table">
                    <thead>
                        <tr>
                            <th>Closer Name</th>
                            <th>Sales</th>
                            <th>Approved</th>
                            <th>Approved %</th>
                            <th>Declined</th>
                            <th>Paid</th>
                            <th>Paid %</th>
                            <th>Chargeback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($closerStats as $stat)
                            <tr>
                                <td>
                                    <a href="{{ route('settings.reports.closer-report.drilldown', [
                                        'closer_name' => $stat['closer_name'],
                                        'date_from' => $dateFrom,
                                        'date_to' => $dateTo
                                    ]) }}" class="cr-closer-name">
                                        {{ $stat['closer_name'] }}
                                    </a>
                                </td>
                                <td>{{ $stat['sales_count'] }}</td>
                                <td style="color:#059669">{{ $stat['approved_count'] }}</td>
                                <td style="color:#059669;font-weight:700">{{ $stat['approved_percentage'] }}%</td>
                                <td style="color:#dc2626">{{ $stat['declined_count'] }}</td>
                                <td style="color:#7c3aed">{{ $stat['paid_count'] }}</td>
                                <td style="color:#7c3aed;font-weight:700">{{ $stat['paid_percentage'] }}%</td>
                                <td style="color:#ea580c">{{ $stat['chargeback_count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>{{ $totals['sales_count'] }}</td>
                            <td style="color:#059669">{{ $totals['approved_count'] }}</td>
                            <td style="color:#059669;font-weight:800">{{ $totals['approved_percentage'] }}%</td>
                            <td style="color:#dc2626">{{ $totals['declined_count'] }}</td>
                            <td style="color:#7c3aed">{{ $totals['paid_count'] }}</td>
                            <td style="color:#7c3aed;font-weight:800">{{ $totals['paid_percentage'] }}%</td>
                            <td style="color:#ea580c">{{ $totals['chargeback_count'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div style="padding:2rem;text-align:center;color:var(--bs-surface-400)">
                    <i class="bx bx-info-circle" style="font-size:2rem"></i>
                    <p style="margin-top:.5rem">No sales found for the selected date range</p>
                </div>
            @endif
        </div>
    </div>

@endsection
