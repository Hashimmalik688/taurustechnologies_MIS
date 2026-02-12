@extends('layouts.master')

@section('title') Bad Leads @endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('build/libs/toastr/build/toastr.min.css') }}" />
@endsection

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
                                    <th width="120" class="text-center">Actions</th>
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
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success" onclick="sendBackLead({{ $badLead->lead_id }}, this)" title="Send back to calling system">
                                                <i class="bx bx-undo"></i> Send Back
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
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

@section('script')
    <script src="{{ URL::asset('build/libs/toastr/build/toastr.min.js') }}"></script>
    <script>
        function sendBackLead(leadId, button) {
            if (!confirm('Are you sure you want to send this lead back to the calling system?')) {
                return;
            }

            // Disable button and show loading
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Sending...';

            fetch('{{ route('ravens.leads.restore') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    lead_id: leadId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Lead restored successfully');
                    // Remove the row from table after successful restoration
                    button.closest('tr').remove();
                    
                    // Check if table is now empty
                    const tbody = document.querySelector('tbody');
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `<tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bx bx-info-circle fs-3"></i>
                                <p class="mb-0">No bad leads found</p>
                            </td>
                        </tr>`;
                    }
                } else {
                    toastr.error(data.message || 'Failed to restore lead');
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while restoring the lead');
                button.disabled = false;
                button.innerHTML = originalHtml;
            });
        }
    </script>
@endsection
