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
        .cr-kpi-row { display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:.75rem }
        .cr-kpi { 
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            min-width:100px;padding:.75rem .8rem;border-radius:.6rem;
            border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);
            text-align:center;flex:1;position:relative;overflow:hidden;
            transition:all .3s ease;
            box-shadow:0 1px 3px rgba(0,0,0,.05);
        }
        .cr-kpi::before {
            content:'';
            position:absolute;
            top:0;left:0;right:0;height:3px;
            background:linear-gradient(90deg, var(--kpi-color, #0369a1), var(--kpi-color-light, #0284c7));
        }
        .cr-kpi:hover { 
            transform:translateY(-2px);
            box-shadow:0 4px 12px rgba(0,0,0,.1);
            border-color:var(--kpi-color, #0369a1);
        }
        .cr-kpi-val { font-size:1.4rem;font-weight:800;line-height:1;text-shadow:0 1px 2px rgba(0,0,0,.05) }
        .cr-kpi-lbl { font-size:.62rem;font-weight:700;color:var(--bs-surface-500);margin-top:.25rem;white-space:nowrap;text-transform:uppercase;letter-spacing:.4px }

        /* Table */
        .cr-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.75rem }
        .cr-table thead th {
            padding:.5rem .6rem;font-size:.62rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.45px;color:#fff;
            background:linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-bottom:2px solid rgba(0,0,0,.1);
            white-space:nowrap;position:sticky;top:0;z-index:2;text-align:center;
            box-shadow:0 2px 4px rgba(0,0,0,.05);
        }
        .cr-table thead th:first-child { text-align:left;border-radius:.5rem 0 0 0 }
        .cr-table thead th:last-child { border-radius:0 .5rem 0 0 }
        .cr-table tbody td { 
            padding:.55rem .6rem;border-bottom:1px solid rgba(0,0,0,.04);
            vertical-align:middle;text-align:center;font-weight:600;
            font-variant-numeric:tabular-nums;transition:all .2s;
        }
        .cr-table tbody td:first-child { text-align:left }
        .cr-table tbody tr:hover td { 
            background:linear-gradient(90deg, rgba(212,175,55,.08) 0%, rgba(212,175,55,.04) 100%);
            transform:scale(1.001);
        }
        .cr-table tfoot td { padding:.6rem .6rem;border-top:2px solid rgba(0,0,0,.08);font-weight:800;text-align:center;font-variant-numeric:tabular-nums }
        .cr-table tfoot td:first-child { text-align:left }

        .cr-closer-name { 
            font-weight:700;
            background:linear-gradient(90deg, #0369a1, #0284c7);
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            background-clip:text;
            cursor:pointer;text-decoration:none;display:inline-block;
            transition:all .2s;
        }
        .cr-closer-name:hover { 
            background:linear-gradient(90deg, #d4af37, #f59e0b);
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            background-clip:text;
            transform:translateX(3px);
        }

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
        <div class="cr-kpi" style="--kpi-color:#0369a1;--kpi-color-light:#0284c7">
            <div class="cr-kpi-val" style="color:#0369a1">{{ $totals['sales_count'] }}</div>
            <div class="cr-kpi-lbl">Total Sales</div>
        </div>
        <div class="cr-kpi" style="--kpi-color:#059669;--kpi-color-light:#10b981">
            <div class="cr-kpi-val" style="color:#059669">{{ $totals['approved_count'] }}</div>
            <div class="cr-kpi-lbl">Approved</div>
        </div>
        <div class="cr-kpi" style="--kpi-color:#10b981;--kpi-color-light:#34d399">
            <div class="cr-kpi-val" style="color:#059669">{{ $totals['approved_percentage'] }}%</div>
            <div class="cr-kpi-lbl">Approved %</div>
        </div>
        <div class="cr-kpi" style="--kpi-color:#dc2626;--kpi-color-light:#ef4444">
            <div class="cr-kpi-val" style="color:#dc2626">{{ $totals['declined_count'] }}</div>
            <div class="cr-kpi-lbl">Declined</div>
        </div>
        <div class="cr-kpi" style="--kpi-color:#7c3aed;--kpi-color-light:#a78bfa">
            <div class="cr-kpi-val" style="color:#7c3aed">{{ $totals['paid_count'] }}</div>
            <div class="cr-kpi-lbl">Paid</div>
        </div>
        <div class="cr-kpi" style="--kpi-color:#8b5cf6;--kpi-color-light:#a78bfa">
            <div class="cr-kpi-val" style="color:#7c3aed">{{ $totals['paid_percentage'] }}%</div>
            <div class="cr-kpi-lbl">Paid %</div>
        </div>
        <div class="cr-kpi" style="--kpi-color:#ea580c;--kpi-color-light:#f97316">
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
                                <td><span style="display:inline-block;padding:.2rem .5rem;background:linear-gradient(135deg,#e0f2fe,#bae6fd);border-radius:.3rem;color:#0369a1;font-weight:700">{{ $stat['sales_count'] }}</span></td>
                                <td><span style="display:inline-block;padding:.2rem .5rem;background:linear-gradient(135deg,#d1fae5,#a7f3d0);border-radius:.3rem;color:#059669;font-weight:700">{{ $stat['approved_count'] }}</span></td>
                                <td><span style="display:inline-block;padding:.2rem .5rem;background:linear-gradient(135deg,#10b981,#059669);border-radius:.3rem;color:#fff;font-weight:800;box-shadow:0 2px 4px rgba(5,150,105,.2)">{{ $stat['approved_percentage'] }}%</span></td>
                                <td><span style="display:inline-block;padding:.2rem .5rem;background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:.3rem;color:#dc2626;font-weight:700">{{ $stat['declined_count'] }}</span></td>
                                <td><span style="display:inline-block;padding:.2rem .5rem;background:linear-gradient(135deg,#ede9fe,#ddd6fe);border-radius:.3rem;color:#7c3aed;font-weight:700">{{ $stat['paid_count'] }}</span></td>
                                <td><span style="display:inline-block;padding:.2rem .5rem;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:.3rem;color:#fff;font-weight:800;box-shadow:0 2px 4px rgba(124,58,237,.2)">{{ $stat['paid_percentage'] }}%</span></td>
                                <td><span style="display:inline-block;padding:.2rem .5rem;background:linear-gradient(135deg,#fed7aa,#fdba74);border-radius:.3rem;color:#ea580c;font-weight:700">{{ $stat['chargeback_count'] }}</span></td>
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
