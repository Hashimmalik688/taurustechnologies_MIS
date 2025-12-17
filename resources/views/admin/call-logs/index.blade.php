@extends('layouts.master')

@section('title')
    Call Logs
@endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Phone System
        @endslot
        @slot('title')
            Call Logs
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('call-logs.index') }}" id="filterForm">
                        <div class="row align-items-end">
                            {{-- <div class="col-md-2">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ $filters['start_date'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ $filters['end_date'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Direction</label>
                                <select name="direction" class="form-select">
                                    <option value="">All Directions</option>
                                    <option value="inbound" {{ request('direction') == 'inbound' ? 'selected' : '' }}>
                                        Inbound</option>
                                    <option value="outbound" {{ request('direction') == 'outbound' ? 'selected' : '' }}>
                                        Outbound</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Per Page</label>
                                <select name="per_page" class="form-select">
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="phoneSearch" class="form-control"
                                        placeholder="Search by phone number...">
                                    <button type="button" id="searchBtn" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('call-logs.export') }}{{ '?' . http_build_query(request()->query()) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                                {{-- <a href="{{ route('call-logs.statistics') }}{{ '?' . http_build_query(request()->query()) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-chart-bar"></i> Statistics
                                </a> --}}
                                <button type="button" id="refreshBtn" class="btn btn-warning btn-sm">
                                    <i class="fas fa-refresh"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Call Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Direction</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Duration</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($callLogs as $log)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($log['date_time'])->format('M d, Y') }}<br>
                                                {{ \Carbon\Carbon::parse($log['date_time'])->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $log['direction_badge'] ?? 'bg-secondary' }}">
                                                <i
                                                    class="fas {{ ($log['direction'] ?? '') === 'inbound' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                                {{ ucfirst($log['direction'] ?? '') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $log['caller_display'] ?? 'Unknown' }}</strong>
                                                @if (!empty($log['caller']['phone_number']))
                                                    <small class="text-muted">{{ $log['caller']['caller_did_number'] }} -
                                                        {{ $log['caller']['phone_number'] }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $log['callee_display'] ?? 'Unknown' }}</strong>
                                                @if (!empty($log['callee']['phone_number']))
                                                    <small class="text-muted">{{ $log['callee']['phone_number'] }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $log['formatted_duration'] ?? '00:00' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $log['result_badge'] ?? 'bg-secondary' }}">
                                                {{ ucfirst($log['result'] ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('call-logs.show', $log['id']) }}"
                                                class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if (!empty($log['has_recording']))
                                                <button class="btn btn-sm btn-outline-success"
                                                    onclick="playRecording('{{ $log['id'] }}')" title="Play Recording">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-phone-slash fa-2x mb-3"></i>
                                                <p>No call logs found for the selected criteria.</p>
                                                <small>Try adjusting your filters or date range.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination -->
                    @if (!empty($callLogs) && isset($pagination))
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info">
                                    Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of
                                    {{ $pagination['total'] }} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers float-end">
                                    <ul class="pagination">
                                        @if ($pagination['current_page'] > 1)
                                            <li class="paginate_button page-item previous">
                                                <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}"
                                                    class="page-link">Previous</a>
                                            </li>
                                        @endif

                                        @for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++)
                                            <li
                                                class="paginate_button page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                                    class="page-link">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        @if ($pagination['current_page'] < $pagination['last_page'])
                                            <li class="paginate_button page-item next">
                                                <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}"
                                                    class="page-link">Next</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Load More Button (Alternative to pagination) -->
                    <div class="row mt-3" id="loadMoreSection" style="display: none;">
                        <div class="col-12 text-center">
                            <button type="button" id="loadMoreBtn" class="btn btn-outline-primary">
                                <i class="fas fa-spinner fa-spin" style="display: none;"></i>
                                Load More Calls
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call Detail Modal -->
    <div class="modal fade" id="callDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Call Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="callDetailContent">
                        <div class="text-center py-3">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results Modal -->
    <div class="modal fade" id="searchResultsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="searchResults"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let loading = false;

            // Phone number search
            $('#searchBtn').click(function() {
                const phoneNumber = $('#phoneSearch').val().trim();
                if (phoneNumber.length < 3) {
                    alert('Please enter at least 3 digits');
                    return;
                }

                searchByPhone(phoneNumber);
            });

            // Enter key for search
            $('#phoneSearch').keypress(function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });

            // Refresh cache
            $('#refreshBtn').click(function() {
                const btn = $(this);
                const originalText = btn.html();

                btn.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
                btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route('call-logs.refresh') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: $('select[name="user_id"]').val()
                    },
                    success: function() {
                        location.reload();
                    },
                    error: function() {
                        alert('Failed to refresh cache');
                    },
                    complete: function() {
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                });
            });

            // Load more functionality
            $('#loadMoreBtn').click(function() {
                if (loading) return;

                loading = true;
                currentPage++;

                const btn = $(this);
                btn.find('.fa-spinner').show();
                btn.prop('disabled', true);

                const params = new URLSearchParams(window.location.search);
                params.set('page', currentPage);

                $.ajax({
                    url: '{{ route('call-logs.load-more') }}',
                    method: 'GET',
                    data: params.toString().split('&').reduce((acc, pair) => {
                        const [key, value] = pair.split('=');
                        acc[decodeURIComponent(key)] = decodeURIComponent(value);
                        return acc;
                    }, {}),
                    success: function(response) {
                        if (response.success && response.call_logs.length > 0) {
                            appendCallLogs(response.call_logs);
                            if (!response.has_more) {
                                $('#loadMoreSection').hide();
                            }
                        } else {
                            $('#loadMoreSection').hide();
                        }
                    },
                    error: function() {
                        alert('Failed to load more calls');
                        currentPage--;
                    },
                    complete: function() {
                        loading = false;
                        btn.find('.fa-spinner').hide();
                        btn.prop('disabled', false);
                    }
                });
            });

            // Auto-submit form on select changes
            $('select[name="user_id"], select[name="direction"], select[name="per_page"]').change(function() {
                $('#filterForm').submit();
            });
        });

        function searchByPhone(phoneNumber) {
            const modal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
            $('#searchResults').html(
                '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Searching...</div>');
            modal.show();

            $.ajax({
                url: '{{ route('call-logs.search') }}',
                method: 'GET',
                data: {
                    phone_number: phoneNumber,
                    start_date: $('input[name="start_date"]').val(),
                    end_date: $('input[name="end_date"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        displaySearchResults(response.call_logs);
                    } else {
                        $('#searchResults').html('<div class="alert alert-danger">Search failed: ' + response
                            .message + '</div>');
                    }
                },
                error: function() {
                    $('#searchResults').html(
                        '<div class="alert alert-danger">Search failed. Please try again.</div>');
                }
            });
        }

        function displaySearchResults(callLogs) {
            if (callLogs.length === 0) {
                $('#searchResults').html('<div class="alert alert-info">No calls found for this phone number.</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-striped">';
            html +=
                '<thead><tr><th>Date/Time</th><th>Direction</th><th>From</th><th>To</th><th>Duration</th><th>Result</th><th>Actions</th></tr></thead><tbody>';

            callLogs.forEach(function(log) {
                html += '<tr>';
                html += '<td>' + new Date(log.date_time).toLocaleString() + '</td>';
                html += '<td><span class="badge ' + (log.direction === 'inbound' ? 'bg-success' : 'bg-primary') +
                    '">' + log.direction + '</span></td>';
                html += '<td>' + (log.caller ? (log.caller.name || log.caller.phone_number) : 'Unknown') + '</td>';
                html += '<td>' + (log.callee ? (log.callee.name || log.callee.phone_number) : 'Unknown') + '</td>';
                html += '<td>' + formatDuration(log.duration || 0) + '</td>';
                html += '<td><span class="badge ' + getResultBadge(log.result) + '">' + log.result + '</span></td>';
                html += '<td><button class="btn btn-sm btn-outline-primary" onclick="viewCallDetail(\'' + log.id +
                    '\')"><i class="fas fa-eye"></i></button></td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            $('#searchResults').html(html);
        }

        function viewCallDetail(callId) {
            const modal = new bootstrap.Modal(document.getElementById('callDetailModal'));
            $('#callDetailContent').html(
                '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
            modal.show();

            window.location.href = '{{ route('call-logs.show', '') }}/' + callId;
        }

        function playRecording(callId) {
            // Implement recording playback functionality
            alert('Recording playback functionality to be implemented');
        }

        function appendCallLogs(callLogs) {
            const tbody = $('table tbody');

            callLogs.forEach(function(log) {
                const row = `
                    <tr>
                        <td>
                            <small class="text-muted">
                                ${new Date(log.date_time).toLocaleDateString()}<br>
                                ${new Date(log.date_time).toLocaleTimeString()}
                            </small>
                        </td>
                        <td>
                            <span class="badge ${log.direction === 'inbound' ? 'bg-success' : 'bg-primary'}">
                                <i class="fas ${log.direction === 'inbound' ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${log.direction}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <strong>${log.caller_display || 'Unknown'}</strong>
                                ${log.caller && log.caller.phone_number ? '<small class="text-muted">' + log.caller.phone_number + '</small>' : ''}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <strong>${log.callee_display || 'Unknown'}</strong>
                                ${log.callee && log.callee.phone_number ? '<small class="text-muted">' + log.callee.phone_number + '</small>' : ''}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                ${log.formatted_duration || '00:00'}
                            </span>
                        </td>
                        <td>
                            <span class="badge ${getResultBadge(log.result)}">
                                ${log.result || 'unknown'}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('call-logs.show', '') }}/${log.id}" 
                               class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            ${log.has_recording ? '<button class="btn btn-sm btn-outline-success" onclick="playRecording(\'' + log.id + '\')" title="Play Recording"><i class="fas fa-play"></i></button>' : ''}
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function formatDuration(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return mins.toString().padStart(2, '0') + ':' + secs.toString().padStart(2, '0');
        }

        function getResultBadge(result) {
            switch (result) {
                case 'answered':
                    return 'bg-success';
                case 'missed':
                    return 'bg-danger';
                case 'voicemail':
                    return 'bg-warning';
                default:
                    return 'bg-secondary';
            }
        }
    </script>
@endsection
