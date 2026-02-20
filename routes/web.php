<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ChargebackController;
use App\Http\Controllers\Admin\EPMSProjectController;
use App\Http\Controllers\Admin\InsuranceCarrierController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\ChartOfAccountController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RetentionController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\PublicHolidayController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RavensDashboardController;
use App\Http\Controllers\Admin\RetentionDashboardController;
use App\Http\Controllers\Admin\DupeCheckerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamDashboardController;
use App\Http\Controllers\AgentDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatNotificationController;
use App\Http\Controllers\CommunityAnnouncementController;
use App\Http\Controllers\VerifierController;
use App\Http\Controllers\PeregrineController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Support\Roles;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication routes (without registration)
Auth::routes(['register' => false]);

// Partner Authentication Routes
Route::prefix('partner')->group(function () {
    // Login routes (accessible always, but prevent logged-in users from accessing)
    Route::middleware('prevent.user')->group(function () {
        Route::get('login', [App\Http\Controllers\Partner\PartnerAuthController::class, 'showLoginForm'])->name('partner.login');
        Route::post('login', [App\Http\Controllers\Partner\PartnerAuthController::class, 'login'])->name('partner.login.submit');
    });

    // Protected partner routes (only partners can access)
    Route::middleware(['partner.auth', 'prevent.user'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Partner\PartnerDashboardController::class, 'index'])->name('partner.dashboard');
        Route::post('logout', [App\Http\Controllers\Partner\PartnerAuthController::class, 'logout'])->name('partner.logout');
    });
});

// Logout GET route to prevent page expired error
Route::get('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout.get');

// Authenticated routes - Dashboard with role-based redirects
// Prevent partners from accessing user/employee areas
Route::group(['middleware' => ['auth', 'prevent.partner', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::QA, Roles::RETENTION_OFFICER, Roles::COORDINATOR, Roles::HR)]], function () {
    // Smart router - redirects each user to their appropriate landing page
    Route::get('/', [DashboardController::class, 'root'])->name('root');
    
    // Executive Dashboard (Company Overview) - has its own URL with permission check
    Route::get('/dashboard', [DashboardController::class, 'executiveDashboard'])->name('dashboard')->middleware('role.permission:dashboard,view');
    
    // API endpoint to fetch fresh KPI data for live updates
    Route::get('/dashboard/kpi-data', [DashboardController::class, 'getKpiData'])->name('dashboard.kpi-data');
});

// Team Dashboards - restricted access
Route::group(['middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE)]], function () {
    Route::get('/team/peregrine', [TeamDashboardController::class, 'peregrineTeam'])->name('team.peregrine');
    Route::get('/team/ravens', [TeamDashboardController::class, 'ravensTeam'])->name('team.ravens');
    Route::get('/closer/{userId}/details', [TeamDashboardController::class, 'closerDetails'])->name('closer.details');
});

// Employee & Ravens Closer Routes - Only Attendance and Chat access
Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['auth', Roles::middleware(Roles::EMPLOYEE, Roles::RAVENS_CLOSER)]], function () {
    // Redirect to attendance dashboard
    Route::get('/dashboard', function() {
        return redirect()->route('attendance.dashboard');
    })->name('dashboard');
});

// HR Routes - Limited access to Dock, Attendance, and Public Holidays only
Route::group(['prefix' => 'hr', 'as' => 'hr.', 'middleware' => ['auth', Roles::middleware(Roles::HR)]], function () {
    // HR Dashboard - redirect to attendance
    Route::get('/dashboard', function() {
        return redirect()->route('attendance.index');
    })->name('dashboard');
});

// Users Management (CEO & Super Admin & Co-ordinator)
Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR)]], function () {
    Route::get('/', [UserController::class, 'index'])->name('index')->middleware('role.permission:users,view');
    Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('role.permission:users,edit');
    Route::post('/store', [UserController::class, 'store'])->name('store')->middleware('role.permission:users,edit');
    Route::get('/show/{id}', [UserController::class, 'show'])->name('show')->middleware('role.permission:users,view');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit')->middleware('role.permission:users,edit');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('update')->middleware('role.permission:users,edit');
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete')->middleware('role.permission:users,full');
    
    // Admin password reset (Super Admin & Manager only)
    Route::get('/{id}/reset-password', [UserController::class, 'resetPasswordForm'])->name('reset-password-form')->middleware('role.permission:users,edit');
    Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password')->middleware('role.permission:users,edit');
    
    // Avatar upload
    Route::get('/{id}/upload-avatar', [UserController::class, 'uploadAvatarForm'])->name('upload-avatar-form')->middleware('role.permission:users,edit');
    Route::post('/{id}/upload-avatar', [UserController::class, 'uploadAvatar'])->name('upload-avatar')->middleware('role.permission:users,edit');
    
    // Update plain password via AJAX
    Route::post('/{id}/update-password', [UserController::class, 'updatePassword'])->name('update-password')->middleware('role.permission:users,edit');
});

// Employee Management Sheet (E.M.S) - HR has view-only, others have full access
Route::group(['prefix' => 'ems', 'as' => 'employee.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::HR, Roles::COORDINATOR, Roles::MANAGER)]], function () {
    // View and export - accessible to all roles in this group
    Route::get('/', [EmployeeController::class, 'index'])->name('ems')->middleware('role.permission:ems,view');
    Route::get('/export', [EmployeeController::class, 'export'])->name('export')->middleware('role.permission:ems,view');
    
    // Modification routes - accessible to all roles in this group (including HR)
    Route::post('/store', [EmployeeController::class, 'store'])->name('store')->middleware('role.permission:ems,edit');
    Route::post('/import', [EmployeeController::class, 'import'])->name('import')->middleware('role.permission:ems,edit');
    Route::post('/update/{employee}', [EmployeeController::class, 'update'])->name('update')->middleware('role.permission:ems,edit');
    Route::delete('/terminate/{id}', [EmployeeController::class, 'terminate'])->name('terminate')->middleware('role.permission:ems,full');
    Route::post('/restore/{id}', [EmployeeController::class, 'restore'])->name('restore')->middleware('role.permission:ems,edit');
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy')->middleware('role.permission:ems,full');
});

// Dupe Checker (CEO, Super Admin and Co-ordinator)
Route::group(['prefix' => 'admin/dupe-checker', 'as' => 'admin.dupe-checker.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR)]], function () {
    Route::get('/', [DupeCheckerController::class, 'index'])->name('index')->middleware('role.permission:duplicate-checker,view');
    Route::post('/self-check', [DupeCheckerController::class, 'selfCheck'])->name('self-check')->middleware('role.permission:duplicate-checker,edit');
    Route::post('/file-comparison', [DupeCheckerController::class, 'fileComparison'])->name('file-comparison')->middleware('role.permission:duplicate-checker,edit');
    Route::post('/run-deduplication', [DupeCheckerController::class, 'runDeduplication'])->name('run-deduplication')->middleware('role.permission:duplicate-checker,full');
});

