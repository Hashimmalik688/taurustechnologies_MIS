@extends('layouts.master')

@section('title')
    Peregrine Team Report
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }

        .rp-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.73rem }
        .rp-table thead th {
            padding:.5rem .65rem;font-size:.64rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.5px;color:var(--bs-surface-500,#64748b);
            background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
            white-space:nowrap;cursor:pointer;
        }
        .rp-table thead th:hover { background:rgba(212,175,55,.06) }
        .rp-table tbody td {
            padding:.45rem .65rem;border-bottom:1px solid rgba(0,0,0,.035);
            color:var(--bs-surface-900,#1e293b);vertical-align:middle;
        }
        .rp-table tbody tr:hover td { background:rgba(212,175,55,.04) }
        .rp-table tbody tr:last-child td { border-bottom:none }
        .rp-table tfoot td {
            padding:.5rem .65rem;border-top:2px solid rgba(0,0,0,.08);font-weight:700;
        }
        .rp-td-num { text-align:right;font-weight:600;font-variant-numeric:tabular-nums }
        .rp-th-num { text-align:right }
        .rp-td-name { font-weight:600 }

        .rp-badge {
            font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:10px;
            display:inline-block;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;
        }
        .rp-badge-sale     { background:rgba(52,195,143,.12);color:#1a8754 }
        .rp-badge-declined { background:rgba(244,106,106,.12);color:#c84646 }
        .rp-badge-pending  { background:rgba(241,180,76,.12);color:#b87a14 }
        .rp-badge-returned { background:rgba(251,146,60,.1);color:#c2410c }
        .rp-badge-default  { background:rgba(108,117,125,.08);color:#6c757d }

        .sec-pill {
            display:inline-flex;align-items:center;gap:.3rem;
            font-size:.68rem;font-weight:700;padding:.2rem .6rem;
            border-radius:20px;margin-bottom:.6rem;
        }
        .sec-pill-pjc  { background:rgba(80,165,241,.1);color:#1e6eb5 }
        .sec-pill-closer { background:rgba(52,195,143,.1);color:#1a8754 }
        .sec-pill-validator { background:rgba(212,175,55,.12);color:#92760d }

        .rate-bar-wrap { display:flex;align-items:center;gap:.4rem }
        .rate-bar { height:6px;border-radius:3px;flex:1;background:rgba(0,0,0,.06);overflow:hidden;min-width:40px }
        .rate-bar-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#34c38f,#1a8754);transition:width .4s }
        .rate-label { font-size:.68rem;font-weight:700;min-width:32px;text-align:right }

        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tbody td,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tfoot td { color:#e2e8f0 }
    </style>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-shield-alt"></i> Peregrine Team Report
        </h5>
        <a href="{{ route('settings.reports.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>

    {{-- Date Filter --}}
    <form method="GET" action="{{ route('settings.reports.peregrine-team-report') }}" class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-body" style="padding:.75rem">
            <div style="display:flex;gap:.55rem;align-items:flex-end;flex-wrap:wrap">
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff">
                </div>
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff">
                </div>
                <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.3rem .75rem">
                    <i class="bx bx-refresh" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Apply
                </button>
                <a href="{{ route('settings.reports.peregrine-team-report', ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                   class="pipe-pill" style="font-size:.72rem;padding:.3rem .75rem;font-weight:600">
                    <i class="bx bx-calendar-check" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> This Month
                </a>
                <a href="{{ route('settings.reports.peregrine-team-report', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                   class="pipe-pill" style="font-size:.72rem;padding:.3rem .75rem;font-weight:600">
                    <i class="bx bx-calendar" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Today
                </a>
            </div>
        </div>
    </form>

    {{-- KPI Summary --}}
    <div class="kpi-row" style="margin-bottom:.65rem">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-file k-icon"></i>
            <div class="k-val">{{ $teamTotals['total_leads'] }}</div>
            <div class="k-lbl">Total Leads</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-check-double k-icon"></i>
            <div class="k-val">{{ $teamTotals['total_sales'] }}</div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val">{{ $teamTotals['total_declined'] }}</div>
            <div class="k-lbl">Declined</div>
        </div>
        <div class="kpi-card k-gold ex-card">
            <i class="bx bx-trending-up k-icon"></i>
            <div class="k-val">{{ $teamTotals['conversion_rate'] }}%</div>
            <div class="k-lbl">Conversion Rate</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-time k-icon"></i>
            <div class="k-val">{{ $teamTotals['total_pending'] }}</div>
            <div class="k-lbl">Pending</div>
        </div>
    </div>

    {{-- ── Section 1: PJC Performance ─────────────────────────── --}}
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-hdr">
            <h6>
                <span class="sec-pill sec-pill-pjc"><i class="bx bx-edit-alt"></i> PJC Performance</span>
                <span style="font-size:.62rem;color:var(--bs-surface-400);font-weight:400">Peregrines Junior Closers — {{ $pjcRows->count() }} active · {{ $dateFrom }} → {{ $dateTo }}</span>
            </h6>
        </div>
        <div class="scroll-tbl">
            <table class="rp-table" id="pjcTable">
                <thead>
                    <tr>
                        <th>PJC Name</th>
                        <th class="rp-th-num">Submitted</th>
                        <th class="rp-th-num">With Closer</th>
                        <th class="rp-th-num">With Validator</th>
                        <th class="rp-th-num">Sales</th>
                        <th class="rp-th-num">Declined</th>
                        <th class="rp-th-num">Pending</th>
                        <th class="rp-th-num">Conv %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pjcRows as $row)
                        @php $conv = $row->total > 0 ? round(($row->sales / $row->total) * 100, 1) : 0; @endphp
                        <tr>
                            <td class="rp-td-name">{{ $row->pjc_name }}</td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-default">{{ $row->total }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-default">{{ $row->with_closer }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-default">{{ $row->with_validator }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-sale">{{ $row->sales }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-declined">{{ $row->declined }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-pending">{{ $row->pending }}</span></td>
                            <td class="rp-td-num">
                                <div class="rate-bar-wrap">
                                    <div class="rate-bar"><div class="rate-bar-fill" style="width:{{ $conv }}%"></div></div>
                                    <span class="rate-label">{{ $conv }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align:center;padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem">
                            <i class="bx bx-info-circle"></i> No PJC activity in this date range
                        </td></tr>
                    @endforelse
                </tbody>
                @if($pjcRows->count() > 0)
                <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="rp-td-num">{{ $pjcRows->sum('total') }}</td>
                        <td class="rp-td-num">{{ $pjcRows->sum('with_closer') }}</td>
                        <td class="rp-td-num">{{ $pjcRows->sum('with_validator') }}</td>
                        <td class="rp-td-num">{{ $pjcRows->sum('sales') }}</td>
                        <td class="rp-td-num">{{ $pjcRows->sum('declined') }}</td>
                        <td class="rp-td-num">{{ $pjcRows->sum('pending') }}</td>
                        <td class="rp-td-num">
                            @php $tot = $pjcRows->sum('total'); $sal = $pjcRows->sum('sales'); @endphp
                            {{ $tot > 0 ? round(($sal / $tot) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── Section 2: Closer Performance ──────────────────────── --}}
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-hdr">
            <h6>
                <span class="sec-pill sec-pill-closer"><i class="bx bx-user-pin"></i> Closer Performance</span>
                <span style="font-size:.62rem;color:var(--bs-surface-400);font-weight:400">Peregrine Closers — {{ $closerRows->count() }} active</span>
            </h6>
        </div>
        <div class="scroll-tbl">
            <table class="rp-table" id="closerTable">
                <thead>
                    <tr>
                        <th>Closer Name</th>
                        <th class="rp-th-num">Received</th>
                        <th class="rp-th-num">Sent to Validator</th>
                        <th class="rp-th-num">Sales</th>
                        <th class="rp-th-num">Returned</th>
                        <th class="rp-th-num">Declined</th>
                        <th class="rp-th-num">Conv %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($closerRows as $row)
                        <tr>
                            <td class="rp-td-name">{{ $row->closer_name }}</td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-default">{{ $row->total_received }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-default">{{ $row->sent_to_validator }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-sale">{{ $row->sales }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-returned">{{ $row->returned }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-declined">{{ $row->declined }}</span></td>
                            <td class="rp-td-num">
                                <div class="rate-bar-wrap">
                                    <div class="rate-bar"><div class="rate-bar-fill" style="width:{{ $row->conversion_rate }}%"></div></div>
                                    <span class="rate-label">{{ $row->conversion_rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem">
                            <i class="bx bx-info-circle"></i> No closer activity in this date range
                        </td></tr>
                    @endforelse
                </tbody>
                @if($closerRows->count() > 0)
                <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="rp-td-num">{{ $closerRows->sum('total_received') }}</td>
                        <td class="rp-td-num">{{ $closerRows->sum('sent_to_validator') }}</td>
                        <td class="rp-td-num">{{ $closerRows->sum('sales') }}</td>
                        <td class="rp-td-num">{{ $closerRows->sum('returned') }}</td>
                        <td class="rp-td-num">{{ $closerRows->sum('declined') }}</td>
                        <td class="rp-td-num">
                            @php $tot = $closerRows->sum('total_received'); $sal = $closerRows->sum('sales'); @endphp
                            {{ $tot > 0 ? round(($sal / $tot) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── Section 3: Validator Performance ───────────────────── --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6>
                <span class="sec-pill sec-pill-validator"><i class="bx bx-check-shield"></i> Peregrines Validator Performance</span>
                <span style="font-size:.62rem;color:var(--bs-surface-400);font-weight:400">{{ $validatorRows->count() }} active</span>
            </h6>
        </div>
        <div class="scroll-tbl">
            <table class="rp-table" id="validatorTable">
                <thead>
                    <tr>
                        <th>Validator Name</th>
                        <th class="rp-th-num">Received</th>
                        <th class="rp-th-num">Marked Sale</th>
                        <th class="rp-th-num">Returned to Closer</th>
                        <th class="rp-th-num">Declined</th>
                        <th class="rp-th-num">Pending HO</th>
                        <th class="rp-th-num">Conv %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($validatorRows as $row)
                        <tr>
                            <td class="rp-td-name">{{ $row->validator_name }}</td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-default">{{ $row->total_received }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-sale">{{ $row->marked_sale }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-returned">{{ $row->returned_closer }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-declined">{{ $row->declined }}</span></td>
                            <td class="rp-td-num"><span class="rp-badge rp-badge-pending">{{ $row->pending_ho }}</span></td>
                            <td class="rp-td-num">
                                <div class="rate-bar-wrap">
                                    <div class="rate-bar"><div class="rate-bar-fill" style="width:{{ $row->conversion_rate }}%"></div></div>
                                    <span class="rate-label">{{ $row->conversion_rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem">
                            <i class="bx bx-info-circle"></i> No validator activity in this date range
                        </td></tr>
                    @endforelse
                </tbody>
                @if($validatorRows->count() > 0)
                <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="rp-td-num">{{ $validatorRows->sum('total_received') }}</td>
                        <td class="rp-td-num">{{ $validatorRows->sum('marked_sale') }}</td>
                        <td class="rp-td-num">{{ $validatorRows->sum('returned_closer') }}</td>
                        <td class="rp-td-num">{{ $validatorRows->sum('declined') }}</td>
                        <td class="rp-td-num">{{ $validatorRows->sum('pending_ho') }}</td>
                        <td class="rp-td-num">
                            @php $tot = $validatorRows->sum('total_received'); $sal = $validatorRows->sum('marked_sale'); @endphp
                            {{ $tot > 0 ? round(($sal / $tot) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── Section 4: Sales Detail ─────────────────────────────── --}}
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-hdr">
            <h6>
                <span class="sec-pill sec-pill-sale"><i class="bx bx-dollar-circle"></i> Sales</span>
                <span style="font-size:.62rem;color:var(--bs-surface-400);font-weight:400">{{ $salesLeads->count() }} sale(s) · {{ $dateFrom }} → {{ $dateTo }}</span>
            </h6>
        </div>
        <div class="scroll-tbl">
            <table class="rp-table" id="salesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Policy Type</th>
                        <th class="rp-th-num">Coverage</th>
                        <th class="rp-th-num">Premium/mo</th>
                        <th>Closed By</th>
                        <th>Validator</th>
                        <th>Status</th>
                        <th class="rp-th-num">Sale Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesLeads as $i => $lead)
                        <tr>
                            <td style="font-size:.72rem;color:var(--bs-surface-400)">{{ $i + 1 }}</td>
                            <td class="rp-td-name">{{ $lead->cn_name ?? '—' }}</td>
                            <td style="font-size:.75rem">{{ $lead->policy_type ?? '—' }}</td>
                            <td class="rp-td-num">${{ number_format($lead->coverage_amount ?? 0) }}</td>
                            <td class="rp-td-num">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                            <td style="font-size:.75rem">{{ $lead->assignedCloser?->name ?? $lead->closer_name ?? '—' }}</td>
                            <td style="font-size:.75rem">{{ $lead->assignedValidator?->name ?? '—' }}</td>
                            <td>@php $statusBadge = match($lead->status ?? '') { 'sale' => 'rp-badge-sale', 'declined' => 'rp-badge-declined', 'chargeback' => 'rp-badge-declined', 'pending' => 'rp-badge-pending', 'returned' => 'rp-badge-returned', default => 'rp-badge-default' }; @endphp<span class="rp-badge {{ $statusBadge }}">{{ $lead->status ?? '—' }}</span></td>
                            <td class="rp-td-num" style="white-space:nowrap;font-size:.75rem">{{ $lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" style="text-align:center;padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem"><i class="bx bx-info-circle"></i> No sales in this period</td></tr>
                    @endforelse
                </tbody>
                @if($salesLeads->count() > 0)
                <tfoot>
                    <tr class="rp-total-row">
                        <td colspan="3" style="font-size:.72rem;font-weight:600">Totals</td>
                        <td class="rp-td-num">${{ number_format($salesLeads->sum('coverage_amount')) }}</td>
                        <td class="rp-td-num">${{ number_format($salesLeads->sum('monthly_premium'), 2) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    ['pjcTable', 'closerTable', 'validatorTable', 'salesTable'].forEach(function (tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const headers = table.querySelectorAll('thead th');
        headers.forEach(function (th, colIdx) {
            th.addEventListener('click', function () {
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const asc = th.dataset.sortDir !== 'asc';
                th.dataset.sortDir = asc ? 'asc' : 'desc';
                headers.forEach(function (h) { if (h !== th) delete h.dataset.sortDir; });
                rows.sort(function (a, b) {
                    const aVal = a.children[colIdx]?.textContent.trim().replace(/[%,]/g, '') || '';
                    const bVal = b.children[colIdx]?.textContent.trim().replace(/[%,]/g, '') || '';
                    const aNum = parseFloat(aVal), bNum = parseFloat(bVal);
                    if (!isNaN(aNum) && !isNaN(bNum)) return asc ? aNum - bNum : bNum - aNum;
                    return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                });
                rows.forEach(function (row) { tbody.appendChild(row); });
            });
        });
    });
});
</script>
@endsection
