<!-- ========== Left Sidebar Start ========== -->
<div id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <img src="<?php echo e(asset('images/icon.png')); ?>" alt="Taurus" onerror="this.style.display='none'">
        <span class="logo-text">TAURUS MIS</span>
    </div>

    <!-- Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bx bx-chevron-left"></i>
    </button>

    <!-- Menu -->
    <nav class="sidebar-menu">
        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Retention Officer')): ?>
            <div class="menu-label">RETENTION</div>

            <a href="<?php echo e(route('retention.dashboard')); ?>" class="menu-item <?php echo e(Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                <i class="bx bx-home-circle"></i>
                <span class="menu-text">My Dashboard</span>
            </a>

            <a href="<?php echo e(route('retention.index')); ?>" class="menu-item <?php echo e(Request::is('retention') || Request::is('retention/*') ? 'active' : ''); ?>">
                <i class="bx bx-refresh"></i>
                <span class="menu-text">Retention Management</span>
            </a>
        <?php endif; ?>

        <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Verifier|Paraguins Closer|Paraguins Validator|Employee|Ravens Closer|Retention Officer|QA')): ?>
            <div class="menu-label">MAIN</div>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Employee')): ?>
                <!-- Employee only sees COMMUNICATION section below -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', 'Ravens Closer')): ?>
                <!-- Ravens Closer sees same as Employee - attendance and chat only -->
            <?php elseif (\Illuminate\Support\Facades\Blade::check('hasrole', 'QA')): ?>
                <!-- QA only sees QA Review and COMMUNICATION section below -->
            <?php else: ?>
                <a href="<?php echo e(route('root')); ?>" class="menu-item <?php echo e(Request::is('/') ? 'active' : ''); ?>">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Dashboard</span>
                </a>

                <div class="menu-label">CRM</div>

                <a href="<?php echo e(route('leads.index')); ?>" class="menu-item <?php echo e(Request::is('leads*') && !Request::is('sales*') ? 'active' : ''); ?>">
                    <i class="bx bx-user-plus"></i>
                    <span class="menu-text">All Leads</span>
                </a>

                <a href="<?php echo e(route('sales.index')); ?>" class="menu-item <?php echo e(Request::is('sales*') ? 'active' : ''); ?>">
                    <i class="bx bx-dollar-circle"></i>
                    <span class="menu-text">Sales</span>
                </a>

                <a href="<?php echo e(route('chargebacks.index')); ?>" class="menu-item <?php echo e(Request::is('chargebacks*') ? 'active' : ''); ?>">
                    <i class="bx bx-error"></i>
                    <span class="menu-text">Chargebacks</span>
                </a>

                <div class="menu-label">RETENTION</div>

                <a href="<?php echo e(route('retention.dashboard')); ?>" class="menu-item <?php echo e(Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Retention Dashboard</span>
                </a>

                <a href="<?php echo e(route('retention.index')); ?>" class="menu-item <?php echo e(Request::is('retention') && !Request::is('retention-dashboard*') ? 'active' : ''); ?>">
                    <i class="bx bx-refresh"></i>
                    <span class="menu-text">Retention Management</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Verifier|Super Admin')): ?>
            <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Paraguins Validator')): ?>
                <div class="menu-label">PARAGUINS</div>

                <a href="<?php echo e(route('verifier.dashboard')); ?>" class="menu-item <?php echo e(Request::is('verifier/dashboard') ? 'active' : ''); ?>">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">My Dashboard</span>
                </a>

                <a href="<?php echo e(route('verifier.create.team', 'paraguins')); ?>" class="menu-item <?php echo e(Request::is('verifier*create*') ? 'active' : ''); ?>">
                    <i class="bx bx-check-shield"></i>
                    <span class="menu-text">Verifier Form</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Paraguins Closer|Super Admin')): ?>
            <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Verifier|Paraguins Validator')): ?>
                <div class="menu-label">PARAGUINS</div>
            <?php endif; ?>

            <a href="<?php echo e(route('paraguins.closers.index')); ?>" class="menu-item <?php echo e(Request::is('paraguins/closers*') ? 'active' : ''); ?>">
                <i class="bx bx-edit"></i>
                <span class="menu-text">Paraguins Leads</span>
            </a>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Paraguins Validator|Super Admin')): ?>
            <div class="menu-label">VALIDATOR</div>

            <a href="<?php echo e(route('validator.index')); ?>" class="menu-item <?php echo e(Request::is('validator*') ? 'active' : ''); ?>">
                <i class="bx bx-check-shield"></i>
                <span class="menu-text">Validation Dashboard</span>
            </a>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Ravens Closer|Super Admin')): ?>
            <div class="menu-label">RAVENS</div>

            <a href="<?php echo e(route('ravens.dashboard')); ?>" class="menu-item <?php echo e(Request::is('ravens/dashboard') ? 'active' : ''); ?>">
                <i class="bx bx-home-circle"></i>
                <span class="menu-text">Ravens Dashboard</span>
            </a>

            <a href="<?php echo e(route('ravens.calling')); ?>" class="menu-item <?php echo e(Request::is('ravens/calling*') ? 'active' : ''); ?>">
                <i class="bx bx-phone"></i>
                <span class="menu-text">Calling System</span>
            </a>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'QA|HR|Super Admin|Manager')): ?>
            <div class="menu-label">QA / HR</div>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'QA|Super Admin')): ?>
            <a href="<?php echo e(route('qa.review')); ?>" class="menu-item <?php echo e(Request::is('qa*') ? 'active' : ''); ?>">
                <i class="bx bx-check-double"></i>
                <span class="menu-text">QA Review</span>
            </a>
            <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'QA|HR|Super Admin|Manager')): ?>
            <a href="<?php echo e(route('dock.index')); ?>" class="menu-item <?php echo e(Request::is('dock*') ? 'active' : ''); ?>">
                <i class="mdi mdi-cash-minus"></i>
                <span class="menu-text">Dock Section</span>
            </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'Verifier|Paraguins Closer|Paraguins Validator|Employee|Ravens Closer')): ?>
            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin')): ?>
                <div class="menu-label">ADMIN</div>

                <a href="<?php echo e(route('agents.index')); ?>" class="menu-item <?php echo e(Request::is('agents*') ? 'active' : ''); ?>">
                    <i class="bx bx-user-circle"></i>
                    <span class="menu-text">Partners</span>
                </a>

                <a href="<?php echo e(route('users.index')); ?>" class="menu-item <?php echo e(Request::is('users*') ? 'active' : ''); ?>">
                    <i class="bx bx-group"></i>
                    <span class="menu-text">Users</span>
                </a>
            <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin|Manager')): ?>

                <a href="<?php echo e(route('ledger.index')); ?>" class="menu-item <?php echo e(Request::is('ledger*') ? 'active' : ''); ?>">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    <span class="menu-text">Ledger</span>
                </a>

                <div class="menu-label">OPERATIONS</div>

                <a href="<?php echo e(route('attendance.index')); ?>" class="menu-item <?php echo e(Request::is('attendance*') ? 'active' : ''); ?>">
                    <i class="bx bx-time-five"></i>
                    <span class="menu-text">Attendance</span>
                </a>

                <a href="<?php echo e(route('salary.index')); ?>" class="menu-item <?php echo e(Request::is('salary*') ? 'active' : ''); ?>">
                    <i class="bx bx-dollar-circle"></i>
                    <span class="menu-text">Salary</span>
                </a>

                <div class="menu-label">SETTINGS</div>

                <a href="<?php echo e(route('settings.index')); ?>" class="menu-item <?php echo e(Request::is('settings*') ? 'active' : ''); ?>">
                    <i class="bx bx-cog"></i>
                    <span class="menu-text">Settings</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <div class="menu-label">COMMUNICATION</div>

        <a href="<?php echo e(route('attendance.dashboard')); ?>" class="menu-item <?php echo e(Request::is('attendance/dashboard') ? 'active' : ''); ?>">
            <i class="bx bx-calendar-check"></i>
            <span class="menu-text">My Attendance</span>
        </a>

        <a href="<?php echo e(route('chat.index')); ?>" class="menu-item <?php echo e(Request::is('chat*') ? 'active' : ''); ?>">
            <i class="bx bx-message-square-dots"></i>
            <span class="menu-text">Team Chat</span>
        </a>
    </nav>
</div>
<!-- Left Sidebar End -->
<?php /**PATH C:\code\taurus-crm-master\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>