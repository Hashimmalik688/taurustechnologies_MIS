@extends('layouts.master')

@section('title')
    Submission Performance
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ── Page header ── */
.rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
.rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
.rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
.rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500,#64748b);margin-left:.25rem }

/* ── Filter card ── */
.sp-filter-card {
    background:var(--bs-card-bg,#fff);
    border:1px solid rgba(0,0,0,.06);
    border-radius:.65rem;
    padding:.85rem 1rem;
    margin-bottom:1rem;
    box-shadow:0 1px 4px rgba(0,0,0,.04);
}
.sp-filter-card .form-label {
    font-size:.68rem;font-weight:700;text-transform:uppercase;
    letter-spacing:.5px;color:var(--bs-surface-500,#64748b);margin-bottom:.3rem;
}
.sp-filter-card .form-control {
    font-size:.78rem;padding:.35rem .6rem;border-radius:.45rem;
    border:1px solid rgba(0,0,0,.1);background:var(--bs-input-bg,#fff);
    color:var(--bs-body-color,#212529);
}
.sp-filter-card .form-control:focus { border-color:var(--bs-gold,#d4af37);box-shadow:0 0 0 3px rgba(212,175,55,.15) }
.sp-filter-btn {
    display:inline-flex;align-items:center;gap:.35rem;
    font-size:.76rem;font-weight:700;padding:.38rem .85rem;
    border-radius:22px;transition:all .18s;cursor:pointer;
    border:none;
}
.sp-filter-btn-primary {
    background:linear-gradient(135deg,#d4af37,#b8941f);color:#0f172a;
}
.sp-filter-btn-primary:hover { transform:translateY(-1px);box-shadow:0 3px 10px rgba(212,175,55,.35) }
.sp-filter-btn-secondary {
    background:transparent;color:var(--bs-surface-500,#64748b);
    border:1px solid rgba(0,0,0,.1)!important;
}
.sp-filter-btn-secondary:hover { border-color:var(--bs-gold,#d4af37)!important;color:#92760d }

/* ── KPI summary strip ── */
.sp-kpi-strip {
    display:flex;gap:.65rem;margin-bottom:1rem;flex-wrap:wrap;
}
.sp-kpi-card {
    flex:1;min-width:140px;
    background:var(--bs-card-bg,#fff);
    border:1px solid rgba(0,0,0,.06);border-radius:.6rem;
    padding:.65rem .9rem;
    box-shadow:0 1px 4px rgba(0,0,0,.04);
    position:relative;overflow:hidden;
}
.sp-kpi-card::before {
    content:'';position:absolute;inset:0 auto 0 0;width:3px;
    background:linear-gradient(180deg,#d4af37,#b8941f);border-radius:3px 0 0 3px;
}
.sp-kpi-label { font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-surface-500,#64748b);margin-bottom:.2rem }
.sp-kpi-value { font-size:1.35rem;font-weight:800;color:var(--bs-surface-900,#1e293b);line-height:1;font-variant-numeric:tabular-nums }
.sp-kpi-sub { font-size:.62rem;color:var(--bs-surface-500,#64748b);margin-top:.15rem }

/* ── Results card ── */
.sp-results-card {
    background:var(--bs-card-bg,#fff);
    border:1px solid rgba(0,0,0,.06);border-radius:.65rem;
    box-shadow:0 1px 4px rgba(0,0,0,.04);overflow:hidden;
}
.sp-results-hdr {
    padding:.65rem .9rem;border-bottom:1px solid rgba(0,0,0,.06);
    display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.4rem;
}
.sp-results-hdr h6 { margin:0;font-size:.82rem;font-weight:700;display:flex;align-items:center;gap:.35rem }
.sp-results-hdr h6 i { color:var(--bs-gold,#d4af37) }
.sp-results-meta { font-size:.7rem;color:var(--bs-surface-500,#64748b) }

/* ── Table ── */
.sp-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.73rem }
.sp-table thead th {
    padding:.55rem .75rem;font-size:.63rem;font-weight:700;text-transform:uppercase;
    letter-spacing:.5px;color:var(--bs-surface-500,#64748b);
    background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
    white-space:nowrap;position:sticky;top:0;z-index:2;
}
.sp-table tbody td {
    padding:.55rem .75rem;border-bottom:1px solid rgba(0,0,0,.035);
    color:var(--bs-surface-900,#1e293b);vertical-align:middle;
}
.sp-table tbody tr:last-child td { border-bottom:none }
.sp-table tfoot td {
    padding:.6rem .75rem;border-top:2px solid rgba(0,0,0,.08);
    background:rgba(212,175,55,.04);font-weight:700;
    color:var(--bs-surface-900,#1e293b);
}
.sp-th-rank { width:48px;text-align:center }
.sp-th-carrier { min-width:180px }
.sp-th-num { text-align:right }
.sp-td-rank { text-align:center;font-size:.65rem;font-weight:700;color:var(--bs-surface-400,#94a3b8) }
.sp-td-num { text-align:right;font-weight:600;font-variant-numeric:tabular-nums }
.sp-td-total { text-align:right;font-weight:800;font-variant-numeric:tabular-nums }

/* ── Carrier link row ── */
.sp-carrier-link {
    display:inline-flex;align-items:center;gap:.4rem;
    color:var(--bs-surface-900,#1e293b);font-weight:600;
    text-decoration:none;transition:color .15s;
}
.sp-carrier-link:hover { color:var(--bs-gold-dark,#92760d) }
.sp-carrier-link .sp-carrier-icon {
    width:26px;height:26px;border-radius:50%;
    background:rgba(212,175,55,.1);display:inline-flex;align-items:center;justify-content:center;
    font-size:.7rem;flex-shrink:0;
    color:var(--bs-gold-dark,#92760d);
    transition:background .15s;
}
tbody tr:hover .sp-carrier-icon { background:rgba(212,175,55,.2) }
.sp-carrier-link .sp-carrier-name { flex:1 }
.sp-carrier-link .sp-arrow {
    opacity:0;font-size:.8rem;color:var(--bs-gold,#d4af37);transition:opacity .15s,transform .15s;
}
tbody tr:hover .sp-arrow { opacity:1;transform:translateX(2px) }

/* ── Row hover ── */
.sp-row-clickable { cursor:pointer }
.sp-row-clickable:hover td { background:rgba(212,175,55,.04) }

/* ── Rank medals ── */
.sp-rank-1 { color:#f4c430!important }
.sp-rank-2 { color:var(--bs-surface-400,#94a3b8)!important }
.sp-rank-3 { color:#cd7f32!important }
.sp-rank-badge {
    display:inline-flex;align-items:center;justify-content:center;
    width:22px;height:22px;border-radius:50%;font-size:.65rem;font-weight:800;
}
.sp-rank-badge-1 { background:rgba(244,196,48,.15) }
.sp-rank-badge-2 { background:rgba(148,163,184,.12) }
.sp-rank-badge-3 { background:rgba(205,127,50,.12) }

/* ── Premium chip ── */
.sp-premium-chip {
    display:inline-flex;align-items:center;gap:.25rem;
    background:rgba(52,195,143,.09);color:#1a8754;
    font-size:.66rem;font-weight:700;padding:.15rem .45rem;border-radius:20px;
}

/* ── Progress bar ── */
.sp-progress-wrap { display:flex;align-items:center;gap:.5rem }
.sp-progress { flex:1;height:5px;border-radius:10px;background:rgba(0,0,0,.06);overflow:hidden;min-width:60px }
.sp-progress-fill { height:100%;border-radius:10px;background:linear-gradient(90deg,#d4af37,#b8941f);transition:width .3s ease }

/* ── Empty state ── */
.sp-empty { text-align:center;padding:3.5rem 1rem;color:var(--bs-surface-500,#64748b) }
.sp-empty i { font-size:2.8rem;display:block;margin-bottom:.6rem;opacity:.2 }
.sp-empty h6 { font-size:.88rem;font-weight:700;margin-bottom:.3rem }
.sp-empty p { font-size:.73rem;max-width:280px;margin:0 auto }

/* ── Dark mode ── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sp-table thead th {
    background:rgba(15,23,42,.6);color:#94a3b8;border-bottom-color:rgba(255,255,255,.06);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sp-filter-card,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sp-results-card,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sp-kpi-card {
    background:rgba(15,23,42,.45);border-color:rgba(255,255,255,.06);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sp-table tfoot td {
    background:rgba(212,175,55,.06);border-top-color:rgba(255,255,255,.08);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sp-filter-card .form-control {
    background:rgba(15,23,42,.5);border-color:rgba(255,255,255,.1);color:#e2e8f0;
}
</style>
@endsection

@section('content')
<div style="max-width:900px;margin:0 auto">

    {{-- Page Header --}}
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-award"></i>
            Submission Performance
            <span class="rp-sub">Carrier-wise breakdown of approved sales sent to Pending Contract</span>
        </h5>
        <a href="{{ route('settings.reports.hub') }}" class="sp-filter-btn sp-filter-btn-secondary" style="font-size:.72rem;padding:.3rem .7rem;border-radius:20px;border:1px solid rgba(0,0,0,.1);text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;color:var(--bs-surface-500,#64748b)">
            <i class="bx bx-arrow-back"></i> Reports Hub
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="sp-filter-card">
        <form method="GET" action="{{ route('settings.reports.submission-performance') }}" id="sp-filter-form">
            <div class="row align-items-end g-2">
                <div class="col-12 col-sm-auto">
                    <label class="form-label" for="sp-date-from">Date From</label>
                    <input type="date" id="sp-date-from" name="date_from" class="form-control"
                           value="{{ $dateFrom }}" style="min-width:140px">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label" for="sp-date-to">Date To</label>
                    <input type="date" id="sp-date-to" name="date_to" class="form-control"
                           value="{{ $dateTo }}" style="min-width:140px">
                </div>
                <div class="col-12 col-sm-auto d-flex gap-2">
                    <button type="submit" class="sp-filter-btn sp-filter-btn-primary">
                        <i class="bx bx-search-alt"></i> Apply
                    </button>
                    <a href="{{ route('settings.reports.submission-performance') }}" class="sp-filter-btn sp-filter-btn-secondary">
                        <i class="bx bx-reset"></i> Reset
                    </a>
                </div>
                @if($dateFrom || $dateTo)
                <div class="col-12 col-sm" style="padding-bottom:.1rem">
                    <span style="font-size:.68rem;color:var(--bs-surface-500,#64748b)">
                        <i class="bx bx-calendar-check" style="color:var(--bs-gold,#d4af37)"></i>
                        Showing:
                        <strong>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : '—' }}</strong>
                        &rarr;
                        <strong>{{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : '—' }}</strong>
                    </span>
                </div>
                @endif
            </div>
        </form>
    </div>

    {{-- KPI Strip --}}
    <div class="sp-kpi-strip">
        <div class="sp-kpi-card">
            <div class="sp-kpi-label">Total Submissions</div>
            <div class="sp-kpi-value">{{ number_format($grandTotalSales) }}</div>
            <div class="sp-kpi-sub">Sent to Pending Contract</div>
        </div>
        <div class="sp-kpi-card">
            <div class="sp-kpi-label">Total Premium</div>
            <div class="sp-kpi-value">${{ number_format($grandTotalPremium, 2) }}</div>
            <div class="sp-kpi-sub">Monthly premium across all carriers</div>
        </div>
        <div class="sp-kpi-card">
            <div class="sp-kpi-label">Carriers</div>
            <div class="sp-kpi-value">{{ $carriersData->count() }}</div>
            <div class="sp-kpi-sub">Unique carriers with submissions</div>
        </div>
        @if($carriersData->count() > 0)
        <div class="sp-kpi-card">
            <div class="sp-kpi-label">Avg Premium / Sale</div>
            <div class="sp-kpi-value">${{ $grandTotalSales > 0 ? number_format($grandTotalPremium / $grandTotalSales, 2) : '0.00' }}</div>
            <div class="sp-kpi-sub">Across all carriers</div>
        </div>
        @endif
    </div>

    {{-- Results Card --}}
    <div class="sp-results-card">
        <div class="sp-results-hdr">
            <h6>
                <i class="bx bx-building"></i>
                Carrier Breakdown
            </h6>
            <span class="sp-results-meta">
                Click a carrier to view its leads in Pending Contract
            </span>
        </div>

        @if($carriersData->isEmpty())
            <div class="sp-empty">
                <i class="bx bx-bar-chart-alt-2"></i>
                <h6>No submissions found</h6>
                <p>No leads have been sent to Pending Contract for the selected date range.</p>
            </div>
        @else
        <div style="overflow-x:auto">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th class="sp-th-rank">#</th>
                        <th class="sp-th-carrier">Carrier</th>
                        <th class="sp-th-num" style="min-width:140px">Sales Distribution</th>
                        <th class="sp-th-num">Total Sales</th>
                        <th class="sp-th-num">Total Monthly Premium</th>
                        <th class="sp-th-num">Avg Premium / Sale</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carriersData as $idx => $row)
                    @php
                        $rank        = $idx + 1;
                        $pct         = $grandTotalSales > 0 ? round(($row->total_sales / $grandTotalSales) * 100, 1) : 0;
                        $avgPremium  = $row->total_sales > 0 ? $row->total_premium / $row->total_sales : 0;
                        $carrierName = $row->carrier_name ?: 'Unknown Carrier';

                        // Build link to pending contracts page with carrier + date filters
                        $pcParams = ['date_from' => $dateFrom, 'date_to' => $dateTo];
                        if ($row->insurance_carrier_id) {
                            $pcParams['carrier'] = $row->insurance_carrier_id;
                        }
                        $pcUrl = route('issuance.index', $pcParams);
                    @endphp
                    <tr class="sp-row-clickable" onclick="window.location='{{ $pcUrl }}'">
                        <td class="sp-td-rank">
                            @if($rank <= 3)
                                <span class="sp-rank-badge sp-rank-badge-{{ $rank }} sp-rank-{{ $rank }}">
                                    @if($rank === 1)<i class="bx bxs-crown"></i>@else{{ $rank }}@endif
                                </span>
                            @else
                                <span style="color:var(--bs-surface-400,#94a3b8);font-size:.68rem;font-weight:700">{{ $rank }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ $pcUrl }}" class="sp-carrier-link" onclick="event.stopPropagation()">
                                <span class="sp-carrier-icon">
                                    <i class="bx bx-building"></i>
                                </span>
                                <span class="sp-carrier-name">{{ $carrierName }}</span>
                                <i class="bx bx-right-arrow-alt sp-arrow"></i>
                            </a>
                        </td>
                        <td class="sp-td-num">
                            <div class="sp-progress-wrap">
                                <div class="sp-progress">
                                    <div class="sp-progress-fill" style="width:{{ $pct }}%"></div>
                                </div>
                                <span style="font-size:.65rem;color:var(--bs-surface-500,#64748b);min-width:34px;text-align:right">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="sp-td-num">
                            <span style="font-size:.85rem;font-weight:700;color:var(--bs-surface-900,#1e293b)">{{ number_format($row->total_sales) }}</span>
                        </td>
                        <td class="sp-td-num">
                            <span class="sp-premium-chip">
                                <i class="bx bx-dollar"></i>
                                {{ number_format($row->total_premium, 2) }}
                            </span>
                        </td>
                        <td class="sp-td-num" style="font-size:.72rem;color:var(--bs-surface-600,#475569)">
                            ${{ number_format($avgPremium, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td style="font-weight:800;font-size:.75rem">
                            <span style="display:inline-flex;align-items:center;gap:.3rem">
                                <i class="bx bx-sum" style="color:var(--bs-gold,#d4af37)"></i>
                                TOTAL
                            </span>
                        </td>
                        <td></td>
                        <td class="sp-td-total" style="font-size:.85rem">{{ number_format($grandTotalSales) }}</td>
                        <td class="sp-td-total">
                            <span class="sp-premium-chip" style="background:rgba(212,175,55,.12);color:#92760d">
                                <i class="bx bx-dollar"></i>
                                {{ number_format($grandTotalPremium, 2) }}
                            </span>
                        </td>
                        <td class="sp-td-total" style="font-size:.72rem">
                            ${{ $grandTotalSales > 0 ? number_format($grandTotalPremium / $grandTotalSales, 2) : '0.00' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    <div style="margin-top:.65rem;font-size:.65rem;color:var(--bs-surface-400,#94a3b8);text-align:center">
        <i class="bx bx-info-circle"></i>
        Data sourced from Pending Contract — all leads where the sale was approved and sent to contract, regardless of final issuance status.
    </div>

</div>
@endsection
