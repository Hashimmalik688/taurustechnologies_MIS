@extends('layouts.master')

@section('title')
    Sales Operations Hub
@endsection

@section('css')
@include('components.hub-styles')
@endsection

@section('content')
    <div class="hub-page">
        <div class="hub-header">
            <div class="hub-header-top">
                <div>
                    <h4><i class="bx bx-briefcase-alt"></i> Sales Operations</h4>
                    <p>Sales records, QA review, policy submissions, bank verification &amp; analytics</p>
                </div>
            </div>

            {{-- Master Lead Search --}}
            <div class="hub-search-wrap">
                <div class="hub-search-input-wrap">
                    <i class="bx bx-search hub-search-icon"></i>
                    <input type="text" id="hubLeadSearch" class="hub-search-input"
                           placeholder="Search lead by name, phone, or policy #…"
                           autocomplete="off" spellcheck="false">
                    <span id="hubSearchSpinner" class="hub-search-spinner d-none">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                    </span>
                </div>
                <div id="hubSearchResults" class="hub-search-results d-none"></div>
            </div>
        </div>

        @php $user = auth()->user(); @endphp

        {{-- Records & Pipeline --}}
        @if($user->canViewModule('sales') || $user->canViewModule('issuance') || $user->canViewModule('pendings-approved') || $user->canViewModule('pending-draft') || $user->canViewModule('paid-sales'))
        <div class="hub-section-label">Records &amp; Pipeline</div>
        <div class="hub-grid">
            @canViewModule('sales')
            <a href="{{ route('sales.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-dollar-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Sales Records</div>
                    <p class="hub-card-desc">View, filter and manage all closed sales across teams</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('pendings-approved')
            <a href="{{ route('submissions.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-task"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Pending Submission</div>
                    <p class="hub-card-desc">Validated leads awaiting manager decision before Pending Contract</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('issuance')
            <a href="{{ route('issuance.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-send"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Pending Contract</div>
                    <p class="hub-card-desc">Track and process insurance policy submissions pending contract</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>

            <a href="{{ route('followup.my-followups') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-phone-outgoing"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">My Followups</div>
                    <p class="hub-card-desc">Issued leads assigned to you awaiting closer confirmation</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>

            <a href="{{ route('followup.followup-done') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-check-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Followup Done</div>
                    <p class="hub-card-desc">Leads confirmed by closers, ready for Pending Draft assignment</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('pending-draft')
            <a href="{{ route('pending-draft.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-time-five"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Pending Draft</div>
                    <p class="hub-card-desc">Leads awaiting first premium draft — mark Not Paid (FDFP) or Paid</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('paid-sales')
            <a href="{{ route('paid-sales.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-badge-check"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Paid Sales</div>
                    <p class="hub-card-desc">Successfully collected first draft — final paid sales records</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
        @endif

        {{-- Quality Assurance --}}
        @php $canSeeQaSection = auth()->user()->canViewModule('qa-review') || auth()->user()->canViewModule('qa-scoring'); @endphp
        @if($canSeeQaSection)
        <div class="hub-section-label">Quality Assurance</div>
        <div class="hub-grid">
            @canViewModule('qa-review')
            <a href="{{ route('qa.review') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-check-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">QA Review</div>
                    <p class="hub-card-desc">Listen to calls and evaluate sales quality for each record</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('qa-scoring')
            <a href="{{ route('qa.scoring') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-shield-quarter"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">QA Scoring</div>
                    <p class="hub-card-desc">Score cards, rubrics and agent performance benchmarks</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
        @endif

        {{-- Verification --}}
        @if(\Illuminate\Support\Facades\Route::has('bank-verification.index'))
        @canViewModule('bank-verification')
        <div class="hub-section-label">Verification</div>
        <div class="hub-grid">
            <a href="{{ route('bank-verification.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-check-shield"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Bank Verification</div>
                    <p class="hub-card-desc">Verify client banking details and update verification status</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
        </div>
        @endcanViewModule
        @endif

        {{-- Analytics --}}
        @if($user->canViewModule('revenue-analytics') || $user->canViewModule('live-analytics'))
        <div class="hub-section-label">Analytics</div>
        <div class="hub-grid">
            @canViewModule('revenue-analytics')
            <a href="{{ route('revenue-analytics.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-line-chart"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Revenue Analytics</div>
                    <p class="hub-card-desc">Track revenue trends, carrier breakdown and monthly performance</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('live-analytics')
            <a href="{{ route('analytics.live') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-pulse"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Live Analytics</div>
                    <p class="hub-card-desc">Real-time sales activity and team performance dashboard</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const input    = document.getElementById('hubLeadSearch');
    const results  = document.getElementById('hubSearchResults');
    const spinner  = document.getElementById('hubSearchSpinner');
    const searchUrl = '{{ route('sales.hub.search') }}';
    let debounce;

    input.addEventListener('input', function () {
        clearTimeout(debounce);
        const q = this.value.trim();
        if (q.length < 2) {
            results.classList.add('d-none');
            results.innerHTML = '';
            return;
        }
        spinner.classList.remove('d-none');
        debounce = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                spinner.classList.add('d-none');
                renderResults(data.results);
            })
            .catch(err => {
                spinner.classList.add('d-none');
                results.innerHTML = '<div class="hub-search-empty"><i class="bx bx-error-circle" style="color:#e53e3e"></i> Search failed — try again</div>';
                results.classList.remove('d-none');
                console.warn('[Hub Search]', err);
            });
        }, 320);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.hub-search-wrap')) {
            results.classList.add('d-none');
        }
    });

    input.addEventListener('focus', function () {
        if (results.innerHTML.trim() !== '') results.classList.remove('d-none');
    });

    function badgeClass(badge) {
        const map = {
            success:   'bg-success',
            danger:    'bg-danger',
            warning:   'bg-warning text-dark',
            primary:   'bg-primary',
            info:      'bg-info text-dark',
            secondary: 'bg-secondary',
        };
        return map[badge] || 'bg-secondary';
    }

    function renderResults(items) {
        if (!items.length) {
            results.innerHTML = '<div class="hub-search-empty"><i class="bx bx-search-alt"></i> No leads found</div>';
            results.classList.remove('d-none');
            return;
        }
        const html = items.map(lead => {
            const href = lead.url ? `href="${lead.url}"` : '';
            const tag  = lead.url ? 'a' : 'div';
            return `<${tag} ${href} class="hub-sr-item" target="${lead.url ? '_self' : ''}">
                <div class="hub-sr-icon"><i class="bx ${lead.icon}"></i></div>
                <div class="hub-sr-body">
                    <div class="hub-sr-name">${lead.cn_name || '—'}</div>
                    <div class="hub-sr-meta">
                        ${lead.phone_number ? `<span><i class="bx bx-phone"></i> ${lead.phone_number}</span>` : ''}
                        ${lead.carrier_name ? `<span><i class="bx bx-buildings"></i> ${lead.carrier_name}</span>` : ''}
                        ${lead.sale_date    ? `<span><i class="bx bx-calendar"></i> ${lead.sale_date}</span>` : ''}
                        ${lead.closer_name  ? `<span><i class="bx bx-user"></i> ${lead.closer_name}</span>` : ''}
                    </div>
                </div>
                <div class="hub-sr-stage">
                    <span class="badge ${badgeClass(lead.badge)}">${lead.stage}</span>
                    ${lead.url ? '<i class="bx bx-chevron-right hub-sr-arrow"></i>' : ''}
                </div>
            </${tag}>`;
        }).join('');
        results.innerHTML = html;
        results.classList.remove('d-none');
    }
})();
</script>
@endpush
