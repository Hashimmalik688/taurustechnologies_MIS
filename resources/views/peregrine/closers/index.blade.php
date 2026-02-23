@use('App\Support\Statuses')
@extends('layouts.master')

@section('title')
    Peregrine Closers
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
    .clickable-row { cursor: pointer; }
    .clickable-row:hover { background: rgba(212,175,55,.04) !important; }
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
</style>
@endsection

@section('content')
    {{-- Bubble-Pill Filter Bar --}}
    <form method="GET" action="{{ route('peregrine.closers.index') }}" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="{{ route('peregrine.closers.index', ['filter' => 'today']) }}" class="pipe-pill {{ $filter === 'today' ? 'active' : '' }}"><i class="bx bx-calendar"></i> Today</a>
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
            <a href="{{ route('peregrine.closers.index', ['filter' => 'today']) }}" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
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
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-transfer-alt k-icon"></i>
            <div class="k-val">{{ $todayStats['total_assigned'] ?? 0 }}</div>
            <div class="k-lbl">Assigned</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-user-pin k-icon"></i>
            <div class="k-val">{{ $todayStats['with_closer'] ?? 0 }}</div>
            <div class="k-lbl">With Closer</div>
        </div>
        <div class="kpi-card k-purple ex-card">
            <i class="bx bx-check-shield k-icon"></i>
            <div class="k-val">{{ $todayStats['sent_to_validator'] ?? 0 }}</div>
            <div class="k-lbl">Sent to Validator</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val">{{ $todayStats['sales'] ?? 0 }}</div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-red ex-card">
            <i class="bx bx-x-circle k-icon"></i>
            <div class="k-val">{{ $todayStats['declined'] ?? 0 }}</div>
            <div class="k-lbl">Declined</div>
        </div>
    </div>

    {{-- Pending Leads --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#b87a14;">
            <i class="bx bx-time-five" style="color:#f1b44c;"></i> Pending Leads
            <span class="badge-count">{{ $pendingLeads->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:400px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Verifier</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingLeads as $lead)
                        <tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#leadModal{{ $lead->id }}">
                            <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                            <td style="white-space:nowrap;">{{ $lead->date ?? ($lead->created_at ? $lead->created_at->setTimezone('America/Denver')->format('M d, h:i A') : 'N/A') }}</td>
                            <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->status == Statuses::LEAD_RETURNED)
                                    <span class="s-pill s-returned">Returned</span>
                                @elseif($lead->pending_reason)
                                    <span class="s-pill s-pending">{{ $lead->pending_reason }}</span>
                                @else
                                    <span class="s-pill s-transferred">Pending</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="act-btn a-primary" type="button"><i class="bx bx-edit"></i> Fill Form</button>
                            </td>
                        </tr>

                        {{-- Modal for this lead --}}
                        <div class="modal fade" id="leadModal{{ $lead->id }}" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <h5 class="modal-title">Complete Lead — {{ $lead->cn_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body u-overflow-y-auto" style="max-height: calc(100vh - 250px)">
                                        <form method="POST" action="{{ route('peregrine.closers.update', $lead->id) }}" id="leadForm{{ $lead->id }}">
                                            @csrf
                                            @method('PUT')
                                            @include('peregrine.closers.form', ['lead' => $lead])
                                        </form>
                                    </div>
                                    <div class="modal-footer" style="gap:.3rem;">
                                        <button type="button" class="act-btn a-danger" style="padding:.35rem .7rem;" data-bs-toggle="modal" data-bs-target="#failModal{{ $lead->id }}"><i class="bx bx-x-circle"></i> Failed</button>
                                        <button type="button" class="act-btn a-warn" style="padding:.35rem .7rem;" data-bs-toggle="modal" data-bs-target="#pendingModal{{ $lead->id }}"><i class="bx bx-time-five"></i> Pending</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" form="leadForm{{ $lead->id }}" class="act-btn a-success" style="padding:.35rem .7rem;"><i class="bx bx-send"></i> Submit to Validator</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pending Reason Modal --}}
                        <div class="modal fade" id="pendingModal{{ $lead->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <h5 class="modal-title">Select Pending Reason</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('peregrine.closers.mark-pending', $lead->id) }}" id="pendingReasonForm{{ $lead->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <p class="mb-3" style="font-size:.8rem;">Why is this lead being marked as pending?</p>
                                            @foreach(['Pending:Future Potential', 'Pending:Callback', 'Pending:Pending Banking', 'Pending:Pending Validation'] as $reason)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="pending_reason" id="pr_{{ Str::slug($reason) }}_{{ $lead->id }}" value="{{ $reason }}" required>
                                                <label class="form-check-label" for="pr_{{ Str::slug($reason) }}_{{ $lead->id }}" style="font-size:.8rem;font-weight:600;">{{ $reason }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="act-btn a-warn" style="padding:.35rem .7rem;">Confirm Pending</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Failure Reason Modal --}}
                        <div class="modal fade" id="failModal{{ $lead->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background:rgba(244,106,106,.08);border-bottom:1px solid rgba(244,106,106,.15);">
                                        <h5 class="modal-title" style="font-size:.85rem;font-weight:700;">Select Failure Reason</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('peregrine.closers.mark-failed', $lead->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <p class="mb-3" style="font-size:.8rem;">Why is this lead being marked as failed?</p>
                                            @foreach(['Failed:POA', 'Failed:DNQ-Age', 'Failed:Declined SSN', 'Failed:Not Interested', 'Failed:DNC', 'Failed:Cannot Afford', 'Failed:DNQ-Health', 'Failed:Declined Banking', 'Failed:No Pitch (Not Interested)', 'Failed:No Answer'] as $reason)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="failure_reason" id="fr_{{ Str::slug($reason) }}_{{ $lead->id }}" value="{{ $reason }}" required>
                                                <label class="form-check-label" for="fr_{{ Str::slug($reason) }}_{{ $lead->id }}" style="font-size:.8rem;font-weight:600;">{{ $reason }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="act-btn a-danger" style="padding:.35rem .7rem;">Confirm Failed</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.getElementById('pendingReasonForm{{ $lead->id }}').addEventListener('submit', function(e) {
                            const mainForm = document.getElementById('leadForm{{ $lead->id }}');
                            const pendingForm = this;
                            const addedRadios = new Set();
                            mainForm.querySelectorAll('input, select, textarea').forEach(function(input) {
                                if (input.name && input.name !== '_token' && input.name !== '_method' && input.name !== 'pending_reason') {
                                    if (input.type === 'radio') {
                                        if (input.checked && !addedRadios.has(input.name)) {
                                            addedRadios.add(input.name);
                                            let hidden = document.createElement('input');
                                            hidden.type = 'hidden'; hidden.name = input.name; hidden.value = input.value;
                                            pendingForm.appendChild(hidden);
                                        }
                                    } else {
                                        let hidden = pendingForm.querySelector('input[name="' + input.name + '"]');
                                        if (!hidden) {
                                            hidden = document.createElement('input');
                                            hidden.type = 'hidden'; hidden.name = input.name;
                                            pendingForm.appendChild(hidden);
                                        }
                                        hidden.value = input.type === 'checkbox' ? (input.checked ? '1' : '0') : (input.value || '');
                                    }
                                }
                            });
                        });
                        </script>
                    @empty
                        <tr><td colspan="6" class="text-center" style="padding:1.5rem;color:var(--bs-surface-400);font-size:.75rem;">
                            <i class="bx bx-inbox" style="font-size:1.3rem;display:block;margin-bottom:.3rem;"></i> No pending leads
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Completed Leads --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#1a8754;">
            <i class="bx bx-check-circle" style="color:#34c38f;"></i> Completed Leads
            <span class="badge-count">{{ $completedLeads->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Verifier</th>
                        <th class="text-center">Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedLeads as $lead)
                        <tr>
                            <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                            <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->status == 'closed')
                                    <span class="s-pill s-closed">With Validator</span>
                                @elseif($lead->status == 'sale')
                                    <span class="s-pill s-sale">Sale</span>
                                @elseif($lead->status == 'forwarded')
                                    <span class="s-pill s-forwarded">Forwarded</span>
                                @else
                                    <span class="s-pill s-closed">Closed</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">{{ $lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-info-circle"></i> No completed leads yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Failed Leads --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#c84646;">
            <i class="bx bx-x-circle" style="color:#f46a6a;"></i> Failed Leads
            <span class="badge-count">{{ $failedLeads->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Verifier</th>
                        <th class="text-center">Reason</th>
                        <th>Failed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($failedLeads as $lead)
                        <tr>
                            <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                            <td>{{ $lead->account_verified_by ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="s-pill s-declined">
                                    @if($lead->status == Statuses::LEAD_DECLINED)
                                        {{ $lead->manager_reason ?? $lead->decline_reason ?? 'Declined' }}
                                    @else
                                        {{ $lead->decline_reason ?? 'Failed' }}
                                    @endif
                                </span>
                            </td>
                            <td style="white-space:nowrap;">{{ $lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-smile"></i> No failed leads</td></tr>
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
