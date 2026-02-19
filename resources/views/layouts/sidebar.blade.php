@use('App\Support\Roles')
<!-- ========== Left Sidebar Start ========== -->
<div id="sidebar">
    <!-- User Profile -->
    <div class="sidebar-profile" id="sidebarProfile">
        <div class="sidebar-avatar-wrapper" onclick="toggleProfileDropdown(event)">
            @if(Auth::user()->avatar)
                <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="sidebar-avatar">
            @else
                <div class="sidebar-avatar sidebar-avatar-initial">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="sidebar-profile-info">
            <span class="sidebar-profile-name">{{ Auth::user()->name }}</span>
            <span class="sidebar-profile-role">{{ Auth::user()->roles->first()?->name ?? 'User' }}</span>
        </div>
        <!-- Profile Dropdown -->
        <div class="sidebar-profile-dropdown" id="profileDropdown">
            <div class="profile-dropdown-header">
                <div class="profile-dropdown-avatar">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                    @else
                        <div class="avatar-initial">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    @endif
                </div>
                <div class="profile-dropdown-info">
                    <span class="profile-dropdown-name">{{ Auth::user()->name }}</span>
                    <span class="profile-dropdown-email">{{ Auth::user()->email }}</span>
                </div>
            </div>
            <div class="profile-dropdown-divider"></div>
            <a href="#" class="profile-dropdown-item" data-bs-toggle="modal" data-bs-target="#profileSettingsModal" onclick="closeProfileDropdown()">
                <i class="bx bx-edit-alt"></i>
                <span>Edit Profile</span>
            </a>
            <a href="{{ route('logout.get') }}" class="profile-dropdown-item profile-dropdown-logout">
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
        {{-- RETENTION OFFICER --}}
        @hasrole(Roles::RETENTION_OFFICER)
            @canViewModule('dashboard')
                <a href="{{ route('retention.dashboard') }}" class="menu-item {{ Request::is('retention-dashboard*') ? 'active' : '' }}">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Company Overview</span>
                </a>
            @endcanViewModule

            @canViewModule('retention')
                <a href="{{ route('retention.index') }}" class="menu-item {{ Request::is('retention') || Request::is('retention/*') ? 'active' : '' }}">
                    <i class="bx bx-refresh"></i>
                    <span class="menu-text">Retention Management</span>
                </a>
            @endcanViewModule
        @endhasrole

        {{-- MAIN MENU (NON-RESTRICTED ROLES) --}}
        @unlessrole([Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::RETENTION_OFFICER, Roles::QA, Roles::HR])
            @hasrole(Roles::EMPLOYEE)
                <!-- Employee only sees COMMUNICATION section below -->
            @elsehasrole(Roles::RAVENS_CLOSER)
                <!-- Ravens Closer sees same as Employee - attendance and chat only -->
            @elsehasrole(Roles::QA)
                <!-- QA only sees QA Review and COMMUNICATION section below -->
            @elsehasrole(Roles::HR)
                <!-- HR only sees Dock, Attendance, and Public Holidays -->
            @else
                @canViewModule('dashboard')
                    <a href="{{ route('dashboard') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
                        <i class="bx bx-home-circle"></i>
                        <span class="menu-text">Company Overview</span>
                    </a>
                @endcanViewModule

                @canViewModule('sales')
                    <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'misDropdown')">
                        <i class="bx bx-briefcase-alt"></i>
                        <span class="menu-text">Sales Operations</span>
                        <i class="bx bx-chevron-down dropdown-icon"></i>
                    </a>

                    <div class="menu-dropdown" id="misDropdown">
                        @hasanyrole([Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])
                            @canViewModule('leads-peregrine')
                                <a href="{{ route('leads.peregrine') }}" class="dropdown-item {{ Request::is('leads/peregrine*') ? 'active' : '' }}">
                                    <i class="bx bx-user-voice"></i>
                                    <span class="menu-text">Peregrine Leads</span>
                                </a>
                            @endcanViewModule

                            @canViewModule('leads')
                                <a href="{{ route('leads.index') }}" class="dropdown-item {{ Request::is('leads') && !Request::is('leads/peregrine*') && !Request::is('sales*') ? 'active' : '' }}">
                                    <i class="bx bx-briefcase"></i>
                                    <span class="menu-text">Raven Leads</span>
                                </a>
                            @endcanViewModule
                        @endhasanyrole

                        @hasanyrole([Roles::QA, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])
                            @canViewModule('qa-review')
                                <a href="{{ route('qa.review') }}" class="dropdown-item {{ Request::is('qa*') ? 'active' : '' }}">
                                    <i class="bx bx-check-circle"></i>
                                    <span class="menu-text">QA Review</span>
                                </a>
                            @endcanViewModule
                        @endhasanyrole

                        @canViewModule('sales')
                            <a href="{{ route('sales.index') }}" class="dropdown-item {{ Request::is('sales*') ? 'active' : '' }}">
                                <i class="bx bx-dollar-circle"></i>
                                <span class="menu-text">Sales Records</span>
                            </a>
                        @endcanViewModule

                        @canViewModule('issuance')
                            <a href="{{ route('issuance.index') }}" class="dropdown-item {{ Request::is('issuance*') ? 'active' : '' }}">
                                <i class="bx bx-send"></i>
                                <span class="menu-text">Policy Submission</span>
                            </a>
                        @endcanViewModule

                        @hasanyrole([Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])
                            @canViewModule('bank-verification')
                                <a href="{{ route('bank-verification.index') }}" class="dropdown-item {{ Request::is('bank-verification*') ? 'active' : '' }}">
                                    <i class="bx bx-check-shield"></i>
                                    <span class="menu-text">Bank Verification</span>
                                </a>
                            @endcanViewModule

                            @canViewModule('revenue-analytics')
                                <a href="{{ route('revenue-analytics.index') }}" class="dropdown-item {{ Request::is('revenue-analytics*') ? 'active' : '' }}">
                                    <i class="bx bx-line-chart"></i>
                                    <span class="menu-text">Revenue Analytics</span>
                                </a>
                            @endcanViewModule

                            @canViewModule('live-analytics')
                                <a href="{{ route('analytics.live') }}" class="dropdown-item {{ Request::is('analytics/live*') ? 'active' : '' }}">
                                    <i class="bx bx-line-chart"></i>
                                    <span class="menu-text">Live Analytics</span>
                                </a>
                            @endcanViewModule
                        @endhasanyrole
                    </div>
                @endcanViewModule

                @canViewModule('retention')
                    <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'retentionDropdown')">
                        <i class="bx bx-refresh"></i>
                        <span class="menu-text">Retention & Chargebacks</span>
                        <i class="bx bx-chevron-down dropdown-icon"></i>
                    </a>

                    <div class="menu-dropdown" id="retentionDropdown">
                        <a href="{{ route('retention.dashboard') }}" class="dropdown-item {{ Request::is('retention-dashboard*') ? 'active' : '' }}">
                            <i class="bx bx-tachometer"></i>
                            <span class="menu-text">Retention Dashboard</span>
                        </a>

                        <a href="{{ route('retention.index') }}" class="dropdown-item {{ Request::is('retention') && !Request::is('retention-dashboard*') ? 'active' : '' }}">
                            <i class="bx bx-user-check"></i>
                            <span class="menu-text">Manage Retention</span>
                        </a>

                        <a href="{{ route('chargebacks.index') }}" class="dropdown-item {{ Request::is('chargebacks*') ? 'active' : '' }}">
                            <i class="bx bx-error-circle"></i>
                            <span class="menu-text">Chargebacks</span>
                        </a>
                    </div>
                @endcanViewModule
            @endhasrole
        @endunlessrole

        {{-- PEREGRINE SECTION --}}
        @hasanyrole([Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::MANAGER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])
            @canViewModule('peregrine')
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'peregrineDropdown')">
                    <i class="bx bx-shield-alt"></i>
                    <span class="menu-text">Peregrine Operations</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="peregrineDropdown">
                    @hasanyrole([Roles::VERIFIER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])
                        @unlessrole(Roles::PEREGRINE_VALIDATOR)
                            @canViewModule('peregrine-dashboard')
                                <a href="{{ route('verifier.dashboard') }}" class="dropdown-item {{ Request::is('verifier/dashboard') ? 'active' : '' }}">
                                    <i class="bx bx-shield-alt"></i>
                                    <span class="menu-text">Peregrine Dashboard</span>
                                </a>
                            @endcanViewModule

                            @canViewModule('peregrine-verifier')
                                <a href="{{ route('verifier.create.team', 'peregrine') }}" class="dropdown-item {{ Request::is('verifier*create*') ? 'active' : '' }}">
                                    <i class="bx bx-edit-alt"></i>
                                    <span class="menu-text">Verifier Form</span>
                                </a>
                            @endcanViewModule
                        @endunlessrole
                    @endhasanyrole

                    @hasanyrole([Roles::PEREGRINE_CLOSER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])
                        @canViewModule('peregrine-closers')
                            <a href="{{ route('peregrine.closers.index') }}" class="dropdown-item {{ Request::is('peregrine/closers*') ? 'active' : '' }}">
                                <i class="bx bx-shield-alt"></i>
                                <span class="menu-text">Peregrine Closers</span>
                            </a>
                        @endcanViewModule
                    @endhasanyrole

                    @hasanyrole([Roles::PEREGRINE_VALIDATOR, Roles::MANAGER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])
                        @canViewModule('peregrine-validation')
                            <a href="{{ route('validator.index') }}" class="dropdown-item {{ Request::is('validator*') ? 'active' : '' }}">
                                <i class="bx bx-check-shield"></i>
                                <span class="menu-text">Validation Dashboard</span>
                            </a>
                        @endcanViewModule
                    @endhasanyrole
                </div>
            @endcanViewModule
        @endhasanyrole

        {{-- RAVENS SECTION --}}
        @hasanyrole([Roles::RAVENS_CLOSER, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])
            @canViewModule('ravens')
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'ravensDropdown')">
                    <i class="bx bx-phone-call"></i>
                    <span class="menu-text">Ravens Operations</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="ravensDropdown">
                    @canViewModule('ravens-dashboard')
                        <a href="{{ route('ravens.dashboard') }}" class="dropdown-item {{ Request::is('ravens/dashboard') ? 'active' : '' }}">
                            <i class="bx bx-phone-call"></i>
                            <span class="menu-text">Ravens Dashboard</span>
                        </a>
                    @endcanViewModule

                    @canViewModule('ravens-calling')
                        <a href="{{ route('ravens.calling') }}" class="dropdown-item {{ Request::is('ravens/calling*') ? 'active' : '' }}">
                            <i class="bx bx-phone"></i>
                            <span class="menu-text">Ravens Calling</span>
                        </a>
                    @endcanViewModule

                    @canViewModule('ravens-bad-leads')
                        <a href="{{ route('ravens.bad-leads') }}" class="dropdown-item {{ Request::is('ravens/bad-leads*') ? 'active' : '' }}">
                            <i class="bx bx-x-circle"></i>
                            <span class="menu-text">Bad Leads</span>
                        </a>
                    @endcanViewModule

                    @canViewModule('ravens-followups')
                        <a href="{{ route('followup.my-followups') }}" class="dropdown-item {{ Request::is('followup*') ? 'active' : '' }}">
                            <i class="bx bx-task"></i>
                            <span class="menu-text">My Followup & Bank Verification</span>
                        </a>
                    @endcanViewModule
                </div>
            @endcanViewModule
        @endhasanyrole

        @hasanyrole([Roles::QA, Roles::HR, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])
            <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'hrOpsDropdown')">
                <i class="bx bx-user-check"></i>
                <span class="menu-text">HR Operations</span>
                <i class="bx bx-chevron-down dropdown-icon"></i>
            </a>

            <div class="menu-dropdown" id="hrOpsDropdown">
                @hasanyrole([Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::HR, Roles::CEO])
                    <a href="{{ route('employee.ems') }}" class="dropdown-item {{ Request::is('ems*') ? 'active' : '' }}">
                        <i class="bx bx-id-card"></i>
                        <span class="menu-text">E.M.S</span>
                    </a>

                    <a href="{{ route('attendance.index') }}" class="dropdown-item {{ Request::is('attendance*') ? 'active' : '' }}">
                        <i class="bx bx-time-five"></i>
                        <span class="menu-text">Attendance</span>
                    </a>
                @endhasanyrole

                @hasanyrole([Roles::QA, Roles::HR, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])
                    <a href="{{ route('dock.index') }}" class="dropdown-item {{ Request::is('dock*') ? 'active' : '' }}">
                        <i class="bx bx-dock-top"></i>
                        <span class="menu-text">Dock Management</span>
                    </a>
                @endhasanyrole

                @hasanyrole([Roles::HR, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])
                    <a href="{{ route('admin.public-holidays.index') }}" class="dropdown-item {{ Request::is('admin/public-holidays*') ? 'active' : '' }}">
                        <i class="bx bx-calendar"></i>
                        <span class="menu-text">Public Holidays</span>
                    </a>
                @endhasanyrole
            </div>
        @endhasanyrole

        {{-- EPMS - Project Management (CEO, Super Admin Only) --}}
        @hasanyrole([Roles::SUPER_ADMIN, Roles::CEO])
            <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'epmsDropdown')">
                <i class="bx bx-briefcase-alt"></i>
                <span class="menu-text">Project Management</span>
                <i class="bx bx-chevron-down dropdown-icon"></i>
            </a>

            <div class="menu-dropdown" id="epmsDropdown">
                <a href="{{ route('epms.index') }}" class="dropdown-item {{ Request::is('epms') && !Request::is('epms/*') ? 'active' : '' }}">
                    <i class="bx bx-list-ul"></i>
                    <span class="menu-text">All Projects</span>
                </a>
                <a href="{{ route('epms.create') }}" class="dropdown-item {{ Request::is('epms/create') ? 'active' : '' }}">
                    <i class="bx bx-plus-circle"></i>
                    <span class="menu-text">New Project</span>
                </a>
            </div>
        @endhasanyrole

        {{-- ADMIN SECTION --}}
        @unlessrole([Roles::VERIFIER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::EMPLOYEE, Roles::RAVENS_CLOSER])
            @hasanyrole([Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO, Roles::MANAGER])
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'partnersDropdown')">
                    <i class="bx bx-group"></i>
                    <span class="menu-text">Partner Management</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="partnersDropdown">
                    <a href="{{ route('agents.index') }}" class="dropdown-item {{ Request::is('agents*') ? 'active' : '' }}">
                        <i class="bx bx-user-circle"></i>
                        <span class="menu-text">Partners</span>
                    </a>

                    <a href="{{ route('admin.insurance-carriers.index') }}" class="dropdown-item {{ Request::is('admin/insurance-carriers*') ? 'active' : '' }}">
                        <i class="bx bx-buildings"></i>
                        <span class="menu-text">Insurance Cluster</span>
                    </a>
                </div>


            @endhasanyrole

            {{-- FINANCE SECTION --}}
            @hasanyrole([Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])
                <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'financeDropdown')">
                    <i class="bx bx-dollar-circle"></i>
                    <span class="menu-text">Finance & Accounts</span>
                    <i class="bx bx-chevron-down dropdown-icon"></i>
                </a>

                <div class="menu-dropdown" id="financeDropdown">
                    <a href="{{ route('chart-of-accounts.index') }}" class="dropdown-item {{ Request::is('chart-of-accounts*') ? 'active' : '' }}">
                        <i class="bx bx-list-ul"></i>
                        <span class="menu-text">Chart of Accounts</span>
                    </a>

                    <a href="{{ route('ledger.index') }}" class="dropdown-item {{ Request::is('ledger*') ? 'active' : '' }}">
                        <i class="bx bx-book-open"></i>
                        <span class="menu-text">General Ledger</span>
                    </a>

                    @hasanyrole([Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])
                        <a href="{{ route('petty-cash.index') }}" class="dropdown-item {{ Request::is('petty-cash*') ? 'active' : '' }}">
                            <i class="bx bx-wallet"></i>
                            <span class="menu-text">Petty Cash</span>
                        </a>
                    @endhasanyrole

                    <a href="{{ route('payroll.index') }}" class="dropdown-item {{ Request::is('payroll*') ? 'active' : '' }}">
                        <i class="bx bx-credit-card-alt"></i>
                        <span class="menu-text">Payroll</span>
                    </a>

                    <a href="{{ route('pabs.tickets.index') }}" class="dropdown-item {{ Request::is('pabs/tickets*') ? 'active' : '' }}">
                        <i class="bx bx-message-square-error"></i>
                        <span class="menu-text">PABS Tickets</span>
                    </a>
                </div>
            @endhasanyrole
        @endunlessrole

        {{-- USERS MANAGEMENT SECTION --}}
        @hasanyrole([Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO])
            <a href="{{ route('users.index') }}" class="menu-item {{ Request::is('users*') ? 'active' : '' }}">
                <i class="bx bx-user-circle"></i>
                <span class="menu-text">Users MGMT</span>
            </a>
        @endhasanyrole

        {{-- PERSONAL RECORDS SECTION (ALL USERS) --}}
        <a href="#" class="menu-item menu-dropdown-toggle" onclick="toggleDropdown(event, 'myRecordsDropdown')">
            <i class="bx bx-user"></i>
            <span class="menu-text">My Records</span>
            <i class="bx bx-chevron-down dropdown-icon"></i>
        </a>

        <div class="menu-dropdown" id="myRecordsDropdown">
            <a href="{{ route('attendance.dashboard') }}" class="dropdown-item {{ Request::is('attendance/dashboard') ? 'active' : '' }}">
                <i class="bx bx-time-five"></i>
                <span class="menu-text">My Attendance</span>
            </a>

            <a href="{{ route('my-dock-records') }}" class="dropdown-item {{ Request::is('my-dock-records*') ? 'active' : '' }}">
                <i class="bx bx-dock-top"></i>
                <span class="menu-text">My Dock Records</span>
            </a>
        </div>
    </nav>

    {{-- Settings pinned to bottom --}}
    @hasanyrole([Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO])
        <div class="sidebar-bottom">
            <a href="{{ route('settings.hub') }}" class="sidebar-bottom-item {{ Request::is('settings*') || Request::is('admin/dupe-checker*') || Request::is('admin/account-switching-log*') ? 'active' : '' }}">
                <i class="bx bx-cog"></i>
                <span class="sidebar-bottom-text">Settings</span>
            </a>
        </div>
    @endhasanyrole
