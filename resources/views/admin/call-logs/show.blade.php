@extends('layouts.master')

@section('title')
    Call Details
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('call-logs.index') }}">Call Logs</a>
        @endslot
        @slot('title')
            Call Details
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Call Information</h5>
                        <a href="{{ route('call-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Call Logs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (isset($callLog))
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4">Call ID:</dt>
                                            <dd class="col-sm-8"><code>{{ $callLog['id'] ?? 'N/A' }}</code></dd>

                                            <dt class="col-sm-4">Date & Time:</dt>
                                            <dd class="col-sm-8">
                                                {{ \Carbon\Carbon::parse($callLog['date_time'] ?? now())->format('F j, Y, g:i A') }}
                                            </dd>

                                            <dt class="col-sm-4">Duration:</dt>
                                            <dd class="col-sm-8">
                                                <span class="badge bg-primary">
                                                    {{ floor(($callLog['duration'] ?? 0) / 60) }}:{{ sprintf('%02d', ($callLog['duration'] ?? 0) % 60) }}
                                                </span>
                                            </dd>

                                            <dt class="col-sm-4">Direction:</dt>
                                            <dd class="col-sm-8">
                                                <span
                                                    class="badge {{ ($callLog['direction'] ?? '') === 'inbound' ? 'bg-success' : 'bg-info' }}">
                                                    <i
                                                        class="fas {{ ($callLog['direction'] ?? '') === 'inbound' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                                    {{ ucfirst($callLog['direction'] ?? 'Unknown') }}
                                                </span>
                                            </dd>

                                            <dt class="col-sm-4">Result:</dt>
                                            <dd class="col-sm-8">
                                                @php
                                                    $resultClass = match ($callLog['result'] ?? '') {
                                                        'answered' => 'bg-success',
                                                        'missed' => 'bg-danger',
                                                        'voicemail' => 'bg-warning',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $resultClass }}">
                                                    {{ ucfirst($callLog['result'] ?? 'Unknown') }}
                                                </span>
                                            </dd>

                                            <dt class="col-sm-4">Call Type:</dt>
                                            <dd class="col-sm-8">{{ $callLog['call_type'] ?? 'N/A' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Participants</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h6 class="text-success mb-2">
                                                    <i class="fas fa-user"></i> Caller
                                                </h6>
                                                <div class="ps-3">
                                                    <p class="mb-1">
                                                        <strong>Name:</strong>
                                                        {{ $callLog['caller']['name'] ?? 'Unknown' }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Number:</strong>
                                                        <a href="tel:{{ $callLog['caller']['phone_number'] ?? '' }}">
                                                            {{ $callLog['caller']['phone_number'] ?? 'N/A' }}
                                                        </a>
                                                    </p>
                                                    @if (isset($callLog['caller']['extension']))
                                                        <p class="mb-0">
                                                            <strong>Extension:</strong>
                                                            {{ $callLog['caller']['extension'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-info mb-2">
                                                    <i class="fas fa-user"></i> Callee
                                                </h6>
                                                <div class="ps-3">
                                                    <p class="mb-1">
                                                        <strong>Name:</strong>
                                                        {{ $callLog['callee']['name'] ?? 'Unknown' }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Number:</strong>
                                                        <a href="tel:{{ $callLog['callee']['phone_number'] ?? '' }}">
                                                            {{ $callLog['callee']['phone_number'] ?? 'N/A' }}
                                                        </a>
                                                    </p>
                                                    @if (isset($callLog['callee']['extension']))
                                                        <p class="mb-0">
                                                            <strong>Extension:</strong>
                                                            {{ $callLog['callee']['extension'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Call Timeline -->
                        @if (isset($callLog['call_events']) && !empty($callLog['call_events']))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Call Timeline</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="timeline">
                                                @foreach ($callLog['call_events'] as $event)
                                                    <div class="timeline-item">
                                                        <div class="timeline-marker bg-primary"></div>
                                                        <div class="timeline-content">
                                                            <h6 class="mb-1">
                                                                {{ ucfirst(str_replace('_', ' ', $event['event_type'] ?? '')) }}
                                                            </h6>
                                                            <p class="text-muted mb-1">
                                                                {{ \Carbon\Carbon::parse($event['date_time'] ?? now())->format('g:i:s A') }}
                                                            </p>
                                                            @if (isset($event['description']))
                                                                <p class="mb-0">{{ $event['description'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Recordings Section -->
                        @if (isset($recordings) && !empty($recordings['recordings']))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-microphone"></i> Call Recordings
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($recordings['recordings'] as $recording)
                                                <div
                                                    class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                    <div>
                                                        <h6 class="mb-1">{{ $recording['file_name'] ?? 'Recording' }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            Size:
                                                            {{ number_format(($recording['file_size'] ?? 0) / 1024 / 1024, 2) }}
                                                            MB |
                                                            Duration:
                                                            {{ floor(($recording['duration'] ?? 0) / 60) }}:{{ sprintf('%02d', ($recording['duration'] ?? 0) % 60) }}
                                                            |
                                                            Type: {{ $recording['recording_type'] ?? 'N/A' }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        @if (isset($recording['download_url']))
                                                            <a href="{{ $recording['download_url'] }}"
                                                                class="btn btn-sm btn-primary" target="_blank">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        @endif
                                                        @if (isset($recording['play_url']))
                                                            <button class="btn btn-sm btn-success"
                                                                onclick="playRecording('{{ $recording['play_url'] }}')">
                                                                <i class="fas fa-play"></i> Play
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Raw Data (for debugging) -->
                        @if (config('app.debug'))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Raw API Data (Debug)</h6>
                                        </div>
                                        <div class="card-body">
                                            <pre class="bg-light p-3 small"><code>{{ json_encode($callLog, JSON_PRETTY_PRINT) }}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>Call Details Not Found</h4>
                            <p class="text-muted">The requested call log could not be retrieved.</p>
                            <a href="{{ route('call-logs.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Call Logs
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Player Modal -->
    <div class="modal fade" id="audioPlayerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Call Recording</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <audio id="recordingPlayer" controls class="w-100">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function playRecording(url) {
            const modal = new bootstrap.Modal(document.getElementById('audioPlayerModal'));
            const player = document.getElementById('recordingPlayer');
            player.src = url;
            modal.show();
        }
    </script>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }
    </style>
@endsection
