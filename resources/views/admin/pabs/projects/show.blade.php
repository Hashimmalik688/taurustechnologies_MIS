@extends('layouts.master')

@section('title', $project->project_code . ' - ' . $project->project_name)

@section('css')
<style>
    .workflow-step {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
    }

    .workflow-step.active {
        background-color: #e7f3ff;
        border-color: #084298;
    }

    .workflow-step.completed {
        background-color: #d1e7dd;
        border-color: #0f5132;
    }

    .workflow-step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-weight: bold;
        color: white;
        flex-shrink: 0;
    }

    .workflow-step.completed .workflow-step-icon {
        background-color: #198754;
    }

    .workflow-step.active .workflow-step-icon {
        background-color: #0d6efd;
    }

    .workflow-step .workflow-step-icon {
        background-color: #6c757d;
    }

    .workflow-step-content {
        flex: 1;
    }

    .budget-summary {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .budget-item {
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
    }

    .budget-item-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .budget-item-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #000;
    }

    .variance-alert {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .quote-card {
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .quote-card.lowest {
        border: 2px solid #28a745;
        background-color: #f1f9f6;
    }

    .comments-section {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
        margin-bottom: 1rem;
    }

    .comment-item {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border-left: 3px solid #0d6efd;
        background-color: white;
        border-radius: 0.25rem;
    }

    .comment-meta {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    @component('components.breadcrumb')
        @slot('title') Project Details @endslot
    @endcomponent

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Project Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ $project->project_code }}</h4>
                            <h5>{{ $project->project_name }}</h5>
                            <p class="text-muted mb-0">{{ Str::limit($project->description, 100) }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-primary">{{ $project->status }}</span>
                            @if($project->priority)
                                <span class="badge bg-warning">{{ $project->priority }}</span>
                            @endif
                            <p class="text-muted small mt-2">
                                Section: <strong>{{ $sections[$project->section_id] ?? 'N/A' }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Actions</h6>
                    <div class="d-grid gap-2">
                        @if($project->status === 'DRAFT')
                            <form action="{{ route('pabs.projects.moveToScoping', $project) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">Move to Scoping</button>
                            </form>
                            <a href="{{ route('pabs.projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @elseif($project->status === 'QUOTING')
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quotesModal">Add Vendor Quotes</button>
                        @elseif($project->status === 'PENDING APPROVAL' && auth()->user()->hasRole('CEO|Super Admin'))
                            <a href="{{ route('pabs.projects.approval', $project) }}" class="btn btn-sm btn-outline-warning">Review for Approval</a>
                        @elseif($project->status === 'BUDGET ALLOCATED')
                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#executionModal">Start Execution</button>
                        @elseif($project->status === 'IN PROGRESS')
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#completeModal">Mark Complete</button>
                        @elseif($project->status === 'COMPLETED' && auth()->user()->hasRole('CEO|Super Admin'))
                            <form action="{{ route('pabs.projects.archive', $project) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Archive Project</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Variance Alert -->
    @if($project->variance_flagged)
        <div class="variance-alert">
            <strong><i class="bx bx-error"></i> Variance Detected!</strong>
            <p class="mb-0">{{ $project->variance_notes }}</p>
        </div>
    @endif

    <!-- Main Content -->
    <div class="row">
        <!-- Left: Workflow & Details -->
        <div class="col-lg-8">
            <!-- Workflow Steps -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Workflow Progress</h6>
                </div>
                <div class="card-body">
                    <!-- Step 1: Initiation -->
                    <div class="workflow-step {{ $project->status !== 'DRAFT' ? 'completed' : 'active' }}">
                        <div class="workflow-step-icon">1</div>
                        <div class="workflow-step-content">
                            <strong>Step 1: Initiation (DRAFT)</strong>
                            <small class="d-block text-muted">Project created with job title and justification</small>
                            <small class="d-block">{{ $project->created_at->format('M d, Y H:i') }} by {{ $project->creator->name }}</small>
                        </div>
                    </div>

                    <!-- Step 2: Scoping -->
                    <div class="workflow-step {{ in_array($project->status, ['QUOTING', 'PENDING APPROVAL', 'BUDGET ALLOCATED', 'IN PROGRESS', 'COMPLETED']) ? 'completed' : (in_array($project->status, ['SCOPING']) ? 'active' : '') }}">
                        <div class="workflow-step-icon">2</div>
                        <div class="workflow-step-content">
                            <strong>Step 2: Scoping</strong>
                            <small class="d-block text-muted">Survey conducted and requirements document uploaded</small>
                            @if($project->scoping_completed_at)
                                <small class="d-block">{{ $project->scoping_completed_at->format('M d, Y H:i') }} by {{ $project->scopingLead->name ?? 'N/A' }}</small>
                                @if($project->scoping_document_path)
                                    <small><a href="{{ asset('storage/' . $project->scoping_document_path) }}" target="_blank" class="text-decoration-none">
                                        <i class="bx bx-download"></i> View Document
                                    </a></small>
                                @endif
                            @endif
                        </div>
                        @if($project->status === 'SCOPING')
                            <form action="{{ route('pabs.projects.completeScopingAndQuote', $project) }}" method="POST" enctype="multipart/form-data" class="ms-auto">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="file" name="scoping_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <button type="submit" class="btn btn-primary">Upload & Continue</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    <!-- Step 3: Quoting -->
                    <div class="workflow-step {{ in_array($project->status, ['PENDING APPROVAL', 'BUDGET ALLOCATED', 'IN PROGRESS', 'COMPLETED']) ? 'completed' : (in_array($project->status, ['QUOTING']) ? 'active' : '') }}">
                        <div class="workflow-step-icon">3</div>
                        <div class="workflow-step-content">
                            <strong>Step 3: Quoting</strong>
                            <small class="d-block text-muted">Market rates entered for 3 vendors</small>
                            @if($project->vendor_a_quote || $project->vendor_b_quote || $project->vendor_c_quote)
                                <small class="d-block">
                                    Lowest: <strong>${{ number_format($project->getLowestQuote(), 2) }}</strong> | 
                                    Average: <strong>${{ number_format($project->getAverageQuote(), 2) }}</strong>
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- Step 4: Approval -->
                    <div class="workflow-step {{ in_array($project->status, ['BUDGET ALLOCATED', 'IN PROGRESS', 'COMPLETED']) ? 'completed' : (in_array($project->status, ['PENDING APPROVAL']) ? 'active' : '') }}">
                        <div class="workflow-step-icon">4</div>
                        <div class="workflow-step-content">
                            <strong>Step 4: Executive Review (CEO Approval)</strong>
                            <small class="d-block text-muted">CEO approves with budget and deadline</small>
                            @if($project->approved_at)
                                <small class="d-block">{{ $project->approved_at->format('M d, Y H:i') }} by {{ $project->approver->name ?? 'N/A' }}</small>
                            @endif
                        </div>
                    </div>

                    <!-- Step 5: Allocation -->
                    <div class="workflow-step {{ in_array($project->status, ['IN PROGRESS', 'COMPLETED']) ? 'completed' : (in_array($project->status, ['BUDGET ALLOCATED']) ? 'active' : '') }}">
                        <div class="workflow-step-icon">5</div>
                        <div class="workflow-step-content">
                            <strong>Step 5: Allocation (Finance Lock)</strong>
                            <small class="d-block text-muted">Accounts confirms funds available</small>
                            @if($project->allocated_at)
                                <small class="d-block">{{ $project->allocated_at->format('M d, Y H:i') }} by {{ $project->allocatedBy->name ?? 'N/A' }}</small>
                            @endif
                        </div>
                    </div>

                    <!-- Step 6: Execution -->
                    <div class="workflow-step {{ $project->status === 'COMPLETED' ? 'completed' : (in_array($project->status, ['IN PROGRESS']) ? 'active' : '') }}">
                        <div class="workflow-step-icon">6</div>
                        <div class="workflow-step-content">
                            <strong>Step 6: Execution (The Work)</strong>
                            <small class="d-block text-muted">Work completed with progress updates</small>
                            @if($project->started_at)
                                <small class="d-block">Started: {{ $project->started_at->format('M d, Y') }}</small>
                            @endif
                        </div>
                    </div>

                    <!-- Step 7: Completion -->
                    <div class="workflow-step {{ $project->status === 'COMPLETED' || $project->status === 'ARCHIVED' ? 'completed' : '' }}">
                        <div class="workflow-step-icon">7</div>
                        <div class="workflow-step-content">
                            <strong>Step 7: Verification & Closure</strong>
                            <small class="d-block text-muted">Final invoice submitted; completion marked</small>
                            @if($project->completed_at)
                                <small class="d-block">{{ $project->completed_at->format('M d, Y H:i') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Quotes Section -->
            @if($project->status !== 'DRAFT' && $project->status !== 'SCOPING')
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Vendor Quotes</h6>
                    </div>
                    <div class="card-body">
                        @if($project->status === 'QUOTING')
                            @if($project->vendor_a_quote || $project->vendor_b_quote || $project->vendor_c_quote)
                                <p class="mb-3">
                                    <strong>Lowest Quote:</strong> ${{ number_format($project->getLowestQuote(), 2) }} |
                                    <strong>Average Quote:</strong> ${{ number_format($project->getAverageQuote(), 2) }}
                                </p>
                            @endif
                        @endif

                        <div class="row">
                            @foreach(['a' => 'Vendor A', 'b' => 'Vendor B', 'c' => 'Vendor C'] as $key => $label)
                                <div class="col-md-4">
                                    <div class="quote-card {{ ($key === 'a' && $project->vendor_a_quote == $project->getLowestQuote() && $project->vendor_a_quote) ? 'lowest' : (($key === 'b' && $project->vendor_b_quote == $project->getLowestQuote() && $project->vendor_b_quote) ? 'lowest' : (($key === 'c' && $project->vendor_c_quote == $project->getLowestQuote() && $project->vendor_c_quote) ? 'lowest' : '')) }}">
                                        <small class="text-muted">{{ $label }}</small>
                                        <p class="mb-1">
                                            <strong>{{ ${'project'}->{'vendor_' . $key . '_name'} ?? 'N/A' }}</strong>
                                        </p>
                                        <h6 class="mb-0">${{ ${'project'}->{'vendor_' . $key . '_quote'} ? number_format(${'project'}->{'vendor_' . $key . '_quote'}, 2) : 'N/A' }}</h6>
                                        @if(($key === 'a' && $project->vendor_a_quote == $project->getLowestQuote() && $project->vendor_a_quote) || ($key === 'b' && $project->vendor_b_quote == $project->getLowestQuote() && $project->vendor_b_quote) || ($key === 'c' && $project->vendor_c_quote == $project->getLowestQuote() && $project->vendor_c_quote))
                                            <small class="text-success d-block mt-2"><i class="bx bx-check"></i> Lowest</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tickets Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Related Tickets</h6>
                </div>
                <div class="card-body">
                    @if($project->tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->tickets as $ticket)
                                        <tr>
                                            <td><small><strong>{{ $ticket->ticket_code }}</strong></small></td>
                                            <td><small>{{ Str::limit($ticket->subject, 25) }}</small></td>
                                            <td><small><span class="badge bg-{{ $ticket->status == 'OPEN' ? 'primary' : ($ticket->status == 'IN PROGRESS' ? 'info' : ($ticket->status == 'RESOLVED' ? 'success' : 'warning')) }}">{{ $ticket->status }}</span></small></td>
                                            <td>
                                                <a href="{{ route('pabs.tickets.show', $ticket) }}" class="btn btn-xs btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No tickets linked to this project.</p>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Progress Updates & Comments</h6>
                </div>
                <div class="card-body">
                    <div class="comments-section">
                        @forelse($project->comments as $comment)
                            <div class="comment-item">
                                <div class="comment-meta">
                                    <strong>{{ $comment->user->name }}</strong> - {{ $comment->created_at->format('M d, Y H:i') }}
                                </div>
                                <p class="mb-0">{{ $comment->comment }}</p>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No comments yet.</p>
                        @endforelse
                    </div>

                    @if(in_array($project->status, ['IN PROGRESS']))
                        <form action="{{ route('pabs.projects.addComment', $project) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="comment" class="form-control form-control-sm" placeholder="Add progress update..." required>
                                <button type="submit" class="btn btn-sm btn-primary">Post</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Budget & Summary -->
        <div class="col-lg-4">
            <!-- Budget Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Budget Summary</h6>
                </div>
                <div class="card-body">
                    <div class="budget-summary">
                        <div class="budget-item">
                            <div class="budget-item-label">Approved Budget</div>
                            <div class="budget-item-value">
                                @if($project->approved_budget)
                                    ${{ number_format($project->approved_budget, 2) }}
                                @else
                                    <span class="text-muted" style="font-size: 0.875rem;">Pending</span>
                                @endif
                            </div>
                        </div>
                        <div class="budget-item">
                            <div class="budget-item-label">Actual Cost</div>
                            <div class="budget-item-value">
                                @if($project->actual_cost)
                                    ${{ number_format($project->actual_cost, 2) }}
                                @else
                                    <span class="text-muted" style="font-size: 0.875rem;">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($project->actual_cost && $project->approved_budget)
                        <div class="alert {{ $project->hasVariance() ? 'alert-danger' : 'alert-success' }} py-2">
                            <small>
                                @if($project->hasVariance())
                                    <strong>Variance:</strong> +${{ number_format($project->getVarianceAmount(), 2) }} ({{ number_format($project->getVariancePercentage(), 2) }}%)
                                @else
                                    <strong>Within Budget ✓</strong>
                                @endif
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Info -->
            @if($project->approved_at)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Approval Details</h6>
                    </div>
                    <div class="card-body small">
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Approved By:</div>
                            <div class="col-7"><strong>{{ $project->approver->name ?? 'N/A' }}</strong></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Approved Date:</div>
                            <div class="col-7">{{ $project->approved_at->format('M d, Y') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Priority:</div>
                            <div class="col-7"><strong>{{ $project->priority ?? 'N/A' }}</strong></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Target Deadline:</div>
                            <div class="col-7">{{ $project->target_deadline ? $project->target_deadline->format('M d, Y') : 'N/A' }}</div>
                        </div>
                        @if($project->approval_notes)
                            <div class="row">
                                <div class="col-12 text-muted">Notes:</div>
                                <div class="col-12"><small>{{ $project->approval_notes }}</small></div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Approval History -->
            @if($project->approvals->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Approval History</h6>
                    </div>
                    <div class="card-body small">
                        @foreach($project->approvals as $approval)
                            <div class="mb-2 pb-2 border-bottom">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong>{{ $approval->action }}</strong>
                                    <small class="text-muted">{{ $approval->approved_at->format('M d, Y') }}</small>
                                </div>
                                <small class="text-muted">By: {{ $approval->approver->name }}</small>
                                @if($approval->comments)
                                    <p class="mb-0 mt-1"><small>{{ $approval->comments }}</small></p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->

<!-- Quotes Modal -->
<div class="modal fade" id="quotesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vendor Quotes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pabs.projects.addQuotes', $project) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @for($i = 0; $i < 3; $i++)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Vendor {{ chr(65 + $i) }} Name</label>
                                <input type="text" name="vendor_{{ chr(97 + $i) }}_name" class="form-control" placeholder="e.g., ABC Construction Co.">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vendor {{ chr(65 + $i) }} Quote</label>
                                <input type="number" name="vendor_{{ chr(97 + $i) }}_quote" class="form-control" placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="move_to_approval" value="1">Save & Move to Approval</button>
                    <button type="submit" class="btn btn-outline-primary">Save Only</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Execution Modal -->
<div class="modal fade" id="executionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start Execution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pabs.projects.startExecution', $project) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">-- Select User --</option>
                            <!-- Add users list here -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Start Execution</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Project Complete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pabs.projects.complete', $project) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Actual Cost <span class="text-danger">*</span></label>
                        <input type="number" name="actual_cost" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                        @if($project->approved_budget)
                            <small class="form-text text-muted">Approved Budget: ${{ number_format($project->approved_budget, 2) }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Final Notes</label>
                        <textarea name="final_notes" class="form-control" rows="3" placeholder="Add any final notes or remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Mark Complete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
