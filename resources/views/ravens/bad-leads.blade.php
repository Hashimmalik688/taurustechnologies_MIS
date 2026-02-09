@extends('layouts.master')

@section('title') Bad Leads @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Ravens @endslot
        @slot('title') Bad Leads @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Bad Leads - Disposed Contacts</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Lead Name</th>
                                    <th>Phone</th>
                                    <th>Disposition</th>
                                    <th>Disposed By</th>
                                    <th>Date</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($badLeads as $index => $badLead)
                                    <tr>
                                        <td>{{ $badLeads->firstItem() + $index }}</td>
                                        <td>{{ $badLead->lead_name ?? 'N/A' }}</td>
                                        <td>{{ $badLead->lead_phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($badLead->disposition === 'no_answer') bg-warning
                                                @elseif($badLead->disposition === 'wrong_number') bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ \App\Models\BadLead::getDispositionLabel($badLead->disposition) }}
                                            </span>
                                        </td>
                                        <td>{{ $badLead->disposedBy->name ?? 'Unknown' }}</td>
                                        <td>{{ $badLead->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $badLead->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0">No bad leads found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $badLeads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
