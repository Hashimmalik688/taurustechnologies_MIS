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
            <h4><i class="bx bx-briefcase-alt"></i> Sales Operations</h4>
            <p>Sales records, QA review, policy submissions, bank verification &amp; analytics</p>
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
        @canViewModule('qa-review')
        <div class="hub-section-label">Quality Assurance</div>
        <div class="hub-grid">
            <a href="{{ route('qa.review') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-check-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">QA Review</div>
                    <p class="hub-card-desc">Listen to calls and evaluate sales quality for each record</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>

            <a href="{{ route('qa.scoring') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-shield-quarter"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">QA Scoring</div>
                    <p class="hub-card-desc">Score cards, rubrics and agent performance benchmarks</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
        </div>
        @endcanViewModule

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
