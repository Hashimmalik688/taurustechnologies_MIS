@extends('layouts.master')

@section('title') Partner Details — {{ $partner->name }} @endsection

@section('css')
<style>
/* ═══════════════════════════════════════════════════
   Partner Detail — Executive Dashboard Theme
   ═══════════════════════════════════════════════════ */

/* Page Header */
.pd-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; flex-wrap:wrap; gap:.5rem; }
.pd-page-hdr h5 { font-weight:800; font-size:1.05rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.pd-page-hdr .pd-sub { font-size:.68rem; font-weight:500; color:var(--bs-surface-500); }
.pd-back-btn { font-size:.68rem; font-weight:600; padding:.3rem .7rem; border-radius:.35rem; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; transition:all .15s; }
.pd-back-btn:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); }

/* Glass-card base (match executive dashboard) */
.pd-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: box-shadow .2s;
    overflow: hidden;
    margin-bottom: .65rem;
}
.pd-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

/* Hero Banner */
.pd-hero {
    background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
    padding: 1.25rem 1.25rem;
    position: relative;
    overflow: hidden;
}
.pd-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M20 20.5V18H0v-2h20v-2l4 3.5-4 3z'/%3E%3C/g%3E%3C/svg%3E");
}
.pd-avatar {
    width: 56px; height: 56px;
    background: rgba(255,255,255,.18);
    backdrop-filter: blur(6px);
    border-radius: .6rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; font-weight: 700; color: #fff;
    text-transform: uppercase;
    position: relative; z-index: 1;
}
.pd-hero-info { position: relative; z-index: 1; }
.pd-hero-info h4 { color: #fff; font-weight: 700; font-size: 1.1rem; margin: 0; }
.pd-hero-info .pd-code { color: rgba(255,255,255,.75); font-size: .72rem; font-weight: 500; display: flex; align-items: center; gap: .3rem; }

.pd-status-pill {
    font-size: .62rem; font-weight: 700; padding: .2rem .6rem;
    border-radius: 1rem; display: inline-flex; align-items: center; gap: .3rem;
    position: relative; z-index: 1;
}
.pd-status-pill.active { background: rgba(52,195,143,.2); color: #fff; }
.pd-status-pill.active .pd-dot { width: 6px; height: 6px; border-radius: 50%; background: #38ef7d; animation: pdPulse 2s infinite; }
.pd-status-pill.inactive { background: rgba(255,255,255,.15); color: rgba(255,255,255,.7); }
.pd-status-pill.inactive .pd-dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,.5); }

@keyframes pdPulse {
    0% { box-shadow: 0 0 0 0 rgba(56,239,125,.6); }
    70% { box-shadow: 0 0 0 6px rgba(56,239,125,0); }
    100% { box-shadow: 0 0 0 0 rgba(56,239,125,0); }
}

/* Section headers */
.pd-sec-hdr {
    display: flex; justify-content: space-between; align-items: center;
    padding: .5rem .75rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
}
.pd-sec-hdr h6 {
    margin: 0; font-size: .78rem; font-weight: 700;
    display: flex; align-items: center; gap: .3rem;
    color: var(--bs-surface-700);
}
.pd-sec-hdr h6 i { opacity: .6; font-size: .9rem; }
.pd-sec-body { padding: .75rem; }

/* KPI mini cards */
.pd-kpi-row { display: flex; gap: .4rem; flex-wrap: wrap; }
.pd-kpi {
    flex: 1 1 120px;
    padding: .6rem .7rem;
    border-radius: .45rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    background: var(--bs-card-bg);
    border: 1px solid rgba(0,0,0,.04);
    transition: transform .15s;
}
.pd-kpi:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,.06); }
.pd-kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:.45rem .45rem 0 0; }
.pd-kpi.k-blue::before { background:linear-gradient(90deg,#556ee6,#8b9cf7); }
.pd-kpi.k-green::before { background:linear-gradient(90deg,#34c38f,#6eddb8); }
.pd-kpi.k-purple::before { background:linear-gradient(90deg,#764ba2,#a880d4); }
.pd-kpi.k-teal::before { background:linear-gradient(90deg,#50a5f1,#8cc5f7); }
.pd-kpi .k-val { font-size: 1.25rem; font-weight: 700; line-height: 1; }
.pd-kpi .k-lbl { font-size: .55rem; text-transform: uppercase; font-weight: 600; letter-spacing: .4px; color: var(--bs-surface-500); margin-top: .15rem; }
.pd-kpi.k-blue .k-val { color: #556ee6; }
.pd-kpi.k-green .k-val { color: #1a8754; }
.pd-kpi.k-purple .k-val { color: #764ba2; }
.pd-kpi.k-teal .k-val { color: #2b81c9; }

/* Contact info items */
.pd-info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .5rem; }
.pd-info-item {
    padding: .6rem .7rem;
    border-radius: .4rem;
    background: var(--bs-card-bg);
    border: 1px solid rgba(0,0,0,.04);
    transition: all .2s;
}
.pd-info-item:hover { border-color: rgba(85,110,230,.2); }
.pd-info-lbl {
    font-size: .55rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .4px; color: var(--bs-surface-500); margin-bottom: .2rem;
    display: flex; align-items: center; gap: .3rem;
}
.pd-info-lbl i { font-size: .7rem; opacity: .7; }
.pd-info-val { font-size: .78rem; font-weight: 600; color: var(--bs-surface-800); }
.pd-info-val a { color: #556ee6; text-decoration: none; }
.pd-info-val a:hover { text-decoration: underline; }
.pd-info-val .empty { color: var(--bs-surface-400); font-weight: 500; }

/* Carrier cards */
.pd-carrier {
    background: var(--bs-card-bg);
    border: 1px solid rgba(0,0,0,.06);
    border-radius: .5rem;
    overflow: hidden;
    margin-bottom: .5rem;
    transition: all .2s;
}
.pd-carrier:hover { border-color: rgba(85,110,230,.2); box-shadow: 0 3px 12px rgba(0,0,0,.06); }
.pd-carrier-hdr {
    display: flex; justify-content: space-between; align-items: center;
    padding: .5rem .7rem;
    border-bottom: 1px solid rgba(0,0,0,.04);
    background: rgba(85,110,230,.02);
}
.pd-carrier-name {
    font-size: .78rem; font-weight: 700; color: #556ee6;
    display: flex; align-items: center; gap: .3rem;
}
.pd-carrier-name i { font-size: .85rem; }
.pd-carrier-body { padding: .6rem .7rem; }

/* State pills — designed to be visible in ALL themes */
.pd-state-pills { display: flex; flex-wrap: wrap; gap: .2rem; margin-top: .25rem; }
.pd-state-pill {
    font-size: .55rem; font-weight: 700; padding: .12rem .35rem;
    border-radius: .2rem;
    background: rgba(85,110,230,.1);
    color: #556ee6;
    border: 1px solid rgba(85,110,230,.15);
    letter-spacing: .3px;
}

/* Settlement rates grid */
.pd-rates { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: .3rem; margin-top: .4rem; }
.pd-rate {
    text-align: center;
    padding: .35rem .3rem;
    border-radius: .3rem;
    background: rgba(0,0,0,.02);
    border: 1px solid rgba(0,0,0,.04);
}
.pd-rate .r-lbl { font-size: .48rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; color: var(--bs-surface-500); }
.pd-rate .r-val { font-size: .72rem; font-weight: 700; color: var(--bs-surface-700); }
.pd-rate .r-val.empty { color: var(--bs-surface-400); }

/* Action buttons */
.pd-actions { display: flex; gap: .4rem; justify-content: flex-end; margin-top: .5rem; }
.pd-btn {
    font-size: .68rem; font-weight: 600; padding: .35rem .8rem;
    border-radius: .35rem; border: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: .25rem;
    transition: all .2s; text-decoration: none;
}
.pd-btn.primary {
    background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
    color: #fff; box-shadow: 0 2px 8px rgba(102,126,234,.25);
}
.pd-btn.primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(102,126,234,.35); color:#fff; }
.pd-btn.secondary {
    background: var(--bs-card-bg); border: 1px solid var(--bs-surface-200);
    color: var(--bs-surface-600);
}
.pd-btn.secondary:hover { border-color: var(--bs-surface-400); color: var(--bs-surface-700); }

/* Empty state */
.pd-empty {
    text-align: center; padding: 1.5rem; color: var(--bs-surface-400);
}
.pd-empty i { font-size: 2rem; display: block; margin-bottom: .4rem; opacity: .2; }
.pd-empty p { font-size: .72rem; margin: 0; }

/* Chart */
.pd-chart-wrap { padding: .5rem; }
.pd-chart-wrap canvas { max-height: 200px; }
</style>
@endsection

@section('content')

@php
    $groupedCarrierStates = $partner->carrierStates->groupBy('insurance_carrier_id');
    $totalCarriers = $groupedCarrierStates->count();
    $totalStates = $partner->carrierStates->pluck('state')->unique()->count();
    $totalLeads = \App\Models\Lead::where('partner_id', $partner->id)->count();
@endphp

<!-- Page Header -->
<div class="pd-page-hdr">
    <h5>
        <i class="bx bx-user-circle"></i> {{ $partner->name }}
        <span class="pd-sub">— Partner Profile</span>
    </h5>
    <a href="{{ route('admin.partners.index') }}" class="pd-back-btn"><i class="bx bx-arrow-back"></i> Partners</a>
</div>

<!-- Hero Card -->
<div class="pd-card">
    <div class="pd-hero">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="pd-avatar">{{ substr($partner->name, 0, 2) }}</div>
                <div class="pd-hero-info">
                    <h4>{{ $partner->name }}</h4>
                    <div class="pd-code"><i class="bx bx-hash"></i> Partner Code: {{ $partner->code }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($partner->is_active)
                    <span class="pd-status-pill active"><span class="pd-dot"></span> Active Partner</span>
                @else
                    <span class="pd-status-pill inactive"><span class="pd-dot"></span> Inactive</span>
                @endif
            </div>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="pd-sec-body">
        <div class="pd-kpi-row">
            <div class="pd-kpi k-blue">
                <div class="k-val">{{ $totalCarriers }}</div>
                <div class="k-lbl">Carriers</div>
            </div>
            <div class="pd-kpi k-green">
                <div class="k-val">{{ $totalStates }}</div>
                <div class="k-lbl">Licensed States</div>
            </div>
            <div class="pd-kpi k-purple">
                <div class="k-val">{{ $totalLeads }}</div>
                <div class="k-lbl">Total Leads</div>
            </div>
            <div class="pd-kpi k-teal">
                <div class="k-val">{{ $partner->our_commission_percentage ?? 0 }}%</div>
                <div class="k-lbl">Our Commission</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-5">
        <!-- Contact Information -->
        <div class="pd-card">
            <div class="pd-sec-hdr">
                <h6><i class="bx bx-id-card"></i> Contact Information</h6>
            </div>
            <div class="pd-sec-body">
                <div class="pd-info-grid">
                    <div class="pd-info-item">
                        <div class="pd-info-lbl"><i class="bx bx-envelope"></i> Email</div>
                        <div class="pd-info-val">
                            @if($partner->email)
                                <a href="mailto:{{ $partner->email }}">{{ $partner->email }}</a>
                            @else
                                <span class="empty">Not provided</span>
                            @endif
                        </div>
                    </div>
                    <div class="pd-info-item">
                        <div class="pd-info-lbl"><i class="bx bx-phone"></i> Phone</div>
                        <div class="pd-info-val">
                            @if($partner->phone)
                                {{ $partner->phone }}
                            @else
                                <span class="empty">Not provided</span>
                            @endif
                        </div>
                    </div>
                    <div class="pd-info-item">
                        <div class="pd-info-lbl"><i class="bx bx-lock-alt"></i> SSN (Last 4)</div>
                        <div class="pd-info-val">
                            @if($partner->ssn_last4)
                                •••• {{ $partner->ssn_last4 }}
                            @else
                                <span class="empty">Not provided</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settlement Overview Chart -->
        @if($totalCarriers > 0)
        <div class="pd-card">
            <div class="pd-sec-hdr">
                <h6><i class="bx bx-bar-chart-alt-2"></i> Settlement Overview</h6>
            </div>
            <div class="pd-sec-body">
                <div class="pd-chart-wrap">
                    <canvas id="settlementChart"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column -->
    <div class="col-lg-7">
        <!-- Carrier & State Assignments -->
        <div class="pd-card">
            <div class="pd-sec-hdr">
                <h6><i class="bx bx-briefcase"></i> Carrier & State Assignments</h6>
                <span style="font-size:.6rem;font-weight:600;padding:.15rem .45rem;border-radius:1rem;background:rgba(85,110,230,.1);color:#556ee6;">{{ $totalCarriers }} carrier{{ $totalCarriers != 1 ? 's' : '' }}</span>
            </div>
            <div class="pd-sec-body">
                @forelse($groupedCarrierStates as $carrierId => $carrierStates)
                    @php
                        $carrier = $carrierStates->first()->insuranceCarrier;
                    @endphp
                    <div class="pd-carrier">
                        <div class="pd-carrier-hdr">
                            <div class="pd-carrier-name">
                                <i class="bx bx-shield-quarter"></i>
                                {{ $carrier->name ?? 'Unknown Carrier' }}
                            </div>
                            <span style="font-size:.55rem;font-weight:700;padding:.1rem .35rem;border-radius:.2rem;background:rgba(52,195,143,.1);color:#1a8754;border:1px solid rgba(52,195,143,.15);">
                                {{ $carrierStates->count() }} state{{ $carrierStates->count() != 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="pd-carrier-body">
                            <!-- States -->
                            <div style="font-size:.55rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.2rem;">Licensed States</div>
                            <div class="pd-state-pills">
                                @foreach($carrierStates as $cs)
                                    <span class="pd-state-pill">{{ $cs->state }}</span>
                                @endforeach
                            </div>

                            <!-- Settlement Summary -->
                            @php
                                $avgLevel = $carrierStates->avg('settlement_level_pct');
                                $avgGraded = $carrierStates->avg('settlement_graded_pct');
                                $avgGi = $carrierStates->avg('settlement_gi_pct');
                                $avgModified = $carrierStates->avg('settlement_modified_pct');
                            @endphp
                            @if($avgLevel || $avgGraded || $avgGi || $avgModified)
                                <div style="font-size:.55rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.5rem;margin-bottom:.2rem;">Settlement Rates</div>
                                <div class="pd-rates">
                                    <div class="pd-rate">
                                        <div class="r-lbl">Level</div>
                                        <div class="r-val {{ !$avgLevel ? 'empty' : '' }}">{{ $avgLevel ? number_format($avgLevel, 1).'%' : '—' }}</div>
                                    </div>
                                    <div class="pd-rate">
                                        <div class="r-lbl">Graded</div>
                                        <div class="r-val {{ !$avgGraded ? 'empty' : '' }}">{{ $avgGraded ? number_format($avgGraded, 1).'%' : '—' }}</div>
                                    </div>
                                    <div class="pd-rate">
                                        <div class="r-lbl">GI</div>
                                        <div class="r-val {{ !$avgGi ? 'empty' : '' }}">{{ $avgGi ? number_format($avgGi, 1).'%' : '—' }}</div>
                                    </div>
                                    <div class="pd-rate">
                                        <div class="r-lbl">Modified</div>
                                        <div class="r-val {{ !$avgModified ? 'empty' : '' }}">{{ $avgModified ? number_format($avgModified, 1).'%' : '—' }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="pd-empty">
                        <i class="bx bx-briefcase-alt"></i>
                        <p>No carrier or state assignments yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="pd-actions">
    <a href="{{ route('admin.partners.index') }}" class="pd-btn secondary"><i class="bx bx-arrow-back"></i> Back to List</a>
    @canEditModule('partners')
    <a href="{{ route('admin.partners.edit', $partner->id) }}" class="pd-btn primary"><i class="bx bx-edit-alt"></i> Edit Partner</a>
    @endcanEditModule
</div>

@endsection

@section('script')
@if($totalCarriers > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('settlementChart');
    if (!ctx) return;

    const carriers = @json($groupedCarrierStates->map(function($states, $carrierId) {
        $carrier = $states->first()->insuranceCarrier;
        return [
            'name' => $carrier->name ?? 'Unknown',
            'level' => round($states->avg('settlement_level_pct') ?? 0, 1),
            'graded' => round($states->avg('settlement_graded_pct') ?? 0, 1),
            'gi' => round($states->avg('settlement_gi_pct') ?? 0, 1),
            'modified' => round($states->avg('settlement_modified_pct') ?? 0, 1),
        ];
    })->values());

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: carriers.map(c => c.name),
            datasets: [
                { label: 'Level', data: carriers.map(c => c.level), backgroundColor: 'rgba(85,110,230,.7)', borderRadius: 3 },
                { label: 'Graded', data: carriers.map(c => c.graded), backgroundColor: 'rgba(52,195,143,.7)', borderRadius: 3 },
                { label: 'GI', data: carriers.map(c => c.gi), backgroundColor: 'rgba(241,180,76,.7)', borderRadius: 3 },
                { label: 'Modified', data: carriers.map(c => c.modified), backgroundColor: 'rgba(118,75,162,.7)', borderRadius: 3 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 10, weight: '600' }, padding: 12, usePointStyle: true, pointStyle: 'rectRounded' } }
            },
            scales: {
                y: { beginAtZero: true, max: 150, ticks: { font: { size: 10 }, callback: v => v + '%' }, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { ticks: { font: { size: 10, weight: '600' } }, grid: { display: false } }
            }
        }
    });
});
</script>
@endif
@endsection
