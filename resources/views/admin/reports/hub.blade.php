@extends('layouts.master')

@section('title')
    Reports
@endsection

@section('css')
@include('components.hub-styles')
@endsection

@section('content')
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-bar-chart-alt-2"></i> Reports</h4>
            <p>Analytics, performance tracking &amp; data exports</p>
        </div>

        <div class="hub-section-label">Sales &amp; Performance</div>
        <div class="hub-grid">
            @canViewModule('report-submission-performance')
            <a href="{{ route('settings.reports.submission-performance') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-award"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Submission Performance</div>
                    <p class="hub-card-desc">Carrier-wise breakdown of approved sales — total submissions &amp; premium per carrier</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('report-policy-type')
            <a href="{{ route('settings.reports.policy-type-report') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-category"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Policy Type Report</div>
                    <p class="hub-card-desc">Sales breakdown by policy type (Level, Graded, G.I, Modified) — premium &amp; revenue per type</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('report-sales-status')
            <a href="{{ route('settings.reports.sales-status') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-table"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Sales Status Report</div>
                    <p class="hub-card-desc">All pipeline stages per carrier in one view — paid, issued, not issued, chargebacks &amp; more</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>

        <div class="hub-section-label">Call Tracking</div>
        <div class="hub-grid">
            @canViewModule('report-disposition')
            <a href="{{ route('settings.reports.disposition-report') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-phone-call"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Dialer Report</div>
                    <p class="hub-card-desc">Per-closer breakdown of End Call &amp; Save &amp; Exit dispositions — volume, type &amp; trends over time</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('report-closer')
            <a href="{{ route('settings.reports.closer-report') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-user-check"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Closer Performance Report</div>
                    <p class="hub-card-desc">Per-closer sales metrics — total sales, approved, declined, paid &amp; chargeback counts with detailed drilldown</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('report-manager-submission')
            <a href="{{ route('settings.reports.manager-submission-report') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-user-check"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Manager Submission Report</div>
                    <p class="hub-card-desc">Per-manager count of sales approved to Pending Contract or marked Declined — click any manager to view individual leads</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('report-peregrine-team')
            <a href="{{ route('settings.reports.peregrine-team-report') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-shield-alt"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Peregrine Team Report</div>
                    <p class="hub-card-desc">PJC submissions, Closer pipeline &amp; Validator outcomes — full Peregrine team performance in one view</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('report-zoom-logs')
            <a href="{{ route('settings.reports.zoom-logs') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-video"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Zoom Logs</div>
                    <p class="hub-card-desc">Call recordings, durations &amp; Zoom session history</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>

        <div class="hub-section-label">Commission Tracking</div>
        <div class="hub-grid">
            @canViewModule('carrier-sheet')
            <a href="{{ route('settings.reports.carrier-sheet.dashboard') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-spreadsheet"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Carrier Sheet</div>
                    <p class="hub-card-desc">Track carrier commissions, chargebacks &amp; balances — per carrier workbook with auto-calculations</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
    </div>
@endsection
