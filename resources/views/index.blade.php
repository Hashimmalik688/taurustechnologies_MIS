@extends('layouts.master')

@section('title', 'Company Overview')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Company Overview — CEO Dashboard
   ═══════════════════════════════════════════════════ */

.ex-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 0.65rem;
    box-shadow: 0 1px 5px rgba(0,0,0,.06);
    transition: box-shadow .2s;
}
.ex-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.09); }

/* KPI Grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.55rem;
    margin-bottom: 0.55rem;
}
@media (max-width: 768px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }

.kpi-card {
    padding: 0.75rem 0.85rem;
    border-radius: 0.65rem;
    position: relative; overflow: hidden;
    border: 1px solid rgba(255,255,255,.06);
    transition: transform .15s, box-shadow .15s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.1); }
.kpi-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    border-radius: 0.65rem 0.65rem 0 0;
}
.kpi-icon {
    width: 36px; height: 36px; border-radius: 0.45rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; margin-bottom: 0.6rem;
}
.kpi-val { font-size: 1.5rem; font-weight: 800; line-height: 1; letter-spacing: -.5px; }
.kpi-lbl { font-size: 0.62rem; text-transform: uppercase; font-weight: 700; letter-spacing: .5px; color: var(--bs-surface-500); margin-top: 0.25rem; }
.kpi-sub { font-size: 0.62rem; color: var(--bs-surface-400); margin-top: 0.15rem; }

.kpi-gold::before  { background: linear-gradient(90deg,#d4af37,#e8c84a); }
.kpi-gold  { background: rgba(212,175,55,.06); }
.kpi-gold  .kpi-icon { background: rgba(212,175,55,.12); color: #b89730; }
.kpi-gold  .kpi-val  { color: #b89730; }

.kpi-blue::before  { background: linear-gradient(90deg,#556ee6,#8b9cf7); }
.kpi-blue  { background: rgba(85,110,230,.06); }
.kpi-blue  .kpi-icon { background: rgba(85,110,230,.12); color: #556ee6; }
.kpi-blue  .kpi-val  { color: #556ee6; }

.kpi-green::before { background: linear-gradient(90deg,#34c38f,#6eddb8); }
.kpi-green { background: rgba(52,195,143,.06); }
.kpi-green .kpi-icon { background: rgba(52,195,143,.12); color: #1a8754; }
.kpi-green .kpi-val  { color: #1a8754; }

.kpi-purple::before { background: linear-gradient(90deg,#7c69ef,#a899f5); }
.kpi-purple { background: rgba(124,105,239,.06); }
.kpi-purple .kpi-icon { background: rgba(124,105,239,.12); color: #5b49c7; }
.kpi-purple .kpi-val  { color: #5b49c7; }

/* Section Cards */
.sec-card { margin-bottom: 0.6rem; overflow: hidden; padding: 0; }
.sec-hdr {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.55rem 0.85rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    flex-wrap: wrap; gap: 0.4rem;
}
.sec-hdr h6 {
    margin: 0; font-size: 0.78rem; font-weight: 700;
    display: flex; align-items: center; gap: 0.35rem;
    text-transform: uppercase; letter-spacing: .3px;
}
.sec-hdr h6 i { opacity: .6; font-size: 1rem; }
.sec-body { padding: 0.65rem 0.85rem; }

