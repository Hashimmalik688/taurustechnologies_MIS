<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $teamLabel }} Performance Report | Taurus CRM</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }
        body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 8.5pt; color: #1a1a2e; background: #fff; padding: 14px 18px }

        @page { size: A4 landscape; margin: 10mm 9mm }

        /* Controls */
        .controls { display: flex; gap: 8px; margin-bottom: 14px }
        .btn { padding: 7px 18px; border: none; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; color: #fff }
        .btn-print { background: #b8860b }
        .btn-back  { background: #6b7280 }
        @media print { .controls { display: none } }

        /* Header — single compact row */
        .hdr { display: flex; justify-content: space-between; align-items: center; border-bottom: 2.5px solid #d4af37; padding-bottom: 6px; margin-bottom: 10px }
        .hdr-left { display: flex; align-items: baseline; gap: 10px }
        .hdr-brand { font-size: 15pt; font-weight: 900; letter-spacing: -.4px }
        .hdr-brand span { color: #d4af37 }
        .hdr-title { font-size: 9pt; font-weight: 700 }
        .hdr-meta  { font-size: 7pt; color: #64748b }
        .hdr-right { text-align: right; font-size: 7pt; color: #64748b }
        .hdr-conf  { color: #c84646; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; font-size: 6.5pt }

        /* KPI row — single line */
        .kpi-row { display: flex; gap: 5px; margin-bottom: 10px }
        .kpi { flex: 1; border: 1px solid #e2e8f0; border-radius: 5px; padding: 5px 6px; text-align: center; background: #f8fafc }
        .kpi .kn { font-size: 15pt; font-weight: 900; line-height: 1.1 }
        .kpi .kl { font-size: 5.5pt; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #64748b }
        .kpi .ks { font-size: 5pt; color: #94a3b8; margin-top: 1px }
        .k-tot  { border-top: 2.5px solid #2563eb } .k-tot  .kn { color: #2563eb }
        .k-paid { border-top: 2.5px solid #1a8754 } .k-paid .kn { color: #1a8754 }
        .k-appr { border-top: 2.5px solid #1e6eb5 } .k-appr .kn { color: #1e6eb5 }
        .k-drft { border-top: 2.5px solid #b87a14 } .k-drft .kn { color: #b87a14 }
        .k-niss { border-top: 2.5px solid #c2410c } .k-niss .kn { color: #c2410c }
        .k-decl { border-top: 2.5px solid #c84646 } .k-decl .kn { color: #c84646 }
        .k-rate { border-top: 2.5px solid #d4af37; background: #fffdf2 } .k-rate .kn { color: #92760d }

        /* Section label */
        .sec { font-size: 8pt; font-weight: 800; border-left: 2.5px solid #d4af37; padding-left: 5px; margin: 9px 0 5px }
        .sec span { color: #64748b; font-weight: 500; font-size: 7pt }
        .sec em    { color: #92760d; font-weight: 700; font-size: 7pt; font-style: normal }

        /* Role divider */
        .role-div { font-size: 6.5pt; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; padding: 2px 6px; margin: 7px 0 5px; display: inline-block }
        .role-pjc    { background: #dbeafe; color: #1e40af }
        .role-closer { background: #d1fae5; color: #065f46 }

        /* Summary table */
        .stbl { width: 100%; border-collapse: collapse; font-size: 7.5pt; margin-bottom: 8px }
        .stbl thead th { padding: 3px 6px; font-size: 6pt; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #fff; background: #1a1a2e; text-align: left; white-space: nowrap }
        .stbl thead th.r { text-align: right }
        .stbl tbody tr:nth-child(odd)  { background: #f8fafc }
        .stbl tbody tr.top1 { background: #fffdf0 }
        .stbl tbody td { padding: 3px 6px; border-bottom: 1px solid #f0f0f0; vertical-align: middle }
        .stbl tbody td.r { text-align: right }
        .stbl tfoot td { padding: 3px 6px; background: #1a1a2e; color: #fff; font-weight: 700; font-size: 7pt }
        .stbl tfoot td.r { text-align: right }

        /* Agent header — slim two-column */
        .ag-hdr { display: flex; align-items: center; justify-content: space-between; padding: 4px 8px; color: #fff; margin-top: 6px }
        .ag-pjc    { background: #1a2a3e }
        .ag-closer { background: #1a3a2e }
        .ag-hdr .ag-name { font-size: 8pt; font-weight: 800 }
        .ag-hdr .ag-sub  { font-size: 5.5pt; color: #94a3b8 }
        .ag-stats { display: flex; gap: 10px }
        .ag-stat .an { font-size: 10pt; font-weight: 900; line-height: 1; text-align: center }
        .ag-stat .al { font-size: 4.5pt; text-transform: uppercase; letter-spacing: .3px; color: #94a3b8; text-align: center }

        /* Lead table */
        .ltbl { width: 100%; border-collapse: collapse; font-size: 7pt; border: 1px solid #e2e8f0; border-top: none; margin-bottom: 3px }
        .ltbl thead th { padding: 2.5px 6px; font-size: 5.5pt; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; color: #64748b; background: #f8fafc; border-bottom: 1px solid #e2e8f0 }
        .ltbl thead th.r { text-align: right }
        .ltbl tbody td { padding: 2.5px 6px; border-bottom: 1px solid #f5f5f5 }
        .ltbl tbody tr:nth-child(even) td { background: #f9f9f9 }
        .ltbl tbody td.r { text-align: right }

        /* Badges */
        .b { display: inline-block; padding: .5px 4px; border-radius: 4px; font-size: 6pt; font-weight: 700; text-align: center }
        .b-paid { background: #d1fae5; color: #065f46 }
        .b-appr { background: #dbeafe; color: #1e40af }
        .b-drft { background: #fef3c7; color: #92400e }
        .b-niss { background: #ffedd5; color: #9a3412 }
        .b-decl { background: #fee2e2; color: #991b1b }
        .b-pend { background: #f1f5f9; color: #64748b }
        .b-zero { background: #f1f5f9; color: #c0c8d0 }
        .b-tot  { background: #e8edf2; color: #374151 }

        /* Rank */
        .rk { display: inline-block; width: 14px; height: 14px; border-radius: 50%; text-align: center; line-height: 14px; font-size: 6pt; font-weight: 800 }
        .rk1 { background: #d4af37; color: #fff }
        .rk2 { background: #9ca3af; color: #fff }
        .rk3 { background: #b45309; color: #fff }
        .rkn { background: #e2e8f0; color: #475569 }

        /* Rate bar */
        .rb { display: inline-flex; align-items: center; gap: 3px; width: 100% }
        .rt { flex: 1; height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; min-width: 36px }
        .rf { height: 100%; border-radius: 2px }
        .c-g { background: #1a8754 } .t-g { color: #1a8754 }
        .c-a { background: #b87a14 } .t-a { color: #b87a14 }
        .c-r { background: #c84646 } .t-r { color: #c84646 }
        .rl { font-size: 6.5pt; font-weight: 800; min-width: 20px; text-align: right }

        .page-break { page-break-before: always }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 4px; margin-top: 10px; display: flex; justify-content: space-between; font-size: 6pt; color: #94a3b8 }
    </style>
</head>
<body>

<div class="controls">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
    <a class="btn btn-back" href="{{ route($reportRoute, array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'closer_id' => $closerFilter])) }}">← Back</a>
</div>

@php $paidRate = $kpis['total'] > 0 ? round(($kpis['paid'] / $kpis['total']) * 100, 1) : 0; @endphp

{{-- Header --}}
<div class="hdr">
    <div class="hdr-left">
        <div class="hdr-brand">taurus<span>·</span>mis</div>
        <div>
            <div class="hdr-title">{{ $teamLabel }} Team — Agent Performance Report</div>
            <div class="hdr-meta">{{ $dateFrom }} → {{ $dateTo }} &nbsp;·&nbsp; {{ $kpis['total'] }} sales &nbsp;·&nbsp; {{ $agentRows->count() + $pjcRows->count() }} agents</div>
        </div>
    </div>
    <div class="hdr-right">
        <div>{{ now()->format('F j, Y · g:i A') }}</div>
        <div class="hdr-conf">Confidential · Internal Use Only</div>
    </div>
</div>

{{-- KPI Row --}}
<div class="kpi-row">
    <div class="kpi k-tot"><div class="kn">{{ $kpis['total'] }}</div><div class="kl">Total Sales</div></div>
    <div class="kpi k-paid"><div class="kn">{{ $kpis['paid'] }}</div><div class="kl">Paid</div><div class="ks">collected</div></div>
    <div class="kpi k-appr"><div class="kn">{{ $kpis['approved'] }}</div><div class="kl">Approved</div><div class="ks">pending contract</div></div>
    <div class="kpi k-drft"><div class="kn">{{ $kpis['draft'] }}</div><div class="kl">Draft</div><div class="ks">banking set up</div></div>
    <div class="kpi k-niss"><div class="kn">{{ $kpis['not_issued'] }}</div><div class="kl">Not Issued</div></div>
    <div class="kpi k-decl"><div class="kn">{{ $kpis['declined'] }}</div><div class="kl">Declined</div></div>
    <div class="kpi k-rate"><div class="kn">{{ $paidRate }}%</div><div class="kl">Paid Rate</div><div class="ks">${{ number_format($kpis['total_premium'], 2) }}/mo</div></div>
</div>

@if($pjcRows->count() > 0)
<div class="sec">PJC Leaderboard <span>— {{ $pjcLabel }} ({{ $pjcRows->count() }})</span> <em>· Top: {{ $pjcRows->first()->agent_name }} {{ $pjcRows->first()->paid_rate }}%</em></div>
@include('admin.reports._peregrine-print-table', ['rows' => $pjcRows])
@endif

{{-- Closer Summary --}}
@if($agentRows->count() > 0)
<div class="sec">Closer Leaderboard <span>— Closers ({{ $agentRows->count() }})</span> <em>· Top: {{ $agentRows->first()->agent_name }} {{ $agentRows->first()->paid_rate }}%</em></div>
@include('admin.reports._peregrine-print-table', ['rows' => $agentRows])
@endif

{{-- Lead Breakdown --}}
<div class="page-break"></div>
<div class="sec" style="margin-top:0">Full Lead Breakdown <span>— every sale by agent</span></div>

@if($pjcRows->count() > 0)
    <span class="role-div role-pjc">{{ $pjcLabel }}</span>
    @foreach($pjcRows as $i => $row)
        @php $rc = $row->paid_rate >= 50 ? 'g' : ($row->paid_rate >= 20 ? 'a' : 'r'); @endphp
        <div class="ag-hdr ag-pjc">
            <div>
                <div class="ag-name">@if($i===0)★ @endif{{ $row->agent_name }}</div>
                <div class="ag-sub">{{ $pjcLabel }} · {{ $row->total }} sale(s) · {{ $row->paid_rate }}% paid</div>
            </div>
            <div class="ag-stats">
                <div class="ag-stat"><div class="an" style="color:#34c38f">{{ $row->paid }}</div><div class="al">Paid</div></div>
                <div class="ag-stat"><div class="an" style="color:#50a5f1">{{ $row->approved }}</div><div class="al">Apprv</div></div>
                <div class="ag-stat"><div class="an" style="color:#f1b44c">{{ $row->draft }}</div><div class="al">Draft</div></div>
                <div class="ag-stat"><div class="an" style="color:#fb923c">{{ $row->not_issued }}</div><div class="al">Not Iss</div></div>
                <div class="ag-stat"><div class="an" style="color:#f46a6a">{{ $row->declined }}</div><div class="al">Decl</div></div>
                <div class="ag-stat"><div class="an" style="color:#d4af37">${{ number_format($row->premium,2) }}</div><div class="al">Paid/mo</div></div>
            </div>
        </div>
        @include('admin.reports._peregrine-print-leads', ['row' => $row])
    @endforeach
@endif

@if($agentRows->count() > 0)
    <span class="role-div role-closer" style="margin-top:8px">{{ $closerLabel }}</span>
    @foreach($agentRows as $i => $row)
        @php $rc = $row->paid_rate >= 50 ? 'g' : ($row->paid_rate >= 20 ? 'a' : 'r'); @endphp
        <div class="ag-hdr ag-closer">
            <div>
                <div class="ag-name">@if($i===0)★ @endif{{ $row->agent_name }}</div>
                <div class="ag-sub">Closer · {{ $row->total }} sale(s) · {{ $row->paid_rate }}% paid</div>
            </div>
            <div class="ag-stats">
                <div class="ag-stat"><div class="an" style="color:#34c38f">{{ $row->paid }}</div><div class="al">Paid</div></div>
                <div class="ag-stat"><div class="an" style="color:#50a5f1">{{ $row->approved }}</div><div class="al">Apprv</div></div>
                <div class="ag-stat"><div class="an" style="color:#f1b44c">{{ $row->draft }}</div><div class="al">Draft</div></div>
                <div class="ag-stat"><div class="an" style="color:#fb923c">{{ $row->not_issued }}</div><div class="al">Not Iss</div></div>
                <div class="ag-stat"><div class="an" style="color:#f46a6a">{{ $row->declined }}</div><div class="al">Decl</div></div>
                <div class="ag-stat"><div class="an" style="color:#d4af37">${{ number_format($row->premium,2) }}</div><div class="al">Paid/mo</div></div>
            </div>
        </div>
        @include('admin.reports._peregrine-print-leads', ['row' => $row])
    @endforeach
@endif

<div class="footer">
    <span>Taurus MIS · {{ $teamLabel }} Performance Report · {{ $dateFrom }} – {{ $dateTo }}</span>
    <span>{{ now()->format('F j, Y · g:i A') }} · Confidential</span>
</div>

<script>window.addEventListener('load', function(){ window.print(); });</script>
</body>
</html>
