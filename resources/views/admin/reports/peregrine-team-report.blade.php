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
        .sec-pill-sale { background:rgba(52,195,143,.1);color:#1a8754 }

        .rate-bar-wrap { display:flex;align-items:center;gap:.4rem }
        .rate-bar { height:6px;border-radius:3px;flex:1;background:rgba(0,0,0,.06);overflow:hidden;min-width:40px }
        .rate-bar-fill { height:100%;border-radius:3px;background:linear-gradient(90deg,#34c38f,#1a8754);transition:width .4s }
        .rate-label { font-size:.68rem;font-weight:700;min-width:32px;text-align:right }

        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tbody td,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tfoot td { color:#e2e8f0 }

        /* Disposition KPI row — smaller, compact cards in a single line */
        .disp-kpi-row { flex-wrap:wrap }
        .disp-kpi-row .kpi-card { flex:0 0 auto;min-width:70px;max-width:100px;padding:.4rem .35rem;cursor:pointer }
        .disp-kpi-row .kpi-card .k-icon { font-size:.7rem;margin-bottom:.1rem }
        .disp-kpi-row .kpi-card .k-val { font-size:.9rem }
        .disp-kpi-row .kpi-card .k-lbl { font-size:.5rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis }
        .disp-kpi-row .kpi-card.active { border:2px solid var(--bs-gold,#d4af37);transform:translateY(-2px);box-shadow:0 4px 12px rgba(212,175,55,.25) }
        .disp-kpi-row .kpi-card.disp-all { min-width:55px;max-width:65px }

        /* Clickable table rows */
        .rp-clickable { cursor:pointer }
        .rp-clickable:hover td { background:rgba(212,175,55,.08) !important }

        /* Modal */
        .modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center }
        .modal-overlay.show { display:flex }
        .modal-container { background:var(--bs-card-bg,#fff);border-radius:12px;width:95%;max-width:950px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.25);border:1px solid rgba(0,0,0,.08) }
        .modal-hdr { display:flex;align-items:center;justify-content:space-between;padding:.7rem 1rem;border-bottom:1px solid rgba(0,0,0,.06) }
        .modal-hdr h6 { margin:0;font-size:.9rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .modal-hdr h6 .modal-count { font-size:.7rem;font-weight:400;color:var(--bs-surface-500) }
        .modal-close { background:none;border:none;font-size:1.3rem;line-height:1;cursor:pointer;color:var(--bs-surface-500);padding:.2rem .4rem;border-radius:6px }
        .modal-close:hover { background:rgba(0,0,0,.06);color:var(--bs-surface-900) }
        .modal-body { padding:.5rem 1rem 1rem;overflow-y:auto;flex:1 }
        .modal-body .rp-table tbody tr:hover td { background:rgba(212,175,55,.06) }

        .disp-badge { font-size:.58rem;font-weight:700;padding:.1rem .4rem;border-radius:8px;white-space:nowrap }
        .disp-badge-red { background:rgba(244,106,106,.12);color:#c84646 }
        .disp-badge-teal { background:rgba(80,165,241,.12);color:#2b81c9 }
        .disp-badge-gold { background:rgba(212,175,55,.12);color:#92760d }
        .disp-badge-purple { background:rgba(124,105,239,.12);color:#5b49c7 }
        .disp-badge-warn { background:rgba(241,180,76,.12);color:#b87a14 }
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
    <form method="GET" action="{{ route('settings.reports.peregrine-team-report') }}" class="ex-card sec-card" style="margin-bottom:.65rem;background:rgba(212,175,55,.06);border:1px solid rgba(212,175,55,.15)">
        <div class="sec-body" style="padding:.9rem 1rem">
            <div style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap">
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.3rem;display:block;font-size:.7rem;font-weight:700;color:var(--bs-gold,#92760d)">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" style="font-size:.85rem;padding:.45rem .65rem;border:1px solid rgba(212,175,55,.3);border-radius:8px;background:#fff;font-weight:600;min-width:160px">
                </div>
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.3rem;display:block;font-size:.7rem;font-weight:700;color:var(--bs-gold,#92760d)">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" style="font-size:.85rem;padding:.45rem .65rem;border:1px solid rgba(212,175,55,.3);border-radius:8px;background:#fff;font-weight:600;min-width:160px">
                </div>
                <button type="submit" class="pipe-pill-apply" style="font-size:.82rem;padding:.45rem 1rem">
                    <i class="bx bx-refresh" style="font-size:.9rem;vertical-align:middle;margin-right:.2rem"></i> Apply
                </button>
                <a href="{{ route('settings.reports.peregrine-team-report', ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                   class="pipe-pill" style="font-size:.82rem;padding:.45rem .9rem;font-weight:600">
                    <i class="bx bx-calendar-check" style="font-size:.9rem;vertical-align:middle;margin-right:.2rem"></i> This Month
                </a>
                <a href="{{ route('settings.reports.peregrine-team-report', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                   class="pipe-pill" style="font-size:.82rem;padding:.45rem .9rem;font-weight:600">
                    <i class="bx bx-calendar" style="font-size:.9rem;vertical-align:middle;margin-right:.2rem"></i> Today
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

    {{-- Disposition KPI Row --}}
    <div class="kpi-row disp-kpi-row" style="margin-bottom:.5rem" id="dispKpiRow">
        <div class="kpi-card k-blue ex-card disp-all active" data-disp="all" title="Show all">
            <div class="k-val">{{ $teamTotals['total_leads'] }}</div>
            <div class="k-lbl">All</div>
        </div>
        <div class="kpi-card k-red ex-card" data-disp="dnq_age" title="DNQ-Age">
            <div class="k-val">{{ $dispositionCounts['dnq_age'] }}</div>
            <div class="k-lbl">DNQ Age</div>
        </div>
        <div class="kpi-card k-red ex-card" data-disp="dnq_health" title="DNQ-Health">
            <div class="k-val">{{ $dispositionCounts['dnq_health'] }}</div>
            <div class="k-lbl">DNQ Health</div>
        </div>
        <div class="kpi-card k-purple ex-card" data-disp="dnc" title="DNC">
            <div class="k-val">{{ $dispositionCounts['dnc'] }}</div>
            <div class="k-lbl">DNC</div>
        </div>
        <div class="kpi-card k-red ex-card" data-disp="poa" title="POA">
            <div class="k-val">{{ $dispositionCounts['poa'] }}</div>
            <div class="k-lbl">POA</div>
        </div>
        <div class="kpi-card k-warn ex-card" data-disp="not_interested" title="Not Interested / No Pitch">
            <div class="k-val">{{ $dispositionCounts['not_interested'] }}</div>
            <div class="k-lbl">Not Intrst</div>
        </div>
        <div class="kpi-card k-warn ex-card" data-disp="cannot_afford" title="Cannot Afford">
            <div class="k-val">{{ $dispositionCounts['cannot_afford'] }}</div>
            <div class="k-lbl">No Afford</div>
        </div>
        <div class="kpi-card k-red ex-card" data-disp="declined_ssn" title="Declined SSN">
            <div class="k-val">{{ $dispositionCounts['declined_ssn'] }}</div>
            <div class="k-lbl">Dcln SSN</div>
        </div>
        <div class="kpi-card k-red ex-card" data-disp="declined_banking" title="Declined Banking">
            <div class="k-val">{{ $dispositionCounts['declined_banking'] }}</div>
            <div class="k-lbl">Dcln Bank</div>
        </div>
        <div class="kpi-card k-warn ex-card" data-disp="no_answer" title="No Answer">
            <div class="k-val">{{ $dispositionCounts['no_answer'] }}</div>
            <div class="k-lbl">No Answer</div>
        </div>
        <div class="kpi-card k-red ex-card" data-disp="declined_simple" title="Declined (Simple)">
            <div class="k-val">{{ $dispositionCounts['declined_simple'] }}</div>
            <div class="k-lbl">Declined</div>
        </div>
        <div class="kpi-card k-teal ex-card" data-disp="callback" title="Pending Callback">
            <div class="k-val">{{ $dispositionCounts['callback'] }}</div>
            <div class="k-lbl">Callback</div>
        </div>
        <div class="kpi-card k-teal ex-card" data-disp="future_potential" title="Future Potential">
            <div class="k-val">{{ $dispositionCounts['future_potential'] }}</div>
            <div class="k-lbl">Futr Pot</div>
        </div>
        <div class="kpi-card k-teal ex-card" data-disp="pending_banking" title="Pending Banking">
            <div class="k-val">{{ $dispositionCounts['pending_banking'] }}</div>
            <div class="k-lbl">Pnd Bank</div>
        </div>
        <div class="kpi-card k-teal ex-card" data-disp="pending_validation" title="Pending Validation">
            <div class="k-val">{{ $dispositionCounts['pending_validation'] }}</div>
            <div class="k-lbl">Pnd Valid</div>
        </div>
        <div class="kpi-card k-gold ex-card" data-disp="home_office" title="Sent to Home Office">
            <div class="k-val">{{ $dispositionCounts['home_office'] }}</div>
            <div class="k-lbl">Home Off</div>
        </div>
        <div class="kpi-card k-warn ex-card" data-disp="returned" title="Returned to Closer">
            <div class="k-val">{{ $dispositionCounts['returned'] }}</div>
            <div class="k-lbl">Returned</div>
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
                        <tr class="rp-clickable" data-pjc-id="{{ $row->pjc_id }}" data-pjc-name="{{ e($row->pjc_name) }}" onclick="openPersonModal('pjc', '{{ $row->pjc_id }}', '{{ e($row->pjc_name) }}')" title="Click for details">
                            <td class="rp-td-name" style="color:#1e6eb5;cursor:pointer">{{ $row->pjc_name }}</td>
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
                        <tr class="rp-clickable" data-closer-id="{{ $row->closer_id }}" data-closer-name="{{ e($row->closer_name) }}" onclick="openPersonModal('closer', '{{ $row->closer_id }}', '{{ e($row->closer_name) }}')" title="Click for details">
                            <td class="rp-td-name" style="color:#1a8754;cursor:pointer">{{ $row->closer_name }}</td>
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
                        <tr class="rp-clickable" data-validator-id="{{ $row->validator_id }}" data-validator-name="{{ e($row->validator_name) }}" onclick="openPersonModal('validator', '{{ $row->validator_id }}', '{{ e($row->validator_name) }}')" title="Click for details">
                            <td class="rp-td-name" style="color:#92760d;cursor:pointer">{{ $row->validator_name }}</td>
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
                        <tr data-lead-id="{{ $lead->id }}">
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

    {{-- Detail Modal --}}
    <div class="modal-overlay" id="leadDetailModal" onclick="if(event.target===this)closeModal()">
        <div class="modal-container">
            <div class="modal-hdr">
                <h6>
                    <i class="bx bx-user-detail"></i> <span id="modalTitle">Lead Details</span>
                    <span class="modal-count" id="modalCount"></span>
                </h6>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="scroll-tbl">
                    <table class="rp-table" id="modalLeadTable">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th class="rp-th-num">Premium/mo</th>
                                <th>Status</th>
                                <th>PJC</th>
                                <th>Closer</th>
                                <th>Validator</th>
                                <th>Disposition</th>
                            </tr>
                        </thead>
                        <tbody id="modalLeadBody"></tbody>
                    </table>
                </div>
                <div id="modalEmpty" style="display:none;text-align:center;padding:2rem;color:var(--bs-surface-400);font-size:.75rem">
                    <i class="bx bx-info-circle" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
                    No leads found.
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
const allLeads = @json($allLeadsDetail);

var currentDisp = 'all';

var dispMatchers = {
    all:             function() { return true; },
    dnq_age:         function(l) { return (l.decline_reason || '').indexOf('DNQ-Age') !== -1; },
    dnq_health:      function(l) { return (l.decline_reason || '').indexOf('DNQ-Health') !== -1; },
    dnc:             function(l) { return (l.decline_reason || '').indexOf('DNC') !== -1; },
    poa:             function(l) { return (l.decline_reason || '').indexOf('POA') !== -1; },
    not_interested:  function(l) { var r = l.decline_reason || ''; return r.indexOf('Not Interested') !== -1 || r.indexOf('No Pitch') !== -1; },
    cannot_afford:   function(l) { return (l.decline_reason || '').indexOf('Cannot Afford') !== -1; },
    declined_ssn:    function(l) { return (l.decline_reason || '').indexOf('Declined SSN') !== -1; },
    declined_banking: function(l) { return (l.decline_reason || '').indexOf('Declined Banking') !== -1; },
    no_answer:       function(l) { return (l.decline_reason || '').indexOf('No Answer') !== -1; },
    declined_simple: function(l) { return (l.decline_reason || '') === 'Declined'; },
    callback:        function(l) { return (l.pending_reason || '') === 'Pending:Callback'; },
    future_potential: function(l) { return (l.pending_reason || '') === 'Pending:Future Potential'; },
    pending_banking:  function(l) { return (l.pending_reason || '') === 'Pending:Pending Banking'; },
    pending_validation: function(l) { return (l.pending_reason || '') === 'Pending:Pending Validation'; },
    home_office:     function(l) { return (l.pending_reason || '') === 'Pending:Sent to Home Office'; },
    returned:        function(l) { return l.status === 'returned'; }
};

(function() {

    function filterTables() {
        var matcher = dispMatchers[currentDisp] || dispMatchers['all'];
        ['pjcTable', 'closerTable', 'validatorTable', 'salesTable'].forEach(function(tableId) {
            var table = document.getElementById(tableId);
            if (!table) return;
            var tbody = table.querySelector('tbody');
            if (!tbody) return;
            var rows = tbody.querySelectorAll('tr:not(.disp-empty-row)');
            var visible = 0;
            rows.forEach(function(row) {
                if (currentDisp === 'all') {
                    row.style.display = '';
                    visible++;
                    return;
                }
                var pjcId = row.dataset.pjcId || null;
                var closerId = row.dataset.closerId || null;
                var validatorId = row.dataset.validatorId || null;
                var leadId = row.dataset.leadId || null;
                var leadFilter = null;

                if (pjcId) {
                    var pjcName = row.dataset.pjcName || '';
                    leadFilter = function(l) { return String(l.verified_by) === String(pjcId) || String(l.account_verified_by || '') === String(pjcName); };
                } else if (closerId) {
                    leadFilter = function(l) { return String(l.managed_by) === String(closerId); };
                } else if (validatorId) {
                    leadFilter = function(l) { return String(l.assigned_validator_id) === String(validatorId); };
                } else if (leadId) {
                    leadFilter = function(l) { return String(l.id) === String(leadId); };
                }

                if (leadFilter) {
                    var personLeads = allLeads.filter(leadFilter);
                    var hasMatch = personLeads.some(matcher);
                    row.style.display = hasMatch ? '' : 'none';
                    if (hasMatch) visible++;
                } else {
                    row.style.display = '';
                    visible++;
                }
            });
            var existingEmpty = tbody.querySelector('.disp-empty-row');
            if (existingEmpty) { existingEmpty.remove(); }
            if (visible === 0 && rows.length > 0) {
                var emptyRow = document.createElement('tr');
                emptyRow.className = 'disp-empty-row';
                var td = document.createElement('td');
                var firstRow = tbody.querySelector('tr');
                td.colSpan = firstRow ? firstRow.children.length : 5;
                td.style.cssText = 'text-align:center;padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem';
                td.innerHTML = '<i class="bx bx-info-circle"></i> No entries match this disposition';
                emptyRow.appendChild(td);
                tbody.appendChild(emptyRow);
            }
        });
    }

    // Wire up after DOM ready
    function initFilters() {
        var cards = document.querySelectorAll('#dispKpiRow .kpi-card');
        cards.forEach(function(card) {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                currentDisp = this.dataset.disp;
                cards.forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');
                filterTables();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFilters);
    } else {
        initFilters();
    }
})();

// ── Modal ──
function openPersonModal(type, id, name) {
    var leads;
    if (type === 'pjc') {
        leads = allLeads.filter(function(l) {
            return String(l.verified_by) === String(id) || String(l.account_verified_by) === String(name);
        });
    } else if (type === 'closer') {
        leads = allLeads.filter(function(l) {
            return String(l.managed_by) === String(id);
        });
    } else if (type === 'validator') {
        leads = allLeads.filter(function(l) {
            return String(l.assigned_validator_id) === String(id);
        });
    } else {
        return;
    }

    if (currentDisp !== 'all') {
        var matcher = dispMatchers[currentDisp];
        if (matcher) {
            leads = leads.filter(matcher);
        }
    }

    var typeLabel = type === 'pjc' ? 'PJC' : type === 'closer' ? 'Closer' : 'Validator';
    document.getElementById('modalTitle').textContent = name + ' — ' + typeLabel;
    document.getElementById('modalCount').textContent = '(' + leads.length + ' lead' + (leads.length !== 1 ? 's' : '') + ')';

    var tbody = document.getElementById('modalLeadBody');
    var empty = document.getElementById('modalEmpty');
    tbody.innerHTML = '';

    if (leads.length === 0) {
        empty.style.display = 'block';
    } else {
        empty.style.display = 'none';
        leads.forEach(function(l) {
            var premium = l.monthly_premium ? '$' + Number(l.monthly_premium).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '—';
            var statusBadge = statusBadgeHtml(l.status || '');
            var pjcName = (l.verifier && l.verifier.name) ? l.verifier.name : (l.account_verified_by || '—');
            var closerName = (l.assigned_closer && l.assigned_closer.name) ? l.assigned_closer.name : (l.closer_name || '—');
            var validatorName = (l.assigned_validator && l.assigned_validator.name) ? l.assigned_validator.name : '—';
            var disposition = getDispLabel(l);

            var tr = document.createElement('tr');
            tr.innerHTML = '<td class="rp-td-name">' + escHtml(l.cn_name || '—') + '</td>' +
                '<td class="rp-td-num">' + premium + '</td>' +
                '<td>' + statusBadge + '</td>' +
                '<td style="font-size:.72rem">' + escHtml(pjcName) + '</td>' +
                '<td style="font-size:.72rem">' + escHtml(closerName) + '</td>' +
                '<td style="font-size:.72rem">' + escHtml(validatorName) + '</td>' +
                '<td>' + disposition + '</td>';
            tbody.appendChild(tr);
        });
    }

    document.getElementById('leadDetailModal').classList.add('show');
}

function closeModal() {
    document.getElementById('leadDetailModal').classList.remove('show');
}

function getDispLabel(lead) {
    var dr = lead.decline_reason || '';
    var pr = lead.pending_reason || '';
    var status = lead.status || '';

    if (dr) {
        var short = dr.replace(/^(Failed:|Declined:)/, '');
        var cls = 'disp-badge-red';
        if (short.indexOf('DNQ') !== -1) cls = 'disp-badge-red';
        else if (short === 'DNC') cls = 'disp-badge-purple';
        else if (short === 'POA') cls = 'disp-badge-red';
        else if (short.indexOf('Not Interested') !== -1 || short.indexOf('No Pitch') !== -1) cls = 'disp-badge-warn';
        else if (short.indexOf('Cannot Afford') !== -1) cls = 'disp-badge-warn';
        else if (short.indexOf('Declined SSN') !== -1) cls = 'disp-badge-red';
        else if (short.indexOf('Declined Banking') !== -1) cls = 'disp-badge-red';
        else if (short.indexOf('No Answer') !== -1) cls = 'disp-badge-warn';
        else if (short === 'Declined') cls = 'disp-badge-red';
        return '<span class="disp-badge ' + cls + '">' + escHtml(short) + '</span>';
    }
    if (pr) {
        var short = pr.replace(/^Pending:/, '');
        var cls = 'disp-badge-teal';
        if (short === 'Sent to Home Office') cls = 'disp-badge-gold';
        return '<span class="disp-badge ' + cls + '">' + escHtml(short) + '</span>';
    }
    if (status === 'returned') return '<span class="disp-badge disp-badge-warn">Returned</span>';
    if (status === 'sale') return '<span class="disp-badge" style="background:rgba(52,195,143,.12);color:#1a8754">Sale</span>';
    return '<span style="color:var(--bs-surface-400);font-size:.65rem">—</span>';
}

function statusBadgeHtml(status) {
    var cls = 'rp-badge-default';
    if (status === 'sale') cls = 'rp-badge-sale';
    else if (status === 'declined') cls = 'rp-badge-declined';
    else if (status === 'pending') cls = 'rp-badge-pending';
    else if (status === 'returned') cls = 'rp-badge-returned';
    return '<span class="rp-badge ' + cls + '">' + escHtml(status) + '</span>';
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Table sorting ──
document.addEventListener('DOMContentLoaded', function () {
    ['pjcTable', 'closerTable', 'validatorTable', 'salesTable'].forEach(function (tableId) {
        var table = document.getElementById(tableId);
        if (!table) return;
        var headers = table.querySelectorAll('thead th');
        headers.forEach(function (th, colIdx) {
            th.addEventListener('click', function () {
                var tbody = table.querySelector('tbody');
                if (!tbody) return;
                var rows = Array.from(tbody.querySelectorAll('tr:not(.disp-empty-row)'));
                var asc = th.dataset.sortDir !== 'asc';
                th.dataset.sortDir = asc ? 'asc' : 'desc';
                headers.forEach(function (h) { if (h !== th) delete h.dataset.sortDir; });
                rows.sort(function (a, b) {
                    var aVal = a.children[colIdx]?.textContent.trim().replace(/[%,]/g, '') || '';
                    var bVal = b.children[colIdx]?.textContent.trim().replace(/[%,]/g, '') || '';
                    var aNum = parseFloat(aVal), bNum = parseFloat(bVal);
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
