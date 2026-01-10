<!-- ========== Left Sidebar Start ========== -->
<div id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <img src="{{ asset('images/icon.png') }}" alt="Taurus" onerror="this.style.display='none'">
        <span class="logo-text">TAURUS MIS</span>
    </div>

    <!-- Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bx bx-chevron-left"></i>
    </button>

    <!-- Menu -->
    <nav class="sidebar-menu">
        @hasrole('Retention Officer')
            <div class="menu-label">RETENTION</div>

            <a href="{{ route('retention.dashboard') }}" class="menu-item {{ Request::is('retention-dashboard*') ? 'active' : '' }}">
                <i class="bx bx-home-circle"></i>
                <span class="menu-text">My Dashboard</span>
            </a>

            <a href="{{ route('retention.index') }}" class="menu-item {{ Request::is('retention') || Request::is('retention/*') ? 'active' : '' }}">
                <i class="bx bx-refresh"></i>
                <span class="menu-text">Retention Management</span>
            </a>
        @endhasrole

        @unlessrole('Verifier|Paraguins Closer|Paraguins Validator|Employee|Ravens Closer|Retention Officer|QA|HR')
            <div class="menu-label">MAIN</div>

            @hasrole('Employee')
                <!-- Employee only sees COMMUNICATION section below -->
            @elsehasrole('Ravens Closer')
                <!-- Ravens Closer sees same as Employee - attendance and chat only -->
            @elsehasrole('QA')
                <!-- QA only sees QA Review and COMMUNICATION section below -->
            @elsehasrole('HR')
                <!-- HR only sees Dock, Attendance, and Public Holidays -->
            @else
                <a href="{{ route('root') }}" class="menu-item {{ Request::is('/') ? 'active' : '' }}">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Dashboard</span>
                </a>

                <div class="menu-label">CRM</div>

                <a href="{{ route('leads.index') }}" class="menu-item {{ Request::is('leads*') && !Request::is('sales*') ? 'active' : '' }}">
                    <i class="bx bx-user-plus"></i>
                    <span class="menu-text">All Leads</span>
                </a>

                <a href="{{ route('sales.index') }}" class="menu-item {{ Request::is('sales*') ? 'active' : '' }}">
                    <i class="bx bx-dollar-circle"></i>
                    <span class="menu-text">Sales</span>
                </a>

                <a href="{{ route('chargebacks.index') }}" class="menu-item {{ Request::is('chargebacks*') ? 'active' : '' }}">
                    <i class="bx bx-error"></i>
                    <span class="menu-text">Chargebacks</span>
                </a>

                <div class="menu-label">RETENTION</div>

                <a href="{{ route('retention.dashboard') }}" class="menu-item {{ Request::is('retention-dashboard*') ? 'active' : '' }}">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">Retention Dashboard</span>
                </a>

                <a href="{{ route('retention.index') }}" class="menu-item {{ Request::is('retention') && !Request::is('retention-dashboard*') ? 'active' : '' }}">
                    <i class="bx bx-refresh"></i>
                    <span class="menu-text">Retention Management</span>
                </a>
            @endhasrole
        @endunlessrole

        @hasrole('Verifier|Super Admin')
            @unlessrole('Paraguins Validator')
                <div class="menu-label">PARAGUINS</div>

                <a href="{{ route('verifier.dashboard') }}" class="menu-item {{ Request::is('verifier/dashboard') ? 'active' : '' }}">
                    <i class="bx bx-home-circle"></i>
                    <span class="menu-text">My Dashboard</span>
                </a>

                <a href="{{ route('verifier.create.team', 'paraguins') }}" class="menu-item {{ Request::is('verifier*create*') ? 'active' : '' }}">
                    <i class="bx bx-check-shield"></i>
                    <span class="menu-text">Verifier Form</span>
                </a>
            @endunlessrole
        @endhasrole

        @hasrole('Paraguins Closer|Super Admin')
            @unlessrole('Verifier|Paraguins Validator')
                <div class="menu-label">PARAGUINS</div>
            @endunlessrole

            <a href="{{ route('paraguins.closers.index') }}" class="menu-item {{ Request::is('paraguins/closers*') ? 'active' : '' }}">
                <i class="bx bx-edit"></i>
                <span class="menu-text">Paraguins Leads</span>
            </a>
        @endhasrole

        @hasrole('Paraguins Validator|Manager|Super Admin')
            <div class="menu-label">VALIDATOR</div>

            <a href="{{ route('validator.index') }}" class="menu-item {{ Request::is('validator*') ? 'active' : '' }}">
                <i class="bx bx-check-shield"></i>
                <span class="menu-text">Validation Dashboard</span>
            </a>
        @endhasrole

        @hasrole('Ravens Closer|Super Admin')
            <div class="menu-label">RAVENS</div>

            <a href="{{ route('ravens.dashboard') }}" class="menu-item {{ Request::is('ravens/dashboard') ? 'active' : '' }}">
                <i class="bx bx-home-circle"></i>
                <span class="menu-text">Ravens Dashboard</span>
            </a>

            <a href="{{ route('ravens.calling') }}" class="menu-item {{ Request::is('ravens/calling*') ? 'active' : '' }}">
                <i class="bx bx-phone"></i>
                <span class="menu-text">Calling System</span>
            </a>
        @endhasrole

        @hasrole('QA|HR|Super Admin|Manager')
            <div class="menu-label">QA / HR</div>

            @hasrole('QA|Super Admin')
            <a href="{{ route('qa.review') }}" class="menu-item {{ Request::is('qa*') ? 'active' : '' }}">
                <i class="bx bx-check-double"></i>
                <span class="menu-text">QA Review</span>
            </a>
            @endhasrole

            @hasrole('QA|HR|Super Admin|Manager')
            <a href="{{ route('dock.index') }}" class="menu-item {{ Request::is('dock*') ? 'active' : '' }}">
                <i class="mdi mdi-cash-minus"></i>
                <span class="menu-text">Dock Section</span>
            </a>
            @endhasrole

            @hasrole('HR|Super Admin')
            <a href="{{ route('admin.public-holidays.index') }}" class="menu-item {{ Request::is('admin/public-holidays*') ? 'active' : '' }}">
                <i class="bx bx-calendar-star"></i>
                <span class="menu-text">Public Holidays</span>
            </a>
            @endhasrole
        @endhasrole

        @unlessrole('Verifier|Paraguins Closer|Paraguins Validator|Employee|Ravens Closer')
            @hasrole('Super Admin')
                <div class="menu-label">ADMIN</div>

                <a href="{{ route('agents.index') }}" class="menu-item {{ Request::is('agents*') ? 'active' : '' }}">
                    <i class="bx bx-user-circle"></i>
                    <span class="menu-text">Partners</span>
                </a>

                <a href="{{ route('admin.insurance-carriers.index') }}" class="menu-item {{ Request::is('admin/insurance-carriers*') ? 'active' : '' }}">
                    <i class="bx bx-shield-alt-2"></i>
                    <span class="menu-text">Insurance Carriers</span>
                </a>

                <a href="{{ route('users.index') }}" class="menu-item {{ Request::is('users*') ? 'active' : '' }}">
                    <i class="bx bx-group"></i>
                    <span class="menu-text">Users</span>
                </a>
            @endhasrole

            @hasrole('Super Admin|Manager')
                <a href="{{ route('ledger.index') }}" class="menu-item {{ Request::is('ledger*') ? 'active' : '' }}">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    <span class="menu-text">Ledger</span>
                </a>
            @endhasrole

            @hasrole('Super Admin|Manager|HR')
                <div class="menu-label">OPERATIONS</div>

                <a href="{{ route('attendance.index') }}" class="menu-item {{ Request::is('attendance*') ? 'active' : '' }}">
                    <i class="bx bx-time-five"></i>
                    <span class="menu-text">Attendance</span>
                </a>
            @endhasrole

            @hasrole('Super Admin|Manager')
                <a href="{{ route('salary.index') }}" class="menu-item {{ Request::is('salary*') ? 'active' : '' }}">
                    <i class="bx bx-dollar-circle"></i>
                    <span class="menu-text">Salary</span>
                </a>

                <div class="menu-label">SETTINGS</div>

                <a href="{{ route('settings.index') }}" class="menu-item {{ Request::is('settings') ? 'active' : '' }}">
                    <i class="bx bx-cog"></i>
                    <span class="menu-text">Settings</span>
                </a>
            @endhasrole
        @endunlessrole

        <div class="menu-label">COMMUNICATION</div>

        <a href="{{ route('attendance.dashboard') }}" class="menu-item {{ Request::is('attendance/dashboard') ? 'active' : '' }}">
            <i class="bx bx-calendar-check"></i>
            <span class="menu-text">My Attendance</span>
        </a>

        <a href="{{ route('my-dock-records') }}" class="menu-item {{ Request::is('my-dock-records*') ? 'active' : '' }}">
            <i class="mdi mdi-cash-minus"></i>
            <span class="menu-text">My Dock Records</span>
        </a>
    </nav>
</div>
<!-- Left Sidebar End -->
