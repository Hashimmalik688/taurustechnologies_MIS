@extends('layouts.master')

@section('title', 'Live Analytics')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* ═══════════════════════════════════════════════════
   Live Analytics — Team-Split Compact Style
   ═══════════════════════════════════════════════════ */

/* Glass-card base */
.ex-card{background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);transition:box-shadow .2s}
.ex-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.08)}

/* KPI Stat Cards */
.kpi-row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem}
.kpi-card{flex:1 1 80px;min-width:75px;padding:.65rem .6rem;border-radius:.55rem;text-align:center;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.06);transition:transform .15s,box-shadow .15s}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0}
.kpi-card .k-icon{font-size:1rem;margin-bottom:.2rem;display:block;opacity:.7}
.kpi-card .k-val{font-size:1.35rem;font-weight:700;line-height:1}
.kpi-card .k-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem}

.kpi-card.k-gold{background:rgba(212,175,55,.06)}.kpi-card.k-gold::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}.kpi-card.k-gold .k-val,.kpi-card.k-gold .k-icon{color:#b89730}
.kpi-card.k-green{background:rgba(52,195,143,.06)}.kpi-card.k-green::before{background:linear-gradient(90deg,#34c38f,#6eddb8)}.kpi-card.k-green .k-val,.kpi-card.k-green .k-icon{color:#1a8754}
.kpi-card.k-warn{background:rgba(241,180,76,.06)}.kpi-card.k-warn::before{background:linear-gradient(90deg,#f1b44c,#f5cd7e)}.kpi-card.k-warn .k-val,.kpi-card.k-warn .k-icon{color:#b87a14}
.kpi-card.k-red{background:rgba(244,106,106,.06)}.kpi-card.k-red::before{background:linear-gradient(90deg,#f46a6a,#f89b9b)}.kpi-card.k-red .k-val,.kpi-card.k-red .k-icon{color:#c84646}
.kpi-card.k-purple{background:rgba(124,105,239,.06)}.kpi-card.k-purple::before{background:linear-gradient(90deg,#7c69ef,#a899f5)}.kpi-card.k-purple .k-val,.kpi-card.k-purple .k-icon{color:#5b49c7}
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val,.kpi-card.k-blue .k-icon{color:#556ee6}
.kpi-card.k-teal{background:rgba(80,165,241,.06)}.kpi-card.k-teal::before{background:linear-gradient(90deg,#50a5f1,#8cc5f7)}.kpi-card.k-teal .k-val,.kpi-card.k-teal .k-icon{color:#2b81c9}

/* Section Cards */
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;display:flex;align-items:center;gap:.3rem}
.sec-hdr h6 i{opacity:.6;font-size:.95rem}

/* Compact Table */
.ex-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:.75rem}
.ex-tbl thead th{text-transform:uppercase;font-size:.62rem;font-weight:700;letter-spacing:.5px;color:var(--bs-surface-500);padding:.45rem .6rem;border-bottom:1px solid var(--bs-surface-200);white-space:nowrap;background:var(--bs-surface-100);position:sticky;top:0;z-index:1}
.ex-tbl tbody td{padding:.45rem .6rem;border-bottom:1px solid rgba(0,0,0,.03);vertical-align:middle;white-space:nowrap}
.ex-tbl tbody tr{transition:background .12s}
.ex-tbl tbody tr:hover{background:rgba(212,175,55,.03)}
.ex-tbl tfoot td,.ex-tbl tfoot th{padding:.5rem .6rem;font-weight:700;font-size:.72rem;border-top:2px solid var(--bs-surface-200);background:var(--bs-surface-100)}

/* Value badge in table */
.v-badge{font-size:.72rem;font-weight:700;padding:.2rem .45rem;border-radius:.3rem;display:inline-block;min-width:26px;text-align:center}
.v-badge.v-blue{background:rgba(85,110,230,.1);color:#556ee6}
.v-badge.v-green{background:rgba(52,195,143,.1);color:#1a8754}
.v-badge.v-red{background:rgba(244,106,106,.1);color:#c84646}
.v-badge.v-warn{background:rgba(241,180,76,.1);color:#b87a14}
.v-badge.v-teal{background:rgba(80,165,241,.1);color:#2b81c9}
.v-badge.v-gray{background:rgba(108,117,125,.08);color:#6c757d}
.v-badge.v-purple{background:rgba(124,105,239,.1);color:#5b49c7}
.v-badge.v-gold{background:rgba(212,175,55,.1);color:#b89730}

/* Team badge */
.tm-badge{font-size:.55rem;font-weight:700;padding:.12rem .35rem;border-radius:.2rem;text-transform:uppercase;letter-spacing:.3px}
.tm-badge.tm-per{background:rgba(52,195,143,.1);color:#1a8754}
.tm-badge.tm-rav{background:rgba(244,106,106,.1);color:#c84646}

/* Team section header */
.team-section{margin-bottom:.8rem}
.team-hdr{display:flex;align-items:center;gap:.4rem;padding:.45rem .6rem;border-radius:.5rem;margin-bottom:.5rem;font-size:.82rem;font-weight:700}
.team-hdr.t-per{background:linear-gradient(135deg,rgba(52,195,143,.08),rgba(52,195,143,.02));border:1px solid rgba(52,195,143,.15);color:#1a8754}
.team-hdr.t-rav{background:linear-gradient(135deg,rgba(244,106,106,.08),rgba(244,106,106,.02));border:1px solid rgba(244,106,106,.15);color:#c84646}
.team-hdr i{font-size:1.05rem;opacity:.7}

/* Scroll wrapper */
.scroll-tbl{overflow-x:auto;overflow-y:auto;max-height:400px}
.scroll-tbl::-webkit-scrollbar{width:3px;height:3px}
.scroll-tbl::-webkit-scrollbar-thumb{background:var(--bs-surface-300);border-radius:3px}

/* Filter bar */
.filter-bar{display:flex;flex-wrap:wrap;gap:.4rem;padding:.5rem .75rem;align-items:center}
.filter-bar .f-btn{border:1px solid var(--bs-surface-300);border-radius:1rem;padding:.28rem .7rem;font-size:.7rem;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:var(--bs-surface-500)}
.filter-bar .f-btn:hover{border-color:var(--bs-gold,#d4af37);color:var(--bs-gold)}
.filter-bar .f-btn.active{background:var(--bs-gold,#d4af37);border-color:var(--bs-gold);color:#fff}
.filter-bar .f-input{border:1px solid var(--bs-surface-300);border-radius:1rem;padding:.28rem .6rem;font-size:.72rem;background:transparent;color:inherit;outline:none;transition:border-color .15s;width:220px}
.filter-bar .f-input:focus{border-color:var(--bs-gold,#d4af37);box-shadow:0 0 0 2px rgba(212,175,55,.1)}
.filter-bar .refresh-btn{background:rgba(52,195,143,.08);border:1px solid rgba(52,195,143,.2);border-radius:1rem;padding:.28rem .6rem;font-size:.68rem;font-weight:600;color:#1a8754;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:.25rem}
.filter-bar .refresh-btn:hover{background:#1a8754;color:#fff}

/* Two-col grid */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:.65rem}
@media(max-width:992px){.grid-2{grid-template-columns:1fr}}

/* Timestamp footer */
.ts-footer{font-size:.62rem;color:var(--bs-surface-400);display:flex;align-items:center;gap:.5rem}

@media(max-width:768px){
    .kpi-card .k-val{font-size:1.1rem}
    .filter-bar{flex-direction:column}
    .filter-bar .f-input{width:100%}
}
</style>
@endsection

@section('content')
    {{-- Filter Bar --}}
    <div class="ex-card sec-card" style="margin-bottom:.5rem;">
        <div class="filter-bar">
            <button type="button" class="f-btn {{ $filter === 'today' ? 'active' : '' }}" data-filter="today">
                <i class="bx bx-calendar-alt"></i> Today
            </button>
            <button type="button" class="f-btn {{ $filter === 'custom' ? 'active' : '' }}" data-filter="custom" id="customRangeBtn">
                <i class="bx bx-calendar-edit"></i> Custom
            </button>
            <input type="text" id="customDateRange" class="f-input {{ $filter !== 'custom' ? 'd-none' : '' }}" placeholder="Select date range">
            <button type="button" class="refresh-btn" id="refreshBtn">
                <i class="bx bx-refresh"></i> Refresh
            </button>
            <span class="ts-footer" style="margin-left:auto;">
                <i class="bx bx-wifi"></i> Live <span id="countdown" style="font-weight:600;min-width:20px;display:inline-block;">30s</span>
                <span style="margin:0 .3rem;">|</span>
                <i class="bx bx-time-five"></i>
                <span id="last-updated">{{ now('America/Denver')->format('M d, Y h:i A') }} MT</span>
            </span>
        </div>
    </div>

    {{-- Top-Level KPIs --}}
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-trending-up k-icon"></i>
            <div class="k-val" id="kpi-sales">{{ $metrics['sales']['today'] ?? 0 }}</div>
            <div class="k-lbl">Total Sales</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-target-lock k-icon"></i>
            <div class="k-val" id="kpi-per-sales">{{ $metrics['sales']['peregrine_sales'] ?? 0 }}</div>
            <div class="k-lbl">Peregrine</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-meteor k-icon"></i>
            <div class="k-val" id="kpi-rav-sales">{{ $metrics['sales']['ravens_sales'] ?? 0 }}</div>
            <div class="k-lbl">Ravens</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-check-shield k-icon"></i>
            <div class="k-val" id="kpi-verified">{{ $metrics['verifier']['submitted_range'] ?? 0 }}</div>
            <div class="k-lbl">Verified</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-time-five k-icon"></i>
            <div class="k-val" id="kpi-pending">{{ $metrics['manager']['pending'] ?? 0 }}</div>
            <div class="k-lbl">Pending Review</div>
        </div>
        <div class="kpi-card k-purple ex-card">
            <i class="bx bx-shield-quarter k-icon"></i>
            <div class="k-val" id="kpi-qa">{{ $metrics['qa']['reviewed_range'] ?? 0 }}</div>
            <div class="k-lbl">QA Reviewed</div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         PEREGRINE SECTION
    ═══════════════════════════════════════════ --}}
    <div class="team-section">
        <div class="team-hdr t-per">
            <i class="bx bx-check-shield"></i> Verifier Dashboard
            <span style="margin-left:auto;font-size:.6rem;opacity:.7;">Verifier → Closer → Validator → Manager → QA</span>
        </div>

        {{-- Peregrine: Verifier Pipeline & Closer side-by-side --}}
        <div class="grid-2">
            {{-- Verifier Pipeline (Peregrine only) --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-check-shield" style="color:#50a5f1;"></i> Verifier Pipeline</h6>
                    <span style="font-size:.6rem;color:var(--bs-surface-400);" id="per-verifier-count">{{ $verifierPipeline->count() }} active</span>
                </div>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>Verifier</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Disposed</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Sales</th>
                                <th class="text-center">Declined</th>
                            </tr>
                        </thead>
                        <tbody id="per-verifier-tbody">
                            @forelse($verifierPipeline as $v)
                                <tr>
                                    <td><strong>{{ $v['name'] }}</strong></td>
                                    <td class="text-center"><span class="v-badge v-blue">{{ $v['total'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-gray">{{ $v['disposed'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-warn">{{ $v['pending'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-green">{{ $v['sales'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-red">{{ $v['declined'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No verifier activity</td></tr>
                            @endforelse
                        </tbody>
                        @if($verifierPipeline->count() > 0)
                        <tfoot id="per-verifier-tfoot">
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-center"><span class="v-badge v-blue">{{ $verifierPipeline->sum('total') }}</span></td>
                                <td class="text-center"><span class="v-badge v-gray">{{ $verifierPipeline->sum('disposed') }}</span></td>
                                <td class="text-center"><span class="v-badge v-warn">{{ $verifierPipeline->sum('pending') }}</span></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $verifierPipeline->sum('sales') }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $verifierPipeline->sum('declined') }}</span></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Peregrine Closers --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-user-circle" style="color:#d4af37;"></i> Peregrine Closers</h6>
                    <span style="font-size:.6rem;color:var(--bs-surface-400);" id="per-closer-count">{{ $peregrineCloserBreakdown->count() }} active</span>
                </div>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>Closer</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Disposed</th>
                                <th class="text-center">Callbacks</th>
                                <th class="text-center">→ Validator</th>
                                <th class="text-center">Sales</th>
                                <th class="text-center">Declined</th>
                            </tr>
                        </thead>
                        <tbody id="per-closer-tbody">
                            @forelse($peregrineCloserBreakdown as $closer)
                                <tr>
                                    <td><strong>{{ $closer['name'] }}</strong></td>
                                    <td class="text-center"><span class="v-badge v-blue">{{ $closer['total_assigned'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-gray">{{ $closer['disposed'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-warn">{{ $closer['callbacks'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-teal">{{ $closer['sent_to_validator'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-green">{{ $closer['sales'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-red">{{ $closer['declined'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No closer activity</td></tr>
                            @endforelse
                        </tbody>
                        @if($peregrineCloserBreakdown->count() > 0)
                        <tfoot id="per-closer-tfoot">
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-center"><span class="v-badge v-blue">{{ $peregrineCloserBreakdown->sum('total_assigned') }}</span></td>
                                <td class="text-center"><span class="v-badge v-gray">{{ $peregrineCloserBreakdown->sum('disposed') }}</span></td>
                                <td class="text-center"><span class="v-badge v-warn">{{ $peregrineCloserBreakdown->sum('callbacks') }}</span></td>
                                <td class="text-center"><span class="v-badge v-teal">{{ $peregrineCloserBreakdown->sum('sent_to_validator') }}</span></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $peregrineCloserBreakdown->sum('sales') }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $peregrineCloserBreakdown->sum('declined') }}</span></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Verifier Submissions Log --}}
        <div class="ex-card sec-card" style="margin-top:.5rem;">
            <div class="sec-hdr">
                <h6><i class="bx bx-list-ul" style="color:#50a5f1;"></i> Verifier Submissions Log</h6>
                <span style="font-size:.6rem;color:var(--bs-surface-400);" id="per-submissions-count">{{ $verifierSubmissions->count() }} submissions</span>
            </div>
            <div class="scroll-tbl" style="max-height:320px;">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Date / Time</th>
                            <th>Lead Name</th>
                            <th>Phone</th>
                            <th>Verifier</th>
                            <th>Closer</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="per-submissions-tbody">
                        @forelse($verifierSubmissions as $idx => $sub)
                            <tr>
                                <td style="color:var(--bs-surface-400);font-size:.7rem;">{{ $idx + 1 }}</td>
                                <td style="font-size:.75rem;white-space:nowrap;">{{ $sub['submitted_at'] }}</td>
                                <td><strong>{{ $sub['cn_name'] }}</strong></td>
                                <td style="font-size:.75rem;">{{ $sub['phone'] }}</td>
                                <td>{{ $sub['verifier'] }}</td>
                                <td>{{ $sub['closer'] }}</td>
                                <td class="text-center">
                                    @php
                                        $sc = 'v-gray';
                                        if (str_contains($sub['status'], 'Sale')) $sc = 'v-green';
                                        elseif (str_contains($sub['status'], 'Declined')) $sc = 'v-red';
                                        elseif (str_contains($sub['status'], 'Pending')) $sc = 'v-warn';
                                        elseif (str_contains($sub['status'], 'Closer')) $sc = 'v-blue';
                                        elseif (str_contains($sub['status'], 'Validator')) $sc = 'v-teal';
                                        elseif (str_contains($sub['status'], 'Returned')) $sc = 'v-purple';
                                    @endphp
                                    <span class="v-badge {{ $sc }}">{{ $sub['status'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No submissions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Peregrine: Validator & Manager side-by-side --}}
        <div class="grid-2">
            {{-- Peregrine Validators --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-clipboard-check" style="color:#7c69ef;"></i> Validators</h6>
                    <span style="font-size:.6rem;color:var(--bs-surface-400);" id="per-validator-count">{{ $validatorBreakdown->count() }} active</span>
                </div>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>Validator</th>
                                <th class="text-center">Assigned</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Returned</th>
                                <th class="text-center">Declined</th>
                            </tr>
                        </thead>
                        <tbody id="per-validator-tbody">
                            @forelse($validatorBreakdown as $validator)
                                <tr>
                                    <td><strong>{{ $validator['name'] }}</strong></td>
                                    <td class="text-center"><span class="v-badge v-teal">{{ $validator['total_assigned'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-warn">{{ $validator['pending'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-green">{{ $validator['approved'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-gray">{{ $validator['returned'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-red">{{ $validator['declined'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No validator activity</td></tr>
                            @endforelse
                        </tbody>
                        @if($validatorBreakdown->count() > 0)
                        <tfoot id="per-validator-tfoot">
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-center"><span class="v-badge v-teal">{{ $validatorFormMetrics['total_assigned'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-warn">{{ $validatorFormMetrics['pending'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $validatorFormMetrics['approved'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-gray">{{ $validatorFormMetrics['returned'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $validatorFormMetrics['declined'] }}</span></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Peregrine Manager Reviews --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-user-check" style="color:#556ee6;"></i> Manager Reviews</h6>
                    <span class="v-badge v-warn" style="font-size:.55rem;">{{ $metrics['sales']['pending_approval'] ?? 0 }} pending</span>
                </div>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>Manager</th>
                                <th class="text-center">Reviewed</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Declined</th>
                                <th class="text-center">UW</th>
                                <th class="text-center">CB</th>
                            </tr>
                        </thead>
                        <tbody id="per-manager-tbody">
                            @forelse($peregrineManagerBreakdown as $mgr)
                                <tr>
                                    <td><strong>{{ $mgr['name'] }}</strong></td>
                                    <td class="text-center"><span class="v-badge v-blue">{{ $mgr['total_reviewed'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-green">{{ $mgr['approved'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-red">{{ $mgr['declined'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-purple">{{ $mgr['underwriting'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-gray">{{ $mgr['chargeback'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No manager reviews</td></tr>
                            @endforelse
                        </tbody>
                        @if($peregrineManagerBreakdown->count() > 0)
                        <tfoot id="per-manager-tfoot">
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-center"><span class="v-badge v-blue">{{ $peregrineManagerBreakdown->sum('total_reviewed') }}</span></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $peregrineManagerBreakdown->sum('approved') }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $peregrineManagerBreakdown->sum('declined') }}</span></td>
                                <td class="text-center"><span class="v-badge v-purple">{{ $peregrineManagerBreakdown->sum('underwriting') }}</span></td>
                                <td class="text-center"><span class="v-badge v-gray">{{ $peregrineManagerBreakdown->sum('chargeback') }}</span></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Peregrine QA (full width) --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-shield-quarter" style="color:#34c38f;"></i> Quality Assurance</h6>
                <span class="v-badge v-warn" style="font-size:.55rem;">{{ $metrics['qa']['pending'] ?? 0 }} pending</span>
            </div>
            <div class="scroll-tbl">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>QA Reviewer</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Good</th>
                            <th class="text-center">Avg</th>
                            <th class="text-center">Bad</th>
                        </tr>
                    </thead>
                    <tbody id="per-qa-tbody">
                        @forelse($peregrineQABreakdown as $qa)
                            <tr>
                                <td><strong>{{ $qa['name'] }}</strong></td>
                                <td class="text-center"><span class="v-badge v-warn">{{ $qa['pending'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $qa['good'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-purple">{{ $qa['avg'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $qa['bad'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No QA reviews</td></tr>
                        @endforelse
                    </tbody>
                    @if($peregrineQABreakdown->count() > 0)
                    <tfoot id="per-qa-tfoot">
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="text-center"><span class="v-badge v-warn">{{ $peregrineQABreakdown->sum('pending') }}</span></td>
                            <td class="text-center"><span class="v-badge v-green">{{ $peregrineQABreakdown->sum('good') }}</span></td>
                            <td class="text-center"><span class="v-badge v-purple">{{ $peregrineQABreakdown->sum('avg') }}</span></td>
                            <td class="text-center"><span class="v-badge v-red">{{ $peregrineQABreakdown->sum('bad') }}</span></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         RAVENS SECTION
    ═══════════════════════════════════════════ --}}
    <div class="team-section">
        <div class="team-hdr t-rav">
            <i class="bx bx-meteor"></i> Ravens Pipeline
            <span style="margin-left:auto;font-size:.6rem;opacity:.7;">Closer → QA → Manager</span>
        </div>

        {{-- Ravens Closers (full width — sales + manager status breakdown) --}}
        <div class="ex-card sec-card">
            <div class="sec-hdr">
                <h6><i class="bx bx-user-circle" style="color:#d4af37;"></i> Ravens Closers</h6>
                <span style="font-size:.6rem;color:var(--bs-surface-400);" id="rav-closer-count">{{ $ravensCloserBreakdown->count() }} active</span>
            </div>
            <div class="scroll-tbl">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>Closer</th>
                            <th class="text-center">Sales</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Approved</th>
                            <th class="text-center">Declined</th>
                            <th class="text-center">UW</th>
                            <th class="text-center">CB</th>
                        </tr>
                    </thead>
                    <tbody id="rav-closer-tbody">
                        @forelse($ravensCloserBreakdown as $closer)
                            <tr>
                                <td><strong>{{ $closer['name'] }}</strong></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $closer['sales'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-warn">{{ $closer['mgr_pending'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-blue">{{ $closer['mgr_approved'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $closer['mgr_declined'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-purple">{{ $closer['mgr_underwriting'] }}</span></td>
                                <td class="text-center"><span class="v-badge v-gray">{{ $closer['mgr_chargeback'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No closer activity</td></tr>
                        @endforelse
                    </tbody>
                    @if($ravensCloserBreakdown->count() > 0)
                    <tfoot id="rav-closer-tfoot">
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="text-center"><span class="v-badge v-green">{{ $ravensCloserBreakdown->sum('sales') }}</span></td>
                            <td class="text-center"><span class="v-badge v-warn">{{ $ravensCloserBreakdown->sum('mgr_pending') }}</span></td>
                            <td class="text-center"><span class="v-badge v-blue">{{ $ravensCloserBreakdown->sum('mgr_approved') }}</span></td>
                            <td class="text-center"><span class="v-badge v-red">{{ $ravensCloserBreakdown->sum('mgr_declined') }}</span></td>
                            <td class="text-center"><span class="v-badge v-purple">{{ $ravensCloserBreakdown->sum('mgr_underwriting') }}</span></td>
                            <td class="text-center"><span class="v-badge v-gray">{{ $ravensCloserBreakdown->sum('mgr_chargeback') }}</span></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Ravens: QA & Manager side-by-side (QA FIRST per user request) --}}
        <div class="grid-2">
            {{-- Ravens QA --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-shield-quarter" style="color:#34c38f;"></i> Quality Assurance</h6>
                </div>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>QA Reviewer</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Good</th>
                                <th class="text-center">Avg</th>
                                <th class="text-center">Bad</th>
                            </tr>
                        </thead>
                        <tbody id="rav-qa-tbody">
                            @forelse($ravensQABreakdown as $qa)
                                <tr>
                                    <td><strong>{{ $qa['name'] }}</strong></td>
                                    <td class="text-center"><span class="v-badge v-warn">{{ $qa['pending'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-green">{{ $qa['good'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-purple">{{ $qa['avg'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-red">{{ $qa['bad'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No QA reviews</td></tr>
                            @endforelse
                        </tbody>
                        @if($ravensQABreakdown->count() > 0)
                        <tfoot id="rav-qa-tfoot">
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-center"><span class="v-badge v-warn">{{ $ravensQABreakdown->sum('pending') }}</span></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $ravensQABreakdown->sum('good') }}</span></td>
                                <td class="text-center"><span class="v-badge v-purple">{{ $ravensQABreakdown->sum('avg') }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $ravensQABreakdown->sum('bad') }}</span></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Ravens Manager Reviews --}}
            <div class="ex-card sec-card">
                <div class="sec-hdr">
                    <h6><i class="bx bx-user-check" style="color:#556ee6;"></i> Manager Reviews</h6>
                </div>
                <div class="scroll-tbl">
                    <table class="ex-tbl">
                        <thead>
                            <tr>
                                <th>Manager</th>
                                <th class="text-center">Reviewed</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Declined</th>
                                <th class="text-center">UW</th>
                                <th class="text-center">CB</th>
                            </tr>
                        </thead>
                        <tbody id="rav-manager-tbody">
                            @forelse($ravensManagerBreakdown as $mgr)
                                <tr>
                                    <td><strong>{{ $mgr['name'] }}</strong></td>
                                    <td class="text-center"><span class="v-badge v-blue">{{ $mgr['total_reviewed'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-green">{{ $mgr['approved'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-red">{{ $mgr['declined'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-purple">{{ $mgr['underwriting'] }}</span></td>
                                    <td class="text-center"><span class="v-badge v-gray">{{ $mgr['chargeback'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No manager reviews</td></tr>
                            @endforelse
                        </tbody>
                        @if($ravensManagerBreakdown->count() > 0)
                        <tfoot id="rav-manager-tfoot">
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-center"><span class="v-badge v-blue">{{ $ravensManagerBreakdown->sum('total_reviewed') }}</span></td>
                                <td class="text-center"><span class="v-badge v-green">{{ $ravensManagerBreakdown->sum('approved') }}</span></td>
                                <td class="text-center"><span class="v-badge v-red">{{ $ravensManagerBreakdown->sum('declined') }}</span></td>
                                <td class="text-center"><span class="v-badge v-purple">{{ $ravensManagerBreakdown->sum('underwriting') }}</span></td>
                                <td class="text-center"><span class="v-badge v-gray">{{ $ravensManagerBreakdown->sum('chargeback') }}</span></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
let currentFilter = '{{ $filter }}';
let countdownInterval = null;
let secondsLeft = 30;
const REFRESH_SECONDS = 30;

const dateRangePicker = flatpickr("#customDateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    onChange: function(selectedDates) {
        if (selectedDates.length === 2) {
            currentFilter = 'custom';
            updateFilters();
        }
    }
});

@if($filter === 'custom' && $startDate && $endDate)
    dateRangePicker.setDate(['{!! $startDate !!}', '{!! $endDate !!}']);
    document.getElementById('customDateRange').classList.remove('d-none');
@endif

document.querySelectorAll('.filter-bar .f-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        document.querySelectorAll('.filter-bar .f-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = filter;
        if (filter === 'custom') {
            document.getElementById('customDateRange').classList.remove('d-none');
            dateRangePicker.open();
            stopAutoRefresh();
        } else {
            document.getElementById('customDateRange').classList.add('d-none');
            updateFilters();
        }
    });
});

document.getElementById('refreshBtn').addEventListener('click', () => fetchLiveData());

function updateFilters() {
    const params = new URLSearchParams();
    params.append('filter', currentFilter);
    if (currentFilter === 'custom' && dateRangePicker.selectedDates.length === 2) {
        params.append('start_date', dateRangePicker.selectedDates[0].toISOString().split('T')[0]);
        params.append('end_date', dateRangePicker.selectedDates[1].toISOString().split('T')[0]);
    } else if (currentFilter === 'custom') {
        alert('Please select a date range');
        return;
    }
    window.location.href = '{{ route("analytics.live") }}?' + params.toString();
}

// ── Helpers ──
function getFilterParams() {
    const params = new URLSearchParams();
    params.append('filter', currentFilter);
    if (currentFilter === 'custom' && dateRangePicker.selectedDates.length === 2) {
        params.append('start_date', dateRangePicker.selectedDates[0].toISOString().split('T')[0]);
        params.append('end_date', dateRangePicker.selectedDates[1].toISOString().split('T')[0]);
    }
    return params.toString();
}

function badge(cls, val) { return '<span class="v-badge ' + cls + '">' + val + '</span>'; }
function sum(arr, key) { return arr.reduce(function(s, r) { return s + (r[key] || 0); }, 0); }
function cvBadge(rate) {
    var cls = rate >= 50 ? 'v-green' : (rate >= 30 ? 'v-warn' : 'v-red');
    return badge(cls, rate + '%');
}

function emptyRow(cols, msg) {
    return '<tr><td colspan="' + cols + '" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> ' + msg + '</td></tr>';
}

// ── Generic Table Updater ──
function updateTable(tbodyId, data, rowFn, tfootId, totalsFn, extra) {
    var tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    if (!data || data.length === 0) {
        var cols = tbody.closest('table').querySelectorAll('thead th').length;
        tbody.innerHTML = emptyRow(cols, 'No data for selected period');
        if (tfootId) { var tf = document.getElementById(tfootId); if (tf) tf.innerHTML = ''; }
        return;
    }
    tbody.innerHTML = data.map(rowFn).join('');
    if (tfootId && totalsFn) {
        var tfoot = document.getElementById(tfootId);
        if (!tfoot) {
            tfoot = document.createElement('tfoot');
            tfoot.id = tfootId;
            tbody.closest('table').appendChild(tfoot);
        }
        tfoot.innerHTML = totalsFn(data, extra);
    }
}

// ── KPI updater ──
function updateKPIs(m) {
    var map = {
        'kpi-sales': m.sales ? (m.sales.today || 0) : 0,
        'kpi-per-sales': m.sales ? (m.sales.peregrine_sales || 0) : 0,
        'kpi-rav-sales': m.sales ? (m.sales.ravens_sales || 0) : 0,
        'kpi-verified': m.verifier ? (m.verifier.submitted_range || 0) : 0,
        'kpi-pending': m.manager ? (m.manager.pending || 0) : 0,
        'kpi-qa': m.qa ? (m.qa.reviewed_range || 0) : 0
    };
    for (var id in map) {
        var el = document.getElementById(id);
        if (el && el.textContent != map[id]) {
            el.textContent = map[id];
            el.style.transition = 'color .3s';
            el.style.color = '#d4af37';
            (function(e) { setTimeout(function() { e.style.color = ''; }, 800); })(el);
        }
    }
}

// ── Row Renderers ──

// Verifier Pipeline
function renderVerifierPipelineRow(r) {
    return '<tr><td><strong>' + r.name + '</strong></td><td class="text-center">' + badge('v-blue',r.total) + '</td><td class="text-center">' + badge('v-gray',r.disposed) + '</td><td class="text-center">' + badge('v-warn',r.pending) + '</td><td class="text-center">' + badge('v-green',r.sales) + '</td><td class="text-center">' + badge('v-red',r.declined) + '</td></tr>';
}
function renderVerifierPipelineTotals(d) {
    return '<tr><td><strong>Total</strong></td><td class="text-center">' + badge('v-blue',sum(d,'total')) + '</td><td class="text-center">' + badge('v-gray',sum(d,'disposed')) + '</td><td class="text-center">' + badge('v-warn',sum(d,'pending')) + '</td><td class="text-center">' + badge('v-green',sum(d,'sales')) + '</td><td class="text-center">' + badge('v-red',sum(d,'declined')) + '</td></tr>';
}

// Peregrine Closer
function renderPeregrineCloserRow(r) {
    return '<tr><td><strong>' + r.name + '</strong></td><td class="text-center">' + badge('v-blue',r.total_assigned) + '</td><td class="text-center">' + badge('v-gray',r.disposed) + '</td><td class="text-center">' + badge('v-warn',r.callbacks) + '</td><td class="text-center">' + badge('v-teal',r.sent_to_validator) + '</td><td class="text-center">' + badge('v-green',r.sales) + '</td><td class="text-center">' + badge('v-red',r.declined) + '</td></tr>';
}
function renderPeregrineCloserTotals(d) {
    return '<tr><td><strong>Total</strong></td><td class="text-center">' + badge('v-blue',sum(d,'total_assigned')) + '</td><td class="text-center">' + badge('v-gray',sum(d,'disposed')) + '</td><td class="text-center">' + badge('v-warn',sum(d,'callbacks')) + '</td><td class="text-center">' + badge('v-teal',sum(d,'sent_to_validator')) + '</td><td class="text-center">' + badge('v-green',sum(d,'sales')) + '</td><td class="text-center">' + badge('v-red',sum(d,'declined')) + '</td></tr>';
}

// Verifier Submissions Log
function getSubmissionStatusClass(s) {
    if (s.indexOf('Sale') >= 0) return 'v-green';
    if (s.indexOf('Declined') >= 0) return 'v-red';
    if (s.indexOf('Pending') >= 0) return 'v-warn';
    if (s.indexOf('Closer') >= 0) return 'v-blue';
    if (s.indexOf('Validator') >= 0) return 'v-teal';
    if (s.indexOf('Returned') >= 0) return 'v-purple';
    return 'v-gray';
}
function renderSubmissionsLog(data) {
    var tbody = document.getElementById('per-submissions-tbody');
    if (!tbody) return;
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-3" style="color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No submissions yet</td></tr>';
        return;
    }
    var html = '';
    for (var i = 0; i < data.length; i++) {
        var r = data[i];
        html += '<tr><td style="color:var(--bs-surface-400);font-size:.7rem;">' + (i+1) + '</td><td style="font-size:.75rem;white-space:nowrap;">' + r.submitted_at + '</td><td><strong>' + r.cn_name + '</strong></td><td style="font-size:.75rem;">' + r.phone + '</td><td>' + r.verifier + '</td><td>' + r.closer + '</td><td class="text-center">' + badge(getSubmissionStatusClass(r.status), r.status) + '</td></tr>';
    }
    tbody.innerHTML = html;
}

// Ravens Closer
function renderRavensCloserRow(r) {
    return '<tr><td><strong>' + r.name + '</strong></td><td class="text-center">' + badge('v-green',r.sales) + '</td><td class="text-center">' + badge('v-warn',r.mgr_pending) + '</td><td class="text-center">' + badge('v-blue',r.mgr_approved) + '</td><td class="text-center">' + badge('v-red',r.mgr_declined) + '</td><td class="text-center">' + badge('v-purple',r.mgr_underwriting) + '</td><td class="text-center">' + badge('v-gray',r.mgr_chargeback) + '</td></tr>';
}
function renderRavensCloserTotals(d) {
    return '<tr><td><strong>Total</strong></td><td class="text-center">' + badge('v-green',sum(d,'sales')) + '</td><td class="text-center">' + badge('v-warn',sum(d,'mgr_pending')) + '</td><td class="text-center">' + badge('v-blue',sum(d,'mgr_approved')) + '</td><td class="text-center">' + badge('v-red',sum(d,'mgr_declined')) + '</td><td class="text-center">' + badge('v-purple',sum(d,'mgr_underwriting')) + '</td><td class="text-center">' + badge('v-gray',sum(d,'mgr_chargeback')) + '</td></tr>';
}

// Validator
function renderValidatorRow(r) {
    return '<tr><td><strong>' + r.name + '</strong></td><td class="text-center">' + badge('v-teal',r.total_assigned) + '</td><td class="text-center">' + badge('v-warn',r.pending) + '</td><td class="text-center">' + badge('v-green',r.approved) + '</td><td class="text-center">' + badge('v-gray',r.returned) + '</td><td class="text-center">' + badge('v-red',r.declined) + '</td></tr>';
}
function renderValidatorTotals(d, fm) {
    if (fm) return '<tr><td><strong>Total</strong></td><td class="text-center">' + badge('v-teal',fm.total_assigned) + '</td><td class="text-center">' + badge('v-warn',fm.pending) + '</td><td class="text-center">' + badge('v-green',fm.approved) + '</td><td class="text-center">' + badge('v-gray',fm.returned) + '</td><td class="text-center">' + badge('v-red',fm.declined) + '</td></tr>';
    return '<tr><td><strong>Total</strong></td><td class="text-center">' + badge('v-teal',sum(d,'total_assigned')) + '</td><td class="text-center">' + badge('v-warn',sum(d,'pending')) + '</td><td class="text-center">' + badge('v-green',sum(d,'approved')) + '</td><td class="text-center">' + badge('v-gray',sum(d,'returned')) + '</td><td class="text-center">' + badge('v-red',sum(d,'declined')) + '</td></tr>';
}

// Manager
function renderManagerRow(r) {
    return '<tr><td><strong>' + r.name + '</strong></td><td class="text-center">' + badge('v-blue',r.total_reviewed) + '</td><td class="text-center">' + badge('v-green',r.approved) + '</td><td class="text-center">' + badge('v-red',r.declined) + '</td><td class="text-center">' + badge('v-purple',r.underwriting) + '</td><td class="text-center">' + badge('v-gray',r.chargeback) + '</td></tr>';
}
function renderManagerTotals(d) {
    return '<tr><td><strong>Total</strong></td><td class="text-center">' + badge('v-blue',sum(d,'total_reviewed')) + '</td><td class="text-center">' + badge('v-green',sum(d,'approved')) + '</td><td class="text-center">' + badge('v-red',sum(d,'declined')) + '</td><td class="text-center">' + badge('v-purple',sum(d,'underwriting')) + '</td><td class="text-center">' + badge('v-gray',sum(d,'chargeback')) + '</td></tr>';
}

// QA
function renderQARow(r) {
    return '<tr><td><strong>' + r.name + '</strong></td><td class="text-center">' + badge('v-warn',r.pending) + '</td><td class="text-center">' + badge('v-green',r.good) + '</td><td class="text-center">' + badge('v-purple',r.avg) + '</td><td class="text-center">' + badge('v-red',r.bad) + '</td></tr>';
}
function renderQATotals(d) {
    return '<tr><td><strong>Total</strong></td><td class="text-center">' + badge('v-warn',sum(d,'pending')) + '</td><td class="text-center">' + badge('v-green',sum(d,'good')) + '</td><td class="text-center">' + badge('v-purple',sum(d,'avg')) + '</td><td class="text-center">' + badge('v-red',sum(d,'bad')) + '</td></tr>';
}

// ── AJAX Live Fetch ──
function fetchLiveData() {
    var refreshBtn = document.getElementById('refreshBtn');
    refreshBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Updating...';

    fetch('{{ route("analytics.live.data") }}?' + getFilterParams(), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        // KPIs
        updateKPIs(data.metrics);

        // Peregrine tables
        updateTable('per-verifier-tbody', data.verifierPipeline, renderVerifierPipelineRow, 'per-verifier-tfoot', renderVerifierPipelineTotals);
        updateTable('per-closer-tbody', data.peregrineCloserBreakdown, renderPeregrineCloserRow, 'per-closer-tfoot', renderPeregrineCloserTotals);
        updateTable('per-validator-tbody', data.validatorBreakdown, renderValidatorRow, 'per-validator-tfoot', renderValidatorTotals, data.validatorFormMetrics);
        updateTable('per-manager-tbody', data.peregrineManagerBreakdown, renderManagerRow, 'per-manager-tfoot', renderManagerTotals);
        updateTable('per-qa-tbody', data.peregrineQABreakdown, renderQARow, 'per-qa-tfoot', renderQATotals);

        // Verifier submissions log
        renderSubmissionsLog(data.verifierSubmissions);

        // Ravens tables
        updateTable('rav-closer-tbody', data.ravensCloserBreakdown, renderRavensCloserRow, 'rav-closer-tfoot', renderRavensCloserTotals);
        updateTable('rav-manager-tbody', data.ravensManagerBreakdown, renderManagerRow, 'rav-manager-tfoot', renderManagerTotals);
        updateTable('rav-qa-tbody', data.ravensQABreakdown, renderQARow, 'rav-qa-tfoot', renderQATotals);

        // Counts
        var countMap = {
            'per-verifier-count': data.verifierPipeline,
            'per-closer-count': data.peregrineCloserBreakdown,
            'per-validator-count': data.validatorBreakdown,
            'rav-closer-count': data.ravensCloserBreakdown
        };
        for (var cid in countMap) {
            var cel = document.getElementById(cid);
            if (cel) cel.textContent = ((countMap[cid] && countMap[cid].length) || 0) + ' active';
        }
        var subCount = document.getElementById('per-submissions-count');
        if (subCount) subCount.textContent = ((data.verifierSubmissions && data.verifierSubmissions.length) || 0) + ' submissions';

        // Timestamp
        document.getElementById('last-updated').textContent = data.timestamp;
        refreshBtn.innerHTML = '<i class="bx bx-refresh"></i> Refresh';
        secondsLeft = REFRESH_SECONDS;
    })
    .catch(function(err) {
        console.error('Live refresh failed:', err);
        refreshBtn.innerHTML = '<i class="bx bx-refresh"></i> Refresh';
    });
}

// ── Auto-refresh Timer ──
function startAutoRefresh() {
    secondsLeft = REFRESH_SECONDS;
    stopAutoRefresh();
    countdownInterval = setInterval(function() {
        secondsLeft--;
        var cd = document.getElementById('countdown');
        if (cd) cd.textContent = secondsLeft + 's';
        if (secondsLeft <= 0) {
            fetchLiveData();
        }
    }, 1000);
}

function stopAutoRefresh() {
    if (countdownInterval) clearInterval(countdownInterval);
}

startAutoRefresh();
</script>
@endsection
