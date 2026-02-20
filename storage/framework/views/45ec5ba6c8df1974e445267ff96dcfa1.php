<?php use \App\Support\Roles; ?>
<!-- ========== Left Sidebar Start ========== -->
<div id="sidebar">
    <!-- User Profile -->
    <div class="sidebar-profile" id="sidebarProfile">
        <div class="sidebar-avatar-wrapper" onclick="toggleProfileDropdown(event)">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->avatar): ?>
                <img src="<?php echo e(asset(Auth::user()->avatar)); ?>" alt="<?php echo e(Auth::user()->name); ?>" class="sidebar-avatar">
            <?php else: ?>
                <div class="sidebar-avatar sidebar-avatar-initial">
                    <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="sidebar-profile-info">
            <span class="sidebar-profile-name"><?php echo e(Auth::user()->name); ?></span>
            <span class="sidebar-profile-role"><?php echo e(Auth::user()->roles->first()?->name ?? 'User'); ?></span>
        </div>
        <!-- Profile Dropdown -->
        <div class="sidebar-profile-dropdown" id="profileDropdown">
            <div class="profile-dropdown-header">
                <div class="profile-dropdown-avatar">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->avatar): ?>
                        <img src="<?php echo e(asset(Auth::user()->avatar)); ?>" alt="<?php echo e(Auth::user()->name); ?>">
                    <?php else: ?>
                        <div class="avatar-initial"><?php echo e(substr(Auth::user()->name, 0, 1)); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="profile-dropdown-info">
                    <span class="profile-dropdown-name"><?php echo e(Auth::user()->name); ?></span>
                    <span class="profile-dropdown-email"><?php echo e(Auth::user()->email); ?></span>
                </div>
            </div>
            <div class="profile-dropdown-divider"></div>
            <a href="#" class="profile-dropdown-item" data-bs-toggle="modal" data-bs-target="#profileSettingsModal" onclick="closeProfileDropdown()">
                <i class="bx bx-edit-alt"></i>
                <span>Edit Profile</span>
            </a>
            <a href="<?php echo e(route('logout.get')); ?>" class="profile-dropdown-item profile-dropdown-logout">
                <i class="bx bx-log-out"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bx bx-chevron-left"></i>
    </button>

    <!-- Menu -->
    <nav class="sidebar-menu">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::RETENTION_OFFICER)): ?>
            <?php if(auth()->check() && auth()->user()->canViewModule('dashboard')): ?>
                <a href="<?php echo e(route('retention.dashboard')); ?>" class="menu-item <?php echo e(Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Company Overview</span>
                </a>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->canViewModule('retention')): ?>
                <a href="<?php echo e(route('retention.index')); ?>" class="menu-item <?php echo e(Request::is('retention') || Request::is('retention/*') ? 'active' : ''); ?>">
                    <i class="bx bx-refresh"></i>
                    <span class="menu-text">Retention Management</span>
                </a>
            <?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if (! \Illuminate\Support\Facades\Blade::check('role', [Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::RETENTION_OFFICER, Roles::QA, Roles::HR])): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::EMPLOYEE)): ?>
                <!-- Employee only sees COMMUNICATION section below -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::RAVENS_CLOSER)): ?>
                <!-- Ravens Closer sees same as Employee - attendance and chat only -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::QA)): ?>
                <!-- QA only sees QA Review and COMMUNICATION section below -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', Roles::HR)): ?>
                <!-- HR only sees Dock, Attendance, and Public Holidays -->
            <?php else: ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('dashboard')): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="menu-item <?php echo e(Request::is('dashboard') ? 'active' : ''); ?>">
                        <i class="bx bx-home-circle"></i>
                        <span class="menu-text">Company Overview</span>
                    </a>
                <?php endif; ?>

                <?php if(auth()->check() && auth()->user()->canViewModule('sales')): ?>
                    <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'misDropdown')">
                        <i class="bx bx-briefcase-alt"></i>
                        <span class="menu-text">Sales Operations</span>
                        <i class="bx bx-chevron-down dropdown-icon"></i>
                    </a>

                    <div class="menu-dropdown" id="misDropdown">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])): ?>
                            <?php if(auth()->check() && auth()->user()->canViewModule('leads-peregrine')): ?>
                                <a href="<?php echo e(route('leads.peregrine')); ?>" class="dropdown-item <?php echo e(Request::is('leads/peregrine*') ? 'active' : ''); ?>">
                                    <i class="bx bx-user-voice"></i>
                                    <span class="menu-text">Peregrine Leads</span>
                                </a>
                            <?php endif; ?>

                            <?php if(auth()->check() && auth()->user()->canViewModule('leads')): ?>
                                <a href="<?php echo e(route('leads.index')); ?>" class="dropdown-item <?php echo e(Request::is('leads') && !Request::is('leads/peregrine*') && !Request::is('sales*') ? 'active' : ''); ?>">
                                    <i class="bx bx-briefcase"></i>
                                    <span class="menu-text">Raven Leads</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::QA, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])): ?>
                            <?php if(auth()->check() && auth()->user()->canViewModule('qa-review')): ?>
                                <a href="<?php echo e(route('qa.review')); ?>" class="dropdown-item <?php echo e(Request::is('qa*') ? 'active' : ''); ?>">
                                    <i class="bx bx-check-circle"></i>
                                    <span class="menu-text">QA Review</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(auth()->check() && auth()->user()->canViewModule('sales')): ?>
                            <a href="<?php echo e(route('sales.index')); ?>" class="dropdown-item <?php echo e(Request::is('sales*') ? 'active' : ''); ?>">
                                <i class="bx bx-dollar-circle"></i>
                                <span class="menu-text">Sales Records</span>
                            </a>
                        <?php endif; ?>

                        <?php if(auth()->check() && auth()->user()->canViewModule('issuance')): ?>
                            <a href="<?php echo e(route('issuance.index')); ?>" class="dropdown-item <?php echo e(Request::is('issuance*') ? 'active' : ''); ?>">
                                <i class="bx bx-send"></i>
                                <span class="menu-text">Policy Submission</span>
                            </a>
                        <?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])): ?>
                            <?php if(auth()->check() && auth()->user()->canViewModule('bank-verification')): ?>
                                <a href="<?php echo e(route('bank-verification.index')); ?>" class="dropdown-item <?php echo e(Request::is('bank-verification*') ? 'active' : ''); ?>">
                                    <i class="bx bx-check-shield"></i>
                                    <span class="menu-text">Bank Verification</span>
                                </a>
                            <?php endif; ?>

                            <?php if(auth()->check() && auth()->user()->canViewModule('revenue-analytics')): ?>
                                <a href="<?php echo e(route('revenue-analytics.index')); ?>" class="dropdown-item <?php echo e(Request::is('revenue-analytics*') ? 'active' : ''); ?>">
                                    <i class="bx bx-line-chart"></i>
                                    <span class="menu-text">Revenue Analytics</span>
                                </a>
                            <?php endif; ?>

                            <?php if(auth()->check() && auth()->user()->canViewModule('live-analytics')): ?>
                                <a href="<?php echo e(route('analytics.live')); ?>" class="dropdown-item <?php echo e(Request::is('analytics/live*') ? 'active' : ''); ?>">
                                    <i class="bx bx-line-chart"></i>
                                    <span class="menu-text">Live Analytics</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if(auth()->check() && auth()->user()->canViewModule('retention')): ?>
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
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::MANAGER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])): ?>
            <?php if(auth()->check() && auth()->user()->canViewModule('peregrine')): ?>
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'peregrineDropdown')">
                    <i class="bx bx-shield-alt"></i>
                    <span class="menu-text">Peregrine Operations</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="peregrineDropdown">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::VERIFIER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])): ?>
                        <?php if (! \Illuminate\Support\Facades\Blade::check('role', Roles::PEREGRINE_VALIDATOR)): ?>
                            <?php if(auth()->check() && auth()->user()->canViewModule('peregrine-dashboard')): ?>
                                <a href="<?php echo e(route('verifier.dashboard')); ?>" class="dropdown-item <?php echo e(Request::is('verifier/dashboard') ? 'active' : ''); ?>">
                                    <i class="bx bx-shield-alt"></i>
                                    <span class="menu-text">Peregrine Dashboard</span>
                                </a>
                            <?php endif; ?>

                            <?php if(auth()->check() && auth()->user()->canViewModule('peregrine-verifier')): ?>
                                <a href="<?php echo e(route('verifier.create.team', 'peregrine')); ?>" class="dropdown-item <?php echo e(Request::is('verifier*create*') ? 'active' : ''); ?>">
                                    <i class="bx bx-edit-alt"></i>
                                    <span class="menu-text">Verifier Form</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::PEREGRINE_CLOSER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])): ?>
                        <?php if(auth()->check() && auth()->user()->canViewModule('peregrine-closers')): ?>
                            <a href="<?php echo e(route('peregrine.closers.index')); ?>" class="dropdown-item <?php echo e(Request::is('peregrine/closers*') ? 'active' : ''); ?>">
                                <i class="bx bx-shield-alt"></i>
                                <span class="menu-text">Peregrine Closers</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::PEREGRINE_VALIDATOR, Roles::MANAGER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])): ?>
                        <?php if(auth()->check() && auth()->user()->canViewModule('peregrine-validation')): ?>
                            <a href="<?php echo e(route('validator.index')); ?>" class="dropdown-item <?php echo e(Request::is('validator*') ? 'active' : ''); ?>">
                                <i class="bx bx-check-shield"></i>
                                <span class="menu-text">Validation Dashboard</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::RAVENS_CLOSER, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])): ?>
            <?php if(auth()->check() && auth()->user()->canViewModule('ravens')): ?>
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'ravensDropdown')">
                    <i class="bx bx-phone-call"></i>
                    <span class="menu-text">Ravens Operations</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="ravensDropdown">
                    <?php if(auth()->check() && auth()->user()->canViewModule('ravens-dashboard')): ?>
                        <a href="<?php echo e(route('ravens.dashboard')); ?>" class="dropdown-item <?php echo e(Request::is('ravens/dashboard') ? 'active' : ''); ?>">
                            <i class="bx bx-phone-call"></i>
                            <span class="menu-text">Ravens Dashboard</span>
                        </a>
                    <?php endif; ?>

                    <?php if(auth()->check() && auth()->user()->canViewModule('ravens-calling')): ?>
                        <a href="<?php echo e(route('ravens.calling')); ?>" class="dropdown-item <?php echo e(Request::is('ravens/calling*') ? 'active' : ''); ?>">
                            <i class="bx bx-phone"></i>
                            <span class="menu-text">Ravens Calling</span>
                        </a>
                    <?php endif; ?>

                    <?php if(auth()->check() && auth()->user()->canViewModule('ravens-bad-leads')): ?>
                        <a href="<?php echo e(route('ravens.bad-leads')); ?>" class="dropdown-item <?php echo e(Request::is('ravens/bad-leads*') ? 'active' : ''); ?>">
                            <i class="bx bx-x-circle"></i>
                            <span class="menu-text">Bad Leads</span>
                        </a>
                    <?php endif; ?>

                    <?php if(auth()->check() && auth()->user()->canViewModule('ravens-followups')): ?>
                        <a href="<?php echo e(route('followup.my-followups')); ?>" class="dropdown-item <?php echo e(Request::is('followup*') ? 'active' : ''); ?>">
                            <i class="bx bx-task"></i>
                            <span class="menu-text">My Followup & Bank Verification</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::QA, Roles::HR, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])): ?>
            <?php if(auth()->check() && auth()->user()->canViewModule('hr')): ?>
                <a href="<?php echo e(route('hr.hub')); ?>" class="menu-item <?php echo e(Request::is('hr/hub') || Request::is('ems*') || Request::is('attendance*') || Request::is('dock*') || Request::is('admin/public-holidays*') ? 'active' : ''); ?>">
                    <i class="bx bx-user-check"></i>
                    <span class="menu-text">HR Operations</span>
                </a>
            <?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::CEO])): ?>
            <?php if(auth()->check() && auth()->user()->canViewModule('epms')): ?>
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
            <?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if (! \Illuminate\Support\Facades\Blade::check('role', [Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::EMPLOYEE, Roles::RAVENS_CLOSER])): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO, Roles::MANAGER])): ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('partners')): ?>
                    <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'partnersDropdown')">
                        <i class="bx bx-group"></i>
                        <span class="menu-text">Partner Management</span>
                        <i class="bx bx-chevron-down dropdown-icon"></i>
                    </a>

                    <div class="menu-dropdown" id="partnersDropdown">
                        <?php if(auth()->check() && auth()->user()->canViewModule('partners')): ?>
                            <a href="<?php echo e(route('agents.index')); ?>" class="dropdown-item <?php echo e(Request::is('agents*') ? 'active' : ''); ?>">
                                <i class="bx bx-user-circle"></i>
                                <span class="menu-text">Partners</span>
                            </a>
                        <?php endif; ?>

                        <?php if(auth()->check() && auth()->user()->canViewModule('carriers')): ?>
                            <a href="<?php echo e(route('admin.insurance-carriers.index')); ?>" class="dropdown-item <?php echo e(Request::is('admin/insurance-carriers*') ? 'active' : ''); ?>">
                                <i class="bx bx-buildings"></i>
                                <span class="menu-text">Insurance Cluster</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])): ?>
                <?php if(auth()->check() && auth()->user()->canViewModule('finance')): ?>
                    <a href="<?php echo e(route('finance.hub')); ?>" class="menu-item <?php echo e(Request::is('finance/hub') || Request::is('chart-of-accounts*') || Request::is('ledger*') || Request::is('petty-cash*') || Request::is('payroll*') || Request::is('pabs/tickets*') ? 'active' : ''); ?>">
                        <i class="bx bx-dollar-circle"></i>
                        <span class="menu-text">Finance & Accounts</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])): ?>
            <?php if(auth()->check() && auth()->user()->canViewModule('users')): ?>
                <a href="<?php echo e(route('users.index')); ?>" class="menu-item <?php echo e(Request::is('users*') ? 'active' : ''); ?>">
                    <i class="bx bx-user-circle"></i>
                    <span class="menu-text">Users MGMT</span>
                </a>
            <?php endif; ?>
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

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', [Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])): ?>
        <?php if(auth()->check() && auth()->user()->canViewModule('settings')): ?>
            <div class="sidebar-bottom">
                <a href="<?php echo e(route('settings.hub')); ?>" class="sidebar-bottom-item <?php echo e(Request::is('settings*') || Request::is('admin/dupe-checker*') || Request::is('admin/account-switching-log*') ? 'active' : ''); ?>">
                    <i class="bx bx-cog"></i>
                    <span class="sidebar-bottom-text">Settings</span>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<style>
    /* ===== REFINED SIDEBAR DESIGN ===== */

    /* Sidebar Profile Section */
    .sidebar-profile {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-bottom: 1px solid rgba(212, 175, 55, 0.08);
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.04), transparent);
        position: relative;
        height: 58px;
        box-sizing: border-box;
    }

    .sidebar-avatar-wrapper {
        cursor: pointer;
        flex-shrink: 0;
        position: relative;
    }

    .sidebar-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 2px solid var(--gold, #d4af37);
        object-fit: cover;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.15);
    }

    .sidebar-avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        background: linear-gradient(135deg, var(--gold, #d4af37), #b8922e);
        color: #fff;
    }

    .sidebar-avatar-wrapper:hover .sidebar-avatar {
        transform: scale(1.06);
        box-shadow: 0 3px 12px rgba(212, 175, 55, 0.3);
        border-color: var(--gold-dark, #b8922e);
    }

    .sidebar-profile-info {
        display: flex;
        flex-direction: column;
        gap: 1px;
        overflow: hidden;
        min-width: 0;
    }

    .sidebar-profile-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-primary, var(--bs-surface-700));
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
        letter-spacing: 0.1px;
    }

    .sidebar-profile-role {
        font-size: 0.6rem;
        color: var(--gold, #d4af37);
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #sidebar.collapsed .sidebar-profile-info {
        display: none;
    }

    #sidebar.collapsed .sidebar-profile {
        justify-content: center;
        padding: 12px 8px;
    }

    /* Profile Dropdown */
    .sidebar-profile-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 12px;
        width: 230px;
        background: var(--bg-panel, #ffffff);
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12), 0 2px 10px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(212, 175, 55, 0.12);
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .sidebar-profile-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    #sidebar.collapsed .sidebar-profile-dropdown {
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
    }

    #sidebar.collapsed .sidebar-profile-dropdown.show {
        transform: translateX(-50%) translateY(0);
    }

    .profile-dropdown-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.06), transparent);
    }

    .profile-dropdown-avatar img,
    .profile-dropdown-avatar .avatar-initial {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 2px solid var(--gold, #d4af37);
        object-fit: cover;
    }

    .profile-dropdown-avatar .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        background: linear-gradient(135deg, var(--gold, #d4af37), #b8922e);
        color: #fff;
    }

    .profile-dropdown-info {
        display: flex;
        flex-direction: column;
        gap: 1px;
        min-width: 0;
    }

    .profile-dropdown-name {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-primary, #111827);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-dropdown-email {
        font-size: 0.68rem;
        color: var(--text-muted, #6b7280);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-dropdown-divider {
        height: 1px;
        background: rgba(212, 175, 55, 0.08);
        margin: 0;
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 14px;
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--text-secondary, #374151) !important;
        text-decoration: none !important;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .profile-dropdown-item i {
        font-size: 17px;
        color: var(--text-muted, #6b7280);
        transition: all 0.15s ease;
    }

    .profile-dropdown-item:hover {
        background: rgba(212, 175, 55, 0.08);
        color: var(--gold, #d4af37) !important;
    }

    .profile-dropdown-item:hover i {
        color: var(--gold, #d4af37);
    }

    .profile-dropdown-logout {
        border-top: 1px solid rgba(220, 38, 38, 0.06);
    }

    .profile-dropdown-logout:hover {
        background: rgba(220, 38, 38, 0.06) !important;
        color: #dc2626 !important;
    }

    .profile-dropdown-logout:hover i {
        color: #dc2626 !important;
    }

    /* Sidebar Menu */
    .sidebar-menu {
        padding: 8px 0;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }

    /* Section Label */
    .menu-label {
        color: #94a3b8;
        font-size: 0.58rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 12px 20px 4px;
    }

    /* Menu Items */
    .menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        margin: 1px 8px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-secondary, var(--bs-surface-500)) !important;
        text-decoration: none;
        transition: all 0.15s ease;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.1px;
    }

    .menu-item i {
        font-size: 18px;
        flex-shrink: 0;
        transition: all 0.15s ease;
        opacity: 0.7;
    }

    .menu-item .menu-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }

    .menu-item:hover {
        background: rgba(212, 175, 55, 0.08) !important;
        color: var(--gold, var(--bs-gold)) !important;
        transform: translateX(2px);
    }

    .menu-item:hover i {
        color: var(--gold, var(--bs-gold));
        opacity: 1;
    }

    .menu-item.active {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.14) 0%, rgba(212, 175, 55, 0.04) 100%) !important;
        color: var(--gold, var(--bs-gold)) !important;
        font-weight: 600;
    }

    .menu-item.active i {
        color: var(--gold, var(--bs-gold));
        opacity: 1;
    }

    .menu-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 55%;
        background: var(--gold, var(--bs-gold));
        border-radius: 0 3px 3px 0;
    }

    #sidebar.collapsed .menu-item {
        justify-content: center;
        padding: 10px;
        margin: 1px 6px;
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
        font-size: 15px;
        transition: transform 0.25s ease;
        flex-shrink: 0;
        opacity: 0.4;
    }

    .dropdown-icon.rotated {
        transform: rotate(180deg);
        opacity: 0.7;
    }

    /* Dropdown Panel */
    .menu-dropdown {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        margin: 0;
        padding: 0;
    }

    .menu-dropdown[style*="display: block"],
    .menu-dropdown[style*="display:block"] {
        max-height: 800px;
    }

    /* Dropdown Items */
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 14px 7px 42px !important;
        margin: 1px 8px !important;
        border-radius: 8px;
        font-size: 0.78rem !important;
        font-weight: 500;
        color: var(--text-secondary, var(--bs-surface-500)) !important;
        text-decoration: none;
        transition: all 0.15s ease;
        position: relative;
        background: transparent;
        letter-spacing: 0.1px;
    }

    .dropdown-item i {
        font-size: 16px !important;
        opacity: 0.55;
        transition: all 0.15s ease;
    }

    .dropdown-item .menu-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-item:hover {
        background: rgba(212, 175, 55, 0.08) !important;
        color: var(--gold-dark, var(--bs-gold-dark)) !important;
        transform: translateX(2px);
    }

    .dropdown-item:hover i {
        opacity: 1;
        color: var(--gold, var(--bs-gold));
    }

    .dropdown-item.active {
        background: rgba(212, 175, 55, 0.12) !important;
        color: var(--gold, var(--bs-gold)) !important;
        font-weight: 600;
    }

    .dropdown-item.active i {
        color: var(--gold, var(--bs-gold));
        opacity: 1;
    }

    .dropdown-item.active::before {
        content: '';
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 45%;
        background: var(--gold, var(--bs-gold));
        border-radius: 3px;
    }

    /* Sidebar Toggle Button */
    .sidebar-toggle {
        position: absolute;
        top: 28px;
        right: -12px;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, var(--gold, var(--bs-gold)), var(--gold-dark, var(--bs-gold-dark))) !important;
        color: var(--bs-white) !important;
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(212,175,55,0.25);
        transition: all 0.2s ease;
        z-index: 1000;
    }

    .sidebar-toggle:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 14px rgba(212,175,55,0.4);
    }

    .sidebar-toggle i {
        font-size: 15px;
        transition: transform 0.25s ease;
    }

    #sidebar.collapsed .sidebar-toggle i {
        transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidebar-menu::-webkit-scrollbar {
        width: 3px;
    }

    .sidebar-menu::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background: rgba(212, 175, 55, 0.2);
        border-radius: 10px;
    }

    .sidebar-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(212, 175, 55, 0.35);
    }

    /* Collapsed State */
    #sidebar.collapsed .menu-dropdown {
        display: none !important;
    }

    #sidebar.collapsed .dropdown-icon {
        display: none !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .menu-item,
        .dropdown-item {
            padding: 8px 12px;
            font-size: 0.79rem;
        }

        .dropdown-item {
            padding-left: 38px !important;
        }
    }

    /* Spacing */
    .menu-item + .menu-item {
        margin-top: 0;
    }

    /* Sidebar Bottom - Settings */
    .sidebar-bottom {
        padding: 6px 8px;
        border-top: 1px solid rgba(212, 175, 55, 0.08);
        flex-shrink: 0;
    }

    .sidebar-bottom-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-secondary, var(--bs-surface-500)) !important;
        text-decoration: none !important;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .sidebar-bottom-item i {
        font-size: 18px;
        flex-shrink: 0;
        opacity: 0.7;
        transition: all 0.2s ease;
    }

    .sidebar-bottom-item:hover {
        background: rgba(212, 175, 55, 0.08);
        color: var(--gold, var(--bs-gold)) !important;
    }

    .sidebar-bottom-item:hover i {
        color: var(--gold, var(--bs-gold));
        opacity: 1;
        transform: rotate(60deg);
    }

    .sidebar-bottom-item.active {
        background: rgba(212, 175, 55, 0.12);
        color: var(--gold, var(--bs-gold)) !important;
        font-weight: 600;
    }

    .sidebar-bottom-item.active i {
        color: var(--gold, var(--bs-gold));
        opacity: 1;
    }

    #sidebar.collapsed .sidebar-bottom-item {
        justify-content: center;
        padding: 10px;
    }

    #sidebar.collapsed .sidebar-bottom-text {
        display: none;
    }
</style>

<script>
    function toggleProfileDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('show');
    }

    function closeProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.remove('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const profile = document.getElementById('sidebarProfile');
        if (dropdown && profile && !profile.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

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