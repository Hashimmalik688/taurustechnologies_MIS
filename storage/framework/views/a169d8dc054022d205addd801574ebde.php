<?php use \App\Support\Roles; ?>
<div class="card-body">
    <!-- Top Scrollbar -->
    <div class="top-scrollbar-wrapper" id="topScrollbarLeads">
        <div class="top-scrollbar-content" id="topScrollbarContentLeads"></div>
    </div>
    
    <!-- Main Table Wrapper -->
    <div class="leads-table-wrapper" id="leadsTableWrapper">
    <table class="leads-table table table-striped table-bordered table-hover table-sm align-middle text-nowrap" id="leadsTable">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Actions</th>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Client Name</th>
                    <th>Primary Phone</th>
                    <th>Secondary Phone</th>
                    <th>State/Zip</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Smoker</th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::SUPER_ADMIN)): ?>
                        <th>DL#</th>
                        <th>Height</th>
                        <th>Weight</th>
                        <th>Birth Place</th>
                        <th>Medical Issue</th>
                        <th>Medications</th>
                        <th>Doctor</th>
                        <th>SSN</th>
                        <th>Address</th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <th>Carrier</th>
                    <th>Coverage</th>
                    <th>Premium</th>
                    <th>Beneficiaries (Name / Relation / DOB)</th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::SUPER_ADMIN)): ?>
                        <th>Emergency Contact</th>
                        <th>Acc Verified By</th>
                        <th>Policy Type</th>
                        <th>Initial Draft</th>
                        <th>Future Draft</th>
                        <th>Bank</th>
                        <th>Acc Type</th>
                        <th>Routing#</th>
                        <th>Acc#</th>
                        <th>Card#</th>
                        <th>CVV</th>
                        <th>Expiry</th>
                        <th>Source</th>
                        <th>Closer</th>
                        <th>Assigned Partner</th>
                        <th>Comments</th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center"><strong><?php echo e($leads->firstItem() + $index); ?></strong></td>
 <td class="text-center u-min-w-140" >
                            <div class="btn-group" role="group">
                                <a href="<?php echo e(route('leads.show', $lead->id)); ?>" class="btn btn-outline-info btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if(auth()->check() && auth()->user()->canEditModule('leads')): ?>
                                    <a href="<?php echo e(route('leads.edit', $lead->id)); ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->check() && auth()->user()->canDeleteInModule('leads')): ?>
                                    <form action="<?php echo e(route('leads.delete', $lead->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete <?php echo e(addslashes($lead->cn_name)); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo e($lead->id); ?></td>
                        <td><?php echo e($lead->date ?? 'N/A'); ?></td>
                        <td><strong><?php echo e($lead->cn_name); ?></strong></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->phone_number): ?>
                                <span title="<?php echo e($lead->phone_number); ?>"><?php echo e($lead->phone_number); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->secondary_phone_number): ?>
                                <span title="<?php echo e($lead->secondary_phone_number); ?>"><?php echo e($lead->secondary_phone_number); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->state || $lead->zip_code): ?>
                                <small><?php echo e($lead->state ?? '—'); ?> <?php echo e($lead->zip_code ?? '—'); ?></small>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td><?php echo e($lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('m/d/Y') : 'N/A'); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->gender): ?>
                                <span class="badge bg-<?php echo e($lead->gender == 'Male' ? 'primary' : ($lead->gender == 'Female' ? 'info' : 'secondary')); ?>">
                                    <?php echo e($lead->gender); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->smoker): ?>
                                <span class="badge bg-warning">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-success">No</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::SUPER_ADMIN)): ?>
                            <td><?php echo e($lead->driving_license ?? '—'); ?></td>
                            <td><?php echo e($lead->height ?? '—'); ?></td>
                            <td><?php echo e($lead->weight ? $lead->weight . ' lbs' : '—'); ?></td>
                            <td><?php echo e($lead->birth_place ?? '—'); ?></td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 120px;" title="<?php echo e($lead->medical_issue); ?>"><?php echo e($lead->medical_issue ?? '—'); ?></span></td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 120px;" title="<?php echo e($lead->medications); ?>"><?php echo e($lead->medications ?? '—'); ?></span></td>
                            <td><?php echo e($lead->doctor_name ?? '—'); ?></td>
                            <td><?php echo e($lead->ssn ?? '—'); ?></td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 150px;" title="<?php echo e($lead->address); ?>"><?php echo e($lead->address ?? '—'); ?></span></td>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <td><?php echo e($lead->carrier_name ?? '—'); ?></td>
                        <td>$<?php echo e(number_format($lead->coverage_amount ?? 0, 0)); ?></td>
                        <td>$<?php echo e(number_format($lead->monthly_premium ?? 0, 2)); ?></td>
                        <td>
                            <?php
                                $beneficiaries = $lead->beneficiaries ?? [];
                                // Fallback to old fields if no beneficiaries array
                                if (empty($beneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
                                    $beneficiaries = [[
                                        'name' => $lead->beneficiary ?? '',
                                        'dob' => $lead->beneficiary_dob ?? '',
                                        'relation' => ''
                                    ]];
                                }
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($beneficiaries)): ?>
 <div class="u-fs-085 u-max-w-300">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="mb-2 p-1 border-start border-2 border-gold">
                                            <div><strong><?php echo e($index + 1); ?>. <?php echo e($beneficiary['name'] ?? '—'); ?></strong></div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($beneficiary['relation'])): ?>
                                                <div><small class="text-muted">Rel: <?php echo e($beneficiary['relation']); ?></small></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($beneficiary['dob'])): ?>
                                                <div><small class="text-muted">DOB: <?php echo e(\Carbon\Carbon::parse($beneficiary['dob'])->format('m/d/Y')); ?></small></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php else: ?>
                                —
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::SUPER_ADMIN)): ?>
                            <td><?php echo e($lead->emergency_contact ?? '—'); ?></td>
                            <td><?php echo e($lead->account_verified_by ?? '—'); ?></td>
                            <td><?php echo e($lead->policy_type ?? '—'); ?></td>
                            <td><?php echo e($lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('m/d/Y') : '—'); ?></td>
                            <td><?php echo e($lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('m/d/Y') : '—'); ?></td>
                            <td><?php echo e($lead->bank_name ?? '—'); ?></td>
                            <td><?php echo e($lead->account_type ?? '—'); ?></td>
                            <td><?php echo e($lead->routing_number ?? '—'); ?></td>
                            <td><?php echo e($lead->acc_number ?? '—'); ?></td>
                            <td><?php echo e($lead->card_number ?? '—'); ?></td>
                            <td><?php echo e($lead->cvv ?? '—'); ?></td>
                            <td><?php echo e($lead->expiry_date ?? '—'); ?></td>
                            <td><?php echo e($lead->source ?? '—'); ?></td>
                            <td><?php echo e($lead->closer_name ?? '—'); ?></td>
                            <td><?php echo e($lead->assigned_partner ?? '—'); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->preset_line): ?>
 <span class="badge text-white bg-gold"><?php echo e($lead->preset_line); ?></span>
                                <?php else: ?>
                                    —
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="comment-editable u-max-w-200" data-lead-id="<?php echo e($lead->id); ?>">
                                    <div class="comment-display" title="Click to edit">
 <span class="comment-text text-truncate d-inline-block u-cursor-pointer" style="max-width: 180px">
                                            <?php echo e($lead->comments ?? 'Click to add comment'); ?>

                                        </span>
 <i class="fas fa-edit text-muted ms-1 u-fs-11 u-cursor-pointer" ></i>
                                    </div>
 <div class="comment-edit d-none" >
 <textarea class="form-control form-control-sm comment-input u-fs-12" rows="2" ><?php echo e($lead->comments); ?></textarea>
                                        <div class="mt-1">
 <button class="btn btn-success btn-sm save-comment u-fs-11" style="padding: 2px 8px">
                                                <i class="fas fa-check"></i>
                                            </button>
 <button class="btn btn-secondary btn-sm cancel-comment u-fs-11" style="padding: 2px 8px">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status == 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php elseif($lead->status == 'accepted'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif($lead->status == 'rejected'): ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php elseif($lead->status == 'forwarded'): ?>
                                <span class="badge bg-info">Forwarded</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Unknown</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="40" class="text-center text-muted py-4">
                            <i class="bx bx-info-circle fs-3"></i>
                            <p class="mb-0">No leads found</p>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/leads/index_table.blade.php ENDPATH**/ ?>