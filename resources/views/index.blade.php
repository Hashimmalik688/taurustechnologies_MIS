@extends('layouts.master')

@section('title', 'Executive Dashboard')

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Executive Dashboard — Polished CRM Design
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

.kpi-card.k-gray    { background: rgba(108,117,125,.05); }
.kpi-card.k-gray::before    { background: linear-gradient(90deg, #6c757d, #95a0a8); }
.kpi-card.k-gray .k-val, .kpi-card.k-gray .k-icon { color: #6c757d; }

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

/* ── Attendance mini ── */
.att-mini-list {
    max-height: 180px;
    overflow-y: auto;
}
.att-mini-list::-webkit-scrollbar { width: 3px; }
.att-mini-list::-webkit-scrollbar-thumb { background: var(--bs-surface-300); border-radius: 3px; }

.att-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.3rem 0.15rem;
    border-bottom: 1px solid rgba(0,0,0,.03);
    font-size: 0.72rem;
}
.att-row:last-child { border: none; }
.att-row .att-name { font-weight: 500; }
.att-pill {
    font-size: 0.58rem;
    font-weight: 700;
    padding: 0.12rem 0.4rem;
    border-radius: 1rem;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.att-pill.p { background: rgba(52,195,143,.12); color: #1a8754; }
.att-pill.a { background: rgba(244,106,106,.12); color: #c84646; }
.att-pill.l { background: rgba(255,171,0,.12); color: #b37a00; }
.att-pill.h { background: rgba(80,141,237,.12); color: #3b6fc0; }

/* Attendance summary capsules */
.att-caps {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    margin-bottom: 0.5rem;
}
.att-cap {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.55rem;
    border-radius: 1.5rem;
    font-size: 0.72rem;
    font-weight: 700;
}
.att-cap.cap-p { background: rgba(52,195,143,.12); color: #1a8754; border: 1px solid rgba(52,195,143,.25); }
.att-cap.cap-a { background: rgba(244,106,106,.12); color: #c84646; border: 1px solid rgba(244,106,106,.25); }
.att-cap.cap-l { background: rgba(255,171,0,.12); color: #b37a00; border: 1px solid rgba(255,171,0,.25); }
.att-cap.cap-h { background: rgba(80,141,237,.12); color: #3b6fc0; border: 1px solid rgba(80,141,237,.25); }

/* ── Target Chart ── */
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

/* ── Retention mini blocks ── */
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

/* ── Chargebacks display ── */
.cb-display { text-align: center; padding: 0.3rem 0; }
.cb-big { font-size: 1.6rem; font-weight: 700; color: #c84646; line-height: 1; }
.cb-sub { font-size: 0.6rem; font-weight: 600; text-transform: uppercase; color: var(--bs-surface-500); margin-top: 0.15rem; }
.cb-amt { font-size: 0.95rem; font-weight: 700; color: #c84646; margin-top: 0.1rem; }

/* ── Scrollable table wrapper ── */
.scroll-tbl { max-height: 200px; overflow-y: auto; }
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

/* Alert compact */
.att-alert {
    padding: 0.55rem 0.85rem;
    margin-bottom: 0.65rem;
    border-left: 3px solid #f1b44c;
    background: linear-gradient(135deg, rgba(241,180,76,.06) 0%, rgba(241,180,76,.02) 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    font-size: 0.78rem;
}
.att-alert strong { font-weight: 700; }
.att-alert .btn-mark {
    font-size: 0.7rem;
    padding: 0.25rem 0.65rem;
    border-radius: 0.35rem;
    border: none;
    cursor: pointer;
    font-weight: 600;
}
.att-alert .btn-mark.primary { background: var(--bs-gold); color: #fff; }
.att-alert .btn-mark.secondary { background: var(--bs-surface-200); color: var(--bs-surface-600); }
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
            const btn = document.getElementById('markAttendanceBtn');
            const btnForce = document.getElementById('markAttendanceForceBtn');
            const banner = document.getElementById('attendance-manual-banner');
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function postMark(force) {
                btn.disabled = true;
                btnForce.disabled = true;

                fetch('{{ route('attendance.mark-manual.post') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ force_office: force ? 1 : 0 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Attendance marked successfully');
                        if (banner) banner.style.display = 'none';
                        setTimeout(() => location.reload(), 600);
                    } else {
                        alert(data.message || 'Could not mark attendance: ' + (data.debug_ip || ''));
                        btn.disabled = false;
                        btnForce.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error while marking attendance');
                    btn.disabled = false;
                    btnForce.disabled = false;
                });
            }

            btn && btn.addEventListener('click', function(){ postMark(false); });
            btnForce && btnForce.addEventListener('click', function(){
                if (confirm('Force mark attendance (this will override network check)?')) {
                    postMark(true);
                }
            });
        })();
    </script>
@endif

{{-- KPI Row 1 — Primary Metrics --}}
<div class="kpi-row">
    <div class="kpi-card k-gold ex-card">
        <i class="bx bx-trending-up k-icon"></i>
        <div class="k-val" id="salesToday">{{ $total_sales_today }}</div>
        <div class="k-lbl">Today</div>
    </div>
    <div class="kpi-card k-blue ex-card">
        <i class="bx bx-bar-chart-alt-2 k-icon"></i>
        <div class="k-val" id="salesMTD">{{ $total_monthly_sales }}</div>
        <div class="k-lbl">MTD Sales</div>
    </div>
    <div class="kpi-card k-green ex-card">
        <i class="bx bx-dollar-circle k-icon"></i>
        <div class="k-val" id="revenue">${{ number_format($total_revenue, 0) }}</div>
        <div class="k-lbl">Revenue</div>
    </div>
    <div class="kpi-card k-teal ex-card">
        <i class="bx bx-user-check k-icon"></i>
        <div class="k-val" id="activeTeam">{{ count($attendance) }}</div>
        <div class="k-lbl">Active</div>
    </div>
</div>

{{-- KPI Row 2 — Pipeline Status --}}
<div class="kpi-row">
    <div class="kpi-card k-blue ex-card">
        <i class="bx bx-send k-icon"></i>
        <div class="k-val" id="statusDone">{{ $done_count }}</div>
        <div class="k-lbl">Submitted</div>
    </div>
    <div class="kpi-card k-green ex-card">
        <i class="bx bx-check-double k-icon"></i>
        <div class="k-val" id="statusApproved">{{ $approved_count }}</div>
        <div class="k-lbl">Approved</div>
    </div>
    <div class="kpi-card k-warn ex-card">
        <i class="bx bx-loader-alt k-icon"></i>
        <div class="k-val" id="statusUW">{{ $underwriting_count }}</div>
        <div class="k-lbl">UW</div>
    </div>
    <div class="kpi-card k-red ex-card">
        <i class="bx bx-x-circle k-icon"></i>
        <div class="k-val" id="statusDeclined">{{ $declined_count }}</div>
        <div class="k-lbl">Declined</div>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="row g-2">

    {{-- LEFT: Team Performance + Attendance --}}
    <div class="col-xl-9 col-lg-8">

        {{-- Team Performance Table --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-group"></i> Team Performance</h6>
                <div class="team-tabs">
                    <button class="team-tab-btn active" onclick="switchTeam('peregrine')" id="peregrineTab">
                        Peregrine (<span id="peregrineCount">{{ $peregrine_count ?? 0 }}</span>)
                    </button>
                    <button class="team-tab-btn" onclick="switchTeam('ravens')" id="ravensTab">
                        Ravens (<span id="ravensCount">{{ $ravens_count ?? 0 }}</span>)
                    </button>
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
                            <th class="text-center">UW</th>
                        </tr>
                    </thead>
                    <tbody id="closerTable">
                        @forelse($sales_per_closer as $closer)
                        <tr class="closer-row" data-team="{{ $closer['team'] ?? '' }}">
                            <td><i class="bx bx-user-circle me-1" style="color:var(--bs-gold);opacity:.7"></i>{{ $closer['closer'] ?? 'N/A' }}</td>
                            <td class="text-center"><span class="bd-mini bd-teal">{{ $closer['today'] ?? 0 }}</span></td>
                            <td class="text-center"><span class="bd-mini bd-blue">{{ $closer['mtd'] ?? 0 }}</span></td>
                            <td class="text-center"><span class="bd-mini bd-green">{{ $closer['approvedMTD'] ?? 0 }}</span></td>
                            <td class="text-center"><span class="bd-mini bd-red">{{ $closer['declinedMTD'] ?? 0 }}</span></td>
                            <td class="text-center"><span class="bd-mini bd-warn">{{ $closer['uwMTD'] ?? $closer['uw'] ?? $closer['underwriting'] ?? 0 }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No closers data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Attendance --}}
        @php
            $lateCount = 0;
            $halfDayCount = 0;
            foreach($attendance as $a) {
                $s = strtolower($a['status'] ?? '');
                if ($s === 'late') $lateCount++;
                elseif (in_array($s, ['half day', 'half_day', 'halfday'])) $halfDayCount++;
            }
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
                <div class="att-mini-list" id="attendanceTable">
                    @forelse($attendance as $att)
                    @php
                        $status = strtolower($att['status'] ?? '');
                        if ($status === 'late') {
                            $pillClass = 'l';
                            $pillText = 'Late';
                        } elseif (in_array($status, ['half day', 'half_day', 'halfday'])) {
                            $pillClass = 'h';
                            $pillText = 'Half Day';
                        } elseif (in_array($status, ['present', 'p', 'on time', 'ontime'])) {
                            $pillClass = 'p';
                            $pillText = 'Present';
                        } else {
                            $pillClass = 'a';
                            $pillText = ucfirst($att['status'] ?? 'Absent');
                        }
                    @endphp
                    <div class="att-row">
                        <span class="att-name">{{ $att['name'] ?? 'N/A' }}</span>
                        <span class="att-pill {{ $pillClass }}">{{ $pillText }}</span>
                    </div>
                    @empty
                    <div class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No attendance data</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT: Target + Retention + Chargebacks --}}
    <div class="col-xl-3 col-lg-4">

        {{-- Monthly Target --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-target-lock"></i> Monthly Target</h6>
            </div>
            <div class="sec-body">
                <div class="target-chart-wrap">
                    <canvas id="monthlyTargetChart"></canvas>
                    <div class="target-info">
                        Target: <strong style="color:var(--bs-gold)">500</strong> &nbsp;|&nbsp;
                        Achieved: <strong style="color:#1a8754">{{ $total_monthly_sales }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Retention --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-refresh"></i> Retention</h6>
            </div>
            <div class="sec-body">
                <div class="ret-row">
                    <div class="ret-block r-cb">
                        <div class="r-val" id="retCB">{{ $retention_cb }}</div>
                        <div class="r-lbl">CB</div>
                    </div>
                    <div class="ret-block r-ret">
                        <div class="r-val" id="retRetained">{{ $retention_retained }}</div>
                        <div class="r-lbl">Retained</div>
                    </div>
                    <div class="ret-block r-pend">
                        <div class="r-val" id="retPending">{{ $retention_pending }}</div>
                        <div class="r-lbl">Pending</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chargebacks --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-error"></i> Chargebacks</h6>
                <a href="{{ route('chargebacks.index') }}" class="link-btn">Details</a>
            </div>
            <div class="sec-body">
                <div class="cb-display">
                    <div class="cb-big" id="cbThis">{{ $cb_this_count }}</div>
                    <div class="cb-sub">This Month</div>
                    <div class="cb-amt" id="cbThisAmt">${{ number_format($cb_this_amt, 0) }}</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chargebacks Details Modal -->
<div class="modal fade" id="chargebacksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-error"></i> Chargebacks Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter by Period</label>
                        <select class="form-select form-select-sm" id="cbPeriodFilter">
                            <option value="this_month" selected>This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="this_year">This Year</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Closer</th>
                                <th>Customer</th>
                                <th>Policy #</th>
                                <th>Amount</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jan 15, 2025</td>
                                <td>John Doe</td>
                                <td>Michael Brown</td>
                                <td>POL-12345</td>
                                <td><span class="text-danger fw-bold">$850</span></td>
                                <td><span class="badge bg-warning">Card Declined</span></td>
                            </tr>
                            <tr>
                                <td>Jan 18, 2025</td>
                                <td>Jane Smith</td>
                                <td>Sarah Johnson</td>
                                <td>POL-12389</td>
                                <td><span class="text-danger fw-bold">$1,200</span></td>
                                <td><span class="badge bg-danger">Insufficient Funds</span></td>
                            </tr>
                            <tr>
                                <td>Jan 20, 2025</td>
                                <td>Mike Wilson</td>
                                <td>David Lee</td>
                                <td>POL-12401</td>
                                <td><span class="text-danger fw-bold">$950</span></td>
                                <td><span class="badge bg-warning">Customer Dispute</span></td>
                            </tr>
                            <tr>
                                <td>Jan 22, 2025</td>
                                <td>John Doe</td>
                                <td>Emily Davis</td>
                                <td>POL-12456</td>
                                <td><span class="text-danger fw-bold">$1,100</span></td>
                                <td><span class="badge bg-danger">Insufficient Funds</span></td>
                            </tr>
                            <tr>
                                <td>Jan 25, 2025</td>
                                <td>Sarah Connor</td>
                                <td>James Taylor</td>
                                <td>POL-12502</td>
                                <td><span class="text-danger fw-bold">$750</span></td>
                                <td><span class="badge bg-warning">Card Declined</span></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="4" class="text-end fw-bold">Total Chargebacks:</td>
                                <td colspan="2"><span class="text-danger fw-bold fs-6">$12,450</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-gold btn-sm"><i class="bx bx-download"></i> Export</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global dashboard state (defensive against duplicate script loads)
var currentTeam = (typeof window !== 'undefined' && typeof window.currentTeam !== 'undefined')
    ? window.currentTeam
    : 'peregrine';
var salesChart = (typeof window !== 'undefined' && typeof window.salesChart !== 'undefined')
    ? window.salesChart
    : null;
var allData = (typeof window !== 'undefined' && typeof window.allData !== 'undefined')
    ? window.allData
    : null;

if (typeof window !== 'undefined') {
    window.currentTeam = currentTeam;
    window.salesChart = salesChart;
    window.allData = allData;
}

// Server-side data
var serverData = (typeof window !== 'undefined' && typeof window.serverData !== 'undefined')
    ? window.serverData
    : {
        totalSalesToday: {{ $total_sales_today }},
        done: {{ $done_count }},
        totalRevenue: {{ $total_revenue }},
        approved: {{ $approved_count }},
        underwriting: {{ $underwriting_count }},
        declined: {{ $declined_count }},
        salesPerCloser: @json($sales_per_closer),
        attendance: @json($attendance),
        retention: {
            cb: {{ $retention_cb }},
            retained: {{ $retention_retained }},
            pending: {{ $retention_pending }}
        },
        chargebacks: {
            thisMonth: {
                count: {{ $cb_this_count }},
                amount: {{ $cb_this_amt }}
            },
            lastMonth: {
                count: {{ $cb_last_count }},
                amount: {{ $cb_last_amt }}
            }
        }
    };

if (typeof window !== 'undefined') {
    window.serverData = serverData;
}

// Update Clocks (targets navbar elements)
function updateClocks() {
    const now = new Date();

    const floridaTime = now.toLocaleTimeString('en-US', {
        timeZone: 'America/New_York',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
    const el1 = document.getElementById('navFloridaTime');
    if (el1) el1.textContent = floridaTime;

    const pakistanTime = now.toLocaleTimeString('en-US', {
        timeZone: 'Asia/Karachi',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
    const el2 = document.getElementById('navPakistanTime');
    if (el2) el2.textContent = pakistanTime;
}

// Load Data
function loadData() {
    updateUI(serverData);
}

function updateUI(d) {
    // Top Metrics
    $('#salesToday').text(d.totalSalesToday || 0);
    $('#salesMTD').text(d.done || d.TOTAL || 0);
    $('#revenue').text('$' + fmt(d.totalRevenue || 0));
    $('#activeTeam').text((d.attendance || []).length);
    
    // Status Cards
    $('#statusDone').text(d.done || 0);
    $('#statusApproved').text(d.approved || 0);
    $('#statusUW').text(d.underwriting || d.UW || 0);
    $('#statusDeclined').text(d.declined || 0);

    // Call Center Metrics - Calculate from salesPerCloser data
    const totalSales = d.done || 0;
    const totalClosers = (d.salesPerCloser || []).length;
    const avgSalesPerCloser = totalClosers > 0 ? Math.round(totalSales / totalClosers) : 0;
    const conversionRate = totalSales > 0 ? ((d.approved || 0) / totalSales * 100).toFixed(1) : 0;

    $('#totalCalls').text(totalSales);
    $('#connectedCalls').text(d.approved || 0);
    $('#avgCallTime').text(avgSalesPerCloser);
    $('#conversionRate').text(conversionRate + '%');
    
    // Team Data
    if (d.salesPerCloser) {
        const peregrine = d.salesPerCloser.filter(c => (c.team || '').toLowerCase() === 'peregrine');
        const ravens = d.salesPerCloser.filter(c => (c.team || '').toLowerCase() === 'ravens');

        $('#peregrineCount').text(peregrine.length);
        $('#ravensCount').text(ravens.length);

        // Filter based on current team
        const currentTeamData = currentTeam === 'peregrine' ? peregrine : ravens;
        renderClosers(currentTeamData);
        updateCharts(d.salesPerCloser);
        allData = d;
    }
    
    // Attendance
    if (d.attendance) renderTeam(d.attendance);
    
    // Retention
    const ret = d.retention || d.retentionTracking || {};
    $('#retCB').text(ret.cb || ret.CB || 0);
    $('#retRetained').text(ret.retained || 0);
    $('#retPending').text(ret.pending || ret.yetToRetain || 0);
    
    // Chargebacks
    if (d.chargebacks) {
        $('#cbThis').text(d.chargebacks.thisMonth?.count || 0);
        $('#cbThisAmt').text('$' + fmt(d.chargebacks.thisMonth?.amount || 0));
        $('#cbLast').text(d.chargebacks.lastMonth?.count || 0);
        $('#cbLastAmt').text('$' + fmt(d.chargebacks.lastMonth?.amount || 0));
    }
}

function switchTeam(team) {
    currentTeam = team;
    $('#peregrineTab, #ravensTab').removeClass('active');
    $('#' + team + 'Tab').addClass('active');

    // Filter and re-render closers for the selected team
    if (allData && allData.salesPerCloser) {
        const teamClosers = allData.salesPerCloser.filter(c => (c.team || '').toLowerCase() === team);
        renderClosers(teamClosers);
    }
}

function renderClosers(closers) {
    const tbody = $('#closerTable');
    tbody.empty();

    if (!closers || closers.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.78rem">No closers in this team</td></tr>');
        return;
    }

    closers.forEach(c => {
        tbody.append(`
            <tr class="closer-row" data-team="${c.team || 'peregrine'}">
                <td><i class="bx bx-user-circle me-1" style="color:var(--bs-gold);opacity:.7"></i>${c.closer || 'N/A'}</td>
                <td class="text-center"><span class="bd-mini bd-teal">${c.today || 0}</span></td>
                <td class="text-center"><span class="bd-mini bd-blue">${c.mtd || 0}</span></td>
                <td class="text-center"><span class="bd-mini bd-green">${c.approved || c.approvedMTD || 0}</span></td>
                <td class="text-center"><span class="bd-mini bd-red">${c.declined || c.declinedMTD || 0}</span></td>
                <td class="text-center"><span class="bd-mini bd-warn">${c.uw || c.uwMTD || c.underwriting || 0}</span></td>
            </tr>
        `);
    });
}

function renderTeam(team) {
    const list = $('#attendanceTable');
    list.empty();
    let present = 0, absent = 0, late = 0, halfDay = 0;

    team.forEach(t => {
        const status = (t.status || '').toLowerCase();
        let pillClass, pillText;

        if (status === 'late') {
            pillClass = 'l'; pillText = 'Late'; late++;
        } else if (['half day','half_day','halfday'].includes(status)) {
            pillClass = 'h'; pillText = 'Half Day'; halfDay++;
        } else if (['present','p','on time','ontime'].includes(status)) {
            pillClass = 'p'; pillText = 'Present'; present++;
        } else {
            pillClass = 'a'; pillText = 'Absent'; absent++;
        }

        list.append(`<div class="att-row">
            <span class="att-name">${t.name}</span>
            <span class="att-pill ${pillClass}">${pillText}</span>
        </div>`);
    });

    $('#presentCount').text(present);
    $('#absentCount').text(absent);
    $('#lateCount').text(late);
    $('#halfDayCount').text(halfDay);
}

function updateCharts(salesData) {
    // Monthly Target Chart with real data
    const ctx = document.getElementById('monthlyTargetChart');
    if (salesChart) salesChart.destroy();

    const currentMTD = serverData.done || 0; // Real data from webhook
    const target = 500;
    const percentage = Math.min((currentMTD / target) * 100, 100);

    salesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Achieved', 'Remaining'],
            datasets: [{
                data: [currentMTD, Math.max(target - currentMTD, 0)],
                backgroundColor: [
                    themeColors.success, // Green for achieved
                    themeColors.warning  // Yellow for remaining
                ],
                borderColor: [
                    themeColors.success,
                    themeColors.warning
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ' + value;
                        }
                    }
                }
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
                ctx.fillStyle = themeColors.gold;

                const text = currentMTD + "";
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 2 - 10;

                ctx.fillText(text, textX, textY);

                ctx.font = fontSize * 0.5 + "em sans-serif";
                ctx.fillStyle = themeColors.surface500;
                const subText = percentage.toFixed(0) + "%";
                const subTextX = Math.round((width - ctx.measureText(subText).width) / 2);
                const subTextY = height / 2 + 15;

                ctx.fillText(subText, subTextX, subTextY);
                ctx.save();
            }
        }]
    });
}

function fmt(n) {
    return new Intl.NumberFormat().format(Math.round(n));
}

$(document).ready(function() {
    updateClocks();
    setInterval(updateClocks, 1000);

    loadData();
    
    // Poll for KPI data updates every 30 seconds
    setInterval(function() {
        fetch('{{ route('dashboard.kpi-data') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUI(data);
                }
            })
            .catch(error => console.error('KPI update error:', error));
    }, 30000); // 30 seconds
});
</script>
@endsection