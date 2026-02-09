<div class="card-body">
    <!-- Top Scrollbar -->
    <div class="top-scrollbar-wrapper" id="topScrollbarLeads">
        <div class="top-scrollbar-content" id="topScrollbarContentLeads"></div>
    </div>
    
    <!-- Main Table Wrapper -->
    <div class="leads-table-wrapper" id="leadsTableWrapper">
    <table class="leads-table table table-striped table-bordered table-hover table-sm align-middle text-nowrap" id="leadsTable">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Actions</th>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Client Name</th>
                    <th>Primary Phone</th>
                    <th>Secondary Phone</th>
                    <th>State/Zip</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Smoker</th>
                    @hasrole('Super Admin')
                        <th>DL#</th>
                        <th>Height/Weight</th>
                        <th>Birth Place</th>
                        <th>Medical Issue</th>
                        <th>Medications</th>
                        <th>Doctor</th>
                        <th>SSN</th>
                        <th>Address</th>
                    @endhasrole
                    <th>Carrier</th>
                    <th>Coverage</th>
                    <th>Premium</th>
                    <th>Beneficiaries (Name / Relation / DOB)</th>
                    @hasrole('Super Admin')
                        <th>Emergency Contact</th>
                        <th>Acc Verified By</th>
                        <th>Policy Type</th>
                        <th>Initial Draft</th>
                        <th>Future Draft</th>
                        <th>Bank</th>
                        <th>Acc Type</th>
                        <th>Routing#</th>
                        <th>Acc#</th>
                        <th>Card#</th>
                        <th>CVV</th>
                        <th>Expiry</th>
                        <th>Source</th>
                        <th>Closer</th>
                        <th>Assigned Partner</th>
                        <th>Comments</th>
                    @endhasrole
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $index => $lead)
                    <tr>
                        <td class="text-center"><strong>{{ $leads->firstItem() + $index }}</strong></td>
                        <td class="text-center" style="min-width: 140px;">
                            <div class="btn-group" role="group">
                                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-info btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @hasrole('Super Admin|Manager')
                                    <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('leads.delete', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($lead->cn_name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endhasrole
                            </div>
                        </td>
                        <td>{{ $lead->id }}</td>
                        <td>{{ $lead->date ?? 'N/A' }}</td>
                        <td><strong>{{ $lead->cn_name }}</strong></td>
                        <td>
                            @if($lead->phone_number)
                                <span title="{{ $lead->phone_number }}">{{ $lead->phone_number }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->secondary_phone_number)
                                <span title="{{ $lead->secondary_phone_number }}">{{ $lead->secondary_phone_number }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->state || $lead->zip_code)
                                <small>{{ $lead->state ?? '—' }} {{ $lead->zip_code ?? '—' }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('m/d/Y') : 'N/A' }}</td>
                        <td>
                            @if($lead->gender)
                                <span class="badge bg-{{ $lead->gender == 'Male' ? 'primary' : ($lead->gender == 'Female' ? 'info' : 'secondary') }}">
                                    {{ $lead->gender }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->smoker)
                                <span class="badge bg-warning">Yes</span>
                            @else
                                <span class="badge bg-success">No</span>
                            @endif
                        </td>
                        @hasrole('Super Admin')
                            <td>{{ $lead->driving_license ?? '—' }}</td>
                            <td>{{ $lead->height_weight ?? '—' }}</td>
                            <td>{{ $lead->birth_place ?? '—' }}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 120px;" title="{{ $lead->medical_issue }}">{{ $lead->medical_issue ?? '—' }}</span></td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 120px;" title="{{ $lead->medications }}">{{ $lead->medications ?? '—' }}</span></td>
                            <td>{{ $lead->doctor_name ?? '—' }}</td>
                            <td>{{ $lead->ssn ?? '—' }}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $lead->address }}">{{ $lead->address ?? '—' }}</span></td>
                        @endhasrole
                        <td>{{ $lead->carrier_name ?? '—' }}</td>
                        <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                        <td>${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                        <td>
                            @php
                                $beneficiaries = $lead->beneficiaries ?? [];
                                // Fallback to old fields if no beneficiaries array
                                if (empty($beneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
                                    $beneficiaries = [[
                                        'name' => $lead->beneficiary ?? '',
                                        'dob' => $lead->beneficiary_dob ?? '',
                                        'relation' => ''
                                    ]];
                                }
                            @endphp
                            @if(!empty($beneficiaries))
                                <div style="max-width: 300px; font-size: 0.85rem;">
                                    @foreach($beneficiaries as $index => $beneficiary)
                                        <div class="mb-2 p-1 border-start border-2" style="border-color: #d4af37;">
                                            <div><strong>{{ $index + 1 }}. {{ $beneficiary['name'] ?? '—' }}</strong></div>
                                            @if(!empty($beneficiary['relation']))
                                                <div><small class="text-muted">Rel: {{ $beneficiary['relation'] }}</small></div>
                                            @endif
                                            @if(!empty($beneficiary['dob']))
                                                <div><small class="text-muted">DOB: {{ \Carbon\Carbon::parse($beneficiary['dob'])->format('m/d/Y') }}</small></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        @hasrole('Super Admin')
                            <td>{{ $lead->emergency_contact ?? '—' }}</td>
                            <td>{{ $lead->account_verified_by ?? '—' }}</td>
                            <td>{{ $lead->policy_type ?? '—' }}</td>
                            <td>{{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('m/d/Y') : '—' }}</td>
                            <td>{{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('m/d/Y') : '—' }}</td>
                            <td>{{ $lead->bank_name ?? '—' }}</td>
                            <td>{{ $lead->account_type ?? '—' }}</td>
                            <td>{{ $lead->routing_number ?? '—' }}</td>
                            <td>{{ $lead->acc_number ?? '—' }}</td>
                            <td>{{ $lead->card_number ?? '—' }}</td>
                            <td>{{ $lead->cvv ?? '—' }}</td>
                            <td>{{ $lead->expiry_date ?? '—' }}</td>
                            <td>{{ $lead->source ?? '—' }}</td>
                            <td>{{ $lead->closer_name ?? '—' }}</td>
                            <td>{{ $lead->assigned_partner ?? '—' }}</td>
                            <td>
                                @if($lead->preset_line)
                                    <span class="badge" style="background: var(--gold); color: white;">{{ $lead->preset_line }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="comment-editable" data-lead-id="{{ $lead->id }}" style="max-width: 200px;">
                                    <div class="comment-display" title="Click to edit">
                                        <span class="comment-text text-truncate d-inline-block" style="max-width: 180px; cursor: pointer;">
                                            {{ $lead->comments ?? 'Click to add comment' }}
                                        </span>
                                        <i class="fas fa-edit text-muted ms-1" style="font-size: 11px; cursor: pointer;"></i>
                                    </div>
                                    <div class="comment-edit" style="display: none;">
                                        <textarea class="form-control form-control-sm comment-input" rows="2" style="font-size: 12px;">{{ $lead->comments }}</textarea>
                                        <div class="mt-1">
                                            <button class="btn btn-success btn-sm save-comment" style="padding: 2px 8px; font-size: 11px;">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-secondary btn-sm cancel-comment" style="padding: 2px 8px; font-size: 11px;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endhasrole
                        <td>
                            @if ($lead->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif ($lead->status == 'accepted')
                                <span class="badge bg-success">Approved</span>
                            @elseif ($lead->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif ($lead->status == 'forwarded')
                                <span class="badge bg-info">Forwarded</span>
                            @else
                                <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="40" class="text-center text-muted py-4">
                            <i class="bx bx-info-circle fs-3"></i>
                            <p class="mb-0">No leads found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
