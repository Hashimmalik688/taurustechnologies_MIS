@extends('layouts.master')

@section('title') Dialer Report @endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        /* KPI cards */
        .dr-kpi-row { display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem }
        .dr-kpi { display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:92px;padding:.6rem .7rem;border-radius:.5rem;border:1px solid rgba(0,0,0,.06);background:var(--bs-card-bg);text-align:center;flex:1;min-width:80px }
        .dr-kpi-val { font-size:1.2rem;font-weight:800;line-height:1 }
        .dr-kpi-lbl { font-size:.6rem;font-weight:600;color:var(--bs-surface-400);margin-top:.2rem;white-space:nowrap }

        /* Matrix table */
        .dr-matrix { width:100%;border-collapse:separate;border-spacing:0;font-size:.7rem }
        .dr-matrix thead th {
            padding:.5rem .55rem;font-size:.62rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.45px;color:var(--bs-surface-500,#64748b);
            background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
            white-space:nowrap;position:sticky;top:0;z-index:2;text-align:center;
        }
        .dr-matrix thead th:first-child { text-align:left;min-width:140px }
        .dr-matrix tbody td { padding:.45rem .55rem;border-bottom:1px solid rgba(0,0,0,.035);vertical-align:middle;text-align:center;font-weight:600;font-variant-numeric:tabular-nums }
        .dr-matrix tbody td:first-child { text-align:left;font-weight:700;color:var(--bs-surface-800,#1e293b) }
        .dr-matrix tbody tr:hover td { background:rgba(212,175,55,.04) }
        .dr-matrix tfoot td { padding:.5rem .55rem;border-top:2px solid rgba(0,0,0,.08);font-weight:800;text-align:center;font-variant-numeric:tabular-nums }
        .dr-matrix tfoot td:first-child { text-align:left }

        .dr-zero { color:var(--bs-surface-300,#cbd5e1) }

        /* Filter bar */
        .dr-filter-bar { display:flex;flex-wrap:wrap;align-items:flex-end;gap:.5rem;margin-bottom:.7rem }
        .dr-filter-group { display:flex;flex-direction:column;gap:.15rem }
        .dr-filter-lbl { font-size:.62rem;font-weight:700;color:var(--bs-surface-500);text-transform:uppercase;letter-spacing:.4px }
        .dr-filter-ctrl { font-size:.72rem;padding:.3rem .55rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:var(--bs-card-bg);color:var(--bs-surface-700);min-width:140px }
        .dr-filter-ctrl:focus { outline:none;border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12) }

        /* Trigger split badges */
        .tr-split { display:flex;flex-direction:column;gap:.12rem;align-items:center }
        .tr-badge { font-size:.55rem;font-weight:700;padding:.07rem .35rem;border-radius:6px;white-space:nowrap }
        .tr-end  { background:rgba(220,38,38,.08);color:#dc2626 }
        .tr-save { background:rgba(5,150,105,.08);color:#059669 }

        /* Disp pill (small) */
        .dh-pill { font-size:.6rem;font-weight:700;padding:.1rem .38rem;border-radius:8px;white-space:nowrap }
        .dh-am   { background:rgba(99,102,241,.1);color:#6366f1 }
        .dh-busy { background:rgba(245,158,11,.1);color:#d97706 }
        .dh-dair { background:rgba(100,116,139,.1);color:#475569 }
        .dh-dc   { background:rgba(239,68,68,.1);color:#dc2626 }
        .dh-dec  { background:rgba(220,38,38,.12);color:#b91c1c }
        .dh-dnc  { background:rgba(124,58,237,.1);color:#7c3aed }
        .dh-n    { background:rgba(180,83,9,.08);color:#b45309 }
        .dh-ni   { background:rgba(3,105,161,.1);color:#0369a1 }
        .dh-np   { background:rgba(6,95,70,.1);color:#065f46 }
        .dh-bn   { background:rgba(8,145,178,.1);color:#0891b2 }
        .dh-nnis { background:rgba(159,18,57,.1);color:#9f1239 }
        .dh-cb   { background:rgba(5,150,105,.1);color:#059669 }
        .dh-upd  { background:rgba(100,116,139,.1);color:#475569 }

        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .dr-matrix thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;border-bottom-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .dr-matrix tbody td {
            color:#e2e8f0;border-bottom-color:rgba(255,255,255,.04);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .dr-matrix tfoot td {
            color:#e2e8f0;border-top-color:rgba(255,255,255,.1);
        }
    </style>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-phone-call"></i> Dialer Report
            <span class="rp-sub">Per-Closer breakdown &bull; End Call &amp; Save &amp; Exit</span>
        </h5>
        <a href="{{ route('settings.reports.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>

    {{-- Filters --}}
    <div class="ex-card sec-card" style="margin-bottom:.7rem">
        <div class="sec-body" style="padding:.75rem">
            <form method="GET" action="{{ route('settings.reports.disposition-report') }}" class="dr-filter-bar">
                <div class="dr-filter-group">
                    <span class="dr-filter-lbl">Period</span>
                    <div style="display:flex;gap:.3rem;flex-wrap:wrap">
                        <a href="{{ route('settings.reports.disposition-report', array_merge(request()->except('filter','start_date','end_date'), ['filter' => 'today'])) }}"
                           class="pipe-pill {{ ($filter ?? 'today') === 'today' ? 'active' : '' }}">
                            <i class="bx bx-calendar"></i> Today
                        </a>
                        <a href="{{ route('settings.reports.disposition-report', array_merge(request()->except('filter','start_date','end_date'), ['filter' => 'week'])) }}"
                           class="pipe-pill {{ ($filter ?? '') === 'week' ? 'active' : '' }}">
                            <i class="bx bx-calendar-week"></i> This Week
                        </a>
                        <a href="{{ route('settings.reports.disposition-report', array_merge(request()->except('filter','start_date','end_date'), ['filter' => 'month'])) }}"
                           class="pipe-pill {{ ($filter ?? '') === 'month' ? 'active' : '' }}">
                            <i class="bx bx-calendar-alt"></i> This Month
                        </a>
                    </div>
                </div>

                <div class="dr-filter-group">
                    <span class="dr-filter-lbl">Custom From</span>
                    <input type="date" name="start_date" class="dr-filter-ctrl"
                           value="{{ $customStart ?? '' }}" style="min-width:120px">
                </div>
                <div class="dr-filter-group">
                    <span class="dr-filter-lbl">To</span>
                    <input type="date" name="end_date" class="dr-filter-ctrl"
                           value="{{ $customEnd ?? '' }}" style="min-width:120px">
                </div>
                @if(!empty($customStart) || !empty($customEnd))
                    <input type="hidden" name="filter" value="custom">
                @endif

                <div class="dr-filter-group">
                    <span class="dr-filter-lbl">Closer</span>
                    <select name="closer" class="dr-filter-ctrl">
                        <option value="">All Closers</option>
                        @foreach($closersList as $c)
                            <option value="{{ $c->id }}" {{ ($closerFilter ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="dr-filter-group">
                    <span class="dr-filter-lbl">Disposition</span>
                    <select name="disposition" class="dr-filter-ctrl">
                        <option value="">All</option>
                        <option value="answering_machine" {{ ($dispositionFilter ?? '') === 'answering_machine' ? 'selected' : '' }}>A — Answering Machine</option>
                        <option value="busy"              {{ ($dispositionFilter ?? '') === 'busy'              ? 'selected' : '' }}>B — Busy</option>
                        <option value="dead_air"          {{ ($dispositionFilter ?? '') === 'dead_air'          ? 'selected' : '' }}>DAIR — Dead Air</option>
                        <option value="disconnected"      {{ ($dispositionFilter ?? '') === 'disconnected'      ? 'selected' : '' }}>DC — Disconnected</option>
                        <option value="declined_sale"     {{ ($dispositionFilter ?? '') === 'declined_sale'     ? 'selected' : '' }}>DEC — Declined Sale</option>
                        <option value="dnc"               {{ ($dispositionFilter ?? '') === 'dnc'               ? 'selected' : '' }}>DNC</option>
                        <option value="no_answer_ec"      {{ ($dispositionFilter ?? '') === 'no_answer_ec'      ? 'selected' : '' }}>N — No Answer</option>
                        <option value="not_interested"    {{ ($dispositionFilter ?? '') === 'not_interested'    ? 'selected' : '' }}>NI — Not Interested</option>
                        <option value="no_pitch"          {{ ($dispositionFilter ?? '') === 'no_pitch'          ? 'selected' : '' }}>NP — No Pitch</option>
                        <option value="business_number"   {{ ($dispositionFilter ?? '') === 'business_number'   ? 'selected' : '' }}>BN — Business Number</option>
                        <option value="not_in_service"    {{ ($dispositionFilter ?? '') === 'not_in_service'    ? 'selected' : '' }}>NNIS — Number Not In Service</option>
                        <option value="callback_set"      {{ ($dispositionFilter ?? '') === 'callback_set'      ? 'selected' : '' }}>Callback Set</option>
                        <option value="updated_data"      {{ ($dispositionFilter ?? '') === 'updated_data'      ? 'selected' : '' }}>Updated Data</option>
                    </select>
                </div>

                <div class="dr-filter-group">
                    <span class="dr-filter-lbl">Trigger</span>
                    <select name="trigger" class="dr-filter-ctrl" style="min-width:110px">
                        <option value="">All Triggers</option>
                        <option value="end_call"  {{ ($triggerFilter ?? '') === 'end_call'  ? 'selected' : '' }}>End Call</option>
                        <option value="save_exit" {{ ($triggerFilter ?? '') === 'save_exit' ? 'selected' : '' }}>Save &amp; Exit</option>
                    </select>
                </div>

                <div class="dr-filter-group">
                    <span class="dr-filter-lbl">Team</span>
                    <select name="team" class="dr-filter-ctrl" style="min-width:110px">
                        <option value="">All Teams</option>
                        <option value="peregrine" {{ ($teamFilter ?? '') === 'peregrine' ? 'selected' : '' }}>Peregrine</option>
                        <option value="ravens"    {{ ($teamFilter ?? '') === 'ravens'    ? 'selected' : '' }}>Ravens</option>
                    </select>
                </div>

                <div class="dr-filter-group" style="justify-content:flex-end">
                    <span class="dr-filter-lbl">&nbsp;</span>
                    <button type="submit" class="act-btn a-primary" style="font-size:.72rem;padding:.32rem .7rem;height:fit-content">
                        <i class="bx bx-search"></i> Apply
                    </button>
                </div>

                @if(!empty($closerFilter) || !empty($dispositionFilter) || !empty($triggerFilter) || (!empty($customStart) && !empty($customEnd)))
                    <div class="dr-filter-group" style="justify-content:flex-end">
                        <span class="dr-filter-lbl">&nbsp;</span>
                        <a href="{{ route('settings.reports.disposition-report') }}" class="act-btn" style="font-size:.72rem;padding:.32rem .7rem;height:fit-content;background:rgba(0,0,0,.04)">
                            <i class="bx bx-x"></i> Clear
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Overall KPIs --}}
    <div class="dr-kpi-row">
        <div class="dr-kpi" style="border-color:rgba(99,102,241,.25);flex:0 0 auto;min-width:80px">
            <div class="dr-kpi-val" style="color:#6366f1">{{ $totalCalls }}</div>
            <div class="dr-kpi-lbl">Total Calls</div>
        </div>
        <div class="dr-kpi" title="Sales closed in this period" style="border-color:rgba(34,197,94,.28);flex:0 0 auto;min-width:80px;cursor:default">
            <div class="dr-kpi-val" style="color:#16a34a">{{ $totalSales }}</div>
            <div class="dr-kpi-lbl">Sales</div>
        </div>
        <div class="dr-kpi" title="A — Answering Machine" style="border-color:rgba(99,102,241,.15);cursor:default">
            <div class="dr-kpi-val" style="color:#6366f1">{{ $dispoCounts['answering_machine'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">A</div>
        </div>
        <div class="dr-kpi" title="B — Busy" style="border-color:rgba(245,158,11,.18);cursor:default">
            <div class="dr-kpi-val" style="color:#d97706">{{ $dispoCounts['busy'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">B</div>
        </div>
        <div class="dr-kpi" title="DAIR — Dead Air" style="border-color:rgba(100,116,139,.18);cursor:default">
            <div class="dr-kpi-val" style="color:#475569">{{ $dispoCounts['dead_air'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">DAIR</div>
        </div>
        <div class="dr-kpi" title="DC — Disconnected Number" style="border-color:rgba(239,68,68,.18);cursor:default">
            <div class="dr-kpi-val" style="color:#dc2626">{{ $dispoCounts['disconnected'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">DC</div>
        </div>
        <div class="dr-kpi" title="DEC — Declined Sale" style="border-color:rgba(220,38,38,.22);cursor:default">
            <div class="dr-kpi-val" style="color:#b91c1c">{{ $dispoCounts['declined_sale'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">DEC</div>
        </div>
        <div class="dr-kpi" title="DNC — Do Not Call" style="border-color:rgba(124,58,237,.18);cursor:default">
            <div class="dr-kpi-val" style="color:#7c3aed">{{ $dispoCounts['dnc'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">DNC</div>
        </div>
        <div class="dr-kpi" title="N — No Answer" style="border-color:rgba(180,83,9,.15);cursor:default">
            <div class="dr-kpi-val" style="color:#b45309">{{ $dispoCounts['no_answer_ec'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">N</div>
        </div>
        <div class="dr-kpi" title="NI — Not Interested" style="border-color:rgba(3,105,161,.18);cursor:default">
            <div class="dr-kpi-val" style="color:#0369a1">{{ $dispoCounts['not_interested'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">NI</div>
        </div>
        <div class="dr-kpi" title="NP — No Pitch No Price" style="border-color:rgba(6,95,70,.15);cursor:default">
            <div class="dr-kpi-val" style="color:#065f46">{{ $dispoCounts['no_pitch'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">NP</div>
        </div>
        <div class="dr-kpi" title="CB — Callback Set" style="border-color:rgba(5,150,105,.2);cursor:default">
            <div class="dr-kpi-val" style="color:#059669">{{ $dispoCounts['callback_set'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">CB</div>
        </div>
        <div class="dr-kpi" title="BN — Business Number" style="border-color:rgba(8,145,178,.18);cursor:default">
            <div class="dr-kpi-val" style="color:#0891b2">{{ $dispoCounts['business_number'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">BN</div>
        </div>
        <div class="dr-kpi" title="NNIS — Number Not In Service" style="border-color:rgba(159,18,57,.18);cursor:default">
            <div class="dr-kpi-val" style="color:#9f1239">{{ $dispoCounts['not_in_service'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">NNIS</div>
        </div>
        <div class="dr-kpi" title="UPD — Updated Data" style="border-color:rgba(100,116,139,.2);cursor:default">
            <div class="dr-kpi-val" style="color:#475569">{{ $dispoCounts['updated_data'] ?? 0 }}</div>
            <div class="dr-kpi-lbl">UPD</div>
        </div>
    </div>

    {{-- Per-Closer Matrix --}}
    <div class="ex-card sec-card">
        <div style="padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.4rem">
            <h6 style="margin:0;font-size:.82rem;font-weight:700;display:flex;align-items:center;gap:.35rem">
                <i class="bx bx-table" style="color:#d4af37"></i>
                Per-Closer Breakdown
                <span style="font-weight:400;color:var(--bs-surface-500);font-size:.72rem">
                    {{ $startDate->setTimezone('America/Los_Angeles')->format('M d') }}
                    — {{ $endDate->setTimezone('America/Los_Angeles')->format('M d, Y') }} (PT)
                </span>
            </h6>
            <span style="font-size:.7rem;color:var(--bs-surface-400)">{{ $closerRows->count() }} closer(s)</span>
        </div>

        @if($closerRows->isEmpty())
            <div style="text-align:center;padding:3rem 1rem;color:var(--bs-surface-500)">
                <i class="bx bx-phone-off" style="font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25"></i>
                <h6 style="font-size:.85rem;font-weight:700;margin-bottom:.25rem">No disposed calls found</h6>
                <p style="font-size:.72rem">Try adjusting the date range or clearing filters.</p>
            </div>
        @else
            <div class="scroll-tbl" style="max-height:520px">
                <table class="dr-matrix">
                    <thead>
                        <tr>
                            <th style="text-align:left">Closer</th>
                            <th title="Total disposed calls">Total</th>
                            <th title="Sales closed in this period" style="color:#16a34a">Sales</th>
                            <th title="Via End Call button"><span class="tr-badge tr-end">End Call</span></th>
                            <th title="Via Save &amp; Exit button"><span class="tr-badge tr-save">Save &amp; Exit</span></th>
                            <th title="A — Answering Machine"><span class="dh-pill dh-am">A</span></th>
                            <th title="B — Busy"><span class="dh-pill dh-busy">B</span></th>
                            <th title="DAIR — Dead Air"><span class="dh-pill dh-dair">DAIR</span></th>
                            <th title="DC — Disconnected Number"><span class="dh-pill dh-dc">DC</span></th>
                            <th title="DEC — Declined Sale"><span class="dh-pill dh-dec">DEC</span></th>
                            <th title="DNC — Do Not Call"><span class="dh-pill dh-dnc">DNC</span></th>
                            <th title="N — No Answer"><span class="dh-pill dh-n">N</span></th>
                            <th title="NI — Not Interested"><span class="dh-pill dh-ni">NI</span></th>
                            <th title="NP — No Pitch No Price"><span class="dh-pill dh-np">NP</span></th>
                            <th title="BN — Business Number"><span class="dh-pill dh-bn">BN</span></th>
                            <th title="NNIS — Number Not In Service"><span class="dh-pill dh-nnis">NNIS</span></th>
                            <th title="CB — Callback Set"><span class="dh-pill dh-cb">CB</span></th>
                            <th title="UPD — Updated Data"><span class="dh-pill dh-upd">Upd</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($closerRows as $row)
                            @php
                                $d = $row['dispositions'];
                                $closerTeam = isset($row['id']) ? ($userTeamMap[$row['id']] ?? null) : null;
                            @endphp
                            <tr>
                                <td>
                                    {{ $row['name'] }}
                                    @if($closerTeam === 'peregrine')
                                        <span class="badge bg-purple" title="Peregrine" style="font-size:.55rem;padding:.08rem .3rem;margin-left:.2rem;vertical-align:middle">P</span>
                                    @elseif($closerTeam === 'ravens')
                                        <span class="badge bg-dark" title="Ravens" style="font-size:.55rem;padding:.08rem .3rem;margin-left:.2rem;vertical-align:middle">R</span>
                                    @endif
                                </td>
                                <td style="font-weight:800;color:#6366f1">{{ $row['total'] }}</td>
                                <td>
                                    @if(($row['sales'] ?? 0) > 0)
                                        <span style="font-weight:800;color:#16a34a">{{ $row['sales'] }}</span>
                                    @else
                                        <span class="dr-zero">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row['end_call'] > 0)
                                        <span class="tr-badge tr-end">{{ $row['end_call'] }}</span>
                                    @else
                                        <span class="dr-zero">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row['save_exit'] > 0)
                                        <span class="tr-badge tr-save">{{ $row['save_exit'] }}</span>
                                    @else
                                        <span class="dr-zero">—</span>
                                    @endif
                                </td>
                                @foreach(['answering_machine','busy','dead_air','disconnected','declined_sale','dnc','no_answer_ec','not_interested','no_pitch','business_number','not_in_service','callback_set','updated_data'] as $dkey)
                                    <td>
                                        @if(($d[$dkey] ?? 0) > 0)
                                            {{ $d[$dkey] }}
                                        @else
                                            <span class="dr-zero">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="font-weight:800">Totals</td>
                            <td style="color:#6366f1;font-weight:800">{{ $totalCalls }}</td>
                            <td style="color:#16a34a;font-weight:800">{{ $totalSales }}</td>
                            <td>
                                @php $ecTotal = $closerRows->sum('end_call') @endphp
                                @if($ecTotal > 0)<span class="tr-badge tr-end">{{ $ecTotal }}</span>@else <span class="dr-zero">—</span> @endif
                            </td>
                            <td>
                                @php $seTotal = $closerRows->sum('save_exit') @endphp
                                @if($seTotal > 0)<span class="tr-badge tr-save">{{ $seTotal }}</span>@else <span class="dr-zero">—</span> @endif
                            </td>
                            @foreach(['answering_machine','busy','dead_air','disconnected','declined_sale','dnc','no_answer_ec','not_interested','no_pitch','business_number','not_in_service','callback_set','updated_data'] as $dkey)
                                <td>{{ $dispoCounts[$dkey] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

@endsection

@section('script')
@include('partials.sl-filter-assets')
@endsection
