@use('App\Support\Statuses')
@extends('layouts.master')

@section('title', 'Pending Draft')

@section('css')
<style>
.kpi-row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.65rem;}
.kpi-card{flex:1 1 80px;min-width:75px;padding:.65rem .6rem;border-radius:.55rem;text-align:center;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.06);transition:transform .15s;}
.kpi-card:hover{transform:translateY(-2px);}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.55rem .55rem 0 0;}
.kpi-card .k-val{font-size:1.35rem;font-weight:700;line-height:1;}
.kpi-card .k-lbl{font-size:.58rem;text-transform:uppercase;font-weight:600;letter-spacing:.4px;color:var(--bs-surface-500);margin-top:.2rem;}
.kpi-card.k-gold{background:rgba(212,175,55,.06)}.kpi-card.k-gold::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}.kpi-card.k-gold .k-val{color:#b89730}
.kpi-card.k-green{background:rgba(52,195,143,.06)}.kpi-card.k-green::before{background:linear-gradient(90deg,#34c38f,#6eddb8)}.kpi-card.k-green .k-val{color:#1a8754}
.kpi-card.k-red{background:rgba(244,106,106,.06)}.kpi-card.k-red::before{background:linear-gradient(90deg,#f46a6a,#f89b9b)}.kpi-card.k-red .k-val{color:#c84646}
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val{color:#556ee6}
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;}
.ex-tbl{width:100%;font-size:.735rem;border-collapse:collapse;}
.ex-tbl thead th{padding:.35rem .6rem;font-weight:600;font-size:.68rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);white-space:nowrap;border-bottom:1px solid rgba(0,0,0,.07);}
.ex-tbl tbody td{padding:.4rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.04);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}
.bd-np{background:rgba(244,106,106,.12);color:#c84646;border:1px solid rgba(244,106,106,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-pd{background:rgba(108,117,125,.12);color:#495057;border:1px solid rgba(108,117,125,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-wait{background:rgba(241,180,76,.1);color:#b87a14;border:1px solid rgba(241,180,76,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.a-btn{display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.a-paid{background:#34c38f20;color:#1a8754;border-color:#34c38f40;}.a-paid:hover{background:#34c38f30;}
.a-fdfp{background:#f46a6a20;color:#c84646;border-color:#f46a6a40;}.a-fdfp:hover{background:#f46a6a30;}
.a-died{background:#6c757d20;color:#495057;border-color:#6c757d40;}.a-died:hover{background:#6c757d30;}
.a-clear{background:#556ee620;color:#556ee6;border-color:#556ee640;}.a-clear:hover{background:#556ee630;}
.filter-form{display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);}
.filter-form .form-control,.filter-form .form-select{font-size:.72rem;padding:.3rem .5rem;height:2rem;}
.filter-form label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);margin-bottom:.15rem;}
.f-reset{font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;}
.pd-tabs{display:flex;gap:0;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);}
.pd-tab{padding:.3rem .75rem;font-size:.72rem;font-weight:500;border-radius:.35rem;cursor:pointer;text-decoration:none;color:var(--bs-surface-400);background:transparent;border:1px solid transparent;transition:all .15s;}
.pd-tab.active{background:var(--bs-primary);color:#fff;border-color:var(--bs-primary);}
</style>
@endsection

@section('content')
<div class="container-fluid px-3 py-3" style="max-width:1600px">

    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h5 class="mb-0 fw-semibold" style="font-size:1rem;">
                <i class="bx bx-time-five me-1" style="color:#f1b44c;font-size:1.05rem;"></i>
                Pending Draft
            </h5>
            <p class="mb-0" style="font-size:.68rem;color:var(--bs-surface-400);">
                Stage 6 — Awaiting first premium draft confirmation
            </p>
        </div>
        <div class="d-flex gap-1">
            <a href="{{ route('paid-sales.index') }}" class="a-btn" style="background:var(--bs-card-bg);border:1px solid rgba(0,0,0,.08);">
                <i class="bx bx-badge-check"></i> Paid Sales
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <div class="k-val">{{ $totalCount }}</div>
            <div class="k-lbl">Total</div>
        </div>
        <div class="kpi-card k-blue">
            <div class="k-val">{{ $pendingCount }}</div>
            <div class="k-lbl">Awaiting Draft</div>
        </div>
        <div class="kpi-card k-red">
            <div class="k-val">{{ $notPaidCount }}</div>
            <div class="k-lbl">Not Paid (FDFP)</div>
        </div>
    </div>

    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-receipt me-1"></i> Pending Draft Leads</h6>
        </div>

        {{-- Tab switcher --}}
        <div class="pd-tabs">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}"
               class="pd-tab {{ $tab === 'pending' ? 'active' : '' }}">
                Awaiting Draft <span class="ms-1" style="font-size:.65rem;">({{ $pendingCount }})</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'not_paid']) }}"
               class="pd-tab ms-1 {{ $tab === 'not_paid' ? 'active' : '' }}">
                Not Paid / FDFP <span class="ms-1" style="font-size:.65rem;">({{ $notPaidCount }})</span>
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('pending-draft.index') }}" class="filter-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div>
                <label>Search</label>
                <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Name, phone…" style="width:150px;">
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
            <button type="submit" class="a-btn a-paid" style="height:2rem;"><i class="bx bx-search-alt-2"></i> Filter</button>
            <a href="{{ route('pending-draft.index') }}" class="f-reset"><i class="bx bx-reset"></i> Clear</a>
        </form>

        <div class="table-responsive">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Carrier</th>
                        <th>Premium</th>
                        <th>Closer</th>
                        <th>Followup Done</th>
                        @if($tab === 'not_paid')
                        <th>FDFP Type</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
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
                            <td>
                                {{ $lead->followup_done_at ? $lead->followup_done_at->format('M d, Y') : '—' }}
                                @if($lead->followupDoneBy)
                                    <div style="font-size:.6rem;color:var(--bs-surface-400);">by {{ $lead->followupDoneBy->name }}</div>
                                @endif
                            </td>
                            @if($tab === 'not_paid')
                            <td>
                                <span class="bd-np">
                                    {{ $fdfpTypes[$lead->not_paid_fdfp_type] ?? $lead->not_paid_fdfp_type }}
                                </span>
                                @if($lead->not_paid_fdfp_type === 'manual_action' && $lead->not_paid_manual_disposition)
                                    <div style="font-size:.6rem;margin-top:.1rem;color:var(--bs-surface-400);">
                                        → {{ $niDispositions[$lead->not_paid_manual_disposition] ?? $lead->not_paid_manual_disposition }}
                                    </div>
                                @endif
                                @if($lead->notPaidBy)
                                    <div style="font-size:.6rem;color:var(--bs-surface-400);">by {{ $lead->notPaidBy->name }}</div>
                                @endif
                            </td>
                            @endif
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @canDo('pending-draft', 'full')
                                        @if(empty($lead->not_paid_at))
                                        <button class="a-btn a-paid btn-mark-paid" data-id="{{ $lead->id }}">
                                            <i class="bx bx-badge-check"></i> Mark Paid
                                        </button>
                                        @else
                                        <button class="a-btn a-paid btn-mark-paid" data-id="{{ $lead->id }}">
                                            <i class="bx bx-badge-check"></i> Mark Paid
                                        </button>
                                        @endif
                                        <button class="a-btn a-died btn-policy-died" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                            <i class="bx bx-x-circle"></i> Policy Died
                                        </button>
                                    @endcanDo
                                    @canDo('pending-draft', 'edit')
                                        @if(empty($lead->not_paid_at))
                                        <button class="a-btn a-fdfp btn-mark-fdfp" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                            <i class="bx bx-error"></i> Not Paid
                                        </button>
                                        @else
                                        <button class="a-btn a-clear btn-clear-np" data-id="{{ $lead->id }}">
                                            <i class="bx bx-undo"></i> Clear
                                        </button>
                                        @endif
                                    @endcanDo
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.4;"></i>
                                No leads in this queue for the selected period.
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

