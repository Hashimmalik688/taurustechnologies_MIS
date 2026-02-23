@extends('layouts.partner')

@section('title') Partner Dashboard @endsection

@section('css')
<style>
/* ─── Partner Dashboard ─── */
.pd-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.6rem;margin-bottom:1rem;}
.pd-hdr h5{font-weight:800;font-size:1.05rem;color:var(--bs-surface-800,#1f2937);display:flex;align-items:center;gap:.4rem;margin:0;}
.pd-hdr .pd-sub{font-size:.62rem;font-weight:500;color:var(--bs-surface-muted,#9ca3af);margin-left:.2rem;}

/* Filters */
.pd-filters{display:flex;gap:.35rem;align-items:center;flex-wrap:wrap;}
.pd-filter-label{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500,#6b7280);}
.pd-filter-input{border:1px solid var(--bs-surface-200,#e5e7eb);border-radius:.35rem;padding:.3rem .5rem;font-size:.68rem;background:var(--bs-card-bg,#fff);color:var(--bs-body-color,inherit);cursor:pointer;}
.pd-filter-input:focus{outline:none;border-color:var(--bs-gradient-start,#667eea);box-shadow:0 0 0 2px rgba(102,126,234,.1);}
.pd-filter-btn{background:linear-gradient(135deg,var(--bs-gradient-start,#667eea),var(--bs-gradient-end,#764ba2));color:#fff;border:none;padding:.32rem .7rem;border-radius:.35rem;font-size:.62rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:.2rem;transition:all .15s;}
.pd-filter-btn:hover{transform:translateY(-1px);box-shadow:0 3px 10px rgba(102,126,234,.25);}
.pd-filter-btn.outline{background:transparent;border:1px solid var(--bs-surface-200,#e5e7eb);color:var(--bs-surface-500,#6b7280);}
.pd-filter-btn.outline:hover{border-color:var(--bs-gradient-start,#667eea);color:var(--bs-gradient-start,#667eea);}
.pd-filter-sep{width:1px;height:20px;background:var(--bs-surface-200,#e5e7eb);margin:0 .2rem;}

/* KPI Grid */
.pd-kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:.55rem;margin-bottom:1rem;}
.pd-kpi{background:var(--bs-card-bg,#fff);border-radius:.55rem;padding:.65rem .75rem;position:relative;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);border:1px solid var(--bs-surface-100,#f3f4f6);}
.pd-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0;}
.pd-kpi.k-blue::before{background:linear-gradient(90deg,var(--bs-gradient-start,#667eea),var(--bs-gradient-end,#764ba2));}
.pd-kpi.k-green::before{background:linear-gradient(90deg,var(--bs-ui-success,#34c38f),#38ef7d);}
.pd-kpi.k-orange::before{background:linear-gradient(90deg,var(--bs-ui-danger,#f5576c),#ff6b6b);}
.pd-kpi.k-cyan::before{background:linear-gradient(90deg,var(--bs-ui-info,#00b4db),#0083b0);}
.pd-kpi.k-gold::before{background:linear-gradient(90deg,var(--bs-ui-warning,#f5b041),#f39c12);}
.pd-kpi.k-purple::before{background:linear-gradient(90deg,var(--bs-gradient-end,#764ba2),var(--bs-gradient-start,#667eea));}
.pd-kpi.k-teal::before{background:linear-gradient(90deg,#0d9488,#14b8a6);}
.pd-kpi.k-rose::before{background:linear-gradient(90deg,var(--bs-ui-danger,#e11d48),#f43f5e);}
.pd-kpi .k-icon{font-size:1.3rem;opacity:.1;position:absolute;right:.5rem;top:.5rem;}
.pd-kpi.k-blue .k-icon,.pd-kpi.k-blue .k-val{color:var(--bs-gradient-start,#667eea);}
.pd-kpi.k-green .k-icon,.pd-kpi.k-green .k-val{color:var(--bs-ui-success,#34c38f);}
.pd-kpi.k-orange .k-icon,.pd-kpi.k-orange .k-val{color:var(--bs-ui-danger,#f5576c);}
.pd-kpi.k-cyan .k-icon,.pd-kpi.k-cyan .k-val{color:var(--bs-ui-info,#00b4db);}
.pd-kpi.k-gold .k-icon,.pd-kpi.k-gold .k-val{color:var(--bs-ui-warning,#f5b041);}
.pd-kpi.k-purple .k-icon,.pd-kpi.k-purple .k-val{color:var(--bs-gradient-end,#764ba2);}
.pd-kpi.k-teal .k-icon,.pd-kpi.k-teal .k-val{color:#0d9488;}
.pd-kpi.k-rose .k-icon,.pd-kpi.k-rose .k-val{color:var(--bs-ui-danger,#e11d48);}
.pd-kpi .k-val{font-size:1.3rem;font-weight:800;line-height:1;}
.pd-kpi .k-lbl{font-size:.52rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-muted,#9ca3af);margin-top:.15rem;}

/* Card */
.pd-card{background:var(--bs-card-bg,#fff);border-radius:.55rem;box-shadow:0 1px 3px rgba(0,0,0,.04);border:1px solid var(--bs-surface-100,#f3f4f6);margin-bottom:.75rem;overflow:hidden;}
.pd-card-hdr{padding:.55rem .75rem;border-bottom:1px solid var(--bs-surface-100,#f3f4f6);display:flex;justify-content:space-between;align-items:center;}
.pd-card-hdr h6{font-weight:700;font-size:.72rem;color:var(--bs-surface-700,#374151);display:flex;align-items:center;gap:.3rem;margin:0;}
.pd-card-hdr h6 i{color:var(--bs-gradient-start,#667eea);font-size:.85rem;}
.pd-card-hdr .badge-count{font-size:.52rem;font-weight:700;padding:.12rem .4rem;border-radius:.2rem;background:rgba(102,126,234,.08);color:var(--bs-gradient-start,#667eea);}
.pd-card-body{padding:.75rem;}

/* Table */
.pd-table{width:100%;border-collapse:collapse;font-size:.68rem;}
.pd-table thead th{font-size:.52rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-muted,#9ca3af);border-bottom:1.5px solid var(--bs-surface-100,#f3f4f6);padding:.45rem .5rem;text-align:left;white-space:nowrap;}
.pd-table tbody td{padding:.45rem .5rem;border-bottom:1px solid var(--bs-surface-50,#f9fafb);color:var(--bs-surface-700,#374151);vertical-align:middle;}
.pd-table tbody tr:hover{background:var(--bs-surface-50,#f9fafb);}
.pd-table tbody tr:last-child td{border-bottom:none;}
.pd-badge{font-size:.5rem;font-weight:700;padding:.1rem .35rem;border-radius:.2rem;display:inline-block;}
.pd-badge.sale{background:rgba(52,195,143,.1);color:var(--bs-ui-success,#34c38f);}
.pd-badge.pending{background:rgba(245,176,65,.1);color:var(--bs-ui-warning,#f5b041);}
.pd-badge.declined{background:rgba(244,106,106,.1);color:var(--bs-ui-danger,#f46a6a);}
.pd-badge.default{background:rgba(116,120,141,.1);color:var(--bs-surface-500,#74788d);}
.pd-badge.paid{background:rgba(13,148,136,.1);color:#0d9488;}
.pd-badge.unpaid{background:rgba(245,87,108,.08);color:var(--bs-ui-danger,#f5576c);}

/* Commission checkbox */
.pd-check{width:14px;height:14px;cursor:pointer;accent-color:var(--bs-gradient-start,#667eea);}

/* Carrier Section */
.pd-carrier{background:var(--bs-surface-50,#f9fafb);border-radius:.4rem;padding:.55rem .65rem;margin-bottom:.35rem;border:1px solid var(--bs-surface-100,#f3f4f6);}
.pd-carrier-name{font-weight:700;font-size:.72rem;color:var(--bs-surface-700,#374151);display:flex;align-items:center;gap:.25rem;margin-bottom:.3rem;}
.pd-carrier-name i{color:var(--bs-gradient-start,#667eea);font-size:.8rem;}
.pd-carrier-meta{font-size:.55rem;color:var(--bs-surface-muted,#9ca3af);margin-bottom:.25rem;}
.pd-carrier-meta strong{color:var(--bs-surface-500,#6b7280);}
.pd-state-pill{font-size:.48rem;font-weight:700;padding:.08rem .3rem;border-radius:.15rem;background:rgba(102,126,234,.06);color:var(--bs-gradient-start,#667eea);display:inline-block;margin:.08rem .04rem;}
.pd-rate-badge{font-size:.48rem;font-weight:600;padding:.06rem .28rem;border-radius:.15rem;display:inline-block;margin:.06rem;}
.pd-rate-badge.level{background:rgba(52,195,143,.08);color:var(--bs-ui-success,#34c38f);}
.pd-rate-badge.graded{background:rgba(0,180,219,.08);color:var(--bs-ui-info,#0083b0);}
.pd-rate-badge.gi{background:rgba(245,176,65,.08);color:var(--bs-ui-warning,#d4960a);}
.pd-rate-badge.modified{background:rgba(116,120,141,.08);color:var(--bs-surface-500,#74788d);}

.pd-empty{text-align:center;padding:2rem 1rem;color:var(--bs-surface-muted,#9ca3af);}
.pd-empty i{font-size:2rem;display:block;margin-bottom:.5rem;opacity:.15;}

/* Mark paid toolbar */
.pd-toolbar{display:flex;gap:.3rem;align-items:center;flex-wrap:wrap;}
.pd-mark-btn{font-size:.58rem;font-weight:600;padding:.22rem .5rem;border-radius:.3rem;border:1px solid var(--bs-surface-200,#e5e7eb);background:var(--bs-card-bg,#fff);color:var(--bs-surface-500,#6b7280);cursor:pointer;display:inline-flex;align-items:center;gap:.2rem;transition:all .15s;}
.pd-mark-btn:hover{border-color:#0d9488;color:#0d9488;}
.pd-mark-btn.unpaid-btn:hover{border-color:var(--bs-ui-danger,#f46a6a);color:var(--bs-ui-danger,#f46a6a);}
.pd-mark-btn.active{background:rgba(13,148,136,.06);border-color:#0d9488;color:#0d9488;}
.pd-mark-btn:disabled{opacity:.5;cursor:not-allowed;}
.pd-select-all{font-size:.55rem;color:var(--bs-gradient-start,#667eea);cursor:pointer;text-decoration:underline;font-weight:600;}

@media(max-width:768px){
    .pd-kpi-grid{grid-template-columns:repeat(2,1fr);}
    .pd-table{font-size:.6rem;}
}
</style>
@endsection

@section('content')
<div class="pd-hdr">
    <h5><i class="bx bx-grid-alt"></i> Dashboard <span class="pd-sub">Welcome, {{ $partner->name }}</span></h5>
    <form method="GET" action="{{ route('partner.dashboard') }}" class="pd-filters" id="filterForm">
        <span class="pd-filter-label">Month</span>
        <input type="month" name="month" class="pd-filter-input" value="{{ $month }}" onchange="document.getElementById('filterForm').submit()">
        <div class="pd-filter-sep"></div>
        <span class="pd-filter-label">Custom Range</span>
        <input type="date" name="date_from" class="pd-filter-input" value="{{ request('date_from') }}" placeholder="From">
        <input type="date" name="date_to" class="pd-filter-input" value="{{ request('date_to') }}" placeholder="To">
        <button type="submit" class="pd-filter-btn"><i class="bx bx-filter-alt"></i> Apply</button>
        @if(request('date_from') || request('date_to'))
        <a href="{{ route('partner.dashboard') }}" class="pd-filter-btn outline"><i class="bx bx-x"></i> Clear</a>
        @endif
    </form>
</div>

{{-- KPI Cards --}}
<div class="pd-kpi-grid">
    <div class="pd-kpi k-blue"><i class="bx bx-file k-icon"></i><div class="k-val">{{ number_format($monthlyLeads ?? $totalLeads) }}</div><div class="k-lbl">Monthly Leads</div></div>
    <div class="pd-kpi k-green"><i class="bx bx-check-circle k-icon"></i><div class="k-val">{{ number_format($totalSales) }}</div><div class="k-lbl">Sales</div></div>
    <div class="pd-kpi k-orange"><i class="bx bx-time-five k-icon"></i><div class="k-val">{{ number_format($pendingLeads) }}</div><div class="k-lbl">Pending</div></div>
    <div class="pd-kpi k-cyan"><i class="bx bx-dollar-circle k-icon"></i><div class="k-val">${{ number_format($totalRevenue, 0) }}</div><div class="k-lbl">Total Revenue</div></div>
    <div class="pd-kpi k-gold"><i class="bx bx-wallet k-icon"></i><div class="k-val">${{ number_format($partnerCommission, 0) }}</div><div class="k-lbl">Your Commission</div></div>
    <div class="pd-kpi k-purple"><i class="bx bx-pie-chart-alt k-icon"></i><div class="k-val">${{ number_format($taurusShareDollars, 0) }}</div><div class="k-lbl">Taurus Share ({{ $ourCommissionPercentage }}%)</div></div>
    <div class="pd-kpi k-teal"><i class="bx bx-check-shield k-icon"></i><div class="k-val">${{ number_format($commissionPaid ?? 0, 0) }}</div><div class="k-lbl">Commission Paid</div></div>
    <div class="pd-kpi k-rose"><i class="bx bx-error-alt k-icon"></i><div class="k-val">${{ number_format($commissionUnpaid ?? 0, 0) }}</div><div class="k-lbl">Balance Due</div></div>
</div>

{{-- Leads Table with Commission Marking --}}
<div class="pd-card">
    <div class="pd-card-hdr">
        <h6><i class="bx bx-list-ul"></i> Leads & Commission Tracker</h6>
        <div class="pd-toolbar">
            <span class="pd-select-all" onclick="toggleSelectAll()">Select All Unpaid</span>
            <button type="button" class="pd-mark-btn" id="markPaidBtn" onclick="markSelectedPaid()" disabled>
                <i class="bx bx-check-double"></i> Mark as Paid
            </button>
            <button type="button" class="pd-mark-btn unpaid-btn" id="markUnpaidBtn" onclick="markSelectedUnpaid()" disabled>
                <i class="bx bx-x-circle"></i> Mark as Unpaid
            </button>
            <span class="badge-count" id="selectedCount">0 selected</span>
        </div>
    </div>
    <div class="pd-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th style="width:28px"><input type="checkbox" class="pd-check" id="checkAll" onchange="toggleSelectAll(this.checked)"></th>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Carrier</th>
                        <th>State</th>
                        <th>Premium</th>
                        <th>Coverage</th>
                        <th>Commission</th>
                        <th>Status</th>
                        <th>Paid</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLeads as $lead)
                    <tr data-lead-id="{{ $lead->id }}">
                        <td>
                            @if(in_array(strtolower($lead->status ?? ''), ['sale','approved','done','accepted']))
                            <input type="checkbox" class="pd-check lead-check" value="{{ $lead->id }}" data-paid="{{ $lead->commission_paid_to_partner ? '1' : '0' }}" onchange="updateSelectedCount()">
                            @endif
                        </td>
                        <td><strong>#{{ $lead->id }}</strong></td>
                        <td>{{ $lead->cn_name ?? 'N/A' }}</td>
                        <td>
                            @if($lead->insuranceCarrier)
                                <span class="pd-badge default">{{ $lead->insuranceCarrier->name }}</span>
                            @else
                                {{ $lead->carrier_name ?? 'N/A' }}
                            @endif
                        </td>
                        <td>{{ $lead->state ?? '—' }}</td>
                        <td><strong>${{ number_format($lead->monthly_premium ?? $lead->premium_amount ?? $lead->issued_premium ?? 0, 2) }}</strong></td>
                        <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                        <td>
                            @if($lead->agent_commission)
                                <span style="color:var(--bs-ui-success,#34c38f);font-weight:700">${{ number_format($lead->agent_commission, 2) }}</span>
                            @else
                                <span style="color:var(--bs-surface-muted,#9ca3af)">—</span>
                            @endif
                        </td>
                        <td>
                            @php $s = strtolower($lead->status ?? ''); @endphp
                            @if(in_array($s,['sale','approved','done','accepted']))
                                <span class="pd-badge sale">Sale</span>
                            @elseif(in_array($s,['pending']))
                                <span class="pd-badge pending">Pending</span>
                            @elseif($s === 'declined')
                                <span class="pd-badge declined">Declined</span>
                            @else
                                <span class="pd-badge default">{{ ucfirst($lead->status ?? 'N/A') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->commission_paid_to_partner)
                                <span class="pd-badge paid"><i class="bx bx-check" style="font-size:.6rem"></i> Paid</span>
                            @elseif(in_array($s,['sale','approved','done','accepted']))
                                <span class="pd-badge unpaid">Unpaid</span>
                            @else
                                <span style="color:var(--bs-surface-muted,#d1d5db)">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">{{ $lead->created_at ? $lead->created_at->format('M d') : '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11">
                            <div class="pd-empty">
                                <i class="bx bx-inbox"></i>
                                <p style="font-size:.7rem;font-weight:600;margin:0">No leads found for this period</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Carriers & States --}}
<div class="pd-card">
    <div class="pd-card-hdr">
        <h6><i class="bx bx-shield-quarter"></i> Your Assigned Carriers & States</h6>
        <span class="badge-count">{{ $carrierStates->count() }} carriers</span>
    </div>
    <div class="pd-card-body">
        @if($carrierStates->isEmpty())
            <div class="pd-empty">
                <i class="bx bx-briefcase-alt"></i>
                <p style="font-size:.7rem;font-weight:600;margin:0">No carriers assigned yet</p>
                <p style="font-size:.58rem;margin:.2rem 0 0">Contact your administrator for carrier assignments</p>
            </div>
        @else
            @foreach($carrierStates as $carrierId => $cd)
            <div class="pd-carrier">
                <div class="pd-carrier-name"><i class="bx bx-shield-quarter"></i> {{ $cd['carrier']->name }}</div>
                <div class="pd-carrier-meta"><strong>{{ $cd['state_count'] }} States:</strong></div>
                <div style="margin-bottom:.3rem">
                    @foreach($cd['states'] as $state)
                        <span class="pd-state-pill">{{ $state }}</span>
                    @endforeach
                </div>
                <div class="pd-carrier-meta"><strong>Settlement Rates:</strong></div>
                <div>
                    @if($cd['settlement_level_pct'])<span class="pd-rate-badge level">Level {{ $cd['settlement_level_pct'] }}%</span>@endif
                    @if($cd['settlement_graded_pct'])<span class="pd-rate-badge graded">Graded {{ $cd['settlement_graded_pct'] }}%</span>@endif
                    @if($cd['settlement_gi_pct'])<span class="pd-rate-badge gi">GI {{ $cd['settlement_gi_pct'] }}%</span>@endif
                    @if($cd['settlement_modified_pct'])<span class="pd-rate-badge modified">Modified {{ $cd['settlement_modified_pct'] }}%</span>@endif
                    @if(!$cd['settlement_level_pct'] && !$cd['settlement_graded_pct'] && !$cd['settlement_gi_pct'] && !$cd['settlement_modified_pct'])
                        <span style="font-size:.55rem;color:var(--bs-surface-muted,#d1d5db)">No settlement rates set</span>
                    @endif
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
function updateSelectedCount(){
    const checked = document.querySelectorAll('.lead-check:checked').length;
    document.getElementById('selectedCount').textContent = checked + ' selected';
    document.getElementById('markPaidBtn').disabled = checked === 0;
    document.getElementById('markUnpaidBtn').disabled = checked === 0;
}

function toggleSelectAll(forceState){
    const boxes = document.querySelectorAll('.lead-check');
    const state = typeof forceState === 'boolean' ? forceState : ![...boxes].some(b => b.checked);
    boxes.forEach(b => b.checked = state);
    if(document.getElementById('checkAll')) document.getElementById('checkAll').checked = state;
    updateSelectedCount();
}

function markSelectedPaid(){
    const ids = [...document.querySelectorAll('.lead-check:checked')].filter(b => b.dataset.paid === '0').map(b => b.value);
    if(ids.length === 0){ alert('No unpaid sales selected.'); return; }
    if(!confirm('Mark ' + ids.length + ' sale(s) as commission paid?')) return;

    fetch('{{ route("partner.mark-commission-paid") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ lead_ids: ids })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) location.reload();
        else alert(data.message || 'Failed to update.');
    })
    .catch(() => alert('Network error. Please try again.'));
}

function markSelectedUnpaid(){
    const ids = [...document.querySelectorAll('.lead-check:checked')].filter(b => b.dataset.paid === '1').map(b => b.value);
    if(ids.length === 0){ alert('No paid sales selected.'); return; }
    if(!confirm('Mark ' + ids.length + ' sale(s) as UNPAID?')) return;

    fetch('{{ route("partner.mark-commission-unpaid") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ lead_ids: ids })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) location.reload();
        else alert(data.message || 'Failed to update.');
    })
    .catch(() => alert('Network error. Please try again.'));
}
</script>
@endsection
