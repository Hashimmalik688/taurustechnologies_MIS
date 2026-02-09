@extends('layouts.master')

@section('title', 'Approve Project - ' . $project->project_code)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @component('components.breadcrumb')
        @slot('title') Project Approval Dashboard @endslot
    @endcomponent

    <div class="row">
        <!-- Project Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Project Details for Approval</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Code</h6>
                            <p><strong>{{ $project->project_code }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Section</h6>
                            <p><strong>{{ $sections[$project->section_id] ?? 'N/A' }}</strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Project Name</h6>
                            <p><strong>{{ $project->project_name }}</strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Description</h6>
                            <p>{{ $project->description }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Created By</h6>
                            <p>{{ $project->creator->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Created Date</h6>
                            <p>{{ $project->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Quotes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Vendor Quotes Comparison</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th class="text-end">Quote</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['a' => 'Vendor A', 'b' => 'Vendor B', 'c' => 'Vendor C'] as $key => $label)
                                    <tr>
                                        <td>
                                            {{ ${'project'}->{'vendor_' . $key . '_name'} ?? $label }}
                                        </td>
                                        <td class="text-end">
                                            ${{ ${'project'}->{'vendor_' . $key . '_quote'} ? number_format(${'project'}->{'vendor_' . $key . '_quote'}, 2) : '-' }}
                                        </td>
                                        <td>
                                            @if(($key === 'a' && $project->vendor_a_quote == $project->getLowestQuote() && $project->vendor_a_quote) || ($key === 'b' && $project->vendor_b_quote == $project->getLowestQuote() && $project->vendor_b_quote) || ($key === 'c' && $project->vendor_c_quote == $project->getLowestQuote() && $project->vendor_c_quote))
                                                <span class="badge bg-success">Lowest</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($project->getLowestQuote())
                        <div class="alert alert-info py-2">
                            <small>
                                <strong>Lowest Quote:</strong> ${{ number_format($project->getLowestQuote(), 2) }} |
                                <strong>Average Quote:</strong> ${{ number_format($project->getAverageQuote(), 2) }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval History -->
            @if($project->approvals->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Approval History</h6>
                    </div>
                    <div class="card-body">
                        @foreach($project->approvals as $approval)
                            <div class="alert alert-{{ $approval->action === 'APPROVED' ? 'success' : ($approval->action === 'REJECTED' ? 'danger' : 'warning') }} py-2 mb-2">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>{{ $approval->action }}</strong> - {{ $approval->approved_at->format('M d, Y H:i') }}
                                        <br><small class="text-muted">By: {{ $approval->approver->name }}</small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if($approval->approved_budget)
                                            <small><strong>Budget:</strong> ${{ number_format($approval->approved_budget, 2) }}</small>
                                        @endif
                                    </div>
                                </div>
                                @if($approval->comments)
                                    <hr class="my-2">
                                    <small>{{ $approval->comments }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Approval Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h6 class="card-title mb-0">CEO Approval Box</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('pabs.projects.processApproval', $project) }}" method="POST">
                        @csrf

                        <!-- Approval Status -->
                        <div class="mb-3">
                            <label class="form-label">Decision <span class="text-danger">*</span></label>
                            <div class="btn-group d-block mb-3" role="group">
                                <input type="radio" class="btn-check" name="approval_status" id="approved" value="APPROVED" checked>
                                <label class="btn btn-outline-success" for="approved">Approve</label>

                                <input type="radio" class="btn-check" name="approval_status" id="clarification" value="CLARIFICATION NEEDED">
                                <label class="btn btn-outline-warning" for="clarification">Clarification</label>

                                <input type="radio" class="btn-check" name="approval_status" id="rejected" value="REJECTED">
                                <label class="btn btn-outline-danger" for="rejected">Reject</label>
                            </div>
                        </div>

                        <!-- Approved Budget -->
                        <div class="mb-3" id="approvedBudgetDiv">
                            <label class="form-label">Approved Budget <span class="text-danger">*</span></label>
                            <input type="number" name="approved_budget" id="approvedBudget" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ old('approved_budget', $project->getLowestQuote() ? $project->getLowestQuote() : '') }}" required>
                            @if($project->getLowestQuote())
                                <small class="form-text text-muted">Lowest Quote: ${{ number_format($project->getLowestQuote(), 2) }}</small>
                            @endif
                        </div>

                        <!-- Target Deadline -->
                        <div class="mb-3" id="deadlineDiv">
                            <label class="form-label">Target Deadline <span class="text-danger">*</span></label>
                            <input type="date" name="target_deadline" id="deadline" class="form-control" value="{{ old('target_deadline') }}" required>
                        </div>

                        <!-- Priority -->
                        <div class="mb-3" id="priorityDiv">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="MEDIUM" selected>Medium</option>
                                <option value="HIGH">High</option>
                                <option value="LOW">Low</option>
                            </select>
                        </div>

                        <!-- Notes/Reason -->
                        <div class="mb-3">
                            <label class="form-label">Notes / Reason</label>
                            <textarea name="approval_notes" class="form-control" rows="4" placeholder="Add approval notes or reason for rejection/clarification..."></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100">Submit Decision</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="approval_status"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isApproved = this.value === 'APPROVED';
            document.getElementById('approvedBudgetDiv').style.display = isApproved ? 'block' : 'none';
            document.getElementById('deadlineDiv').style.display = isApproved ? 'block' : 'none';
            document.getElementById('priorityDiv').style.display = isApproved ? 'block' : 'none';
            
            // Update required attribute
            document.getElementById('approvedBudget').required = isApproved;
            document.getElementById('deadline').required = isApproved;
            document.getElementById('priority').required = isApproved;
        });
    });
    
    // Trigger on load
    document.getElementById('approved').dispatchEvent(new Event('change'));
</script>
@endsection