{{-- FDFP Modal --}}
<div class="modal fade" id="fdfpModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-error me-1 text-danger"></i> Mark as Not Paid (FDFP)
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="fdfp-lead-name"></strong>
                </p>
                <div class="mb-2">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">FDFP Type</label>
                    <select id="fdfp-type" class="form-select form-select-sm">
                        <option value="">— Select type —</option>
                        @foreach($fdfpTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="manual-disposition-wrap" style="display:none;">
                    <label class="form-label" style="font-size:.72rem;font-weight:600;">Manual Action — Select Disposition</label>
                    <select id="fdfp-manual" class="form-select form-select-sm">
                        <option value="">— Select disposition —</option>
                        @foreach($niDispositions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="fdfp-confirm-btn">Confirm Not Paid</button>
            </div>
        </div>
    </div>
</div>

{{-- Policy Died Modal --}}
<div class="modal fade" id="pdModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0" style="font-size:.85rem;">
                    <i class="bx bx-x-circle me-1 text-secondary"></i> Mark as Policy Died
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <p class="mb-2" style="font-size:.75rem;color:var(--bs-surface-500);">
                    Lead: <strong id="pd-lead-name"></strong>
                </p>
                <p class="mb-2" style="font-size:.72rem;color:#c84646;">
                    <i class="bx bx-info-circle me-1"></i>
                    Policy Died leads are re-dialable. The lead will be reset to <strong>Active</strong> and return to the Ravens queue.
                </p>
                <label class="form-label" style="font-size:.72rem;font-weight:600;">Reason</label>
                <select id="pd-reason" class="form-select form-select-sm">
                    <option value="">— Select reason —</option>
                    @foreach(\App\Support\Statuses::POLICY_DIED_REASONS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-dark" id="pd-confirm-btn">Confirm Policy Died</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function() {
    let currentId = null;

    // Mark Paid
    document.querySelectorAll('.btn-mark-paid').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Mark this lead as Paid and move to Paid Sales?')) return;
            post(`/pending-draft/${this.dataset.id}/mark-paid`, {});
        });
    });

    // FDFP Modal
    document.querySelectorAll('.btn-mark-fdfp').forEach(btn => {
        btn.addEventListener('click', function() {
            currentId = this.dataset.id;
            document.getElementById('fdfp-lead-name').textContent = this.dataset.name;
            document.getElementById('fdfp-type').value = '';
            document.getElementById('fdfp-manual').value = '';
            document.getElementById('manual-disposition-wrap').style.display = 'none';
            new bootstrap.Modal(document.getElementById('fdfpModal')).show();
        });
    });

    document.getElementById('fdfp-type').addEventListener('change', function() {
        document.getElementById('manual-disposition-wrap').style.display = this.value === 'manual_action' ? 'block' : 'none';
    });

    document.getElementById('fdfp-confirm-btn').addEventListener('click', function() {
        const type = document.getElementById('fdfp-type').value;
        const manual = document.getElementById('fdfp-manual').value;
        if (!type) { alert('Please select an FDFP type.'); return; }
        if (type === 'manual_action' && !manual) { alert('Please select a manual action disposition.'); return; }
        post(`/pending-draft/${currentId}/mark-not-paid`, {not_paid_fdfp_type: type, not_paid_manual_disposition: manual || null});
    });

    // Policy Died Modal
    document.querySelectorAll('.btn-policy-died').forEach(btn => {
        btn.addEventListener('click', function() {
            currentId = this.dataset.id;
            document.getElementById('pd-lead-name').textContent = this.dataset.name;
            document.getElementById('pd-reason').value = '';
            new bootstrap.Modal(document.getElementById('pdModal')).show();
        });
    });

    document.getElementById('pd-confirm-btn').addEventListener('click', function() {
        const reason = document.getElementById('pd-reason').value;
        if (!reason) { alert('Please select a reason.'); return; }
        post(`/pending-draft/${currentId}/mark-policy-died`, {policy_died_reason: reason});
    });

    // Clear Not Paid
    document.querySelectorAll('.btn-clear-np').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Clear the Not Paid flag?')) return;
            post(`/pending-draft/${this.dataset.id}/clear-not-paid`, {});
        });
    });

    function post(url, data) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); else alert(d.message); });
    }
})();
</script>
@endsection
