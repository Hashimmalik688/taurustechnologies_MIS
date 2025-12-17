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
        background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
    }

    .vendor-card-name {
        color: #d4af37;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .vendor-card-company {
        color: #cbd5e1;
        font-size: 0.9rem;
    }

    .vendor-card-balance {
        font-size: 1.5rem;
        font-weight: 700;
        color: #10b981;
    }

    .badge-us-agent-sm, .badge-vendor-sm, .badge-supplier-sm,
    .badge-active-sm, .badge-inactive-sm, .badge-suspended-sm {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        color: white;
    }

    .badge-us-agent-sm {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .badge-vendor-sm {
        background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%);
    }

    .badge-supplier-sm {
        background: linear-gradient(135deg, #f97316 0%, #c2410c 100%);
    }

    .badge-active-sm {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .badge-inactive-sm {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .badge-suspended-sm {
        background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
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
        color: #60a5fa;
    }

    .btn-view:hover {
        background: rgba(59, 130, 246, 0.3);
        color: #60a5fa;
    }

    .btn-edit {
        background: rgba(34, 197, 94, 0.2);
        color: #4ade80;
    }

    .btn-edit:hover {
        background: rgba(34, 197, 94, 0.3);
        color: #4ade80;
    }
</style>
