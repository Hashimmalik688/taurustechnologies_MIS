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
        <form class="modal-content" id="quickEntryForm" method="POST">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bx bx-plus-circle me-1"></i> Quick Add Entry</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    {{-- Carrier Sheet Selection --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Carrier Sheet *</label>
                        <select name="carrier_sheet_rate_id" id="quickEntryCarrierSheet" class="form-select form-select-sm" required>
                            <option value="">— Select Carrier Sheet —</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->carrier_label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lead Search --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Search Lead (Optional)</label>
                        <div class="position-relative">
                            <input type="text" 
                                   id="quickEntryLeadSearch" 
                                   class="form-control form-control-sm" 
                                   placeholder="Type name or policy number..."
                                   autocomplete="off">
                            <div id="quickEntrySearchResults" class="position-absolute w-100 bg-white border rounded shadow-sm" style="max-height:250px; overflow-y:auto; z-index:1050; display:none;"></div>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size:.65rem;">
                            <i class="bx bx-info-circle"></i> Search will auto-populate fields and suggest matching carrier sheet
                        </small>
                    </div>

                    {{-- Entry Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Entry Date *</label>
                        <input type="date" name="entry_date" id="quickEntryDate" class="form-control form-control-sm" required>
                    </div>

                    {{-- Policy Number --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Policy Number *</label>
                        <input type="text" name="policy_number" id="quickEntryPolicyNumber" class="form-control form-control-sm" required>
                    </div>

                    {{-- Name --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Name *</label>
                        <input type="text" name="name" id="quickEntryName" class="form-control form-control-sm" required>
                    </div>

                    {{-- Face Value --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Face Value</label>
                        <input type="number" step="0.01" name="face_value" id="quickEntryFaceValue" class="form-control form-control-sm">
                    </div>

                    {{-- Premium --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Premium</label>
                        <input type="number" step="0.01" name="premium" id="quickEntryPremium" class="form-control form-control-sm">
                    </div>

                    {{-- Policy Type --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Policy Type</label>
                        <select name="policy_type" id="quickEntryPolicyType" class="form-select form-select-sm">
                            <option value="">— Select —</option>
                            <option value="IUL">IUL</option>
                            <option value="Term">Term</option>
                            <option value="Whole Life">Whole Life</option>
                            <option value="Final Expense">Final Expense</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Status *</label>
                        <select name="status" id="quickEntryStatus" class="form-select form-select-sm" required>
                            <option value="Paid">Paid</option>
                            <option value="Approved">Approved</option>
                            <option value="Pending">Pending</option>
                            <option value="Chargeback">Chargeback</option>
                            <option value="Declined">Declined</option>
                        </select>
                    </div>

                    {{-- Draft Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Draft Date</label>
                        <input type="date" name="draft_date" id="quickEntryDraftDate" class="form-control form-control-sm">
                    </div>

                    {{-- Payment Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:.72rem;">Payment Date</label>
                        <input type="date" name="payment_date" id="quickEntryPaymentDate" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 mt-3" style="font-size:.68rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    Commission and balance will be calculated automatically based on carrier rates.
                </div>

                <div id="quickEntryError" class="alert alert-danger py-2 px-3 mt-2" style="font-size:.68rem; display:none;"></div>
                <div id="quickEntrySuccess" class="alert alert-success py-2 px-3 mt-2" style="font-size:.68rem; display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary"><i class="bx bx-check me-1"></i> Add Entry</button>
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
    const leadSearchInput = document.getElementById('quickEntryLeadSearch');
    const searchResults = document.getElementById('quickEntrySearchResults');
    const carrierSheetSelect = document.getElementById('quickEntryCarrierSheet');
    const errorAlert = document.getElementById('quickEntryError');
    const successAlert = document.getElementById('quickEntrySuccess');

    let searchTimeout;
    let selectedLeadId = null;

    // Reset form when modal opens
    if (quickEntryModal) {
        quickEntryModal.addEventListener('show.bs.modal', function() {
            quickEntryForm.reset();
            searchResults.style.display = 'none';
            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';
            selectedLeadId = null;
            
            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('quickEntryDate').value = today;
        });
    }

    // Lead search with debounce
    if (leadSearchInput) {
        leadSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/settings/reports/carrier-sheet/lead-lookup?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(leads => {
                        if (leads.length === 0) {
                            searchResults.innerHTML = '<div class="p-2 text-muted" style="font-size:.7rem;">No leads found</div>';
                            searchResults.style.display = 'block';
                            return;
                        }

                        searchResults.innerHTML = leads.map(lead => `
                            <div class="lead-result-item p-2 border-bottom" 
                                 style="cursor:pointer; font-size:.72rem;" 
                                 data-lead='${JSON.stringify(lead)}'>
                                <div class="fw-bold">${lead.name}</div>
                                <div class="text-muted small">
                                    Policy: ${lead.policy_number || 'N/A'} | 
                                    Premium: $${lead.premium || '0'} | 
                                    Carrier: ${lead.carrier_name || 'N/A'}
                                    ${lead.suggested_sheet ? '<span class="badge bg-success ms-1">Auto-match available</span>' : ''}
                                </div>
                            </div>
                        `).join('');
                        
                        searchResults.style.display = 'block';

                        // Attach click handlers
                        searchResults.querySelectorAll('.lead-result-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const lead = JSON.parse(this.dataset.lead);
                                populateFormFromLead(lead);
                                searchResults.style.display = 'none';
                            });
                        });
                    })
                    .catch(err => {
                        console.error('Lead search error:', err);
                        searchResults.innerHTML = '<div class="p-2 text-danger" style="font-size:.7rem;">Error searching leads</div>';
                        searchResults.style.display = 'block';
                    });
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!leadSearchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // Populate form fields from selected lead
    function populateFormFromLead(lead) {
        selectedLeadId = lead.id;
        leadSearchInput.value = `${lead.name} - ${lead.policy_number}`;

        // Auto-populate fields
        document.getElementById('quickEntryPolicyNumber').value = lead.policy_number || '';
        document.getElementById('quickEntryName').value = lead.name;
        document.getElementById('quickEntryFaceValue').value = lead.face_value || '';
        document.getElementById('quickEntryPremium').value = lead.premium || '';
        document.getElementById('quickEntryPolicyType').value = lead.policy_type || '';

        // Set dates if available
        if (lead.draft_date) {
            document.getElementById('quickEntryDraftDate').value = lead.draft_date;
        }
        if (lead.payment_date) {
            document.getElementById('quickEntryPaymentDate').value = lead.payment_date;
        }

        // Auto-select carrier sheet if suggested
        if (lead.suggested_sheet) {
            carrierSheetSelect.value = lead.suggested_sheet;
            
            // Visual feedback
            carrierSheetSelect.classList.add('border-success');
            setTimeout(() => {
                carrierSheetSelect.classList.remove('border-success');
            }, 2000);
        }

        // Show success message
        successAlert.textContent = `✓ Lead info loaded! ${lead.suggested_sheet ? 'Carrier sheet auto-matched based on partner code.' : ''}`;
        successAlert.style.display = 'block';
        setTimeout(() => {
            successAlert.style.display = 'none';
        }, 4000);
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
