@extends('layouts.master')

@section('title', 'Ravens Validation')

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
    .modal-header-custom {
        background: linear-gradient(135deg, var(--bs-card-bg) 0%, rgba(212,175,55,.08) 100%);
        border-bottom: 1px solid rgba(212,175,55,.15);
        color: var(--bs-surface-800);
    }
    .modal-header-custom .modal-title { font-size: .85rem; font-weight: 700; }
    .modal-xl { max-width: 1100px; }
    .modal-dialog-scrollable .modal-body { max-height: calc(100vh - 220px); overflow-y: auto; }

    .mgr-badge { display:inline-block;padding:.15rem .45rem;border-radius:10px;font-size:.62rem;font-weight:700;text-transform:capitalize; }
    .mgr-approved { background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2); }
    .mgr-declined { background:rgba(244,106,106,.1);color:#c84646;border:1px solid rgba(244,106,106,.2); }

    .act-group { display:inline-flex; gap:.25rem; flex-wrap:wrap; }
    .reviewed-row td { opacity:.75; }

    .pipe-toggle {
        display:inline-flex;align-items:center;gap:.35rem;
        font-size:.68rem;font-weight:600;color:var(--bs-surface-500);
        padding:.25rem .6rem;border-radius:22px;
        border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);cursor:pointer;
    }
    .pipe-toggle input { width:14px;height:14px;accent-color:#d4af37;cursor:pointer; }
    .pipe-toggle.active { border-color:rgba(212,175,55,.3);background:rgba(212,175,55,.06);color:#b89730; }

    .detail-tbl td { padding:.3rem .45rem;font-size:.78rem;border-bottom:1px solid rgba(0,0,0,.04); }
    .detail-tbl td:first-child { font-weight:600;color:var(--bs-surface-500);width:38%;white-space:nowrap; }
</style>
@endsection

@section('content')

{{-- Filter Bar --}}
<form method="GET" action="{{ route('ravens.validation.index') }}" id="filterForm" class="ex-card pipe-filter-bar">
    <a href="{{ route('ravens.validation.index', ['filter' => 'today']) }}"
       class="pipe-pill {{ $filter === 'today' ? 'active' : '' }}">
        <i class="bx bx-calendar"></i> Today
    </a>
    <a href="{{ route('ravens.validation.index', ['filter' => 'week']) }}"
       class="pipe-pill {{ $filter === 'week' ? 'active' : '' }}">
        <i class="bx bx-calendar-week"></i> This Week
    </a>
    <a href="{{ route('ravens.validation.index', ['filter' => 'month']) }}"
       class="pipe-pill {{ $filter === 'month' ? 'active' : '' }}">
        <i class="bx bx-calendar-alt"></i> This Month
    </a>
    <span class="pipe-pill {{ $filter === 'custom' ? 'active' : '' }}"
          onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'"
          style="cursor:pointer;">
        <i class="bx bx-calendar-event"></i> Custom Range
    </span>
    <span id="customRange" style="display:{{ $filter === 'custom' ? 'flex' : 'none' }};align-items:center;gap:.3rem;">
        <input type="hidden" name="filter" value="custom">
        <span class="pipe-pill-lbl">FROM</span>
        <input type="text" name="start_date" class="pipe-pill-date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD" readonly>
        <span class="pipe-pill-lbl">TO</span>
        <input type="text" name="end_date" class="pipe-pill-date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD" readonly>
        <button type="submit" class="pipe-pill-apply">Apply</button>
    </span>
    <span style="display:inline-flex;align-items:center;gap:.3rem;position:relative;">
        <i class="bx bx-search" style="position:absolute;left:.55rem;font-size:.8rem;color:var(--bs-surface-400);pointer-events:none;"></i>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name, phone…"
               style="font-size:.72rem;font-weight:600;padding:.32rem .55rem .32rem 1.8rem;border-radius:22px;border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);color:var(--bs-surface-600);outline:none;min-width:165px;">
    </span>
    <label class="pipe-toggle {{ $showAll ? 'active' : '' }}">
        <input type="checkbox" name="show_all" value="1"
               {{ $showAll ? 'checked' : '' }}
               onchange="document.getElementById('filterForm').submit()">
        Show all pending
    </label>
    @if($filter !== 'today' || $search)
        <a href="{{ route('ravens.validation.index', ['filter' => 'today']) }}" class="pipe-pill-clear">
            <i class="bx bx-x"></i> Clear
        </a>
    @endif
</form>

{{-- Flash Message --}}
@if(session('success'))
    <div id="flashMsg" class="ex-card" style="display:flex;align-items:center;gap:.5rem;padding:.55rem .85rem;margin-bottom:.65rem;background:rgba(16,185,129,.06);border-color:rgba(16,185,129,.15);">
        <i class="bx bx-check-circle" style="color:#10b981;font-size:1rem;"></i>
        <span style="font-size:.78rem;font-weight:600;color:#065f46;">{{ session('success') }}</span>
        <button type="button" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#065f46;opacity:.6;font-size:1rem;" onclick="this.parentElement.remove()">&times;</button>
    </div>
@endif

{{-- KPI Cards --}}
<div class="kpi-row">
    <div class="kpi-card k-warn ex-card">
        <i class="bx bx-time-five k-icon"></i>
        <div class="k-val">{{ $todayStats['pending'] }}</div>
        <div class="k-lbl">Pending</div>
    </div>
    <div class="kpi-card k-green ex-card">
        <i class="bx bx-send k-icon"></i>
        <div class="k-val">{{ $todayStats['sent_to_policy'] }}</div>
        <div class="k-lbl">Valid</div>
    </div>
    <div class="kpi-card k-red ex-card">
        <i class="bx bx-x-circle k-icon"></i>
        <div class="k-val">{{ $todayStats['kept_declined'] }}</div>
        <div class="k-lbl">Not Valid</div>
    </div>
</div>

{{-- Pending Validation --}}
<div class="ex-card sec-card">
    <div class="pipe-hdr" style="color:#b87a14;">
        <i class="bx bx-check-shield" style="color:#f1b44c;"></i>
        Pending Validation
        <span class="badge-count">{{ $pendingLeads->count() }}</span>
    </div>

    @if($pendingLeads->isEmpty())
        <div style="padding:2rem;text-align:center;color:var(--bs-surface-400);font-size:.8rem;">
            <i class="bx bx-check-double" style="font-size:2.2rem;opacity:.25;display:block;margin-bottom:.5rem;"></i>
            No leads pending validation for this period.
        </div>
    @else
    <div class="scroll-tbl" style="max-height:520px;">
        <table class="ex-tbl">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th class="text-center">Mgr Status</th>
                    <th class="text-center" style="min-width:240px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingLeads as $lead)
                <tr>
                    <td>
                        <strong>{{ $lead->cn_name ?? 'N/A' }}</strong>
                        @if($lead->state)
                            <br><span style="font-size:.6rem;color:var(--bs-surface-400);">{{ $lead->state }}</span>
                        @endif
                    </td>
                    <td>{{ $lead->phone_number ?? '—' }}</td>
                    <td class="text-center">
                        @if($lead->manager_status === 'approved')
                            <span class="mgr-badge mgr-approved"><i class="bx bx-check"></i> Approved</span>
                        @elseif($lead->manager_status === 'declined')
                            <span class="mgr-badge mgr-declined"><i class="bx bx-x"></i> Declined</span>
                        @else
                            <span class="mgr-badge" style="background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.2);"><i class="bx bx-time-five"></i> Pending</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="act-group">
                            <button type="button" class="act-btn a-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal{{ $lead->id }}">
                                <i class="bx bx-show"></i> View
                            </button>
                            <form method="POST" action="{{ route('ravens.validation.mark-valid', $lead->id) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Mark this lead as valid?')">
                                @csrf
                                <button type="submit" class="act-btn a-success">
                                    <i class="bx bx-check-circle"></i> Valid
                                </button>
                            </form>
                            <form method="POST" action="{{ route('ravens.validation.keep-declined', $lead->id) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Mark this lead as not valid?')">
                                @csrf
                                <button type="submit" class="act-btn a-danger">
                                    <i class="bx bx-x-circle"></i> Not Valid
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Modals outside table to avoid invalid HTML --}}
@foreach($pendingLeads as $lead)
@php
    $beneficiaries = $lead->beneficiaries ?? [];
    if (is_string($beneficiaries)) { $dec = json_decode($beneficiaries, true); $beneficiaries = is_array($dec) ? $dec : []; }
    if (!is_array($beneficiaries)) { $beneficiaries = []; }
    if (empty($beneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
        $beneficiaries = [['name' => $lead->beneficiary ?? '', 'dob' => $lead->beneficiary_dob ?? '', 'relation' => '']];
    }
@endphp
<div class="modal fade" id="detailModal{{ $lead->id }}" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title">
                    <i class="bx bx-user-circle" style="color:#d4af37;margin-right:.4rem;"></i>
                    {{ $lead->cn_name }} &mdash;
                    <span class="mgr-badge {{ $lead->manager_status === 'approved' ? 'mgr-approved' : ($lead->manager_status === 'declined' ? 'mgr-declined' : '') }}" style="margin-left:.3rem;{{ $lead->manager_status === 'pending' ? 'background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.2);' : '' }}">
                        {{ ucfirst($lead->manager_status ?? '—') }}
                    </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">

                    {{-- Personal --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-user"></i> Personal Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Full Name</td><td>{{ $lead->cn_name ?? '—' }}</td></tr>
                                <tr><td>Date of Birth</td><td>{{ $lead->date_of_birth?->format('M d, Y') ?? '—' }}</td></tr>
                                <tr><td>Age</td><td>{{ $lead->age ?? '—' }}</td></tr>
                                <tr><td>Gender</td><td>{{ $lead->gender ?? '—' }}</td></tr>
                                <tr><td>Birth Place</td><td>{{ $lead->birth_place ?? '—' }}</td></tr>
                                <tr><td>SSN</td><td>{{ $lead->ssn ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-phone"></i> Contact Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Primary Phone</td><td>{{ $lead->phone_number ?? '—' }}</td></tr>
                                <tr><td>Secondary Phone</td><td>{{ $lead->secondary_phone_number ?? '—' }}</td></tr>
                                <tr><td>Address</td><td>{{ $lead->address ?? '—' }}</td></tr>
                                <tr><td>State</td><td>{{ $lead->state ?? '—' }}</td></tr>
                                <tr><td>Zip Code</td><td>{{ $lead->zip_code ?? '—' }}</td></tr>
                                <tr><td>Emergency Contact</td><td>{{ $lead->emergency_contact ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Health --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-heart"></i> Health Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Nicotine User</td><td>{{ $lead->smoker ? 'Yes' : 'No' }}</td></tr>
                                <tr><td>Height</td><td>{{ $lead->height ?? '—' }}</td></tr>
                                <tr><td>Weight</td><td>{{ $lead->weight ? $lead->weight.' lbs' : '—' }}</td></tr>
                                <tr><td>Driving License</td><td>{{ $lead->driving_license ? 'Yes' : 'No' }}</td></tr>
                                @if($lead->driving_license_number)
                                <tr><td>DL Number</td><td>{{ $lead->driving_license_number }}</td></tr>
                                @endif
                                <tr><td>Medical Issues</td><td>{{ $lead->medical_issue ?? 'None reported' }}</td></tr>
                                <tr><td>Medications</td><td>{{ $lead->medications ?? 'None reported' }}</td></tr>
                                <tr><td>Doctor Name</td><td>{{ $lead->doctor_name ?? '—' }}</td></tr>
                                <tr><td>Doctor Phone</td><td>{{ $lead->doctor_number ?? '—' }}</td></tr>
                                <tr><td>Doctor Address</td><td>{{ $lead->doctor_address ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Policy --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-shield-check"></i> Policy Information</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Plan Type</td><td>{{ $lead->policy_type ?? '—' }}</td></tr>
                                <tr><td>Policy Number</td><td>{{ $lead->policy_number ?? '—' }}</td></tr>
                                <tr><td>Carrier</td><td>{{ $lead->carrier_name ?? '—' }}</td></tr>
                                <tr><td>Coverage Amount</td><td>{{ $lead->coverage_amount ? '$'.number_format($lead->coverage_amount,0) : '—' }}</td></tr>
                                <tr><td>Monthly Premium</td><td>{{ $lead->monthly_premium ? '$'.number_format($lead->monthly_premium,2).'/mo' : '—' }}</td></tr>
                                <tr><td>Initial Draft Date</td><td>{{ $lead->initial_draft_date?->format('M d, Y') ?? '—' }}</td></tr>
                                <tr><td>Future Draft Date</td><td>{{ $lead->future_draft_date?->format('M d, Y') ?? '—' }}</td></tr>
                                <tr><td>Closer</td><td>{{ $lead->closer_name ?? '—' }}</td></tr>
                                <tr><td>Sale Date</td><td>{{ $lead->sale_at?->setTimezone('America/Los_Angeles')->format('M d, Y h:i A') ?? $lead->sale_date?->format('M d, Y') ?? '—' }}</td></tr>
                                <tr><td>Lead Source</td><td>{{ $lead->source ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Beneficiaries --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-heart-circle"></i> Beneficiary Information</div>
                            @if(!empty($beneficiaries))
                                @foreach($beneficiaries as $bi => $bene)
                                    @if(count($beneficiaries) > 1)
                                        <div style="font-size:.65rem;font-weight:700;color:#b89730;margin-bottom:.2rem;">Beneficiary {{ $bi + 1 }}</div>
                                    @endif
                                    <table class="detail-tbl" style="width:100%;border-collapse:collapse;{{ !$loop->last ? 'margin-bottom:.5rem;' : '' }}">
                                        <tr><td>Name</td><td>{{ $bene['name'] ?? '—' }}</td></tr>
                                        <tr><td>Relation</td><td>{{ $bene['relation'] ?? '—' }}</td></tr>
                                        <tr><td>Date of Birth</td><td>@php try { echo !empty($bene['dob']) ? \Carbon\Carbon::parse($bene['dob'])->format('M d, Y') : '—'; } catch(\Exception $e) { echo '—'; } @endphp</td></tr>
                                    </table>
                                @endforeach
                            @else
                                <span style="font-size:.78rem;color:var(--bs-surface-400);">No beneficiaries added</span>
                            @endif
                        </div>
                    </div>

                    {{-- Banking --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-bank"></i> Bank Account</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr><td>Bank Name</td><td>{{ $lead->bank_name ?? '—' }}</td></tr>
                                <tr><td>Account Type</td><td>{{ $lead->account_type ?? '—' }}</td></tr>
                                <tr><td>Account Title</td><td>{{ $lead->account_title ?? '—' }}</td></tr>
                                <tr><td>Routing #</td><td>{{ $lead->routing_number ?? '—' }}</td></tr>
                                <tr><td>Account #</td><td>{{ $lead->acc_number ?? $lead->account_number ?? '—' }}</td></tr>
                                <tr><td>Bank Balance</td><td>{{ $lead->bank_balance ? '$'.number_format($lead->bank_balance,2) : '—' }}</td></tr>
                                <tr><td>SS Amount</td><td>{{ $lead->ss_amount ? '$'.number_format($lead->ss_amount,2) : '—' }}</td></tr>
                                <tr><td>SS Date</td><td>{{ $lead->ss_date?->format('M d, Y') ?? '—' }}</td></tr>
                                <tr><td>BV Status</td><td>{{ $lead->bank_verification_status ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Manager Review --}}
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-user-check"></i> Manager Review</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td>Status</td>
                                    <td><span class="mgr-badge {{ $lead->manager_status === 'approved' ? 'mgr-approved' : ($lead->manager_status === 'declined' ? 'mgr-declined' : '') }}" style="{{ $lead->manager_status === 'pending' ? 'background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.2);' : '' }}">{{ ucfirst($lead->manager_status ?? '—') }}</span></td>
                                </tr>
                                <tr><td>Reviewed At</td><td>{{ $lead->manager_reviewed_at?->setTimezone('America/Los_Angeles')->format('M d, Y h:i A') ?? '—' }}</td></tr>
                                <tr><td>Reason</td><td>{{ $lead->manager_reason ?? '—' }}</td></tr>
                            </table>
                            @if($lead->manager_notes)
                                <div style="margin-top:.5rem;padding:.5rem .65rem;background:rgba(212,175,55,.05);border-radius:.4rem;border:1px solid rgba(212,175,55,.12);font-size:.77rem;">{{ $lead->manager_notes }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Notes / Comments --}}
                    @if($lead->staff_notes || $lead->comments || $lead->preset_line)
                    <div class="col-md-6">
                        <div class="ex-card" style="padding:.85rem;">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d4af37;margin-bottom:.55rem;"><i class="bx bx-note"></i> Notes</div>
                            <table class="detail-tbl" style="width:100%;border-collapse:collapse;">
                                @if($lead->preset_line)<tr><td>Preset Line</td><td>{{ $lead->preset_line }}</td></tr>@endif
                                @if($lead->comments)<tr><td>Comments</td><td>{{ $lead->comments }}</td></tr>@endif
                                @if($lead->staff_notes)<tr><td>Staff Notes</td><td>{{ $lead->staff_notes }}</td></tr>@endif
                            </table>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            <div class="modal-footer" style="gap:.35rem;">
                <form method="POST" action="{{ route('ravens.validation.mark-valid', $lead->id) }}"
                      onsubmit="return confirm('Mark this lead as valid?')">
                    @csrf
                    <button type="submit" class="act-btn a-success" style="padding:.35rem .9rem;">
                        <i class="bx bx-check-circle"></i> Valid
                    </button>
                </form>
                <form method="POST" action="{{ route('ravens.validation.keep-declined', $lead->id) }}"
                      onsubmit="return confirm('Mark this lead as not valid?')">
                    @csrf
                    <button type="submit" class="act-btn a-danger" style="padding:.35rem .9rem;">
                        <i class="bx bx-x-circle"></i> Not Valid
                    </button>
                </form>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Reviewed --}}
<div class="ex-card sec-card">
    <div class="pipe-hdr" style="color:#2b81c9;">
        <i class="bx bx-check-double" style="color:#50a5f1;"></i>
        Reviewed
        <span class="badge-count">{{ $reviewedLeads->count() }}</span>
    </div>

    @if($reviewedLeads->isEmpty())
        <div style="padding:1.5rem;text-align:center;color:var(--bs-surface-400);font-size:.78rem;">
            No leads reviewed in this period.
        </div>
    @else
    <div class="scroll-tbl" style="max-height:380px;">
        <table class="ex-tbl">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th class="text-center">Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviewedLeads as $lead)
                <tr class="reviewed-row">
                    <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                    <td class="text-center">
                        @if($lead->ravens_validation_status === 'valid')
                            <span class="mgr-badge mgr-approved"><i class="bx bx-check"></i> Valid</span>
                        @else
                            <span class="mgr-badge mgr-declined"><i class="bx bx-x"></i> Not Valid</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection

@section('js')
<script>
    const flash = document.getElementById('flashMsg');
    if (flash) setTimeout(() => flash.remove(), 5000);

    document.querySelectorAll('.pipe-pill-date').forEach(function(el) {
        if (window.flatpickr) flatpickr(el, { dateFormat: 'Y-m-d', allowInput: true });
    });
</script>
@endsection
