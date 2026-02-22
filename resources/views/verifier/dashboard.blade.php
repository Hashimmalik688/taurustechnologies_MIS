@extends('layouts.master')

@section('title')
    My Verifications
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
@endsection

@section('content')
    {{-- Bubble-Pill Filter Bar --}}
    <form method="GET" action="{{ route('verifier.dashboard') }}" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="{{ route('verifier.dashboard', ['filter' => 'today']) }}" class="pipe-pill {{ $filter === 'today' ? 'active' : '' }}"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill {{ $filter === 'custom' ? 'active' : '' }}" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:{{ $filter === 'custom' ? 'flex' : 'none' }};align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="date" name="start_date" class="pipe-pill-date" value="{{ request('start_date') }}">
            <span class="pipe-pill-lbl">TO</span>
            <input type="date" name="end_date" class="pipe-pill-date" value="{{ request('end_date') }}">
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        @if($filter !== 'today')
            <a href="{{ route('verifier.dashboard', ['filter' => 'today']) }}" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        @endif
    </form>

    {{-- KPI Cards --}}
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-file k-icon"></i>
            <div class="k-val">{{ $todayStats['total_submitted'] ?? 0 }}</div>
            <div class="k-lbl">Total Submitted</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-user-pin k-icon"></i>
            <div class="k-val">{{ $todayStats['with_closer'] ?? 0 }}</div>
            <div class="k-lbl">With Closer</div>
        </div>
        <div class="kpi-card k-purple ex-card">
            <i class="bx bx-clipboard k-icon"></i>
            <div class="k-val">{{ $todayStats['with_validator'] ?? 0 }}</div>
            <div class="k-lbl">With Validator</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-check-double k-icon"></i>
            <div class="k-val">{{ $todayStats['sales'] ?? 0 }}</div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val">{{ $todayStats['declined'] ?? 0 }}</div>
            <div class="k-lbl">Declined</div>
        </div>
    </div>

    {{-- Per-Closer Breakdown --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-user-circle" style="color:#d4af37;"></i> Per-Closer Breakdown</h6>
            <span style="font-size:.6rem;color:var(--bs-surface-400);">{{ $closerBreakdown->count() }} closer(s)</span>
        </div>
        <div class="scroll-tbl">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Closer</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">With Closer</th>
                        <th class="text-center">With Validator</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Sales</th>
                        <th class="text-center">Declined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($closerBreakdown as $cb)
                        <tr>
                            <td><strong>{{ $cb['name'] }}</strong></td>
                            <td class="text-center"><span class="v-badge v-blue">{{ $cb['total'] }}</span></td>
                            <td class="text-center"><span class="v-badge v-teal">{{ $cb['with_closer'] }}</span></td>
                            <td class="text-center"><span class="v-badge v-purple">{{ $cb['with_validator'] }}</span></td>
                            <td class="text-center"><span class="v-badge v-warn">{{ $cb['pending'] }}</span></td>
                            <td class="text-center"><span class="v-badge v-green">{{ $cb['sales'] }}</span></td>
                            <td class="text-center"><span class="v-badge v-red">{{ $cb['declined'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No closer activity yet</td></tr>
                    @endforelse
                </tbody>
                @if($closerBreakdown->count() > 0)
                <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="text-center"><span class="v-badge v-blue">{{ $closerBreakdown->sum('total') }}</span></td>
                        <td class="text-center"><span class="v-badge v-teal">{{ $closerBreakdown->sum('with_closer') }}</span></td>
                        <td class="text-center"><span class="v-badge v-purple">{{ $closerBreakdown->sum('with_validator') }}</span></td>
                        <td class="text-center"><span class="v-badge v-warn">{{ $closerBreakdown->sum('pending') }}</span></td>
                        <td class="text-center"><span class="v-badge v-green">{{ $closerBreakdown->sum('sales') }}</span></td>
                        <td class="text-center"><span class="v-badge v-red">{{ $closerBreakdown->sum('declined') }}</span></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Submissions Log --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-ul" style="color:#50a5f1;"></i> My Transferred Forms</h6>
            <a href="{{ route('verifier.create.team', 'peregrine') }}" class="act-btn a-success"><i class="bx bx-plus"></i> New Form</a>
        </div>
        <div class="scroll-tbl" style="max-height:400px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Date / Time</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Closer</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td style="white-space:nowrap;">{{ $lead->verified_at ? $lead->verified_at->setTimezone('America/Denver')->format('M d, h:i A') : ($lead->created_at ? $lead->created_at->setTimezone('America/Denver')->format('M d, h:i A') : '—') }}</td>
                            <td><strong>{{ $lead->cn_name }}</strong></td>
                            <td>{{ $lead->phone_number ?? '—' }}</td>
                            <td>{{ $lead->closer_name ?? '—' }}</td>
                            <td class="text-center">
                                @php
                                    $sMap = [
                                        'transferred' => ['Transferred', 's-transferred'],
                                        'closed' => ['With Validator', 's-closed'],
                                        'sale' => ['Sale', 's-sale'],
                                        'declined' => [$lead->decline_reason ?? 'Declined', 's-declined'],
                                        'pending' => [$lead->pending_reason ?? 'Pending', 's-pending'],
                                        'returned' => ['Returned', 's-returned'],
                                    ];
                                    $s = $sMap[$lead->status] ?? [ucfirst($lead->status), 's-pending'];
                                @endphp
                                <span class="s-pill {{ $s[1] }}">{{ $s[0] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                            <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.3rem;"></i>
                            No forms submitted yet
                            <div style="margin-top:.4rem;"><a href="{{ route('verifier.create.team', 'peregrine') }}" class="act-btn a-primary"><i class="bx bx-plus"></i> Submit Your First Form</a></div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
<script>
    // No additional JS needed — bubble pills use direct links/form submit
</script>
@endsection
