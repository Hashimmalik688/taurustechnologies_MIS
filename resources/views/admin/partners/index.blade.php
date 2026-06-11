@extends('layouts.master')

@section('title') Partners Management @endsection

@section('css')
<style>
/* ─── Partners Page ─── */
.pt-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
.pt-page-hdr h5 { font-weight:800; font-size:1.1rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.pt-page-hdr .pt-sub { font-size:.7rem; font-weight:500; color:var(--bs-surface-500); margin-left:.25rem; }
.pt-add-btn { background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end)); color:#fff; border:none; padding:.45rem 1rem; border-radius:.5rem; font-size:.72rem; font-weight:600; display:inline-flex; align-items:center; gap:.35rem; text-decoration:none; transition:all .2s; box-shadow:0 2px 8px rgba(102,126,234,.25); }
.pt-add-btn:hover { transform:translateY(-2px); box-shadow:0 4px 14px rgba(102,126,234,.35); color:#fff; }

/* KPI Row */
.pt-kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:.65rem; margin-bottom:1.25rem; }
.pt-kpi { background:var(--bs-card-bg); border-radius:.65rem; padding:.85rem 1rem; position:relative; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.04); }
.pt-kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:.65rem .65rem 0 0; }
.pt-kpi.k-blue::before { background:linear-gradient(90deg,var(--bs-gradient-start),var(--bs-gradient-end)); }
.pt-kpi.k-green::before { background:linear-gradient(90deg,#34c38f,#38ef7d); }
.pt-kpi.k-orange::before { background:linear-gradient(90deg,#f5b041,#f39c12); }
.pt-kpi.k-purple::before { background:linear-gradient(90deg,#764ba2,#667eea); }
.pt-kpi .k-icon { font-size:1.5rem; opacity:.15; position:absolute; right:.75rem; top:.75rem; }
.pt-kpi.k-blue .k-icon,.pt-kpi.k-blue .k-val { color:var(--bs-gradient-start); }
.pt-kpi.k-green .k-icon,.pt-kpi.k-green .k-val { color:#34c38f; }
.pt-kpi.k-orange .k-icon,.pt-kpi.k-orange .k-val { color:#f5b041; }
.pt-kpi.k-purple .k-icon,.pt-kpi.k-purple .k-val { color:#764ba2; }
.pt-kpi .k-val { font-size:1.5rem; font-weight:800; line-height:1; }
.pt-kpi .k-lbl { font-size:.62rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--bs-surface-500); margin-top:.25rem; }

/* Table Card */
.pt-card { background:var(--bs-card-bg); border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.04); overflow:hidden; }
.pt-card-hdr { padding:.75rem 1rem; border-bottom:1px solid var(--bs-surface-100); display:flex; justify-content:space-between; align-items:center; }
.pt-card-hdr h6 { font-weight:700; font-size:.78rem; color:var(--bs-surface-700); margin:0; }
.pt-search { border:1px solid var(--bs-surface-200); border-radius:.4rem; padding:.3rem .6rem; font-size:.7rem; width:200px; background:var(--bs-card-bg); }
.pt-search:focus { outline:none; border-color:var(--bs-gradient-start); box-shadow:0 0 0 2px rgba(102,126,234,.1); }

/* Table */
.pt-table { width:100%; border-collapse:collapse; }
.pt-table th { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--bs-surface-500); padding:.55rem .75rem; border-bottom:2px solid var(--bs-surface-100); background:var(--bs-surface-bg-light); }
.pt-table td { font-size:.72rem; padding:.6rem .75rem; border-bottom:1px solid var(--bs-surface-50); vertical-align:middle; color:var(--bs-surface-700); }
.pt-table tr:hover td { background:rgba(102,126,234,.02); }
.pt-table tr:last-child td { border-bottom:none; }

/* Partner Row Styles */
.pt-avatar { width:30px; height:30px; border-radius:.4rem; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:.55rem; color:#fff; flex-shrink:0; }
.pt-name-cell { display:flex; align-items:center; gap:.5rem; }
.pt-name { font-weight:700; font-size:.72rem; color:var(--bs-surface-800); }
.pt-code { font-size:.58rem; font-weight:600; padding:.12rem .4rem; border-radius:.25rem; background:rgba(102,126,234,.08); color:var(--bs-gradient-start); }
.pt-email { font-size:.68rem; color:var(--bs-surface-500); }
.pt-badge { font-size:.58rem; font-weight:600; padding:.15rem .45rem; border-radius:.3rem; }
.pt-badge.active { background:rgba(52,195,143,.1); color:#34c38f; }
.pt-badge.inactive { background:rgba(116,120,141,.1); color:#74788d; }
.pt-carrier-badge { font-size:.55rem; font-weight:600; padding:.1rem .35rem; border-radius:.2rem; background:rgba(85,110,230,.08); color:var(--bs-gradient-start); margin:.1rem; display:inline-block; }
.pt-commission { font-weight:700; color:var(--bs-gradient-end); font-size:.72rem; }
.pt-actions { display:flex; gap:.25rem; }
.pt-act-btn { width:26px; height:26px; border-radius:.35rem; display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); font-size:.7rem; cursor:pointer; transition:all .15s; text-decoration:none; }
.pt-act-btn:hover { transform:translateY(-1px); box-shadow:0 2px 6px rgba(0,0,0,.08); }
.pt-act-btn.view:hover { border-color:#17a2b8; color:#17a2b8; background:rgba(23,162,184,.05); }
.pt-act-btn.edit:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); background:rgba(102,126,234,.05); }
.pt-act-btn.delete:hover { border-color:#f46a6a; color:#f46a6a; background:rgba(244,106,106,.05); }
.pt-empty { text-align:center; padding:3rem 1rem; color:var(--bs-surface-500); }
.pt-empty i { font-size:3rem; display:block; margin-bottom:.75rem; opacity:.15; }
.pt-empty p { font-size:.78rem; margin:.25rem 0; }

/* Type badges */
.pt-type-badge { font-size:.58rem; font-weight:700; padding:.15rem .45rem; border-radius:.3rem; display:inline-flex; align-items:center; gap:.25rem }
.pt-type-badge.partner { background:rgba(85,110,230,.1); color:#556ee6; border:1px solid rgba(85,110,230,.15) }
.pt-type-badge.agent { background:rgba(139,92,246,.1); color:#7c3aed; border:1px solid rgba(139,92,246,.15) }
.pt-downline-badge { font-size:.6rem; font-weight:700; color:#f59e0b; background:rgba(245,158,11,.08); padding:.1rem .4rem; border-radius:.25rem; border:1px solid rgba(245,158,11,.12) }

/* Filter pills */
.pt-filter-pill { font-size:.65rem; font-weight:600; padding:.2rem .55rem; border-radius:.3rem; text-decoration:none; transition:all .15s; border:1px solid rgba(0,0,0,.08); color:var(--bs-surface-500); background:var(--bs-card-bg) }
.pt-filter-pill:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start) }
.pt-filter-pill.active { background:rgba(85,110,230,.1); border-color:rgba(85,110,230,.3); color:var(--bs-gradient-start); font-weight:700 }
</style>
@endsection

@section('content')

<div class="pt-page-hdr">
    <h5><i class="bx bx-group"></i> Partners Management <span class="pt-sub">External partner network</span></h5>
    <a href="{{ route('admin.partners.create') }}" class="pt-add-btn"><i class="bx bx-plus"></i> Add Partner</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 px-3" style="font-size:.75rem;border-radius:.5rem;" role="alert">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.5rem;padding:.75rem;"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-2 px-3" style="font-size:.75rem;border-radius:.5rem;" role="alert">
    <i class="bx bx-error me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.5rem;padding:.75rem;"></button>
</div>
@endif

@php
    $activeCount = $partners->where('is_active', true)->count();
    $inactiveCount = $partners->where('is_active', false)->count();
    $totalCarrierAssignments = $partners->sum(fn($p) => $p->carrierStates->pluck('insurance_carrier_id')->unique()->count());
    $totalStatesCovered = $partners->sum(fn($p) => $p->carrierStates->pluck('state')->unique()->count());
@endphp
<div class="pt-kpi-row">
    <div class="pt-kpi k-blue"><i class="bx bx-group k-icon"></i><div class="k-val">{{ $partners->count() }}</div><div class="k-lbl">Total</div></div>
    <div class="pt-kpi k-green"><i class="bx bx-buildings k-icon"></i><div class="k-val">{{ $partnerCount }}</div><div class="k-lbl">Partners</div></div>
    <div class="pt-kpi k-purple"><i class="bx bx-user-voice k-icon"></i><div class="k-val">{{ $agentCount }}</div><div class="k-lbl">Downline Agents</div></div>
    <div class="pt-kpi k-orange"><i class="bx bx-briefcase k-icon"></i><div class="k-val">{{ $totalCarrierAssignments }}</div><div class="k-lbl">Carrier Assignments</div></div>
</div>

<div class="pt-card">
    <div class="pt-card-hdr">
        <h6>
            <i class="bx bx-list-ul me-1"></i>
            @if($typeFilter === 'agent') Downline Agents
            @elseif($typeFilter === 'partner') Partners
            @else All Partners &amp; Downline
            @endif
        </h6>
        <div style="display:flex;align-items:center;gap:.5rem;">
            <a href="{{ route('admin.partners.index') }}" class="pt-filter-pill {{ !$typeFilter ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.partners.index', ['type' => 'partner']) }}" class="pt-filter-pill {{ $typeFilter === 'partner' ? 'active' : '' }}">Partners</a>
            <a href="{{ route('admin.partners.index', ['type' => 'agent']) }}" class="pt-filter-pill {{ $typeFilter === 'agent' ? 'active' : '' }}">Downline</a>
            <input type="text" class="pt-search" id="ptSearch" placeholder="Search..." autocomplete="off">
        </div>
    </div>
    <div class="table-responsive">
        <table class="pt-table" id="ptTable">
            <thead>
                <tr>
                    <th>Partner</th>
                    <th>Type</th>
                    <th>Upline</th>
                    <th>Downline</th>
                    <th>Email</th>
                    <th>Our Comm</th>
                    <th>Carriers</th>
                    <th>States</th>
                    <th>Status</th>
                    <th>Login</th>
                    <th style="width:100px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partners as $partner)
                @php
                    $uniqueCarriers = $partner->carrierStates->pluck('insurance_carrier_id')->unique();
                    $totalStatesP = $partner->carrierStates->pluck('state')->unique()->count();
                    $hue = crc32($partner->name) % 360;
                    $ini = strtoupper(collect(explode(' ', $partner->name))->map(fn($w) => substr($w,0,1))->take(2)->join(''));
                    $downlineCount = $partner->agents->count();
                @endphp
                <tr>
                    <td>
                        <div class="pt-name-cell">
                            <div class="pt-avatar" style="background:hsl({{ $hue }},55%,50%)">{{ $ini }}</div>
                            <span class="pt-name">{{ $partner->name }}</span>
                            <span class="pt-code">{{ $partner->code }}</span>
                        </div>
                    </td>
                    <td>
                        @if(($partner->type ?? 'partner') === 'agent')
                            <span class="pt-type-badge agent"><i class="bx bx-user"></i> Downline</span>
                        @else
                            <span class="pt-type-badge partner"><i class="bx bx-buildings"></i> Partner</span>
                        @endif
                    </td>
                    <td>
                        @if($partner->parent)
                            <a href="{{ route('admin.partners.show', $partner->parent->id) }}" style="color:var(--bs-gradient-start);font-weight:600;font-size:.68rem;text-decoration:none">
                                {{ $partner->parent->name }}
                            </a>
                        @else
                            <span style="color:var(--bs-surface-400);font-size:.65rem">—</span>
                        @endif
                    </td>
                    <td>
                        @if($downlineCount > 0)
                            <span class="pt-downline-badge">{{ $downlineCount }} agent{{ $downlineCount > 1 ? 's' : '' }}</span>
                        @else
                            <span style="color:var(--bs-surface-400);font-size:.65rem">—</span>
                        @endif
                    </td>
                    <td class="pt-email">{{ $partner->email ?? '—' }}</td>
                    <td><span class="pt-commission">{{ number_format($partner->our_commission_percentage ?? 0, 1) }}%</span></td>
                    <td>
                        @foreach($uniqueCarriers as $cid)
                            @php $cn = $partner->carrierStates->firstWhere('insurance_carrier_id', $cid)?->insuranceCarrier?->name; @endphp
                            @if($cn)<span class="pt-carrier-badge">{{ $cn }}</span>@endif
                        @endforeach
                        @if($uniqueCarriers->isEmpty())<span style="color:var(--bs-surface-400);font-size:.65rem">None</span>@endif
                    </td>
                    <td style="font-size:.72rem;font-weight:600">{{ $totalStatesP }}</td>
                    <td><span class="pt-badge {{ $partner->is_active ? 'active' : 'inactive' }}">{{ $partner->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        @if($partner->password)
                            <span class="pt-badge active"><i class="bx bx-check" style="font-size:.55rem"></i> Set</span>
                        @else
                            <span class="pt-badge inactive"><i class="bx bx-x" style="font-size:.55rem"></i> No</span>
                        @endif
                    </td>
                    <td>
                        <div class="pt-actions">
                            <a href="{{ route('admin.partners.show', $partner->id) }}" class="pt-act-btn view" title="View"><i class="bx bx-show"></i></a>
                            @canEditModule('partners')
                            <a href="{{ route('admin.partners.edit', $partner->id) }}" class="pt-act-btn edit" title="Edit"><i class="bx bx-edit-alt"></i></a>
                            @endcanEditModule
                            @canDeleteInModule('partners')
                            <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete {{ addslashes($partner->name) }}? This removes all carrier assignments.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="pt-act-btn delete" title="Delete"><i class="bx bx-trash"></i></button>
                            </form>
                            @endcanDeleteInModule
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11">
                        <div class="pt-empty">
                            <i class="bx bx-user-x"></i>
                            <p><strong>No {{ $typeFilter === 'agent' ? 'Downline Agents' : 'Partners' }} Found</strong></p>
                            <p>Click "Add Partner" to create your first partner</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
document.getElementById('ptSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#ptTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection
