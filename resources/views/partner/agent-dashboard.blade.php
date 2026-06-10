@extends('layouts.partner')

@section('title') Agent Dashboard @endsection

@section('css')
<style>
:root{--pd-indigo:#4f46e5;--pd-green:#059669;--pd-red:#dc2626;--pd-amber:#d97706;--pd-teal:#0d9488;--pd-br:.6rem;--pd-sh:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);--pd-ease:cubic-bezier(.22,1,.36,1)}
@keyframes pd-fadein{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.pd-anim{animation:pd-fadein .45s var(--pd-ease) both}
.pd-d1{animation-delay:.04s}.pd-d2{animation-delay:.08s}.pd-d3{animation-delay:.12s}.pd-d4{animation-delay:.16s}

.pd-hero{background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%);padding:1.4rem 1.8rem 1.2rem;position:relative;overflow:hidden;margin-bottom:1.2rem}
.pd-hero::after{content:'';position:absolute;width:320px;height:320px;background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 70%);top:-60px;right:-40px;pointer-events:none}
.pd-hero-body{position:relative;z-index:1}
.pd-hero-top{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;margin-bottom:.8rem}
.pd-hero-name{font-size:1.2rem;font-weight:900;color:#fff;letter-spacing:-.2px}
.pd-hero-upline{font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.15rem}
.pd-hero-upline strong{color:rgba(255,255,255,.75)}
.pd-chip{font-size:.62rem;font-weight:700;padding:.15rem .45rem;border-radius:999px;text-transform:uppercase;letter-spacing:.5px}
.pd-chip-green{background:rgba(5,150,105,.25);border:1px solid rgba(5,150,105,.45);color:#6ee7b7}

.pd-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.6rem;margin-bottom:1rem}
.pd-kpi{background:var(--bs-card-bg,#fff);border-radius:var(--pd-br);padding:.75rem .85rem;box-shadow:var(--pd-sh);border:1px solid rgba(0,0,0,.04)}
.pd-kpi-label{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--bs-surface-400,#9ca3af);margin-bottom:.25rem}
.pd-kpi-val{font-size:1.3rem;font-weight:900;color:var(--bs-heading-color,#111827);line-height:1.1}
.pd-kpi-sub{font-size:.58rem;font-weight:600;color:var(--bs-surface-400,#9ca3af);margin-top:.15rem}

.pd-card{background:var(--bs-card-bg,#fff);border-radius:var(--pd-br);overflow:hidden;box-shadow:var(--pd-sh);border:1px solid rgba(0,0,0,.04);margin-bottom:.8rem}
.pd-card-hdr{display:flex;align-items:center;gap:.5rem;padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.05);font-size:.72rem;font-weight:700;color:var(--bs-heading-color,#323a46)}
.pd-card-hdr i{color:var(--pd-indigo);font-size:.85rem}
.pd-card-body{padding:.5rem .85rem}

.pd-tbl{width:100%;border-collapse:collapse;font-size:.68rem}
.pd-tbl th{padding:.4rem .5rem;text-align:left;font-weight:700;font-size:.58rem;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-surface-400,#9ca3af);border-bottom:1px solid rgba(0,0,0,.06);white-space:nowrap}
.pd-tbl td{padding:.45rem .5rem;border-bottom:1px solid rgba(0,0,0,.03);color:var(--bs-body-color,#1f2937);vertical-align:middle}
.pd-tbl tbody tr:hover{background:rgba(79,70,229,.02)}
.pd-status{padding:.12rem .35rem;border-radius:.25rem;font-size:.55rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px}
.pd-status-issued{background:rgba(5,150,105,.1);color:var(--pd-green)}
.pd-status-pending{background:rgba(245,158,11,.1);color:var(--pd-amber)}
.pd-status-not-issued{background:rgba(220,38,38,.1);color:var(--pd-red)}

.pd-state-tags{display:flex;flex-wrap:wrap;gap:.2rem;margin-top:.3rem}
.pd-state-pill{font-size:.52rem;font-weight:700;padding:.08rem .3rem;border-radius:.2rem;background:rgba(79,70,229,.08);color:var(--pd-indigo);border:1px solid rgba(79,70,229,.12)}

.pd-empty{text-align:center;padding:2rem 1rem;color:var(--bs-surface-400,#9ca3af)}
.pd-empty i{font-size:2rem;display:block;margin-bottom:.4rem;opacity:.3}
.pd-empty p{font-size:.75rem;margin:0}
</style>
@endsection

@section('content')
<div class="pd-hero pd-anim">
    <div class="pd-hero-body">
        <div class="pd-hero-top">
            <div>
                <div class="pd-hero-name">{{ $agent->name }}</div>
                <div class="pd-hero-upline">
                    <i class="bx bx-up-arrow-alt"></i> Upline: <strong>{{ $upline->name }}</strong>
                </div>
            </div>
            <span class="pd-chip pd-chip-green">Agent</span>
        </div>
    </div>
</div>

<div class="pd-grid pd-anim pd-d1">
    <div class="pd-kpi">
        <div class="pd-kpi-label">My Sales</div>
        <div class="pd-kpi-val">{{ $totalSales }}</div>
        <div class="pd-kpi-sub">Total issued</div>
    </div>
    <div class="pd-kpi">
        <div class="pd-kpi-label">Pending</div>
        <div class="pd-kpi-val">{{ $pendingSales }}</div>
        <div class="pd-kpi-sub">Awaiting issuance</div>
    </div>
    <div class="pd-kpi">
        <div class="pd-kpi-label">My Commission</div>
        <div class="pd-kpi-val">${{ number_format($totalCommission, 2) }}</div>
        <div class="pd-kpi-sub">Earned this month</div>
    </div>
    <div class="pd-kpi">
        <div class="pd-kpi-label">Carriers</div>
        <div class="pd-kpi-val">{{ $carrierCount }}</div>
        <div class="pd-kpi-sub">States: {{ $stateCount }}</div>
    </div>
</div>

<div class="pd-card pd-anim pd-d2">
    <div class="pd-card-hdr"><i class="bx bx-briefcase"></i> My Carriers & Licensed States</div>
    <div class="pd-card-body">
        @if($agentCarriers->isEmpty())
        <div class="pd-empty"><i class="bx bx-briefcase"></i><p>No carriers assigned yet</p></div>
        @else
            @foreach($agentCarriers as $group)
            <div style="margin-bottom:.6rem;padding-bottom:.4rem;border-bottom:1px solid rgba(0,0,0,.04)">
                <div style="font-size:.72rem;font-weight:700;color:var(--bs-heading-color,#323a46);margin-bottom:.2rem">
                    {{ $group['carrier']->name }}
                </div>
                <div class="pd-state-tags">
                    @foreach($group['states'] as $st)
                        <span class="pd-state-pill">{{ $st }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>

<div class="pd-card pd-anim pd-d3">
    <div class="pd-card-hdr">
        <i class="bx bx-receipt"></i> My Recent Sales
        <span style="margin-left:auto;font-size:.6rem;font-weight:600;color:var(--bs-surface-400)">{{ $myLeads->count() }} leads</span>
    </div>
    <div style="overflow-x:auto">
        <table class="pd-tbl">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Carrier</th>
                    <th>State</th>
                    <th>Premium</th>
                    <th>My Comm</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myLeads as $lead)
                <tr>
                    <td style="font-weight:600">{{ $lead->cn_name ?? 'N/A' }}</td>
                    <td>{{ $lead->insuranceCarrier->name ?? 'N/A' }}</td>
                    <td>{{ $lead->state ?? '-' }}</td>
                    <td>${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                    <td style="font-weight:700;color:var(--pd-green)">${{ number_format($lead->agent_commission ?? 0, 2) }}</td>
                    <td>
                        @php $status = $lead->issuance_status ?? 'Pending'; @endphp
                        <span class="pd-status pd-status-{{ strtolower(str_replace(' ','-',$status)) }}">{{ $status }}</span>
                    </td>
                    <td style="white-space:nowrap">{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="pd-empty"><i class="bx bx-receipt" style="font-size:1.5rem;margin:0"></i><p>No sales yet</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