// Account Switching Log & Audit Logs (CEO, Super Admin and Co-ordinator)
Route::group(['prefix' => 'admin', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR)]], function () {
    Route::get('/account-switching-log', [AuditLogController::class, 'accountSwitchingLog'])->name('admin.account-switching-log')->middleware('role.permission:account-switch-log,view');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index')->middleware('role.permission:account-switch-log,view');
    Route::get('/audit-logs/export/csv', [AuditLogController::class, 'export'])->name('admin.audit-logs.export')->middleware('role.permission:account-switch-log,view');
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('admin.audit-logs.show')->middleware('role.permission:account-switch-log,view');
});

// Communities API routes (used by chat system)
Route::middleware('auth')->group(function () {
    Route::post('/api/communities', [\App\Http\Controllers\Admin\CommunityController::class, 'store']); // Create community via API
    Route::put('/api/communities/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'update']); // Update community
    Route::delete('/api/communities/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'destroy']); // Delete community
    Route::get('/api/communities/{community}/members', [\App\Http\Controllers\Admin\CommunityController::class, 'getMembers']); // Get members
    Route::post('/api/communities/{community}/members', [\App\Http\Controllers\Admin\CommunityController::class, 'addMember']); // Add member
    Route::delete('/api/communities/{community}/members/{user}', [\App\Http\Controllers\Admin\CommunityController::class, 'removeMember']); // Remove member
    Route::patch('/api/communities/{community}/members/{user}/toggle-post', [\App\Http\Controllers\Admin\CommunityController::class, 'toggleMemberPost']); // Toggle posting permission

});

// Agents Management - Redirects to Partners (agents are now managed as partners)
Route::group(['prefix' => 'agents', 'as' => 'agents.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', function() { return redirect()->route('admin.partners.index'); })->name('index');
    Route::get('/create', function() { return redirect()->route('admin.partners.create'); })->name('create');
    Route::post('/store', function() { return redirect()->route('admin.partners.index'); })->name('store');
    Route::get('/show/{id}', function($id) { return redirect()->route('admin.partners.show', $id); })->name('show');
    Route::get('/edit/{id}', function($id) { return redirect()->route('admin.partners.edit', $id); })->name('edit');
    Route::put('/update/{id}', function($id) { return redirect()->route('admin.partners.index'); })->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'destroy'])->name('delete');
});

