<!-- ========== Left Sidebar Start ========== -->
<div id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="logo-icon">
            <img src="<?php echo e(asset('images/icon.png')); ?>" alt="Taurus" onerror="this.style.display='none'">
        </div>
        <div class="logo-content">
            <span class="logo-text">TAURUS</span>
            <span class="logo-subtext">Management System</span>
        </div>
    </div>

    <!-- Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bx bx-chevron-left"></i>
    </button>

    <!-- Menu -->
    <nav class="sidebar-menu">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Retention Officer')): ?>
            <a href="<?php echo e(route('retention.dashboard')); ?>" class="menu-item <?php echo e(Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                <i class="bx bx-home-circle"></i>
                <span class="menu-text">Company Overview</span>
            </a>

            <a href="<?php echo e(route('retention.index')); ?>" class="menu-item <?php echo e(Request::is('retention') || Request::is('retention/*') ? 'active' : ''); ?>">
                <i class="bx bx-refresh"></i>
                <span class="menu-text">Retention Management</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Trainer')): ?>
            <a href="<?php echo e(route('employee.ems')); ?>" class="menu-item <?php echo e(Request::is('ems*') ? 'active' : ''); ?>">
                <i class="bx bx-id-card"></i>
                <span class="menu-text">E.M.S</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Verifier|Peregrine Closer|Peregrine Validator|Employee|Ravens Closer|Retention Officer|QA|HR')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Employee')): ?>
                <!-- Employee only sees COMMUNICATION section below -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', 'Ravens Closer')): ?>
                <!-- Ravens Closer sees same as Employee - attendance and chat only -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', 'QA')): ?>
                <!-- QA only sees QA Review and COMMUNICATION section below -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', 'HR')): ?>
                <!-- HR only sees Dock, Attendance, and Public Holidays -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', 'Co-ordinator')): ?>
                <a href="<?php echo e(route('root')); ?>" class="menu-item <?php echo e(Request::is('/') ? 'active' : ''); ?>">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Company Overview</span>
                </a>

                <a href="<?php echo e(route('followup.my-followups')); ?>" class="menu-item <?php echo e(Request::is('followup*') ? 'active' : ''); ?>">
                    <i class="bx bx-task"></i>
                    <span class="menu-text">My Followup & Bank Verification</span>
                </a>

                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'misDropdownCoord')">
                    <i class="bx bx-briefcase-alt"></i>
                    <span class="menu-text">Sales Operations</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="misDropdownCoord">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'QA|Super Admin|Manager|Co-ordinator|CEO')): ?>
                        <a href="<?php echo e(route('qa.review')); ?>" class="dropdown-item <?php echo e(Request::is('qa*') ? 'active' : ''); ?>">
                            <i class="bx bx-check-circle"></i>
                            <span class="menu-text">QA Review</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <a href="<?php echo e(route('sales.index')); ?>" class="dropdown-item <?php echo e(Request::is('sales*') ? 'active' : ''); ?>">
                        <i class="bx bx-dollar-circle"></i>
                        <span class="menu-text">Sales Records</span>
                    </a>

                    <a href="<?php echo e(route('issuance.index')); ?>" class="dropdown-item <?php echo e(Request::is('issuance*') ? 'active' : ''); ?>">
                        <i class="bx bx-send"></i>
                        <span class="menu-text">Policy Submission</span>
                    </a>

                    <a href="<?php echo e(route('bank-verification.index')); ?>" class="dropdown-item <?php echo e(Request::is('bank-verification*') ? 'active' : ''); ?>">
                        <i class="bx bx-check-shield"></i>
                        <span class="menu-text">Bank Verification</span>
                    </a>

                    <a href="<?php echo e(route('revenue-analytics.index')); ?>" class="dropdown-item <?php echo e(Request::is('revenue-analytics*') ? 'active' : ''); ?>">
                        <i class="bx bx-line-chart"></i>
                        <span class="menu-text">Revenue Analytics</span>
                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Manager|Co-ordinator|CEO')): ?>
                        <a href="<?php echo e(route('analytics.live')); ?>" class="dropdown-item <?php echo e(Request::is('analytics/live*') ? 'active' : ''); ?>">
                            <i class="bx bx-line-chart"></i>
                            <span class="menu-text">Live Analytics</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'retentionDropdownCoord')">
                    <i class="bx bx-refresh"></i>
                    <span class="menu-text">Retention & Chargebacks</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="retentionDropdownCoord">
                    <a href="<?php echo e(route('retention.dashboard')); ?>" class="dropdown-item <?php echo e(Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                        <i class="bx bx-tachometer"></i>
                        <span class="menu-text">Retention Dashboard</span>
                    </a>

                    <a href="<?php echo e(route('retention.index')); ?>" class="dropdown-item <?php echo e(Request::is('retention') && !Request::is('retention-dashboard*') && !Request::is('retention/incomplete*') ? 'active' : ''); ?>">
                        <i class="bx bx-user-check"></i>
                        <span class="menu-text">Manage Retention</span>
                    </a>

                    <a href="<?php echo e(route('chargebacks.index')); ?>" class="dropdown-item <?php echo e(Request::is('chargebacks*') ? 'active' : ''); ?>">
                        <i class="bx bx-error-circle"></i>
                        <span class="menu-text">Chargebacks</span>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo e(route('root')); ?>" class="menu-item <?php echo e(Request::is('/') ? 'active' : ''); ?>">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Company Overview</span>
                </a>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|CEO|Employee|Agent|Co-ordinator|Manager|Trainer|Ravens Closer|Peregrine Closer|Peregrine Validator|Verifier|QA|Retention Officer|HR')): ?>
                    <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'quickActionsDropdown')">
                        <i class="bx bx-bolt-circle"></i>
                        <span class="menu-text">Quick Actions</span>
                        <i class="bx bx-chevron-down dropdown-icon"></i>
                    </a>

                    <div class="menu-dropdown" id="quickActionsDropdown">
                        <a href="<?php echo e(route('followup.my-followups')); ?>" class="dropdown-item <?php echo e(Request::is('followup*') ? 'active' : ''); ?>">
                            <i class="bx bx-task"></i>
                            <span class="menu-text">My Followup & Bank Verification</span>
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'misDropdown')">
                    <i class="bx bx-briefcase-alt"></i>
                    <span class="menu-text">Sales Operations</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="misDropdown">
                    <a href="<?php echo e(route('leads.index')); ?>" class="dropdown-item <?php echo e(Request::is('leads*') && !Request::is('sales*') ? 'active' : ''); ?>">
                        <i class="bx bx-user-voice"></i>
                        <span class="menu-text">All Leads</span>
                    </a>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'QA|Super Admin|Manager|Co-ordinator|CEO')): ?>
                        <a href="<?php echo e(route('qa.review')); ?>" class="dropdown-item <?php echo e(Request::is('qa*') ? 'active' : ''); ?>">
                            <i class="bx bx-check-circle"></i>
                            <span class="menu-text">QA Review</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <a href="<?php echo e(route('sales.index')); ?>" class="dropdown-item <?php echo e(Request::is('sales*') ? 'active' : ''); ?>">
                        <i class="bx bx-dollar-circle"></i>
                        <span class="menu-text">Sales Records</span>
                    </a>

                    <a href="<?php echo e(route('issuance.index')); ?>" class="dropdown-item <?php echo e(Request::is('issuance*') ? 'active' : ''); ?>">
                        <i class="bx bx-send"></i>
                        <span class="menu-text">Policy Submission</span>
                    </a>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Manager|Co-ordinator|CEO|Manager')): ?>
                        <a href="<?php echo e(route('bank-verification.index')); ?>" class="dropdown-item <?php echo e(Request::is('bank-verification*') ? 'active' : ''); ?>">
                            <i class="bx bx-check-shield"></i>
                            <span class="menu-text">Bank Verification</span>
                        </a>

                        <a href="<?php echo e(route('revenue-analytics.index')); ?>" class="dropdown-item <?php echo e(Request::is('revenue-analytics*') ? 'active' : ''); ?>">
                            <i class="bx bx-line-chart"></i>
                            <span class="menu-text">Revenue Analytics</span>
                        </a>

                        <a href="<?php echo e(route('analytics.live')); ?>" class="dropdown-item <?php echo e(Request::is('analytics/live*') ? 'active' : ''); ?>">
                            <i class="bx bx-line-chart"></i>
                            <span class="menu-text">Live Analytics</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'retentionDropdown')">
                    <i class="bx bx-refresh"></i>
                    <span class="menu-text">Retention & Chargebacks</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="retentionDropdown">
                    <a href="<?php echo e(route('retention.dashboard')); ?>" class="dropdown-item <?php echo e(Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                        <i class="bx bx-tachometer"></i>
                        <span class="menu-text">Retention Dashboard</span>
                    </a>

                    <a href="<?php echo e(route('retention.index')); ?>" class="dropdown-item <?php echo e(Request::is('retention') && !Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                        <i class="bx bx-user-check"></i>
                        <span class="menu-text">Manage Retention</span>
                    </a>

                    <a href="<?php echo e(route('chargebacks.index')); ?>" class="dropdown-item <?php echo e(Request::is('chargebacks*') ? 'active' : ''); ?>">
                        <i class="bx bx-error-circle"></i>
                        <span class="menu-text">Chargebacks</span>
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Verifier|Peregrine Closer|Peregrine Validator|Manager|Super Admin|Co-ordinator|CEO')): ?>
            <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'peregrineDropdown')">
                <i class="bx bx-shield-alt"></i>
                <span class="menu-text">Peregrine Operations</span>
                <i class="bx bx-chevron-down dropdown-icon"></i>
            </a>

            <div class="menu-dropdown" id="peregrineDropdown">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Verifier|Super Admin|Co-ordinator|CEO')): ?>
                    <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Peregrine Validator')): ?>
                        <a href="<?php echo e(route('verifier.dashboard')); ?>" class="dropdown-item <?php echo e(Request::is('verifier/dashboard') ? 'active' : ''); ?>">
                            <i class="bx bx-shield-alt"></i>
                            <span class="menu-text">Peregrine Dashboard</span>
                        </a>

                        <a href="<?php echo e(route('verifier.create.team', 'peregrine')); ?>" class="dropdown-item <?php echo e(Request::is('verifier*create*') ? 'active' : ''); ?>">
                            <i class="bx bx-edit-alt"></i>
                            <span class="menu-text">Verifier Form</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Peregrine Closer|Super Admin|Co-ordinator|CEO')): ?>
                    <a href="<?php echo e(route('peregrine.closers.index')); ?>" class="dropdown-item <?php echo e(Request::is('peregrine/closers*') ? 'active' : ''); ?>">
                        <i class="bx bx-shield-alt"></i>
                        <span class="menu-text">Peregrine Closers</span>
                    </a>

                    <a href="<?php echo e(route('followup.my-followups')); ?>" class="dropdown-item <?php echo e(Request::is('followup*') ? 'active' : ''); ?>">
                        <i class="bx bx-task"></i>
                        <span class="menu-text">My Followup & Bank Verification</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Peregrine Validator|Manager|Super Admin|Co-ordinator|CEO')): ?>
                    <a href="<?php echo e(route('validator.index')); ?>" class="dropdown-item <?php echo e(Request::is('validator*') ? 'active' : ''); ?>">
                        <i class="bx bx-check-shield"></i>
                        <span class="menu-text">Validation Dashboard</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Ravens Closer|Super Admin|Manager|Co-ordinator|CEO')): ?>
            <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'ravensDropdown')">
                <i class="bx bx-phone-call"></i>
                <span class="menu-text">Ravens Operations</span>
                <i class="bx bx-chevron-down dropdown-icon"></i>
            </a>

            <div class="menu-dropdown" id="ravensDropdown">
                <a href="<?php echo e(route('ravens.dashboard')); ?>" class="dropdown-item <?php echo e(Request::is('ravens/dashboard') ? 'active' : ''); ?>">
                    <i class="bx bx-phone-call"></i>
                    <span class="menu-text">Ravens Dashboard</span>
                </a>

                <a href="<?php echo e(route('ravens.calling')); ?>" class="dropdown-item <?php echo e(Request::is('ravens/calling*') ? 'active' : ''); ?>">
                    <i class="bx bx-phone"></i>
                    <span class="menu-text">Ravens Calling</span>
                </a>

                <a href="<?php echo e(route('ravens.bad-leads')); ?>" class="dropdown-item <?php echo e(Request::is('ravens/bad-leads*') ? 'active' : ''); ?>">
                    <i class="bx bx-x-circle"></i>
                    <span class="menu-text">Bad Leads</span>
                </a>

                <a href="<?php echo e(route('followup.my-followups')); ?>" class="dropdown-item <?php echo e(Request::is('followup*') ? 'active' : ''); ?>">
                    <i class="bx bx-task"></i>
                    <span class="menu-text">My Followup & Bank Verification</span>
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'QA|HR|Super Admin|Manager|Co-ordinator|CEO')): ?>
            <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'hrOpsDropdown')">
                <i class="bx bx-user-check"></i>
                <span class="menu-text">HR Operations</span>
                <i class="bx bx-chevron-down dropdown-icon"></i>
            </a>

            <div class="menu-dropdown" id="hrOpsDropdown">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Manager|Co-ordinator|HR|CEO')): ?>
                    <a href="<?php echo e(route('employee.ems')); ?>" class="dropdown-item <?php echo e(Request::is('ems*') ? 'active' : ''); ?>">
                        <i class="bx bx-id-card"></i>
                        <span class="menu-text">E.M.S</span>
                    </a>

                    <a href="<?php echo e(route('attendance.index')); ?>" class="dropdown-item <?php echo e(Request::is('attendance*') ? 'active' : ''); ?>">
                        <i class="bx bx-time-five"></i>
                        <span class="menu-text">Attendance</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'QA|HR|Super Admin|Manager|Co-ordinator|CEO')): ?>
                    <a href="<?php echo e(route('dock.index')); ?>" class="dropdown-item <?php echo e(Request::is('dock*') ? 'active' : ''); ?>">
                        <i class="bx bx-dock-top"></i>
                        <span class="menu-text">Dock Management</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'HR|Super Admin|Co-ordinator|CEO')): ?>
                    <a href="<?php echo e(route('admin.public-holidays.index')); ?>" class="dropdown-item <?php echo e(Request::is('admin/public-holidays*') ? 'active' : ''); ?>">
                        <i class="bx bx-calendar"></i>
                        <span class="menu-text">Public Holidays</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|CEO')): ?>
            <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'epmsDropdown')">
                <i class="bx bx-briefcase-alt"></i>
                <span class="menu-text">Project Management</span>
                <i class="bx bx-chevron-down dropdown-icon"></i>
            </a>

            <div class="menu-dropdown" id="epmsDropdown">
                <a href="<?php echo e(route('epms.index')); ?>" class="dropdown-item <?php echo e(Request::is('epms') && !Request::is('epms/*') ? 'active' : ''); ?>">
                    <i class="bx bx-list-ul"></i>
                    <span class="menu-text">All Projects</span>
                </a>
                <a href="<?php echo e(route('epms.create')); ?>" class="dropdown-item <?php echo e(Request::is('epms/create') ? 'active' : ''); ?>">
                    <i class="bx bx-plus-circle"></i>
                    <span class="menu-text">New Project</span>
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Verifier|Peregrine Closer|Peregrine Validator|Employee|Ravens Closer')): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Co-ordinator|CEO|Manager')): ?>
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'partnersDropdown')">
                    <i class="bx bx-group"></i>
                    <span class="menu-text">Partner Management</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="partnersDropdown">
                    <a href="<?php echo e(route('agents.index')); ?>" class="dropdown-item <?php echo e(Request::is('agents*') ? 'active' : ''); ?>">
                        <i class="bx bx-user-circle"></i>
                        <span class="menu-text">Partners</span>
                    </a>

                    <a href="<?php echo e(route('admin.insurance-carriers.index')); ?>" class="dropdown-item <?php echo e(Request::is('admin/insurance-carriers*') ? 'active' : ''); ?>">
                        <i class="bx bx-buildings"></i>
                        <span class="menu-text">Insurance Cluster</span>
                    </a>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Manager|CEO')): ?>
                    <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'settingsDropdown')">
                        <i class="bx bx-cog"></i>
                        <span class="menu-text">Settings</span>
                        <i class="bx bx-chevron-down dropdown-icon"></i>
                    </a>

                    <div class="menu-dropdown" id="settingsDropdown">
                        <a href="<?php echo e(route('settings.index')); ?>" class="dropdown-item <?php echo e(Request::is('settings') ? 'active' : ''); ?>">
                            <i class="bx bx-slider-alt"></i>
                            <span class="menu-text">System Settings</span>
                        </a>

                        <a href="<?php echo e(route('admin.dupe-checker.index')); ?>" class="dropdown-item <?php echo e(Request::is('admin/dupe-checker*') ? 'active' : ''); ?>">
                            <i class="bx bx-copy-alt"></i>
                            <span class="menu-text">Duplicate Checker</span>
                        </a>

                        <a href="<?php echo e(route('admin.account-switching-log')); ?>" class="dropdown-item <?php echo e(Request::is('admin/account-switching-log*') ? 'active' : ''); ?>">
                            <i class="bx bx-transfer"></i>
                            <span class="menu-text">Account Switch Log</span>
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Manager|Co-ordinator|CEO')): ?>
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'financeDropdown')">
                    <i class="bx bx-dollar-circle"></i>
                    <span class="menu-text">Finance & Accounts</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="financeDropdown">
                    <a href="<?php echo e(route('chart-of-accounts.index')); ?>" class="dropdown-item <?php echo e(Request::is('chart-of-accounts*') ? 'active' : ''); ?>">
                        <i class="bx bx-list-ul"></i>
                        <span class="menu-text">Chart of Accounts</span>
                    </a>

                    <a href="<?php echo e(route('ledger.index')); ?>" class="dropdown-item <?php echo e(Request::is('ledger*') ? 'active' : ''); ?>">
                        <i class="bx bx-book-open"></i>
                        <span class="menu-text">General Ledger</span>
                    </a>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Co-ordinator|CEO')): ?>
                        <a href="<?php echo e(route('petty-cash.index')); ?>" class="dropdown-item <?php echo e(Request::is('petty-cash*') ? 'active' : ''); ?>">
                            <i class="bx bx-wallet"></i>
                            <span class="menu-text">Petty Cash</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <a href="<?php echo e(route('payroll.index')); ?>" class="dropdown-item <?php echo e(Request::is('payroll*') ? 'active' : ''); ?>">
                        <i class="bx bx-credit-card-alt"></i>
                        <span class="menu-text">Payroll</span>
                    </a>

                    <a href="<?php echo e(route('pabs.tickets.index')); ?>" class="dropdown-item <?php echo e(Request::is('pabs/tickets*') ? 'active' : ''); ?>">
                        <i class="bx bx-message-square-error"></i>
                        <span class="menu-text">PABS Tickets</span>
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Co-ordinator|CEO')): ?>
            <a href="<?php echo e(route('users.index')); ?>" class="menu-item <?php echo e(Request::is('users*') ? 'active' : ''); ?>">
                <i class="bx bx-user-circle"></i>
                <span class="menu-text">Users MGMT</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'myRecordsDropdown')">
            <i class="bx bx-user"></i>
            <span class="menu-text">My Records</span>
            <i class="bx bx-chevron-down dropdown-icon"></i>
        </a>

        <div class="menu-dropdown" id="myRecordsDropdown">
            <a href="<?php echo e(route('attendance.dashboard')); ?>" class="dropdown-item <?php echo e(Request::is('attendance/dashboard') ? 'active' : ''); ?>">
                <i class="bx bx-time-five"></i>
                <span class="menu-text">My Attendance</span>
            </a>

            <a href="<?php echo e(route('my-dock-records')); ?>" class="dropdown-item <?php echo e(Request::is('my-dock-records*') ? 'active' : ''); ?>">
                <i class="bx bx-dock-top"></i>
                <span class="menu-text">My Dock Records</span>
            </a>
        </div>
    </nav>
