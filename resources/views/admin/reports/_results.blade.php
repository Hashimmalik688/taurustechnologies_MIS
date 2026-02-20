@if($results->count() > 0)
    <div class="results-header">
        <h6>Results <span class="text-muted">({{ $results->total() }} records)</span></h6>
        <span class="text-muted" style="font-size: 0.82rem;">
            Showing {{ $results->firstItem() }}–{{ $results->lastItem() }} of {{ $results->total() }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="results-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>State</th>
                    <th>Status</th>
                    <th>Carrier</th>
                    <th>Coverage</th>
                    <th>Premium</th>
                    <th>Policy Type</th>
                    <th>Closer</th>
                    <th>Partner</th>
                    <th>Source</th>
                    <th>Team</th>
                    <th>Sale Date</th>
                    <th>QA</th>
                    <th>Manager</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $lead)
                    <tr>
                        <td>{{ $lead->id }}</td>
                        <td><strong>{{ $lead->cn_name ?? '—' }}</strong></td>
                        <td>{{ $lead->phone_number ?? '—' }}</td>
                        <td>{{ $lead->state ?? '—' }}</td>
                        <td>
                            @php
                                $statusClass = match($lead->status) {
                                    'sale' => 'status-sale',
                                    'pending' => 'status-pending',
                                    'declined' => 'status-declined',
                                    'chargeback' => 'status-chargeback',
                                    'accepted' => 'status-accepted',
                                    default => 'status-default',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ ucfirst($lead->status ?? '—') }}</span>
                        </td>
                        <td>{{ $lead->insurance_carrier_name ?? $lead->carrier_name ?? '—' }}</td>
                        <td>{{ $lead->coverage_amount ? '$' . number_format($lead->coverage_amount, 2) : '—' }}</td>
                        <td>{{ $lead->monthly_premium ? '$' . number_format($lead->monthly_premium, 2) : '—' }}</td>
                        <td>{{ $lead->policy_type ?? '—' }}</td>
                        <td>{{ $lead->closer_user_name ?? $lead->closer_name ?? '—' }}</td>
                        <td>{{ $lead->partner_name ?? '—' }}</td>
                        <td>{{ $lead->source ?? '—' }}</td>
                        <td>{{ $lead->team ?? '—' }}</td>
                        <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}</td>
                        <td>
                            @if($lead->qa_status)
                                @php
                                    $qaClass = match($lead->qa_status) {
                                        'Good' => 'status-sale',
                                        'Avg' => 'status-pending',
                                        'Bad' => 'status-declined',
                                        default => 'status-default',
                                    };
                                @endphp
                                <span class="status-badge {{ $qaClass }}">{{ $lead->qa_status }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($lead->manager_status)
                                @php
                                    $mgrClass = match($lead->manager_status) {
                                        'approved' => 'status-sale',
                                        'pending' => 'status-pending',
                                        'declined' => 'status-declined',
                                        'chargeback' => 'status-chargeback',
                                        default => 'status-default',
                                    };
                                @endphp
                                <span class="status-badge {{ $mgrClass }}">{{ ucfirst($lead->manager_status) }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $lead->created_at ? \Carbon\Carbon::parse($lead->created_at)->format('M d, Y') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($results->hasPages())
        <div style="padding: 14px 20px; border-top: 1px solid #e9ecef; display: flex; justify-content: center;">
            {{ $results->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <i class="bx bx-search-alt"></i>
        <h6>No records found</h6>
        <p>Try adjusting your filters to find matching records</p>
    </div>
@endif
