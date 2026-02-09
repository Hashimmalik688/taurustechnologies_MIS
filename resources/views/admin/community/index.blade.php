@extends('layouts.master')

@section('title', 'Communities Management')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bx bx-group"></i> Communities
            </h2>
        </div>

        @if($communities->count() > 0)
            <!-- Communities Grid -->
            <div class="row">
                @foreach($communities as $community)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <!-- Avatar -->
                                    @if($community->avatar)
                                        <img src="{{ asset('storage/' . $community->avatar) }}" 
                                             alt="{{ $community->name }}" 
                                             style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; margin-right: 12px;">
                                    @else
                                        <div style="width: 48px; height: 48px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                            <i class="bx bx-group" style="font-size: 24px; color: #6b7280;"></i>
                                        </div>
                                    @endif
                                    
                                    <div style="flex: 1;">
                                        <h6 class="card-title mb-1">{{ $community->name }}</h6>
                                        <small class="text-muted">by {{ $community->creator->name }}</small>
                                    </div>
                                </div>
                                
                                @if($community->description)
                                    <p class="card-text small text-muted mb-3">{{ $community->description }}</p>
                                @endif

                                <div class="small text-muted mb-3">
                                    <i class="bx bx-calendar"></i> Created {{ $community->created_at->format('M d, Y') }}
                                </div>

                                <div class="d-flex gap-2">
                                    @if(auth()->id() === $community->created_by || auth()->user()->hasRole('Super Admin'))
                                        <a href="{{ route('admin.communities.edit', $community->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-pencil"></i> Edit
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('admin.communities.destroy', $community->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this community?');">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $communities->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-group" style="font-size: 48px; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">No Communities Yet</h5>
                    <p class="text-muted mb-0">Communities will appear here once they are created</p>
                    @hasrole('Manager')
                        <a href="{{ route('admin.communities.create') }}" class="btn btn-primary mt-3">
                            <i class="bx bx-plus me-1"></i> Create First Community
                        </a>
                    @endhasrole
                </div>
            </div>
        @endif
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
        transition: box-shadow 0.3s;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .card-title {
        color: #1f2937;
        font-weight: 600;
    }

    .badge {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endsection