</div>

<style>
    /* ===== MODERN FLAT SIDEBAR DESIGN ===== */
    
    /* Logo Section */
    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 22px 18px;
        border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), transparent);
    }

    .logo-icon img {
        width: 42px;
        height: 42px;
        object-fit: contain;
        border-radius: 8px;
    }

    .logo-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .logo-text {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--gold, #d4af37);
        letter-spacing: 0.8px;
        line-height: 1;
    }

    .logo-subtext {
        font-size: 0.68rem;
        color: var(--text-muted, #9ca3af);
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    #sidebar.collapsed .logo-content {
        display: none;
    }

    /* Sidebar Menu */
    .sidebar-menu {
        padding: 12px 0;
        overflow-y: auto;
        max-height: calc(100vh - 100px);
    }

    /* Menu Items - Flat Design */
    .menu-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 18px;
        margin: 3px 12px;
        border-radius: 10px;
        font-size: 0.92rem;
        font-weight: 500;
        color: var(--text-secondary, #6b7280) !important;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .menu-item i {
        font-size: 21px;
        flex-shrink: 0;
        transition: all 0.25s ease;
    }

    .menu-item .menu-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .menu-item:hover {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.12) 0%, rgba(212, 175, 55, 0.04) 100%) !important;
        color: var(--gold, #d4af37) !important;
        transform: translateX(3px);
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.15);
    }

    .menu-item:hover i {
        transform: scale(1.08);
        color: var(--gold, #d4af37);
    }

    .menu-item.active {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.18) 0%, rgba(212, 175, 55, 0.06) 100%) !important;
        color: var(--gold, #d4af37) !important;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(212, 175, 55, 0.2);
    }

    .menu-item.active i {
        color: var(--gold, #d4af37);
    }

    .menu-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 60%;
        background: var(--gold, #d4af37);
        border-radius: 0 4px 4px 0;
    }

    #sidebar.collapsed .menu-item {
        justify-content: center;
        padding: 13px;
    }

    #sidebar.collapsed .menu-text {
        display: none;
    }

    /* Dropdown Toggle */
    .menu-dropdown-toggle {
        position: relative;
    }

    .dropdown-icon {
        margin-left: auto;
        font-size: 17px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }

    .dropdown-icon.rotated {
        transform: rotate(180deg);
    }

    /* Modern Flat Dropdown Design - NO NESTING */
    .menu-dropdown {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        margin: 0;
        padding: 0;
    }

    .menu-dropdown[style*="display: block"],
    .menu-dropdown[style*="display:block"] {
        max-height: 800px;
    }

    /* Dropdown Items - Flat Like Regular Items */
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 18px 12px 48px !important;
        margin: 2px 12px !important;
        border-radius: 10px;
        font-size: 0.88rem !important;
        font-weight: 500;
        color: var(--text-secondary, #6b7280) !important;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background: rgba(212, 175, 55, 0.02);
    }

    .dropdown-item i {
        font-size: 19px !important;
        opacity: 0.8;
        transition: all 0.25s ease;
    }

    .dropdown-item .menu-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%) !important;
        color: var(--gold-dark, #b8941f) !important;
        transform: translateX(3px);
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.15);
    }

    .dropdown-item:hover i {
        opacity: 1;
        transform: scale(1.08);
        color: var(--gold, #d4af37);
    }

    .dropdown-item.active {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.22) 0%, rgba(212, 175, 55, 0.08) 100%) !important;
        color: var(--gold-dark, #b8941f) !important;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(212, 175, 55, 0.25);
    }

    .dropdown-item.active i {
        color: var(--gold, #d4af37);
        opacity: 1;
    }

    .dropdown-item.active::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 50%;
        background: var(--gold, #d4af37);
        border-radius: 4px;
    }

    /* Sidebar Toggle Button */
    .sidebar-toggle {
        position: absolute;
        top: 30px;
        right: -13px;
        width: 26px;
        height: 26px;
        background: linear-gradient(135deg, var(--gold, #d4af37), var(--gold-dark, #b8941f)) !important;
        color: white !important;
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(212,175,55,0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
    }

    .sidebar-toggle:hover {
        transform: scale(1.18);
        box-shadow: 0 5px 16px rgba(212,175,55,0.45);
        background: linear-gradient(135deg, var(--gold-dark, #b8941f), var(--gold, #d4af37)) !important;
    }

    .sidebar-toggle i {
        font-size: 17px;
        transition: transform 0.3s ease;
    }

    #sidebar.collapsed .sidebar-toggle i {
        transform: rotate(180deg);
    }

    /* Scrollbar Styling */
    .sidebar-menu::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar-menu::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background: rgba(212, 175, 55, 0.25);
        border-radius: 10px;
    }

    .sidebar-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(212, 175, 55, 0.4);
    }

    /* Collapsed State */
    #sidebar.collapsed .menu-dropdown {
        display: none !important;
    }

    #sidebar.collapsed .dropdown-icon {
        display: none !important;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .menu-item,
        .dropdown-item {
            padding: 11px 14px;
            font-size: 0.87rem;
        }

        .dropdown-item {
            padding-left: 42px !important;
        }
    }

    /* Subtle Spacing Between Roles */
    .menu-item + .menu-item {
        margin-top: 2px;
    }

    /* Add slight spacing between different role sections */
    @hasrole + @hasrole .menu-item:first-child {
        margin-top: 8px;
    }
</style>

<script>
    function toggleDropdown(event, dropdownId) {
        event.preventDefault();
        const dropdown = document.getElementById(dropdownId);
        const icon = event.currentTarget.querySelector('.dropdown-icon');
        
        if (!dropdown) return;

        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
            if (icon) icon.classList.add('rotated');
        } else {
            dropdown.style.display = 'none';
            if (icon) icon.classList.remove('rotated');
        }
    }

    // Auto-expand active dropdown on page load
    document.addEventListener('DOMContentLoaded', function() {
        const activeItems = document.querySelectorAll('.dropdown-item.active');
        activeItems.forEach(item => {
            const dropdown = item.closest('.menu-dropdown');
            if (dropdown) {
                dropdown.style.display = 'block';
                const toggleBtn = dropdown.previousElementSibling;
                if (toggleBtn && toggleBtn.classList.contains('menu-dropdown-toggle')) {
                    const icon = toggleBtn.querySelector('.dropdown-icon');
                    if (icon) icon.classList.add('rotated');
                }
            }
        });
    });
</script>

<!-- Left Sidebar End -->
<?php /**PATH /var/www/taurus-crm/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>