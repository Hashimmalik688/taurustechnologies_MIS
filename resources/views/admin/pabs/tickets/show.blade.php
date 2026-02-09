@extends('layouts.master')

@section('title', $ticket->ticket_code . ' - ' . $ticket->subject)

@section('css')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-open { background-color: #cfe2ff; color: #084298; }
    .status-in-progress { background-color: #d1ecf1; color: #055160; }
    .status-on-hold { background-color: #fff3cd; color: #664d03; }
    .status-resolved { background-color: #d1e7dd; color: #0f5132; }
    .status-closed { background-color: #e2e3e5; color: #41464b; }

    .priority-high { color: #dc3545; font-weight: 600; }
    .priority-medium { color: #fd7e14; font-weight: 600; }
    .priority-low { color: #28a745; font-weight: 600; }

    .comments-section {
        max-height: 500px;
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
        @slot('title') Ticket Details @endslot
    @endcomponent

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Ticket Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $ticket->ticket_code }}</h4>
                            <h5>{{ $ticket->subject }}</h5>
                            <p class="text-muted mb-0">{{ Str::limit($ticket->description, 150) }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="status-badge status-{{ Str::lower($ticket->status) }}">
                                {{ $ticket->status }}
                            </span>
                            <p class="text-muted small mt-2">
                                <span class="priority-{{ Str::lower($ticket->priority) }}">
                                    {{ $ticket->priority }} Priority
                                </span>
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
                        @if($ticket->created_by === auth()->id() || auth()->user()->hasRole(['Super Admin', 'CEO']))
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal">Edit Ticket</button>
                        @endif
                        
                        <!-- Assigned User Actions -->
                        @if($ticket->assigned_to === auth()->id() && $ticket->approval_status === 'PENDING')
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">Accept Ticket</button>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject Ticket</button>
                        @endif
                        
                        @if($ticket->status !== 'CLOSED')
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#closeModal">Close Ticket</button>
                        @endif
                        
                        @if($ticket->created_by === auth()->id() || auth()->user()->hasRole(['Super Admin', 'CEO']))
                            <form action="{{ route('pabs.tickets.destroy', $ticket) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100">Delete Ticket</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left: Ticket Details -->
        <div class="col-lg-8">
            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Ticket Description</h6>
                </div>
                <div class="card-body">
                    {{ $ticket->description }}
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Comments & Updates</h6>
                    @if($ticket->assigned_to === auth()->id())
                        <small class="text-muted d-block mt-1">Only you and the creator can comment</small>
                    @endif
                </div>
                <div class="card-body">
                    <div class="comments-section">
                        @forelse($ticket->comments as $comment)
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

                    @if(!in_array($ticket->status, ['CLOSED']) && ($ticket->assigned_to === auth()->id() || $ticket->created_by === auth()->id()))
                        <form action="{{ route('pabs.tickets.addComment', $ticket) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="comment" class="form-control form-control-sm" placeholder="Add comment..." required>
                                <button type="submit" class="btn btn-sm btn-primary">Post</button>
                            </div>
                        </form>
                    @elseif(!in_array($ticket->status, ['CLOSED']) && $ticket->assigned_to === null && $ticket->created_by === auth()->id())
                        <form action="{{ route('pabs.tickets.addComment', $ticket) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="comment" class="form-control form-control-sm" placeholder="Add comment..." required>
                                <button type="submit" class="btn btn-sm btn-primary">Post</button>
                            </div>
                        </form>
                    @elseif(in_array($ticket->status, ['CLOSED']))
                        <div class="alert alert-info alert-sm mb-0">
                            <small>Comments are closed for this ticket.</small>
                        </div>
                    @elseif($ticket->assigned_to !== auth()->id() && $ticket->created_by !== auth()->id())
                        <div class="alert alert-warning alert-sm mb-0">
                            <small>Only the assigned user and creator can comment on this ticket.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Ticket Info -->
        <div class="col-lg-4">
            <!-- Ticket Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Ticket Information</h6>
                </div>
                <div class="card-body small">
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Code:</div>
                        <div class="col-7"><strong>{{ $ticket->ticket_code }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Section:</div>
                        <div class="col-7"><strong>{{ $sections[$ticket->section_id] ?? 'N/A' }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Status:</div>
                        <div class="col-7">
                            <span class="status-badge status-{{ Str::lower($ticket->status) }}">
                                {{ $ticket->status }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Priority:</div>
                        <div class="col-7">
                            <span class="priority-{{ Str::lower($ticket->priority) }}">
                                {{ $ticket->priority }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Approval:</div>
                        <div class="col-7">
                            <span class="badge bg-{{ $ticket->approval_status === 'APPROVED' ? 'success' : ($ticket->approval_status === 'REJECTED' ? 'danger' : 'warning') }}">
                                {{ $ticket->approval_status }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Quote/Amount:</div>
                        <div class="col-7">
                            @if($ticket->quote_amount)
                                <strong>PKR {{ number_format($ticket->quote_amount, 2) }}</strong>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Estimated Budget:</div>
                        <div class="col-7">
                            @if($ticket->total_cost)
                                <strong>PKR {{ number_format($ticket->total_cost, 2) }}</strong>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Created:</div>
                        <div class="col-7">{{ $ticket->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Created By:</div>
                        <div class="col-7">{{ $ticket->creator->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Assignment -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Assignment</h6>
                </div>
                <div class="card-body small">
                    @if($ticket->assignee)
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Assigned To:</div>
                            <div class="col-7"><strong>{{ $ticket->assignee->name }}</strong></div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Not yet assigned</p>
                    @endif

                    @if($ticket->resolved_at)
                        <div class="row">
                            <div class="col-5 text-muted">Resolved:</div>
                            <div class="col-7">{{ $ticket->resolved_at->format('M d, Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pabs.tickets.update', $ticket) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" value="{{ $ticket->subject }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required>{{ $ticket->description }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="HIGH" {{ $ticket->priority == 'HIGH' ? 'selected' : '' }}>High</option>
                                    <option value="MEDIUM" {{ $ticket->priority == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                    <option value="LOW" {{ $ticket->priority == 'LOW' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="OPEN" {{ $ticket->status == 'OPEN' ? 'selected' : '' }}>Open</option>
                                    <option value="IN PROGRESS" {{ $ticket->status == 'IN PROGRESS' ? 'selected' : '' }}>In Progress</option>
                                    <option value="ON HOLD" {{ $ticket->status == 'ON HOLD' ? 'selected' : '' }}>On Hold</option>
                                    <option value="RESOLVED" {{ $ticket->status == 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                                    <option value="CLOSED" {{ $ticket->status == 'CLOSED' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Assign To</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estimated Budget</label>
                        <div class="input-group">
                            <span class="input-group-text">PKR</span>
                            <input type="number" name="total_cost" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ $ticket->total_cost ?? '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quote/Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">PKR</span>
                            <input type="number" name="quote_amount" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ $ticket->quote_amount ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Close Modal -->
<div class="modal fade" id="closeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pabs.tickets.close', $ticket) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to close this ticket? It will no longer be editable.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Close Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accept Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pabs.tickets.approve', $ticket) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to accept this ticket?</p>
                    <div class="mb-3">
                        <label class="form-label">Approval Notes <span class="text-danger">*</span></label>
                        <textarea name="approval_notes" class="form-control" rows="3" placeholder="Add any notes about accepting this ticket..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Accept Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pabs.tickets.reject', $ticket) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject this ticket? You will be unassigned and the ticket will be reopened.</p>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="approval_notes" class="form-control" rows="3" placeholder="Please provide a reason for rejecting this ticket..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reject Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
