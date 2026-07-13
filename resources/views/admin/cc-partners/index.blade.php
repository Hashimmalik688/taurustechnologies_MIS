@extends('layouts.master')

@section('title') CC Partners @endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 fw-bold"><i class="bx bx-buildings me-1"></i> CC Partners</h5>
            <p class="text-muted mb-0" style="font-size:.82rem;">Outsource sales companies. Each logs into the CC portal, manages its own closers, and submits sales into your pipeline.</p>
        </div>
        <a href="{{ route('admin.cc-partners.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus"></i> Add CC Partner
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Code</th>
                            <th>Login Email</th>
                            <th>Phone</th>
                            <th class="text-center">Closers</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ccPartners as $cc)
                            <tr>
                                <td class="fw-semibold">{{ $cc->name }}</td>
                                <td><span class="badge bg-light text-dark">{{ $cc->code }}</span></td>
                                <td>{{ $cc->email }}</td>
                                <td>{{ $cc->phone ?: '—' }}</td>
                                <td class="text-center">{{ $cc->closers_count }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $cc->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $cc->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.cc-partners.edit', $cc->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.cc-partners.toggle', $cc->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $cc->is_active ? 'danger' : 'success' }}">
                                            {{ $cc->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No CC Partners yet. <a href="{{ route('admin.cc-partners.create') }}">Create your first one →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
