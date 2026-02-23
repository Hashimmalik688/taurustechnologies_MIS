@extends('layouts.master')

@section('title')
    My Dashboard
@endsection

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
    /* Status mini-pills for sales table */
    .st-pill { display:inline-block;padding:.15rem .45rem;border-radius:10px;font-size:.62rem;font-weight:700;text-transform:capitalize; }
    .st-accepted { background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.15); }
    .st-underwritten { background:rgba(59,130,246,.1);color:#3b82f6;border:1px solid rgba(59,130,246,.15); }
    .st-pending { background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.15); }
    .st-declined { background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.15); }
    .st-chargeback { background:rgba(107,114,128,.1);color:#4b5563;border:1px solid rgba(107,114,128,.15); }
    /* Search input in filter bar */
    .pipe-search {
        font-size:.72rem; font-weight:600; padding:.32rem .55rem .32rem 1.8rem;
        border-radius:22px; border:1px solid rgba(0,0,0,.08);
        background:var(--bs-card-bg); color:var(--bs-surface-600);
        outline:none; min-width:160px; transition:border-color .15s;
    }
    .pipe-search:focus { border-color:#d4af37; box-shadow:0 0 0 2px rgba(212,175,55,.12); }
    .pipe-search-wrap { position:relative;display:inline-flex;align-items:center; }
    .pipe-search-wrap i { position:absolute;left:.55rem;font-size:.8rem;color:var(--bs-surface-400);pointer-events:none; }
</style>
@endsection

@section('content')
    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('ravens.dashboard') }}" id="filterForm" class="ex-card pipe-filter-bar">
        <a href="{{ route('ravens.dashboard', ['filter' => 'today']) }}" class="pipe-pill {{ ($filter ?? 'today') === 'today' ? 'active' : '' }}"><i class="bx bx-calendar"></i> Today</a>
        <span class="pipe-pill {{ ($filter ?? '') === 'custom' ? 'active' : '' }}" onclick="document.getElementById('customRange').style.display = document.getElementById('customRange').style.display === 'none' ? 'flex' : 'none'" style="cursor:pointer;"><i class="bx bx-calendar-event"></i> Custom Range</span>
        <span id="customRange" style="display:{{ ($filter ?? '') === 'custom' ? 'flex' : 'none' }};align-items:center;gap:.3rem;">
            <input type="hidden" name="filter" value="custom">
            <span class="pipe-pill-lbl">FROM</span>
            <input type="text" name="start_date" class="pipe-pill-date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD" readonly>
            <span class="pipe-pill-lbl">TO</span>
            <input type="text" name="end_date" class="pipe-pill-date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD" readonly>
            <button type="submit" class="pipe-pill-apply">Apply</button>
        </span>
        <div class="pipe-search-wrap">
            <i class="bx bx-search"></i>
            <input type="text" name="search" class="pipe-search" value="{{ $search ?? '' }}" placeholder="Search name, phone…">
        </div>
        @if(($filter ?? 'today') !== 'today' || !empty($search))
            <a href="{{ route('ravens.dashboard', ['filter' => 'today']) }}" class="pipe-pill-clear"><i class="bx bx-x"></i> Clear</a>
        @endif
    </form>

    {{-- KPI Cards --}}
    <div class="kpi-row">
        <div class="kpi-card k-blue ex-card">
            <i class="bx bx-phone k-icon"></i>
            <div class="k-val">{{ $stats['dialed'] ?? 0 }}</div>
            <div class="k-lbl">Dialed</div>
        </div>
        <div class="kpi-card k-teal ex-card">
            <i class="bx bx-phone-call k-icon"></i>
            <div class="k-val">{{ $stats['calls_connected'] ?? 0 }}</div>
            <div class="k-lbl">Connected</div>
        </div>
        <div class="kpi-card k-green ex-card">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val">{{ $stats['sales'] ?? 0 }}</div>
            <div class="k-lbl">Sales</div>
        </div>
        <div class="kpi-card k-gold ex-card">
            <i class="bx bx-trophy k-icon"></i>
            <div class="k-val">{{ $stats['mtd_sales'] ?? 0 }}</div>
            <div class="k-lbl">MTD Sales</div>
        </div>
    </div>

    {{-- My Sales Records --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#1a8754;">
            <i class="bx bx-dollar-circle" style="color:#34c38f;"></i> My Sales Records
            <span class="badge-count">{{ $mySales->total() ?? 0 }}</span>
        </div>

        @if(isset($mySales) && $mySales->count() > 0)
            {{-- Status Summary Chips --}}
            <div style="display:flex;gap:.4rem;flex-wrap:wrap;padding:.3rem .65rem .5rem;">
                <span class="st-pill st-accepted"><i class="bx bx-check"></i> Accepted: {{ $mySales->where('status','accepted')->count() }}</span>
                <span class="st-pill st-underwritten"><i class="bx bx-edit"></i> Underwritten: {{ $mySales->where('status','underwritten')->count() }}</span>
                <span class="st-pill st-pending"><i class="bx bx-time"></i> Pending: {{ $mySales->where('status','pending')->count() }}</span>
                <span class="st-pill st-declined"><i class="bx bx-x"></i> Declined: {{ $mySales->where('status','declined')->count() }}</span>
            </div>

            <div class="scroll-tbl" style="max-height:400px;">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Sale Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Coverage</th>
                            <th class="text-end">Premium</th>
                            <th>Carrier</th>
                            <th class="text-center">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mySales as $index => $sale)
                            <tr>
                                <td>{{ $mySales->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $sale->cn_name ?? 'N/A' }}</strong>
                                    @if($sale->phone_number)
                                        <br><span style="font-size:.62rem;color:var(--bs-surface-400);">{{ $sale->phone_number }}</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">{{ $sale->sale_at ? $sale->sale_at->setTimezone('America/Denver')->format('M d, h:i A') : ($sale->sale_date ? $sale->sale_date->format('M d, Y') : 'N/A') }}</td>
                                <td class="text-center">
                                    @php $stClass = 'st-'.($sale->status ?? 'pending'); @endphp
                                    <span class="st-pill {{ $stClass }}">{{ ucfirst($sale->status ?? 'pending') }}</span>
                                    @if($sale->qa_status)
                                        <br><span style="font-size:.55rem;color:var(--bs-surface-400);">QA: {{ $sale->qa_status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($sale->coverage_amount)
                                        <strong>${{ number_format($sale->coverage_amount, 0) }}</strong>
                                    @else
                                        <span style="color:var(--bs-surface-400);">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($sale->monthly_premium)
                                        <strong>${{ number_format($sale->monthly_premium, 2) }}</strong>
                                    @else
                                        <span style="color:var(--bs-surface-400);">—</span>
                                    @endif
                                </td>
                                <td>{{ $sale->carrier_name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('sales.index') }}?search={{ $sale->phone_number }}" class="act-btn a-primary" title="View in Sales"><i class="bx bx-show"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem .65rem;border-top:1px solid rgba(0,0,0,.04);font-size:.68rem;color:var(--bs-surface-400);">
                <span>Showing {{ $mySales->firstItem() }} to {{ $mySales->lastItem() }} of {{ $mySales->total() }}</span>
                <div>{{ $mySales->links() }}</div>
            </div>
        @else
            <div style="text-align:center;padding:2rem 1rem;color:var(--bs-surface-400);">
                <i class="bx bx-package" style="font-size:2rem;display:block;margin-bottom:.4rem;"></i>
                <p style="font-size:.8rem;font-weight:600;margin-bottom:.3rem;">No sales yet</p>
                <p style="font-size:.72rem;margin-bottom:.6rem;">Start calling leads to make your first sale!</p>
                <a href="{{ route('ravens.calling') }}" class="act-btn a-primary" style="padding:.35rem .75rem;">
                    <i class="bx bx-phone-call"></i> Start Calling
                </a>
            </div>
        @endif
    </div>

    {{-- Declined & Chargebacks --}}
    <div class="ex-card sec-card">
        <div class="pipe-hdr" style="color:#c84646;">
            <i class="bx bx-error-circle" style="color:#f46a6a;"></i> Declined & Chargebacks
            <span class="badge-count">{{ $declinedChargebacks->count() }}</span>
        </div>
        <div class="scroll-tbl" style="max-height:300px;">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-center">Status</th>
                        <th>Carrier</th>
                        <th class="text-end">Coverage</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($declinedChargebacks as $lead)
                        <tr>
                            <td><strong>{{ $lead->cn_name ?? 'N/A' }}</strong></td>
                            <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($lead->status === 'chargeback')
                                    <span class="st-pill st-chargeback">Chargeback</span>
                                @else
                                    <span class="st-pill st-declined">Declined</span>
                                @endif
                            </td>
                            <td>{{ $lead->carrier_name ?? 'N/A' }}</td>
                            <td class="text-end">
                                @if($lead->coverage_amount)
                                    ${{ number_format($lead->coverage_amount, 0) }}
                                @else
                                    <span style="color:var(--bs-surface-400);">—</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">{{ $lead->updated_at->setTimezone('America/Denver')->format('M d, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center" style="padding:1rem;color:var(--bs-surface-400);font-size:.75rem;"><i class="bx bx-smile"></i> No declined or chargebacks</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
@include('partials.sl-filter-assets')
<script>
    // Submit search on Enter key
    document.querySelector('.pipe-search')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('filterForm').submit(); }
    });
</script>
@endsection