// Partners Management (CEO & Super Admin & Manager & Co-ordinator)
Route::group(['prefix' => 'admin/partners', 'as' => 'admin.partners.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', [App\Http\Controllers\Admin\PartnerController::class, 'index'])->name('index')->middleware('role.permission:partners,view');
    Route::get('/create', [App\Http\Controllers\Admin\PartnerController::class, 'create'])->name('create')->middleware('role.permission:partners,edit');
    Route::post('/store', [App\Http\Controllers\Admin\PartnerController::class, 'store'])->name('store')->middleware('role.permission:partners,edit');
    Route::get('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'show'])->name('show')->middleware('role.permission:partners,view');
    Route::get('/{id}/edit', [App\Http\Controllers\Admin\PartnerController::class, 'edit'])->name('edit')->middleware('role.permission:partners,edit');
    Route::put('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'update'])->name('update')->middleware('role.permission:partners,edit');
    Route::delete('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'destroy'])->name('destroy')->middleware('role.permission:partners,full');
    Route::delete('/{partnerId}/carriers/{carrierId}', [App\Http\Controllers\Admin\PartnerController::class, 'removeCarrierAssignment'])->name('remove-carrier-assignment')->middleware('role.permission:partners,edit');
});

// Insurance Carriers Management (CEO & Super Admin & Manager & Co-ordinator)
Route::group(['prefix' => 'admin/insurance-carriers', 'as' => 'admin.insurance-carriers.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', [InsuranceCarrierController::class, 'index'])->name('index')->middleware('role.permission:carriers,view');
    Route::get('/create', [InsuranceCarrierController::class, 'create'])->name('create')->middleware('role.permission:carriers,edit');
    Route::post('/store', [InsuranceCarrierController::class, 'store'])->name('store')->middleware('role.permission:carriers,edit');
    Route::post('/{insuranceCarrier}/toggle-active', [InsuranceCarrierController::class, 'toggleActive'])->name('toggle-active')->middleware('role.permission:carriers,edit');
    Route::get('/{insuranceCarrier}', [InsuranceCarrierController::class, 'show'])->name('show')->middleware('role.permission:carriers,view');
    Route::get('/{insuranceCarrier}/edit', [InsuranceCarrierController::class, 'edit'])->name('edit')->middleware('role.permission:carriers,edit');
    Route::put('/{insuranceCarrier}', [InsuranceCarrierController::class, 'update'])->name('update')->middleware('role.permission:carriers,edit');
    Route::delete('/{insuranceCarrier}', [InsuranceCarrierController::class, 'destroy'])->name('destroy')->middleware('role.permission:carriers,full');
});

// Leads Management (Add/Import only - no actions)
Route::group(['prefix' => 'leads', 'as' => 'leads.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER)]], function () {
    Route::get('/', [LeadController::class, 'index'])->name('index')->middleware('role.permission:leads-peregrine,view');
    Route::get('/peregrine', [LeadController::class, 'peregrineLeads'])->name('peregrine')->middleware('role.permission:leads-peregrine,view');
    Route::get('/create', [LeadController::class, 'create'])->name('create')->middleware('role.permission:leads-peregrine,edit');
    Route::post('/store', [LeadController::class, 'store'])->name('store')->middleware('role.permission:leads-peregrine,edit');
    Route::post('/import', [LeadController::class, 'import'])->name('import')->middleware('role.permission:leads-peregrine,edit');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [LeadController::class, 'edit'])->name('edit')->middleware('role.permission:leads,edit');
    Route::put('/update/{id}', [LeadController::class, 'update'])->name('update')->middleware('role.permission:leads,edit');
    Route::delete('/delete/{id}', [LeadController::class, 'destroy'])->name('delete')->middleware('role.permission:leads,full');
    Route::post('/{id}/status', [LeadController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{id}/update-comment', [LeadController::class, 'updateComment'])->name('updateComment');
    Route::post('/{id}/unassign-partner', [LeadController::class, 'unassignPartner'])->name('unassignPartner');
});

// Sales Management (with actions and status management)
Route::group(['prefix' => 'sales', 'as' => 'sales.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::COORDINATOR, Roles::QA)]], function () {
    Route::get('/', [LeadController::class, 'sales'])->name('index')->middleware('role.permission:sales,view');
    Route::get('/pretty-print/{id}', [LeadController::class, 'prettyPrint'])->name('prettyPrint')->middleware('role.permission:sales,view');
    Route::post('/manual-entry', [LeadController::class, 'storeManualSale'])->name('storeManual')->middleware('role.permission:sales,edit');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show')->middleware('role.permission:sales,view');
    Route::get('/edit/{id}', [LeadController::class, 'edit'])->name('edit')->middleware('role.permission:sales,edit');
    Route::put('/update/{id}', [LeadController::class, 'update'])->name('update')->middleware('role.permission:sales,edit');
    Route::delete('/delete/{id}', [LeadController::class, 'destroy'])->name('delete')->middleware('role.permission:sales,full');
    Route::post('/{id}/status', [LeadController::class, 'updateStatus'])->name('updateStatus')->middleware('role.permission:sales,edit');
    Route::post('/{id}/update-field', [LeadController::class, 'updateSalesField'])->name('updateField')->middleware('role.permission:sales,edit');
    Route::post('/{id}/comment', [LeadController::class, 'updateComment'])->name('updateComment')->middleware('role.permission:sales,edit');
    Route::post('/carriers/{carrierId}/status', [LeadController::class, 'updateCarrierStatus'])->name('updateCarrierStatus')->middleware('role.permission:sales,edit');
    Route::post('/update-during-call', [LeadController::class, 'updateDuringCall'])->name('updateDuringCall')->middleware('role.permission:sales,edit');
    Route::post('/forward', [LeadController::class, 'forwardLead'])->name('forwardLead')->middleware('role.permission:sales,edit');
    Route::post('/{id}/qa-status', [LeadController::class, 'updateQaStatus'])->name('updateQaStatus')->middleware('role.permission:sales,edit');
    Route::post('/{id}/qa-status/reset', [LeadController::class, 'resetQaStatus'])->name('resetQaStatus')->middleware('role.permission:sales,edit');
    Route::post('/{id}/manager-status', [LeadController::class, 'updateManagerStatus'])->name('updateManagerStatus')->middleware('role.permission:sales,edit');
    Route::post('/{id}/manager-status/reset', [LeadController::class, 'resetManagerStatus'])->name('resetManagerStatus')->middleware('role.permission:sales,edit');
    Route::post('/{id}/update-manager-reason', [LeadController::class, 'updateManagerReason'])->name('updateManagerReason')->middleware('role.permission:sales,edit');
    Route::post('/{id}/retention-sale', [LeadController::class, 'markRetentionSale'])->name('markRetentionSale')->middleware('role.permission:sales,edit');
});

// Issuance Management Routes (with permission enforcement)
Route::group(['prefix' => 'issuance', 'as' => 'issuance.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', [LeadController::class, 'issuance'])->name('index')->middleware('role.permission:issuance,view');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show')->middleware('role.permission:issuance,view');
    Route::post('/{id}/issuance-status', [LeadController::class, 'updateIssuanceStatus'])->name('updateIssuanceStatus')->middleware('role.permission:issuance,edit');
    Route::post('/{id}/issuance-status/reset', [LeadController::class, 'resetIssuanceStatus'])->name('resetIssuanceStatus')->middleware('role.permission:issuance,edit');
    Route::post('/{id}/unlock-field', [LeadController::class, 'unlockIssuanceField'])->name('unlockField')->middleware('role.permission:issuance,full');
    Route::post('/{id}/recalculate-commission', [LeadController::class, 'recalculateCommission'])->name('recalculateCommission')->middleware('role.permission:issuance,full');
    Route::post('/bulk-recalculate-commission', [LeadController::class, 'bulkRecalculateCommission'])->name('bulkRecalculateCommission')->middleware('role.permission:issuance,full');
});

// QA Review Routes (with permission enforcement)
Route::group(['prefix' => 'qa', 'as' => 'qa.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::QA, Roles::COORDINATOR)]], function () {
    Route::get('/review', [LeadController::class, 'qaReview'])->name('review')->middleware('role.permission:qa-review,view');
});

// Followup Routes
Route::group(['prefix' => 'followup', 'as' => 'followup.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::QA, Roles::RETENTION_OFFICER, Roles::HR)]], function () {
    Route::post('/{id}/assign-person', [\App\Http\Controllers\Admin\FollowupController::class, 'updateFollowupPerson'])->name('assignPerson');
    
    // View and update followups - only shows leads assigned to the user
    Route::get('/my-followups', [\App\Http\Controllers\Admin\FollowupController::class, 'myFollowups'])->name('my-followups');
    Route::post('/{id}/update-status', [\App\Http\Controllers\Admin\FollowupController::class, 'updateFollowupStatus'])->name('updateStatus');
    Route::post('/{id}/update-bank-verification', [\App\Http\Controllers\Admin\FollowupController::class, 'updateBankVerification'])->name('updateBankVerification');
});

// Verifier Routes (only Verifier role)
Route::group([
    'prefix' => 'verifier',
    'as' => 'verifier.',
    'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::VERIFIER, Roles::SUPER_ADMIN, Roles::COORDINATOR)]
], function () {
    // Dashboard
    Route::get('/dashboard', [VerifierController::class, 'dashboard'])->name('dashboard');
    
    // Default to Peregrine for backwards compatibility
    Route::get('/create', [VerifierController::class, 'create'])->name('create');
    Route::post('/store', [VerifierController::class, 'store'])->name('store');

    // Team-specific endpoints
    Route::get('/{team}/create', [VerifierController::class, 'create'])->name('create.team');
    Route::post('/{team}/store', [VerifierController::class, 'store'])->name('store.team');
});

// Peregrine Closers
Route::group([
    'prefix' => 'peregrine/closers',
    'as' => 'peregrine.closers.',
    'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::PEREGRINE_CLOSER, Roles::SUPER_ADMIN, Roles::COORDINATOR)]
], function () {
    Route::get('/', [PeregrineController::class, 'closersIndex'])->name('index')->middleware('role.permission:leads-peregrine,view');
    Route::get('/{lead}/edit', [PeregrineController::class, 'closerEdit'])->name('edit')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/update', [PeregrineController::class, 'closerUpdate'])->name('update')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/mark-failed', [PeregrineController::class, 'closerMarkFailed'])->name('mark-failed')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/mark-pending', [PeregrineController::class, 'closerMarkPending'])->name('mark-pending')->middleware('role.permission:leads-peregrine,edit');
});

// Peregrine Validator
Route::group([
    'prefix' => 'validator',
    'as' => 'validator.',
    'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::PEREGRINE_VALIDATOR, Roles::MANAGER, Roles::SUPER_ADMIN, Roles::COORDINATOR)]
], function () {
    Route::get('/', [\App\Http\Controllers\ValidatorController::class, 'index'])->name('index')->middleware('role.permission:leads-peregrine,view');
    Route::get('/{lead}/edit', [\App\Http\Controllers\ValidatorController::class, 'edit'])->name('edit')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/update', [\App\Http\Controllers\ValidatorController::class, 'update'])->name('update')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/mark-sale', [\App\Http\Controllers\ValidatorController::class, 'markAsSale'])->name('mark-sale')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/mark-forwarded', [\App\Http\Controllers\ValidatorController::class, 'markAsForwarded'])->name('mark-forwarded')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/mark-failed', [\App\Http\Controllers\ValidatorController::class, 'markAsFailed'])->name('mark-failed')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/mark-simple-declined', [\App\Http\Controllers\ValidatorController::class, 'markAsSimpleDeclined'])->name('mark-simple-declined')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/mark-home-office-sale', [\App\Http\Controllers\ValidatorController::class, 'markHomeOfficeSale'])->name('mark-home-office-sale')->middleware('role.permission:leads-peregrine,edit');
    Route::put('/{lead}/return-to-closer', [\App\Http\Controllers\ValidatorController::class, 'returnToCloser'])->name('return-to-closer')->middleware('role.permission:leads-peregrine,edit');
});

