{{-- Reusable Vendor Card Component --}}
<div class="vendor-card glassmorphism-card" data-vendor-id="{{ $vendor->id ?? 1 }}">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="vendor-card-avatar">
                    {{ strtoupper(substr($vendor->name ?? 'VN', 0, 2)) }}
                </div>
                <div>
                    <h5 class="vendor-card-name mb-1">{{ $vendor->name ?? 'Vendor Name' }}</h5>
                    <p class="vendor-card-company mb-2">{{ $vendor->company_name ?? 'Company Name' }}</p>
                    <div class="d-flex gap-2">
                        @if(($vendor->vendor_type ?? 'vendor') == 'us_agent')
                            <span class="badge-us-agent-sm">US Agent</span>
                        @elseif(($vendor->vendor_type ?? 'vendor') == 'vendor')
                            <span class="badge-vendor-sm">Vendor</span>
                        @else
                            <span class="badge-supplier-sm">Supplier</span>
                        @endif

                        @if(($vendor->status ?? 'active') == 'active')
                            <span class="badge-active-sm">Active</span>
                        @elseif(($vendor->status ?? 'active') == 'inactive')
                            <span class="badge-inactive-sm">Inactive</span>
                        @else
                            <span class="badge-suspended-sm">Suspended</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-end">
                <div class="vendor-card-balance">{{ $vendor->balance ?? '$0.00' }}</div>
                <small class="text-muted">Balance</small>
            </div>
        </div>
        <div class="vendor-card-actions mt-3">
            <a href="{{ route('vendors.show', $vendor->id ?? 1) }}" class="btn-card-action btn-view">
                <i class="mdi mdi-eye"></i> View
            </a>
            <a href="{{ route('vendors.edit', $vendor->id ?? 1) }}" class="btn-card-action btn-edit">
                <i class="mdi mdi-pencil"></i> Edit
            </a>
        </div>
    </div>
</div>

<style>
    .vendor-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .vendor-card:hover {
        transform: translateY(-4px);
    }

    .vendor-card-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--bs-surface-900);
    }

    .vendor-card-name {
        color: var(--bs-gold);
        font-weight: 600;
        font-size: 1.1rem;
    }

    .vendor-card-company {
        color: var(--bs-surface-300);
        font-size: 0.9rem;
    }

    .vendor-card-balance {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--bs-ui-success);
    }

    .badge-us-agent-sm, .badge-vendor-sm, .badge-supplier-sm,
    .badge-active-sm, .badge-inactive-sm, .badge-suspended-sm {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--bs-white);
    }

    .badge-us-agent-sm {
        background: linear-gradient(135deg, var(--bs-ui-info) 0%, var(--bs-ui-info-dark) 100%);
    }

    .badge-vendor-sm {
        background: linear-gradient(135deg, var(--bs-ui-purple) 0%, var(--bs-ui-purple) 100%);
    }

    .badge-supplier-sm {
        background: linear-gradient(135deg, var(--bs-ui-warning) 0%, var(--bs-ui-danger-dark) 100%);
    }

    .badge-active-sm {
        background: linear-gradient(135deg, var(--bs-ui-success) 0%, var(--bs-ui-success-dark) 100%);
    }

    .badge-inactive-sm {
        background: linear-gradient(135deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%);
    }

    .badge-suspended-sm {
        background: linear-gradient(135deg, var(--bs-ui-warning) 0%, var(--bs-gold-dark) 100%);
    }

    .vendor-card-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-card-action {
        flex: 1;
        padding: 0.5rem;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-align: center;
        text-decoration: none;
    }

    .btn-view {
        background: rgba(59, 130, 246, 0.2);
        color: var(--bs-ui-info);
    }

    .btn-view:hover {
        background: rgba(59, 130, 246, 0.3);
        color: var(--bs-ui-info);
    }

    .btn-edit {
        background: rgba(34, 197, 94, 0.2);
        color: var(--bs-ui-success);
    }

    .btn-edit:hover {
        background: rgba(34, 197, 94, 0.3);
        color: var(--bs-ui-success);
    }
</style>
