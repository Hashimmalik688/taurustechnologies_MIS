@extends('layouts.master')

@section('title')
    Validator Dashboard
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
    .modal-header-custom {
        background: linear-gradient(135deg, var(--bs-card-bg) 0%, rgba(212,175,55,.08) 100%);
        border-bottom: 1px solid rgba(212,175,55,.15);
        color: var(--bs-surface-800);
    }
    .modal-header-custom .modal-title { font-size: .85rem; font-weight: 700; }
    .modal-dialog-scrollable .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    .modal-xl { max-width: 1200px; }
    /* Toggle switch pill */
    .pipe-toggle {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .68rem; font-weight: 600; color: var(--bs-surface-500);
        padding: .25rem .6rem; border-radius: 22px;
        border: 1px solid rgba(0,0,0,.08);
        background: var(--bs-card-bg); cursor: pointer;
    }
    .pipe-toggle input { width: 14px; height: 14px; accent-color: #d4af37; cursor: pointer; }
    .pipe-toggle.active { border-color: rgba(212,175,55,.3); background: rgba(212,175,55,.06); color: #b89730; }
    /* Partner badge */
    .v-partner { display:inline-block;padding:.15rem .4rem;border-radius:10px;font-size:.62rem;font-weight:700;background:rgba(80,141,237,.1);color:#508ded;border:1px solid rgba(80,141,237,.15); }
    /* Action button group inline */
    .act-group { display:inline-flex; gap:.25rem; }
</style>
@endsection

@section('content')
    {{-- Bubble-Pill Filter Bar --}}
    <form method="GET" action="{{ route('validator.index') }}" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="{{ route('validator.index', ['filter' => 'today']) }}" class="pipe-pill {{ $filter === 'today' ? 'active' : '' }}"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill {{ $filter === 'custom' ? 'active' : '' }}" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:{{ $filter === 'custom' ? 'flex' : 'none' }};align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="text" name="start_date" class="pipe-pill-date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD" readonly>
            <span class="pipe-pill-lbl">TO</span>
            <input type="text" name="end_date" class="pipe-pill-date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD" readonly>
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <label class="pipe-toggle {{ request('show_all_pending') ? 'active' : '' }}">
            <input type="checkbox" name="show_all_pending" value="1" {{ request('show_all_pending') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
            Show all pending
        </label>
        @if($filter !== 'today')
            <a href="{{ route('validator.index', ['filter' => 'today']) }}" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        @endif
    </form>

    @if (session('success'))
        <div class="ex-card" style="display:flex;align-items:center;gap:.5rem;padding:.55rem .85rem;margin-bottom:.65rem;background:rgba(16,185,129,.06);border-color:rgba(16,185,129,.15);">
            <i class="bx bx-check-circle" style="color:#10b981;font-size:1rem;"></i>
            <span style="font-size:.78rem;font-weight:600;color:#065f46;">{{ session('success') }}</span>
            <button type="button" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#065f46;opacity:.6;font-size:1rem;" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="kpi-row">
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-send k-icon"></i>
            <div class="k-val">{{ $todayStats['submitted'] ?? 0 }}</div>
            <div class="k-lbl">Submitted</div>
        </div>
        <div class="kpi-card k-warn ex-card">
            <i class="bx bx-time-five k-icon"></i>
            <div class="k-val">{{ $todayStats['pending'] ?? 0 }}</div>
            <div class="k-lbl">Pending</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val">{{ $todayStats['sales'] ?? 0 }}</div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-undo k-icon"></i>
            <div class="k-val">{{ $todayStats['returned'] ?? 0 }}</div>
            <div class="k-lbl">Returned</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val">{{ $todayStats['declined'] ?? 0 }}</div>
            <div class="k-lbl">Declined</div>
        </div>
    </div>

    {{-- Pending Validation --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#b87a14;">
            <i class="bx bx-check-shield" style="color:#f1b44c;"></i> Pending Validation
            <span class="badge-count">{{ $pendingLeads->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:400px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Verifier</th>
                        <th>Closer</th>
                        <th class="text-center">Partner</th>
                        <th class="text-end">Coverage</th>
                        <th>Submitted</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingLeads as $lead)
                        <tr>
                            <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                            <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                            <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->assigned_partner)
                                    <span class="v-partner">{{ $lead->assigned_partner }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td class="text-end">${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                            <td style="white-space:nowrap;">{{ $lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A') }}</td>
                            <td class="text-center">
                                <button type="button" class="act-btn a-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $lead->id }}"><i class="bx bx-edit"></i> Review</button>
                            </td>
                        </tr>

                        {{-- Edit/Validate Modal --}}
                        <div class="modal fade" id="editModal{{ $lead->id }}" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <h5 class="modal-title">Validate Lead — {{ $lead->cn_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('validator.update', $lead->id) }}" id="validatorForm{{ $lead->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body u-overflow-y-auto" style="max-height: calc(100vh - 250px)">
                                            @include('peregrine.closers.form', ['lead' => $lead, 'isValidator' => true])
                                        </div>
                                        <div class="modal-footer" style="gap:.3rem;">
                                            <button type="submit" class="act-btn a-success" style="padding:.35rem .7rem;"><i class="bx bx-check"></i> Mark as Sale</button>
                                            <button type="button" class="act-btn a-warn" style="padding:.35rem .7rem;" onclick="document.getElementById('forwardForm{{ $lead->id }}').submit(); return false;"><i class="bx bx-send"></i> Home Office</button>
                                            <button type="button" class="act-btn a-danger" style="padding:.35rem .7rem;" onclick="document.getElementById('declineForm{{ $lead->id }}').submit(); return false;"><i class="bx bx-x"></i> Declined</button>
                                            <button type="button" class="act-btn a-info" style="padding:.35rem .7rem;" onclick="returnToCloser{{ $lead->id }}()"><i class="bx bx-arrow-back"></i> Return to Closer</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>

                                    {{-- Hidden forms for other actions --}}
                                    <form method="POST" action="{{ route('validator.mark-forwarded', $lead->id) }}" id="forwardForm{{ $lead->id }}" class="d-none">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                    <form method="POST" action="{{ route('validator.mark-simple-declined', $lead->id) }}" id="declineForm{{ $lead->id }}" class="d-none">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Decline Reason Modal --}}
                        <div class="modal fade" id="declineModal{{ $lead->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background:rgba(244,106,106,.08);border-bottom:1px solid rgba(244,106,106,.15);">
                                        <h5 class="modal-title" style="font-size:.85rem;font-weight:700;">Select Decline Reason</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('validator.mark-failed', $lead->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <p class="mb-3" style="font-size:.8rem;">Why is this lead being declined?</p>
                                            @foreach(['Declined:POA', 'Declined:DNQ-Age', 'Declined:Declined SSN', 'Declined:Not Interested', 'Declined:DNC', 'Declined:Cannot Afford', 'Declined:DNQ-Health', 'Declined:Declined Banking', 'Declined:No Pitch (Not Interested)', 'Declined:No Answer'] as $reason)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="decline_reason" id="dr_{{ Str::slug($reason) }}_{{ $lead->id }}" value="{{ $reason }}" required>
                                                <label class="form-check-label" for="dr_{{ Str::slug($reason) }}_{{ $lead->id }}" style="font-size:.8rem;font-weight:600;">{{ $reason }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="act-btn a-danger" style="padding:.35rem .7rem;">Confirm Declined</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                        function returnToCloser{{ $lead->id }}() {
                            if(confirm('Return this lead to closer for more information?')) {
                                const form = document.getElementById('validatorForm{{ $lead->id }}');
                                form.action = '{{ route('validator.return-to-closer', $lead->id) }}';
                                form.submit();
                            }
                        }
                        </script>
                    @empty
                        <tr><td colspan="8" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                            <i class="bx bx-inbox" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i> No pending leads for validation
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sent to Home Office --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#508ded;">
            <i class="bx bx-building-house" style="color:#508ded;"></i> Sent to Home Office
            <span class="badge-count">{{ $homeOfficeLeads->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Customer Name</th>
                        <th>Closer</th>
                        <th class="text-center">Partner</th>
                        <th>Verifier</th>
                        <th class="text-end">Coverage</th>
                        <th>Submitted</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($homeOfficeLeads as $lead)
                        <tr>
                            <td><strong>#{{ $lead->id }}</strong></td>
                            <td>{{ $lead->cn_name }}</td>
                            <td>{{ $lead->assignedCloser->name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->assigned_partner)
                                    <span class="v-partner">{{ $lead->assigned_partner }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>{{ $lead->verifier->name ?? 'N/A' }}</td>
                            <td class="text-end">${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                            <td style="white-space:nowrap;">{{ $lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A') }}</td>
                            <td class="text-center">
                                <div class="act-group">
                                    <form method="POST" action="{{ route('validator.mark-home-office-sale', $lead->id) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="act-btn a-success" onclick="return confirm('Mark this lead as Sale?')"><i class="bx bx-check"></i> Sale</button>
                                    </form>
                                    <form method="POST" action="{{ route('validator.mark-simple-declined', $lead->id) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="act-btn a-danger" onclick="return confirm('Mark this lead as Declined?')"><i class="bx bx-x"></i> Decline</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-inbox"></i> No leads sent to home office</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Completed Validations --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#1a8754;">
            <i class="bx bx-check-circle" style="color:#34c38f;"></i> Completed Validations
            <span class="badge-count">{{ $completedLeads->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Closer</th>
                        <th class="text-center">Partner</th>
                        <th>Verifier</th>
                        <th class="text-center">Status</th>
                        <th>Validated By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedLeads as $lead)
                        <tr>
                            <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td>{{ $lead->closer_name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->assigned_partner)
                                    <span class="v-partner">{{ $lead->assigned_partner }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->status == 'sale')
                                    <span class="s-pill s-sale">Sale</span>
                                @elseif($lead->status == 'forwarded')
                                    <span class="s-pill s-forwarded">Forwarded</span>
                                @else
                                    <span class="s-pill s-declined">{{ $lead->failure_reason ?? 'Failed' }}</span>
                                @endif
                            </td>
                            <td>{{ $lead->validator ? $lead->validator->name : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No completed validations yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
@include('partials.sl-filter-assets')
<script>
    // No additional JS needed — filter bar uses direct links + form submit
</script>
@endsection