// Call Logs - Temporarily Disabled
// Route::group(['prefix' => 'call-logs', 'as' => 'call-logs.', 'middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor']], function () {
//     Route::get('/', [CallLogController::class, 'index'])->name('index');
//     Route::get('/show/{id}', [CallLogController::class, 'show'])->name('show');
//     Route::get('/search', [CallLogController::class, 'search'])->name('search');
//     Route::get('/statistics', [CallLogController::class, 'statistics'])->name('statistics');
//     Route::get('/export', [CallLogController::class, 'export'])->name('export');
//     Route::get('/load-more', [CallLogController::class, 'load-more'])->name('load-more');
//     Route::get('/recent/{user}', [CallLogController::class, 'recentCalls'])->name('recent');
//     Route::post('/refresh', [CallLogController::class, 'refreshCache'])->name('refresh');
//     Route::get('/test', [CallLogController::class, 'testConnection'])->name('test');
//     Route::get('/test-zoom-oauth', function () {
//         try {
//             $clientId = config('zoom.client_id');
//             $clientSecret = config('zoom.client_secret');
//             $accountId = config('zoom.account_id');
//             return response()->json([
//                 'config_status' => [
//                     'client_id' => ! empty($clientId) ? 'Set ('.strlen($clientId).' chars)' : 'Missing',
//                     'client_secret' => ! empty($clientSecret) ? 'Set ('.strlen($clientSecret).' chars)' : 'Missing',
//                     'account_id' => ! empty($accountId) ? 'Set ('.strlen($accountId).' chars)' : 'Missing',
//                 ],
//                 'base64_auth' => base64_encode($clientId.':'.$clientSecret),
//             ]);
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     });
// });

// Dock Section - READ and ADD access for HR, Super Admin, Manager, QA, Co-ordinator
Route::group(['prefix' => 'dock', 'as' => 'dock.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::QA, Roles::HR, Roles::COORDINATOR, Roles::CEO)]], function () {
    Route::get('/', [\App\Http\Controllers\Admin\DockController::class, 'index'])->name('index')->middleware('role.permission:dock,view');
    Route::post('/', [\App\Http\Controllers\Admin\DockController::class, 'store'])->name('store')->middleware('role.permission:dock,edit');
    Route::get('/history/{userId}', [\App\Http\Controllers\Admin\DockController::class, 'history'])->name('history')->middleware('role.permission:dock,view');
});

// Dock Section - EDIT and DELETE access for Super Admin, Manager, QA, Co-ordinator only (NOT HR)
Route::group(['prefix' => 'dock', 'as' => 'dock.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::QA, Roles::COORDINATOR)]], function () {
    Route::put('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'update'])->name('update')->middleware('role.permission:dock,edit');
    Route::patch('/{dockRecord}/cancel', [\App\Http\Controllers\Admin\DockController::class, 'cancel'])->name('cancel')->middleware('role.permission:dock,edit');
    Route::delete('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'destroy'])->name('destroy')->middleware('role.permission:dock,full');
});

// Employee Dock View - Read-only access for employees to view their own dock records
Route::get('/my-dock-records', [\App\Http\Controllers\Admin\DockController::class, 'myDockRecords'])->name('my-dock-records')->middleware(['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::QA, Roles::RETENTION_OFFICER, Roles::HR, Roles::COORDINATOR)]);

// Attendance
Route::group(['prefix' => 'attendance', 'as' => 'attendance.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::HR, Roles::RETENTION_OFFICER, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::QA, Roles::COORDINATOR)]], function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index')->middleware('role.permission:attendance,view');
    Route::get('/history', [AttendanceController::class, 'history'])->name('history')->middleware('role.permission:attendance,view');
    Route::get('/print-view', [AttendanceController::class, 'printView'])->name('print-view')->middleware('role.permission:attendance,view');
    Route::get('/print', [AttendanceController::class, 'print'])->name('print')->middleware('role.permission:attendance,view');
    Route::get('/employee-report/{userId}', [AttendanceController::class, 'employeeReport'])->name('employee-report')->middleware('role.permission:attendance,view');
    Route::get('/export', [AttendanceController::class, 'index'])->name('export')->middleware('role.permission:attendance,view');
    Route::get('/{id}/json', [AttendanceController::class, 'json'])->name('json')->middleware('role.permission:attendance,view');
    
    // Manual entry, editing, and deleting - CEO, Super Admin, Co-ordinator & HR
    Route::middleware([Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::HR)])->group(function () {
        Route::get('/mark-manual', [AttendanceController::class, 'index'])->name('mark-manual')->middleware('role.permission:attendance,edit');
        Route::post('/mark-manual', [AttendanceController::class, 'markManual'])->name('mark-manual.post')->middleware('role.permission:attendance,edit');
        Route::post('/{id}/update', [AttendanceController::class, 'updateAjax'])->name('update')->middleware('role.permission:attendance,edit');
        Route::delete('/{id}', [AttendanceController::class, 'delete'])->name('delete')->middleware('role.permission:attendance,full');
    });
});

// Notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware(['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::COORDINATOR)]);

// API routes for AJAX requests
Route::prefix('api/notifications')->name('api.notifications.')->middleware(['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::RETENTION_OFFICER, Roles::QA, Roles::COORDINATOR)])->group(function () {
    Route::get('/topbar', [NotificationController::class, 'topbar'])->name('topbar');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::patch('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::patch('/{notification}/mark-unread', [NotificationController::class, 'markAsUnread'])->name('mark-unread');
    Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::post('/test', [NotificationController::class, 'createTest'])->name('test');
});

// ========== SALARY MODULE REMOVED - Using Payroll Module Instead ==========
// Salary Management (Super Admin & Manager & Co-ordinator)
// Route::group(['prefix' => 'salary', 'as' => 'salary.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator']], function () {
//     Route::get('/', [SalaryController::class, 'index'])->name('index');
//     Route::post('/calculate', [SalaryController::class, 'calculate'])->name('calculate');
//     Route::get('/records', [SalaryController::class, 'records'])->name('records');
//     Route::get('/records/{salaryRecord}', [SalaryController::class, 'show'])->name('show');
//     Route::get('/employees', [SalaryController::class, 'employees'])->name('employees');
//     Route::put('/employees/{user}', [SalaryController::class, 'updateEmployee'])->name('employees.update');
//     Route::post('/records/{salaryRecord}/deductions', [SalaryController::class, 'addDeduction'])->name('deductions.store');
//     Route::delete('/deductions/{deduction}', [SalaryController::class, 'removeDeduction'])->name('deductions.destroy');
//     Route::patch('/records/{salaryRecord}/approve', [SalaryController::class, 'approve'])->name('approve');
//     Route::patch('/records/{salaryRecord}/mark-paid', [SalaryController::class, 'markPaid'])->name('mark-paid');
//     Route::get('/records/{salaryRecord}/payslip', [SalaryController::class, 'downloadPayslip'])->name('payslip');
//     Route::get('/print', [SalaryController::class, 'printPayroll'])->name('print');
//     
//     // NEW: Salary Components (Basic & Bonus Sheets)
//     Route::get('/components', [SalaryController::class, 'components'])->name('components');
//     Route::get('/components/{salaryComponent}', [SalaryController::class, 'showComponent'])->name('component.show');
//     Route::post('/components/{salaryComponent}/approve', [SalaryController::class, 'approveComponent'])->name('component.approve');
//     Route::post('/components/{salaryComponent}/mark-paid', [SalaryController::class, 'markPaidComponent'])->name('component.mark-paid');
//     Route::get('/components/{salaryComponent}/payslip', [SalaryController::class, 'downloadComponentPayslip'])->name('component.payslip');
// });
// ========== END SALARY MODULE REMOVAL ==========

// EPMS - Effective Project Management System (CEO, Super Admin Only)
Route::group(['prefix' => 'epms', 'as' => 'epms.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN)]], function () {
    // Project CRUD
    Route::get('/', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'index'])->name('index')->middleware('role.permission:epms,view');
    Route::get('/create', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'create'])->name('create')->middleware('role.permission:epms,edit');
    Route::post('/store', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'store'])->name('store')->middleware('role.permission:epms,edit');
    Route::get('/{id}', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'show'])->name('show')->middleware('role.permission:epms,view');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'edit'])->name('edit')->middleware('role.permission:epms,edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'update'])->name('update')->middleware('role.permission:epms,edit');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'destroy'])->name('destroy')->middleware('role.permission:epms,full');
    
    // Milestones
    Route::post('/{id}/milestones', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addMilestone'])->name('milestones.store');
    Route::post('/{id}/milestones/{milestoneId}/update-date', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'updateMilestoneDate'])->name('milestones.update-date');
    
    // Tasks
    Route::post('/{id}/tasks', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addTask'])->name('tasks.store');
    Route::post('/{id}/tasks/{taskId}/status', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'updateTaskStatus'])->name('tasks.update-status');
    Route::post('/{id}/tasks/{taskId}/dates', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'updateTaskDates'])->name('tasks.update-dates');
    Route::post('/{id}/tasks/{taskId}/move', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'moveTask'])->name('tasks.move');
    
    // Task Dependencies
    Route::post('/{id}/dependencies', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addTaskDependency'])->name('dependencies.store');
    
    // External Costs
    Route::post('/{id}/costs', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addExternalCost'])->name('costs.store');
    
    // Sprints
    Route::post('/{id}/sprints', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'storeSprint'])->name('sprints.store');
    Route::post('/{id}/sprints/{sprintId}/start', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'startSprint'])->name('sprints.start');
    Route::post('/{id}/sprints/{sprintId}/complete', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'completeSprint'])->name('sprints.complete');
    
    // Risks
    Route::post('/{id}/risks', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'storeRisk'])->name('risks.store');
    Route::post('/{id}/risks/{riskId}/status', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'updateRiskStatus'])->name('risks.update-status');
    
    // Team Members (RACI)
    Route::post('/{id}/members', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'storeMember'])->name('members.store');
    Route::delete('/{id}/members/{memberId}', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'removeMember'])->name('members.remove');
    
    // Documents
    Route::post('/{id}/documents', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'storeDocument'])->name('documents.store');
    Route::get('/{id}/documents/{docId}/download', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'downloadDocument'])->name('documents.download');
    
    // Comments
    Route::post('/{id}/comments', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'storeComment'])->name('comments.store');
    
    // WBS
    Route::post('/{id}/wbs', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'storeWbsItem'])->name('wbs.store');
    
    // AI Planning
    Route::post('/ai/generate', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'generateAiPlan'])->name('ai.generate');
    Route::post('/{id}/ai/generate', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'generateAiPlan'])->name('ai.generate-for-project');
    Route::post('/{id}/ai/apply', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'applyAiPlan'])->name('ai.apply');
    
    // Gantt Data API
    Route::get('/{id}/gantt-data', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'getGanttData'])->name('gantt-data');
});

