<?php $__env->startSection('title'); ?>
    View Lead
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        :root {
            --gold: #d4af37;
            --gold-light: #f5e6c8;
            --gold-dark: #b8941f;
        }

        .lead-header {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.3);
            color: #1a1a1a;
        }

        .lead-header h2 {
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .lead-header .lead-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .lead-header .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #2d2d2d;
        }

        .lead-header .lead-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-call {
            background: #28a745;
            border: none;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-call:hover {
            background: #218838;
            transform: translateY(-2px);
            color: white;
        }

        .btn-back {
            background: rgba(0, 0, 0, 0.2);
            border: none;
            color: #1a1a1a;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .card-header-gold {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
            border-bottom: 2px solid var(--gold);
            padding: 1rem 1.25rem;
            border-radius: 10px 10px 0 0;
        }

        .card-header-gold h5 {
            color: var(--gold-dark);
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .info-row {
            margin-bottom: 1rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: #2d3436;
            word-wrap: break-word;
        }

        .info-value.empty {
            color: #95a5a6;
            font-style: italic;
        }

        .badge-gold {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #1a1a1a;
            font-weight: 600;
            padding: 0.3rem 0.65rem;
            border-radius: 5px;
        }

        .badge-status {
            padding: 0.3rem 0.65rem;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-accepted { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-closed { background: #d1ecf1; color: #0c5460; }
        .status-chargeback { background: #f5c6cb; color: #721c24; }

        .section-divider {
            border-top: 2px solid #e9ecef;
            margin: 1.5rem 0;
        }

        .pipeline-checklist {
            /* Normal layout flow - no sticky/fixed positioning */
        }

        .checklist-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-left: 3px solid #e9ecef;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            border-radius: 0 8px 8px 0;
        }

        .checklist-item.completed {
            border-left-color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
        }

        .checklist-item.current {
            border-left-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
        }

        .checklist-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .checklist-item.completed .checklist-checkbox {
            background: var(--gold);
            border-color: var(--gold);
        }

        .checklist-item.current .checklist-checkbox {
            background: #28a745;
            border-color: #28a745;
            animation: pulse 2s infinite;
        }

        .checklist-checkbox i {
            color: white;
            font-size: 14px;
        }

        .checklist-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #6c757d;
        }

        .checklist-item.completed .checklist-label {
            color: var(--gold-dark);
        }

        .checklist-item.current .checklist-label {
            color: #28a745;
            font-weight: 600;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Leads <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Lead Details <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <!-- Lead Header -->
    <div class="lead-header">
        <h2><i class="mdi mdi-account-circle me-2"></i><?php echo e($insurance->cn_name ?? 'Unnamed Lead'); ?></h2>
        <div class="lead-meta">
            <div class="meta-item">
                <i class="mdi mdi-phone"></i>
                <strong><?php echo e($insurance->phone_number ?? 'No phone'); ?></strong>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->secondary_phone_number): ?>
            <div class="meta-item">
                <i class="mdi mdi-phone-plus"></i>
                <?php echo e($insurance->secondary_phone_number); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="meta-item">
                <i class="mdi mdi-map-marker"></i>
                <?php echo e($insurance->state ?? 'N/A'); ?> <?php echo e($insurance->zip_code ?? ''); ?>

            </div>
            <div class="meta-item">
                <i class="mdi mdi-clock-outline"></i>
                Created: <?php echo e($insurance->created_at ? $insurance->created_at->format('M d, Y') : 'N/A'); ?>

            </div>
        </div>
        <div class="lead-actions">
            <button onclick="makeZoomCall()" class="btn btn-call">
                <i class="mdi mdi-phone me-1"></i>Call Now
            </button>
            <a href="<?php echo e(route('sales.prettyPrint', $insurance->id)); ?>" class="btn btn-success" target="_blank">
                <i class="mdi mdi-printer me-1"></i>Pretty Print
            </a>
            <a href="<?php echo e(route('leads.index')); ?>" class="btn btn-back">
                <i class="mdi mdi-arrow-left me-1"></i>Back to Leads
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-6">
            <!-- Personal Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-account"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Full Name</div>
                            <div class="info-value <?php echo e($insurance->cn_name ? '' : 'empty'); ?>">
                                <?php echo e($insurance->cn_name ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value <?php echo e($insurance->date_of_birth ? '' : 'empty'); ?>">
                                <?php echo e($insurance->date_of_birth ? \Carbon\Carbon::parse($insurance->date_of_birth)->format('M d, Y') : 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Age</div>
                            <div class="info-value <?php echo e($insurance->age ? '' : 'empty'); ?>">
                                <?php echo e($insurance->age ?? 'Not calculated'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Gender</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->gender): ?>
                                    <span class="badge bg-info"><?php echo e($insurance->gender); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Birth Place</div>
                            <div class="info-value <?php echo e($insurance->birth_place ? '' : 'empty'); ?>">
                                <?php echo e($insurance->birth_place ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">SSN</div>
                            <div class="info-value <?php echo e($insurance->ssn ? '' : 'empty'); ?>">
                                <?php echo e($insurance->ssn ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-phone-in-talk"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Primary Phone</div>
                            <div class="info-value <?php echo e($insurance->phone_number ? '' : 'empty'); ?>">
                                <?php echo e($insurance->phone_number ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Secondary Phone</div>
                            <div class="info-value <?php echo e($insurance->secondary_phone_number ? '' : 'empty'); ?>">
                                <?php echo e($insurance->secondary_phone_number ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Address</div>
                            <div class="info-value <?php echo e($insurance->address ? '' : 'empty'); ?>">
                                <?php echo e($insurance->address ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">State</div>
                            <div class="info-value <?php echo e($insurance->state ? '' : 'empty'); ?>">
                                <?php echo e($insurance->state ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Zip Code</div>
                            <div class="info-value <?php echo e($insurance->zip_code ? '' : 'empty'); ?>">
                                <?php echo e($insurance->zip_code ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Emergency Contact</div>
                            <div class="info-value <?php echo e($insurance->emergency_contact ? '' : 'empty'); ?>">
                                <?php echo e($insurance->emergency_contact ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-heart-pulse"></i>Health Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 info-row">
                            <div class="info-label">Nicotine User</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->smoker !== null): ?>
                                    <span class="badge <?php echo e($insurance->smoker ? 'bg-warning' : 'bg-success'); ?>">
                                        <?php echo e($insurance->smoker ? 'Yes' : 'Non'); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3 info-row">
                            <div class="info-label">Height</div>
                            <div class="info-value <?php echo e($insurance->height ? '' : 'empty'); ?>">
                                <?php echo e($insurance->height ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-3 info-row">
                            <div class="info-label">Weight (lbs)</div>
                            <div class="info-value <?php echo e($insurance->weight ? '' : 'empty'); ?>">
                                <?php echo e($insurance->weight ? $insurance->weight . ' lbs' : 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-3 info-row">
                            <div class="info-label">Driving License</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->driving_license !== null): ?>
                                    <span class="badge bg-info"><?php echo e($insurance->driving_license ? 'Yes' : 'No'); ?></span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->driving_license_number): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Driving License Number</div>
                            <div class="info-value"><?php echo e($insurance->driving_license_number); ?></div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Medical Issues</div>
                            <div class="info-value <?php echo e($insurance->medical_issue ? '' : 'empty'); ?>">
                                <?php echo e($insurance->medical_issue ?? 'None reported'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Medications</div>
                            <div class="info-value <?php echo e($insurance->medications ? '' : 'empty'); ?>">
                                <?php echo e($insurance->medications ?? 'None reported'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="section-divider"></div>
                    <h6 class="text-muted mb-3"><i class="mdi mdi-doctor"></i> Primary Care Physician</h6>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Doctor Name</div>
                            <div class="info-value <?php echo e($insurance->doctor_name ? '' : 'empty'); ?>">
                                <?php echo e($insurance->doctor_name ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Doctor Phone</div>
                            <div class="info-value <?php echo e($insurance->doctor_number ? '' : 'empty'); ?>">
                                <?php echo e($insurance->doctor_number ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Doctor Address</div>
                            <div class="info-value <?php echo e($insurance->doctor_address ? '' : 'empty'); ?>">
                                <?php echo e($insurance->doctor_address ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Policy Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-shield-check"></i>Policy Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Policy Type</div>
                            <div class="info-value <?php echo e($insurance->policy_type ? '' : 'empty'); ?>">
                                <?php echo e($insurance->policy_type ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Policy Number</div>
                            <div class="info-value <?php echo e($insurance->policy_number ? '' : 'empty'); ?>">
                                <?php echo e($insurance->policy_number ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Carrier Name</div>
                            <div class="info-value <?php echo e($insurance->carrier_name ? '' : 'empty'); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->insuranceCarrier): ?>
                                    <?php echo e($insurance->insuranceCarrier->name); ?>

                                <?php else: ?>
                                    <?php echo e($insurance->carrier_name ?? 'Not provided'); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Coverage Amount</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->coverage_amount): ?>
                                    <span class="badge bg-primary">
                                        $<?php echo e(number_format($insurance->coverage_amount, 0)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                            <div class="info-label">Monthly Premium</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->monthly_premium): ?>
                                    <span class="badge bg-success">
                                        $<?php echo e(number_format($insurance->monthly_premium, 2)); ?>/mo
                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Initial Draft Date</div>
                            <div class="info-value <?php echo e($insurance->initial_draft_date ? '' : 'empty'); ?>">
                                <?php echo e($insurance->initial_draft_date ? \Carbon\Carbon::parse($insurance->initial_draft_date)->format('M d, Y') : 'Not set'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Future Draft Date</div>
                            <div class="info-value <?php echo e($insurance->future_draft_date ? '' : 'empty'); ?>">
                                <?php echo e($insurance->future_draft_date ? \Carbon\Carbon::parse($insurance->future_draft_date)->format('M d, Y') : 'Not set'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Beneficiary Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-account-heart"></i>Beneficiary Information</h5>
                </div>
                <div class="card-body">
                    <?php
                        $beneficiaries = $insurance->beneficiaries ?? [];
                        // Ensure beneficiaries is always an array (handle string/non-array values)
                        if (is_string($beneficiaries)) {
                            // Try JSON decode first
                            $decoded = json_decode($beneficiaries, true);
                            $beneficiaries = is_array($decoded) ? $decoded : [];
                        }
                        if (!is_array($beneficiaries)) {
                            $beneficiaries = [];
                        }
                        if (empty($beneficiaries) && ($insurance->beneficiary || $insurance->beneficiary_dob)) {
                            $beneficiaries = [[
                                'name' => $insurance->beneficiary ?? '',
                                'dob' => $insurance->beneficiary_dob ?? '',
                                'relation' => ''
                            ]];
                        }
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($beneficiaries)): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row <?php echo e($loop->last ? '' : 'mb-3 pb-3 border-bottom'); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($beneficiaries) > 1): ?>
                                <div class="col-12 mb-2">
                                    <strong class="text-primary">Beneficiary <?php echo e($index + 1); ?></strong>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="col-md-4 info-row">
                                    <div class="info-label">Name</div>
                                    <div class="info-value <?php echo e(!empty($beneficiary['name']) ? '' : 'empty'); ?>">
                                        <?php echo e($beneficiary['name'] ?? 'Not provided'); ?>

                                    </div>
                                </div>
                                <div class="col-md-4 info-row">
                                    <div class="info-label">Relation</div>
                                    <div class="info-value <?php echo e(!empty($beneficiary['relation']) ? '' : 'empty'); ?>">
                                        <?php echo e($beneficiary['relation'] ?? 'Not provided'); ?>

                                    </div>
                                </div>
                                <div class="col-md-4 info-row">
                                    <div class="info-label">Date of Birth</div>
                                    <div class="info-value <?php echo e(!empty($beneficiary['dob']) ? '' : 'empty'); ?>">
                                        <?php echo e(!empty($beneficiary['dob']) ? \Carbon\Carbon::parse($beneficiary['dob'])->format('M d, Y') : 'Not provided'); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="info-value empty">No beneficiaries added</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-6">
            <!-- Live & Health Pipeline -->
            <?php
                // Define the 9-stage pipeline for Live & Health
                $pipelineStages = [
                    ['key' => 'sales', 'label' => 'Sale Made', 'icon' => 'mdi-handshake'],
                    ['key' => 'submission', 'label' => 'Submitted to Carrier', 'icon' => 'mdi-file-upload'],
                    ['key' => 'issuance', 'label' => 'Policy Issued', 'icon' => 'mdi-file-document-check'],
                    ['key' => 'followup', 'label' => 'Client Followup', 'icon' => 'mdi-phone-in-talk'],
                    ['key' => 'banking_validation', 'label' => 'Banking Validation', 'icon' => 'mdi-bank-check'],
                    ['key' => 'draft_confirmation', 'label' => 'Draft Confirmation', 'icon' => 'mdi-check-circle'],
                    ['key' => 'commission', 'label' => 'Commission Statement', 'icon' => 'mdi-currency-usd'],
                    ['key' => 'paid', 'label' => 'Paid', 'icon' => 'mdi-cash-check'],
                    ['key' => 'advance_recovery', 'label' => 'Advance Recovery', 'icon' => 'mdi-refresh']
                ];

                $completedStages = [];
                $currentStage = null;

                // Sale Made - if status is Accepted
                if ($insurance->status === 'Accepted') {
                    $completedStages[] = 'sales';
                }

                // Submitted to Carrier - if carrier is assigned
                if ($insurance->carrier_name || $insurance->insuranceCarrier) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                }

                // Policy Issued - if issued_policy_number or issuance_status is 'issued'
                if ($insurance->issued_policy_number || $insurance->issuance_status === 'issued') {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                }

                // Client Followup - if followup has occurred
                if ($insurance->followup_status === 'Completed' || $insurance->followup_notes) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    $completedStages[] = 'followup';
                }

                // Banking Validation - if bank details are verified
                if ($insurance->bank_verification_status === 'verified' || $insurance->account_verified_by) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    $completedStages[] = 'followup';
                    $completedStages[] = 'banking_validation';
                }

                // Draft Confirmation - if initial_draft_date is set
                if ($insurance->initial_draft_date) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    $completedStages[] = 'followup';
                    $completedStages[] = 'banking_validation';
                    $completedStages[] = 'draft_confirmation';
                }

                // Commission Statement - if agent_commission is calculated
                if ($insurance->agent_commission || $insurance->commission_calculated_at) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    $completedStages[] = 'followup';
                    $completedStages[] = 'banking_validation';
                    $completedStages[] = 'draft_confirmation';
                    $completedStages[] = 'commission';
                }

                // Paid - custom logic (you can add a 'payment_status' field later)
                // For now, assume if commission exists and 30+ days passed
                if ($insurance->agent_commission && $insurance->commission_calculated_at && 
                    \Carbon\Carbon::parse($insurance->commission_calculated_at)->diffInDays(now()) > 30) {
                    $completedStages[] = 'paid';
                }

                // Advance Recovery - if there's any recovery tracking (you can add field later)
                // For now, leave it unchecked unless specifically marked

                // Determine current stage if not set
                if (!$currentStage) {
                    if (!in_array('sales', $completedStages)) {
                        $currentStage = 'sales';
                    } elseif (!in_array('submission', $completedStages)) {
                        $currentStage = 'submission';
                    } elseif (!in_array('issuance', $completedStages)) {
                        $currentStage = 'issuance';
                    } elseif (!in_array('followup', $completedStages)) {
                        $currentStage = 'followup';
                    } elseif (!in_array('banking_validation', $completedStages)) {
                        $currentStage = 'banking_validation';
                    } elseif (!in_array('draft_confirmation', $completedStages)) {
                        $currentStage = 'draft_confirmation';
                    } elseif (!in_array('commission', $completedStages)) {
                        $currentStage = 'commission';
                    } elseif (!in_array('paid', $completedStages)) {
                        $currentStage = 'paid';
                    } else {
                        $currentStage = 'advance_recovery';
                    }
                }

                $completedStages = array_unique($completedStages);
            ?>

            <div class="info-card pipeline-checklist">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-timeline-check"></i>Live & Health Pipeline</h5>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pipelineStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isCompleted = in_array($stage['key'], $completedStages);
                            $isCurrent = $stage['key'] === $currentStage;
                        ?>
                        <div class="checklist-item <?php echo e($isCompleted ? 'completed' : ''); ?> <?php echo e($isCurrent ? 'current' : ''); ?>">
                            <div class="checklist-checkbox">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCompleted): ?>
                                    <i class="mdi mdi-check"></i>
                                <?php elseif($isCurrent): ?>
                                    <i class="mdi mdi-dots-horizontal"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="checklist-label">
                                <i class="mdi <?php echo e($stage['icon']); ?> me-1"></i>
                                <?php echo e($stage['label']); ?>

                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Status & Assignment -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-clipboard-check"></i>Status & Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Lead Status</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->status): ?>
                                    <span class="status-badge status-<?php echo e(strtolower($insurance->status)); ?>">
                                        <?php echo e(ucfirst($insurance->status)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Team</div>
                            <div class="info-value <?php echo e($insurance->team ? '' : 'empty'); ?>">
                                <?php echo e($insurance->team ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Closer Name</div>
                            <div class="info-value <?php echo e($insurance->closer_name ? '' : 'empty'); ?>">
                                <?php echo e($insurance->closer_name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Managed By</div>
                            <div class="info-value <?php echo e($insurance->managedBy ? '' : 'empty'); ?>">
                                <?php echo e($insurance->managedBy->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Assigned Partner</div>
                            <div class="info-value <?php echo e($insurance->partner ? '' : 'empty'); ?>">
                                <?php echo e($insurance->partner->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Assigned Validator</div>
                            <div class="info-value <?php echo e($insurance->assignedValidator ? '' : 'empty'); ?>">
                                <?php echo e($insurance->assignedValidator->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->sale_date || $insurance->sale_at): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Sale Date</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->sale_date): ?>
                                    <?php echo e(\Carbon\Carbon::parse($insurance->sale_date)->format('M d, Y')); ?>

                                <?php elseif($insurance->sale_at): ?>
                                    <?php echo e(\Carbon\Carbon::parse($insurance->sale_at)->format('M d, Y')); ?>

                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->decline_reason || $insurance->pending_reason): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Status Reason</div>
                            <div class="info-value">
                                <?php echo e($insurance->decline_reason ?? $insurance->pending_reason ?? 'N/A'); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="row">
                        <div class="col-md-4 info-row">
                            <div class="info-label">Source</div>
                            <div class="info-value <?php echo e($insurance->source ? '' : 'empty'); ?>">
                                <?php echo e($insurance->source ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-4 info-row">
                            <div class="info-label">Preset Line</div>
                            <div class="info-value <?php echo e($insurance->preset_line ? '' : 'empty'); ?>">
                                <?php echo e($insurance->preset_line ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-4 info-row">
                            <div class="info-label">Comments</div>
                            <div class="info-value <?php echo e($insurance->comments ? '' : 'empty'); ?>">
                                <?php echo e($insurance->comments ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Account Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-bank"></i>Bank Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Bank Name</div>
                            <div class="info-value <?php echo e($insurance->bank_name ? '' : 'empty'); ?>">
                                <?php echo e($insurance->bank_name ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Account Type</div>
                            <div class="info-value <?php echo e($insurance->account_type ? '' : 'empty'); ?>">
                                <?php echo e($insurance->account_type ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Account Title</div>
                            <div class="info-value <?php echo e($insurance->account_title ? '' : 'empty'); ?>">
                                <?php echo e($insurance->account_title ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Bank Balance</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_balance): ?>
                                    <span class="badge bg-info">
                                        $<?php echo e(number_format($insurance->bank_balance, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not provided</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Routing Number</div>
                            <div class="info-value <?php echo e($insurance->routing_number ? '' : 'empty'); ?>">
                                <?php echo e($insurance->routing_number ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Account Number</div>
                            <div class="info-value <?php echo e($insurance->acc_number ? '' : 'empty'); ?>">
                                <?php echo e($insurance->acc_number ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->account_verified_by): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Verified By</div>
                            <div class="info-value"><?php echo e($insurance->account_verified_by); ?></div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->ss_amount || $insurance->ss_date): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">SS Amount</div>
                            <div class="info-value <?php echo e($insurance->ss_amount ? '' : 'empty'); ?>">
                                <?php echo e($insurance->ss_amount ? '$' . number_format($insurance->ss_amount, 2) : 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">SS Date</div>
                            <div class="info-value <?php echo e($insurance->ss_date ? '' : 'empty'); ?>">
                                <?php echo e($insurance->ss_date ? \Carbon\Carbon::parse($insurance->ss_date)->format('M d, Y') : 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_verification_status || $insurance->bank_verification_notes): ?>
                    <div class="section-divider"></div>
                    <h6 class="text-muted mb-3">Bank Verification Details</h6>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Verification Status</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_verification_status): ?>
                                    <span class="badge bg-<?php echo e($insurance->bank_verification_status == 'verified' ? 'success' : 'warning'); ?>">
                                        <?php echo e(ucfirst($insurance->bank_verification_status)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Pending</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Verified By</div>
                            <div class="info-value <?php echo e($insurance->bankVerifier ? '' : 'empty'); ?>">
                                <?php echo e($insurance->bankVerifier->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->bank_verification_notes): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Verification Notes</div>
                            <div class="info-value"><?php echo e($insurance->bank_verification_notes); ?></div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Card Information (Super Admin/Manager Only) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|CEO|Manager|Co-ordinator')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->card_number || $insurance->cvv || $insurance->expiry_date): ?>
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-credit-card"></i>Card Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Card Number</div>
                            <div class="info-value <?php echo e($insurance->card_number ? '' : 'empty'); ?>">
                                <?php echo e($insurance->card_number ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">CVV</div>
                            <div class="info-value <?php echo e($insurance->cvv ? '' : 'empty'); ?>">
                                <?php echo e($insurance->cvv ?? 'Not provided'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Expiry Date</div>
                            <div class="info-value <?php echo e($insurance->expiry_date ? '' : 'empty'); ?>">
                                <?php echo e($insurance->expiry_date ?? 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Follow-Up Schedule -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->followup_required || $insurance->followup_scheduled_at): ?>
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-calendar-clock"></i>Follow-Up Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Follow-Up Required</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->followup_required): ?>
                                    <span class="badge bg-warning">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-success">No</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Assigned To</div>
                            <div class="info-value <?php echo e($insurance->followupPerson ? '' : 'empty'); ?>">
                                <?php echo e($insurance->followupPerson->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->followup_scheduled_at): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Scheduled Date & Time</div>
                            <div class="info-value">
                                <?php echo e(\Carbon\Carbon::parse($insurance->followup_scheduled_at)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Status</div>
                            <div class="info-value <?php echo e($insurance->followup_status ? '' : 'empty'); ?>">
                                <?php echo e($insurance->followup_status ?? 'Pending'); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- QA Review -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-check-decagram"></i>QA Review</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">QA Status</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->qa_status): ?>
                                    <span class="badge bg-<?php echo e($insurance->qa_status == 'Approved' ? 'success' : ($insurance->qa_status == 'Rejected' ? 'danger' : 'warning')); ?>">
                                        <?php echo e($insurance->qa_status); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not reviewed</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Reviewed By</div>
                            <div class="info-value <?php echo e($insurance->qaUser ? '' : 'empty'); ?>">
                                <?php echo e($insurance->qaUser->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">QA Notes / Reason</div>
                            <div class="info-value <?php echo e($insurance->qa_reason ? '' : 'empty'); ?>">
                                <?php echo e($insurance->qa_reason ?? 'No notes provided'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Retention Information (if applicable) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retention_status || $insurance->retention_notes): ?>
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-account-reactivate"></i>Retention Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Retention Status</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retention_status): ?>
                                    <span class="badge bg-info"><?php echo e($insurance->retention_status); ?></span>
                                <?php else: ?>
                                    <span class="empty">N/A</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Retention Officer</div>
                            <div class="info-value <?php echo e($insurance->retentionOfficer ? '' : 'empty'); ?>">
                                <?php echo e($insurance->retentionOfficer->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retained_at): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Retained Date</div>
                            <div class="info-value">
                                <?php echo e(\Carbon\Carbon::parse($insurance->retained_at)->format('M d, Y')); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Is Rewrite</div>
                            <div class="info-value">
                                <span class="badge bg-<?php echo e($insurance->is_rewrite ? 'warning' : 'secondary'); ?>">
                                    <?php echo e($insurance->is_rewrite ? 'Yes' : 'No'); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->retention_notes): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Retention Notes</div>
                            <div class="info-value"><?php echo e($insurance->retention_notes); ?></div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->chargeback_marked_date): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Chargeback Marked Date</div>
                            <div class="info-value text-danger">
                                <?php echo e(\Carbon\Carbon::parse($insurance->chargeback_marked_date)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Issuance Information (if applicable) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issuance_status || $insurance->assigned_agent_id): ?>
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-file-document-check"></i>Issuance Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Issuance Status</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issuance_status): ?>
                                    <span class="badge bg-<?php echo e($insurance->issuance_status == 'issued' ? 'success' : 'warning'); ?>">
                                        <?php echo e(ucfirst($insurance->issuance_status)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Assigned Agent</div>
                            <div class="info-value <?php echo e($insurance->assignedAgent ? '' : 'empty'); ?>">
                                <?php echo e($insurance->assignedAgent->name ?? 'Not assigned'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issued_policy_number): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Issued Policy Number</div>
                            <div class="info-value"><?php echo e($insurance->issued_policy_number); ?></div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Issuance Date</div>
                            <div class="info-value <?php echo e($insurance->issuance_date ? '' : 'empty'); ?>">
                                <?php echo e($insurance->issuance_date ? \Carbon\Carbon::parse($insurance->issuance_date)->format('M d, Y') : 'Not set'); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->issuance_reason): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Issuance Reason/Notes</div>
                            <div class="info-value"><?php echo e($insurance->issuance_reason); ?></div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Revenue & Commission (Super Admin/Manager Only) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|CEO|Manager|Co-ordinator')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->agent_commission || $insurance->agent_revenue || $insurance->settlement_percentage): ?>
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-currency-usd"></i>Revenue & Commission</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 info-row">
                            <div class="info-label">Agent Commission</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->agent_commission): ?>
                                    <span class="badge bg-success">
                                        $<?php echo e(number_format($insurance->agent_commission, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 info-row">
                            <div class="info-label">Agent Revenue</div>
                            <div class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->agent_revenue): ?>
                                    <span class="badge bg-primary">
                                        $<?php echo e(number_format($insurance->agent_revenue, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="empty">Not set</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 info-row">
                            <div class="info-label">Settlement %</div>
                            <div class="info-value <?php echo e($insurance->settlement_percentage ? '' : 'empty'); ?>">
                                <?php echo e($insurance->settlement_percentage ? $insurance->settlement_percentage . '%' : 'Not set'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->commission_calculation_notes): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Commission Notes</div>
                            <div class="info-value"><?php echo e($insurance->commission_calculation_notes); ?></div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->commission_calculated_at): ?>
                    <div class="row">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Calculated At</div>
                            <div class="info-value">
                                <?php echo e(\Carbon\Carbon::parse($insurance->commission_calculated_at)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Notes (Super Admin/Manager Only) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|CEO|Manager|Co-ordinator')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->staff_notes || $insurance->manager_notes): ?>
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-note-text"></i>Notes</h5>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->staff_notes): ?>
                    <div class="info-row">
                        <div class="info-label">Staff Notes</div>
                        <div class="info-value"><?php echo e($insurance->staff_notes); ?></div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->manager_notes): ?>
                    <div class="info-row">
                        <div class="info-label">Manager Notes</div>
                        <div class="info-value"><?php echo e($insurance->manager_notes); ?></div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Timeline -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-timeline-clock"></i>Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Created At</div>
                            <div class="info-value">
                                <?php echo e($insurance->created_at ? $insurance->created_at->format('M d, Y h:i A') : 'N/A'); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">
                                <?php echo e($insurance->updated_at ? $insurance->updated_at->format('M d, Y h:i A') : 'N/A'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->verified_at): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Verified At</div>
                            <div class="info-value">
                                <?php echo e(\Carbon\Carbon::parse($insurance->verified_at)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Verified By</div>
                            <div class="info-value <?php echo e($insurance->verifier ? '' : 'empty'); ?>">
                                <?php echo e($insurance->verifier->name ?? 'N/A'); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->validated_at): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Validated At</div>
                            <div class="info-value">
                                <?php echo e(\Carbon\Carbon::parse($insurance->validated_at)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Validated By</div>
                            <div class="info-value <?php echo e($insurance->validator ? '' : 'empty'); ?>">
                                <?php echo e($insurance->validator->name ?? 'N/A'); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->transferred_at): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Transferred At</div>
                            <div class="info-value">
                                <?php echo e(\Carbon\Carbon::parse($insurance->transferred_at)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->closed_at): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Closed At</div>
                            <div class="info-value">
                                <?php echo e(\Carbon\Carbon::parse($insurance->closed_at)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($insurance->declined_at): ?>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Declined At</div>
                            <div class="info-value text-danger">
                                <?php echo e(\Carbon\Carbon::parse($insurance->declined_at)->format('M d, Y h:i A')); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function makeZoomCall() {
            const phoneNumber = '<?php echo e($insurance->phone_number ?? ''); ?>';
            const sanitizedZoomNumber = '<?php echo e(Auth::user()->sanitized_zoom_number ?? ''); ?>';

            if (!phoneNumber) {
                alert('No phone number available for this lead.');
                return;
            }

            if (!sanitizedZoomNumber) {
                alert('You do not have a Zoom phone number configured.');
                return;
            }

            const cleanNumber = phoneNumber.replace(/[\s\-\(\)]/g, '');
            const zoomUrl = `zoomphonenumber://call?to=${cleanNumber}`;
            window.location.href = zoomUrl;
            console.log('Attempting to call:', cleanNumber);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/leads/show.blade.php ENDPATH**/ ?>