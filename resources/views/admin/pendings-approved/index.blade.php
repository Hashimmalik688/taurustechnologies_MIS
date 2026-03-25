@use('App\Support\Statuses')
@extends('layouts.master')

@section('title', 'Submissions')

@section('css')
<style>
/* ── KPI Cards ── */
.kpi-row { display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem; }
.kpi-card { flex:1 1 80px;min-width:75px;padding:.65rem .6rem;border-radius:.55rem;text-align:center;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.06);transition:transform .15s,box-shadow .15s; }
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08);}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0;}
.kpi-card .k-val{font-size:1.35rem;font-weight:700;line-height:1;}
.kpi-card .k-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem;}
.kpi-card.k-gold{background:rgba(212,175,55,.06)}.kpi-card.k-gold::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}.kpi-card.k-gold .k-val{color:#b89730}
.kpi-card.k-green{background:rgba(52,195,143,.06)}.kpi-card.k-green::before{background:linear-gradient(90deg,#34c38f,#6eddb8)}.kpi-card.k-green .k-val{color:#1a8754}
.kpi-card.k-red{background:rgba(244,106,106,.06)}.kpi-card.k-red::before{background:linear-gradient(90deg,#f46a6a,#f89b9b)}.kpi-card.k-red .k-val{color:#c84646}
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val{color:#556ee6}

/* ── Section Card ── */
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;}

/* ── Table ── */
.ex-tbl{width:100%;font-size:.735rem;border-collapse:collapse;}
.ex-tbl thead th{padding:.35rem .6rem;font-weight:600;font-size:.68rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);white-space:nowrap;border-bottom:1px solid rgba(0,0,0,.07);}
.ex-tbl tbody td{padding:.4rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.04);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}

/* ── Status badges ── */
.bd-ni{background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-resolved{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-pending{background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}

/* ── Action Buttons ── */
.a-btn{display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.a-send{background:#34c38f20;color:#1a8754;border-color:#34c38f40;}.a-send:hover{background:#34c38f30;color:#1a8754;}
.a-edit{background:#556ee620;color:#556ee6;border-color:#556ee640;}.a-edit:hover{background:#556ee630;color:#556ee6;}
.a-ni{background:#f46a6a20;color:#c84646;border-color:#f46a6a40;}.a-ni:hover{background:#f46a6a30;color:#c84646;}
.a-resolve{background:#556ee620;color:#556ee6;border-color:#556ee640;}.a-resolve:hover{background:#556ee630;color:#556ee6;}

/* ── Filter bar ── */
.filter-form{display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);}
.filter-form .form-control,.filter-form .form-select{font-size:.72rem;padding:.3rem .5rem;height:2rem;}
.filter-form label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);margin-bottom:.15rem;}
.f-reset{font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;}.f-reset:hover{color:var(--bs-body-color);}
</style>
@endsection

@section('content')
<div class="container-fluid" style="max-width:1600px">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h5 class="mb-0 fw-semibold" style="font-size:1rem;">
                <i class="bx bx-check-circle me-1" style="color:#34c38f;font-size:1.05rem;"></i>
                Submissions
            </h5>
        </div>
        <div class="d-flex gap-1 align-items-center">
            <a href="{{ route('issuance.index') }}" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.08);">
                <i class="bx bx-right-arrow-alt"></i> Pending Contract
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <div class="k-val">{{ $totalCount }}</div>
            <div class="k-lbl">Total</div>
        </div>
        <div class="kpi-card k-green">
            <div class="k-val">{{ $approvedCount }}</div>
            <div class="k-lbl">Approved</div>
        </div>
        <div class="kpi-card k-red">
            <div class="k-val">{{ $declinedCount }}</div>
            <div class="k-lbl">Declined</div>
        </div>
        <div class="kpi-card k-blue">
            <div class="k-val">{{ $underwritingCount }}</div>
            <div class="k-lbl">Underwriting</div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-list-ul me-1"></i> Validated Leads</h6>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('submissions.index') }}" class="filter-form">
            <div>
                <label>Search</label>
                <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Name, phone, carrier…" style="width:160px;">
            </div>
            <div>
                <label>Carrier</label>
                <select name="carrier" class="form-select" style="width:130px;">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ $carrier == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" style="width:135px;">
            </div>
            <div>
                <label>To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" style="width:135px;">
            </div>
            <button type="submit" class="a-btn a-send" style="height:2rem;">
                <i class="bx bx-search-alt-2"></i> Filter
            </button>
            <a href="{{ route('submissions.index') }}" class="f-reset">
                <i class="bx bx-reset"></i> Clear
            </a>
        </form>

        {{-- Table --}}
        <div class="table-responsive" style="min-height:200px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Phone</th>
                        <th>Carrier</th>
                        <th>Premium</th>
                        <th>Closer</th>
                        <th>Partner</th>
                        <th>Sale Date</th>
                        <th>App ID</th>
                        <th>Manager Status</th>
                        <th>Policy Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        @php
                            $isBlocked = !empty($lead->not_issued_at) && empty($lead->not_issued_resolved_at);
                            $wasResolved = !empty($lead->not_issued_at) && !empty($lead->not_issued_resolved_at);
                            $isApproved = $lead->manager_status === Statuses::MGR_APPROVED;
                        @endphp
                        <tr>
                            <td style="color:var(--bs-surface-400);">{{ $lead->id }}</td>
                            <td>
                                <a href="{{ route('issuance.show', $lead->id) }}" style="font-weight:500;font-size:.73rem;">
                                    {{ $lead->cn_name ?? '—' }}
                                </a>
                            </td>
                            <td>{{ $lead->phone_number ?? '—' }}</td>
                            <td>{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? '—') }}</td>
                            <td>${{ number_format($lead->monthly_premium, 2) }}</td>
                            <td>{{ $lead->closer_name ?? '—' }}</td>
                            <td>{{ $lead->assigned_partner ?? '—' }}</td>
                            <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}</td>
                            <td style="color:var(--bs-surface-500);font-weight:600;">{{ $lead->id }}</td>
                            <td>
                                <span style="display:inline-block;padding:.25rem .5rem;background:#e8f5e9;color:#2e7d32;border-radius:.25rem;font-size:.7rem;font-weight:500;">
                                    Valid - Approved
                                </span>
                            </td>
                            <td>
                                <span style="color:var(--bs-surface-400);font-size:.7rem;">—</span>
                            </td>
                            <td>
                                @if($isBlocked)
                                    <span class="bd-ni" style="font-size:.65rem;">
                                        Not Issued
                                    </span>
                                    <div style="font-size:.6rem;color:var(--bs-surface-400);margin-top:.1rem;">
                                        {{ $lead->notIssuedBy->name ?? '?' }} · {{ $lead->not_issued_at->diffForHumans() }}
                                    </div>
                                @elseif($wasResolved)
                                    <span class="bd-resolved" style="font-size:.65rem;">Resolved</span>
                                @else
                                    <span class="bd-pending" style="font-size:.65rem;">Ready</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($isApproved && !$isBlocked)
                                        <button class="a-btn a-send btn-send-contract" data-id="{{ $lead->id }}" style="font-size:.65rem;">
                                            <i class="bx bx-right-arrow-alt"></i> Send
                                        </button>
                                    @endif
                                    <button class="a-btn a-edit btn-open-actions-modal" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" data-status="{{ $lead->manager_status }}" data-policy="{{ $lead->policy_number ?? '' }}" data-partner="{{ $lead->assigned_partner ?? '' }}" style="font-size:.65rem;">
                                        <i class="bx bx-pencil"></i> Manage
                                    </button>
                                    @if(!$isBlocked)
                                        <button class="a-btn a-ni btn-mark-ni" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.65rem;">
                                            <i class="bx bx-error-circle"></i> Not Issued
                                        </button>
                                    @endif
                                    @if($isBlocked)
                                        <button class="a-btn a-resolve btn-resolve-ni" data-id="{{ $lead->id }}" style="font-size:.65rem;">
                                            <i class="bx bx-check"></i> Resolve
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.4;"></i>
                                No leads in Submissions for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="px-3 py-2">{{ $leads->withQueryString()->links() }}</div>
        @endif
    </div>

</div>


{{-- Actions Management Modal --}}
<div class="modal fade" id="actionsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-pencil me-1"></i> Manage Application
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-3" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="actions-lead-name"></strong>
                </p>
                
                {{-- Manager Status Dropdown --}}
                <div class="mb-3">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Manager Decision</label>
                    <select id="actions-status" class="form-select form-select-sm">
                        <option value="approved">Approved</option>
                        <option value="declined">Declined</option>
                        <option value="underwriting">Underwriting</option>
                    </select>
                </div>

                {{-- Policy Number (conditional) --}}
                <div class="mb-3" id="policy-field-wrapper" style="display:none;">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Policy Number</label>
                    <input type="text" id="actions-policy-number" class="form-control form-control-sm" placeholder="Enter policy number" style="font-size:.7rem;">
                </div>

                {{-- App ID (read-only) --}}
                <div class="mb-3">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">App ID</label>
                    <input type="text" id="actions-app-id" class="form-control form-control-sm" readonly style="font-size:.7rem;background:#f5f5f5;">
                </div>

                {{-- Partner Select --}}
                <div class="mb-3">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Partner</label>
                    <select id="actions-partner" class="form-select form-select-sm" style="font-size:.7rem;">
                        <option value="">— Select Partner —</option>
                        @if(isset($partners) && $partners)
                            @foreach($partners as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="actions-save-btn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

{{-- Not Issued Modal --}}
<div class="modal fade" id="niModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-error-circle me-1 text-danger"></i> Mark as Not Issued
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="ni-lead-name"></strong>
                </p>
                <label class="form-label" style="font-size:.72rem;font-weight:600;">Disposition Reason</label>
                <select id="ni-disposition" class="form-select form-select-sm">
                    <option value="">— Select reason —</option>
                    @foreach(Statuses::NOT_ISSUED_DISPOSITIONS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="ni-confirm-btn">Mark Not Issued</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function() {
    let currentLeadId = null;

    // ==== Actions Modal ====
    document.querySelectorAll('.btn-open-actions-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentLeadId = this.dataset.id;
            const name = this.dataset.name;
            const status = this.dataset.status;
            const policy = this.dataset.policy;
            const partner = this.dataset.partner;

            document.getElementById('actions-lead-name').textContent = name;
            document.getElementById('actions-status').value = status;
            document.getElementById('actions-policy-number').value = policy;
            document.getElementById('actions-app-id').value = currentLeadId;
            document.getElementById('actions-partner').value = partner;

            // Show/hide policy field based on status
            updatePolicyFieldVisibility(status);

            new bootstrap.Modal(document.getElementById('actionsModal')).show();
        });
    });

    // Toggle visibility of Policy Number field
    document.getElementById('actions-status').addEventListener('change', function() {
        updatePolicyFieldVisibility(this.value);
    });

    function updatePolicyFieldVisibility(status) {
        const wrapper = document.getElementById('policy-field-wrapper');
        if (status === 'approved') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }

    // Save Actions Modal changes
    document.getElementById('actions-save-btn').addEventListener('click', function() {
        const newStatus = document.getElementById('actions-status').value;
        const policyNumber = document.getElementById('actions-policy-number').value.trim();
        const partner = document.getElementById('actions-partner').value;

        // Update manager status
        fetch(`/submissions/${currentLeadId}/update-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({manager_status: newStatus})
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Error updating status');
                return;
            }
            
            // Update policy number if approved
            if (newStatus === 'approved' && policyNumber) {
                return fetch(`/submissions/${currentLeadId}/update-field`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({field: 'policy_number', value: policyNumber})
                }).then(r => r.json());
            }
            return Promise.resolve({success: true});
        })
        .then(data => {
            if (data && !data.success) {
                alert(data.message || 'Error updating policy number');
                return;
            }

            // Update partner if selected
            if (partner) {
                return fetch(`/submissions/${currentLeadId}/update-field`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({field: 'assigned_partner', value: partner})
                }).then(r => r.json());
            }
            return Promise.resolve({success: true});
        })
        .then(data => {
            if (data && !data.success) {
                alert(data.message || 'Error updating partner');
            } else {
                bootstrap.Modal.getInstance(document.getElementById('actionsModal')).hide();
                location.reload();
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
        });
    });

    // ==== Send to Contract ====
    document.querySelectorAll('.btn-send-contract').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (!confirm('Send this lead to Pending Contract?')) return;
            fetch(`/submissions/${id}/send-to-contract`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) { location.reload(); }
                else { alert(data.message); }
            });
        });
    });

    // ==== Mark Not Issued — open modal ====
    document.querySelectorAll('.btn-mark-ni').forEach(btn => {
        btn.addEventListener('click', function() {
            currentLeadId = this.dataset.id;
            document.getElementById('ni-lead-name').textContent = this.dataset.name;
            document.getElementById('ni-disposition').value = '';
            new bootstrap.Modal(document.getElementById('niModal')).show();
        });
    });

    // ==== Confirm Not Issued ====
    document.getElementById('ni-confirm-btn').addEventListener('click', function() {
        const disposition = document.getElementById('ni-disposition').value;
        if (!disposition) { alert('Please select a disposition reason.'); return; }
        fetch(`/submissions/${currentLeadId}/mark-not-issued`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify({not_issued_disposition: disposition})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert(data.message); }
        });
    });

    // ==== Resolve Not Issued ====
    document.querySelectorAll('.btn-resolve-ni').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (!confirm('Mark this Not Issued block as resolved?')) return;
            fetch(`/submissions/${id}/resolve-not-issued`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) { location.reload(); }
                else { alert(data.message); }
            });
        });
    });
})();
</script>
@endsection
