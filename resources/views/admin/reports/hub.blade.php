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
            @canViewModule('reports')
            <a href="{{ route('settings.reports.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-file-find"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Sales Reports</div>
                    <p class="hub-card-desc">Filter, generate &amp; export sales, partner, chargeback and issuance reports</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('reports')
            <a href="{{ route('settings.reports.per-closer') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-phone-call"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Per-Closer Performance</div>
                    <p class="hub-card-desc">Dialed, connected, disposed &amp; sales ratios broken down by closer</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>

        <div class="hub-section-label">Call Tracking</div>
        <div class="hub-grid">
            @canViewModule('reports')
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
    </div>
@endsection
