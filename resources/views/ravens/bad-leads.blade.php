@extends('layouts.master')

@section('title') Bad Leads & Disposed Calls @endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
    /* Disposition pills */
    .disp-pill      { display:inline-block;padding:.15rem .5rem;border-radius:10px;font-size:.62rem;font-weight:700;white-space:nowrap; }
    .disp-no-answer { background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.15); }
    .disp-wrong-number { background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.15); }
    .disp-wrong-details{ background:rgba(245,158,11,.06);color:#b45309;border:1px solid rgba(245,158,11,.12); }
    .disp-am        { background:rgba(99,102,241,.1);color:#6366f1;border:1px solid rgba(99,102,241,.2); }
    .disp-busy      { background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.18); }
    .disp-dair      { background:rgba(100,116,139,.1);color:#475569;border:1px solid rgba(100,116,139,.18); }
    .disp-dc        { background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.18); }
    .disp-dec       { background:rgba(220,38,38,.12);color:#b91c1c;border:1px solid rgba(220,38,38,.2); }
    .disp-dnc       { background:rgba(124,58,237,.1);color:#7c3aed;border:1px solid rgba(124,58,237,.18); }
    .disp-n         { background:rgba(180,83,9,.08);color:#b45309;border:1px solid rgba(180,83,9,.15); }
    .disp-ni        { background:rgba(3,105,161,.1);color:#0369a1;border:1px solid rgba(3,105,161,.18); }
    .disp-np        { background:rgba(6,95,70,.1);color:#065f46;border:1px solid rgba(6,95,70,.15); }
    .disp-callback  { background:rgba(5,150,105,.1);color:#059669;border:1px solid rgba(5,150,105,.18); }
    .disp-bn        { background:rgba(8,145,178,.1);color:#0891b2;border:1px solid rgba(8,145,178,.18); }
    .disp-nnis      { background:rgba(159,18,57,.1);color:#9f1239;border:1px solid rgba(159,18,57,.18); }
    .disp-upd       { background:rgba(100,116,139,.1);color:#475569;border:1px solid rgba(100,116,139,.18); }
    .disp-other     { background:rgba(156,163,175,.1);color:#6b7280;border:1px solid rgba(156,163,175,.15); }
    /* Trigger badges */
    .trigger-pill { display:inline-block;padding:.12rem .4rem;border-radius:8px;font-size:.6rem;font-weight:700; }
    .trigger-end  { background:rgba(220,38,38,.08);color:#dc2626; }
    .trigger-save { background:rgba(5,150,105,.08);color:#059669; }
    /* Search input */
    .pipe-search { font-size:.72rem;font-weight:600;padding:.32rem .55rem .32rem 1.8rem;border-radius:22px;border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);color:var(--bs-surface-600);outline:none;min-width:160px;transition:border-color .15s; }
    .pipe-search:focus { border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12); }
    .pipe-search-wrap { position:relative;display:inline-flex;align-items:center; }
    .pipe-search-wrap i { position:absolute;left:.55rem;font-size:.8rem;color:var(--bs-surface-400);pointer-events:none; }
    /* DC filter bar */
    .dc-filter-bar { display:flex;flex-wrap:wrap;align-items:center;gap:.4rem;padding:.6rem .8rem;background:var(--bs-card-bg);border-radius:.5rem;border:1px solid rgba(0,0,0,.06);margin-bottom:.7rem; }
    .dc-filter-select { font-size:.7rem;font-weight:600;padding:.28rem .5rem;border-radius:18px;border:1px solid rgba(0,0,0,.1);background:var(--bs-card-bg);color:var(--bs-surface-600);outline:none;cursor:pointer; }
    .dc-filter-select:focus { border-color:#d4af37; }
    /* KPI row for disposed calls */
    .dc-kpi-row { display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.7rem; }
    .dc-kpi { display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:90px;padding:.5rem .6rem;border-radius:.45rem;border:1px solid rgba(0,0,0,.06);background:var(--bs-card-bg);text-align:center; }
    .dc-kpi-val { font-size:1.1rem;font-weight:800;line-height:1; }
    .dc-kpi-lbl { font-size:.6rem;font-weight:600;color:var(--bs-surface-400);margin-top:.18rem;white-space:nowrap; }
    /* Tab switcher */
    .bl-tab-bar { display:flex;align-items:center;gap:.35rem;padding:.55rem .7rem;background:var(--bs-card-bg);border-radius:.6rem;border:1px solid rgba(0,0,0,.07);margin-bottom:1rem; }
    .bl-tab { display:inline-flex;align-items:center;gap:.35rem;padding:.34rem .85rem;border-radius:22px;font-size:.72rem;font-weight:700;color:var(--bs-surface-500);cursor:pointer;border:1.5px solid transparent;transition:all .15s;text-decoration:none;white-space:nowrap; }
    .bl-tab:hover { background:rgba(0,0,0,.04);color:var(--bs-surface-700);text-decoration:none; }
    .bl-tab.active-contacts { background:rgba(220,38,38,.08);color:#c84646;border-color:rgba(220,38,38,.2); }
    .bl-tab.active-calls    { background:rgba(99,102,241,.08);color:#4f46e5;border-color:rgba(99,102,241,.2); }
    .bl-tab .badge-count { display:inline-block;padding:.07rem .38rem;border-radius:9px;font-size:.6rem;font-weight:800;background:rgba(0,0,0,.06);color:inherit; }
</style>
@endsection

@section('content')

    @php $activeTab = request('tab', 'contacts'); @endphp

    {{-- SHARED DATE/SEARCH FILTER BAR --}}
    <form method="GET" action="{{ route('ravens.bad-leads') }}" id="filterForm" class="ex-card pipe-filter-bar">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <a href="{{ route('ravens.bad-leads', array_merge(request()->except('filter','start_date','end_date'), ['filter' => 'today'])) }}" class="pipe-pill {{ ($filter ?? 'today') === 'today' ? 'active' : '' }}"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill {{ ($filter ?? '') === 'custom' ? 'active' : '' }}" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:{{ ($filter ?? '') === 'custom' ? 'flex' : 'none' }};align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="text" name="start_date" class="pipe-pill-date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD" readonly>
            <span class="pipe-pill-lbl">TO</span>
            <input type="text" name="end_date" class="pipe-pill-date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD" readonly>
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <div class="pipe-search-wrap">
            <i class="bx bx-search"></i>
            <input type="text" name="search" class="pipe-search" value="{{ $search ?? '' }}" placeholder="Search name, phone...">
        </div>
        @if(($filter ?? 'today') !== 'today' || !empty($search))
            <a href="{{ route('ravens.bad-leads', ['tab' => $activeTab]) }}" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        @endif
    </form>

    {{-- TAB SWITCHER --}}
    <div class="bl-tab-bar">
        <a href="{{ route('ravens.bad-leads', array_merge(request()->except('tab'), ['tab' => 'contacts'])) }}"
           class="bl-tab {{ $activeTab === 'contacts' ? 'active-contacts' : '' }}">
            <i class="bx bx-trash"></i> Disposed Contacts
            <span class="badge-count">{{ $badLeads->total() }}</span>
        </a>
        <a href="{{ route('ravens.bad-leads', array_merge(request()->except('tab'), ['tab' => 'calls'])) }}"
           class="bl-tab {{ $activeTab === 'calls' ? 'active-calls' : '' }}">
            <i class="bx bx-phone-call"></i> Disposed Calls
            <span class="badge-count">{{ $disposedCalls->total() }}</span>
            <span style="font-size:.58rem;font-weight:500;color:var(--bs-surface-400);margin-left:.2rem;">stays in system</span>
        </a>
    </div>

    {{-- ═══ TAB PANEL: DISPOSED CONTACTS ═══ --}}
    <div id="tab-contacts" style="{{ $activeTab === 'contacts' ? '' : 'display:none;' }}">

        <div class="kpi-row">
            <div class="kpi-card k-red ex-card">
                <i class="bx bx-error-circle k-icon"></i>
                <div class="k-val">{{ $badStats['total'] ?? 0 }}</div>
                <div class="k-lbl">Total Disposed</div>
            </div>
            <div class="kpi-card k-warn ex-card">
                <i class="bx bx-phone-off k-icon"></i>
                <div class="k-val">{{ $badStats['no_answer'] ?? 0 }}</div>
                <div class="k-lbl">No Answer</div>
            </div>
            <div class="kpi-card k-purple ex-card">
                <i class="bx bx-x-circle k-icon"></i>
                <div class="k-val">{{ $badStats['wrong_number'] ?? 0 }}</div>
                <div class="k-lbl">Wrong Number</div>
            </div>
            <div class="kpi-card k-gray ex-card">
                <i class="bx bx-error k-icon"></i>
                <div class="k-val">{{ $badStats['wrong_details'] ?? 0 }}</div>
                <div class="k-lbl">Wrong Details</div>
            </div>
            <div class="kpi-card k-blue ex-card">
                <i class="bx bx-dots-horizontal-rounded k-icon"></i>
                <div class="k-val">{{ $badStats['other'] ?? 0 }}</div>
                <div class="k-lbl">Other</div>
            </div>
        </div>

        <div class="ex-card sec-card">
            <div class="pipe-hdr" style="color:#c84646;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.4rem;">
                <span><i class="bx bx-trash" style="color:#f46a6a;"></i> Disposed Contacts <span class="badge-count">{{ $badLeads->total() }}</span></span>
                <form method="GET" action="{{ route('ravens.bad-leads') }}" style="display:inline-flex;align-items:center;gap:.35rem;font-size:.7rem;font-weight:600;color:var(--bs-surface-500);">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="tab" value="contacts">
                    <label for="perPageSelect">Show</label>
                    <select id="perPageSelect" name="per_page" onchange="this.form.submit()" style="font-size:.7rem;font-weight:700;padding:.2rem .4rem;border-radius:6px;border:1px solid rgba(0,0,0,.1);background:var(--bs-card-bg);color:var(--bs-surface-600);cursor:pointer;">
                        @foreach([10, 20, 50, 100, 200, 500, 1000] as $size)
                            <option value="{{ $size }}" {{ $badLeads->perPage() == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>entries</span>
                </form>
            </div>
            <div class="scroll-tbl" style="max-height:450px;">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>#</th><th>Lead Name</th><th>Phone</th>
                            <th class="text-center">Disposition</th>
                            <th>Disposed By</th><th>Date</th><th>Notes</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($badLeads as $index => $badLead)
                            <tr id="row-{{ $badLead->lead_id }}">
                                <td>{{ $badLeads->firstItem() + $index }}</td>
                                <td><strong>{{ $badLead->lead_name ?? 'N/A' }}</strong></td>
                                <td>{{ $badLead->lead_phone ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @php
                                        $dispClass = match($badLead->disposition) {
                                            'no_answer'    => 'disp-no-answer',
                                            'wrong_number' => 'disp-wrong-number',
                                            'wrong_details'=> 'disp-wrong-details',
                                            default        => 'disp-other',
                                        };
                                    @endphp
                                    <span class="disp-pill {{ $dispClass }}">{{ \App\Models\BadLead::getDispositionLabel($badLead->disposition) }}</span>
                                </td>
                                <td>{{ $badLead->disposedBy->name ?? 'Unknown' }}</td>
                                <td style="white-space:nowrap;">{{ $badLead->created_at->setTimezone('America/Los_Angeles')->format('M d, h:i A') }}</td>
                                <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $badLead->notes }}">{{ $badLead->notes ?? '—' }}</td>
                                <td class="text-center">
                                    <button class="act-btn a-success" onclick="sendBackLead({{ $badLead->lead_id }}, this)" title="Restore to calling system">
                                        <i class="bx bx-undo"></i> Restore
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-check-circle" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i> No disposed contacts found
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($badLeads->hasPages())
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem .65rem;border-top:1px solid rgba(0,0,0,.04);font-size:.68rem;color:var(--bs-surface-400);">
                    <span>Showing {{ $badLeads->firstItem() }} to {{ $badLeads->lastItem() }} of {{ $badLeads->total() }}</span>
                    <div>{{ $badLeads->appends(['tab' => 'contacts'])->links() }}</div>
                </div>
            @endif
        </div>

    </div>{{-- /tab-contacts --}}

    {{-- ═══ TAB PANEL: DISPOSED CALLS ═══ --}}
    <div id="tab-calls" style="{{ $activeTab === 'calls' ? '' : 'display:none;' }}">

        {{-- DC KPIs --}}
        <div class="dc-kpi-row">
            <div class="dc-kpi" style="border-color:rgba(99,102,241,.25);min-width:70px;">
                <div class="dc-kpi-val" style="color:#6366f1;">{{ $disposedCallStats['total'] }}</div>
                <div class="dc-kpi-lbl">Total</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(99,102,241,.15);">
                <div class="dc-kpi-val" style="color:#6366f1;">{{ $disposedCallStats['answering_machine'] }}</div>
                <div class="dc-kpi-lbl">A — Answ. Machine</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(245,158,11,.18);">
                <div class="dc-kpi-val" style="color:#d97706;">{{ $disposedCallStats['busy'] }}</div>
                <div class="dc-kpi-lbl">B — Busy</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(100,116,139,.18);">
                <div class="dc-kpi-val" style="color:#475569;">{{ $disposedCallStats['dead_air'] }}</div>
                <div class="dc-kpi-lbl">DAIR — Dead Air</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(239,68,68,.18);">
                <div class="dc-kpi-val" style="color:#dc2626;">{{ $disposedCallStats['disconnected'] }}</div>
                <div class="dc-kpi-lbl">DC — Disconnected</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(220,38,38,.22);">
                <div class="dc-kpi-val" style="color:#b91c1c;">{{ $disposedCallStats['declined_sale'] }}</div>
                <div class="dc-kpi-lbl">DEC — Declined</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(124,58,237,.18);">
                <div class="dc-kpi-val" style="color:#7c3aed;">{{ $disposedCallStats['dnc'] }}</div>
                <div class="dc-kpi-lbl">DNC</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(180,83,9,.15);">
                <div class="dc-kpi-val" style="color:#b45309;">{{ $disposedCallStats['no_answer_ec'] }}</div>
                <div class="dc-kpi-lbl">N — No Answer</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(3,105,161,.18);">
                <div class="dc-kpi-val" style="color:#0369a1;">{{ $disposedCallStats['not_interested'] }}</div>
                <div class="dc-kpi-lbl">NI — Not Interested</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(6,95,70,.15);">
                <div class="dc-kpi-val" style="color:#065f46;">{{ $disposedCallStats['no_pitch'] }}</div>
                <div class="dc-kpi-lbl">NP — No Pitch</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(5,150,105,.2);">
                <div class="dc-kpi-val" style="color:#059669;">{{ $disposedCallStats['callback_set'] }}</div>
                <div class="dc-kpi-lbl">Callback Set</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(8,145,178,.18);">
                <div class="dc-kpi-val" style="color:#0891b2;">{{ $disposedCallStats['business_number'] }}</div>
                <div class="dc-kpi-lbl">BN — Business #</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(159,18,57,.18);">
                <div class="dc-kpi-val" style="color:#9f1239;">{{ $disposedCallStats['not_in_service'] }}</div>
                <div class="dc-kpi-lbl">NNIS — Not In Svc</div>
            </div>
            <div class="dc-kpi" style="border-color:rgba(100,116,139,.2);">
                <div class="dc-kpi-val" style="color:#475569;">{{ $disposedCallStats['updated_data'] }}</div>
                <div class="dc-kpi-lbl">Updated Data</div>
            </div>
        </div>

        {{-- DC Advanced Filters --}}
        <form method="GET" action="{{ route('ravens.bad-leads') }}" id="dcFilterForm" class="dc-filter-bar">
            @foreach(request()->only(['filter','start_date','end_date','search','per_page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <input type="hidden" name="tab" value="calls">
            <span style="font-size:.68rem;font-weight:700;color:var(--bs-surface-500);white-space:nowrap;"><i class="bx bx-filter-alt me-1"></i>Filter:</span>

            <select name="dc_closer" class="dc-filter-select" onchange="this.form.submit()">
                <option value="">All Closers</option>
                @foreach($closersList as $closer)
                    <option value="{{ $closer->id }}" {{ ($dcCloserFilter ?? '') == $closer->id ? 'selected' : '' }}>{{ $closer->name }}</option>
                @endforeach
            </select>

            <select name="dc_disposition" class="dc-filter-select" onchange="this.form.submit()">
                <option value="">All Dispositions</option>
                <option value="answering_machine" {{ ($dcDispositionFilter ?? '') === 'answering_machine' ? 'selected' : '' }}>A — Answering Machine</option>
                <option value="busy"              {{ ($dcDispositionFilter ?? '') === 'busy'              ? 'selected' : '' }}>B — Busy</option>
                <option value="dead_air"          {{ ($dcDispositionFilter ?? '') === 'dead_air'          ? 'selected' : '' }}>DAIR — Dead Air</option>
                <option value="disconnected"      {{ ($dcDispositionFilter ?? '') === 'disconnected'      ? 'selected' : '' }}>DC — Disconnected Number</option>
                <option value="declined_sale"     {{ ($dcDispositionFilter ?? '') === 'declined_sale'     ? 'selected' : '' }}>DEC — Declined Sale</option>
                <option value="dnc"               {{ ($dcDispositionFilter ?? '') === 'dnc'               ? 'selected' : '' }}>DNC — Do Not Call</option>
                <option value="no_answer_ec"      {{ ($dcDispositionFilter ?? '') === 'no_answer_ec'      ? 'selected' : '' }}>N — No Answer</option>
                <option value="not_interested"    {{ ($dcDispositionFilter ?? '') === 'not_interested'    ? 'selected' : '' }}>NI — Not Interested</option>
                <option value="no_pitch"          {{ ($dcDispositionFilter ?? '') === 'no_pitch'          ? 'selected' : '' }}>NP — No Pitch No Price</option>
                        <option value="business_number"   {{ ($dcDispositionFilter ?? '') === 'business_number'   ? 'selected' : '' }}>BN — Business Number</option>
                        <option value="not_in_service"    {{ ($dcDispositionFilter ?? '') === 'not_in_service'    ? 'selected' : '' }}>NNIS — Number Not In Service</option>
                        <option value="callback_set"      {{ ($dcDispositionFilter ?? '') === 'callback_set'      ? 'selected' : '' }}>Callback Set</option>
                        <option value="updated_data"      {{ ($dcDispositionFilter ?? '') === 'updated_data'      ? 'selected' : '' }}>Updated Data</option>
            </select>

            <select name="dc_trigger" class="dc-filter-select" onchange="this.form.submit()">
                <option value="">All Triggers</option>
                <option value="end_call"  {{ ($dcTriggerFilter ?? '') === 'end_call'  ? 'selected' : '' }}>End Call</option>
                <option value="save_exit" {{ ($dcTriggerFilter ?? '') === 'save_exit' ? 'selected' : '' }}>Save &amp; Exit</option>
            </select>

            <div class="pipe-search-wrap">
                <i class="bx bx-search"></i>
                <input type="text" name="dc_search" class="pipe-search" value="{{ $dcSearch ?? '' }}" placeholder="Name, phone..." id="dcSearchInput">
            </div>

            @if(!empty($dcCloserFilter) || !empty($dcDispositionFilter) || !empty($dcTriggerFilter) || !empty($dcSearch))
                <a href="{{ route('ravens.bad-leads', array_merge(request()->only(['filter','start_date','end_date','search','per_page']), ['tab' => 'calls'])) }}" class="pipe-pill-clear" style="margin-left:auto;"><i class="bx bx-x"></i> Clear</a>
            @endif
        </form>

        {{-- DC Table --}}
        <div class="ex-card sec-card">
            <div class="pipe-hdr" style="color:#4f46e5;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.4rem;">
                <span><i class="bx bx-phone-call" style="color:#6366f1;"></i> Disposed Calls <span class="badge-count">{{ $disposedCalls->total() }}</span></span>
                <form method="GET" action="{{ route('ravens.bad-leads') }}" style="display:inline-flex;align-items:center;gap:.35rem;font-size:.7rem;font-weight:600;color:var(--bs-surface-500);">
                    @foreach(request()->except('dc_per_page', 'dc_page') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <input type="hidden" name="tab" value="calls">
                    <label for="dcPerPageSelect">Show</label>
                    <select id="dcPerPageSelect" name="dc_per_page" onchange="this.form.submit()" style="font-size:.7rem;font-weight:700;padding:.2rem .4rem;border-radius:6px;border:1px solid rgba(0,0,0,.1);background:var(--bs-card-bg);color:var(--bs-surface-600);cursor:pointer;">
                        @foreach([10, 20, 50, 100, 200, 500, 1000] as $size)
                            <option value="{{ $size }}" {{ $disposedCalls->perPage() == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>entries</span>
                </form>
            </div>
            <div class="scroll-tbl" style="max-height:500px;">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>#</th><th>Lead Name</th><th>Phone</th>
                            <th class="text-center">Disposition</th>
                            <th class="text-center">Trigger</th>
                            <th>Closer</th><th>Date (PT)</th><th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disposedCalls as $index => $dc)
                            @php
                                $dcClass = match($dc->disposition) {
                                    'answering_machine' => 'disp-am',
                                    'busy'              => 'disp-busy',
                                    'dead_air'          => 'disp-dair',
                                    'disconnected'      => 'disp-dc',
                                    'declined_sale'     => 'disp-dec',
                                    'dnc'               => 'disp-dnc',
                                    'no_answer_ec'      => 'disp-n',
                                    'not_interested'    => 'disp-ni',
                                    'no_pitch'          => 'disp-np',
                                    'callback_set'      => 'disp-callback',
                                    'business_number'   => 'disp-bn',
                                    'not_in_service'    => 'disp-nnis',
                                    'updated_data'      => 'disp-upd',
                                    default             => 'disp-other',
                                };
                                $triggerLabel = $dc->trigger === 'end_call' ? 'End Call' : ($dc->trigger === 'save_exit' ? 'Save & Exit' : 'Unknown');
                                $triggerClass = $dc->trigger === 'end_call' ? 'trigger-end' : 'trigger-save';
                            @endphp
                            <tr>
                                <td>{{ $disposedCalls->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $dc->lead_name ?? 'N/A' }}</strong>
                                    @if($dc->lead_id)<div style="font-size:.62rem;color:var(--bs-surface-400);">Lead #{{ $dc->lead_id }}</div>@endif
                                </td>
                                <td>{{ $dc->lead_phone ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="disp-pill {{ $dcClass }}">{{ \App\Models\BadLead::getDispositionLabel($dc->disposition) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="trigger-pill {{ $triggerClass }}">{{ $triggerLabel }}</span>
                                </td>
                                <td>{{ $dc->disposedBy->name ?? 'Unknown' }}</td>
                                <td style="white-space:nowrap;">{{ $dc->created_at->setTimezone('America/Los_Angeles')->format('M d, h:i A') }}</td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $dc->notes }}">{{ $dc->notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-phone-off" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i> No disposed calls found for this period
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($disposedCalls->hasPages())
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem .65rem;border-top:1px solid rgba(0,0,0,.04);font-size:.68rem;color:var(--bs-surface-400);">
                    <span>Showing {{ $disposedCalls->firstItem() }} to {{ $disposedCalls->lastItem() }} of {{ $disposedCalls->total() }}</span>
                    <div>{{ $disposedCalls->appends(['tab' => 'calls'])->links() }}</div>
                </div>
            @endif
        </div>

    </div>{{-- /tab-calls --}}

@endsection

@section('script')
@include('partials.sl-filter-assets')
<script>
    document.querySelector('#filterForm .pipe-search')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('filterForm').submit(); }
    });
    document.getElementById('dcSearchInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('dcFilterForm').submit(); }
    });

    function sendBackLead(leadId, button) {
        if (!confirm('Send this lead back to the calling system?')) return;
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
        fetch('{{ route('ravens.leads.restore') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ lead_id: leadId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = button.closest('tr');
                row.style.transition = 'opacity .3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            } else {
                alert(data.message || 'Failed to restore lead');
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        })
        .catch(() => { alert('An error occurred'); button.disabled = false; button.innerHTML = originalHtml; });
    }
</script>
@endsection
