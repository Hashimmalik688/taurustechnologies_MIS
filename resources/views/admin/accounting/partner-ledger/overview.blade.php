@extends('layouts.master')

@section('title', $partner->name . ' — Carrier Ledger Overview')

@section('css')
<style>
:root {
    --acct-gold:       #d4af37;
    --acct-gold-dark:  #b8941f;
    --acct-gold-light: #f5ecd0;
    --acct-header-bg:  #2d2d2d;
}

/* ── Partner header ── */
.partner-hdr {
    background: var(--acct-header-bg);
    border-bottom: 3px solid var(--acct-gold);
    border-radius: 6px 6px 0 0;
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
}
.partner-hdr-label  { font-size:.65rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--acct-gold); margin-bottom:3px; }
.partner-hdr-name   { font-size:1.3rem; font-weight:700; color:#fff; line-height:1.2; }
.partner-hdr-meta   { font-size:.78rem; color:#aaa; margin-top:4px; }

/* ── Summary strip ── */
.ov-summary-strip {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    border: 1px solid #dee2e6;
    border-top: none;
    background: #fff;
    margin-bottom: 24px;
}
@media (max-width:576px) { .ov-summary-strip { grid-template-columns: 1fr; } }
.ov-summary-cell { padding:12px 18px; border-right:1px solid #dee2e6; position:relative; }
.ov-summary-cell:last-child { border-right:none; }
.ov-summary-cell::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.ov-summary-cell.cell-dr::before  { background:#66bb6a; }
.ov-summary-cell.cell-cr::before  { background:#ef5350; }
.ov-summary-cell.cell-bal::before { background:var(--acct-gold); }
.ov-cell-label { font-size:.71rem; font-weight:600; letter-spacing:.07em; text-transform:uppercase; color:#6c757d; margin-bottom:3px; }
.ov-cell-value { font-size:1.1rem; font-weight:700; font-family:'Courier New',monospace; }
.ov-cell-value.dr-amt  { color:#2e7d32; }
.ov-cell-value.cr-amt  { color:#c62828; }
.ov-cell-value.bal-pos { color:#2e7d32; }
.ov-cell-value.bal-neg { color:#c62828; }

/* ── Carrier cards ── */
.carrier-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 12px;
    margin-top: 6px;
}
.carrier-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 16px 18px;
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: block;
    color: inherit;
}
.carrier-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: transparent;
    transition: background .15s;
}
.carrier-card:hover { border-color:var(--acct-gold); box-shadow:0 3px 12px rgba(212,175,55,.2); transform:translateY(-2px); color:inherit; }
.carrier-card:hover::before { background:var(--acct-gold); }
.carrier-card.no-tx { opacity:.75; }
.cc-name { font-size:.96rem; font-weight:700; color:#2d2d2d; margin-bottom:10px; padding-right:24px; }
.cc-name .cc-unassigned { color:#aaa; font-style:italic; font-weight:500; }
.cc-financials { display:flex; gap:10px; margin-bottom:8px; }
.cc-fin-cell { flex:1; }
.cc-fin-label { font-size:.62rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#aaa; margin-bottom:1px; }
.cc-fin-val { font-family:'Courier New',monospace; font-size:.88rem; font-weight:700; }
.cc-fin-val.dr { color:#2e7d32; }
.cc-fin-val.cr { color:#c62828; }
.cc-balance-row { display:flex; align-items:center; justify-content:space-between; border-top:1px solid #f1f3f5; padding-top:8px; margin-top:4px; }
.cc-balance-label { font-size:.7rem; color:#888; }
.cc-balance-val { font-family:'Courier New',monospace; font-size:1rem; font-weight:700; }
.cc-balance-val.pos { color:#2e7d32; }
.cc-balance-val.neg { color:#c62828; }
.cc-badge-side { font-size:.65rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; background:#f1f3f5; color:#888; border-radius:3px; padding:2px 6px; margin-left:6px; }
.cc-tx-count { font-size:.72rem; color:#aaa; }
.cc-arrow { position:absolute; right:12px; top:16px; color:#ccc; font-size:1.2rem; transition:color .15s; }
.carrier-card:hover .cc-arrow { color:var(--acct-gold); }

/* ── Section label ── */
.section-label {
    font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#888;
    margin-bottom:10px; display:flex; align-items:center; gap:6px;
}
.section-label::after { content:''; flex:1; height:1px; background:#e9ecef; }

/* ── Quick action buttons ── */
.quick-actions { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.btn-qa {
    font-size:.8rem; font-weight:600; padding:6px 14px; border-radius:4px;
    display:inline-flex; align-items:center; gap:5px; cursor:pointer;
    text-decoration:none; transition:background .15s;
    border:1px solid;
}
.btn-qa-gold   { background:var(--acct-gold); border-color:var(--acct-gold); color:#1a1a1a; }
.btn-qa-gold:hover { background:var(--acct-gold-dark); border-color:var(--acct-gold-dark); color:#fff; }
.btn-qa-green  { background:#e8f5e9; border-color:#a5d6a7; color:#2e7d32; }
.btn-qa-green:hover { background:#2e7d32; color:#fff; border-color:#2e7d32; }
.btn-qa-outline { background:#fff; border-color:#dee2e6; color:#6c757d; }
.btn-qa-outline:hover { background:#f8f9fa; color:#2d2d2d; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <div class="d-flex align-items-center gap-2 mb-3" style="font-size:.82rem;color:#888;">
        <a href="{{ route('admin.accounting.journal.index') }}" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            <i class="bx bx-book-open me-1"></i>Journal
        </a>
        <i class="bx bx-chevron-right"></i>
        <a href="{{ route('admin.accounting.partner-ledger') }}" style="color:var(--acct-gold-dark);text-decoration:none;font-weight:600;">
            Partner Ledger
        </a>
        <i class="bx bx-chevron-right"></i>
        <span style="color:#495057;font-weight:600;">{{ $partner->name }}</span>
    </div>

    {{-- Partner header --}}
    <div class="partner-hdr">
        <div>
            <div class="partner-hdr-label">Partner Account · Carrier Breakdown</div>
            <div class="partner-hdr-name">{{ $partner->name }}</div>
            <div class="partner-hdr-meta">
                @if($partner->code)
                    <span style="color:var(--acct-gold);font-weight:600;">{{ $partner->code }}</span>
                    &nbsp;·&nbsp;
                @endif
                {{ $carrierSummaries->count() }} carrier ledger(s)
                &nbsp;·&nbsp; AR Account <strong style="color:#ddd;">1200</strong>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
            @canEditModule('accounting')
            <a href="{{ route('admin.accounting.record-sale') }}"
               class="btn-qa btn-qa-gold" style="font-size:.78rem;">
                <i class="bx bx-purchase-tag"></i> Sale
            </a>
            <a href="{{ route('admin.accounting.record-payment') }}"
               class="btn-qa btn-qa-green" style="font-size:.78rem;">
                <i class="bx bx-money"></i> Payment
            </a>
            @endcanEditModule
        </div>
    </div>

    {{-- Overall summary strip --}}
    <div class="ov-summary-strip">
        <div class="ov-summary-cell cell-dr">
            <div class="ov-cell-label">Total Debits (All Carriers)</div>
            <div class="ov-cell-value dr-amt">${{ number_format($totalDr, 2) }}</div>
        </div>
        <div class="ov-summary-cell cell-cr">
            <div class="ov-cell-label">Total Credits (All Carriers)</div>
            <div class="ov-cell-value cr-amt">${{ number_format($totalCr, 2) }}</div>
        </div>
        <div class="ov-summary-cell cell-bal">
            @php $nb = $netBalance; @endphp
            <div class="ov-cell-label">Net Balance (All Carriers)</div>
            <div class="ov-cell-value {{ $nb >= 0 ? 'bal-pos' : 'bal-neg' }}">
                ${{ number_format(abs($nb), 2) }}
                <small style="font-size:.65rem;font-family:sans-serif;color:#999;margin-left:2px;">
                    {{ $nb >= 0 ? 'Dr' : 'Cr' }}
                </small>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    @canEditModule('accounting')
    <div class="quick-actions">
        <a href="{{ route('admin.accounting.opening-balance') }}"
           class="btn-qa btn-qa-outline">
            <i class="bx bx-align-left"></i> Record Opening Balance
        </a>
    </div>
    @endcanEditModule

    {{-- Explanation banner --}}
    <div style="background:#fffbea;border:1px solid #ffe082;border-radius:5px;padding:10px 16px;margin-bottom:16px;font-size:.82rem;color:#7a5c00;display:flex;align-items:center;gap:8px;">
        <i class="bx bx-info-circle" style="font-size:1.1rem;flex-shrink:0;"></i>
        <span>
            Each card below is a <strong>separate ledger</strong> for this partner under that insurance carrier.
            When recording a Sale or Payment, select a carrier to have that transaction appear in that carrier's own ledger.
            Entries recorded without a carrier appear in the <strong>Unassigned</strong> ledger.
        </span>
    </div>

    {{-- Carrier cards --}}
    <div class="section-label">
        <i class="bx bx-buildings" style="color:var(--acct-gold);"></i>
        Carrier Ledgers ({{ $carrierSummaries->count() }})
    </div>

    @if($carrierSummaries->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bx bx-folder-open d-block mb-2" style="font-size:2.5rem;color:#dee2e6;"></i>
            <div style="font-size:.875rem;">No carriers linked to this partner yet.</div>
            <div style="font-size:.78rem;margin-top:4px;">
                Record a sale or payment with a carrier, or link carriers in the partner profile.
            </div>
        </div>
    @else
    <div class="carrier-cards-grid">
        @foreach($carrierSummaries as $cs)
        @php
            $url = $cs['carrier_id'] > 0
                ? route('admin.accounting.partner-ledger.carrier.show', [$partner->id, $cs['carrier_id']])
                : route('admin.accounting.partner-ledger.carrier.show', [$partner->id, 0]);
            $bal = $cs['balance'];
        @endphp
        <a href="{{ $url }}" class="carrier-card {{ $cs['tx_count'] === 0 ? 'no-tx' : '' }}">
            <i class="bx bx-chevron-right cc-arrow"></i>
            <div class="cc-name">
                @if($cs['carrier_id'] === 0)
                    <span class="cc-unassigned">{{ $cs['carrier_name'] }}</span>
                @else
                    {{ $cs['carrier_name'] }}
                @endif
            </div>
            <div class="cc-financials">
                <div class="cc-fin-cell">
                    <div class="cc-fin-label">Debit (USD)</div>
                    <div class="cc-fin-val dr">{{ number_format($cs['total_dr'], 2) }}</div>
                </div>
                <div class="cc-fin-cell">
                    <div class="cc-fin-label">Credit (USD)</div>
                    <div class="cc-fin-val cr">{{ number_format($cs['total_cr'], 2) }}</div>
                </div>
            </div>
            <div class="cc-balance-row">
                <div>
                    <span class="cc-balance-val {{ $bal >= 0 ? 'pos' : 'neg' }}">
                        ${{ number_format(abs($bal), 2) }}
                    </span>
                    <span class="cc-badge-side">{{ $bal >= 0 ? 'Dr' : 'Cr' }}</span>
                </div>
                <div class="cc-tx-count">
                    {{ $cs['tx_count'] }} txn{{ $cs['tx_count'] !== 1 ? 's' : '' }}
                    &nbsp;·&nbsp;
                    <span style="color:var(--acct-gold-dark);font-weight:600;">View Ledger →</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection
