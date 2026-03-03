@extends('layouts.master')

@section('title', 'Zoom Webhook Diagnostics')

@section('css')
<style>
    .diag-card { background:#fff; border-radius:12px; padding:1.5rem; margin-bottom:1rem; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .diag-card h4 { font-size:1.1rem; font-weight:700; margin-bottom:1rem; color:#1e293b; }
    .diag-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:1rem; margin-bottom:2rem; }
    .stat-box { background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff; padding:1.5rem; border-radius:12px; text-align:center; }
    .stat-box.warning { background:linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-box.success { background:linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-value { font-size:2.5rem; font-weight:700; margin-bottom:.5rem; }
    .stat-label { font-size:.85rem; opacity:.9; text-transform:uppercase; letter-spacing:1px; }
    .event-list { list-style:none; padding:0; }
    .event-list li { padding:.75rem 1rem; border-bottom:1px solid rgba(0,0,0,.05); display:flex; justify-content:space-between; align-items:center; }
    .event-list li:last-child { border-bottom:none; }
    .badge { padding:.25rem .75rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .badge-success { background:#d1fae5; color:#065f46; }
    .badge-danger { background:#fee2e2; color:#991b1b; }
    .badge-info { background:#dbeafe; color:#1e40af; }
    .instructions { background:#f0f9ff; border-left:4px solid #3b82f6; padding:1rem 1.5rem; margin-bottom:1.5rem; border-radius:8px; }
    .instructions h5 { font-size:.95rem; font-weight:700; color:#1e40af; margin-bottom:.5rem; }
    .instructions ol { margin:.5rem 0 0 1.2rem; }
    .instructions li { margin-bottom:.5rem; font-size:.85rem; color:#1e3a8a; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 style="margin:0; font-size:1.5rem; font-weight:700;">
                    <i class="bx bx-pulse"></i> Zoom Webhook Diagnostics
                </h3>
                <a href="{{ route('settings.reports.zoom-logs') }}" class="btn btn-primary">
                    <i class="bx bx-arrow-back"></i> Back to Zoom Logs
                </a>
            </div>

            {{-- Stats Overview --}}
            <div class="diag-grid">
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['crm_events'] }}</div>
                    <div class="stat-label">Events in CRM</div>
                </div>
                <div class="stat-box warning">
                    <div class="stat-value">{{ $stats['estimated_zoom'] }}</div>
                    <div class="stat-label">Estimated Zoom Total</div>
                </div>
                <div class="stat-box success">
                    <div class="stat-value">{{ $stats['coverage_percent'] }}%</div>
                    <div class="stat-label">Coverage Rate</div>
                </div>
            </div>

            {{-- Instructions --}}
            <div class="instructions">
                <h5><i class="bx bx-info-circle"></i> How to Enable Missing Webhooks</h5>
                <ol>
                    <li>Go to <a href="https://marketplace.zoom.us/develop/apps" target="_blank"><strong>Zoom App Marketplace</strong></a></li>
                    <li>Click on your <strong>Taurus CRM Integration</strong> app</li>
                    <li>Navigate to <strong>Features → Event Subscriptions</strong></li>
                    <li>Under <strong>Zoom Phone</strong>, enable these 4 missing event types:</li>
                </ol>
            </div>

            {{-- Current Events --}}
            <div class="diag-card">
                <h4><i class="bx bx-check-circle" style="color:#10b981"></i> Currently Enabled ({{ count($current_events) }} events)</h4>
                <ul class="event-list">
                    @foreach($current_events as $event => $count)
                        <li>
                            <span><code>{{ $event }}</code></span>
                            <span class="badge badge-success">{{ $count }} captured</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Missing Events --}}
            <div class="diag-card">
                <h4><i class="bx bx-error-circle" style="color:#ef4444"></i> Missing Webhook Events (Enable These!)</h4>
                <ul class="event-list">
                    <li>
                        <span><code>phone.callee_ringing</code></span>
                        <span class="badge badge-danger">Critical - Captures every ring</span>
                    </li>
                    <li>
                        <span><code>phone.callee_missed</code></span>
                        <span class="badge badge-danger">Critical - Missed/unanswered calls</span>
                    </li>
                    <li>
                        <span><code>phone.callee_rejected</code></span>
                        <span class="badge badge-danger">Important - Rejected calls</span>
                    </li>
                    <li>
                        <span><code>phone.voicemail_received</code></span>
                        <span class="badge badge-info">Optional - Voicemail drops</span>
                    </li>
                </ul>
                <div style="margin-top:1rem; padding:1rem; background:#fef3c7; border-radius:8px; font-size:.85rem;">
                    <strong>⚠️ Impact:</strong> These 4 events account for ~{{ $stats['estimated_zoom'] - $stats['crm_events'] }} missing call records ({{ 100 - $stats['coverage_percent'] }}% gap)
                </div>
            </div>

            {{-- Optional Events --}}
            <div class="diag-card">
                <h4><i class="bx bx-bulb" style="color:#f59e0b"></i> Optional Events (Nice to Have)</h4>
                <ul class="event-list">
                    <li>
                        <span><code>phone.caller_ringing</code></span>
                        <span class="badge badge-info">Caller hears ringback</span>
                    </li>
                    <li>
                        <span><code>phone.caller_hold</code></span>
                        <span class="badge badge-info">Call on hold</span>
                    </li>
                    <li>
                        <span><code>phone.voicemail_transcription_completed</code></span>
                        <span class="badge badge-info">Transcription available</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
