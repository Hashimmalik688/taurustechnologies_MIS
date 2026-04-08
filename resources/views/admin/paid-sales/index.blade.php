@extends('layouts.master')

@section('title', 'Paid Sales')

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
.kpi-card.k-blue{background:rgba(85,110,230,.06)}.kpi-card.k-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}.kpi-card.k-blue .k-val{color:#556ee6}
.kpi-card.k-purple{background:rgba(114,46,209,.06)}.kpi-card.k-purple::before{background:linear-gradient(90deg,#722ed1,#9d5ef5)}.kpi-card.k-purple .k-val{color:#722ed1}
.kpi-card.k-orange{background:rgba(240,150,9,.06)}.kpi-card.k-orange::before{background:linear-gradient(90deg,#f09609,#f5b83e)}.kpi-card.k-orange .k-val{color:#c47a05}
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;}
.ex-tbl{width:100%;font-size:.735rem;border-collapse:collapse;}
.ex-tbl thead th{padding:.35rem .6rem;font-weight:600;font-size:.68rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);white-space:nowrap;border-bottom:1px solid rgba(0,0,0,.07);}
.ex-tbl tbody td{padding:.4rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.04);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}
.ex-tbl tbody tr.tr-chargeback td{background:rgba(220,53,69,.04) !important;border-bottom-color:rgba(220,53,69,.08);}
.ex-tbl tbody tr.tr-chargeback:hover td{background:rgba(220,53,69,.08) !important;}
.ex-tbl tbody tr.tr-chargeback td:first-child{border-left:3px solid #dc3545;}
.bd-paid{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
.bd-posted{background:rgba(99,102,241,.12);color:#4338ca;border:1px solid rgba(99,102,241,.3);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;display:inline-flex;align-items:center;gap:.2rem;white-space:nowrap;}
.bd-posted i{font-size:.7rem;}
.btn-post-ledger{font-size:.62rem;background:rgba(99,102,241,.12);color:#4338ca;border:1px solid rgba(99,102,241,.3);}
.btn-post-ledger:hover{background:rgba(99,102,241,.22);}
.kpi-card.k-indigo{background:rgba(99,102,241,.06)}.kpi-card.k-indigo::before{background:linear-gradient(90deg,#6366f1,#818cf8)}.kpi-card.k-indigo .k-val{color:#4338ca}
.kpi-card.k-red{background:rgba(220,53,69,.06)}.kpi-card.k-red::before{background:linear-gradient(90deg,#dc3545,#f56475)}.kpi-card.k-red .k-val{color:#dc3545}
.a-btn{display:inline-flex;align-items:center;gap:.25rem;padding:.28rem .55rem;border-radius:.35rem;font-size:.68rem;font-weight:500;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:all .15s;}
.filter-form{display:flex;flex-wrap:wrap;gap:.4rem;align-items:flex-end;padding:.65rem .75rem;border-bottom:1px solid rgba(0,0,0,.04);}
.filter-form .form-control,.filter-form .form-select{font-size:.72rem;padding:.3rem .5rem;height:2rem;}
.filter-form label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);margin-bottom:.15rem;}
.f-reset{font-size:.68rem;color:var(--bs-surface-400);text-decoration:none;align-self:flex-end;padding:.3rem .5rem;}
/* Prominent page title */
.sl-page-title{font-size:1.35rem;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px;margin:0;}
.sl-page-title i{color:#d4af37;font-size:1.5rem;}
.sl-page-subtitle{font-size:.78rem;color:#94a3b8;margin:0;}
[data-bs-theme=dark] .sl-page-title,:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sl-page-title{color:#f1f5f9;}
</style>
@endsection

@section('content')
<div class="container-fluid px-3 py-3" style="max-width:1600px">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="sl-page-title"><i class="bx bx-badge-check"></i> Paid Sales</h1>
            <p class="sl-page-subtitle mt-1">Stage 7 — Successfully collected first draft payment</p>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <div class="k-val">{{ $totalCount }}</div>
            <div class="k-lbl">Total Paid</div>
        </div>
        <div class="kpi-card k-purple">
            <div class="k-val">${{ number_format($totalCommission, 2) }}</div>
            <div class="k-lbl">Total Commission</div>
        </div>
        <div class="kpi-card k-orange">
            <div class="k-val">${{ number_format($totalOurShare, 2) }}</div>
            <div class="k-lbl">Our Share</div>
        </div>
        <div class="kpi-card k-indigo">
            <div class="k-val">{{ $unpostedCount }}</div>
            <div class="k-lbl">Not in Ledger</div>
        </div>
        <a href="{{ route('chargebacks.index') }}" class="kpi-card k-red" style="text-decoration:none;" title="View all chargebacks">
            <div class="k-val">{{ $chargebackCount }}</div>
            <div class="k-lbl">Sent to Chargeback</div>
        </a>
    </div>

    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-trophy me-1"></i> Paid Sales Records</h6>
            <div class="d-flex align-items-center gap-2">
                @canViewModule('accounting')
                @if($unpostedCount > 0)
                <button id="btn-post-all" class="a-btn" style="font-size:.63rem;background:rgba(99,102,241,.15);color:#4338ca;border-color:rgba(99,102,241,.3);">
                    <i class="bx bx-book-open"></i> Post All to Ledger
                    <span class="badge rounded-pill" style="background:#4338ca;color:#fff;font-size:.58rem;padding:.15rem .4rem;margin-left:.2rem;">{{ $unpostedCount }}</span>
                </button>
                @endif
                @endcanViewModule
                <a href="{{ route('admin.accounting.dashboard') }}" class="a-btn" style="font-size:.63rem;background:rgba(212,175,55,.1);color:#b89730;border-color:rgba(212,175,55,.25);">
                    <i class="bx bx-line-chart"></i> Accounting
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('paid-sales.index') }}" class="filter-form">
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
                <label>Partner</label>
                <select name="partner" class="form-select" style="width:150px;">
                    <option value="">All Partners</option>
                    @foreach($partners as $p)
                        <option value="{{ $p->id }}" {{ $partnerId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
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
            <button type="submit" class="a-btn" style="background:rgba(52,195,143,.2);color:#1a8754;border-color:rgba(52,195,143,.3);height:2rem;">
                <i class="bx bx-search-alt-2"></i> Filter
            </button>
            <a href="{{ route('paid-sales.index') }}" class="f-reset"><i class="bx bx-reset"></i> Clear</a>
        </form>

        <div class="table-responsive">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Policy Number</th>
                        <th>Partner</th>
                        <th>Carrier</th>
                        <th>Premium</th>
                        <th>Commission</th>
                        <th>Our Share</th>
                        <th>Coverage</th>
                        <th>Status</th>
                        <th>Ledger</th>
                        <th>Closer</th>
                        <th>Paid By</th>
                        <th>Paid At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr class="{{ $lead->status === 'chargeback' ? 'tr-chargeback' : '' }}">
                            <td style="color:var(--bs-surface-400);">{{ $lead->id }}</td>
                            <td>
                                <a href="{{ route('issuance.show', $lead->id) }}" style="font-weight:500;font-size:.73rem;">
                                    {{ $lead->cn_name ?? '—' }}
                                </a>
                            </td>
                            <td>{{ $lead->policy_number ?? '—' }}</td>
                            <td>
                                @if($lead->partner)
                                    <span style="font-size:.7rem;font-weight:500;">{{ $lead->partner->name }}</span>
                                    @if($lead->partner->code)
                                        <span style="font-size:.6rem;color:var(--bs-surface-400);display:block;">{{ $lead->partner->code }}</span>
                                    @endif
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>{{ $lead->carrier_name ?? ($lead->insuranceCarrier->name ?? '—') }}</td>
                            <td>${{ number_format($lead->monthly_premium, 2) }}</td>
                            <td>
                                @if($lead->calculated_commission > 0)
                                    <span style="color:#722ed1;font-weight:600;">${{ number_format($lead->calculated_commission, 2) }}</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->calculated_our_share > 0)
                                    @php $sharePct = $lead->partner ? ($lead->partner->our_commission_percentage ?? 15.0) : 15.0; @endphp
                                    <span style="color:#c47a05;font-weight:600;">${{ number_format($lead->calculated_our_share, 2) }}</span>
                                    <span style="font-size:.6rem;color:var(--bs-surface-400);display:block;">{{ rtrim(rtrim(number_format($sharePct, 2), '0'), '.') }}%</span>
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->coverage_amount)
                                    ${{ number_format($lead->coverage_amount, 0) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($lead->status === 'chargeback')
                                    <span class="bd-paid" style="background:rgba(220,53,69,.12);color:#dc3545;border-color:rgba(220,53,69,.3);">Chargeback</span>
                                @else
                                    <span class="bd-paid">Paid</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->ledger_journal_entry_id && $lead->ledgerJournalEntry)
                                    <a href="{{ route('admin.accounting.journal.show', $lead->ledger_journal_entry_id) }}" target="_blank"
                                       class="bd-posted" title="{{ $lead->ledgerJournalEntry->entry_number }}">
                                        <i class="bx bx-check-circle"></i> {{ $lead->ledgerJournalEntry->entry_number }}
                                    </a>
                                @else
                                    @canViewModule('accounting')
                                    <button class="a-btn btn-post-ledger" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}">
                                        <i class="bx bx-upload"></i> Post
                                    </button>
                                    @else
                                    <span style="color:var(--bs-surface-400);font-size:.65rem;">—</span>
                                    @endcanViewModule
                                @endif
                            </td>
                            <td>{{ $lead->closer_name ?? '—' }}</td>
                            <td>{{ $lead->paidBy->name ?? '—' }}</td>
                            <td>{{ $lead->paid_at ? $lead->paid_at->format('M d, Y') : '—' }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($lead->status === 'chargeback')
                                        @if($lead->ledger_chargeback_paid_entry_id)
                                            <span style="font-size:.63rem;background:rgba(5,150,105,.1);color:#059669;border:1px solid rgba(5,150,105,.3);border-radius:4px;padding:3px 8px;font-weight:600;">
                                                <i class="bx bx-check-circle"></i> Recovered
                                            </span>
                                        @else
                                            @canEditModule('accounting')
                                            <button class="a-btn btn-chargeback-paid" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;background:rgba(5,150,105,.12);color:#059669;border-color:rgba(5,150,105,.3);">
                                                <i class="bx bx-dollar"></i> Mark Paid
                                            </button>
                                            @endcanEditModule
                                        @endif
                                    @else
                                        <button class="a-btn btn-chargeback" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;background:rgba(220,53,69,.12);color:#dc3545;border-color:rgba(220,53,69,.3);">
                                            <i class="bx bx-error"></i> Chargeback
                                        </button>
                                        <button class="a-btn btn-send-back" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;background:rgba(100,116,139,.1);color:#64748b;border-color:rgba(100,116,139,.25);">
                                            <i class="bx bx-arrow-back"></i> Back
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
                                <i class="bx bx-inbox" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.4;"></i>
                                No paid sales records for the selected period.
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
@endsection

@section('script')
<script>
(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ── Post single sale to Ledger ──
    document.querySelectorAll('.btn-post-ledger').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.dataset.processing === 'true') return;
            const id   = this.dataset.id;
            const name = this.dataset.name;
            this.dataset.processing = 'true';
            if (!confirm('Post "' + name + '" commission to the accounting ledger?\nA journal entry (Dr AR / Cr Sales) will be created.')) {
                this.dataset.processing = 'false';
                return;
            }
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            fetch('/paid-sales/' + id + '/post-to-ledger', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    button.disabled = false;
                    button.innerHTML = '<i class="bx bx-upload"></i> Post';
                    button.dataset.processing = 'false';
                    alert(data.message || 'Error posting to ledger.');
                }
            })
            .catch(err => {
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-upload"></i> Post';
                button.dataset.processing = 'false';
                alert('Network error: ' + err.message);
            });
        });
    });

    // ── Post All Unposted Sales to Ledger ──
    const postAllBtn = document.getElementById('btn-post-all');
    if (postAllBtn) {
        postAllBtn.addEventListener('click', function() {
            if (this.dataset.processing === 'true') return;
            const params = new URLSearchParams(window.location.search);
            const dateFrom = params.get('date_from') || '';
            const dateTo   = params.get('date_to') || '';
            const countBadge = this.querySelector('.badge');
            const count = countBadge ? parseInt(countBadge.textContent) : '?';
            if (!confirm('Post ' + count + ' unposted sale(s) to the accounting ledger?\n\n• Creates journal entries (Dr AR / Cr Sales) for each sale.\n• Sales already in the ledger are skipped.\n• This processes up to 200 records.')) return;
            this.dataset.processing = 'true';
            this.disabled = true;
            this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Posting…';
            const body = {};
            if (dateFrom) body.date_from = dateFrom;
            if (dateTo)   body.date_to   = dateTo;
            fetch('/paid-sales/post-all-to-ledger', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message + (data.errors && data.errors.length ? '\n\nErrors:\n' + data.errors.slice(0,5).join('\n') : ''));
                    location.reload();
                } else {
                    this.disabled = false;
                    this.innerHTML = '<i class="bx bx-book-open"></i> Post All to Ledger';
                    this.dataset.processing = 'false';
                    alert(data.message || 'Error.');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.innerHTML = '<i class="bx bx-book-open"></i> Post All to Ledger';
                this.dataset.processing = 'false';
                alert('Network error: ' + err.message);
            });
        });
    }

    // ── Mark as Chargeback ──
    document.querySelectorAll('.btn-chargeback').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.dataset.processing === 'true') return;
            const id = this.dataset.id;
            const name = this.dataset.name;
            this.dataset.processing = 'true';
            if (!confirm('Mark "' + name + '" as Chargeback?\nThis will move the lead to Retention for follow-up.')) {
                this.dataset.processing = 'false';
                return;
            }
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            fetch('/paid-sales/' + id + '/mark-chargeback', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-error"></i> Chargeback';
                    btn.dataset.processing = 'false';
                    alert(data.message || 'Error.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-error"></i> Chargeback';
                btn.dataset.processing = 'false';
                alert('Error: ' + err.message);
            });
        });
    });

    // ── Send Back to Previous Stage ──
    document.querySelectorAll('.btn-send-back').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (this.dataset.processing === 'true') return;
            const id = this.dataset.id;
            const name = this.dataset.name;
            this.dataset.processing = 'true';
            if (!confirm('Send "' + name + '" back to Pending Draft?')) {
                this.dataset.processing = 'false';
                return;
            }
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            fetch('/leads/' + id + '/send-to-previous-stage', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    button.disabled = false;
                    button.innerHTML = '<i class="bx bx-arrow-back"></i> Back';
                    button.dataset.processing = 'false';
                    alert(data.message || 'Error sending back.');
                }
            })
            .catch(err => {
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-arrow-back"></i> Back';
                button.dataset.processing = 'false';
                alert('Error: ' + err.message);
            });
        });
    });

    // ── Mark Chargeback as Paid (Recovery) ──
    document.querySelectorAll('.btn-chargeback-paid').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.dataset.processing === 'true') return;
            const id   = this.dataset.id;
            const name = this.dataset.name;
            this.dataset.processing = 'true';
            if (!confirm('Mark chargeback for "' + name + '" as Paid?\n\nThis will post a Chargeback Recovery entry:\n  Dr 1200 Accounts Receivable\n  Cr 4100 Sales Income\n\nDated today (' + new Date().toLocaleDateString() + '). The original Sales Return remains for audit.')) {
                this.dataset.processing = 'false';
                return;
            }
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
            fetch('/paid-sales/' + id + '/mark-chargeback-paid', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Recovery posted: ' + data.entry_number + ' ($' + data.amount + ')');
                    location.reload();
                } else {
                    button.disabled = false;
                    button.innerHTML = '<i class="bx bx-dollar"></i> Mark Paid';
                    button.dataset.processing = 'false';
                    alert(data.message || 'Error recording recovery.');
                }
            })
            .catch(err => {
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-dollar"></i> Mark Paid';
                button.dataset.processing = 'false';
                alert('Error: ' + err.message);
            });
        });
    });
})();
</script>
@endsection
