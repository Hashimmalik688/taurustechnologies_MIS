@extends('layouts.master')

@section('title', 'Announcements Management')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bx bx-bell"></i> System Announcements
            </h2>
            @can('create', App\Models\Announcement::class)
                <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Create Announcement
                </a>
            @endcan
        </div>

        <!-- Alerts -->
        @if($message = session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Announcements Table -->
        <div class="card">
            <div class="card-body">
                @if($announcements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Animation</th>
                                    <th>Auto Dismiss</th>
                                    <th>Created By</th>
                                    <th>Published</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($announcements as $announcement)
                                    <tr>
                                        <td>
                                            <strong>{{ $announcement->title }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-muted small" title="{{ $announcement->message }}">
                                                {{ Str::limit($announcement->message, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($announcement->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: var(--gold); color: #111;">
                                                {{ ucfirst($announcement->animation) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $announcement->auto_dismiss }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $announcement->createdBy->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $announcement->published_at->format('M d, Y H:i') ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bx bx-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $announcements->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bx bx-bell-off" style="font-size: 3rem; color: #d1d5db;"></i>
                        <p class="text-muted mt-3">No announcements yet. Create one to get started!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .page-wrapper {
        padding: 1.5rem 0;
    }

    .page-content {
        max-width: 1200px;
        margin: 0 auto;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .btn-group {
        gap: 0.25rem;
    }
</style>
@endsection
