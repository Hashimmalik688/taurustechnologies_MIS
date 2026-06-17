@extends('layouts.master')

@section('title')
    Peregrine Sales Report
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
        .rp-table tfoot td { padding:.5rem .65rem;border-top:2px solid rgba(0,0,0,.08);font-weight:700 }
        .rp-td-num { text-align:right;font-weight:600;font-variant-numeric:tabular-nums }
        .rp-th-num { text-align:right }
        .rp-td-name { font-weight:600 }

        .rp-badge {
            font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:10px;
            display:inline-block;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;
        }
        .rp-badge-paid             { background:rgba(52,195,143,.14);color:#1a8754 }
        .rp-badge-pending-contract { background:rgba(80,165,241,.14);color:#1e6eb5 }
        .rp-badge-pending-draft    { background:rgba(241,180,76,.14);color:#b87a14 }
        .rp-badge-declined         { background:rgba(244,106,106,.14);color:#c84646 }
        .rp-badge-sale             { background:rgba(124,105,239,.12);color:#5b49c7 }
        .rp-badge-default          { background:rgba(108,117,125,.08);color:#6c757d }

        .sec-pill {
            display:inline-flex;align-items:center;gap:.3rem;
            font-size:.68rem;font-weight:700;padding:.2rem .6rem;
            border-radius:20px;margin-bottom:.6rem;
        }
        .sec-pill-sales { background:rgba(52,195,143,.1);color:#1a8754 }

        /* Filter bar */
        .filter-bar { display:flex;gap:.6rem;align-items:flex-end;flex-wrap:wrap }
        .filter-bar label { font-size:.7rem;font-weight:700;color:var(--bs-gold,#92760d);display:block;margin-bottom:.25rem }
        .filter-bar select, .filter-bar input[type=date] {
            font-size:.82rem;padding:.4rem .65rem;border:1px solid rgba(212,175,55,.3);
            border-radius:8px;background:#fff;font-weight:500;min-width:140px;
        }

        /* Status tooltip */
        .status-info-wrap { display:inline-flex;align-items:center;gap:.3rem }
        .info-icon {
            display:inline-flex;align-items:center;justify-content:center;
            width:15px;height:15px;border-radius:50%;background:rgba(100,116,139,.18);
            color:var(--bs-surface-500);font-size:.62rem;cursor:pointer;flex-shrink:0;
        }
        .info-icon:hover { background:rgba(212,175,55,.25);color:#92760d }

        #statusInfoPopover {
            display:none;position:fixed;z-index:9999;
            background:#1e293b;color:#e2e8f0;font-size:.68rem;font-weight:400;
            padding:.65rem .85rem;border-radius:10px;min-width:240px;max-width:300px;
            white-space:normal;line-height:1.6;
            box-shadow:0 10px 32px rgba(0,0,0,.4);
            pointer-events:none;
        }
        #statusInfoPopover.show { display:block }
        #statusInfoPopover strong { color:#d4af37 }
        #statusInfoPopover p { margin:.3rem 0 0 }
        #statusInfoPopover p:first-child { margin-top:0 }

        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tbody td,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tfoot td { color:#e2e8f0 }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .filter-bar select,
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .filter-bar input[type=date] {
            background:rgba(15,23,42,.7);color:#e2e8f0;border-color:rgba(212,175,55,.25);
        }
    </style>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-dollar-circle"></i> Peregrine Sales Report
        </h5>
        <a href="{{ route('settings.reports.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('settings.reports.peregrine-sales-report') }}"
          class="ex-card sec-card" style="margin-bottom:.65rem;background:rgba(212,175,55,.06);border:1px solid rgba(212,175,55,.15)">
        <div class="sec-body" style="padding:.9rem 1rem">
            <div class="filter-bar">
                <div>
                    <label>From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div>
                    <label>To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}">
                </div>
                <div>
                    <label>Closer</label>
                    <select name="closer_id">
                        <option value="">All Closers</option>
                        @foreach($closerOptions as $c)
                            <option value="{{ $c['id'] }}" @selected($closerFilter == $c['id'])>{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Validator</label>
                    <select name="validator_id">
                        <option value="">All Validators</option>
                        @foreach($validatorOptions as $v)
                            <option value="{{ $v['id'] }}" @selected($validatorFilter == $v['id'])>{{ $v['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>PJC</label>
                    <select name="pjc_id">
                        <option value="">All PJCs</option>
                        @foreach($pjcOptions as $p)
                            <option value="{{ $p['id'] }}" @selected($pjcFilter == $p['id'])>{{ $p['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Sale Status</label>
                    <select name="sale_status">
                        <option value="">All Statuses</option>
                        <option value="paid"             @selected($statusFilter === 'paid')>Paid</option>
                        <option value="pending_contract" @selected($statusFilter === 'pending_contract')>Pending Contract</option>
                        <option value="pending_draft"    @selected($statusFilter === 'pending_draft')>Pending Draft</option>
                        <option value="declined"         @selected($statusFilter === 'declined')>Declined</option>
                    </select>
                </div>
                <div style="display:flex;gap:.5rem;align-items:flex-end">
                    <button type="submit" class="pipe-pill-apply" style="font-size:.82rem;padding:.4rem 1rem">
                        <i class="bx bx-refresh" style="font-size:.9rem;vertical-align:middle;margin-right:.2rem"></i> Apply
                    </button>
                    <a href="{{ route('settings.reports.peregrine-sales-report', ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                       class="pipe-pill" style="font-size:.82rem;padding:.4rem .9rem;font-weight:600">
                        <i class="bx bx-calendar-check" style="font-size:.9rem;vertical-align:middle;margin-right:.2rem"></i> This Month
                    </a>
                    <a href="{{ route('settings.reports.peregrine-sales-report', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                       class="pipe-pill" style="font-size:.82rem;padding:.4rem .9rem;font-weight:600">
                        <i class="bx bx-calendar" style="font-size:.9rem;vertical-align:middle;margin-right:.2rem"></i> Today
                    </a>
                    @if($closerFilter || $validatorFilter || $pjcFilter || $statusFilter)
                        <a href="{{ route('settings.reports.peregrine-sales-report', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                           class="pipe-pill" style="font-size:.82rem;padding:.4rem .9rem;font-weight:600;color:#c84646">
                            <i class="bx bx-x" style="font-size:.9rem;vertical-align:middle"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- KPI Summary --}}
    <div class="kpi-row" style="margin-bottom:.65rem">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val">{{ $kpis['total_sales'] }}</div>
            <div class="k-lbl">Total Sales</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-check-double k-icon"></i>
            <div class="k-val">{{ $kpis['paid'] }}</div>
            <div class="k-lbl">Paid</div>
        </div>
        <div class="kpi-card k-blue ex-card" style="--k-accent:#1e6eb5">
            <i class="bx bx-file-blank k-icon"></i>
            <div class="k-val">{{ $kpis['pending_contract'] }}</div>
            <div class="k-lbl">Pnd Contract</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-time k-icon"></i>
            <div class="k-val">{{ $kpis['pending_draft'] }}</div>
            <div class="k-lbl">Pnd Draft</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val">{{ $kpis['declined'] }}</div>
            <div class="k-lbl">Declined</div>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6>
                <span class="sec-pill sec-pill-sales"><i class="bx bx-dollar-circle"></i> Sales</span>
                <span style="font-size:.62rem;color:var(--bs-surface-400);font-weight:400">
                    {{ $sales->count() }} sale(s) · {{ $dateFrom }} → {{ $dateTo }}
                    @if($closerFilter || $validatorFilter || $pjcFilter || $statusFilter)
                        &nbsp;<span style="color:#d4af37">· filtered</span>
                    @endif
                </span>
            </h6>
        </div>
        <div class="scroll-tbl">
            <table class="rp-table" id="salesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th class="rp-th-num">Premium/mo</th>
                        <th class="rp-th-num">Coverage</th>
                        <th>Closer</th>
                        <th>Validator</th>
                        <th>PJC</th>
                        <th>
                            <div class="status-info-wrap">
                                Sale Status
                                <span class="info-icon" id="statusInfoBtn" title="What do these statuses mean?">
                                    <i class="bx bx-info-circle"></i>
                                </span>
                            </div>
                        </th>
                        <th class="rp-th-num">Sale Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $i => $lead)
                        @php
                            $statusClass = match($lead->sale_status) {
                                'paid'             => 'rp-badge-paid',
                                'pending_contract' => 'rp-badge-pending-contract',
                                'pending_draft'    => 'rp-badge-pending-draft',
                                'declined'         => 'rp-badge-declined',
                                default            => 'rp-badge-sale',
                            };
                            $pjcName      = $lead->verifier?->name ?? $lead->account_verified_by ?? '—';
                            $closerName   = $lead->assignedCloser?->name ?? $lead->closer_name ?? '—';
                            $validatorName = $lead->assignedValidator?->name ?? '—';
                        @endphp
                        <tr data-lead-id="{{ $lead->id }}">
                            <td style="font-size:.72rem;color:var(--bs-surface-400)">{{ $i + 1 }}</td>
                            <td class="rp-td-name">{{ $lead->cn_name ?? '—' }}</td>
                            <td style="font-size:.75rem;white-space:nowrap">{{ $lead->phone_number ?? '—' }}</td>
                            <td class="rp-td-num">${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                            <td class="rp-td-num">${{ number_format($lead->coverage_amount ?? 0) }}</td>
                            <td style="font-size:.75rem">{{ $closerName }}</td>
                            <td style="font-size:.75rem">{{ $validatorName }}</td>
                            <td style="font-size:.75rem">{{ $pjcName }}</td>
                            <td><span class="rp-badge {{ $statusClass }}">{{ $lead->sale_status_label }}</span></td>
                            <td class="rp-td-num" style="white-space:nowrap;font-size:.75rem">
                                {{ $lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center;padding:2rem;color:var(--bs-surface-400);font-size:.75rem">
                                <i class="bx bx-info-circle" style="font-size:1.5rem;display:block;margin-bottom:.4rem"></i>
                                No sales found for the selected filters and date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sales->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="3" style="font-size:.72rem;font-weight:600">
                            Totals ({{ $sales->count() }} sale{{ $sales->count() !== 1 ? 's' : '' }})
                        </td>
                        <td class="rp-td-num">${{ number_format($sales->sum('monthly_premium'), 2) }}</td>
                        <td class="rp-td-num">${{ number_format($sales->sum('coverage_amount')) }}</td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Status info popover --}}
    <div id="statusInfoPopover">
        <strong>Sale Pipeline Statuses</strong>
        <p><strong style="color:#34c38f">Paid</strong> — Payment received &amp; policy is active.</p>
        <p><strong style="color:#50a5f1">Pending Contract</strong> — Sale marked, awaiting contract signature from client.</p>
        <p><strong style="color:#f1b44c">Pending Draft</strong> — Contract signed but banking/draft details pending confirmation.</p>
        <p><strong style="color:#f46a6a">Declined</strong> — Sale was reversed or application was declined after submission.</p>
        <p><strong style="color:#7c69ef">Sale</strong> — Marked as sale by validator, pipeline status not yet set.</p>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Status info popover ──
    var btn = document.getElementById('statusInfoBtn');
    var pop = document.getElementById('statusInfoPopover');
    if (btn && pop) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (pop.classList.contains('show')) {
                pop.classList.remove('show');
                return;
            }
            var rect = btn.getBoundingClientRect();
            pop.style.top  = (rect.bottom + window.scrollY + 6) + 'px';
            pop.style.left = Math.max(8, rect.left + window.scrollX - 100) + 'px';
            pop.classList.add('show');
        });
        document.addEventListener('click', function () {
            pop.classList.remove('show');
        });
    }

    var table = document.getElementById('salesTable');
    if (!table) return;
    var headers = table.querySelectorAll('thead th');
    headers.forEach(function (th, colIdx) {
        // Skip the info-icon column (status col has nested markup — still sortable by text)
        th.style.cursor = 'pointer';
        th.addEventListener('click', function (e) {
            if (e.target.closest('#statusInfoBtn')) return;
            var tbody = table.querySelector('tbody');
            if (!tbody) return;
            var rows = Array.from(tbody.querySelectorAll('tr'));
            var asc = th.dataset.sortDir !== 'asc';
            th.dataset.sortDir = asc ? 'asc' : 'desc';
            headers.forEach(function (h) { if (h !== th) delete h.dataset.sortDir; });
            rows.sort(function (a, b) {
                var aVal = (a.children[colIdx]?.textContent || '').trim().replace(/[$,]/g, '');
                var bVal = (b.children[colIdx]?.textContent || '').trim().replace(/[$,]/g, '');
                var aNum = parseFloat(aVal), bNum = parseFloat(bVal);
                if (!isNaN(aNum) && !isNaN(bNum)) return asc ? aNum - bNum : bNum - aNum;
                return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });
            rows.forEach(function (row) { tbody.appendChild(row); });
        });
    });
});
</script>
@endsection
