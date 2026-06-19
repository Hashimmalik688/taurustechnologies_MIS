@extends('layouts.master')

@section('title')
    {{ $teamLabel }} Performance Report
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        /* ═══════════════════════════════════════════════════════════
           SCREEN STYLES
        ═══════════════════════════════════════════════════════════ */
        .pr-wrap { max-width:1160px;margin:0 auto }

        .pr-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem }
        .pr-hdr h5 { margin:0 0 .15rem;font-size:1.2rem;font-weight:800;display:flex;align-items:center;gap:.45rem }
        .pr-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .pr-hdr .sub { font-size:.72rem;color:var(--bs-surface-400);font-weight:500 }

        .pr-filter { display:flex;gap:.65rem;align-items:flex-end;flex-wrap:wrap;padding:.85rem 1rem;background:rgba(212,175,55,.05);border:1px solid rgba(212,175,55,.15);border-radius:10px;margin-bottom:1rem }
        .pr-filter label { font-size:.68rem;font-weight:700;color:var(--bs-gold,#92760d);display:block;margin-bottom:.25rem }
        .pr-filter input[type=date] { font-size:.82rem;padding:.4rem .65rem;border:1px solid rgba(212,175,55,.3);border-radius:8px;background:#fff;font-weight:600;min-width:150px }

        /* KPI Banner */
        .pr-kpi-banner { display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:.65rem;margin-bottom:1rem }
        .pr-kpi { border-radius:12px;padding:1rem .9rem;text-align:center;border:1px solid rgba(0,0,0,.06);background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.05) }
        .pr-kpi .kpi-n { font-size:2rem;font-weight:800;line-height:1;margin-bottom:.25rem;font-variant-numeric:tabular-nums }
        .pr-kpi .kpi-l { font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--bs-surface-500) }
        .pr-kpi .kpi-sub { font-size:.6rem;margin-top:.2rem;color:var(--bs-surface-400) }
        .kpi-total { border-top:3px solid #5ba4f5 } .kpi-total .kpi-n { color:#2563eb }
        .kpi-paid  { border-top:3px solid #34c38f } .kpi-paid  .kpi-n { color:#1a8754 }
        .kpi-appr  { border-top:3px solid #50a5f1 } .kpi-appr  .kpi-n { color:#1e6eb5 }
        .kpi-draft { border-top:3px solid #f1b44c } .kpi-draft .kpi-n { color:#b87a14 }
        .kpi-notiss{ border-top:3px solid #fb923c } .kpi-notiss .kpi-n{ color:#c2410c }
        .kpi-decl  { border-top:3px solid #f46a6a } .kpi-decl  .kpi-n { color:#c84646 }
        .kpi-rate  { border-top:3px solid #d4af37;background:rgba(212,175,55,.06) } .kpi-rate .kpi-n { color:#92760d }

        /* Table card */
        .pr-table-card { border-radius:12px;overflow:hidden;border:1px solid rgba(0,0,0,.07);box-shadow:0 1px 6px rgba(0,0,0,.06);margin-bottom:1rem }
        .pr-table-hdr { display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.1rem;background:rgba(212,175,55,.06);border-bottom:1px solid rgba(212,175,55,.15);flex-wrap:wrap;gap:.4rem }
        .pr-table-title { font-size:.85rem;font-weight:800;letter-spacing:-.2px }
        .pr-table-meta { font-size:.67rem;color:var(--bs-surface-400);font-weight:500 }
        .pr-top-chip { display:inline-flex;align-items:center;gap:.3rem;font-size:.67rem;font-weight:700;padding:.22rem .65rem;border-radius:20px;background:rgba(212,175,55,.18);color:#92760d;border:1px solid rgba(212,175,55,.35);white-space:nowrap }

        /* Section pill */
        .sec-pill-pjc    { display:inline-flex;align-items:center;gap:.3rem;font-size:.7rem;font-weight:700;padding:.18rem .55rem;border-radius:16px;background:rgba(80,165,241,.1);color:#1e6eb5 }
        .sec-pill-closer { display:inline-flex;align-items:center;gap:.3rem;font-size:.7rem;font-weight:700;padding:.18rem .55rem;border-radius:16px;background:rgba(52,195,143,.1);color:#1a8754 }

        .pr-table { width:100%;border-collapse:collapse;font-size:.75rem }
        .pr-table thead th { padding:.6rem .9rem;font-size:.63rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-surface-500);background:rgba(248,250,252,.95);border-bottom:2px solid rgba(0,0,0,.06);white-space:nowrap;cursor:pointer;user-select:none }
        .pr-table thead th:hover { background:rgba(212,175,55,.06) }
        .pr-table thead th.sorted-asc::after  { content:' ↑';font-size:.6rem }
        .pr-table thead th.sorted-desc::after { content:' ↓';font-size:.6rem }
        .pr-table tbody td { padding:.55rem .9rem;border-bottom:1px solid rgba(0,0,0,.04);color:var(--bs-surface-900);vertical-align:middle }
        .pr-table tbody tr.row-gold td { background:rgba(212,175,55,.06) }
        .pr-table tbody tr.row-agent:hover td { background:rgba(212,175,55,.05) }
        .pr-table tfoot td { padding:.6rem .9rem;border-top:2px solid rgba(0,0,0,.08);font-weight:700;font-size:.74rem;background:rgba(248,250,252,.7) }

        .td-r { text-align:right;font-variant-numeric:tabular-nums }
        .th-r { text-align:right }
        .td-name { font-weight:700 }

        .rank-badge { display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;font-size:.63rem;font-weight:800;flex-shrink:0 }
        .rank-1 { background:rgba(212,175,55,.28);color:#78600a }
        .rank-2 { background:rgba(148,163,184,.22);color:#475569 }
        .rank-3 { background:rgba(180,83,9,.16);color:#7c2d12 }
        .rank-n { background:rgba(100,116,139,.1);color:#64748b }

        .sp { display:inline-block;min-width:28px;text-align:center;font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:10px }
        .sp-0    { background:rgba(100,116,139,.07);color:#94a3b8 }
        .sp-paid { background:rgba(52,195,143,.14);color:#1a8754 }
        .sp-appr { background:rgba(80,165,241,.14);color:#1e6eb5 }
        .sp-draft{ background:rgba(241,180,76,.14);color:#b87a14 }
        .sp-niss { background:rgba(251,146,60,.12);color:#c2410c }
        .sp-decl { background:rgba(244,106,106,.14);color:#c84646 }
        .sp-tot  { background:rgba(100,116,139,.1);color:#475569 }

        .rate-wrap { display:flex;align-items:center;gap:.45rem;min-width:110px }
        .rate-track { flex:1;height:7px;border-radius:4px;background:rgba(0,0,0,.07);overflow:hidden }
        .rate-fill  { height:100%;border-radius:4px }
        .rate-lbl   { font-size:.7rem;font-weight:800;min-width:36px;text-align:right }
        .rate-hi  .rate-fill { background:linear-gradient(90deg,#34c38f,#1a8754) } .rate-hi  .rate-lbl { color:#1a8754 }
        .rate-mid .rate-fill { background:linear-gradient(90deg,#f1b44c,#e8940a) } .rate-mid .rate-lbl { color:#b87a14 }
        .rate-lo  .rate-fill { background:linear-gradient(90deg,#f46a6a,#c84646) } .rate-lo  .rate-lbl { color:#c84646 }

        /* Expand */
        .row-agent { cursor:pointer }
        .expand-icon { font-size:.75rem;color:var(--bs-surface-400);margin-left:.3rem;transition:transform .2s;display:inline-block }
        .row-agent.expanded .expand-icon { transform:rotate(90deg) }
        .row-leads { display:none }
        .row-leads.open { display:table-row }

        .leads-inner { padding:.5rem .9rem .9rem 2.5rem }
        .leads-sub-table { width:100%;border-collapse:collapse;font-size:.7rem }
        .leads-sub-table thead th { padding:.35rem .6rem;font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-400);border-bottom:1px solid rgba(0,0,0,.07);background:rgba(248,250,252,.6) }
        .leads-sub-table tbody td { padding:.38rem .6rem;border-bottom:1px solid rgba(0,0,0,.03);color:var(--bs-surface-800) }
        .leads-sub-table tbody tr:last-child td { border-bottom:none }
        .leads-sub-table tbody tr:hover td { background:rgba(212,175,55,.04) }

        .pr-print-btn { display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;padding:.3rem .7rem;border-radius:8px;font-weight:600;cursor:pointer;background:rgba(212,175,55,.12);color:#92760d;border:1px solid rgba(212,175,55,.3);text-decoration:none }
        .pr-print-btn:hover { background:rgba(212,175,55,.22) }

        /* Dark theme */
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pr-kpi { background:rgba(15,23,42,.5);border-color:rgba(255,255,255,.06) }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pr-table thead th,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pr-table tfoot td,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .leads-sub-table thead th { background:rgba(15,23,42,.7);color:#94a3b8 }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pr-table tbody td,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .leads-sub-table tbody td { color:#e2e8f0 }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pr-filter input[type=date] { background:rgba(15,23,42,.6);color:#e2e8f0 }

        /* ═══════════════════════════════════════════════════════════
           PRINT STYLES
        ═══════════════════════════════════════════════════════════ */
        @media print {
            body * { visibility:hidden }
            #printArea, #printArea * { visibility:visible }
            #printArea { position:absolute;top:0;left:0;width:100% }

            @page { size:A4 landscape; margin:12mm 10mm }

            #printArea {
                font-family:'Segoe UI',Arial,sans-serif;
                font-size:8.5pt;color:#1a1a2e;background:#fff;
            }

            /* Header */
            .print-header { display:flex;align-items:flex-start;justify-content:space-between;border-bottom:3pt solid #d4af37;padding-bottom:8pt;margin-bottom:12pt }
            .print-header .co-name { font-size:17pt;font-weight:900;letter-spacing:-.5px;color:#1a1a2e;line-height:1;margin-bottom:2pt }
            .print-header .co-name span { color:#d4af37 }
            .print-header .report-title { font-size:10pt;font-weight:700;margin-bottom:2pt }
            .print-header .report-meta  { font-size:7.5pt;color:#64748b }
            .print-header .print-date   { font-size:7.5pt;color:#64748b;text-align:right }
            .print-header .confidential { font-size:6.5pt;font-weight:700;color:#c84646;letter-spacing:.5px;text-transform:uppercase;margin-top:3pt }

            /* KPI row */
            .print-kpi-row { display:grid;grid-template-columns:repeat(7,1fr);gap:5pt;margin-bottom:12pt }
            .print-kpi-box { border:1pt solid #e2e8f0;border-radius:5pt;padding:7pt 5pt;text-align:center;background:#f8fafc }
            .print-kpi-box .p-num { font-size:17pt;font-weight:900;line-height:1;margin-bottom:2pt }
            .print-kpi-box .p-lbl { font-size:6pt;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b }
            .print-kpi-box .p-sub { font-size:5.5pt;color:#94a3b8;margin-top:1pt }
            .pkpi-total  { border-top:3pt solid #2563eb } .pkpi-total  .p-num { color:#2563eb }
            .pkpi-paid   { border-top:3pt solid #1a8754 } .pkpi-paid   .p-num { color:#1a8754 }
            .pkpi-appr   { border-top:3pt solid #1e6eb5 } .pkpi-appr   .p-num { color:#1e6eb5 }
            .pkpi-draft  { border-top:3pt solid #b87a14 } .pkpi-draft  .p-num { color:#b87a14 }
            .pkpi-notiss { border-top:3pt solid #c2410c } .pkpi-notiss .p-num { color:#c2410c }
            .pkpi-decl   { border-top:3pt solid #c84646 } .pkpi-decl   .p-num { color:#c84646 }
            .pkpi-rate   { border-top:3pt solid #d4af37;background:#fffdf2 } .pkpi-rate .p-num { color:#92760d }

            /* Section title */
            .print-section-title { font-size:9pt;font-weight:800;border-left:3pt solid #d4af37;padding-left:6pt;margin-bottom:7pt;display:flex;align-items:center;gap:5pt }
            .print-section-title .s-sub { color:#64748b;font-size:7pt;font-weight:500 }
            .print-section-title .s-top  { color:#92760d;font-size:7pt;font-weight:700 }

            /* Summary table */
            .print-agent-table { width:100%;border-collapse:collapse;font-size:8pt;margin-bottom:14pt }
            .print-agent-table thead tr { background:#1a1a2e }
            .print-agent-table thead th { padding:5pt 7pt;font-size:6.5pt;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#fff;text-align:left;border:none }
            .print-agent-table thead th.th-r { text-align:right }
            .print-agent-table tbody tr.p-agent-row { border-bottom:1pt solid #e2e8f0 }
            .print-agent-table tbody tr.p-agent-row:nth-child(odd) { background:#f8fafc }
            .print-agent-table tbody tr.p-agent-row.p-rank-1 { background:#fffdf0 }
            .print-agent-table tbody td { padding:5pt 7pt;vertical-align:middle }
            .print-agent-table tbody td.td-r { text-align:right }
            .print-agent-table tfoot tr { background:#1a1a2e }
            .print-agent-table tfoot td { padding:5pt 7pt;font-weight:700;color:#fff;font-size:7.5pt }
            .print-agent-table tfoot td.td-r { text-align:right }

            .p-rank { display:inline-block;width:14pt;height:14pt;border-radius:50%;text-align:center;line-height:14pt;font-size:6.5pt;font-weight:800 }
            .p-rank-gold   { background:#d4af37;color:#fff }
            .p-rank-silver { background:#9ca3af;color:#fff }
            .p-rank-bronze { background:#b45309;color:#fff }
            .p-rank-other  { background:#e2e8f0;color:#475569 }

            .p-rate-bar { display:inline-flex;align-items:center;gap:3pt;width:100% }
            .p-rate-track { flex:1;height:5pt;background:#e2e8f0;border-radius:3pt;overflow:hidden }
            .p-rate-fill-green { height:100%;background:#1a8754;border-radius:3pt }
            .p-rate-fill-amber { height:100%;background:#b87a14;border-radius:3pt }
            .p-rate-fill-red   { height:100%;background:#c84646;border-radius:3pt }
            .p-rate-lbl { font-size:7pt;font-weight:800;min-width:22pt;text-align:right }
            .p-rate-lbl-green { color:#1a8754 } .p-rate-lbl-amber { color:#b87a14 } .p-rate-lbl-red { color:#c84646 }

            .p-badge { display:inline-block;padding:1pt 4pt;border-radius:5pt;font-size:6.5pt;font-weight:700;text-align:center;min-width:14pt }
            .pb-paid  { background:#d1fae5;color:#065f46 } .pb-appr  { background:#dbeafe;color:#1e40af }
            .pb-draft { background:#fef3c7;color:#92400e } .pb-niss  { background:#ffedd5;color:#9a3412 }
            .pb-decl  { background:#fee2e2;color:#991b1b } .pb-zero  { background:#f1f5f9;color:#94a3b8 }
            .pb-tot   { background:#f1f5f9;color:#374151 }
            .p-agent-name { font-weight:700;font-size:8pt }

            /* Agent breakdown blocks */
            .print-agent-block { margin-bottom:16pt;page-break-inside:avoid }
            .print-agent-block-hdr { display:flex;align-items:center;justify-content:space-between;padding:5pt 9pt;border-radius:3pt 3pt 0 0 }
            .print-agent-block-hdr-closer { background:#1a3a2e }
            .print-agent-block-hdr-pjc    { background:#1a2a3e }
            .print-agent-block-hdr .a-name { font-size:8.5pt;font-weight:800;color:#fff }
            .print-agent-block-hdr .a-role { font-size:6pt;color:#94a3b8;margin-top:1pt }
            .print-agent-block-hdr .a-stats { display:flex;gap:8pt }
            .print-agent-block-hdr .a-stat  { text-align:center }
            .print-agent-block-hdr .a-stat-n { font-size:10pt;font-weight:900;line-height:1;color:#fff }
            .print-agent-block-hdr .a-stat-l { font-size:5pt;text-transform:uppercase;letter-spacing:.4px;color:#94a3b8 }

            .print-leads-table { width:100%;border-collapse:collapse;font-size:7pt;border:1pt solid #e2e8f0;border-top:none }
            .print-leads-table thead th { padding:3.5pt 7pt;font-size:6pt;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#64748b;background:#f8fafc;border-bottom:1pt solid #e2e8f0;text-align:left }
            .print-leads-table thead th.th-r { text-align:right }
            .print-leads-table tbody td { padding:3.5pt 7pt;border-bottom:1pt solid #f1f5f9;vertical-align:middle }
            .print-leads-table tbody tr:nth-child(even) td { background:#f8fafc }
            .print-leads-table tbody td.td-r { text-align:right }

            .print-page-break { page-break-before:always }
            .print-footer { border-top:1pt solid #e2e8f0;padding-top:5pt;margin-top:12pt;display:flex;justify-content:space-between;font-size:6.5pt;color:#94a3b8 }
        }
    </style>
@endsection

@section('content')
<div class="pr-wrap">

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div class="pr-hdr">
        <div>
            <h5><i class="bx bx-trophy"></i> {{ $teamLabel }} Performance Report</h5>
            <div class="sub">{{ $dateFrom }} &nbsp;→&nbsp; {{ $dateTo }} &nbsp;·&nbsp; {{ $kpis['total'] }} total sale(s)</div>
        </div>
        <div style="display:flex;gap:.5rem;align-items:center">
            <a href="{{ route($printRoute, array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'closer_id' => $closerFilter])) }}"
               target="_blank" class="pr-print-btn">
                <i class="bx bx-printer"></i> Print / PDF
            </a>
            <a href="{{ route('settings.reports.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .7rem">
                <i class="bx bx-arrow-back"></i> Reports
            </a>
        </div>
    </div>

    {{-- ── Date Filter ──────────────────────────────────────────── --}}
    <form method="GET" action="{{ route($reportRoute) }}" class="pr-filter">
        <div>
            <label>From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}">
        </div>
        <div>
            <label>Closer</label>
            <select name="closer_id" style="font-size:.82rem;padding:.4rem .65rem;border:1px solid rgba(212,175,55,.3);border-radius:8px;background:#fff;font-weight:500;min-width:160px">
                <option value="">All Closers</option>
                @foreach($closerOptions as $opt)
                    <option value="{{ $opt['id'] }}" @selected($closerFilter == $opt['id'])>{{ $opt['name'] }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="pipe-pill-apply" style="font-size:.82rem;padding:.42rem 1rem">
            <i class="bx bx-refresh" style="vertical-align:middle;margin-right:.2rem"></i> Apply
        </button>
        <a href="{{ route($reportRoute, ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->toDateString()]) }}"
           class="pipe-pill" style="font-size:.82rem;padding:.42rem .9rem">
            <i class="bx bx-calendar-check" style="vertical-align:middle;margin-right:.2rem"></i> This Month
        </a>
        <a href="{{ route($reportRoute, ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()]) }}"
           class="pipe-pill" style="font-size:.82rem;padding:.42rem .9rem">
            <i class="bx bx-calendar" style="vertical-align:middle;margin-right:.2rem"></i> Today
        </a>
        @if($closerFilter)
            <a href="{{ route($reportRoute, ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               class="pipe-pill" style="font-size:.82rem;padding:.42rem .9rem;color:#c84646">
                <i class="bx bx-x" style="vertical-align:middle"></i> Clear Filter
            </a>
        @endif
    </form>

    {{-- ── KPI Banner ───────────────────────────────────────────── --}}
    @php $paidRate = $kpis['total'] > 0 ? round(($kpis['paid'] / $kpis['total']) * 100, 1) : 0; @endphp
    <div class="pr-kpi-banner">
        <div class="pr-kpi kpi-total"><div class="kpi-n">{{ $kpis['total'] }}</div><div class="kpi-l">Total Sales</div></div>
        <div class="pr-kpi kpi-paid"><div class="kpi-n">{{ $kpis['paid'] }}</div><div class="kpi-l">Paid</div><div class="kpi-sub">Premium collected</div></div>
        <div class="pr-kpi kpi-appr"><div class="kpi-n">{{ $kpis['approved'] }}</div><div class="kpi-l">Approved</div><div class="kpi-sub">Pending contract</div></div>
        <div class="pr-kpi kpi-draft"><div class="kpi-n">{{ $kpis['draft'] }}</div><div class="kpi-l">Draft</div><div class="kpi-sub">Banking set up</div></div>
        <div class="pr-kpi kpi-notiss"><div class="kpi-n">{{ $kpis['not_issued'] }}</div><div class="kpi-l">Not Issued</div><div class="kpi-sub">Could not issue</div></div>
        <div class="pr-kpi kpi-decl"><div class="kpi-n">{{ $kpis['declined'] }}</div><div class="kpi-l">Declined</div><div class="kpi-sub">Sale reversed</div></div>
        <div class="pr-kpi kpi-rate"><div class="kpi-n">{{ $paidRate }}%</div><div class="kpi-l">Paid Rate</div><div class="kpi-sub">${{ number_format($kpis['total_premium'], 2) }}/mo</div></div>
    </div>

    {{-- ── Macro to render a leaderboard section ───────────────── --}}
    @php
        function renderLeaderboardTable($rows, $tableId, $sectionLabel) { return [$rows, $tableId, $sectionLabel]; }
    @endphp

    @php
        $sections = [];
        if($pjcRows->count() > 0) $sections[] = ['rows' => $pjcRows,   'id' => 'pjcTable',   'pill' => 'sec-pill-pjc',    'label' => 'PJC Leaderboard',    'role' => $pjcLabel];
        $sections[] =                             ['rows' => $agentRows, 'id' => 'closerTable', 'pill' => 'sec-pill-closer', 'label' => 'Closer Leaderboard', 'role' => $closerLabel];
    @endphp
    @foreach($sections as $section)
    @php $sRows = $section['rows']; @endphp
    <div class="pr-table-card">
        <div class="pr-table-hdr">
            <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
                <span class="{{ $section['pill'] }}">{{ $section['label'] }}</span>
                <span class="pr-table-meta">{{ $sRows->count() }} agent(s) · {{ $dateFrom }} → {{ $dateTo }} · click row to expand leads</span>
            </div>
            @if($sRows->count() > 0)
                <span class="pr-top-chip">
                    <i class="bx bx-trophy"></i>
                    Top: <strong>{{ $sRows->first()->agent_name }}</strong> · {{ $sRows->first()->paid_rate }}% paid
                    @if($sRows->first()->paid > 0) · ${{ number_format($sRows->first()->premium, 2) }}/mo @endif
                </span>
            @endif
        </div>
        <div style="overflow-x:auto">
            <table class="pr-table" id="{{ $section['id'] }}">
                <thead>
                    <tr>
                        <th style="width:36px">#</th>
                        <th>Agent</th>
                        <th class="th-r">Total Sales</th>
                        <th class="th-r">Paid</th>
                        <th class="th-r">Approved</th>
                        <th class="th-r">Draft</th>
                        <th class="th-r">Not Issued</th>
                        <th class="th-r">Declined</th>
                        <th class="th-r">Paid Premium/mo</th>
                        <th class="th-r" style="min-width:130px">Paid Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sRows as $i => $row)
                        @php $rateClass = $row->paid_rate >= 50 ? 'rate-hi' : ($row->paid_rate >= 20 ? 'rate-mid' : 'rate-lo'); @endphp
                        <tr class="row-agent {{ $i===0 && $sRows->count()>1 ? 'row-gold' : '' }}"
                            data-target="{{ $section['id'] }}-leads-{{ $row->agent_id }}">
                            <td>
                                @if($i===0)<span class="rank-badge rank-1">1</span>
                                @elseif($i===1)<span class="rank-badge rank-2">2</span>
                                @elseif($i===2)<span class="rank-badge rank-3">3</span>
                                @else<span class="rank-badge rank-n">{{ $i+1 }}</span>@endif
                            </td>
                            <td class="td-name">
                                {{ $row->agent_name }}
                                @if($i===0 && $sRows->count()>1)<i class="bx bx-trophy" style="color:#d4af37;font-size:.8rem;margin-left:.2rem"></i>@endif
                                <i class="bx bx-chevron-right expand-icon"></i>
                            </td>
                            <td class="td-r"><span class="sp sp-tot">{{ $row->total }}</span></td>
                            <td class="td-r"><span class="sp {{ $row->paid>0?'sp-paid':'sp-0' }}">{{ $row->paid }}</span></td>
                            <td class="td-r"><span class="sp {{ $row->approved>0?'sp-appr':'sp-0' }}">{{ $row->approved }}</span></td>
                            <td class="td-r"><span class="sp {{ $row->draft>0?'sp-draft':'sp-0' }}">{{ $row->draft }}</span></td>
                            <td class="td-r"><span class="sp {{ $row->not_issued>0?'sp-niss':'sp-0' }}">{{ $row->not_issued }}</span></td>
                            <td class="td-r"><span class="sp {{ $row->declined>0?'sp-decl':'sp-0' }}">{{ $row->declined }}</span></td>
                            <td class="td-r" style="font-size:.73rem">${{ number_format($row->premium,2) }}</td>
                            <td class="td-r">
                                <div class="rate-wrap {{ $rateClass }}">
                                    <div class="rate-track"><div class="rate-fill" style="width:{{ $row->paid_rate }}%"></div></div>
                                    <span class="rate-lbl">{{ $row->paid_rate }}%</span>
                                </div>
                            </td>
                        </tr>
                        <tr class="row-leads" id="{{ $section['id'] }}-leads-{{ $row->agent_id }}">
                            <td colspan="10" style="padding:0;background:rgba(248,250,252,.7)">
                                <div class="leads-inner">
                                    <div style="font-size:.63rem;font-weight:700;color:var(--bs-surface-400);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.35rem">
                                        {{ $row->leads->count() }} lead(s) — {{ $row->agent_name }}
                                    </div>
                                    <table class="leads-sub-table">
                                        <thead>
                                            <tr>
                                                <th>#</th><th>Customer</th><th>Policy Type</th><th>Carrier</th>
                                                <th>State</th><th style="text-align:right">Coverage</th>
                                                <th style="text-align:right">Premium/mo</th><th>Status</th>
                                                <th style="text-align:right">Sale Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($row->leads as $li => $lead)
                                            @php
                                                [$sc,$sl] = match($lead->sale_stage) {
                                                    'paid'       => ['sp-paid','Paid'],
                                                    'approved'   => ['sp-appr','Approved'],
                                                    'draft'      => ['sp-draft','Draft'],
                                                    'not_issued' => ['sp-niss','Not Issued'],
                                                    'declined'   => ['sp-decl','Declined'],
                                                    default      => ['sp-0','Pending'],
                                                };
                                            @endphp
                                            <tr>
                                                <td style="color:var(--bs-surface-400);font-size:.65rem">{{ $li+1 }}</td>
                                                <td style="font-weight:600">{{ $lead->cn_name ?? '—' }}</td>
                                                <td>{{ $lead->policy_type ?? '—' }}</td>
                                                <td style="font-size:.68rem">{{ $lead->carrier_name ?? '—' }}</td>
                                                <td>{{ $lead->state ?? '—' }}</td>
                                                <td style="text-align:right">${{ number_format($lead->coverage_amount??0) }}</td>
                                                <td style="text-align:right">${{ number_format($lead->monthly_premium??0,2) }}</td>
                                                <td><span class="sp {{ $sc }}" style="font-size:.6rem">{{ $sl }}</span></td>
                                                <td style="text-align:right;font-size:.68rem;white-space:nowrap">
                                                    {{ $lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : '—' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" style="text-align:center;padding:2rem;color:var(--bs-surface-400);font-size:.8rem">
                            <i class="bx bx-info-circle" style="font-size:1.8rem;display:block;margin-bottom:.5rem"></i>
                            No sales in this period
                        </td></tr>
                    @endforelse
                </tbody>
                @if($sRows->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="2">Total</td>
                        <td class="td-r">{{ $sRows->sum('total') }}</td>
                        <td class="td-r">{{ $sRows->sum('paid') }}</td>
                        <td class="td-r">{{ $sRows->sum('approved') }}</td>
                        <td class="td-r">{{ $sRows->sum('draft') }}</td>
                        <td class="td-r">{{ $sRows->sum('not_issued') }}</td>
                        <td class="td-r">{{ $sRows->sum('declined') }}</td>
                        <td class="td-r" style="font-size:.73rem">${{ number_format($sRows->sum('premium'),2) }}</td>
                        <td class="td-r">
                            @php $t=$sRows->sum('total');$p=$sRows->sum('paid'); @endphp
                            {{ $t>0?round(($p/$t)*100,1):0 }}%
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endforeach

</div>{{-- .pr-wrap --}}


{{-- ═══════════════════════════════════════════════════════════════
     PRINT AREA
═══════════════════════════════════════════════════════════════ --}}
<div id="printArea" style="display:none">

    {{-- Header --}}
    <div class="print-header">
        <div>
            <div class="co-name">taurus<span>·</span>mis</div>
            <div class="report-title">{{ $teamLabel }} Team — Agent Performance Report</div>
            <div class="report-meta">Period: {{ $dateFrom }} → {{ $dateTo }} &nbsp;·&nbsp; {{ $kpis['total'] }} total sales &nbsp;·&nbsp; {{ $agentRows->count() + $pjcRows->count() }} active agent(s)</div>
        </div>
        <div>
            <div class="print-date">Generated: {{ now()->format('F j, Y \a\t g:i A') }}</div>
            <div class="confidential">Confidential — Internal Use Only</div>
        </div>
    </div>

    {{-- KPI --}}
    <div class="print-kpi-row">
        <div class="print-kpi-box pkpi-total"><div class="p-num">{{ $kpis['total'] }}</div><div class="p-lbl">Total Sales</div></div>
        <div class="print-kpi-box pkpi-paid"><div class="p-num">{{ $kpis['paid'] }}</div><div class="p-lbl">Paid</div><div class="p-sub">Premium collected</div></div>
        <div class="print-kpi-box pkpi-appr"><div class="p-num">{{ $kpis['approved'] }}</div><div class="p-lbl">Approved</div><div class="p-sub">Pending contract</div></div>
        <div class="print-kpi-box pkpi-draft"><div class="p-num">{{ $kpis['draft'] }}</div><div class="p-lbl">Draft</div><div class="p-sub">Banking set up</div></div>
        <div class="print-kpi-box pkpi-notiss"><div class="p-num">{{ $kpis['not_issued'] }}</div><div class="p-lbl">Not Issued</div></div>
        <div class="print-kpi-box pkpi-decl"><div class="p-num">{{ $kpis['declined'] }}</div><div class="p-lbl">Declined</div><div class="p-sub">Sale reversed</div></div>
        <div class="print-kpi-box pkpi-rate"><div class="p-num">{{ $paidRate }}%</div><div class="p-lbl">Paid Rate</div><div class="p-sub">${{ number_format($kpis['total_premium'],2) }}/mo</div></div>
    </div>

    {{-- PJC Summary Table --}}
    @if($pjcRows->count() > 0)
    <div class="print-section-title">
        PJC Leaderboard <span class="s-sub">— {{ $pjcLabel }}</span>
        <span class="s-top">· Top: {{ $pjcRows->first()->agent_name }} ({{ $pjcRows->first()->paid_rate }}%)</span>
    </div>
    @include('admin.reports._peregrine-print-table', ['rows' => $pjcRows])
    @endif

    @if($agentRows->count() > 0)
    <div class="print-section-title" style="margin-top:10pt">
        Closer Leaderboard <span class="s-sub">— {{ $closerLabel }}</span>
        <span class="s-top">· Top: {{ $agentRows->first()->agent_name }} ({{ $agentRows->first()->paid_rate }}%)</span>
    </div>
    @include('admin.reports._peregrine-print-table', ['rows' => $agentRows])
    @endif

    {{-- ── Lead Breakdown — new page ──────────────────────────── --}}
    <div class="print-page-break"></div>

    <div class="print-section-title" style="margin-bottom:10pt">
        Full Lead Breakdown <span class="s-sub">— every sale, by agent</span>
    </div>

    @if($pjcRows->count() > 0)
        <div style="font-size:7.5pt;font-weight:700;color:#1e6eb5;border-left:2pt solid #1e6eb5;padding-left:5pt;margin-bottom:8pt;text-transform:uppercase;letter-spacing:.5px">
            PJC — Junior Closers
        </div>
        @foreach($pjcRows as $i => $row)
            @include('admin.reports._peregrine-print-agent', ['row' => $row, 'rank' => $i+1, 'role' => 'PJC', 'headerClass' => 'print-agent-block-hdr-pjc'])
        @endforeach
    @endif

    @if($agentRows->count() > 0)
        <div style="font-size:7.5pt;font-weight:700;color:#1a8754;border-left:2pt solid #1a8754;padding-left:5pt;margin:10pt 0 8pt;text-transform:uppercase;letter-spacing:.5px">
            Closers
        </div>
        @foreach($agentRows as $i => $row)
            @include('admin.reports._peregrine-print-agent', ['row' => $row, 'rank' => $i+1, 'role' => 'Closer', 'headerClass' => 'print-agent-block-hdr-closer'])
        @endforeach
    @endif

    <div class="print-footer">
        <div>Taurus MIS · {{ $teamLabel }} Performance Report · {{ $dateFrom }} – {{ $dateTo }}</div>
        <div>Generated {{ now()->format('F j, Y \a\t g:i A') }} · Confidential</div>
    </div>

</div>{{-- #printArea --}}
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Expand / collapse
    document.querySelectorAll('.row-agent').forEach(function (row) {
        row.addEventListener('click', function () {
            var tid   = this.dataset.target;
            var leads = document.getElementById(tid);
            if (!leads) return;
            var open  = leads.classList.toggle('open');
            this.classList.toggle('expanded', open);
        });
    });

    // Sortable — keeps summary+detail pairs together
    ['pjcTable','closerTable'].forEach(function (tableId) {
        var table = document.getElementById(tableId);
        if (!table) return;
        var headers = table.querySelectorAll('thead th');
        headers.forEach(function (th, colIdx) {
            th.addEventListener('click', function () {
                var tbody = table.querySelector('tbody');
                var rows  = Array.from(tbody.querySelectorAll('tr'));
                var pairs = [];
                for (var j = 0; j < rows.length; j++) {
                    if (rows[j].classList.contains('row-agent')) {
                        pairs.push({ s: rows[j], d: rows[j+1] || null });
                    }
                }
                var asc = th.dataset.sortDir !== 'asc';
                th.dataset.sortDir = asc ? 'asc' : 'desc';
                headers.forEach(function (h) { h.classList.remove('sorted-asc','sorted-desc'); if(h!==th) delete h.dataset.sortDir; });
                th.classList.add(asc ? 'sorted-asc' : 'sorted-desc');
                pairs.sort(function (a, b) {
                    var aT = a.s.children[colIdx]?.textContent.trim().replace(/[$,%\s]/g,'').replace(/,/g,'') || '';
                    var bT = b.s.children[colIdx]?.textContent.trim().replace(/[$,%\s]/g,'').replace(/,/g,'') || '';
                    var aN = parseFloat(aT), bN = parseFloat(bT);
                    if (!isNaN(aN) && !isNaN(bN)) return asc ? aN-bN : bN-aN;
                    return asc ? aT.localeCompare(bT) : bT.localeCompare(aT);
                });
                pairs.forEach(function (p) { tbody.appendChild(p.s); if(p.d) tbody.appendChild(p.d); });
            });
        });
    });
});
</script>
@endsection