// Payroll - View Access (CEO, Super Admin, Co-ordinator & Manager can view)
Route::group(['prefix' => 'payroll', 'as' => 'payroll.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::MANAGER)]], function () {
    Route::get('/', [SalaryController::class, 'payroll'])->name('index')->middleware('role.permission:payroll,view');
    Route::get('/print', [SalaryController::class, 'printPayroll'])->name('print')->middleware('role.permission:payroll,view');
});

// Payroll - Edit Access (Only CEO, Super Admin & Co-ordinator can edit)
Route::group(['prefix' => 'payroll', 'as' => 'payroll.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR)]], function () {
    Route::post('/working-days', [SalaryController::class, 'updateWorkingDays'])->name('working-days.update')->middleware('role.permission:payroll,edit');
    
    // Manual Payroll Entries (for non-system users like ex-employees) - MUST come before /{userId} route
    Route::post('/manual', [SalaryController::class, 'storeManualEntry'])->name('manual.store')->middleware('role.permission:payroll,edit');
    Route::put('/manual/{id}', [SalaryController::class, 'updateManualEntry'])->name('manual.update')->middleware('role.permission:payroll,edit');
    Route::delete('/manual/{id}', [SalaryController::class, 'destroyManualEntry'])->name('manual.destroy')->middleware('role.permission:payroll,full');
    
    Route::match(['post', 'put'], '/{userId}', [SalaryController::class, 'updatePayroll'])->name('update')->middleware('role.permission:payroll,edit');
});

// Chart of Accounts
Route::group(['prefix' => 'chart-of-accounts', 'as' => 'chart-of-accounts.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', [ChartOfAccountController::class, 'index'])->name('index')->middleware('role.permission:chart-of-accounts,view');
    Route::get('/create', [ChartOfAccountController::class, 'create'])->name('create')->middleware('role.permission:chart-of-accounts,edit');
    Route::post('/store', [ChartOfAccountController::class, 'store'])->name('store')->middleware('role.permission:chart-of-accounts,edit');
    Route::get('/show/{id}', [ChartOfAccountController::class, 'show'])->name('show')->middleware('role.permission:chart-of-accounts,view');
    Route::get('/edit/{id}', [ChartOfAccountController::class, 'edit'])->name('edit')->middleware('role.permission:chart-of-accounts,edit');
    Route::put('/update/{id}', [ChartOfAccountController::class, 'update'])->name('update')->middleware('role.permission:chart-of-accounts,edit');
    Route::delete('/delete/{id}', [ChartOfAccountController::class, 'destroy'])->name('delete')->middleware('role.permission:chart-of-accounts,full');
});

// Ledger
Route::group(['prefix' => 'ledger', 'as' => 'ledger.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', [LedgerController::class, 'index'])->name('index')->middleware('role.permission:general-ledger,view');
    Route::get('/create', [LedgerController::class, 'create'])->name('create')->middleware('role.permission:general-ledger,edit');
    Route::post('/store', [LedgerController::class, 'store'])->name('store')->middleware('role.permission:general-ledger,edit');
    Route::get('/show/{id}', [LedgerController::class, 'show'])->name('show')->middleware('role.permission:general-ledger,view');
    Route::get('/export', [LedgerController::class, 'export'])->name('export')->middleware('role.permission:general-ledger,view');
    Route::get('/summary', [LedgerController::class, 'summary'])->name('summary')->middleware('role.permission:general-ledger,view');
});

