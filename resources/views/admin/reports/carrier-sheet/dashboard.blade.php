@extends('layouts.master')

@section('title')
    Carrier Sheet — Dashboard
@endsection

@section('css')
<style>
/* ════════════════════════════════════════════════════════
   CARRIER SHEET DASHBOARD  — D.B replica
   ════════════════════════════════════════════════════════ */
:root {
    --cs-indigo:  #283593; --cs-green: #2E7D32; --cs-purple: #4527A0;
    --cs-red:     #C62828; --cs-blue:  #1565C0; --cs-amber:  #F57F17;
    --cs-orange:  #E65100;
    --cs-surface: var(--bs-card-bg, #ffffff);
    --cs-border:  rgba(0,0,0,.07);
    --cs-shadow:  0 1px 4px rgba(0,0,0,.06), 0 0 0 1px rgba(0,0,0,.03);
    --cs-text-1:  var(--bs-body-color, #0f172a);
    --cs-text-2:  var(--bs-surface-700, #374151);
    --cs-text-3:  var(--bs-surface-500, #64748b);
}
.cs-page { width: 100%; }

/* ── Header ────────────────────────────────────────── */
.cs-hdr {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem;
}
.cs-hdr-left { display: flex; align-items: center; gap: .6rem; }
.cs-hdr-icon {
    width: 32px; height: 32px; border-radius: .45rem; flex-shrink: 0;
    background: linear-gradient(135deg, var(--cs-indigo), #1a237e);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 6px rgba(40,53,147,.35);
}
.cs-hdr-icon i { font-size: 1rem; color: #fff; }
.cs-hdr h5 { margin:0; font-size:1rem; font-weight:800; color:var(--cs-text-1); }
.cs-hdr-sub {
    font-size: .68rem; color: var(--cs-text-3); font-weight: 400;
    border-left: 2px solid var(--cs-border); padding-left: .5rem; margin-left: .1rem;
}
.cs-back {
    font-size:.7rem; font-weight:700; padding:.28rem .6rem; border-radius:20px;
    border:1.5px solid var(--cs-border); background:transparent; color:var(--cs-text-3);
    text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:all .15s;
}
.cs-back:hover { border-color: var(--cs-indigo); color: #1a237e; }

/* ── Filter bar ────────────────────────────────────── */
.cs-filter {
    display:flex; flex-wrap:wrap; gap:.5rem; align-items:flex-end;
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; padding:.55rem .8rem; margin-bottom:1rem;
    box-shadow:var(--cs-shadow);
}
.cs-filter label {
    font-size:.58rem; font-weight:800; text-transform:uppercase;
    letter-spacing:.6px; color:var(--cs-text-3); display:block; margin-bottom:.12rem;
}
.cs-filter select, .cs-filter input[type=month], .cs-filter input[type=search] {
    font-size:.73rem; padding:.28rem .45rem; border-radius:.4rem;
    border:1.5px solid var(--cs-border); background:var(--bs-input-bg, #f8fafc);
    color:var(--cs-text-1); outline:none; transition:border-color .15s;
}
.cs-filter select:focus, .cs-filter input:focus {
    border-color:var(--cs-indigo); box-shadow:0 0 0 2px rgba(40,53,147,.15);
}
.cs-btn {
    font-size:.7rem; font-weight:700; padding:.32rem .7rem; border-radius:20px;
    border:none; cursor:pointer; display:inline-flex; align-items:center;
    gap:.22rem; transition:all .15s; text-decoration:none;
}
.cs-btn-primary { background:linear-gradient(135deg, var(--cs-indigo), #1a237e); color:#fff; }
.cs-btn-primary:hover { box-shadow:0 2px 10px rgba(40,53,147,.4); transform:translateY(-1px); color:#fff; }
.cs-btn-outline { background:transparent; border:1.5px solid var(--cs-border)!important; color:var(--cs-text-3); }
.cs-btn-outline:hover { border-color:var(--cs-indigo)!important; color:#1a237e; }
.cs-btn-success { background:linear-gradient(135deg, #2E7D32, #1B5E20); color:#fff; }
.cs-btn-success:hover { box-shadow:0 2px 10px rgba(46,125,50,.4); color:#fff; }
.cs-btn-danger { background:linear-gradient(135deg, #C62828, #B71C1C); color:#fff; }

/* ── Table ─────────────────────────────────────────── */
.cs-card {
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; box-shadow:var(--cs-shadow); overflow:hidden;
}
.cs-table { width:100%; font-size:.73rem; border-collapse:collapse; }
.cs-table thead th {
    background:#1a237e; color:#fff; font-weight:700; font-size:.62rem;
    text-transform:uppercase; letter-spacing:.5px; padding:.45rem .5rem;
    text-align:center; white-space:nowrap; border:none;
}
.cs-table tbody td {
    padding:.38rem .5rem; border-bottom:1px solid var(--cs-border);
    text-align:center; vertical-align:middle; color:var(--cs-text-1);
}
.cs-table tbody tr:hover { background: rgba(40,53,147,.04); }

/* ── Grand total row ───────────────────────────────── */
.cs-table .cs-total td {
    background: #1B5E20; color: #fff; font-weight: 800; font-size: .74rem;
    border: none; padding: .5rem; text-align: center; vertical-align: middle;
}

/* ── Number formatting ─────────────────────────────── */
.cs-money { font-weight:700; font-variant-numeric:tabular-nums; }
.cs-money-pos { color:#2E7D32; }
.cs-money-neg { color:#C62828; }

/* ── Status count pills ────────────────────────────── */
.cs-pill {
    display:inline-block; min-width:28px; padding:.14rem .4rem;
    border-radius:.25rem; font-weight:700; font-size:.65rem;
    text-align:center; color:#fff;
}
.cs-pill-apps  { background:var(--cs-blue); }
.cs-pill-paid  { background:var(--cs-green); }
.cs-pill-appr  { background:var(--cs-amber); }
.cs-pill-cb    { background:var(--cs-red); }
.cs-pill-dec   { background:var(--cs-orange); }

/* ── Carrier link ──────────────────────────────────── */
.cs-carrier-link {
    font-weight:700; color:var(--cs-text-1); text-decoration:none;
    display:flex; align-items:center; gap:.3rem; white-space:nowrap;
}
.cs-carrier-link:hover { color:var(--cs-indigo); }
.cs-carrier-dot {
    width:10px; height:10px; border-radius:2px; flex-shrink:0;
}

/* ── Actions row ───────────────────────────────────── */
.cs-actions {
    display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem;
}

/* ── Money column alignment ───────────────────────── */
.cs-table th.cs-th-right,
.cs-table td.cs-money {
    text-align:right !important;
    padding-right:1rem !important;
    font-variant-numeric:tabular-nums;
}

/* ── Charts ────────────────────────────────────────── */
.cs-charts-row {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:1rem;
    margin-top:1rem;
}
@media(max-width:768px){ .cs-charts-row{ grid-template-columns:1fr; } }
.cs-chart-card {
    background:var(--cs-surface); border:1px solid var(--cs-border);
    border-radius:.55rem; box-shadow:var(--cs-shadow); padding:1rem;
}
.cs-chart-wrap {
    position:relative; height:260px;
}
.cs-chart-title {
    font-size:.68rem; font-weight:800; text-transform:uppercase;
    letter-spacing:.6px; color:var(--cs-text-3); margin-bottom:.75rem;
    display:flex; align-items:center; gap:.35rem;
}
.cs-chart-title i { font-size:.85rem; }
</style>
@endsection

@section('content')
<div class="cs-page">
    {{-- Header --}}
    <div class="cs-hdr">
        <div class="cs-hdr-left">
            <div class="cs-hdr-icon"><i class="bx bx-spreadsheet"></i></div>
            <div>
                <h5>Commission Dashboard</h5>
                <span class="cs-hdr-sub">Carrier commission tracking workbook</span>
            </div>
        </div>
        <a href="{{ route('settings.reports.hub') }}" class="cs-back"><i class="bx bx-arrow-back"></i> Reports Hub</a>
    </div>

    {{-- Filter + actions --}}
    <form class="cs-filter" method="GET" action="{{ route('settings.reports.carrier-sheet.dashboard') }}">
        <div>
            <label>Period</label>
            <select name="month" onchange="this.form.submit()">
                <option value="">All Time</option>
                @foreach($months as $m)
                    <option value="{{ $m }}" {{ $periodMonth === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($m)->format('F Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Search</label>
            <input type="search" id="dashboardCarrierSearch" placeholder="Search carrier..." autocomplete="off">
        </div>
        <div style="margin-left:auto; display:flex; gap:.4rem; align-items:flex-end;">
            @canEditModule('carrier-sheet')
            <button type="button" class="cs-btn cs-btn-primary" data-bs-toggle="modal" data-bs-target="#quickEntryModal">
                <i class="bx bx-plus-circle"></i> Add Entry
            </button>
            <button type="button" class="cs-btn cs-btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-import"></i> Import .xlsx
            </button>
            @endcanEditModule
            <a href="{{ route('settings.reports.carrier-sheet.rates') }}" class="cs-btn cs-btn-outline">
                <i class="bx bx-cog"></i> Rates
            </a>
        </div>
    </form>

    {{-- Import results flash --}}
    @if(session('import_results'))
    <div class="alert alert-info alert-dismissible fade show" style="font-size:.75rem;">
        <strong>Import Results:</strong>
        <ul class="mb-0 mt-1">
        @foreach(session('import_results') as $r)
            <li>{{ $r['sheet'] }}: {{ $r['status'] === 'imported' ? $r['imported'].' imported, '.$r['skipped'].' skipped' : 'Skipped — '.$r['reason'] }}</li>
        @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Dashboard table --}}
    <div class="cs-card">
        <table class="cs-table">
            <thead>
                <tr>
                    <th style="text-align:left; width:30px;">#</th>
                    <th style="text-align:left;">Carrier</th>
                    <th>Total Apps</th>
                    <th>Paid</th>
                    <th>Approved</th>
                    <th>Chargeback</th>
                    <th>Declined</th>
                    <th class="cs-th-right">Commission ($)</th>
                    <th class="cs-th-right">Paid Amt ($)</th>
                    <th class="cs-th-right">Chargeback ($)</th>
                    <th class="cs-th-right">Balance ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                <tr>
                    <td style="text-align:left;">{{ $i + 1 }}</td>
                    <td style="text-align:left;">
                        <a href="{{ route('settings.reports.carrier-sheet.show', ['rate' => $row['carrier']->id, 'month' => $periodMonth]) }}" class="cs-carrier-link">
                            <span class="cs-carrier-dot" style="background:{{ $row['carrier']->title_color }};"></span>
                            {{ $row['carrier']->carrier_label }}
                        </a>
                    </td>
                    <td><span class="cs-pill cs-pill-apps">{{ $row['total_apps'] }}</span></td>
                    <td><span class="cs-pill cs-pill-paid">{{ $row['paid_count'] }}</span></td>
                    <td><span class="cs-pill cs-pill-appr">{{ $row['approved_count'] }}</span></td>
                    <td><span class="cs-pill cs-pill-cb">{{ $row['chargeback_count'] }}</span></td>
                    <td><span class="cs-pill cs-pill-dec">{{ $row['declined_count'] }}</span></td>
                    <td class="cs-money {{ $row['commission'] >= 0 ? 'cs-money-pos' : 'cs-money-neg' }}">
                        {{ number_format($row['commission'], 2) }}
                    </td>
                    <td class="cs-money">{{ number_format($row['paid'], 2) }}</td>
                    <td class="cs-money cs-money-neg">{{ number_format($row['chargeback_total'], 2) }}</td>
                    <td class="cs-money {{ $row['balance'] >= 0 ? 'cs-money-pos' : 'cs-money-neg' }}">
                        {{ number_format($row['balance'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="cs-total">
                    <td colspan="2" style="text-align:left;">GRAND TOTAL</td>
                    <td><span class="cs-pill cs-pill-apps">{{ $totals['total_apps'] }}</span></td>
                    <td><span class="cs-pill cs-pill-paid">{{ $totals['paid_count'] }}</span></td>
                    <td><span class="cs-pill cs-pill-appr">{{ $totals['approved_count'] }}</span></td>
                    <td><span class="cs-pill cs-pill-cb">{{ $totals['chargeback_count'] }}</span></td>
                    <td><span class="cs-pill cs-pill-dec">{{ $totals['declined_count'] }}</span></td>
                    <td class="cs-money" style="color:#fff; text-align:right !important; padding-right:1rem;">{{ number_format($totals['commission'], 2) }}</td>
                    <td class="cs-money" style="color:#fff; text-align:right !important; padding-right:1rem;">{{ number_format($totals['paid'], 2) }}</td>
                    <td class="cs-money" style="color:#fff; text-align:right !important; padding-right:1rem;">{{ number_format($totals['chargeback_total'], 2) }}</td>
                    <td class="cs-money" style="color:#fff; text-align:right !important; padding-right:1rem;">{{ number_format($totals['balance'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Charts row --}}
    <div class="cs-charts-row">
        {{-- Paid Amount chart --}}
        <div class="cs-chart-card">
            <div class="cs-chart-title"><i class="bx bx-dollar-circle" style="color:var(--cs-green);"></i> Paid Amount by Carrier</div>
            <div class="cs-chart-wrap"><canvas id="chartPaid"></canvas></div>
        </div>
        {{-- Balance chart --}}
        <div class="cs-chart-card">
            <div class="cs-chart-title"><i class="bx bx-bar-chart-alt-2" style="color:var(--cs-indigo);"></i> Balance by Carrier</div>
            <div class="cs-chart-wrap"><canvas id="chartBalance"></canvas></div>
        </div>
    </div>
</div>

{{-- Quick Entry Modal --}}
@canEditModule('carrier-sheet')
<div class="modal fade" id="quickEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="quickEntryForm">
            @csrf
            <div class="modal-header" style="background:var(--cs-title); color:#fff;">
                <h6 class="modal-title fw-bold"><i class="bx bx-plus me-1"></i> Add Entry</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Row 1: Carrier Sheet + Date --}}
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Carrier Sheet *</label>
                        <select name="carrier_sheet_rate_id" id="quickEntryCarrierSheet" class="form-select form-select-sm" required>
                            <option value="">— Select Carrier Sheet —</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->carrier_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Date *</label>
                        <input type="date" name="entry_date" id="quickEntryDate" class="form-control form-control-sm" required>
                    </div>
                </div>
                {{-- Row 2: Policy # + Name + Face Value --}}
                <div class="row g-2 mt-1">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Policy #</label>
                        <input type="text" name="policy_number" id="quickEntryPolicyNumber" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4" style="position:relative;">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Name *</label>
                        <input type="text" name="name" id="quickEntryName" class="form-control form-control-sm" autocomplete="off" placeholder="Type to search leads..." required>
                        <div id="quickEntryLeadSuggestions" style="display:none; position:absolute; z-index:9999; width:100%; background:#fff; border:1px solid #ced4da; border-radius:4px; max-height:220px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,.15); top:100%; left:0;"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Face Value</label>
                        <input type="text" name="face_value" id="quickEntryFaceValue" class="form-control form-control-sm" placeholder="e.g. 5K">
                    </div>
                </div>
                {{-- Row 3: Premium + Policy Type + Status + Draft Date + Payment Date + Paid Amt --}}
                <div class="row g-2 mt-1">
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Premium *</label>
                        <input type="number" step="0.01" name="premium" id="quickEntryPremium" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Policy Type</label>
                        <select name="policy_type" id="quickEntryPolicyType" class="form-select form-select-sm">
                            <option value="">—</option>
                            <option value="iul">IUL</option>
                            <option value="term">Term</option>
                            <option value="whole life">Whole Life</option>
                            <option value="final expense">Final Expense</option>
                            <option value="graded">Graded</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Status *</label>
                        <select name="status" id="quickEntryStatus" class="form-select form-select-sm" required>
                            <option value="approved">APPROVED</option>
                            <option value="paid">PAID</option>
                            <option value="chargeback">CHARGEBACK</option>
                            <option value="declined">DECLINED</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Draft Date</label>
                        <input type="date" name="draft_date" id="quickEntryDraftDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Payment Date</label>
                        <input type="date" name="payment_date" id="quickEntryPaymentDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Paid Amt</label>
                        <input type="number" step="0.01" name="paid_amount" class="form-control form-control-sm" value="0">
                    </div>
                </div>
                {{-- Row 4: Chargeback + Rate Override + Notes --}}
                <div class="row g-2 mt-1">
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Chargeback $</label>
                        <input type="number" step="0.01" name="chargeback_amount" class="form-control form-control-sm" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Rate Override</label>
                        <input type="number" step="0.0001" name="rate_override" class="form-control form-control-sm" placeholder="optional">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" style="font-size:.68rem; font-weight:700;">Notes</label>
                        <input type="text" name="notes" class="form-control form-control-sm">
                    </div>
                </div>

                <div id="quickEntryError" class="alert alert-danger py-2 px-3 mt-2" style="font-size:.68rem; display:none;"></div>
                <div id="quickEntrySuccess" class="alert alert-success py-2 px-3 mt-2" style="font-size:.68rem; display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success"><i class="bx bx-check me-1"></i> Add Entry</button>
            </div>
        </form>
    </div>
</div>
@endcanEditModule

{{-- Import Modal --}}
@canEditModule('carrier-sheet')
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('settings.reports.carrier-sheet.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bx bx-import me-1"></i> Import Excel Workbook</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.72rem; color:var(--cs-text-3);">
                    Upload the Carrier Sheet workbook (.xlsx). Each sheet (T.A F-1, AIG Y-1, etc.) will be imported into its matching carrier.
                    The RATES and D.B sheets are skipped automatically.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:.72rem;">Excel File</label>
                    <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls" required>
                </div>
                <div class="alert alert-warning py-2 px-3" style="font-size:.68rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    Period is auto-detected from each row's date. Commission &amp; balance are calculated from rates. No existing data is deleted — new rows are appended.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success"><i class="bx bx-check me-1"></i> Import</button>
            </div>
        </form>
    </div>
</div>
@endcanEditModule
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    const rows = @json($rows);
    const searchInput = document.getElementById('dashboardCarrierSearch');

    const labels  = rows.map(r => r.carrier.carrier_label);
    const colors  = rows.map(r => r.carrier.title_color || '#1565C0');
    const paidAmt = rows.map(r => parseFloat(r.paid)    || 0);
    const balance = rows.map(r => parseFloat(r.balance) || 0);

    function filterDashboardRows() {
        const q = (searchInput?.value || '').trim().toLowerCase();
        const tableRows = document.querySelectorAll('.cs-table tbody tr');
        tableRows.forEach((tr) => {
            const txt = tr.textContent.toLowerCase();
            tr.style.display = !q || txt.includes(q) ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterDashboardRows);
    }

    // ── Paid Amount chart ─────────────────────────────────────────────────
    new Chart(document.getElementById('chartPaid'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Paid ($)',
                data: paidAmt,
                backgroundColor: colors.map(c => c + 'cc'),
                borderColor:     colors,
                borderWidth: 1.5,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' $' + ctx.parsed.x.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        font:{size:10},
                        callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(1)+'k' : v)
                    },
                    grid: { color:'rgba(0,0,0,.05)' }
                },
                y: { ticks:{ font:{size:10} }, grid:{ display:false } }
            }
        }
    });

    // ── Balance chart ─────────────────────────────────────────────────────
    const balanceBg     = balance.map(v => v >= 0 ? 'rgba(46,125,50,.80)' : 'rgba(198,40,40,.80)');
    const balanceBorder = balance.map(v => v >= 0 ? '#2E7D32' : '#C62828');

    new Chart(document.getElementById('chartBalance'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Balance ($)',
                data: balance,
                backgroundColor: balanceBg,
                borderColor:     balanceBorder,
                borderWidth: 1.5,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' $' + ctx.parsed.x.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        font:{size:10},
                        callback: v => {
                            const abs = Math.abs(v);
                            const fmt = abs >= 1000 ? (abs/1000).toFixed(1)+'k' : abs;
                            return (v < 0 ? '-$' : '$') + fmt;
                        }
                    },
                    grid: { color:'rgba(0,0,0,.05)' }
                },
                y: { ticks:{ font:{size:10} }, grid:{ display:false } }
            }
        }
    });

    // ══════════════════════════════════════════════════════════════════════
    // QUICK ENTRY MODAL FUNCTIONALITY
    // ══════════════════════════════════════════════════════════════════════
    
    const quickEntryModal = document.getElementById('quickEntryModal');
    const quickEntryForm = document.getElementById('quickEntryForm');
    const nameInput = document.getElementById('quickEntryName');
    const suggestBox = document.getElementById('quickEntryLeadSuggestions');
    const carrierSheetSelect = document.getElementById('quickEntryCarrierSheet');
    const errorAlert = document.getElementById('quickEntryError');
    const successAlert = document.getElementById('quickEntrySuccess');
    const LOOKUP_URL = "{{ route('settings.reports.carrier-sheet.lead-lookup') }}";

    let debounceTimer = null;
    let selectedLeadId = null;

    // Reset form when modal opens
    if (quickEntryModal) {
        quickEntryModal.addEventListener('show.bs.modal', function() {
            quickEntryForm.reset();
            suggestBox.style.display = 'none';
            suggestBox.innerHTML = '';
            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';
            selectedLeadId = null;
            
            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('quickEntryDate').value = today;
            // Reset modal title
            quickEntryModal.querySelector('.modal-title').innerHTML = '<i class="bx bx-plus me-1"></i> Add Entry';
        });
    }

    // Update modal title when carrier sheet is selected
    if (carrierSheetSelect) {
        carrierSheetSelect.addEventListener('change', function() {
            const label = this.options[this.selectedIndex]?.text;
            const title = quickEntryModal.querySelector('.modal-title');
            if (title) {
                title.innerHTML = label && this.value
                    ? `<i class="bx bx-plus me-1"></i> Add Entry — ${label}`
                    : '<i class="bx bx-plus me-1"></i> Add Entry';
            }
        });
    }

    // Function to fill form fields from selected lead
    function fillFromLead(lead) {
        selectedLeadId = lead.id;
        nameInput.value = lead.name || '';
        
        const set = (id, v) => { 
            const el = document.getElementById(id); 
            if (el && v != null) el.value = v; 
        };
        
        set('quickEntryPolicyNumber', lead.policy_number);
        set('quickEntryFaceValue', lead.face_value);
        set('quickEntryPremium', lead.premium);
        set('quickEntryPolicyType', lead.policy_type ? lead.policy_type.toLowerCase() : null);
        set('quickEntryDraftDate', lead.draft_date);
        set('quickEntryPaymentDate', lead.payment_date);
        
        // Auto-select carrier sheet if suggested
        if (lead.suggested_sheet) {
            carrierSheetSelect.value = lead.suggested_sheet;
            carrierSheetSelect.classList.add('border-success');
            setTimeout(() => {
                carrierSheetSelect.classList.remove('border-success');
            }, 2000);
            
            // Show success message
            successAlert.textContent = '✓ Lead info loaded! Carrier sheet auto-matched based on partner code.';
            successAlert.style.display = 'block';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 3000);
        }
        
        suggestBox.style.display = 'none';
        suggestBox.innerHTML = '';
    }

    // Render suggestion list
    function renderSuggestions(leads) {
        suggestBox.innerHTML = '';
        if (!leads.length) {
            suggestBox.style.display = 'none';
            return;
        }
        leads.forEach(lead => {
            const div = document.createElement('div');
            div.style.cssText = 'padding:7px 12px; cursor:pointer; border-bottom:1px solid #f0f0f0; font-size:.82rem;';
            div.innerHTML = `<strong>${lead.name}</strong>`
                + (lead.policy_number ? ` &nbsp;<span style="color:#6c757d">${lead.policy_number}</span>` : '')
                + (lead.premium       ? ` &nbsp;<span style="color:#0d6efd">$${lead.premium}</span>` : '')
                + (lead.face_value    ? ` &nbsp;<span style="background:#e9ecef;border-radius:3px;padding:1px 5px;font-size:.72rem;color:#495057">${lead.face_value}</span>` : '')
                + (lead.carrier_name  ? ` &nbsp;<span style="color:#6c757d;font-size:.72rem">${lead.carrier_name}</span>` : '')
                + (lead.suggested_sheet ? ` &nbsp;<span style="background:#d1e7dd;border-radius:3px;padding:1px 5px;font-size:.68rem;color:#0a3622">matched</span>` : '');
            div.addEventListener('mouseover', () => div.style.background = '#f8f9fa');
            div.addEventListener('mouseout', () => div.style.background = '');
            div.addEventListener('mousedown', (e) => { e.preventDefault(); fillFromLead(lead); });
            suggestBox.appendChild(div);
        });
        suggestBox.style.display = 'block';
    }

    // Name input autocomplete
    if (nameInput) {
        nameInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const q = nameInput.value.trim();
            if (q.length < 2) { 
                suggestBox.style.display = 'none'; 
                return; 
            }
            
            debounceTimer = setTimeout(async () => {
                try {
                    const res = await fetch(`${LOOKUP_URL}?q=${encodeURIComponent(q)}`, { 
                        headers: { 'Accept': 'application/json' } 
                    });
                    renderSuggestions(await res.json());
                } catch (err) {
                    console.error('Lead lookup error:', err);
                    suggestBox.style.display = 'none';
                }
            }, 280);
        });

        nameInput.addEventListener('blur', () => {
            setTimeout(() => { suggestBox.style.display = 'none'; }, 200);
        });
    }

    // Form submission
    if (quickEntryForm) {
        quickEntryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const carrierSheetId = carrierSheetSelect.value;
            if (!carrierSheetId) {
                errorAlert.textContent = 'Please select a carrier sheet';
                errorAlert.style.display = 'block';
                return;
            }

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Saving...';
            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';

            // Add lead_id if one was selected
            if (selectedLeadId) {
                formData.append('lead_id', selectedLeadId);
            }

            fetch(`/settings/reports/carrier-sheet/${carrierSheetId}/entries`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    successAlert.textContent = '✓ Entry added successfully!';
                    successAlert.style.display = 'block';
                    
                    // Close modal and reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to add entry');
                }
            })
            .catch(err => {
                console.error('Form submission error:', err);
                errorAlert.textContent = err.message || 'An error occurred while saving the entry';
                errorAlert.style.display = 'block';
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }
})();
</script>
@endpush