/* Team tabs */
.team-tabs { display: flex; gap: 0.3rem; }
.team-tab-btn {
    font-size: 0.68rem; font-weight: 600; padding: 0.22rem 0.65rem;
    border-radius: 1rem; border: 1px solid var(--bs-surface-300);
    background: transparent; color: var(--bs-surface-500);
    cursor: pointer; transition: all .15s;
}
.team-tab-btn.active { background: var(--bs-gold, #d4af37); border-color: var(--bs-gold); color: #fff; }
.team-tab-btn:hover:not(.active) { border-color: var(--bs-gold); color: var(--bs-gold); }

/* Table */
.ex-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.75rem; }
.ex-tbl thead th {
    text-transform: uppercase; font-size: 0.6rem; font-weight: 700;
    letter-spacing: .5px; color: var(--bs-surface-500);
    padding: 0.4rem 0.6rem; border-bottom: 1px solid var(--bs-surface-200);
    white-space: nowrap; background: var(--bs-surface-100);
    position: sticky; top: 0; z-index: 1;
}
.ex-tbl tbody td { padding: 0.42rem 0.6rem; border-bottom: 1px solid rgba(0,0,0,.03); vertical-align: middle; }
.ex-tbl tbody tr { transition: background .1s; }
.ex-tbl tbody tr:hover { background: rgba(212,175,55,.03); }
.ex-tbl tfoot td { padding: 0.45rem 0.6rem; font-weight: 700; font-size: 0.72rem; border-top: 2px solid rgba(0,0,0,.08); background: var(--bs-surface-100); }

.scroll-tbl { max-height: 210px; overflow-y: auto; }
.scroll-tbl::-webkit-scrollbar { width: 3px; }
.scroll-tbl::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

/* Badges */
.bd { font-size: 0.62rem; font-weight: 700; padding: 0.18rem 0.45rem; border-radius: 0.3rem; display: inline-block; min-width: 24px; text-align: center; }
.bd-blue   { background: rgba(85,110,230,.12);  color: #556ee6; }
.bd-green  { background: rgba(52,195,143,.12);  color: #1a8754; }
.bd-red    { background: rgba(244,106,106,.12); color: #c84646; }
.bd-teal   { background: rgba(80,165,241,.12);  color: #2b81c9; }
.bd-gold   { background: rgba(212,175,55,.12);  color: #b89730; }

/* Pipeline */
.pipeline-flow {
    display: flex; align-items: stretch;
    background: var(--bs-surface-100); border-radius: 0.6rem;
    overflow: hidden; border: 1px solid rgba(0,0,0,.06);
    margin-bottom: 0.7rem;
}
.pipeline-stage {
    flex: 1; text-align: center; padding: 0.7rem 0.4rem;
    position: relative; border-right: 1px solid rgba(0,0,0,.06);
}
.pipeline-stage:last-child { border-right: none; }
.pipeline-stage::after {
    content: '›'; position: absolute; right: -8px; top: 50%;
    transform: translateY(-50%); font-size: 1.2rem; font-weight: 700;
    color: var(--bs-surface-400); z-index: 1;
}
.pipeline-stage:last-child::after { display: none; }
.pipeline-stage .ps-icon { font-size: 1.1rem; margin-bottom: 0.2rem; display: block; }
.pipeline-stage .ps-val { font-size: 1.25rem; font-weight: 800; line-height: 1; }
.pipeline-stage .ps-lbl { font-size: 0.58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--bs-surface-500); margin-top: 0.15rem; }

.ps-gold  { background: rgba(212,175,55,.06); }
.ps-gold  .ps-icon, .ps-gold  .ps-val { color: #b89730; }
.ps-blue  { background: rgba(85,110,230,.06); }
.ps-blue  .ps-icon, .ps-blue  .ps-val { color: #556ee6; }
.ps-green { background: rgba(52,195,143,.06); }
.ps-green .ps-icon, .ps-green .ps-val { color: #1a8754; }
.ps-red   { background: rgba(244,106,106,.06); }
.ps-red   .ps-icon, .ps-red   .ps-val { color: #c84646; }

/* Revenue bars */
.rev-carrier-bar { display: flex; align-items: center; gap: 0.5rem; padding: 0.32rem 0; border-bottom: 1px solid rgba(0,0,0,.03); font-size: 0.73rem; }
.rev-carrier-bar:last-child { border: none; }
.rev-bar-track { flex: 1; height: 6px; background: var(--bs-surface-200); border-radius: 3px; overflow: hidden; min-width: 40px; }
.rev-bar-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg,#34c38f,#6eddb8); }
.rev-carrier-name { min-width: 80px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rev-premium-val { min-width: 55px; text-align: right; font-weight: 700; color: #1a8754; font-size: 0.68rem; }

/* Attendance */
.att-caps { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.att-cap { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.18rem 0.5rem; border-radius: 1.5rem; font-size: 0.72rem; font-weight: 700; }
.cap-p { background: rgba(52,195,143,.1);  color: #1a8754; border: 1px solid rgba(52,195,143,.2); }
.cap-a { background: rgba(244,106,106,.1); color: #c84646; border: 1px solid rgba(244,106,106,.2); }
.cap-l { background: rgba(255,171,0,.1);   color: #b37a00; border: 1px solid rgba(255,171,0,.2); }
.cap-h { background: rgba(80,141,237,.1);  color: #3b6fc0; border: 1px solid rgba(80,141,237,.2); }

.att-list { max-height: 190px; overflow-y: auto; }
.att-list::-webkit-scrollbar { width: 3px; }
.att-list::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }
.att-row { display: flex; justify-content: space-between; align-items: center; padding: 0.32rem 0.1rem; border-bottom: 1px solid rgba(0,0,0,.03); font-size: 0.73rem; }
.att-row:last-child { border: none; }
.att-pill { font-size: 0.58rem; font-weight: 700; padding: 0.12rem 0.42rem; border-radius: 1rem; text-transform: uppercase; letter-spacing: .3px; }
.att-pill.p { background: rgba(52,195,143,.12); color: #1a8754; }
.att-pill.a { background: rgba(244,106,106,.12); color: #c84646; }
.att-pill.l { background: rgba(255,171,0,.12); color: #b37a00; }
.att-pill.h { background: rgba(80,141,237,.12); color: #3b6fc0; }



/* Period badge */
.period-badge { font-size: 0.6rem; font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 1rem; background: rgba(52,195,143,.1); color: #1a8754; border: 1px solid rgba(52,195,143,.2); }

/* View Mode Toggle */
.view-toggle { display: flex; gap: 0.3rem; }
.view-tog-btn {
    font-size: 0.7rem; font-weight: 700; padding: 0.3rem 0.8rem;
    border-radius: 1rem; border: 1px solid var(--bs-surface-300);
    background: transparent; color: var(--bs-surface-500);
    cursor: pointer; transition: all .15s; letter-spacing: .3px;
}
.view-tog-btn.active { background: var(--bs-gold, #d4af37); border-color: var(--bs-gold); color: #fff; }
.view-tog-btn:hover:not(.active) { border-color: var(--bs-gold); color: var(--bs-gold); }
.view-tog-btn .period-sm { font-size: .58rem; opacity: .75; font-weight: 500; }

/* Misc */
.link-btn { font-size: 0.62rem; padding: 0.18rem 0.45rem; border-radius: 0.3rem; border: 1px solid var(--bs-surface-300); background: transparent; color: var(--bs-surface-500); cursor: pointer; text-decoration: none; transition: all .15s; }
.link-btn:hover { border-color: var(--bs-gold); color: var(--bs-gold); }
.last-updated { font-size: 0.6rem; color: var(--bs-surface-400); }

.att-alert { padding: 0.6rem 0.9rem; margin-bottom: 0.65rem; border-left: 3px solid #f1b44c; background: linear-gradient(135deg,rgba(241,180,76,.07) 0%,rgba(241,180,76,.02) 100%); border-radius: 0 0.5rem 0.5rem 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; font-size: 0.78rem; }
.att-alert strong { font-weight: 700; }
.btn-mark { font-size: 0.7rem; padding: 0.25rem 0.65rem; border-radius: 0.35rem; border: none; cursor: pointer; font-weight: 600; }
.btn-mark.primary { background: var(--bs-gold); color: #fff; }
.btn-mark.secondary { background: var(--bs-surface-200); color: var(--bs-surface-600); }
</style>
@endsection

@section('content')

@if(session('attendance_manual_needed'))
<div class="ex-card att-alert" id="attendance-manual-banner">
    <div>
        <strong><i class="bx bx-time-five"></i> Mark Attendance:</strong>
        <span>{{ session('attendance_manual_needed') }}</span>
    </div>
    <div class="d-flex gap-2">
        <button id="markAttendanceBtn" class="btn-mark primary">Mark Attendance</button>
        <button id="markAttendanceForceBtn" class="btn-mark secondary">Force</button>
    </div>
</div>
<script>
(function(){
    const btn=document.getElementById('markAttendanceBtn'),btnForce=document.getElementById('markAttendanceForceBtn'),banner=document.getElementById('attendance-manual-banner'),token=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    function postMark(f){btn.disabled=true;btnForce.disabled=true;fetch('{{ route('attendance.mark-manual.post') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},body:JSON.stringify({force_office:f?1:0})}).then(r=>r.json()).then(d=>{if(d.success){alert(d.message||'Marked');if(banner)banner.style.display='none';setTimeout(()=>location.reload(),600);}else{alert(d.message||'Could not mark');btn.disabled=false;btnForce.disabled=false;}}).catch(e=>{console.error(e);alert('Network error');btn.disabled=false;btnForce.disabled=false;});}
    btn&&btn.addEventListener('click',()=>postMark(false));
    btnForce&&btnForce.addEventListener('click',()=>{if(confirm('Force mark attendance?'))postMark(true);});
})();
</script>
@endif

{{-- View Mode Toggle + Period Selector --}}
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <span style="font-size:.62rem;color:var(--bs-surface-400);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Period:</span>
        <select id="periodSelector" onchange="changePeriod(this.value)" style="font-size:.72rem;font-weight:600;padding:.25rem .55rem;border-radius:.4rem;border:1px solid var(--bs-surface-300);background:var(--bs-card-bg);color:inherit;cursor:pointer;">
            @php
                $periodOptions = [];
                // Start from the current period (rev_period_start is already the correct anchor)
                $p = \Carbon\Carbon::createFromFormat('Y-m', $selected_period)->setDay(3);
                for ($i = 0; $i < 12; $i++) {
                    $pStart = $p->copy();
                    $pEnd   = $p->copy()->addMonthNoOverflow()->setDay(3);
                    $periodOptions[] = [
                        'value' => $pStart->format('Y-m'),
                        'label' => $pStart->format('M j') . ' → ' . $pEnd->format('M j, Y'),
                    ];
                    $p->subMonthNoOverflow()->setDay(3);
                }
            @endphp
            @foreach($periodOptions as $opt)
            <option value="{{ $opt['value'] }}" {{ $opt['value'] === $selected_period ? 'selected' : '' }}>{{ $opt['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span style="font-size:.62rem;color:var(--bs-surface-400);font-weight:600;text-transform:uppercase;letter-spacing:.5px">View:</span>
        <div class="view-toggle">
            <button class="view-tog-btn active" id="toggleMTD" onclick="setViewMode('mtd')">MTD <span class="period-sm" id="toggleMTDLabel">{{ $revenue_period_label }}</span></button>
            <button class="view-tog-btn" id="toggleToday" onclick="setViewMode('today')">Today · PT</button>
        </div>
    </div>
</div>

{{-- ROW 1: Primary KPIs --}}
<div class="kpi-grid mb-1">
    <div class="kpi-card kpi-gold ex-card">
        <div class="kpi-icon"><i class="bx bx-calendar-week"></i></div>
        <div class="kpi-val" id="kpiDailyAvg" data-mtd="{{ number_format($daily_avg_premium, 0) }}" data-today="{{ number_format($today_revenue, 0) }}">${{ number_format($daily_avg_premium, 0) }}</div>
        <div class="kpi-lbl" id="kpiDailyAvgLbl">Daily Avg Premium · MTD</div>
        <div class="kpi-sub" id="kpiDailyAvgSub">{{ $distinct_sale_days }} sale days · {{ $revenue_period_label }}</div>
    </div>
    <div class="kpi-card kpi-green ex-card">
        <div class="kpi-icon"><i class="bx bx-dollar-circle"></i></div>
        <div class="kpi-val" id="kpiEstRevenue" data-mtd="{{ number_format($est_commission, 0) }}" data-today="{{ number_format($today_est_commission, 0) }}">${{ number_format($est_commission, 0) }}</div>
        <div class="kpi-lbl" id="kpiEstRevenueLbl">Est. Revenue · MTD</div>
        <div class="kpi-sub" id="kpiEstRevenueSub">{{ $revenue_period_label }}</div>
    </div>
    <div class="kpi-card kpi-purple ex-card">
        <div class="kpi-icon"><i class="bx bx-user-check"></i></div>
        <div class="kpi-val"><span id="attPresent">{{ $present_count }}</span><span style="font-size:.75em;font-weight:500;opacity:.55">/</span><span id="attTotal">{{ $total_attendance_count }}</span></div>
        <div class="kpi-lbl">Attendance</div>
        <div class="kpi-sub">Present / Total</div>
    </div>
</div>

{{-- Pipeline Flow --}}
<div class="pipeline-flow ex-card mb-2">
    <div class="pipeline-stage ps-blue">
        <i class="bx bx-send ps-icon"></i>
        <div class="ps-val" id="pipeSubmitted" data-mtd="{{ $submitted_count }}" data-today="{{ $today_sales }}">{{ $submitted_count }}</div>
        <div class="ps-lbl" id="pipeLblSubmitted">Submitted MTD</div>
    </div>
    <div class="pipeline-stage ps-green">
        <i class="bx bx-check-double ps-icon"></i>
        <div class="ps-val" id="pipeApproved" data-mtd="{{ $approved_count }}" data-today="{{ $today_approved }}">{{ $approved_count }}</div>
        <div class="ps-lbl" id="pipeLblApproved">Approved MTD</div>
    </div>
    <div class="pipeline-stage ps-red">
        <i class="bx bx-x-circle ps-icon"></i>
        <div class="ps-val" id="pipeDeclined" data-mtd="{{ $sub_declined_count }}" data-today="{{ $today_declined }}">{{ $sub_declined_count }}</div>
        <div class="ps-lbl" id="pipeLblDeclined">Declined MTD</div>
    </div>
    <div class="pipeline-stage" style="background:rgba(212,175,55,.06)">
        <i class="bx bx-stats ps-icon" style="color:#b89730"></i>
        <div class="ps-val" style="color:#b89730" id="pipeApprovalRate">{{ $submitted_count > 0 ? round($approved_count / $submitted_count * 100) : 0 }}%</div>
        <div class="ps-lbl">Approval Rate</div>
    </div>
</div>

{{-- Main Grid --}}
<div class="row g-2">

    {{-- LEFT --}}
    <div class="col-xl-8 col-lg-7">

        {{-- Team Performance --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-group"></i> Team Performance</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="last-updated"><i class="bx bx-time-five"></i> <span id="lastUpdated">–</span></span>
                    <div class="team-tabs">
                        <button class="team-tab-btn active" onclick="switchTeam('peregrine')" id="peregrineTab">Peregrine (<span id="peregrineCount">{{ $peregrine_count ?? 0 }}</span>)</button>
                        <button class="team-tab-btn" onclick="switchTeam('ravens')" id="ravensTab">Ravens (<span id="ravensCount">{{ $ravens_count ?? 0 }}</span>)</button>
                    </div>
                </div>
            </div>
            <div class="scroll-tbl">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>Closer</th>
                            <th class="text-center">Today</th>
                            <th class="text-center">MTD</th>
                            <th class="text-center">Approved</th>
                            <th class="text-center">Declined</th>
                            <th style="min-width:60px">Progress</th>
                        </tr>
                    </thead>
                    <tbody id="closerTable">
                        @forelse($sales_per_closer as $closer)
                        <tr class="closer-row" data-team="{{ $closer['team'] ?? '' }}">
                            <td><i class="bx bx-user-circle me-1" style="color:var(--bs-gold);opacity:.7"></i>{{ $closer['closer'] ?? 'N/A' }}</td>
                            <td class="text-center"><span class="bd bd-teal">{{ $closer['today'] ?? 0 }}</span></td>
                            <td class="text-center"><span class="bd bd-blue">{{ $closer['mtd'] ?? 0 }}</span></td>
                            <td class="text-center"><span class="bd bd-green">{{ $closer['approvedMTD'] ?? 0 }}</span></td>
                            <td class="text-center"><span class="bd bd-red">{{ $closer['declinedMTD'] ?? 0 }}</span></td>
                            <td>
                                @php $pct = ($closer['mtd'] ?? 0) > 0 ? min(100, round(($closer['today'] ?? 0) / max(1,$closer['mtd']) * 100)) : 0; @endphp
                                <div style="height:6px;background:var(--bs-surface-200);border-radius:3px;overflow:hidden">
                                    <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,#556ee6,#8b9cf7);border-radius:3px"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No closers data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Revenue Summary --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-dollar-circle"></i> Revenue Summary</h6>
                <span class="period-badge" id="revSummaryPeriod">{{ $revenue_period_label }}</span>
            </div>
            <div class="sec-body">
                <div class="row g-2 mb-3">
                    <div class="col-4 text-center">
                        <div id="revTotalPremium" style="font-size:1.3rem;font-weight:800;color:#1a8754">${{ number_format($total_revenue, 0) }}</div>
                        <div style="font-size:0.6rem;text-transform:uppercase;font-weight:700;letter-spacing:.4px;color:var(--bs-surface-500)">Total Premium</div>
                    </div>
                    <div class="col-4 text-center">
                        <div id="revSubmissions" style="font-size:1.3rem;font-weight:800;color:#556ee6">{{ $mtd_sales }}</div>
                        <div style="font-size:0.6rem;text-transform:uppercase;font-weight:700;letter-spacing:.4px;color:var(--bs-surface-500)">Contracted</div>
                    </div>
                    <div class="col-4 text-center">
                        <div id="revAvgSale" style="font-size:1.3rem;font-weight:800;color:#b89730">${{ $mtd_sales > 0 ? number_format($total_revenue / $mtd_sales, 0) : '0' }}</div>
                        <div style="font-size:0.6rem;text-transform:uppercase;font-weight:700;letter-spacing:.4px;color:var(--bs-surface-500)">Avg / Sale</div>
                    </div>
                </div>
                <div id="revCarrierBars">
                    @php $maxPremium = collect($revenue_by_carrier)->max('premium') ?: 1; @endphp
                    @forelse($revenue_by_carrier as $carrier)
                    <div class="rev-carrier-bar">
                        <div class="rev-carrier-name">{{ $carrier['carrier'] ?: '—' }}</div>
                        <div class="rev-bar-track"><div class="rev-bar-fill" style="width:{{ min(100, round($carrier['premium'] / $maxPremium * 100)) }}%"></div></div>
                        <div style="font-size:0.65rem;color:var(--bs-surface-400);min-width:22px;text-align:right">{{ $carrier['count'] }}</div>
                        <div class="rev-premium-val">${{ number_format($carrier['premium'], 0) }}</div>
                    </div>
                    @empty
                    <div class="text-center py-2" style="color:var(--bs-surface-400);font-size:.78rem">No revenue data for this period</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Carrier Performance --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-bar-chart-alt-2"></i> Carrier Performance</h6>
                <span class="period-badge">{{ $revenue_period_label }}</span>
            </div>
            <div class="sec-body" style="padding-top:.4rem">
                <div style="position:relative;height:160px"><canvas id="carrierChart"></canvas></div>
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="col-xl-4 col-lg-5">

        {{-- Manager Submission Today (PT) --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-shield-check"></i> Manager Submission</h6>
                <span class="bd bd-gold" style="font-size:.6rem">Today · PT</span>
            </div>
            <div class="scroll-tbl" style="max-height:175px">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>Manager</th>
                            <th class="text-center">PC</th>
                            <th class="text-center">Dec</th>
                            <th style="text-align:right">Premium</th>
                        </tr>
                    </thead>
                    <tbody id="managerTable">
                        @forelse($manager_breakdown as $mgr)
                        <tr>
                            <td style="font-weight:600;font-size:.72rem">{{ $mgr['manager_name'] }}</td>
                            <td class="text-center"><span class="bd bd-green">{{ $mgr['pending_contract'] }}</span></td>
                            <td class="text-center"><span class="bd bd-red">{{ $mgr['declined'] }}</span></td>
                            <td style="text-align:right;font-weight:700;color:#1a8754;font-size:.72rem">${{ number_format($mgr['total_premium'], 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No submissions today</td></tr>
                        @endforelse
                    </tbody>
                    @if(count($manager_breakdown) > 0)
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="text-center"><span class="bd bd-green">{{ $mgr_total_pending }}</span></td>
                            <td class="text-center"><span class="bd bd-red">{{ $mgr_total_declined }}</span></td>
                            <td style="text-align:right;color:#1a8754">${{ number_format($mgr_total_premium, 0) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if(count($manager_breakdown) > 0)
            <div class="sec-body" style="padding-top:.4rem;padding-bottom:.6rem">
                <div style="position:relative;height:85px"><canvas id="managerChart"></canvas></div>
            </div>
            @endif
        </div>

        {{-- Closer Sales Today Chart --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-bar-chart-squares"></i> Closer Sales <span id="closerChartLbl">Today</span></h6>
                <span class="bd bd-gold" style="font-size:.6rem">Top 8</span>
            </div>
            <div class="sec-body" style="padding-top:.4rem">
                <div style="position:relative;height:130px"><canvas id="closerTodayChart"></canvas></div>
            </div>
        </div>

        {{-- Attendance --}}
        @php
            $lateCount=0;$halfDayCount=0;
            foreach($attendance as $a){$s=strtolower($a['status']??'');if($s==='late')$lateCount++;elseif(in_array($s,['half day','half_day','halfday']))$halfDayCount++;}
        @endphp
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-time-five"></i> Attendance</h6>
                <div class="att-caps">
                    <span class="att-cap cap-p"><span id="presentCount">{{ $present_count }}</span>&nbsp;P</span>
                    <span class="att-cap cap-a"><span id="absentCount">{{ $absent_count }}</span>&nbsp;A</span>
                    <span class="att-cap cap-l"><span id="lateCount">{{ $lateCount }}</span>&nbsp;L</span>
                    <span class="att-cap cap-h"><span id="halfDayCount">{{ $halfDayCount }}</span>&nbsp;HD</span>
                </div>
            </div>
            <div class="sec-body">
                <div class="att-list" id="attendanceTable">
                    @forelse($attendance as $att)
                    @php
                        $s=strtolower($att['status']??'');
                        if($s==='late'){$pc='l';$pt='Late';}
                        elseif(in_array($s,['half day','half_day','halfday'])){$pc='h';$pt='HD';}
                        elseif(in_array($s,['present','p','on time','ontime'])){$pc='p';$pt='Present';}
                        else{$pc='a';$pt=ucfirst($att['status']??'Absent');}
                    @endphp
                    <div class="att-row">
                        <span style="font-weight:500">{{ $att['name'] ?? 'N/A' }}</span>
                        <span class="att-pill {{ $pc }}">{{ $pt }}</span>
                    </div>
                    @empty
                    <div class="text-center py-2" style="color:var(--bs-surface-400);font-size:.78rem">No attendance data</div>
                    @endforelse
                </div>
            </div>
        </div>



    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var SD = {
    done:              {{ $done_count }},
    submitted:         {{ $submitted_count }},
    approved:          {{ $approved_count }},
    declined:          {{ $sub_declined_count }},
    totalRevenue:      {{ $total_revenue }},
    mtdSales:          {{ $mtd_sales }},
    dailyAvgPremium:   {{ $daily_avg_premium }},
    estCommission:     {{ $est_commission }},
    distinctSaleDays:  {{ $distinct_sale_days }},
    todaySales:        {{ $today_sales }},
    todayRevenue:      {{ $today_revenue }},
    todayApproved:     {{ $today_approved }},
    todayDeclined:     {{ $today_declined }},
    todayEstCommission:{{ $today_est_commission }},
    presentCount:      {{ $present_count }},
    totalAttendance:   {{ $total_attendance_count }},
    salesPerCloser:    @json($sales_per_closer),
    managerBreakdown:  @json($manager_breakdown),
    attendance:        @json($attendance),
    revByCarrier:      @json($revenue_by_carrier),
    revPeriodLabel:    '{{ $revenue_period_label }}',
    selectedPeriod:    '{{ $selected_period }}',
};

var currentTeam = 'peregrine';
var allData = SD;
var charts = {};

function fmt(n) { return new Intl.NumberFormat().format(Math.round(n)); }

var currentMode = 'mtd';
var currentPeriod = SD.selectedPeriod;

function changePeriod(val) {
    currentPeriod = val;
    fetch('{{ route('dashboard.kpi-data') }}?period=' + val)
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            allData = Object.assign({}, allData, d);
            rebuildDataAttrs(d);
            setViewMode(currentMode);
            const lbl = document.getElementById('toggleMTDLabel');
            if (lbl && d.revPeriodLabel) lbl.textContent = d.revPeriodLabel;
            if (d.revByCarrier) { allData.revByCarrier = d.revByCarrier; buildCarrierChart(); }
            if (d.salesPerCloser) {
                renderClosers(d.salesPerCloser.filter(c=>(c.team||'').toLowerCase()===currentTeam));
                buildCloserTodayChart(d.salesPerCloser);
            }
            if (d.managerBreakdown) { renderManagerTable(d.managerBreakdown); buildManagerChart(d.managerBreakdown); }
            if (d.attendance) renderAttendance(d.attendance);
        })
        .catch(e => console.error('Period change error:', e));
}

function updateKPIs(d) {
    allData = Object.assign({}, allData, d);
    rebuildDataAttrs(d);
    setViewMode(currentMode);

    $('#attPresent').text(d.presentCount ?? SD.presentCount);
    $('#attTotal').text(d.totalAttendance ?? SD.totalAttendance);

    if (d.revPeriodLabel) {
        const lbl = document.getElementById('toggleMTDLabel');
        if (lbl) lbl.textContent = d.revPeriodLabel;
        allData.revPeriodLabel = d.revPeriodLabel;
    }
    if (d.salesPerCloser) {
        $('#peregrineCount').text(d.salesPerCloser.filter(c=>(c.team||'').toLowerCase()==='peregrine').length);
        $('#ravensCount').text(d.salesPerCloser.filter(c=>(c.team||'').toLowerCase()==='ravens').length);
        renderClosers(d.salesPerCloser.filter(c=>(c.team||'').toLowerCase()===currentTeam));
        buildCloserTodayChart(d.salesPerCloser);
    }
    if (d.revByCarrier) { allData.revByCarrier = d.revByCarrier; buildCarrierChart(); }
    if (d.managerBreakdown) { renderManagerTable(d.managerBreakdown); buildManagerChart(d.managerBreakdown); }
    if (d.attendance) renderAttendance(d.attendance);
    $('#lastUpdated').text(new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit',hour12:true}));
}

function rebuildDataAttrs(d) {
    const mtdSales    = d.mtdSales ?? d.done ?? 0;
    const submitted   = d.submitted ?? 0;
    const tdSales     = d.todaySales ?? 0;
    const mtdRev      = d.totalRevenue ?? 0;
    const tdRev       = d.todayRevenue ?? 0;
    const mtdApp      = d.approved ?? 0;
    const tdApp       = d.todayApproved ?? 0;
    const mtdDec      = d.declined ?? 0;
    const tdDec       = d.todayDeclined ?? 0;
    const dailyAvg    = d.dailyAvgPremium ?? (d.distinctSaleDays > 0 ? Math.round(mtdRev / d.distinctSaleDays) : 0);
    const estComm     = d.estCommission ?? 0;
    const todayEst    = d.todayEstCommission ?? 0;

    const setData = (id, mtd, today) => {
        const el = document.getElementById(id); if (!el) return;
        el.dataset.mtd = mtd; el.dataset.today = today;
    };
    setData('kpiDailyAvg',    fmt(dailyAvg),    fmt(tdRev));
    setData('kpiEstRevenue',  fmt(estComm),      fmt(todayEst));
    setData('pipeSubmitted',  submitted,         tdSales);
    setData('pipeApproved',   mtdApp,            tdApp);
    setData('pipeDeclined',   mtdDec,            tdDec);

    // Revenue Summary stats (always show MTD — period-based, not toggle-sensitive)
    const revPrem = document.getElementById('revTotalPremium');
    const revSub  = document.getElementById('revSubmissions');
    const revAvg  = document.getElementById('revAvgSale');
    if (revPrem) revPrem.textContent = '$' + fmt(mtdRev);
    if (revSub)  revSub.textContent  = mtdSales;
    if (revAvg)  revAvg.textContent  = '$' + (mtdSales > 0 ? fmt(Math.round(mtdRev / mtdSales)) : '0');

    // Revenue Summary period label
    const revPeriodEl = document.getElementById('revSummaryPeriod');
    if (revPeriodEl && d.revPeriodLabel) revPeriodEl.textContent = d.revPeriodLabel;

    // Carrier bars in Revenue Summary
    if (d.revByCarrier) renderCarrierBars(d.revByCarrier);
}

function setViewMode(mode) {
    currentMode = mode;
    document.getElementById('toggleMTD').classList.toggle('active', mode === 'mtd');
    document.getElementById('toggleToday').classList.toggle('active', mode === 'today');
    const suffix    = mode === 'mtd' ? 'MTD' : 'Today';
    const periodLbl = mode === 'mtd' ? (allData.revPeriodLabel || SD.revPeriodLabel) : 'Today · PT';
    const days      = allData.distinctSaleDays || SD.distinctSaleDays || 1;

    // Dollar KPIs
    ['kpiDailyAvg', 'kpiEstRevenue'].forEach(id => {
        const el = document.getElementById(id); if (!el) return;
        el.textContent = '$' + (el.dataset[mode] !== undefined ? el.dataset[mode] : '0');
    });
    // Numeric KPIs
    ['pipeSubmitted', 'pipeApproved', 'pipeDeclined'].forEach(id => {
        const el = document.getElementById(id); if (!el) return;
        if (el.dataset[mode] !== undefined) el.textContent = el.dataset[mode];
    });

    // Labels
    const lblMap = {
        kpiDailyAvgLbl:   mode === 'mtd' ? 'Daily Avg Premium · MTD' : 'Today Total Premium',
        kpiDailyAvgSub:   mode === 'mtd' ? days + ' sale days · ' + periodLbl : periodLbl,
        kpiEstRevenueLbl: 'Est. Revenue · ' + suffix,
        kpiEstRevenueSub: periodLbl,
        pipeLblSubmitted: 'Submitted ' + suffix,
        pipeLblApproved:  'Approved ' + suffix,
        pipeLblDeclined:  'Declined ' + suffix,
        closerChartLbl:   suffix,
    };
    Object.entries(lblMap).forEach(([id, txt]) => { const el = document.getElementById(id); if (el) el.textContent = txt; });

    // Approval rate
    const sub = +(document.getElementById('pipeSubmitted')?.dataset[mode] || 0);
    const app = +(document.getElementById('pipeApproved')?.dataset[mode]  || 0);
    const rateEl = document.getElementById('pipeApprovalRate');
    if (rateEl) rateEl.textContent = sub > 0 ? Math.round(app / sub * 100) + '%' : '0%';

    if (allData && allData.salesPerCloser) buildCloserTodayChart(allData.salesPerCloser);
}

function updateKPIs(d) {
    allData = Object.assign({}, allData, d);
    rebuildDataAttrs(d);
    setViewMode(currentMode);

    // Attendance (no toggle)
    $('#attPresent').text(d.presentCount ?? SD.presentCount);
    $('#attTotal').text(d.totalAttendance ?? SD.totalAttendance);

    if (d.revPeriodLabel) {
        const lbl = document.getElementById('toggleMTDLabel');
        if (lbl) lbl.textContent = d.revPeriodLabel;
        document.querySelectorAll('.period-badge').forEach(el => el.textContent = d.revPeriodLabel);
        allData.revPeriodLabel = d.revPeriodLabel;
    }
    if (d.salesPerCloser) {
        $('#peregrineCount').text(d.salesPerCloser.filter(c=>(c.team||'').toLowerCase()==='peregrine').length);
        $('#ravensCount').text(d.salesPerCloser.filter(c=>(c.team||'').toLowerCase()==='ravens').length);
        renderClosers(d.salesPerCloser.filter(c=>(c.team||'').toLowerCase()===currentTeam));
        updateCarrierChart(d.revByCarrier);
        buildCloserTodayChart(d.salesPerCloser);
    }
    if (d.managerBreakdown) renderManagerTable(d.managerBreakdown);
    if (d.attendance) renderAttendance(d.attendance);
    $('#lastUpdated').text(new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit',hour12:true}));
}

function switchTeam(team) {
    currentTeam = team;
    document.querySelectorAll('.team-tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById(team+'Tab').classList.add('active');
    if (allData && allData.salesPerCloser)
        renderClosers(allData.salesPerCloser.filter(c=>(c.team||'').toLowerCase()===team));
}

function renderClosers(closers) {
    const tbody = document.getElementById('closerTable');
    if (!closers || !closers.length) {
        tbody.innerHTML='<tr><td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No closers in this team</td></tr>';
        return;
    }
    tbody.innerHTML = closers.map(c=>{
        const pct=c.mtd>0?Math.min(100,Math.round(c.today/c.mtd*100)):0;
        return `<tr>
            <td><i class="bx bx-user-circle me-1" style="color:var(--bs-gold);opacity:.7"></i>${c.closer||'N/A'}</td>
            <td class="text-center"><span class="bd bd-teal">${c.today||0}</span></td>
            <td class="text-center"><span class="bd bd-blue">${c.mtd||0}</span></td>
            <td class="text-center"><span class="bd bd-green">${c.approved||c.approvedMTD||0}</span></td>
            <td class="text-center"><span class="bd bd-red">${c.declined||c.declinedMTD||0}</span></td>
            <td><div style="height:6px;background:var(--bs-surface-200);border-radius:3px;overflow:hidden"><div style="width:${pct}%;height:100%;background:linear-gradient(90deg,#556ee6,#8b9cf7);border-radius:3px"></div></div></td>
        </tr>`;
    }).join('');
}

function renderManagerTable(managers) {
    const tbody = document.getElementById('managerTable');
    if (!tbody) return;
    if (!managers || !managers.length) {
        tbody.innerHTML='<tr><td colspan="4" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No submissions today</td></tr>';
        return;
    }
    tbody.innerHTML = managers.map(m=>`
        <tr>
            <td style="font-weight:600;font-size:.72rem">${m.manager_name}</td>
            <td class="text-center"><span class="bd bd-green">${m.pending_contract}</span></td>
            <td class="text-center"><span class="bd bd-red">${m.declined}</span></td>
            <td style="text-align:right;font-weight:700;color:#1a8754;font-size:.72rem">$${fmt(m.total_premium||0)}</td>
        </tr>
    `).join('');
    buildManagerChart(managers);
}

function renderAttendance(team) {
    const list = document.getElementById('attendanceTable');
    if (!list) return;
    let p=0,a=0,l=0,h=0;
    list.innerHTML = team.map(t=>{
        const s=(t.status||'').toLowerCase();let cls,txt;
        if(s==='late'){cls='l';txt='Late';l++;}
        else if(['half day','half_day','halfday'].includes(s)){cls='h';txt='HD';h++;}
        else if(['present','p','on time','ontime'].includes(s)){cls='p';txt='Present';p++;}
        else{cls='a';txt='Absent';a++;}
        return `<div class="att-row"><span style="font-weight:500">${t.name}</span><span class="att-pill ${cls}">${txt}</span></div>`;
    }).join('');
    $('#presentCount').text(p);$('#absentCount').text(a);$('#lateCount').text(l);$('#halfDayCount').text(h);
}

function buildCarrierChart() {
    const ctx=document.getElementById('carrierChart');if(!ctx)return;
    const data=(allData.revByCarrier||SD.revByCarrier||[]);
    const labels = data.map(d=>d.carrier||'Unknown');
    const values = data.map(d=>d.premium||0);
    if (charts.carrierChart) {
        charts.carrierChart.data.labels = labels;
        charts.carrierChart.data.datasets[0].data = values;
        charts.carrierChart.update('none');
        return;
    }
    charts.carrierChart=new Chart(ctx,{
        type:'bar',
        data:{labels,datasets:[{label:'Premium',data:values,backgroundColor:'rgba(52,195,143,.5)',borderColor:'#34c38f',borderWidth:1.5,borderRadius:4}]},
        options:{responsive:true,maintainAspectRatio:false,animation:{duration:400},plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>'$'+fmt(c.parsed.y)}}},scales:{x:{grid:{display:false},ticks:{font:{size:9},maxRotation:30}},y:{grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{size:9},callback:v=>'$'+fmt(v)}}}}
    });
}

function renderCarrierBars(carriers) {
    const container = document.getElementById('revCarrierBars'); if (!container) return;
    if (!carriers || !carriers.length) {
        container.innerHTML = '<div class="text-center py-2" style="color:var(--bs-surface-400);font-size:.78rem">No revenue data for this period</div>';
        return;
    }
    const maxPremium = Math.max(...carriers.map(c => c.premium || 0)) || 1;
    container.innerHTML = carriers.slice(0, 5).map(c => {
        const pct = Math.min(100, Math.round((c.premium || 0) / maxPremium * 100));
        return `<div class="rev-carrier-bar">
            <div class="rev-carrier-name">${c.carrier || '—'}</div>
            <div class="rev-bar-track"><div class="rev-bar-fill" style="width:${pct}%"></div></div>
            <div style="font-size:0.65rem;color:var(--bs-surface-400);min-width:22px;text-align:right">${c.count}</div>
            <div class="rev-premium-val">$${fmt(c.premium || 0)}</div>
        </div>`;
    }).join('');
}

function buildManagerChart(data) {
    const ctx=document.getElementById('managerChart');if(!ctx||!data||!data.length)return;
    const labels = data.map(d=>d.manager_name.split(' ')[0]);
    const pending = data.map(d=>d.pending_contract);
    const declined = data.map(d=>d.declined);
    if (charts.managerChart) {
        charts.managerChart.data.labels = labels;
        charts.managerChart.data.datasets[0].data = pending;
        charts.managerChart.data.datasets[1].data = declined;
        charts.managerChart.update('none');
        return;
    }
    charts.managerChart=new Chart(ctx,{
        type:'bar',
        data:{labels,datasets:[
            {label:'Pending Contract',data:pending,backgroundColor:'rgba(52,195,143,.65)',borderRadius:3},
            {label:'Declined',data:declined,backgroundColor:'rgba(244,106,106,.65)',borderRadius:3}
        ]},
        options:{responsive:true,maintainAspectRatio:false,animation:{duration:400},plugins:{legend:{display:false}},scales:{x:{stacked:true,grid:{display:false},ticks:{font:{size:8}}},y:{stacked:true,grid:{display:false},ticks:{font:{size:8},stepSize:1}}}}
    });
}

function buildCloserTodayChart(salesPerCloser) {
    const src = salesPerCloser || SD.salesPerCloser || [];
    const isMtd = currentMode === 'mtd';
    const key = isMtd ? 'mtd' : 'today';
    const color  = isMtd ? 'rgba(85,110,230,.6)' : 'rgba(212,175,55,.6)';
    const border = isMtd ? '#556ee6' : '#d4af37';
    const label  = isMtd ? 'MTD' : 'Today';
    const raw = src.filter(c => (c[key]||0) > 0).sort((a,b) => (b[key]||0) - (a[key]||0)).slice(0, 8);
    const labels = raw.map(c => c.closer.split(' ')[0]);
    const values = raw.map(c => c[key] || 0);
    const ctx = document.getElementById('closerTodayChart'); if (!ctx) return;
    if (charts.closerTodayChart) {
        charts.closerTodayChart.data.labels = labels;
        charts.closerTodayChart.data.datasets[0].data = values;
        charts.closerTodayChart.data.datasets[0].backgroundColor = color;
        charts.closerTodayChart.data.datasets[0].borderColor = border;
        charts.closerTodayChart.data.datasets[0].label = label;
        charts.closerTodayChart.update('none');
        return;
    }
    charts.closerTodayChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label, data: values,
                backgroundColor: color,
                borderColor: border,
                borderWidth: 1.5, borderRadius: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, animation: { duration: 400 }, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 35 } }, y: { grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { size: 9 }, stepSize: 1 } } } }
    });
}

function updateClocks() {
    const now=new Date(),opts={hour:'2-digit',minute:'2-digit',hour12:true};
    [{id:'navEasternTime',tz:'America/New_York'},{id:'navCentralTime',tz:'America/Chicago'},{id:'navMountainTime',tz:'America/Denver'},{id:'navPacificTime',tz:'America/Los_Angeles'}]
    .forEach(z=>{const el=document.getElementById(z.id);if(el)el.textContent=now.toLocaleTimeString('en-US',{...opts,timeZone:z.tz});});
}

$(document).ready(function () {
    updateClocks();
    setInterval(updateClocks, 1000);

    // Seed allData from server-rendered SD
    allData = Object.assign({}, SD);

    renderClosers(SD.salesPerCloser.filter(c=>(c.team||'').toLowerCase()==='peregrine'));
    renderAttendance(SD.attendance||[]);
    renderManagerTable(SD.managerBreakdown||[]);
    buildCarrierChart();
    buildManagerChart(SD.managerBreakdown||[]);
    buildCloserTodayChart();

    // Live poll every 30s — pass current period so data stays in sync
    setInterval(function(){
        fetch('{{ route('dashboard.kpi-data') }}?period=' + encodeURIComponent(currentPeriod))
            .then(r=>r.json())
            .then(d=>{ if(d.success) updateKPIs(d); })
            .catch(e=>console.error('KPI poll error:',e));
    }, 30000);
});
</script>
@endsection