// Petty Cash Ledger (CEO, Super Admin & Co-ordinator only)
Route::group(['prefix' => 'petty-cash', 'as' => 'petty-cash.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::COORDINATOR)]], function () {
    Route::get('/', [LedgerController::class, 'pettyCashIndex'])->name('index')->middleware('role.permission:petty-cash,view');
    Route::post('/store', [LedgerController::class, 'pettyCashStore'])->name('store')->middleware('role.permission:petty-cash,edit');
    Route::get('/{id}/edit', [LedgerController::class, 'pettyCashEdit'])->name('edit')->middleware('role.permission:petty-cash,edit');
    Route::put('/{id}', [LedgerController::class, 'pettyCashUpdate'])->name('update')->middleware('role.permission:petty-cash,edit');
    Route::delete('/{id}', [LedgerController::class, 'pettyCashDestroy'])->name('destroy')->middleware('role.permission:petty-cash,full');
    Route::get('/print', [LedgerController::class, 'pettyCashPrint'])->name('print')->middleware('role.permission:petty-cash,view');
    Route::get('/export', [LedgerController::class, 'pettyCashExport'])->name('export')->middleware('role.permission:petty-cash,view');
});

// Revenue Analytics (Super Admin & Manager only)
Route::get('/revenue', [DashboardController::class, 'revenue'])
    ->name('revenue.index')
    ->middleware(['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER), 'role.permission:revenue-analytics,view']);

// Live Analytics Dashboard (CEO, Super Admin, Manager & Co-ordinator)
Route::group(['prefix' => 'analytics', 'as' => 'analytics.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/live', [\App\Http\Controllers\Admin\AnalyticsController::class, 'live'])->name('live')->middleware('role.permission:live-analytics,view');
    Route::get('/live/data', [\App\Http\Controllers\Admin\AnalyticsController::class, 'getLiveData'])->name('live.data')->middleware('role.permission:live-analytics,view');
    Route::get('/historical', [\App\Http\Controllers\Admin\AnalyticsController::class, 'getHistoricalData'])->name('historical')->middleware('role.permission:live-analytics,view');
    Route::get('/drill-down', [\App\Http\Controllers\Admin\AnalyticsController::class, 'getDrillDown'])->name('drill-down')->middleware('role.permission:live-analytics,view');
});

// Utility Routes
Route::get('/check-my-ip', function () {
    return response()->json([
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'message' => 'This is your current IP address.',
    ]);
});

// Hub Pages
Route::get('/settings/hub', [SettingsController::class, 'hub'])->name('settings.hub')->middleware(['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO)]);
Route::get('/hr/hub', function () { return view('admin.hr.hub'); })->name('hr.hub')->middleware(['auth', Roles::middleware(Roles::QA, Roles::HR, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO)]);
Route::get('/finance/hub', function () { return view('admin.finance.hub'); })->name('finance.hub')->middleware(['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO)]);

// Settings (Super Admin only)
Route::group(['prefix' => 'settings', 'as' => 'settings.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN)]], function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index')->middleware('role.permission:settings,view');
    Route::post('/', [SettingsController::class, 'update'])->name('update')->middleware('role.permission:settings,edit');
    Route::post('/test-network', [SettingsController::class, 'testNetwork'])->name('test-network')->middleware('role.permission:settings,edit');
});

// Reports (Super Admin, Manager, Co-ordinator, CEO)
Route::group(['prefix' => 'settings/reports', 'as' => 'settings.reports.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::CEO)]], function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/generate', [ReportController::class, 'generate'])->name('generate');
    Route::get('/export', [ReportController::class, 'export'])->name('export');
});

// Permission Management (Super Admin only)
Route::group(['prefix' => 'settings/permissions', 'as' => 'settings.permissions.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN)]], function () {
    // Main permission management page (list of roles)
    Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
    
    // Role permission management
    Route::get('/roles/{role}', [\App\Http\Controllers\Admin\PermissionController::class, 'editRole'])->name('roles.edit');
    Route::post('/roles/{role}', [\App\Http\Controllers\Admin\PermissionController::class, 'updateRole'])->name('roles.update');
    
    // User permission overrides
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\PermissionController::class, 'editUser'])->name('users.edit');
    Route::post('/users/{user}', [\App\Http\Controllers\Admin\PermissionController::class, 'updateUser'])->name('users.update');
    
    // AJAX endpoints for real-time updates
    Route::post('/sync', [\App\Http\Controllers\Admin\PermissionController::class, 'syncPermissions'])->name('sync');
    Route::post('/clear-cache', [\App\Http\Controllers\Admin\PermissionController::class, 'clearCache'])->name('clear-cache');
});

// Holidays (Super Admin and Manager and Co-ordinator)
Route::group(['prefix' => 'holidays', 'as' => 'admin.holidays.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', [HolidayController::class, 'index'])->name('index')->middleware('role.permission:holidays,view');
    Route::get('/create', [HolidayController::class, 'create'])->name('create')->middleware('role.permission:holidays,edit');
    Route::post('/', [HolidayController::class, 'store'])->name('store')->middleware('role.permission:holidays,edit');
    Route::get('/{holiday}/edit', [HolidayController::class, 'edit'])->name('edit')->middleware('role.permission:holidays,edit');
    Route::put('/{holiday}', [HolidayController::class, 'update'])->name('update')->middleware('role.permission:holidays,edit');
    Route::delete('/{holiday}', [HolidayController::class, 'destroy'])->name('destroy')->middleware('role.permission:holidays,full');
    Route::post('/check-date', [HolidayController::class, 'checkDate'])->name('check-date')->middleware('role.permission:holidays,view');
});

// Public Holidays Management (Super Admin only - HR and Co-ordinator can view)
Route::group(['prefix' => 'admin/public-holidays', 'as' => 'admin.public-holidays.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::HR, Roles::COORDINATOR)]], function () {
    Route::get('/', [PublicHolidayController::class, 'index'])->name('index')->middleware('role.permission:public-holidays,view');
    Route::get('/create', [PublicHolidayController::class, 'create'])->name('create')->middleware('role.permission:public-holidays,edit');
    Route::post('/', [PublicHolidayController::class, 'store'])->name('store')->middleware('role.permission:public-holidays,edit');
    Route::get('/{holiday}/edit', [PublicHolidayController::class, 'edit'])->name('edit')->middleware('role.permission:public-holidays,edit');
    Route::put('/{holiday}', [PublicHolidayController::class, 'update'])->name('update')->middleware('role.permission:public-holidays,edit');
    Route::delete('/{holiday}', [PublicHolidayController::class, 'destroy'])->name('destroy')->middleware('role.permission:public-holidays,full');
    Route::post('/{holiday}/toggle', [PublicHolidayController::class, 'toggle'])->name('toggle')->middleware('role.permission:public-holidays,edit');
    Route::post('/check-date', [PublicHolidayController::class, 'checkDate'])->name('check-date')->middleware('role.permission:public-holidays,view');
    Route::get('/month', [PublicHolidayController::class, 'getMonthHolidays'])->name('month');
});

// Profile Update
Route::post('/update-profile/{id}', [ProfileController::class, 'updateProfile'])->name('updateProfile')->middleware('auth');
Route::post('/update-password/{id}', [ProfileController::class, 'updatePassword'])->name('updatePassword')->middleware('auth');

// Language Translation
Route::get('index/{locale}', [LocalizationController::class, 'lang'])->middleware('auth');

