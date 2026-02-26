@if($results->count() > 0)
    <div class="rp-results-hdr">
        <h6><i class="bx bx-table"></i> Results <span>({{ number_format($results->total()) }} records)</span></h6>
        <div class="rp-results-meta">
            <span>Showing {{ $results->firstItem() }}–{{ $results->lastItem() }} of {{ number_format($results->total()) }}</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="rp-table" id="reportTable">
            <thead>
                <tr>
                    <th class="rp-th-id">#</th>
                    <th class="rp-th-name">Client Name</th>
                    <th>Phone</th>
                    <th>State</th>
                    <th>Status</th>
                    <th>Carrier</th>
                    <th class="rp-th-num">Coverage</th>
                    <th class="rp-th-num">Premium</th>
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
                        <td class="rp-td-id">{{ $lead->id }}</td>
                        <td class="rp-td-name">{{ $lead->cn_name ?? '—' }}</td>
                        <td class="rp-td-mono">{{ $lead->phone_number ?? '—' }}</td>
                        <td>{{ $lead->state ?? '—' }}</td>
                        <td>
                            @php
                                $statusClass = match(strtolower($lead->status ?? '')) {
                                    'sale' => 'rp-badge-sale',
                                    'pending' => 'rp-badge-pending',
                                    'declined' => 'rp-badge-declined',
                                    'chargeback' => 'rp-badge-chargeback',
                                    'accepted','underwritten' => 'rp-badge-accepted',
                                    'transferred' => 'rp-badge-transferred',
                                    'returned' => 'rp-badge-returned',
                                    'closed' => 'rp-badge-closed',
                                    'disposed' => 'rp-badge-declined',
                                    default => 'rp-badge-default',
                                };
                            @endphp
                            <span class="rp-badge {{ $statusClass }}">{{ ucfirst($lead->status ?? '—') }}</span>
                        </td>
                        <td>{{ trim($lead->insurance_carrier_name ?? $lead->carrier_name ?? '—') }}</td>
                        <td class="rp-td-num">{{ $lead->coverage_amount ? '$' . number_format($lead->coverage_amount, 0) : '—' }}</td>
                        <td class="rp-td-num">{{ $lead->monthly_premium ? '$' . number_format($lead->monthly_premium, 2) : '—' }}</td>
                        <td>{{ $lead->policy_type ?? '—' }}</td>
                        <td>{{ $lead->closer_user_name ?? $lead->closer_name ?? '—' }}</td>
                        <td>{{ $lead->partner_name ?? $lead->assigned_partner ?? '—' }}</td>
                        <td>{{ $lead->source ?? '—' }}</td>
                        <td>{{ $lead->team ?? '—' }}</td>
                        <td>{{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '—' }}</td>
                        <td>
                            @if($lead->qa_status)
                                @php
                                    $qaClass = match($lead->qa_status) {
                                        'Good' => 'rp-badge-sale',
                                        'Avg' => 'rp-badge-pending',
                                        'Bad' => 'rp-badge-declined',
                                        default => 'rp-badge-default',
                                    };
                                @endphp
                                <span class="rp-badge {{ $qaClass }}">{{ $lead->qa_status }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($lead->manager_status)
                                @php
                                    $mgrClass = match($lead->manager_status) {
                                        'approved' => 'rp-badge-sale',
                                        'pending' => 'rp-badge-pending',
                                        'declined' => 'rp-badge-declined',
                                        'chargeback' => 'rp-badge-chargeback',
                                        'underwriting' => 'rp-badge-accepted',
                                        default => 'rp-badge-default',
                                    };
                                @endphp
                                <span class="rp-badge {{ $mgrClass }}">{{ ucfirst($lead->manager_status) }}</span>
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
        <div class="rp-pagination">
            {{ $results->links() }}
        </div>
    @endif
@else
    <div class="rp-empty">
        <i class="bx bx-search-alt"></i>
        <h6>No records found</h6>
        <p>Try adjusting your filters to find matching records</p>
    </div>
@endif
