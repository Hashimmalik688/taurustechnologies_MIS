@extends('layouts.master')

@section('title', 'Retention Dashboard')

@section('css')
<link href="{{ URL::asset('css/light-theme.css') }}" rel="stylesheet" type="text/css" />
<style>
/* ═══════════════════════════════════════════════════
   Retention Dashboard — Matches Executive Dashboard
   ═══════════════════════════════════════════════════ */

/* Glass-card base */
.ex-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: box-shadow .2s;
}
.ex-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* ── KPI Stat Cards ── */
.kpi-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.65rem; }
.kpi-card {
    flex: 1 1 80px;
    min-width: 75px;
    padding: 0.65rem 0.6rem;
    border-radius: 0.55rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0.55rem 0.55rem 0 0;
}
.kpi-card .k-icon {
    font-size: 1rem;
    margin-bottom: 0.2rem;
    display: block;
    opacity: .7;
}
.kpi-card .k-val { font-size: 1.35rem; font-weight: 700; line-height: 1; }
.kpi-card .k-lbl {
    font-size: 0.58rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--bs-surface-500);
    margin-top: 0.2rem;
}

/* KPI color variants */
.kpi-card.k-gold    { background: rgba(212,175,55,.06); }
.kpi-card.k-gold::before    { background: linear-gradient(90deg, #d4af37, #e8c84a); }
.kpi-card.k-gold .k-val, .kpi-card.k-gold .k-icon { color: #b89730; }

.kpi-card.k-blue    { background: rgba(85,110,230,.06); }
.kpi-card.k-blue::before    { background: linear-gradient(90deg, #556ee6, #8b9cf7); }
.kpi-card.k-blue .k-val, .kpi-card.k-blue .k-icon { color: #556ee6; }

.kpi-card.k-green   { background: rgba(52,195,143,.06); }
.kpi-card.k-green::before   { background: linear-gradient(90deg, #34c38f, #6eddb8); }
.kpi-card.k-green .k-val, .kpi-card.k-green .k-icon { color: #1a8754; }

.kpi-card.k-teal    { background: rgba(80,165,241,.06); }
.kpi-card.k-teal::before    { background: linear-gradient(90deg, #50a5f1, #8cc5f7); }
.kpi-card.k-teal .k-val, .kpi-card.k-teal .k-icon { color: #2b81c9; }

.kpi-card.k-red     { background: rgba(244,106,106,.06); }
.kpi-card.k-red::before     { background: linear-gradient(90deg, #f46a6a, #f89b9b); }
.kpi-card.k-red .k-val, .kpi-card.k-red .k-icon { color: #c84646; }

.kpi-card.k-warn    { background: rgba(241,180,76,.06); }
.kpi-card.k-warn::before    { background: linear-gradient(90deg, #f1b44c, #f5cd7e); }
.kpi-card.k-warn .k-val, .kpi-card.k-warn .k-icon { color: #b87a14; }

.kpi-card.k-purple  { background: rgba(124,105,239,.06); }
.kpi-card.k-purple::before  { background: linear-gradient(90deg, #7c69ef, #a899f5); }
.kpi-card.k-purple .k-val, .kpi-card.k-purple .k-icon { color: #5b49c7; }

/* ── Section Cards ── */
.sec-card {
    padding: 0;
    margin-bottom: 0.65rem;
    overflow: hidden;
}
.sec-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    flex-wrap: wrap;
    gap: 0.4rem;
}
.sec-hdr h6 {
    margin: 0;
    font-size: 0.78rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.sec-hdr h6 i { opacity: .6; font-size: 0.95rem; }
.sec-body { padding: 0.6rem 0.75rem; }

/* ── Team Tabs ── */
.team-tabs {
    display: flex;
    gap: 0.3rem;
}
.team-tab-btn {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.22rem 0.6rem;
    border-radius: 1rem;
    border: 1px solid var(--bs-surface-300);
    background: transparent;
    color: var(--bs-surface-500);
    cursor: pointer;
    transition: all .15s;
}
.team-tab-btn.active {
    background: var(--bs-gold, #d4af37);
    border-color: var(--bs-gold);
    color: #fff;
}
.team-tab-btn:hover:not(.active) {
    border-color: var(--bs-gold);
    color: var(--bs-gold);
}

/* ── Compact Table ── */
.ex-tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.75rem;
}
.ex-tbl thead th {
    text-transform: uppercase;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--bs-surface-500);
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap;
    background: var(--bs-surface-100);
    position: sticky;
    top: 0;
    z-index: 1;
}
.ex-tbl tbody td {
    padding: 0.4rem 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,.03);
    vertical-align: middle;
}
.ex-tbl tbody tr { transition: background .12s; }
.ex-tbl tbody tr:hover { background: rgba(212,175,55,.03); }

/* Badge mini */
.bd-mini {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
    display: inline-block;
    min-width: 22px;
    text-align: center;
}
.bd-mini.bd-blue   { background: rgba(85,110,230,.12); color: #556ee6; }
.bd-mini.bd-green  { background: rgba(52,195,143,.12); color: #1a8754; }
.bd-mini.bd-red    { background: rgba(244,106,106,.12); color: #c84646; }
.bd-mini.bd-warn   { background: rgba(241,180,76,.12); color: #b87a14; }
.bd-mini.bd-teal   { background: rgba(80,165,241,.12); color: #2b81c9; }
.bd-mini.bd-gold   { background: rgba(212,175,55,.12); color: #b89730; }



/* Retention mini blocks */
.ret-row {
    display: flex;
    gap: 0.4rem;
}
.ret-block {
    flex: 1;
    text-align: center;
    padding: 0.5rem 0.3rem;
    border-radius: 0.45rem;
    border: 1px solid;
    transition: transform .15s;
}
.ret-block:hover { transform: translateY(-1px); }
.ret-block .r-val { font-size: 1.2rem; font-weight: 700; line-height: 1; }
.ret-block .r-lbl {
    font-size: 0.55rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--bs-surface-500);
    margin-top: 0.15rem;
}
.ret-block.r-cb   { border-color: rgba(244,106,106,.3); background: rgba(244,106,106,.04); }
.ret-block.r-cb .r-val   { color: #c84646; }
.ret-block.r-ret  { border-color: rgba(52,195,143,.3); background: rgba(52,195,143,.04); }
.ret-block.r-ret .r-val  { color: #1a8754; }
.ret-block.r-pend { border-color: rgba(241,180,76,.3); background: rgba(241,180,76,.04); }
.ret-block.r-pend .r-val { color: #b87a14; }
.ret-block.r-rw   { border-color: rgba(124,105,239,.3); background: rgba(124,105,239,.04); }
.ret-block.r-rw .r-val   { color: #5b49c7; }

/* ── Scrollable table wrapper ── */
.scroll-tbl { max-height: 380px; overflow-y: auto; }
.scroll-tbl::-webkit-scrollbar { width: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

/* ── Link button ── */
.link-btn {
    font-size: 0.62rem;
    padding: 0.18rem 0.45rem;
    border-radius: 0.3rem;
    border: 1px solid var(--bs-surface-300);
    background: transparent;
    color: var(--bs-surface-500);
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
}
.link-btn:hover { border-color: var(--bs-gold); color: var(--bs-gold); }



/* ── Status badge ── */
.status-pill {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.18rem 0.55rem;
    border-radius: 1rem;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.status-pill.s-retained { background: rgba(52,195,143,.12); color: #1a8754; }
.status-pill.s-rewrite  { background: rgba(124,105,239,.12); color: #5b49c7; }
.status-pill.s-pending  { background: rgba(241,180,76,.12); color: #b87a14; }
.status-pill.s-cb       { background: rgba(244,106,106,.12); color: #c84646; }

/* Target Chart */
.target-chart-wrap {
    text-align: center;
    padding: 0.5rem 0;
}
.target-chart-wrap canvas {
    max-height: 130px;
}
.target-info {
    margin-top: 0.35rem;
    font-size: 0.7rem;
    color: var(--bs-surface-500);
}
.target-info strong { font-weight: 700; }
</style>
@endsection

@section('content')

    {{-- KPI Row 1 — Primary Retention Metrics --}}
    <div class="kpi-row">
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-error k-icon"></i>
            <div class="k-val">{{ $stats['total_chargebacks'] ?? 0 }}</div>
            <div class="k-lbl">Total CB</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-time k-icon"></i>
            <div class="k-val">{{ $stats['yet_to_retain'] ?? 0 }}</div>
            <div class="k-lbl">Yet to Retain</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-check-circle k-icon"></i>
            <div class="k-val">{{ $stats['retained_today'] ?? 0 }}</div>
            <div class="k-lbl">Retained Today</div>
        </div>
        <div class="kpi-card k-gold ex-card">
            <i class="bx bx-trophy k-icon"></i>
            <div class="k-val">{{ $stats['retained_mtd'] ?? 0 }}</div>
            <div class="k-lbl">Retained MTD</div>
        </div>
        <div class="kpi-card k-purple ex-card">
            <i class="bx bx-revision k-icon"></i>
            <div class="k-val">{{ $stats['rewrite_count'] ?? 0 }}</div>
            <div class="k-lbl">Rewrites</div>
        </div>
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-shield-quarter k-icon"></i>
            <div class="k-val">{{ $stats['total_retained'] ?? 0 }}</div>
            <div class="k-lbl">Total Retained</div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-2">

        {{-- LEFT: Tables --}}
        <div class="col-xl-9 col-lg-8">

            {{-- Retained / Rewritten Sales Table --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-check-double"></i> Retained & Rewritten Sales</h6>
                    <div class="team-tabs">
                        <button class="team-tab-btn active" onclick="switchTab('retained')" id="retainedTab">
                            Retained (<span id="retainedCount">{{ $retainedLeads->count() }}</span>)
                        </button>
                        <button class="team-tab-btn" onclick="switchTab('rewrite')" id="rewriteTab">
                            Rewrites (<span id="rewriteCount">{{ $rewriteLeads->count() }}</span>)
                        </button>
                    </div>
                </div>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Carrier</th>
                                <th>Closer</th>
                                <th class="text-center">Premium</th>
                                <th class="text-center">Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                            {{-- Retained leads --}}
                            @forelse($retainedLeads as $lead)
                            <tr class="sales-row" data-type="retained">
                                <td>
                                    <i class="bx bx-user-circle me-1" style="color:var(--bs-gold);opacity:.7"></i>
                                    {{ $lead->cn_name ?? 'N/A' }}
                                </td>
                                <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                                <td>{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? 'N/A') }}</td>
                                <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="bd-mini bd-green">${{ number_format($lead->monthly_premium ?? 0, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="status-pill s-retained">Retained</span>
                                </td>
                                <td>{{ $lead->retained_at ? $lead->retained_at->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr class="sales-row" data-type="retained">
                                <td colspan="7" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">
                                    <i class="bx bx-inbox" style="font-size:1.2rem;display:block;margin-bottom:0.3rem;opacity:.5"></i>
                                    No retained sales yet
                                </td>
                            </tr>
                            @endforelse

                            {{-- Rewrite leads (hidden by default) --}}
                            @forelse($rewriteLeads as $lead)
                            <tr class="sales-row" data-type="rewrite" style="display:none">
                                <td>
                                    <i class="bx bx-user-circle me-1" style="color:#7c69ef;opacity:.7"></i>
                                    {{ $lead->cn_name ?? 'N/A' }}
                                </td>
                                <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                                <td>{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? 'N/A') }}</td>
                                <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="bd-mini bd-warn">${{ number_format($lead->monthly_premium ?? 0, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="status-pill s-rewrite">Rewrite</span>
                                </td>
                                <td>{{ $lead->sale_date ? $lead->sale_date->format('M d, Y') : ($lead->chargeback_marked_date ? $lead->chargeback_marked_date->format('M d, Y') : 'N/A') }}</td>
                            </tr>
                            @empty
                            <tr class="sales-row" data-type="rewrite" style="display:none">
                                <td colspan="7" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">
                                    <i class="bx bx-inbox" style="font-size:1.2rem;display:block;margin-bottom:0.3rem;opacity:.5"></i>
                                    No rewrite leads
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quick Action --}}
            <div class="ex-card sec-card">
                <div class="sec-body text-center py-3">
                    <a href="{{ route('retention.index') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-refresh me-1"></i> Go to Retention Management
                    </a>
                </div>
            </div>

        </div>

        {{-- RIGHT: Attendance + Retention Blocks --}}
        <div class="col-xl-3 col-lg-4">

            {{-- Retention Target --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-target-lock"></i> Retention Progress</h6>
                </div>
                <div class="sec-body">
                    <div class="target-chart-wrap">
                        <canvas id="retentionChart"></canvas>
                        <div class="target-info">
                            Chargebacks: <strong style="color:#c84646">{{ $stats['total_chargebacks'] ?? 0 }}</strong> &nbsp;|&nbsp;
                            Retained: <strong style="color:#1a8754">{{ $stats['total_retained'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Retention Summary Blocks --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-refresh"></i> Retention Summary</h6>
                </div>
                <div class="sec-body">
                    <div class="ret-row" style="margin-bottom:0.4rem">
                        <div class="ret-block r-cb">
                            <div class="r-val">{{ $stats['total_chargebacks'] ?? 0 }}</div>
                            <div class="r-lbl">Chargebacks</div>
                        </div>
                        <div class="ret-block r-ret">
                            <div class="r-val">{{ $stats['retained_today'] ?? 0 }}</div>
                            <div class="r-lbl">Today</div>
                        </div>
                    </div>
                    <div class="ret-row">
                        <div class="ret-block r-pend">
                            <div class="r-val">{{ $stats['yet_to_retain'] ?? 0 }}</div>
                            <div class="r-lbl">Pending</div>
                        </div>
                        <div class="ret-block r-rw">
                            <div class="r-val">{{ $stats['rewrite_count'] ?? 0 }}</div>
                            <div class="r-lbl">Rewrites</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Rewrite Alert --}}
    @if(($stats['rewrite_count'] ?? 0) > 0)
    <div class="ex-card att-alert" style="border-left-color: #7c69ef; background: linear-gradient(135deg, rgba(124,105,239,.06) 0%, rgba(124,105,239,.02) 100%); margin-top: 0.5rem;">
        <div>
            <strong><i class="bx bx-error-circle"></i> Rewrite Alert:</strong>
            <span>There are <strong>{{ $stats['rewrite_count'] }}</strong> sales that need to be rewritten (30+ days old chargebacks).</span>
        </div>
        <a href="{{ route('retention.index') }}" class="link-btn" style="border-color:#7c69ef;color:#5b49c7">View Rewrites</a>
    </div>
    @endif
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tab switching for retained/rewrite tables
function switchTab(tab) {
    document.getElementById('retainedTab').classList.toggle('active', tab === 'retained');
    document.getElementById('rewriteTab').classList.toggle('active', tab === 'rewrite');

    document.querySelectorAll('.sales-row').forEach(function(row) {
        row.style.display = row.getAttribute('data-type') === tab ? '' : 'none';
    });
}

// Retention Progress Doughnut Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('retentionChart');
    if (!ctx) return;

    const retained = {{ $stats['total_retained'] ?? 0 }};
    const pending = {{ $stats['yet_to_retain'] ?? 0 }};
    const rewrites = {{ $stats['rewrite_count'] ?? 0 }};
    const total = retained + pending + rewrites || 1;

    const style = getComputedStyle(document.documentElement);
    const surfaceColor = style.getPropertyValue('--bs-surface-500').trim() || '#6c757d';

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Retained', 'Pending', 'Rewrites'],
            datasets: [{
                data: [retained, pending, rewrites],
                backgroundColor: [
                    'rgba(52,195,143,.8)',
                    'rgba(241,180,76,.8)',
                    'rgba(124,105,239,.8)'
                ],
                borderColor: [
                    'rgba(52,195,143,1)',
                    'rgba(241,180,76,1)',
                    'rgba(124,105,239,1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            }
        },
        plugins: [{
            id: 'centerText',
            beforeDraw: function(chart) {
                const width = chart.width;
                const height = chart.height;
                const ctx = chart.ctx;
                ctx.restore();

                const fontSize = (height / 80).toFixed(2);
                ctx.font = "bold " + fontSize + "em sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = '#1a8754';

                const text = retained + "";
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 2 - 10;
                ctx.fillText(text, textX, textY);

                ctx.font = fontSize * 0.45 + "em sans-serif";
                ctx.fillStyle = surfaceColor;
                const pct = total > 0 ? Math.round((retained / total) * 100) : 0;
                const subText = pct + "% retained";
                const subTextX = Math.round((width - ctx.measureText(subText).width) / 2);
                const subTextY = height / 2 + 15;
                ctx.fillText(subText, subTextX, subTextY);
                ctx.save();
            }
        }]
    });
});


</script>
@endsection