</div>

<style>
    /* ===== MODERN FLAT SIDEBAR DESIGN ===== */

    /* Sidebar Profile Section */
    .sidebar-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 16px;
        border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), transparent);
        position: relative;
    }

    .sidebar-avatar-wrapper {
        cursor: pointer;
        flex-shrink: 0;
        position: relative;
    }

    .sidebar-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 2.5px solid var(--gold, #d4af37);
        object-fit: cover;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
    }

    .sidebar-avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        background: linear-gradient(135deg, var(--gold, #d4af37), #b8922e);
        color: #fff;
    }

    .sidebar-avatar-wrapper:hover .sidebar-avatar {
        transform: scale(1.05);
        box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
        border-color: var(--gold-dark, #b8922e);
    }

    .sidebar-profile-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        overflow: hidden;
        min-width: 0;
    }

    .sidebar-profile-name {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--text-primary, var(--bs-surface-700));
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .sidebar-profile-role {
        font-size: 0.72rem;
        color: var(--gold, #d4af37);
        font-weight: 600;
        letter-spacing: 0.3px;
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
        padding: 14px 8px;
    }

    /* Profile Dropdown */
    .sidebar-profile-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 12px;
        width: 240px;
        background: var(--bg-panel, #ffffff);
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12), 0 2px 10px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(212, 175, 55, 0.15);
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
        padding: 14px 16px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.06), transparent);
    }

    .profile-dropdown-avatar img,
    .profile-dropdown-avatar .avatar-initial {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid var(--gold, #d4af37);
        object-fit: cover;
    }

    .profile-dropdown-avatar .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
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
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-primary, #111827);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-dropdown-email {
        font-size: 0.72rem;
        color: var(--text-muted, #6b7280);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-dropdown-divider {
        height: 1px;
        background: rgba(212, 175, 55, 0.1);
        margin: 0;
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        font-size: 0.88rem;
        font-weight: 500;
        color: var(--text-secondary, #374151) !important;
        text-decoration: none !important;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .profile-dropdown-item i {
        font-size: 18px;
        color: var(--text-muted, #6b7280);
        transition: all 0.2s ease;
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
        padding: 12px 0;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
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
        color: var(--text-secondary, var(--bs-surface-500)) !important;
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
        color: var(--gold, var(--bs-gold)) !important;
        transform: translateX(3px);
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.15);
    }

    .menu-item:hover i {
        transform: scale(1.08);
        color: var(--gold, var(--bs-gold));
    }

    .menu-item.active {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.18) 0%, rgba(212, 175, 55, 0.06) 100%) !important;
        color: var(--gold, var(--bs-gold)) !important;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(212, 175, 55, 0.2);
    }

    .menu-item.active i {
        color: var(--gold, var(--bs-gold));
    }

    .menu-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 60%;
        background: var(--gold, var(--bs-gold));
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
        color: var(--text-secondary, var(--bs-surface-500)) !important;
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
        color: var(--gold-dark, var(--bs-gold-dark)) !important;
        transform: translateX(3px);
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.15);
    }

    .dropdown-item:hover i {
        opacity: 1;
        transform: scale(1.08);
        color: var(--gold, var(--bs-gold));
    }

    .dropdown-item.active {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.22) 0%, rgba(212, 175, 55, 0.08) 100%) !important;
        color: var(--gold-dark, var(--bs-gold-dark)) !important;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(212, 175, 55, 0.25);
    }

    .dropdown-item.active i {
        color: var(--gold, var(--bs-gold));
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
        background: var(--gold, var(--bs-gold));
        border-radius: 4px;
    }

    /* Sidebar Toggle Button */
    .sidebar-toggle {
        position: absolute;
        top: 30px;
        right: -13px;
        width: 26px;
        height: 26px;
        background: linear-gradient(135deg, var(--gold, var(--bs-gold)), var(--gold-dark, var(--bs-gold-dark))) !important;
        color: var(--bs-white) !important;
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
        background: linear-gradient(135deg, var(--gold-dark, var(--bs-gold-dark)), var(--gold, var(--bs-gold))) !important;
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
    @@hasrole + @@hasrole .menu-item:first-child {
        margin-top: 8px;
    }

    /* Sidebar Bottom - Pinned Settings */
    .sidebar-bottom {
        padding: 8px 12px;
        border-top: 1px solid rgba(212, 175, 55, 0.1);
        flex-shrink: 0;
    }

    .sidebar-bottom-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-secondary, var(--bs-surface-500)) !important;
        text-decoration: none !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .sidebar-bottom-item i {
        font-size: 21px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .sidebar-bottom-item:hover {
        background: rgba(212, 175, 55, 0.1);
        color: var(--gold, var(--bs-gold)) !important;
    }

    .sidebar-bottom-item:hover i {
        color: var(--gold, var(--bs-gold));
        transform: rotate(90deg);
    }

    .sidebar-bottom-item.active {
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.18) 0%, rgba(212, 175, 55, 0.06) 100%);
        color: var(--gold, var(--bs-gold)) !important;
        font-weight: 600;
    }

    .sidebar-bottom-item.active i {
        color: var(--gold, var(--bs-gold));
    }

    #sidebar.collapsed .sidebar-bottom-item {
        justify-content: center;
        padding: 12px;
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
