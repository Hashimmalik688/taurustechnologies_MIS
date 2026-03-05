@extends('layouts.master')

@section('title', 'Agent Performance — Zoom Calls')

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ── Page header ── */
.page-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem}
.page-hdr h5{margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
.page-hdr h5 i{color:var(--bs-gold,#d4af37)}
.page-hdr .ph-sub{font-size:.72rem;color:var(--bs-surface-500);margin-left:.15rem}

/* ── Tab Pills ── */
.tab-row{display:flex;gap:.35rem;margin-bottom:.65rem;flex-wrap:wrap}
.tab-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:20px;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid var(--bs-surface-200,#e2e8f0);color:var(--bs-surface-500,#64748b);background:transparent;transition:all .15s}
.tab-pill:hover{border-color:rgba(212,175,55,.3);color:#b89730}
.tab-pill.active{background:linear-gradient(135deg,#d4af37,#c9a227);color:#fff;border-color:transparent;box-shadow:0 2px 8px rgba(212,175,55,.25)}
.tab-pill i{font-size:.85rem}

/* ── Summary Cards ── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.65rem;margin-bottom:.65rem}
.stat-card{background:#fff;padding:.75rem 1rem;border-radius:.55rem;border:1px solid rgba(0,0,0,.06);box-shadow:0 1px 3px rgba(0,0,0,.03)}
.stat-card-label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-surface-500);margin-bottom:.25rem}
.stat-card-value{font-size:1.5rem;font-weight:700;color:var(--bs-surface-900);font-variant-numeric:tabular-nums}
.stat-card-icon{float:right;font-size:1.8rem;opacity:.15;margin-top:-.2rem}

/* ── Agent Table ── */
.ap-table{width:100%;border-collapse:separate;border-spacing:0;font-size:.73rem}
.ap-table thead th{
    padding:.55rem .65rem;font-size:.65rem;font-weight:700;text-transform:uppercase;
    letter-spacing:.5px;color:var(--bs-surface-500,#64748b);
    background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
    white-space:nowrap;position:sticky;top:0;z-index:2;
}
.ap-table thead th[data-sort]{cursor:pointer;user-select:none}
.ap-table thead th[data-sort]:hover{color:var(--bs-gold,#d4af37)}
.ap-table thead th .si{font-size:.55rem;opacity:.4;margin-left:.15rem}
.ap-table thead th.s-asc .si::after{content:'▲';opacity:1}
.ap-table thead th.s-desc .si::after{content:'▼';opacity:1}
.ap-table thead th:not(.s-asc):not(.s-desc) .si::after{content:'⇅'}
.ap-table tbody td{padding:.5rem .65rem;border-bottom:1px solid rgba(0,0,0,.035);color:var(--bs-surface-900,#1e293b);vertical-align:middle}
.ap-table tbody tr:hover td{background:rgba(212,175,55,.04)}
.ap-table tbody tr:last-child td{border-bottom:none}
.ap-num{text-align:right;font-variant-numeric:tabular-nums}
.ap-table tfoot td{padding:.6rem .65rem;font-size:.73rem;border-top:2px solid rgba(0,0,0,.09);background:rgba(212,175,55,.04);font-weight:700}

/* ── Rate bar ── */
.rate-bar{display:inline-block;width:44px;height:5px;background:rgba(0,0,0,.08);border-radius:3px;margin-left:5px;vertical-align:middle}
.rate-fill{height:100%;border-radius:3px;background:var(--bs-gold,#d4af37);display:block}

/* ── Date filter ── */
.df-row{display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap;padding:.7rem;border-bottom:1px solid rgba(0,0,0,.06)}
.df-row label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);display:block;margin-bottom:.2rem}
.df-row input[type=date]{font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff}

/* ── Empty state ── */
.ap-empty{text-align:center;padding:3rem 1rem;color:var(--bs-surface-500)}
.ap-empty i{font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25}
.ap-empty h6{font-size:.85rem;font-weight:700;margin-bottom:.25rem}
.ap-empty p{font-size:.72rem}

/* ── Dark themes ── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card{
    background:rgba(15,23,42,.6);border-color:rgba(255,255,255,.06);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card-label,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table thead th{
    color:#94a3b8;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .stat-card-value,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table tbody td{
    color:#e2e8f0;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table thead th{
    background:rgba(15,23,42,.6);border-bottom-color:rgba(255,255,255,.06);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table tbody td{
    border-bottom-color:rgba(255,255,255,.04);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .ap-table tfoot td{
    border-top-color:rgba(255,255,255,.1);color:#e2e8f0;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .df-row input[type=date]{
    background:rgba(15,23,42,.6);color:#e2e8f0;border-color:rgba(255,255,255,.1);
}
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="page-hdr">
    <h5>
        <i class="bx bx-video"></i> Zoom Call Logs
        <span class="ph-sub">Agent Performance</span>
    </h5>
    <div style="display:flex;gap:.5rem">
        <a href="{{ route('settings.reports.zoom-diagnostics') }}" class="act-btn a-warn" style="font-size:.72rem;padding:.3rem .65rem" title="Diagnostics">
            <i class="bx bx-pulse"></i> Diagnostics
        </a>
        <a href="{{ route('settings.reports.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>
</div>

{{-- Tab Pills --}}
<div class="tab-row">
    <a href="{{ route('settings.reports.zoom-logs') }}" class="tab-pill">
        <i class="bx bx-list-ul"></i> Call Logs
    </a>
    <a href="{{ route('settings.reports.zoom-agent-performance') }}" class="tab-pill active">
        <i class="bx bx-bar-chart-alt-2"></i> Agent Performance
    </a>
</div>

{{-- Summary KPI Cards --}}
@php
    $totalSecs = $summaryTotalDuration;
    $sH = floor($totalSecs / 3600);
    $sM = floor(($totalSecs % 3600) / 60);
    $sS = $totalSecs % 60;
    $totalDurFmt = sprintf('%02d:%02d:%02d', $sH, $sM, $sS);
    $connectRate = $summaryTotalCalls > 0 ? round(($summaryAnswered / $summaryTotalCalls) * 100, 1) : 0;
@endphp
<div class="stats-grid">
    <div class="stat-card">
        <i class="bx bx-phone-outgoing stat-card-icon"></i>
        <div class="stat-card-label">Outbound Calls</div>
        <div class="stat-card-value">{{ number_format($summaryTotalCalls) }}</div>
    </div>
    <div class="stat-card">
        <i class="bx bx-time stat-card-icon"></i>
        <div class="stat-card-label">Total Talk Time</div>
        <div class="stat-card-value" style="font-size:1.3rem">{{ $totalDurFmt }}</div>
    </div>
    <div class="stat-card">
        <i class="bx bx-phone-call stat-card-icon"></i>
        <div class="stat-card-label">Answered</div>
        <div class="stat-card-value">{{ number_format($summaryAnswered) }}</div>
    </div>
    <div class="stat-card">
        <i class="bx bx-user-check stat-card-icon"></i>
        <div class="stat-card-label">Connect Rate</div>
        <div class="stat-card-value">{{ $connectRate }}%</div>
    </div>
    <div class="stat-card">
        <i class="bx bx-group stat-card-icon"></i>
        <div class="stat-card-label">Agents Tracked</div>
        <div class="stat-card-value">{{ $agentKpis->count() }}</div>
    </div>
</div>

{{-- Date filter + Table --}}
<div class="ex-card sec-card">

    {{-- Date Filter --}}
    <form method="GET" action="{{ route('settings.reports.zoom-agent-performance') }}" id="apFilterForm">
        <div class="df-row">
            <div>
                <label>From (PT)</label>
                <input type="date" name="date_from" id="apDateFrom" value="{{ $dateFrom ?? '' }}">
            </div>
            <div>
                <label>To (PT)</label>
                <input type="date" name="date_to" id="apDateTo" value="{{ $dateTo ?? '' }}">
            </div>
            <button type="button" class="pipe-pill" style="font-size:.72rem;padding:.3rem .75rem;border:none;cursor:pointer" onclick="setToday()">
                <i class="bx bx-calendar-check" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Today (PT)
            </button>
            <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.3rem .75rem">
                <i class="bx bx-filter-alt" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Apply
            </button>
            <a href="{{ route('settings.reports.zoom-agent-performance') }}" class="pipe-pill" style="font-size:.72rem;padding:.3rem .75rem;text-decoration:none">
                <i class="bx bx-x" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Clear
            </a>
            @if($dateFrom || $dateTo)
            <span style="font-size:.65rem;color:var(--bs-surface-400);align-self:center">
                Showing {{ ($dateFrom && $dateTo && $dateFrom !== $dateTo) ? $dateFrom . ' → ' . $dateTo : ($dateFrom ?: $dateTo) }} (PT)
            </span>
            @endif
        </div>
    </form>

    @if($agentKpis->count() > 0)

    {{-- Table --}}
    <div style="overflow-x:auto">
        <table class="ap-table" id="apTable">
            <thead>
                <tr>
                    <th data-sort="name">Agent <span class="si"></span></th>
                    <th class="ap-num" data-sort="total_calls">Total <span class="si"></span></th>
                    <th class="ap-num" data-sort="answered">Answered <span class="si"></span></th>
                    <th class="ap-num" data-sort="missed">No Answer <span class="si"></span></th>
                    <th class="ap-num" data-sort="declined">Declined <span class="si"></span></th>
                    <th class="ap-num" data-sort="voicemail">Voicemail <span class="si"></span></th>
                    <th class="ap-num" data-sort="recorded">Recorded <span class="si"></span></th>
                    <th class="ap-num" data-sort="total_duration">Talk Time <span class="si"></span></th>
                    <th class="ap-num" data-sort="avg_duration">Avg Duration <span class="si"></span></th>
                    <th class="ap-num" data-sort="connect_rate">Connect Rate <span class="si"></span></th>
                </tr>
            </thead>
            <tbody id="apBody">
                @foreach($agentKpis as $agent)
                @php
                    $cr      = $agent['total_calls'] > 0 ? round(($agent['answered'] / $agent['total_calls']) * 100, 1) : 0;
                    $avgSec  = $agent['total_calls'] > 0 ? round($agent['total_duration'] / $agent['total_calls']) : 0;
                    $tH = floor($agent['total_duration'] / 3600);
                    $tM = floor(($agent['total_duration'] % 3600) / 60);
                    $tS = $agent['total_duration'] % 60;
                    $talkFmt = $tH > 0 ? sprintf('%d:%02d:%02d', $tH, $tM, $tS) : sprintf('%02d:%02d', $tM, $tS);
                    $aM = floor($avgSec / 60); $aS = $avgSec % 60;
                    $avgFmt  = sprintf('%02d:%02d', $aM, $aS);
                @endphp
                <tr
                    data-name="{{ $agent['name'] }}"
                    data-total_calls="{{ $agent['total_calls'] }}"
                    data-answered="{{ $agent['answered'] }}"
                    data-missed="{{ $agent['missed'] }}"
                    data-declined="{{ $agent['declined'] }}"
                    data-voicemail="{{ $agent['voicemail'] }}"
                    data-recorded="{{ $agent['recorded'] }}"
                    data-total_duration="{{ $agent['total_duration'] }}"
                    data-avg_duration="{{ $avgSec }}"
                    data-connect_rate="{{ $cr }}"
                >
                    <td>
                        <div style="font-weight:600">{{ $agent['name'] }}</div>
                        <div style="font-size:.6rem;color:var(--bs-surface-500)">Ext. {{ $agent['extension'] }}</div>
                    </td>
                    <td class="ap-num"><strong>{{ number_format($agent['total_calls']) }}</strong></td>
                    <td class="ap-num"><span style="color:#1a8754;font-weight:600">{{ number_format($agent['answered']) }}</span></td>
                    <td class="ap-num"><span style="color:#b87a14">{{ number_format($agent['missed']) }}</span></td>
                    <td class="ap-num"><span style="color:#c84646">{{ number_format($agent['declined']) }}</span></td>
                    <td class="ap-num"><span style="color:#6c757d">{{ number_format($agent['voicemail']) }}</span></td>
                    <td class="ap-num"><span style="color:var(--bs-gold,#d4af37)">{{ number_format($agent['recorded']) }}</span></td>
                    <td class="ap-num" style="white-space:nowrap;font-family:monospace;font-size:.7rem">{{ $talkFmt }}</td>
                    <td class="ap-num" style="white-space:nowrap;font-family:monospace;font-size:.7rem">{{ $avgFmt }}</td>
                    <td class="ap-num" style="white-space:nowrap">
                        <strong>{{ $cr }}%</strong>
                        <span class="rate-bar"><span class="rate-fill" style="width:{{ min($cr, 100) }}%"></span></span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            @if($agentKpis->count() > 1)
            @php
                $tot = [
                    'total_calls'    => $agentKpis->sum('total_calls'),
                    'answered'       => $agentKpis->sum('answered'),
                    'missed'         => $agentKpis->sum('missed'),
                    'declined'       => $agentKpis->sum('declined'),
                    'voicemail'      => $agentKpis->sum('voicemail'),
                    'recorded'       => $agentKpis->sum('recorded'),
                    'total_duration' => $agentKpis->sum('total_duration'),
                ];
                $totCR  = $tot['total_calls'] > 0 ? round(($tot['answered'] / $tot['total_calls']) * 100, 1) : 0;
                $totAvg = $tot['total_calls'] > 0 ? round($tot['total_duration'] / $tot['total_calls']) : 0;
                $fH = floor($tot['total_duration'] / 3600);
                $fM = floor(($tot['total_duration'] % 3600) / 60);
                $fS = $tot['total_duration'] % 60;
                $totTalk = $fH > 0 ? sprintf('%d:%02d:%02d', $fH, $fM, $fS) : sprintf('%02d:%02d', $fM, $fS);
                $taM = floor($totAvg / 60); $taS = $totAvg % 60;
                $totAvgFmt = sprintf('%02d:%02d', $taM, $taS);
            @endphp
            <tfoot>
                <tr>
                    <td>
                        TOTAL
                        <span style="font-size:.6rem;font-weight:400;color:var(--bs-surface-500)">({{ $agentKpis->count() }} agents)</span>
                    </td>
                    <td class="ap-num">{{ number_format($tot['total_calls']) }}</td>

                    <td class="ap-num"><span style="color:#1a8754">{{ number_format($tot['answered']) }}</span></td>
                    <td class="ap-num"><span style="color:#b87a14">{{ number_format($tot['missed']) }}</span></td>
                    <td class="ap-num"><span style="color:#c84646">{{ number_format($tot['declined']) }}</span></td>
                    <td class="ap-num"><span style="color:#6c757d">{{ number_format($tot['voicemail']) }}</span></td>
                    <td class="ap-num"><span style="color:var(--bs-gold,#d4af37)">{{ number_format($tot['recorded']) }}</span></td>
                    <td class="ap-num" style="font-family:monospace;font-size:.7rem">{{ $totTalk }}</td>
                    <td class="ap-num" style="font-family:monospace;font-size:.7rem">{{ $totAvgFmt }}</td>
                    <td class="ap-num">{{ $totCR }}%</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @else
    <div class="ap-empty">
        <i class="bx bx-user-x"></i>
        <h6>No Agent Data</h6>
        <p>
            @if($dateFrom || $dateTo)
                No outbound calls found for the selected date range. Try adjusting the filter or click Clear.
            @else
                No outbound call data found. Zoom Phone webhook calls will appear here once captured.
            @endif
        </p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function setToday() {
    const ptDate = new Intl.DateTimeFormat('en-CA', { timeZone: 'America/Los_Angeles' }).format(new Date());
    document.getElementById('apDateFrom').value = ptDate;
    document.getElementById('apDateTo').value   = ptDate;
    document.getElementById('apFilterForm').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const tbl = document.getElementById('apTable');
    if (!tbl) return;
    const tbody = document.getElementById('apBody');
    let sortCol = 'total_calls', sortDir = 'desc';

    function renderIcons() {
        tbl.querySelectorAll('thead th[data-sort]').forEach(th => {
            th.classList.remove('s-asc', 's-desc');
            if (th.dataset.sort === sortCol) th.classList.add(sortDir === 'asc' ? 's-asc' : 's-desc');
        });
    }

    function doSort(col) {
        sortDir = (sortCol === col && sortDir === 'desc') ? 'asc' : 'desc';
        sortCol = col;
        Array.from(tbody.querySelectorAll('tr'))
            .sort((a, b) => {
                const va = a.dataset[col] ?? '', vb = b.dataset[col] ?? '';
                const na = parseFloat(va), nb = parseFloat(vb);
                const cmp = isNaN(na) ? va.localeCompare(vb) : na - nb;
                return sortDir === 'asc' ? cmp : -cmp;
            })
            .forEach(r => tbody.appendChild(r));
        renderIcons();
    }

    tbl.querySelectorAll('thead th[data-sort]').forEach(th => {
        th.addEventListener('click', () => doSort(th.dataset.sort));
    });

    renderIcons();
});
</script>
@endpush