// Broadcasting Auth
Route::post('/broadcasting/auth', function (Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
})->middleware(['web', 'auth']);

// Team Chat
Route::group(['prefix' => 'chat', 'as' => 'chat.', 'middleware' => 'auth'], function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/notification-settings', [ChatNotificationController::class, 'settingsView'])->name('notification-settings');
});

// Chat API Routes (in web.php to use web session auth)
Route::group(['prefix' => 'api/chat', 'middleware' => ['auth']], function () {
    Route::get('/conversations', [ChatController::class, 'getConversations']);
    Route::get('/group-conversations', [ChatController::class, 'getGroupConversations']); // Get group conversations for Communities tab
    Route::get('/unread-count', [ChatController::class, 'getUnreadCount']);
    Route::get('/new-messages', [ChatController::class, 'getNewMessages']);
    Route::post('/conversations/direct', [ChatController::class, 'getOrCreateConversation']);
    Route::post('/conversations/group', [ChatController::class, 'createGroupConversation']);
    Route::post('/groups', [ChatController::class, 'createGroup']); // Alternative endpoint
    Route::get('/communities', [ChatController::class, 'getCommunities']); // Get communities for group creation

    // Chat Notifications
    Route::get('/notifications/preferences', [ChatNotificationController::class, 'getPreferences']);
    Route::post('/notifications/preferences', [ChatNotificationController::class, 'updatePreferences']);
    Route::post('/notifications/request-permission', [ChatNotificationController::class, 'requestPermission']);
    Route::post('/notifications/should-notify', [ChatNotificationController::class, 'shouldNotify']);
    Route::post('/notifications/subscribe', [ChatNotificationController::class, 'subscribeToNotifications']);
    
    // Community announcements
    Route::get('/user/community-announcements', [CommunityAnnouncementController::class, 'getUserCommunityAnnouncements']);
    Route::get('/announcements/poll', [CommunityAnnouncementController::class, 'poll']);
    Route::get('/communities/{community}/announcements', [CommunityAnnouncementController::class, 'index']);
    Route::post('/communities/{community}/announcements', [CommunityAnnouncementController::class, 'store']);
    Route::put('/communities/{community}/announcements/{announcement}', [CommunityAnnouncementController::class, 'update']);
    Route::delete('/communities/{community}/announcements/{announcement}', [CommunityAnnouncementController::class, 'destroy']);
    
    // Group management routes
    Route::get('/conversations/{id}', [ChatController::class, 'getConversation']);
    Route::put('/conversations/{id}', [ChatController::class, 'updateConversation']);
    Route::post('/conversations/{id}/avatar', [ChatController::class, 'updateConversationAvatar']);
    Route::delete('/conversations/{id}', [ChatController::class, 'deleteConversation']);
    Route::get('/conversations/{id}/members', [ChatController::class, 'getConversationMembers']);
    Route::get('/conversations/{id}/users', [ChatController::class, 'getConversationUsers']); // For mentions autocomplete
    Route::post('/conversations/{id}/members', [ChatController::class, 'addMember']);
    Route::delete('/conversations/{id}/members/{userId}', [ChatController::class, 'removeMember']);
    
    Route::get('/conversations/{id}/messages', [ChatController::class, 'getMessages']);
    Route::post('/messages', [ChatController::class, 'sendMessage']);
    Route::delete('/messages/{id}', [ChatController::class, 'deleteMessage']);
    Route::get('/users', [ChatController::class, 'getUsers']);
    Route::get('/search', [ChatController::class, 'search']);
});

// Chargebacks
Route::group(['prefix' => 'chargebacks', 'as' => 'chargebacks.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::COORDINATOR)]], function () {
    Route::get('/', [ChargebackController::class, 'index'])->name('index');
    Route::get('/show/{id}', [ChargebackController::class, 'show'])->name('show');
});

// Retention
Route::group(['prefix' => 'retention', 'as' => 'retention.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::RETENTION_OFFICER, Roles::COORDINATOR, Roles::CEO)]], function () {
    Route::get('/', [RetentionController::class, 'index'])->name('index')->middleware('role.permission:retention,view');
    Route::post('/{id}/status', [RetentionController::class, 'updateStatus'])->name('updateStatus')->middleware('role.permission:retention,edit');
    Route::get('/incomplete', [RetentionController::class, 'incompleteIssuance'])->name('incomplete')->middleware('role.permission:retention,view');
    Route::get('/incomplete/{id}/details', [RetentionController::class, 'showIncompleteDetails'])->name('incompleteDetails')->middleware('role.permission:retention,view');
    Route::post('/{id}/disposition', [RetentionController::class, 'saveDisposition'])->name('saveDisposition')->middleware('role.permission:retention,edit');
    Route::get('/check-other-insurances/{id}', [RetentionController::class, 'checkOtherInsurances'])->name('checkOtherInsurances')->middleware('role.permission:retention,view');
});

// Retention Officer Dashboard
Route::get('/retention-dashboard', [RetentionDashboardController::class, 'index'])
    ->middleware(['auth', Roles::middleware(Roles::RETENTION_OFFICER, Roles::SUPER_ADMIN, Roles::COORDINATOR, Roles::CEO, Roles::MANAGER)])
    ->middleware('role.permission:retention,view')
    ->name('retention.dashboard');

// Bank Verification (Super Admin Only)
Route::group(['prefix' => 'bank-verification', 'as' => 'bank-verification.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    Route::get('/', [\App\Http\Controllers\Admin\BankVerificationController::class, 'index'])->name('index')->middleware('role.permission:bank-verification,view');
    Route::get('/{id}/show', [\App\Http\Controllers\Admin\BankVerificationController::class, 'show'])->name('show')->middleware('role.permission:bank-verification,view');
    Route::post('/{id}/update', [\App\Http\Controllers\Admin\BankVerificationController::class, 'updateVerification'])->name('update')->middleware('role.permission:bank-verification,edit');
    Route::post('/{id}/assign-verifier', [\App\Http\Controllers\Admin\BankVerificationController::class, 'assignVerifier'])->name('assignVerifier')->middleware('role.permission:bank-verification,edit');
    Route::post('/{id}/update-assignment', [\App\Http\Controllers\Admin\BankVerificationController::class, 'updateAssignmentDetails'])->name('updateAssignment')->middleware('role.permission:bank-verification,edit');
});

// Revenue Analytics (Super Admin Only)
Route::group(['prefix' => 'revenue-analytics', 'as' => 'revenue-analytics.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::CEO, Roles::COORDINATOR, Roles::MANAGER)]], function () {
    Route::get('/', [\App\Http\Controllers\Admin\RevenueAnalyticsController::class, 'index'])->name('index')->middleware('role.permission:revenue-analytics,view');
});

