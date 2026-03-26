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
.sec-card{padding:0;margin-bottom:.65rem;overflow:hidden;background:var(--bs-card-bg);border:1px solid rgba(255,255,255,.08);border-radius:.6rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);flex-wrap:wrap;gap:.4rem;}
.sec-hdr h6{margin:0;font-size:.78rem;font-weight:600;}
.ex-tbl{width:100%;font-size:.735rem;border-collapse:collapse;}
.ex-tbl thead th{padding:.35rem .6rem;font-weight:600;font-size:.68rem;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-surface-500);white-space:nowrap;border-bottom:1px solid rgba(0,0,0,.07);}
.ex-tbl tbody td{padding:.4rem .6rem;vertical-align:middle;border-bottom:1px solid rgba(0,0,0,.04);}
.ex-tbl tbody tr:last-child td{border-bottom:0;}
.bd-paid{background:rgba(52,195,143,.12);color:#1a8754;border:1px solid rgba(52,195,143,.25);font-size:.6rem;padding:.2rem .5rem;border-radius:.3rem;font-weight:600;}
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
        <div class="kpi-card k-green">
            <div class="k-val">${{ number_format($totalPremium, 2) }}</div>
            <div class="k-lbl">Monthly Premium</div>
        </div>
        <div class="kpi-card k-blue">
            <div class="k-val">${{ number_format($totalCoverage, 0) }}</div>
            <div class="k-lbl">Total Coverage</div>
        </div>
    </div>

    <div class="sec-card">
        <div class="sec-hdr">
            <h6><i class="bx bx-trophy me-1"></i> Paid Sales Records</h6>
            <span style="font-size:.68rem;color:var(--bs-surface-400);">Read-only</span>
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
                        <th>Phone</th>
                        <th>Carrier</th>
                        <th>Premium</th>
                        <th>Coverage</th>
                        <th>Status</th>
                        <th>Closer</th>
                        <th>Paid By</th>
                        <th>Paid At</th>
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
                            <td>
                                @if($lead->coverage_amount)
                                    ${{ number_format($lead->coverage_amount, 0) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="bd-paid">Paid</span></td>
                            <td>{{ $lead->closer_name ?? '—' }}</td>
                            <td>{{ $lead->paidBy->name ?? '—' }}</td>
                            <td>{{ $lead->paid_at ? $lead->paid_at->format('M d, Y') : '—' }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button class="a-btn btn-chargeback" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;background:rgba(220,53,69,.12);color:#dc3545;border-color:rgba(220,53,69,.3);">
                                        <i class="bx bx-error"></i> Chargeback
                                    </button>
                                    <button class="a-btn btn-send-back" data-id="{{ $lead->id }}" data-name="{{ $lead->cn_name }}" style="font-size:.63rem;background:rgba(100,116,139,.1);color:#64748b;border-color:rgba(100,116,139,.25);">
                                        <i class="bx bx-arrow-back"></i> Back
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4" style="color:var(--bs-surface-400);font-size:.75rem;">
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
})();
</script>
@endsection
