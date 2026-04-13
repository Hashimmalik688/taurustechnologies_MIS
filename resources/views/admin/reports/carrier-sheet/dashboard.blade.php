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
.cs-filter select, .cs-filter input[type=month] {
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
    border: none; padding: .5rem;
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
        <div style="margin-left:auto; display:flex; gap:.4rem; align-items:flex-end;">
            @canEditModule('carrier-sheet')
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
                    <th>Commission ($)</th>
                    <th>Paid Amt ($)</th>
                    <th>Chargeback ($)</th>
                    <th>Balance ($)</th>
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
                    <td class="cs-money" style="color:#fff;">{{ number_format($totals['commission'], 2) }}</td>
                    <td class="cs-money" style="color:#fff;">{{ number_format($totals['paid'], 2) }}</td>
                    <td class="cs-money" style="color:#fff;">{{ number_format($totals['chargeback_total'], 2) }}</td>
                    <td class="cs-money" style="color:#fff;">{{ number_format($totals['balance'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

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
