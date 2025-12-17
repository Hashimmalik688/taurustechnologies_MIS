<?php $__env->startSection('title'); ?>
    Agents List
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('/assets/libs/datatables/datatables.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        :root {
            --gold: #d4af37;
            --gold-light: #f4e4b4;
            --gold-dark: #b8941f;
        }

        .agents-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .agents-header h3 {
            color: var(--gold);
            font-weight: 900;
            margin: 0;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .agents-header .subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        .agents-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            border: 1px solid rgba(212, 175, 55, 0.2);
            overflow: hidden;
        }

        .agents-card .card-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(244, 228, 180, 0.1));
            padding: 1.5rem 2rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }

        .agents-card .card-title {
            color: var(--gold);
            font-weight: 900;
            font-size: 1.3rem;
            margin: 0;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            border: none;
            color: #111;
            font-weight: 700;
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(212, 175, 55, 0.5);
            color: #111;
        }

        .btn-outline-gold {
            background: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
            font-weight: 700;
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-outline-gold:hover {
            background: var(--gold);
            color: #111;
            transform: translateY(-2px);
        }

        #datatable {
            color: #e2e8f0;
        }

        #datatable thead th {
            background: rgba(15, 23, 42, 0.7);
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 1.25rem 1rem;
            border-bottom: 2px solid var(--gold);
            letter-spacing: 1.5px;
            color: var(--gold) !important;
        }

        #datatable tbody tr {
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            transition: all 0.3s ease;
            background: rgba(15, 23, 42, 0.3);
        }

        #datatable tbody tr:hover {
            background: linear-gradient(to right, rgba(212, 175, 55, 0.15), transparent);
            transform: scale(1.01);
        }

        #datatable tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            color: #e2e8f0;
            border-color: rgba(212, 175, 55, 0.1);
        }

        .agent-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.2rem;
            color: #111;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .agent-info-name {
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            margin-bottom: 0.25rem;
        }

        .agent-info-email {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #34d399;
            box-shadow: 0 0 10px rgba(52, 211, 153, 0.5);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .badge-gold {
            background: rgba(212, 175, 55, 0.2);
            color: var(--gold);
            border: 1px solid var(--gold);
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.75rem;
        }

        .badge-carrier {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid #10b981;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.7rem;
            margin: 0.2rem;
            display: inline-block;
        }

        .badge-state {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid #f59e0b;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.7rem;
            margin: 0.2rem;
            display: inline-block;
        }

        .address-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #cbd5e1;
        }

        .address-cell:hover {
            white-space: normal;
            overflow: visible;
        }

        .carriers-list, .states-list {
            max-width: 250px;
        }

        .dropdown-menu {
            background: rgba(30, 41, 59, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .dropdown-item {
            color: #e2e8f0;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: rgba(212, 175, 55, 0.15);
            color: var(--gold);
        }

        .dropdown-item i {
            color: var(--gold);
        }

        .modal-content {
            background: rgba(30, 41, 59, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 20px;
            color: #e2e8f0;
        }

        .modal-header {
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }

        .modal-title {
            color: var(--gold);
            font-weight: 900;
        }

        .modal-footer {
            border-top: 1px solid rgba(212, 175, 55, 0.3);
        }

        .alert {
            border-radius: 12px;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: #10b981;
            color: #34d399;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
            color: #f87171;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-left-color: #f59e0b;
            color: #fbbf24;
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--gold) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--gold) !important;
            color: #111 !important;
            border-color: var(--gold) !important;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem;
        }

        .dataTables_wrapper .dataTables_info {
            color: #94a3b8;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header -->
    <div class="agents-header">
        <h3>
            <i class="bx bx-user-circle"></i>
            Agents Management
        </h3>
        <div class="subtitle">Manage and monitor all insurance agents</div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            <strong>Error!</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card agents-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2"></i>
                        All Agents
                    </h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-gold btn-sm" onclick="refreshTable()">
                            <i class="bx bx-refresh me-1"></i>
                            Refresh
                        </button>
                        <a class="btn btn-gold btn-sm" href="<?php echo e(route('agents.create')); ?>">
                            <i class="bx bx-user-plus me-1"></i>
                            Add Agent
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Agent Info</th>
                                    <th width="10%">Phone</th>
                                    <th width="8%">SSN Last 4</th>
                                    <th width="8%">DOB</th>
                                    <th width="10%">Primary State</th>
                                    <th width="15%">Carriers</th>
                                    <th width="15%">Active States</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-primary">#<?php echo e($agent->id); ?></span>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="agent-avatar me-3">
                                                    <?php echo e(strtoupper(substr($agent->name, 0, 2))); ?>

                                                </div>
                                                <div>
                                                    <div class="agent-info-name"><?php echo e($agent->name); ?></div>
                                                    <div class="agent-info-email"><?php echo e($agent->email); ?></div>
                                                    <div class="agent-status mt-1 d-flex align-items-center gap-2">
                                                        <span class="status-indicator"></span>
                                                        <small style="color: #34d399; font-weight: 600;">Active</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <?php if($agent->userDetail && $agent->userDetail->phone): ?>
                                                <span class="text-white-50">
                                                    <i class="bx bx-phone me-1"></i>
                                                    <?php echo e($agent->userDetail->phone); ?>

                                                </span>
                                            <?php else: ?>
                                                <span style="color: #64748b;">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if($agent->userDetail && $agent->userDetail->ssn_last4): ?>
                                                <span class="badge-gold">
                                                    ***-**-<?php echo e($agent->userDetail->ssn_last4); ?>

                                                </span>
                                            <?php else: ?>
                                                <span style="color: #64748b;">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if($agent->userDetail && $agent->userDetail->dob): ?>
                                                <span class="text-white-50">
                                                    <?php echo e($agent->userDetail->dob->format('m/d/Y')); ?>

                                                </span>
                                            <?php else: ?>
                                                <span style="color: #64748b;">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if($agent->userDetail && $agent->userDetail->state): ?>
                                                <span class="badge-gold">
                                                    <i class="bx bx-map me-1"></i>
                                                    <?php echo e($agent->userDetail->state); ?>

                                                </span>
                                            <?php else: ?>
                                                <span style="color: #64748b;">Not specified</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="carriers-list">
                                                <?php if($agent->userDetail && $agent->userDetail->carriers && count($agent->userDetail->carriers) > 0): ?>
                                                    <?php $__currentLoopData = $agent->userDetail->carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge-carrier">
                                                            <i class="bx bx-car me-1"></i>
                                                            <?php echo e($carrier); ?>

                                                        </span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <span style="color: #64748b;">
                                                        <i class="bx bx-car me-1"></i>
                                                        No carriers
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="states-list">
                                                <?php if($agent->userDetail && $agent->userDetail->active_states && count($agent->userDetail->active_states) > 0): ?>
                                                    <?php $__currentLoopData = $agent->userDetail->active_states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge-state">
                                                            <?php echo e($state); ?>

                                                        </span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="mt-2">
                                                        <small style="color: #94a3b8; font-weight: 600;">
                                                            <?php echo e(count($agent->userDetail->active_states)); ?> state(s)
                                                        </small>
                                                    </div>
                                                <?php else: ?>
                                                    <span style="color: #64748b;">
                                                        <i class="bx bx-map-alt me-1"></i>
                                                        No active states
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-gold btn-sm dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="<?php echo e(route('agents.show', $agent->id)); ?>">
                                                            <i class="bx bx-show me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Admin')): ?>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="<?php echo e(route('agents.edit', $agent->id)); ?>">
                                                                <i class="bx bx-edit me-2"></i>Edit Agent
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider" style="border-color: rgba(212, 175, 55, 0.2);">
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="viewCarriers(<?php echo e($agent->id); ?>)">
                                                                <i class="bx bx-car me-2"></i>View Carriers
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="viewActiveStates(<?php echo e($agent->id); ?>)">
                                                                <i class="bx bx-map-alt me-2"></i>View Active States
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider" style="border-color: rgba(212, 175, 55, 0.2);">
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item" style="color: #f87171;"
                                                                onclick="confirmDelete(<?php echo e($agent->id); ?>)">
                                                                <i class="bx bx-trash me-2" style="color: #ef4444;"></i>Delete Agent
                                                            </button>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    
    <div class="modal fade" id="carriersModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-car me-2"></i>
                        Agent Carriers
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body" id="carriersModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="activeStatesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-map-alt me-2"></i>
                        Active States
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body" id="activeStatesModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: #f87171;">
                        <i class="bx bx-error-circle me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body">
                    <p style="color: #cbd5e1;">Are you sure you want to delete this agent? This action cannot be undone.</p>
                    <div class="alert alert-warning">
                        <i class="bx bx-error me-2"></i>
                        <strong>Warning:</strong> All associated data will be permanently removed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn" style="background: #ef4444; color: white; font-weight: 700; border-radius: 8px; padding: 0.5rem 1rem;">
                            <i class="bx bx-trash me-1"></i>
                            Delete Agent
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('/assets/libs/datatables/datatables.min.js')); ?>"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with custom configuration
            $('#datatable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [
                    [0, 'desc']
                ], // Order by ID descending
                columnDefs: [{
                        orderable: false,
                        targets: [6]
                    }, // Disable sorting for Actions column
                    {
                        className: "text-center",
                        targets: [0, 6]
                    }
                ],
                language: {
                    search: "Search agents:",
                    lengthMenu: "Show _MENU_ agents per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ agents",
                    emptyTable: "No agents found"
                }
            });
        });

        // Function to refresh the table
        function refreshTable() {
            location.reload();
        }

        // Function to view carriers in modal
        function viewCarriers(agentId) {
            // Find the agent data from the table
            const agentData = <?php echo json_encode($agents->keyBy('id'), 15, 512) ?>;
            const agent = agentData[agentId];

            let content = '<div class="text-center">';
            content += `<h6 class="mb-3">${agent.name}'s Carriers</h6>`;

            if (agent.user_detail && agent.user_detail.carriers && agent.user_detail.carriers.length > 0) {
                content += '<div class="d-flex flex-wrap gap-2 justify-content-center">';
                agent.user_detail.carriers.forEach(carrier => {
                    content +=
                        `<span class="badge bg-success fs-6"><i class="mdi mdi-truck me-1"></i>${carrier}</span>`;
                });
                content += '</div>';
            } else {
                content += '<p class="text-muted"><i class="mdi mdi-truck-outline me-2"></i>No carriers assigned</p>';
            }
            content += '</div>';

            document.getElementById('carriersModalBody').innerHTML = content;
            new bootstrap.Modal(document.getElementById('carriersModal')).show();
        }

        // Function to view active states in modal
        function viewActiveStates(agentId) {
            const agentData = <?php echo json_encode($agents->keyBy('id'), 15, 512) ?>;
            const agent = agentData[agentId];

            let content = '<div class="text-center">';
            content += `<h6 class="mb-3">${agent.name}'s Active States</h6>`;

            if (agent.user_detail && agent.user_detail.active_states && agent.user_detail.active_states.length > 0) {
                content += '<div class="row">';
                agent.user_detail.active_states.forEach(state => {
                    content +=
                        `<div class="col-6 mb-2"><span class="badge bg-warning text-dark w-100">${state}</span></div>`;
                });
                content += '</div>';
                content += `<p class="mt-3 text-muted">Total: ${agent.user_detail.active_states.length} state(s)</p>`;
            } else {
                content +=
                    '<p class="text-muted"><i class="mdi mdi-map-marker-multiple-outline me-2"></i>No active states assigned</p>';
            }
            content += '</div>';

            document.getElementById('activeStatesModalBody').innerHTML = content;
            new bootstrap.Modal(document.getElementById('activeStatesModal')).show();
        }

        // Function to confirm delete
        function confirmDelete(agentId) {
            document.getElementById('deleteForm').action = `/agents/${agentId}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/admin/agents/index.blade.php ENDPATH**/ ?>