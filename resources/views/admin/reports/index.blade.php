@extends('layouts.master')

@section('title')
    Reports
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    @include('partials.sl-filter-assets')
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        /* Results table overrides */
        .rp-results .results-header {
            padding:.55rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);
            display:flex;justify-content:space-between;align-items:center;
        }
        .rp-results .results-header h6 { margin:0;font-size:.78rem;font-weight:700 }

        .rp-empty { text-align:center;padding:3rem 1rem;color:var(--bs-surface-500) }
        .rp-empty i { font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25 }
        .rp-empty h6 { font-size:.85rem;font-weight:700;margin-bottom:.25rem }
        .rp-empty p { font-size:.72rem }

        .loading-overlay {
            position:absolute;top:0;left:0;right:0;bottom:0;
            background:rgba(255,255,255,.8);display:flex;align-items:center;
            justify-content:center;z-index:10;border-radius:.55rem;
        }
        .loading-overlay .spinner-border { width:2rem;height:2rem }

        /* Status badges in results */
        .rp-status { font-size:.62rem;font-weight:700;padding:.15rem .4rem;border-radius:1rem;display:inline-block;text-transform:uppercase;letter-spacing:.3px }
        .rp-sale { background:rgba(52,195,143,.12);color:#1a8754 }
        .rp-pending { background:rgba(241,180,76,.12);color:#b87a14 }
        .rp-declined { background:rgba(244,106,106,.12);color:#c84646 }
        .rp-chargeback { background:rgba(244,106,106,.12);color:#c84646 }
        .rp-accepted { background:rgba(80,165,241,.12);color:#2b81c9 }
        .rp-default { background:rgba(108,117,125,.08);color:#6c757d }
    </style>
@endsection

@section('content')
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-bar-chart-alt-2"></i> Reports
            <span class="rp-sub">Generate &amp; export</span>
        </h5>
        <a href="{{ route('settings.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Settings
        </a>
    </div>

    {{-- Report Type Pills --}}
    <div class="ex-card pipe-filter-bar" style="margin-bottom:.65rem">
        <span class="pipe-pill-lbl">Type</span>
        <button class="pipe-pill active" data-type="all">All Records</button>
        <button class="pipe-pill" data-type="sales">Sales</button>
        <button class="pipe-pill" data-type="partner">Partner</button>
        <button class="pipe-pill" data-type="submissions">Manager Submissions</button>
        <button class="pipe-pill" data-type="chargebacks">Chargebacks</button>
        <button class="pipe-pill" data-type="retention">Retention</button>
        <button class="pipe-pill" data-type="issuance">Issuance</button>
    </div>

    {{-- Filters --}}
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-hdr" id="filterToggle" style="cursor:pointer">
            <h6><i class="bx bx-filter-alt"></i> Filters</h6>
            <i class="bx bx-chevron-down" id="filterToggleIcon" style="font-size:1rem;opacity:.5;transition:transform .2s"></i>
        </div>
        <div class="sec-body" id="filterBody" style="padding:.75rem">
            <form id="reportForm">
                <input type="hidden" name="report_type" id="reportType" value="all">

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.55rem">
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Closer</label>
                        <select name="closer_id" id="closerFilter" class="sl-pill-select">
                            <option value="">All Closers</option>
                            @foreach($closers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Manager</label>
                        <select name="manager_id" id="managerFilter" class="sl-pill-select">
                            <option value="">All Managers</option>
                            @foreach($managers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Carrier</label>
                        <select name="carrier_id" id="carrierFilter" class="sl-pill-select">
                            <option value="">All Carriers</option>
                            @foreach($carriers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Partner</label>
                        <select name="partner_id" id="partnerFilter" class="sl-pill-select">
                            <option value="">All Partners</option>
                            @foreach($partners as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Verifier</label>
                        <select name="verifier_id" id="verifierFilter" class="sl-pill-select">
                            <option value="">All Verifiers</option>
                            @foreach($verifiers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Lead Status</label>
                        <select name="status" id="statusFilter" class="sl-pill-select">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Team</label>
                        <select name="team" id="teamFilter" class="sl-pill-select">
                            <option value="">All Teams</option>
                            @foreach($teams as $team)
                                <option value="{{ $team }}">{{ $team }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Source</label>
                        <select name="source" id="sourceFilter" class="sl-pill-select">
                            <option value="">All Sources</option>
                            @foreach($sources as $source)
                                <option value="{{ $source }}">{{ $source }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">State</label>
                        <select name="state" id="stateFilter" class="sl-pill-select">
                            <option value="">All States</option>
                            @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">QA Status</label>
                        <select name="qa_status" id="qaStatusFilter" class="sl-pill-select">
                            <option value="">All</option>
                            <option value="Good">Good</option>
                            <option value="Avg">Avg</option>
                            <option value="Bad">Bad</option>
                            <option value="In Review">In Review</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Manager Status</label>
                        <select name="manager_status" id="managerStatusFilter" class="sl-pill-select">
                            <option value="">All</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="declined">Declined</option>
                            <option value="underwriting">Underwriting</option>
                            <option value="chargeback">Chargeback</option>
                        </select>
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Created From</label>
                        <input type="text" name="date_from" id="dateFrom" class="pipe-pill-date sl-pill-date" placeholder="From">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Created To</label>
                        <input type="text" name="date_to" id="dateTo" class="pipe-pill-date sl-pill-date" placeholder="To">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Sale From</label>
                        <input type="text" name="sale_date_from" id="saleDateFrom" class="pipe-pill-date sl-pill-date" placeholder="From">
                    </div>
                    <div>
                        <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Sale To</label>
                        <input type="text" name="sale_date_to" id="saleDateTo" class="pipe-pill-date sl-pill-date" placeholder="To">
                    </div>
                </div>

                <div style="display:flex;gap:.4rem;margin-top:.65rem;align-items:center">
                    <button type="submit" class="pipe-pill-apply" style="font-size:.72rem;padding:.3rem .75rem">
                        <i class="bx bx-search-alt" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Generate
                    </button>
                    <button type="button" class="pipe-pill-clear" id="resetFilters">
                        <i class="bx bx-reset"></i> Reset
                    </button>
                    <button type="button" class="act-btn a-success" id="exportCsv" style="margin-left:auto">
                        <i class="bx bx-download"></i> Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary KPIs --}}
    <div class="kpi-row" id="summaryRow" style="display:none">
        <div class="ex-card kpi-card k-gold">
            <i class="bx bx-file k-icon"></i>
            <div class="k-val" id="summaryTotal">0</div>
            <div class="k-lbl">Records</div>
        </div>
        <div class="ex-card kpi-card k-green">
            <i class="bx bx-dollar-circle k-icon"></i>
            <div class="k-val" id="summaryPremium">$0</div>
            <div class="k-lbl">Premium</div>
        </div>
        <div class="ex-card kpi-card k-blue">
            <i class="bx bx-shield k-icon"></i>
            <div class="k-val" id="summaryCoverage">$0</div>
            <div class="k-lbl">Coverage</div>
        </div>
        <div class="ex-card kpi-card k-purple">
            <i class="bx bx-trending-up k-icon"></i>
            <div class="k-val" id="summaryCommission">$0</div>
            <div class="k-lbl">Commission</div>
        </div>
        <div class="ex-card kpi-card k-teal">
            <i class="bx bx-wallet k-icon"></i>
            <div class="k-val" id="summaryRevenue">$0</div>
            <div class="k-lbl">Revenue</div>
        </div>
    </div>

    {{-- Results --}}
    <div class="ex-card sec-card rp-results" id="resultsCard" style="position:relative">
        <div id="resultsContent">
            <div class="rp-empty">
                <i class="bx bx-bar-chart"></i>
                <h6>Select filters and generate a report</h6>
                <p>Use the filters above to customize your report</p>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportForm');
    const resultsContent = document.getElementById('resultsContent');
    const resultsCard = document.getElementById('resultsCard');
    const summaryRow = document.getElementById('summaryRow');
    const reportTypeInput = document.getElementById('reportType');

    // Report type pills
    document.querySelectorAll('.pipe-pill[data-type]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pipe-pill[data-type]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            reportTypeInput.value = this.dataset.type;
        });
    });

    // Toggle filters
    document.getElementById('filterToggle').addEventListener('click', function() {
        const body = document.getElementById('filterBody');
        const icon = document.getElementById('filterToggleIcon');
        const isVisible = body.style.display !== 'none';
        body.style.display = isVisible ? 'none' : 'block';
        icon.style.transform = isVisible ? 'rotate(-90deg)' : '';
    });

    // Generate report
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        loadReport();
    });

    // Reset
    document.getElementById('resetFilters').addEventListener('click', function() {
        form.reset();
        document.querySelectorAll('.pipe-pill[data-type]').forEach(b => b.classList.remove('active'));
        document.querySelector('.pipe-pill[data-type="all"]').classList.add('active');
        reportTypeInput.value = 'all';
        summaryRow.style.display = 'none';
        // Reset custom dropdowns
        document.querySelectorAll('.sl-cdd-trigger').forEach(t => {
            const firstOpt = t.closest('.sl-cdd')?.querySelector('.sl-cdd-opt');
            if (firstOpt) { t.textContent = firstOpt.textContent; }
        });
        resultsContent.innerHTML = '<div class="rp-empty"><i class="bx bx-bar-chart"></i><h6>Select filters and generate a report</h6><p>Use the filters above</p></div>';
    });

    // Export CSV
    document.getElementById('exportCsv').addEventListener('click', function() {
        const params = new URLSearchParams(new FormData(form));
        window.location.href = '{{ route("settings.reports.export") }}?' + params.toString();
    });

    // Pagination
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#resultsContent .pagination a');
        if (link) { e.preventDefault(); loadReport(link.href); }
    });

    function loadReport(url) {
        url = url || '{{ route("settings.reports.generate") }}';
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = '<div class="spinner-border text-warning"><span class="visually-hidden">Loading...</span></div>';
        resultsCard.appendChild(loader);

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const urlObj = new URL(url, window.location.origin);
        for (const [key, value] of params.entries()) {
            if (!urlObj.searchParams.has(key)) urlObj.searchParams.set(key, value);
        }

        fetch(urlObj.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(data => {
            resultsContent.innerHTML = data.html;
            if (data.summary) {
                summaryRow.style.display = 'flex';
                document.getElementById('summaryTotal').textContent = Number(data.summary.total_records).toLocaleString();
                document.getElementById('summaryPremium').textContent = '$' + Number(data.summary.total_premium).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                document.getElementById('summaryCoverage').textContent = '$' + Number(data.summary.total_coverage).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                document.getElementById('summaryCommission').textContent = '$' + Number(data.summary.total_commission).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                document.getElementById('summaryRevenue').textContent = '$' + Number(data.summary.total_revenue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            }
        })
        .catch(err => {
            resultsContent.innerHTML = '<div class="rp-empty"><i class="bx bx-error-circle"></i><h6>Error loading report</h6><p>' + (err.message || 'Something went wrong') + '</p></div>';
        })
        .finally(() => {
            const o = resultsCard.querySelector('.loading-overlay');
            if (o) o.remove();
        });
    }
});
</script>
@endsection