// Ravens Routes
Route::group(['prefix' => 'ravens', 'as' => 'ravens.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::RAVENS_CLOSER, Roles::COORDINATOR)]], function () {
    Route::get('/dashboard', [RavensDashboardController::class, 'index'])->name('dashboard')->middleware('role.permission:ravens-dashboard,view');
    Route::get('/calling', [RavensDashboardController::class, 'calling'])->name('calling')->middleware('role.permission:ravens-calling,view');
    Route::get('/leads/{leadId}/data', [RavensDashboardController::class, 'getLeadData'])->name('leads.data')->middleware('role.permission:ravens,view');
    Route::post('/leads/save', [RavensDashboardController::class, 'saveLead'])->name('leads.save')->middleware('role.permission:ravens,edit');
    Route::post('/leads/submit-sale', [RavensDashboardController::class, 'submitSale'])->name('leads.submit-sale')->middleware('role.permission:ravens,edit');
    Route::post('/leads/dispose', [RavensDashboardController::class, 'disposeLead'])->name('leads.dispose')->middleware('role.permission:ravens,edit');
    Route::post('/leads/restore', [RavensDashboardController::class, 'restoreLead'])->name('leads.restore')->middleware('role.permission:ravens,edit');
    Route::post('/leads/save-callback-note', [RavensDashboardController::class, 'saveCallbackNote'])->name('leads.save-callback-note');
    Route::post('/leads/record-dial', [RavensDashboardController::class, 'recordDial'])->name('leads.record-dial');
    Route::get('/leads/dial-status', [RavensDashboardController::class, 'getDialStatus'])->name('leads.dial-status');
    Route::get('/bad-leads', [RavensDashboardController::class, 'badLeads'])->name('bad-leads')->middleware('role.permission:ravens-bad-leads,view');
});

// Public (authenticated) attendance endpoints for all users - MUST BE BEFORE CATCH-ALL
Route::group(['middleware' => ['auth']], function () {
    // Check-in and check-out API (AJAX)
    Route::post('/attendance/check-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [\App\Http\Controllers\Admin\AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    // Personal attendance dashboard (calendar + stats)
    Route::get('/attendance/dashboard', [\App\Http\Controllers\Admin\AttendanceController::class, 'dashboard'])->name('attendance.dashboard');
});

// Zoom Phone Integration Routes
Route::group(['prefix' => 'zoom', 'as' => 'zoom.', 'middleware' => ['auth']], function () {
    Route::get('/authorize', [App\Http\Controllers\ZoomController::class, 'startAuthorization'])->name('authorize');
    Route::get('/callback', [App\Http\Controllers\ZoomController::class, 'callback'])->name('callback');
    Route::post('/dial/{leadId}', [App\Http\Controllers\ZoomController::class, 'makeCall'])->name('dial');
    Route::get('/call-status/{leadId}', [App\Http\Controllers\ZoomController::class, 'getCallStatusByLead'])->name('call.status');    
    // Test endpoint to simulate webhook call connected
    Route::get('/test-webhook-connected/{leadId}', function($leadId) {
        $callLog = \App\Models\CallLog::where('lead_id', $leadId)->orderBy('created_at', 'desc')->first();
        if ($callLog) {
            $callLog->update(['call_status' => 'connected']);
            return response()->json([
                'success' => true, 
                'message' => 'CallLog updated to connected for testing',
                'call_log_id' => $callLog->id,
                'lead_id' => $leadId,
                'status' => 'connected'
            ]);
        }
        return response()->json(['error' => 'CallLog not found for lead'], 404);
    });});

// Zoom Webhook (public, no auth required)
Route::post('/zoom/webhook', [App\Http\Controllers\Admin\ZoomWebhookController::class, 'handleWebhook'])->name('zoom.webhook');

// Zoom API Testing Routes (for proper development)
Route::middleware(['auth'])->group(function () {
    Route::get('/zoom/test-api', [App\Http\Controllers\ZoomController::class, 'testApiCapabilities'])->name('zoom.test-api');
    Route::get('/zoom/test-phone-auth', [App\Http\Controllers\ZoomController::class, 'testPhoneAuth'])->name('zoom.test-phone-auth');
});

// Call Events API (moved from api.php for proper web authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/call-events/poll', function (Request $request) {
        $userId = auth()->id();

        // Get unread connected call events for this user
        $callEvent = \App\Models\CallEvent::where('user_id', $userId)
            ->where('status', 'connected')
            ->where('is_read', false)
            ->orderBy('event_time', 'desc')
            ->first();

        if ($callEvent) {
            return response()->json([
                'has_call' => true,
                'lead_id' => $callEvent->lead_id,
                'status' => $callEvent->status,
                'lead_data' => $callEvent->lead_data,
                'caller_number' => $callEvent->caller_number,
                'callee_number' => $callEvent->callee_number,
                'event_id' => $callEvent->id,
            ]);
        }

        return response()->json(['has_call' => false]);
    });

    Route::post('/api/call-events/{id}/mark-read', function (Request $request, $id) {
        $callEvent = \App\Models\CallEvent::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($callEvent) {
            $callEvent->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Call event not found'], 404);
    });
});

// Project Authorization & Budget System (PABS) Routes - CEO, Super Admin, Manager, Co-ordinator
Route::group(['prefix' => 'pabs', 'as' => 'pabs.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR)]], function () {
    // Tickets
    Route::get('/tickets', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index')->middleware('role.permission:pabs-tickets,view');
    Route::get('/tickets/create', [App\Http\Controllers\Admin\TicketController::class, 'create'])->name('tickets.create')->middleware('role.permission:pabs-tickets,edit');
    Route::post('/tickets', [App\Http\Controllers\Admin\TicketController::class, 'store'])->name('tickets.store')->middleware('role.permission:pabs-tickets,edit');
    Route::get('/tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show')->middleware('role.permission:pabs-tickets,view');
    Route::put('/tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'update'])->name('tickets.update')->middleware('role.permission:pabs-tickets,edit');
    Route::delete('/tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'destroy'])->name('tickets.destroy')->middleware('role.permission:pabs-tickets,full');
    Route::post('/tickets/{ticket}/add-comment', [App\Http\Controllers\Admin\TicketController::class, 'addComment'])->name('tickets.addComment')->middleware('role.permission:pabs-tickets,edit');
    Route::post('/tickets/{ticket}/approve', [App\Http\Controllers\Admin\TicketController::class, 'approve'])->name('tickets.approve')->middleware('role.permission:pabs-tickets,edit');
    Route::post('/tickets/{ticket}/reject', [App\Http\Controllers\Admin\TicketController::class, 'reject'])->name('tickets.reject')->middleware('role.permission:pabs-tickets,edit');
    Route::post('/tickets/{ticket}/resolve', [App\Http\Controllers\Admin\TicketController::class, 'resolve'])->name('tickets.resolve')->middleware('role.permission:pabs-tickets,edit');
    Route::post('/tickets/{ticket}/close', [App\Http\Controllers\Admin\TicketController::class, 'close'])->name('tickets.close')->middleware('role.permission:pabs-tickets,edit');
});

// Sticky Notes Routes - All authenticated users
Route::middleware(['auth'])->prefix('sticky-notes')->as('sticky-notes.')->group(function () {
    Route::get('/', [App\Http\Controllers\StickyNoteController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\StickyNoteController::class, 'store'])->name('store');
    Route::put('/{stickyNote}', [App\Http\Controllers\StickyNoteController::class, 'update'])->name('update');
    Route::delete('/{stickyNote}', [App\Http\Controllers\StickyNoteController::class, 'destroy'])->name('destroy');
});

// Catch-all route - MUST BE LAST
Route::get('{any}', [DashboardController::class, 'index'])->where('any', '.*')->name('index');