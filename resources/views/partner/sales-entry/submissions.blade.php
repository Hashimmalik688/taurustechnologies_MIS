@extends('layouts.partner')

@section('title') Submissions @endsection

@section('css')
<style>
    .psub-hdr h4{font-size:1.25rem;font-weight:900;color:#111827;margin:0 0 .25rem;}
    .psub-hdr p{font-size:.84rem;color:#6b7280;margin:0 0 1.25rem;}
    .psub-cards{display:grid;grid-template-columns:repeat(5,1fr);gap:.8rem;margin-bottom:1.25rem;}
    @media(max-width:768px){.psub-cards{grid-template-columns:repeat(2,1fr);}}
    .psub-card{background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:.55rem;padding:.85rem 1rem;box-shadow:0 1px 2px rgba(0,0,0,.05);}
    .psub-card .n{font-size:1.6rem;font-weight:900;color:#111827;line-height:1;}
    .psub-card .l{font-size:.68rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;margin-top:.35rem;}
    .psub-toolbar{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:.9rem;flex-wrap:wrap;}
    .psub-search{display:flex;gap:.4rem;}
    .psub-search input{border:1px solid rgba(0,0,0,.14);border-radius:.4rem;padding:.4rem .65rem;font-size:.83rem;min-width:220px;}
    .psub-search button{background:rgba(79,70,229,.08);border:1px solid rgba(79,70,229,.25);color:#4f46e5;border-radius:.4rem;padding:.4rem .8rem;font-size:.8rem;font-weight:700;cursor:pointer;}
    .psub-table-wrap{background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:.55rem;overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.06);}
    table.psub{width:100%;border-collapse:collapse;font-size:.83rem;}
    table.psub th{text-align:left;padding:.65rem .85rem;background:#f8fafc;font-size:.68rem;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;border-bottom:1px solid rgba(0,0,0,.07);white-space:nowrap;}
    table.psub td{padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.05);color:#374151;white-space:nowrap;}
    .badge{display:inline-block;padding:.2rem .55rem;border-radius:1rem;font-size:.68rem;font-weight:800;}
    .b-submitted{background:#eff6ff;color:#1d4ed8;}
    .b-review{background:#fefce8;color:#a16207;}
    .b-issued{background:#ecfdf5;color:#047857;}
    .b-notissued{background:#fff7ed;color:#c2410c;}
    .b-chargeback{background:#fef2f2;color:#b91c1c;}
    .psub-empty{padding:2.5rem;text-align:center;color:#9ca3af;font-size:.9rem;}
    .psub-pag{margin-top:1rem;}
</style>
@endsection

@section('content')
<div class="psub-hdr">
    <h4>Submissions</h4>
    <p>
        @if($partner->isCcPartner())
            Every sale submitted by your company and its closers, with live pipeline status.
        @else
            Sales you have submitted, with their live pipeline status.
        @endif
    </p>
</div>

<div class="psub-cards">
    <div class="psub-card"><div class="n">{{ $summary['total'] }}</div><div class="l">Total Sent</div></div>
    <div class="psub-card"><div class="n">{{ $summary['submitted'] }}</div><div class="l">In Progress</div></div>
    <div class="psub-card"><div class="n">{{ $summary['issued'] }}</div><div class="l">Issued</div></div>
    <div class="psub-card"><div class="n">{{ $summary['not_issued'] }}</div><div class="l">Not Issued</div></div>
    <div class="psub-card"><div class="n">{{ $summary['chargeback'] }}</div><div class="l">Chargeback</div></div>
</div>

<div class="psub-toolbar">
    <form method="GET" class="psub-search">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search customer name…">
        <button type="submit"><i class="bx bx-search"></i> Search</button>
    </form>
    <a class="psub-search" href="{{ route('partner.sales.create') }}"><button type="button" style="background:#4f46e5;color:#fff;border-color:#4f46e5;"><i class="bx bx-plus"></i> New Sale</button></a>
</div>

<div class="psub-table-wrap">
    <table class="psub">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Carrier</th>
                <th>Premium</th>
                @if($partner->isCcPartner())<th>Closer</th>@endif
                <th>Submitted</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $lead)
                @php
                    if ($lead->status === \App\Support\Statuses::LEAD_CHARGEBACK) {
                        $label = 'Chargeback'; $cls = 'b-chargeback';
                    } elseif ($lead->issuance_status === 'Issued') {
                        $label = 'Issued'; $cls = 'b-issued';
                    } elseif ($lead->issuance_status === 'Not Issued') {
                        $label = 'Not Issued'; $cls = 'b-notissued';
                    } elseif ($lead->pending_contract_at) {
                        $label = 'In Review'; $cls = 'b-review';
                    } else {
                        $label = 'Submitted'; $cls = 'b-submitted';
                    }
                @endphp
                <tr>
                    <td>{{ $lead->cn_name }}</td>
                    <td>{{ $lead->phone_number }}</td>
                    <td>{{ optional($lead->carriers->first())->name ?? '—' }}</td>
                    <td>{{ $lead->monthly_premium ? '$'.number_format($lead->monthly_premium, 2) : '—' }}</td>
                    @if($partner->isCcPartner())<td>{{ optional($lead->partner)->name ?? $lead->closer_name }}</td>@endif
                    <td>{{ optional($lead->created_at)->format('M j, Y') }}</td>
                    <td><span class="badge {{ $cls }}">{{ $label }}</span></td>
                </tr>
            @empty
                <tr><td colspan="{{ $partner->isCcPartner() ? 7 : 6 }}" class="psub-empty">No submissions yet. <a href="{{ route('partner.sales.create') }}">Submit your first sale →</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="psub-pag">{{ $leads->links() }}</div>
@endsection
