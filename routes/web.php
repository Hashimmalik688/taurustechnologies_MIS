<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ChargebackController;
use App\Http\Controllers\Admin\EPMSProjectController;
use App\Http\Controllers\Admin\InsuranceCarrierController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\LedgerJournalController;
use App\Http\Controllers\Admin\ChartOfAccountController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RetentionController;
use App\Http\Controllers\Admin\PendingsApprovedController;
use App\Http\Controllers\Admin\PendingDraftController;
use App\Http\Controllers\Admin\PaidSalesController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\ChatShadowController;
use App\Http\Controllers\Admin\NotepadController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\PublicHolidayController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RavensDashboardController;
use App\Http\Controllers\Admin\RetentionDashboardController;
use App\Http\Controllers\Admin\DupeCheckerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CloserReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamDashboardController;
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
Auth::routes(['register' => false, 'reset' => false, 'confirm' => false, 'verify' => false]);

// Partner Authentication Routes
Route::prefix('partner')->group(function () {
    // Login routes (accessible always, but prevent logged-in users from accessing)
    Route::middleware('prevent.user')->group(function () {
        Route::get('login', [App\Http\Controllers\Partner\PartnerAuthController::class, 'showLoginForm'])->name('partner.login');
        Route::post('login', [App\Http\Controllers\Partner\PartnerAuthController::class, 'login'])
            ->middleware('throttle:partner-login')
            ->name('partner.login.submit');
    });

    // Protected partner routes (only partners can access)
    Route::middleware(['partner.auth', 'prevent.user'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Partner\PartnerDashboardController::class, 'index'])->name('partner.dashboard');
        Route::get('carriers', [App\Http\Controllers\Partner\PartnerDashboardController::class, 'carriers'])->name('partner.carriers');
        Route::get('sales', [App\Http\Controllers\Partner\PartnerDashboardController::class, 'sales'])->name('partner.sales');
        Route::get('ledger', [App\Http\Controllers\Partner\PartnerDashboardController::class, 'ledger'])->name('partner.ledger');
        Route::post('mark-commission-paid', [App\Http\Controllers\Partner\PartnerDashboardController::class, 'markCommissionPaid'])->name('partner.mark-commission-paid');
        Route::post('mark-commission-unpaid', [App\Http\Controllers\Partner\PartnerDashboardController::class, 'markCommissionUnpaid'])->name('partner.mark-commission-unpaid');
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
Route::group(['middleware' => ['auth', 'prevent.partner', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::PEREGRINE_MANAGER, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::QA, Roles::RETENTION_OFFICER, Roles::COORDINATOR, Roles::HR, Roles::IT_MANAGER)]], function () {
    // Smart router - redirects each user to their appropriate landing page
    Route::get('/', [DashboardController::class, 'root'])->name('root');
    
    // Executive Dashboard (Company Overview) - has its own URL with permission check
    Route::get('/dashboard', [DashboardController::class, 'executiveDashboard'])->name('dashboard')->middleware('role.permission:dashboard,view');
    
    // API endpoint to fetch fresh KPI data for live updates
    Route::get('/dashboard/kpi-data', [DashboardController::class, 'getKpiData'])->name('dashboard.kpi-data');

    // Chill Party topbar widget — ALL Ravens Closers with sale status for today
    Route::get('/api/freeloaders', [DashboardController::class, 'freeloaders'])->name('api.freeloaders');
});

// Team Dashboards — access controlled by role.permission:team-dashboards,level
Route::group(['middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/team/peregrine', [TeamDashboardController::class, 'peregrineTeam'])->name('team.peregrine')->middleware('role.permission:team-dashboards,view');
    Route::get('/team/ravens', [TeamDashboardController::class, 'ravensTeam'])->name('team.ravens')->middleware('role.permission:team-dashboards,view');
    Route::get('/closer/{userId}/details', [TeamDashboardController::class, 'closerDetails'])->name('closer.details')->middleware('role.permission:team-dashboards,view');
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

// Users Management — access controlled by role.permission:users,level
Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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

// Employee Management Sheet (E.M.S) — access controlled by role.permission:ems,level
Route::group(['prefix' => 'ems', 'as' => 'employee.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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
    Route::post('/toggle-strip-photo/{employee}', [EmployeeController::class, 'toggleStripPhoto'])->name('toggle-strip-photo')->middleware('role.permission:ems,edit');
});

// Dupe Checker — access controlled by role.permission:duplicate-checker,level
Route::group(['prefix' => 'admin/dupe-checker', 'as' => 'admin.dupe-checker.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [DupeCheckerController::class, 'index'])->name('index')->middleware('role.permission:duplicate-checker,view');
    Route::post('/self-check', [DupeCheckerController::class, 'selfCheck'])->name('self-check')->middleware('role.permission:duplicate-checker,edit');
    Route::post('/file-comparison', [DupeCheckerController::class, 'fileComparison'])->name('file-comparison')->middleware('role.permission:duplicate-checker,edit');
    Route::post('/run-deduplication', [DupeCheckerController::class, 'runDeduplication'])->name('run-deduplication')->middleware('role.permission:duplicate-checker,full');
});

// Security suspect reporting (screenshot, devtools, print) — all authenticated users
Route::post('/api/security/report-suspect', [SecurityController::class, 'reportSuspect'])
    ->name('security.report-suspect')
    ->middleware(['auth', 'throttle:30,1']);

// Account Switching Log & Audit Logs — access controlled by role.permission:account-switch-log,level
Route::group(['prefix' => 'admin', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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
Route::group(['prefix' => 'agents', 'as' => 'agents.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', function() { return redirect()->route('admin.partners.index'); })->name('index');
    Route::get('/create', function() { return redirect()->route('admin.partners.create'); })->name('create');
    Route::post('/store', function() { return redirect()->route('admin.partners.index'); })->name('store');
    Route::get('/show/{id}', function($id) { return redirect()->route('admin.partners.show', $id); })->name('show');
    Route::get('/edit/{id}', function($id) { return redirect()->route('admin.partners.edit', $id); })->name('edit');
    Route::put('/update/{id}', function($id) { return redirect()->route('admin.partners.index'); })->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'destroy'])->name('delete');
});

// Partners Management — access controlled by role.permission:partners,level
Route::group(['prefix' => 'admin/partners', 'as' => 'admin.partners.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [App\Http\Controllers\Admin\PartnerController::class, 'index'])->name('index')->middleware('role.permission:partners,view');
    Route::get('/create', [App\Http\Controllers\Admin\PartnerController::class, 'create'])->name('create')->middleware('role.permission:partners,edit');
    Route::post('/store', [App\Http\Controllers\Admin\PartnerController::class, 'store'])->name('store')->middleware('role.permission:partners,edit');
    Route::get('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'show'])->name('show')->middleware('role.permission:partners,view');
    Route::get('/{id}/edit', [App\Http\Controllers\Admin\PartnerController::class, 'edit'])->name('edit')->middleware('role.permission:partners,edit');
    Route::put('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'update'])->name('update')->middleware('role.permission:partners,edit');
    Route::delete('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'destroy'])->name('destroy')->middleware('role.permission:partners,full');
    Route::delete('/{partnerId}/carriers/{carrierId}', [App\Http\Controllers\Admin\PartnerController::class, 'removeCarrierAssignment'])->name('remove-carrier-assignment')->middleware('role.permission:partners,edit');
});

// Insurance Carriers Management — access controlled by role.permission:carriers,level
Route::group(['prefix' => 'admin/insurance-carriers', 'as' => 'admin.insurance-carriers.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [InsuranceCarrierController::class, 'index'])->name('index')->middleware('role.permission:carriers,view');
    Route::get('/create', [InsuranceCarrierController::class, 'create'])->name('create')->middleware('role.permission:carriers,edit');
    Route::post('/store', [InsuranceCarrierController::class, 'store'])->name('store')->middleware('role.permission:carriers,edit');
    Route::post('/{insuranceCarrier}/toggle-active', [InsuranceCarrierController::class, 'toggleActive'])->name('toggle-active')->middleware('role.permission:carriers,edit');
    Route::get('/{insuranceCarrier}', [InsuranceCarrierController::class, 'show'])->name('show')->middleware('role.permission:carriers,view');
    Route::get('/{insuranceCarrier}/edit', [InsuranceCarrierController::class, 'edit'])->name('edit')->middleware('role.permission:carriers,edit');
    Route::put('/{insuranceCarrier}', [InsuranceCarrierController::class, 'update'])->name('update')->middleware('role.permission:carriers,edit');
    Route::delete('/{insuranceCarrier}', [InsuranceCarrierController::class, 'destroy'])->name('destroy')->middleware('role.permission:carriers,full');
});

// Leads Management — access controlled by role.permission:leads/leads-peregrine,level
Route::group(['prefix' => 'leads', 'as' => 'leads.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [LeadController::class, 'index'])->name('index')->middleware('role.permission:leads-peregrine,view');
    Route::get('/duplicates', [LeadController::class, 'duplicates'])->name('duplicates')->middleware('role.permission:leads-peregrine,view');
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
    Route::post('/{id}/send-to-previous-stage', [LeadController::class, 'sendToPreviousStage'])->name('sendToPreviousStage')->middleware('role.permission:issuance,edit');
});

// Sales Management — access controlled by role.permission:sales,level
Route::group(['prefix' => 'sales', 'as' => 'sales.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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
    Route::post('/{id}/assign-back', [LeadController::class, 'assignBack'])->name('assignBack')->middleware('role.permission:sales,edit');
});

// Pending Contracts (formerly Issuance) — access controlled by role.permission:issuance,level
Route::group(['prefix' => 'pending-contracts', 'as' => 'issuance.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [LeadController::class, 'issuance'])->name('index')->middleware('role.permission:issuance,view');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show')->middleware('role.permission:issuance,view');
    Route::post('/{id}/issuance-status', [LeadController::class, 'updateIssuanceStatus'])->name('updateIssuanceStatus')->middleware('role.permission:issuance,edit');
    Route::post('/{id}/issuance-status/reset', [LeadController::class, 'resetIssuanceStatus'])->name('resetIssuanceStatus')->middleware('role.permission:issuance,edit');
    Route::post('/{id}/mark-issued', [LeadController::class, 'markAsIssued'])->name('markIssued')->middleware('role.permission:issuance,edit');
    Route::post('/{id}/mark-not-issued', [LeadController::class, 'markAsNotIssued'])->name('markNotIssued')->middleware('role.permission:issuance,edit');
    Route::post('/{id}/unlock-field', [LeadController::class, 'unlockIssuanceField'])->name('unlockField')->middleware('role.permission:issuance,full');
    Route::post('/{id}/recalculate-commission', [LeadController::class, 'recalculateCommission'])->name('recalculateCommission')->middleware('role.permission:issuance,full');
    Route::post('/bulk-recalculate-commission', [LeadController::class, 'bulkRecalculateCommission'])->name('bulkRecalculateCommission')->middleware('role.permission:issuance,full');
    Route::post('/{id}/send-to-pending-draft', [LeadController::class, 'sendToPendingDraft'])->name('sendToPendingDraft')->middleware('role.permission:issuance,edit');
});

// Pendings Approved — Stage 2 pipeline
Route::group(['prefix' => 'submissions', 'as' => 'submissions.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [PendingsApprovedController::class, 'index'])->name('index')->middleware('role.permission:pendings-approved,view');
    Route::get('/export', [PendingsApprovedController::class, 'export'])->name('export')->middleware('role.permission:pendings-approved,view');
    Route::post('/{id}/send-to-contract', [PendingsApprovedController::class, 'sendToContract'])->name('sendToContract')->middleware('role.permission:pendings-approved,edit');
    Route::post('/{id}/mark-not-issued', [PendingsApprovedController::class, 'markNotIssued'])->name('markNotIssued')->middleware('role.permission:pendings-approved,edit');
    Route::post('/{id}/resolve-not-issued', [PendingsApprovedController::class, 'resolveNotIssued'])->name('resolveNotIssued')->middleware('role.permission:pendings-approved,edit');
    Route::post('/{id}/save-decision', [PendingsApprovedController::class, 'saveDecision'])->name('saveDecision')->middleware('role.permission:pendings-approved,edit');
    Route::post('/{id}/update-field', [PendingsApprovedController::class, 'updateField'])->name('updateField')->middleware('role.permission:pendings-approved,edit');
    Route::post('/{id}/recall-to-closer', [PendingsApprovedController::class, 'recallToCloser'])->name('recallToCloser')->middleware('role.permission:pendings-approved,edit');
    Route::post('/{id}/update-coverage', [PendingsApprovedController::class, 'updateCoverage'])->name('updateCoverage')->middleware('role.permission:pendings-approved,edit');
});

// Pending Draft — Stage 6 pipeline
Route::group(['prefix' => 'pending-draft', 'as' => 'pending-draft.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [PendingDraftController::class, 'index'])->name('index')->middleware('role.permission:pending-draft,view');
    Route::get('/export', [PendingDraftController::class, 'export'])->name('export')->middleware('role.permission:pending-draft,view');
    Route::post('/{id}/mark-not-paid', [PendingDraftController::class, 'markNotPaid'])->name('markNotPaid')->middleware('role.permission:pending-draft,edit');
    Route::post('/{id}/mark-paid', [PendingDraftController::class, 'markPaid'])->name('markPaid')->middleware('role.permission:pending-draft,full');
    Route::post('/{id}/mark-policy-died', [PendingDraftController::class, 'markPolicyDied'])->name('markPolicyDied')->middleware('role.permission:pending-draft,full');
    Route::post('/{id}/clear-not-paid', [PendingDraftController::class, 'clearNotPaid'])->name('clearNotPaid')->middleware('role.permission:pending-draft,edit');
});

// Paid Sales — Stage 7 pipeline (read-only)
Route::group(['prefix' => 'paid-sales', 'as' => 'paid-sales.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [PaidSalesController::class, 'index'])->name('index')->middleware('role.permission:paid-sales,view');
    Route::post('/{id}/mark-chargeback', [PaidSalesController::class, 'markChargeback'])->name('markChargeback')->middleware('role.permission:paid-sales,edit');
    Route::post('/{id}/mark-chargeback-paid', [PaidSalesController::class, 'markChargebackPaid'])->name('markChargebackPaid')->middleware('role.permission:accounting,edit');
    Route::post('/{id}/post-to-ledger', [PaidSalesController::class, 'postToLedger'])->name('postToLedger')->middleware('role.permission:accounting,edit');
    Route::post('/post-all-to-ledger', [PaidSalesController::class, 'postAllToLedger'])->name('postAllToLedger')->middleware('role.permission:accounting,edit');
});

// QA Review — access controlled by role.permission:qa-review,level
Route::group(['prefix' => 'qa', 'as' => 'qa.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/review', [LeadController::class, 'qaReview'])->name('review')->middleware('role.permission:qa-review,view');
    Route::post('/{id}/qa-status', [LeadController::class, 'updateQaStatus'])->name('updateQaStatus')->middleware('role.permission:qa-review,edit');
    Route::post('/{id}/qa-status/reset', [LeadController::class, 'resetQaStatus'])->name('resetQaStatus')->middleware('role.permission:qa-review,edit');
});

// Followup Routes
Route::group(['prefix' => 'followup', 'as' => 'followup.', 'middleware' => ['auth', Roles::middleware(Roles::CEO, Roles::SUPER_ADMIN, Roles::MANAGER, Roles::COORDINATOR, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::QA, Roles::RETENTION_OFFICER, Roles::HR)]], function () {
    Route::post('/{id}/assign-person', [\App\Http\Controllers\Admin\FollowupController::class, 'updateFollowupPerson'])->name('assignPerson');
    
    // View and update followups - only shows leads assigned to the user
    Route::get('/my-followups', [\App\Http\Controllers\Admin\FollowupController::class, 'myFollowups'])->name('my-followups');
    Route::get('/report', [\App\Http\Controllers\Admin\FollowupController::class, 'report'])->name('report')->middleware('role.permission:issuance,view');
    Route::post('/{id}/update-status', [\App\Http\Controllers\Admin\FollowupController::class, 'updateFollowupStatus'])->name('update-status');
    // Route::post('/{id}/update-bank-verification', [\App\Http\Controllers\Admin\FollowupController::class, 'updateBankVerification'])->name('updateBankVerification'); // Bank verification disabled
    Route::post('/{id}/mark-done', [\App\Http\Controllers\Admin\FollowupController::class, 'markFollowupDone'])->name('mark-done');
    Route::get('/followup-done', [\App\Http\Controllers\Admin\FollowupController::class, 'followupDone'])->name('followup-done')->middleware('role.permission:issuance,view');
});

// Verifier Routes — access controlled by role.permission:peregrine*,level
Route::group([
    'prefix' => 'verifier',
    'as' => 'verifier.',
    'middleware' => ['auth', Roles::middleware(...Roles::ALL)]
], function () {
    // Dashboard
    Route::get('/dashboard', [VerifierController::class, 'dashboard'])->name('dashboard')->middleware('role.permission:peregrine-dashboard,view');
    
    // Default to Peregrine for backwards compatibility
    Route::get('/create', [VerifierController::class, 'create'])->name('create')->middleware('role.permission:peregrine-verifier,view');
    Route::post('/store', [VerifierController::class, 'store'])->name('store')->middleware('role.permission:peregrine-verifier,edit');

    // Team-specific endpoints
    Route::get('/{team}/create', [VerifierController::class, 'create'])->name('create.team')->middleware('role.permission:peregrine-verifier,view');
    Route::post('/{team}/store', [VerifierController::class, 'store'])->name('store.team')->middleware('role.permission:peregrine-verifier,edit');
});

// Peregrine Closers — access controlled by role.permission:peregrine-closers,level
Route::group([
    'prefix' => 'peregrine/closers',
    'as' => 'peregrine.closers.',
    'middleware' => ['auth', Roles::middleware(...Roles::ALL)]
], function () {
    Route::get('/', [PeregrineController::class, 'closersIndex'])->name('index')->middleware('role.permission:peregrine-closers,view');
    Route::post('/manual-store', [PeregrineController::class, 'manualStore'])->name('manual-store')->middleware('role.permission:peregrine-closers,edit');
    Route::get('/{lead}/edit', [PeregrineController::class, 'closerEdit'])->name('edit')->middleware('role.permission:peregrine-closers,edit');
    Route::put('/{lead}/update', [PeregrineController::class, 'closerUpdate'])->name('update')->middleware('role.permission:peregrine-closers,edit');
    Route::put('/{lead}/mark-failed', [PeregrineController::class, 'closerMarkFailed'])->name('mark-failed')->middleware('role.permission:peregrine-closers,edit');
    Route::put('/{lead}/mark-pending', [PeregrineController::class, 'closerMarkPending'])->name('mark-pending')->middleware('role.permission:peregrine-closers,edit');
});

// Peregrine Validator — access controlled by role.permission:peregrine-validation,level
Route::group([
    'prefix' => 'validator',
    'as' => 'validator.',
    'middleware' => ['auth', Roles::middleware(...Roles::ALL)]
], function () {
    Route::get('/', [\App\Http\Controllers\ValidatorController::class, 'index'])->name('index')->middleware('role.permission:peregrine-validation,view');
    Route::get('/{lead}/edit', [\App\Http\Controllers\ValidatorController::class, 'edit'])->name('edit')->middleware('role.permission:peregrine-validation,edit');
    Route::put('/{lead}/update', [\App\Http\Controllers\ValidatorController::class, 'update'])->name('update')->middleware('role.permission:peregrine-validation,edit');
    Route::put('/{lead}/mark-sale', [\App\Http\Controllers\ValidatorController::class, 'markAsSale'])->name('mark-sale')->middleware('role.permission:peregrine-validation,edit');
    Route::put('/{lead}/mark-forwarded', [\App\Http\Controllers\ValidatorController::class, 'markAsForwarded'])->name('mark-forwarded')->middleware('role.permission:peregrine-validation,edit');
    Route::put('/{lead}/mark-failed', [\App\Http\Controllers\ValidatorController::class, 'markAsFailed'])->name('mark-failed')->middleware('role.permission:peregrine-validation,edit');
    Route::put('/{lead}/mark-simple-declined', [\App\Http\Controllers\ValidatorController::class, 'markAsSimpleDeclined'])->name('mark-simple-declined')->middleware('role.permission:peregrine-validation,edit');
    Route::put('/{lead}/mark-home-office-sale', [\App\Http\Controllers\ValidatorController::class, 'markHomeOfficeSale'])->name('mark-home-office-sale')->middleware('role.permission:peregrine-validation,edit');
    Route::put('/{lead}/return-to-closer', [\App\Http\Controllers\ValidatorController::class, 'returnToCloser'])->name('return-to-closer')->middleware('role.permission:peregrine-validation,edit');
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

// Dock Section — READ and ADD access controlled by role.permission:dock,level
Route::group(['prefix' => 'dock', 'as' => 'dock.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [\App\Http\Controllers\Admin\DockController::class, 'index'])->name('index')->middleware('role.permission:dock,view');
    Route::post('/', [\App\Http\Controllers\Admin\DockController::class, 'store'])->name('store')->middleware('role.permission:dock,edit');
    Route::get('/history/{userId}', [\App\Http\Controllers\Admin\DockController::class, 'history'])->name('history')->middleware('role.permission:dock,view');
    Route::put('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'update'])->name('update')->middleware('role.permission:dock,edit');
    Route::patch('/{dockRecord}/cancel', [\App\Http\Controllers\Admin\DockController::class, 'cancel'])->name('cancel')->middleware('role.permission:dock,edit');
    Route::delete('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'destroy'])->name('destroy')->middleware('role.permission:dock,full');
});

// Employee Dock View - Read-only access for employees to view their own dock records
Route::get('/my-dock-records', [\App\Http\Controllers\Admin\DockController::class, 'myDockRecords'])->name('my-dock-records')->middleware(['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::MANAGER, Roles::EMPLOYEE, Roles::RAVENS_CLOSER, Roles::PEREGRINE_CLOSER, Roles::PEREGRINE_VALIDATOR, Roles::VERIFIER, Roles::QA, Roles::RETENTION_OFFICER, Roles::HR, Roles::COORDINATOR)]);

// My Devices — every logged-in user can see and name their own device
Route::get('/my-devices', [\App\Http\Controllers\Admin\DeviceController::class, 'myDevices'])->name('my-devices')->middleware('auth');
Route::post('/my-devices/name', [\App\Http\Controllers\Admin\DeviceController::class, 'updateMyDeviceName'])->name('my-devices.name')->middleware('auth');

// Device token retrieval — no auth needed, bypassed by RestrictToAllowedDevice middleware
Route::get('/device/get-token', [\App\Http\Controllers\Admin\DeviceController::class, 'getToken'])->name('device.get-token');

// Device activation — no auth needed, bypassed by RestrictToAllowedDevice middleware
Route::post('/device/activate', [\App\Http\Controllers\Admin\DeviceController::class, 'activate'])->name('device.activate');

// Attendance — access controlled by role.permission:attendance,level
Route::group(['prefix' => 'attendance', 'as' => 'attendance.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index')->middleware('role.permission:attendance,view');
    Route::get('/history', [AttendanceController::class, 'history'])->name('history')->middleware('role.permission:attendance,view');
    Route::get('/print-view', [AttendanceController::class, 'printView'])->name('print-view')->middleware('role.permission:attendance,view');
    Route::get('/print', [AttendanceController::class, 'print'])->name('print')->middleware('role.permission:attendance,view');
    Route::get('/employee-report/{userId}', [AttendanceController::class, 'employeeReport'])->name('employee-report')->middleware('role.permission:attendance,view');
    Route::get('/export', [AttendanceController::class, 'index'])->name('export')->middleware('role.permission:attendance,view');
    Route::get('/{id}/json', [AttendanceController::class, 'json'])->name('json')->middleware('role.permission:attendance,view');
    
    // Manual entry, editing, and deleting — controlled by role.permission:attendance,edit/full
    Route::get('/mark-manual', [AttendanceController::class, 'index'])->name('mark-manual')->middleware('role.permission:attendance,edit');
    Route::post('/mark-manual', [AttendanceController::class, 'markManual'])->name('mark-manual.post')->middleware('role.permission:attendance,edit');
    Route::post('/{id}/update', [AttendanceController::class, 'updateAjax'])->name('update')->middleware('role.permission:attendance,edit');
    Route::delete('/{id}', [AttendanceController::class, 'delete'])->name('delete')->middleware('role.permission:attendance,full');
    // Bulk calendar attendance marking
    Route::get('/bulk-month-data', [AttendanceController::class, 'getUserMonthAttendance'])->name('bulk-month-data')->middleware('role.permission:attendance,edit');
    Route::post('/bulk-mark', [AttendanceController::class, 'bulkMarkAttendance'])->name('bulk-mark.post')->middleware('role.permission:attendance,edit');
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

// EPMS - Effective Project Management System — access controlled by role.permission:epms,level
Route::group(['prefix' => 'epms', 'as' => 'epms.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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

// Payroll - View Access — controlled by role.permission:payroll,level
Route::group(['prefix' => 'payroll', 'as' => 'payroll.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [SalaryController::class, 'payroll'])->name('index')->middleware('role.permission:payroll,view');
    Route::get('/print', [SalaryController::class, 'printPayroll'])->name('print')->middleware('role.permission:payroll,view');
    Route::get('/export', [SalaryController::class, 'exportXlsx'])->name('export')->middleware('role.permission:payroll,view');
    Route::post('/working-days', [SalaryController::class, 'updateWorkingDays'])->name('working-days.update')->middleware('role.permission:payroll,edit');
    
    // Manual Payroll Entries (for non-system users like ex-employees)
    Route::post('/manual', [SalaryController::class, 'storeManualEntry'])->name('manual.store')->middleware('role.permission:payroll,edit');
    Route::put('/manual/{id}', [SalaryController::class, 'updateManualEntry'])->name('manual.update')->middleware('role.permission:payroll,edit');
    Route::delete('/manual/{id}', [SalaryController::class, 'destroyManualEntry'])->name('manual.destroy')->middleware('role.permission:payroll,full');
    
    Route::match(['post', 'put'], '/{userId}', [SalaryController::class, 'updatePayroll'])->name('update')->middleware('role.permission:payroll,edit');
});

// Chart of Accounts — access controlled by role.permission:chart-of-accounts,level
Route::group(['prefix' => 'chart-of-accounts', 'as' => 'chart-of-accounts.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [ChartOfAccountController::class, 'index'])->name('index')->middleware('role.permission:chart-of-accounts,view');
    Route::get('/create', [ChartOfAccountController::class, 'create'])->name('create')->middleware('role.permission:chart-of-accounts,edit');
    Route::post('/store', [ChartOfAccountController::class, 'store'])->name('store')->middleware('role.permission:chart-of-accounts,edit');
    Route::get('/show/{id}', [ChartOfAccountController::class, 'show'])->name('show')->middleware('role.permission:chart-of-accounts,view');
    Route::get('/edit/{id}', [ChartOfAccountController::class, 'edit'])->name('edit')->middleware('role.permission:chart-of-accounts,edit');
    Route::put('/update/{id}', [ChartOfAccountController::class, 'update'])->name('update')->middleware('role.permission:chart-of-accounts,edit');
    Route::delete('/delete/{id}', [ChartOfAccountController::class, 'destroy'])->name('delete')->middleware('role.permission:chart-of-accounts,full');
});

// Ledger — access controlled by role.permission:general-ledger,level
Route::group(['prefix' => 'ledger', 'as' => 'ledger.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [LedgerController::class, 'index'])->name('index')->middleware('role.permission:general-ledger,view');
    Route::get('/create', [LedgerController::class, 'create'])->name('create')->middleware('role.permission:general-ledger,edit');
    Route::post('/store', [LedgerController::class, 'store'])->name('store')->middleware('role.permission:general-ledger,edit');
    Route::get('/show/{id}', [LedgerController::class, 'show'])->name('show')->middleware('role.permission:general-ledger,view');
    Route::get('/export', [LedgerController::class, 'export'])->name('export')->middleware('role.permission:general-ledger,view');
    Route::get('/summary', [LedgerController::class, 'summary'])->name('summary')->middleware('role.permission:general-ledger,view');
});

// Petty Cash Ledger — access controlled by role.permission:petty-cash,level
Route::group(['prefix' => 'petty-cash', 'as' => 'petty-cash.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [LedgerController::class, 'pettyCashIndex'])->name('index')->middleware('role.permission:petty-cash,view');
    Route::post('/store', [LedgerController::class, 'pettyCashStore'])->name('store')->middleware('role.permission:petty-cash,edit');
    Route::get('/{id}/edit', [LedgerController::class, 'pettyCashEdit'])->name('edit')->middleware('role.permission:petty-cash,edit');
    Route::put('/{id}', [LedgerController::class, 'pettyCashUpdate'])->name('update')->middleware('role.permission:petty-cash,edit');
    Route::delete('/{id}', [LedgerController::class, 'pettyCashDestroy'])->name('destroy')->middleware('role.permission:petty-cash,full');
    Route::get('/print', [LedgerController::class, 'pettyCashPrint'])->name('print')->middleware('role.permission:petty-cash,view');
    Route::get('/export', [LedgerController::class, 'pettyCashExport'])->name('export')->middleware('role.permission:petty-cash,view');
});

// Accounting Ledger (Double-Entry) — access controlled by role.permission:accounting,level
Route::group(['prefix' => 'admin/accounting', 'as' => 'admin.accounting.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {

    // Accounting Dashboard
    Route::get('/', [LedgerJournalController::class, 'dashboard'])->name('dashboard')->middleware('role.permission:accounting,view');

    // Sales Ledger (AR sub-ledger)
    Route::get('/sales-ledger', [LedgerJournalController::class, 'salesLedger'])->name('sales-ledger')->middleware('role.permission:accounting,view');
    Route::get('/sales-ledger/{partnerId}', [LedgerJournalController::class, 'salesLedgerPartner'])->name('sales-ledger.partner')->middleware('role.permission:accounting,view');

    // Sales Returns sub-ledger (separate page)
    Route::get('/sales-returns', [LedgerJournalController::class, 'salesReturnLedger'])->name('sales-returns')->middleware('role.permission:accounting,view');

    // Journal Entries list + detail
    Route::get('/journal', [LedgerJournalController::class, 'index'])->name('journal.index')->middleware('role.permission:accounting,view');
    Route::get('/journal/create', [LedgerJournalController::class, 'createGeneral'])->name('journal.create')->middleware('role.permission:accounting,edit');
    Route::post('/journal/store', [LedgerJournalController::class, 'storeGeneral'])->name('journal.store')->middleware('role.permission:accounting,edit');
    Route::get('/journal/{id}/print', [LedgerJournalController::class, 'printEntry'])->name('journal.print')->middleware('role.permission:accounting,view');
    Route::get('/journal/{id}', [LedgerJournalController::class, 'show'])->name('journal.show')->middleware('role.permission:accounting,view');

    // Quick Entry: Sale
    Route::get('/record-sale', [LedgerJournalController::class, 'recordSaleForm'])->name('record-sale')->middleware('role.permission:accounting,edit');
    Route::post('/record-sale', [LedgerJournalController::class, 'storeSale'])->name('record-sale.store')->middleware('role.permission:accounting,edit');

    // Quick Entry: Payment Received
    Route::get('/record-payment', [LedgerJournalController::class, 'recordPaymentForm'])->name('record-payment')->middleware('role.permission:accounting,edit');
    Route::post('/record-payment', [LedgerJournalController::class, 'storePayment'])->name('record-payment.store')->middleware('role.permission:accounting,edit');

    // ChargeBack / Sales Return
    Route::get('/record-chargeback', [LedgerJournalController::class, 'recordChargebackForm'])->name('record-chargeback')->middleware('role.permission:accounting,edit');
    Route::post('/record-chargeback', [LedgerJournalController::class, 'storeChargeback'])->name('record-chargeback.store')->middleware('role.permission:accounting,edit');

    // Quick Entry: Opening Balance
    Route::get('/opening-balance', [LedgerJournalController::class, 'openingBalanceForm'])->name('opening-balance')->middleware('role.permission:accounting,edit');
    Route::post('/opening-balance', [LedgerJournalController::class, 'storeOpeningBalance'])->name('opening-balance.store')->middleware('role.permission:accounting,edit');

    // Partner Ledger Report
    Route::get('/partner-ledger', [LedgerJournalController::class, 'partnerLedgerSelect'])->name('partner-ledger')->middleware('role.permission:accounting,view');
    Route::get('/partner-ledger/{partnerId}', [LedgerJournalController::class, 'partnerLedgerShow'])->name('partner-ledger.show')->middleware('role.permission:accounting,view');
    Route::get('/partner-ledger/{partnerId}/carrier/{carrierId}', [LedgerJournalController::class, 'partnerCarrierLedgerShow'])->name('partner-ledger.carrier.show')->middleware('role.permission:accounting,view');

    // AJAX: fetch carriers for a partner
    Route::get('/partner/{partnerId}/carriers', [LedgerJournalController::class, 'getPartnerCarriers'])->name('partner.carriers');

    // Financial Reports
    Route::get('/reports/trial-balance',   [LedgerJournalController::class, 'trialBalance'])->name('reports.trial-balance')->middleware('role.permission:accounting,view');
    Route::get('/reports/profit-loss',     [LedgerJournalController::class, 'profitAndLoss'])->name('reports.profit-loss')->middleware('role.permission:accounting,view');
    Route::get('/reports/balance-sheet',   [LedgerJournalController::class, 'balanceSheet'])->name('reports.balance-sheet')->middleware('role.permission:accounting,view');
    Route::get('/reports/expense-tracker', [LedgerJournalController::class, 'expenseTracker'])->name('reports.expense-tracker')->middleware('role.permission:accounting,view');
});

// Revenue Analytics — access controlled by role.permission:revenue-analytics,level
Route::get('/revenue', [DashboardController::class, 'revenue'])
    ->name('revenue.index')
    ->middleware(['auth', Roles::middleware(...Roles::ALL), 'role.permission:revenue-analytics,view']);

// Live Analytics Dashboard — access controlled by role.permission:live-analytics,level
Route::group(['prefix' => 'analytics', 'as' => 'analytics.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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

// Hub Pages — accessible if user can view ANY child module within the hub
Route::get('/settings/hub', [SettingsController::class, 'hub'])->name('settings.hub')->middleware(['auth', Roles::middleware(...Roles::ALL)]);
Route::get('/hr/hub', function () {
    $user = auth()->user();
    if (!$user->canViewModule('hr') && !$user->canViewModule('ems') && !$user->canViewModule('attendance') && !$user->canViewModule('dock') && !$user->canViewModule('public-holidays')) {
        abort(403, "You don't have permission to view any HR module.");
    }
    return view('admin.hr.hub');
})->name('hr.hub')->middleware(['auth', Roles::middleware(...Roles::ALL)]);
Route::get('/finance/hub', function () {
    $user = auth()->user();
    if (!$user->canViewModule('finance') && !$user->canViewModule('chart-of-accounts') && !$user->canViewModule('general-ledger') && !$user->canViewModule('petty-cash') && !$user->canViewModule('payroll') && !$user->canViewModule('pabs-tickets')) {
        abort(403, "You don't have permission to view any Finance module.");
    }
    return view('admin.finance.hub');
})->name('finance.hub')->middleware(['auth', Roles::middleware(...Roles::ALL)]);

Route::get('/leads/hub', function () {
    $user = auth()->user();
    if (!$user->canViewModule('leads-peregrine') && !$user->canViewModule('leads') && !$user->canViewModule('ravens-bad-leads')) {
        abort(403, "You don't have permission to view any Leads module.");
    }
    return view('admin.leads.hub');
})->name('leads.hub')->middleware(['auth', Roles::middleware(...Roles::ALL)]);

Route::get('/sales/hub', function () {
    $user = auth()->user();
    if (!$user->canViewModule('sales') && !$user->canViewModule('qa-review') && !$user->canViewModule('issuance') && !$user->canViewModule('pendings-approved') && !$user->canViewModule('pending-draft') && !$user->canViewModule('paid-sales') && !$user->canViewModule('revenue-analytics') && !$user->canViewModule('live-analytics')) {
        abort(403, "You don't have permission to view any Sales Operations module.");
    }
    return view('admin.sales.hub');
})->name('sales.hub')->middleware(['auth', Roles::middleware(...Roles::ALL)]);

Route::get('/sales/hub/search', function (\Illuminate\Http\Request $request) {
    $q = trim($request->get('q', ''));
    if (strlen($q) < 2) {
        return response()->json(['results' => []]);
    }
    $leads = \App\Models\Lead::where(function ($query) use ($q) {
            $query->where('cn_name', 'like', "%{$q}%")
                  ->orWhere('phone_number', 'like', "%{$q}%")
                  ->orWhere('policy_number', 'like', "%{$q}%")
                  ->orWhere('ssn', 'like', "%{$q}%");
        })
        ->whereNotNull('closer_name')
        ->where(function ($q2) {
            $q2->whereNotNull('sale_at')->orWhereNotNull('sale_date');
        })
        ->select([
            'id', 'cn_name', 'phone_number', 'policy_number', 'carrier_name',
            'sale_date', 'sale_at', 'closer_name',
            'paid_at', 'policy_died_at', 'followup_done_at',
            'pending_draft_at', 'pending_contract_at', 'submission_status',
            'issuance_status', 'not_paid_at', 'status',
            'chargeback_marked_date', 'declined_at', 'decline_reason',
            'submission_at',
            // sub-status fields
            'qa_status', 'bank_verification_status',
            'not_issued_disposition', 'not_paid_fdfp_type',
            'retention_disposition',
        ])
        ->orderByDesc('sale_at')
        ->limit(15)
        ->get()
        ->map(function ($lead) {
            // Determine current pipeline stage (most specific first)
            if ($lead->policy_died_at) {
                $stage  = 'Policy Died';
                $badge  = 'danger';
                $icon   = 'bx-x-circle';
                $url    = null;
            } elseif ($lead->chargeback_marked_date) {
                $stage  = 'Chargeback';
                $badge  = 'danger';
                $icon   = 'bx-error-circle';
                $url    = route('chargebacks.index') . '?search=' . urlencode($lead->cn_name ?? '');
            } elseif ($lead->declined_at && (!$lead->pending_contract_at || $lead->declined_at > $lead->pending_contract_at)) {
                $stage  = 'Declined';
                $badge  = 'danger';
                $icon   = 'bx-block';
                $url    = route('sales.index') . '?search=' . urlencode($lead->cn_name ?? '');
            } elseif ($lead->paid_at) {
                $stage  = 'Paid Sales';
                $badge  = 'success';
                $icon   = 'bx-badge-check';
                $url    = route('paid-sales.index') . '?search=' . urlencode($lead->cn_name ?? '');
            } elseif ($lead->followup_done_at && !$lead->pending_draft_at) {
                $stage  = 'Followup Done';
                $badge  = 'info';
                $icon   = 'bx-check-circle';
                $url    = route('followup.followup-done') . '?search=' . urlencode($lead->cn_name ?? '');
            } elseif ($lead->pending_draft_at) {
                $stageName = $lead->not_paid_at ? 'Pending Draft (Not Paid)' : 'Pending Draft';
                $stage  = $stageName;
                $badge  = $lead->not_paid_at ? 'warning' : 'primary';
                $icon   = 'bx-time-five';
                $url    = route('pending-draft.index') . '?search=' . urlencode($lead->cn_name ?? '');
            } elseif ($lead->issuance_status === \App\Support\Statuses::ISSUANCE_ISSUED) {
                $stage  = 'Issued – My Followups';
                $badge  = 'primary';
                $icon   = 'bx-phone-outgoing';
                $url    = route('followup.my-followups') . '?search=' . urlencode($lead->cn_name ?? '');
            } elseif ($lead->pending_contract_at) {
                $stage  = 'Pending Contract';
                $badge  = 'warning';
                $icon   = 'bx-send';
                $url    = route('issuance.index') . '?search=' . urlencode($lead->cn_name ?? '');
            } elseif ($lead->submission_status === \App\Support\Statuses::SUB_APPROVED || !$lead->pending_contract_at) {
                $stage  = 'Pending Submission';
                $badge  = 'secondary';
                $icon   = 'bx-task';
                $url    = route('submissions.index') . '?search=' . urlencode($lead->cn_name ?? '');
            } else {
                $stage  = 'Sales Records';
                $badge  = 'secondary';
                $icon   = 'bx-dollar-circle';
                $url    = route('sales.index') . '?search=' . urlencode($lead->cn_name ?? '');
            }

            // Build chronological stage history from timestamps
            $stageTimestamps = [
                'Sale'             => $lead->sale_at,
                'Pending Sub.'     => $lead->submission_at,
                'Pending Contract' => $lead->pending_contract_at,
                'Followup Done'    => $lead->followup_done_at,
                'Pending Draft'    => $lead->pending_draft_at,
                // "Not Paid" is stamped automatically by the chargeback action —
                // suppress it from the trail when a chargeback exists to avoid the
                // misleading "Chargeback → Not Paid" sequence.
                'Not Paid'         => $lead->chargeback_marked_date ? null : $lead->not_paid_at,
                'Paid'             => $lead->paid_at,
                'Declined'         => $lead->declined_at,
                'Chargeback'       => $lead->chargeback_marked_date ? \Carbon\Carbon::parse($lead->chargeback_marked_date) : null,
                'Policy Died'      => $lead->policy_died_at,
            ];
            $stageHistory = collect($stageTimestamps)
                ->filter(fn($ts) => !is_null($ts))
                ->map(fn($ts) => \Carbon\Carbon::parse($ts))
                ->sortBy(fn($ts) => $ts->timestamp)
                ->keys()
                ->values()
                ->toArray();

            return [
                'id'            => $lead->id,
                'cn_name'       => $lead->cn_name,
                'phone_number'  => $lead->phone_number,
                'policy_number' => $lead->policy_number,
                'carrier_name'  => $lead->carrier_name,
                'sale_date'     => $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M j, Y') : null,
                'closer_name'   => $lead->closer_name,
                'stage'         => $stage,
                'badge'         => $badge,
                'icon'          => $icon,
                'url'           => $url,
                'stage_history' => $stageHistory,
                'sub_statuses'  => (function () use ($lead, $stage) {
                    $chips = [];

                    // ── Issuance status (Pending Contract stage) ──
                    if ($lead->pending_contract_at && !$lead->followup_done_at) {
                        if ($lead->not_issued_disposition) {
                            $niLabels = \App\Support\Statuses::NOT_ISSUED_DISPOSITIONS;
                            $chips[] = ['label' => 'Not Issued: ' . ($niLabels[$lead->not_issued_disposition] ?? $lead->not_issued_disposition), 'type' => 'danger'];
                        } elseif ($lead->issuance_status === \App\Support\Statuses::ISSUANCE_ISSUED) {
                            $chips[] = ['label' => 'Issued', 'type' => 'success'];
                        } elseif ($lead->issuance_status) {
                            $chips[] = ['label' => 'Issuance: ' . $lead->issuance_status, 'type' => 'warning'];
                        }
                    }

                    // ── Submission status (Pending Submission stage) ──
                    if (!$lead->pending_contract_at && $lead->submission_status && $lead->submission_status !== \App\Support\Statuses::SUB_PENDING) {
                        $subMap = [
                            \App\Support\Statuses::SUB_APPROVED     => ['Approved',      'success'],
                            \App\Support\Statuses::SUB_DECLINED      => ['Sub. Declined', 'danger'],
                            \App\Support\Statuses::SUB_UNDERWRITING  => ['Underwriting',  'warning'],
                            \App\Support\Statuses::SUB_CHARGEBACK    => ['Chargeback',    'danger'],
                        ];
                        if (isset($subMap[$lead->submission_status])) {
                            $chips[] = ['label' => $subMap[$lead->submission_status][0], 'type' => $subMap[$lead->submission_status][1]];
                        }
                    }

                    // ── FDFP type (Not Paid / Pending Draft) ──
                    if ($lead->not_paid_at && $lead->not_paid_fdfp_type) {
                        $fdfpLabels = \App\Support\Statuses::FDFP_TYPES;
                        $chips[] = ['label' => $fdfpLabels[$lead->not_paid_fdfp_type] ?? $lead->not_paid_fdfp_type, 'type' => 'warning'];
                    }

                    // ── Retention disposition (Chargeback / Not Paid) ──
                    if (($lead->chargeback_marked_date || $lead->not_paid_at) && $lead->retention_disposition) {
                        $retMap = [
                            'retained'           => ['Retained',          'success'],
                            'rewrite'            => ['Rewrite',           'info'],
                            'recalled_to_closer' => ['Recalled to Closer','info'],
                            'cancelled'          => ['Cancelled',         'muted'],
                            'in_progress'        => ['In Progress',       'primary'],
                            'unable_to_connect'  => ['UTC',               'warning'],
                            'not_answering'      => ['Not Answering',     'warning'],
                            'pending'            => ['Ret. Pending',      'muted'],
                        ];
                        if (isset($retMap[$lead->retention_disposition])) {
                            $chips[] = ['label' => $retMap[$lead->retention_disposition][0], 'type' => $retMap[$lead->retention_disposition][1]];
                        }
                    }

                    // ── Bank verification ──
                    if ($lead->bank_verification_status) {
                        $bvMap = [
                            \App\Support\Statuses::BANK_GOOD    => ['Bank: Good',    'success'],
                            \App\Support\Statuses::BANK_AVERAGE => ['Bank: Average', 'warning'],
                            \App\Support\Statuses::BANK_BAD     => ['Bank: Bad',     'danger'],
                        ];
                        if (isset($bvMap[$lead->bank_verification_status])) {
                            $chips[] = ['label' => $bvMap[$lead->bank_verification_status][0], 'type' => $bvMap[$lead->bank_verification_status][1]];
                        }
                    }

                    // ── QA status ──
                    if ($lead->qa_status && $lead->qa_status !== \App\Support\Statuses::QA_PENDING) {
                        $qaMap = [
                            \App\Support\Statuses::QA_GOOD      => ['QA: Good',      'success'],
                            \App\Support\Statuses::QA_AVG       => ['QA: Avg',       'warning'],
                            \App\Support\Statuses::QA_BAD       => ['QA: Bad',       'danger'],
                            \App\Support\Statuses::QA_IN_REVIEW => ['QA: In Review', 'info'],
                        ];
                        if (isset($qaMap[$lead->qa_status])) {
                            $chips[] = ['label' => $qaMap[$lead->qa_status][0], 'type' => $qaMap[$lead->qa_status][1]];
                        }
                    }

                    return $chips;
                })(),
            ];
        });

    return response()->json(['results' => $leads]);
})->name('sales.hub.search')->middleware(['auth', Roles::middleware(...Roles::ALL)]);

// Settings (access controlled by role.permission:settings,level — Permission Management remains Super Admin only)
Route::group(['prefix' => 'settings', 'as' => 'settings.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index')->middleware('role.permission:settings,view');
    Route::post('/', [SettingsController::class, 'update'])->name('update')->middleware('role.permission:settings,edit');
    Route::get('/themes', [SettingsController::class, 'themes'])->name('themes')->middleware('role.permission:themes,view');

    // Chat Shadowing — access controlled by role.permission:chat-shadow,level
    Route::get('/chat-shadow', [ChatShadowController::class, 'index'])->name('chat-shadow.index')->middleware('role.permission:chat-shadow,view');
    Route::get('/chat-shadow/conversations', [ChatShadowController::class, 'getConversations'])->name('chat-shadow.conversations')->middleware('role.permission:chat-shadow,view');
    Route::get('/chat-shadow/conversations/{id}/messages', [ChatShadowController::class, 'getMessages'])->name('chat-shadow.messages')->middleware('role.permission:chat-shadow,view');
    Route::get('/chat-shadow/notes', [ChatShadowController::class, 'getNotes'])->name('chat-shadow.notes')->middleware('role.permission:chat-shadow,view');
    Route::get('/chat-shadow/notepad-notes', [ChatShadowController::class, 'getNotepadNotes'])->name('chat-shadow.notepad-notes')->middleware('role.permission:chat-shadow,view');
});

// Notepad — available to all authenticated users
Route::group(['prefix' => 'notepad', 'as' => 'notepad.', 'middleware' => ['auth']], function () {
    Route::get('/', [NotepadController::class, 'index'])->name('index');
    Route::post('/', [NotepadController::class, 'store'])->name('store');
    Route::put('/{note}', [NotepadController::class, 'update'])->name('update');
    Route::delete('/{note}', [NotepadController::class, 'destroy'])->name('destroy');
    Route::get('/{note}/poll', [NotepadController::class, 'poll'])->name('poll');
    Route::get('/{note}/shares', [NotepadController::class, 'getShares'])->name('shares.get');
    Route::post('/{note}/shares', [NotepadController::class, 'updateShares'])->name('shares.update');
});

// Allowed Devices Management — access controlled by role.permission:allowed-devices,level
Route::group(['prefix' => 'settings/devices', 'as' => 'settings.devices.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [DeviceController::class, 'index'])->name('index')->middleware('role.permission:allowed-devices,view');
    Route::post('/', [DeviceController::class, 'store'])->name('store')->middleware('role.permission:allowed-devices,full');
    Route::post('/{device}/approve', [DeviceController::class, 'approve'])->name('approve')->middleware('role.permission:allowed-devices,full');
    Route::put('/{device}', [DeviceController::class, 'update'])->name('update')->middleware('role.permission:allowed-devices,edit');
    Route::post('/{device}/disable', [DeviceController::class, 'disable'])->name('disable')->middleware('role.permission:allowed-devices,edit');
    Route::post('/{device}/enable', [DeviceController::class, 'enable'])->name('enable')->middleware('role.permission:allowed-devices,edit');
    Route::delete('/{device}', [DeviceController::class, 'destroy'])->name('destroy')->middleware('role.permission:allowed-devices,full');
});

// Reports — access controlled by role.permission:reports,level
Route::group(['prefix' => 'settings/reports', 'as' => 'settings.reports.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [ReportController::class, 'hub'])->name('hub')->middleware('role.permission:reports,view');
    Route::get('/zoom-logs', [ReportController::class, 'zoomLogs'])->name('zoom-logs')->middleware('role.permission:reports,view');
    Route::get('/zoom-agent-performance', [ReportController::class, 'zoomAgentPerformance'])->name('zoom-agent-performance')->middleware('role.permission:reports,view');
    Route::get('/zoom-agent-performance/data', [ReportController::class, 'zoomAgentPerformanceData'])->name('zoom-agent-performance.data')->middleware('role.permission:reports,view');
    Route::get('/zoom-diagnostics', [ReportController::class, 'zoomDiagnostics'])->name('zoom-diagnostics')->middleware('role.permission:reports,view');
    Route::get('/submission-performance', [ReportController::class, 'submissionPerformance'])->name('submission-performance')->middleware('role.permission:reports,view');
    Route::get('/submission-performance/drilldown', [ReportController::class, 'submissionPerformanceDrilldown'])->name('submission-performance.drilldown')->middleware('role.permission:reports,view');
    Route::get('/policy-type-report', [ReportController::class, 'policyTypeReport'])->name('policy-type-report')->middleware('role.permission:reports,view');
    Route::get('/policy-type-report/drilldown', [ReportController::class, 'policyTypeReportDrilldown'])->name('policy-type-report.drilldown')->middleware('role.permission:reports,view');
    Route::get('/sales-status', [ReportController::class, 'salesStatus'])->name('sales-status')->middleware('role.permission:reports,view');
    Route::get('/sales-status/drilldown', [ReportController::class, 'salesStatusDrilldown'])->name('sales-status.drilldown')->middleware('role.permission:reports,view');
    Route::get('/disposition-report', [ReportController::class, 'dispositionReport'])->name('disposition-report')->middleware('role.permission:reports,view');
    Route::get('/manager-submission-report', [ReportController::class, 'managerSubmissionReport'])->name('manager-submission-report')->middleware('role.permission:reports,view');
    Route::get('/manager-submission-report/drilldown', [ReportController::class, 'managerSubmissionDrilldown'])->name('manager-submission-report.drilldown')->middleware('role.permission:reports,view');
    Route::get('/closer-report', [CloserReportController::class, 'index'])->name('closer-report')->middleware('role.permission:reports,view');
    Route::get('/closer-report/drilldown', [CloserReportController::class, 'drilldown'])->name('closer-report.drilldown')->middleware('role.permission:reports,view');
    Route::get('/peregrine-team-report', [ReportController::class, 'peregrineTeamReport'])->name('peregrine-team-report')->middleware('role.permission:reports,view');

    // ── Carrier Commission Sheet (standalone) ──────────
    Route::prefix('carrier-sheet')->as('carrier-sheet.')->group(function () {
        Route::get('/',      [\App\Http\Controllers\Admin\CarrierSheetController::class, 'dashboard'])->name('dashboard')->middleware('role.permission:carrier-sheet,view');
        Route::get('/rates', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'rates'])->name('rates')->middleware('role.permission:carrier-sheet,view');
        Route::post('/rates', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'storeCarrier'])->name('rates.store')->middleware('role.permission:carrier-sheet,edit');
        Route::put('/rates/{rate}', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'updateRate'])->name('rates.update')->middleware('role.permission:carrier-sheet,edit');
        Route::delete('/rates/{rate}', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'deleteCarrier'])->name('rates.destroy')->middleware('role.permission:carrier-sheet,edit');
        Route::post('/import', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'import'])->name('import')->middleware('role.permission:carrier-sheet,edit');
        Route::get('/lead-lookup', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'leadLookup'])->name('lead-lookup')->middleware('role.permission:carrier-sheet,view');
        Route::get('/{rate}', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'show'])->name('show')->middleware('role.permission:carrier-sheet,view');
        Route::get('/{rate}/export', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'export'])->name('export')->middleware('role.permission:carrier-sheet,view');
        Route::post('/{rate}/entries', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'storeEntry'])->name('entries.store')->middleware('role.permission:carrier-sheet,edit');
        Route::put('/entries/{entry}', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'updateEntry'])->name('entries.update')->middleware('role.permission:carrier-sheet,edit');
        Route::delete('/entries/{entry}', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'deleteEntry'])->name('entries.destroy')->middleware('role.permission:carrier-sheet,edit');
        Route::put('/{rate}/opening-chargeback', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'updateOpeningChargeback'])->name('opening-cb.update')->middleware('role.permission:carrier-sheet,edit');
        Route::put('/{rate}/opening-balance', [\App\Http\Controllers\Admin\CarrierSheetController::class, 'updateOpeningBalance'])->name('opening-balance.update')->middleware('role.permission:carrier-sheet,edit');
    });
});

// Permission Management (Super Admin only)
Route::group(['prefix' => 'settings/permissions', 'as' => 'settings.permissions.', 'middleware' => ['auth', Roles::middleware(Roles::SUPER_ADMIN, Roles::CEO, Roles::COORDINATOR)]], function () {
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

// Public Holidays Management — access controlled by role.permission:public-holidays,level
Route::group(['prefix' => 'admin/public-holidays', 'as' => 'admin.public-holidays.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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

// Announcement Management (Super Admin / Manager only)
Route::group(['prefix' => 'admin/announcements', 'as' => 'admin.announcements.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
    Route::post('/', [AnnouncementController::class, 'store'])->name('store');
    Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
    Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
    Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
    Route::post('/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('toggle');
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
    Route::put('/messages/{messageId}', [ChatController::class, 'updateMessage']);
    Route::post('/messages/{messageId}/forward', [ChatController::class, 'forwardMessage']);
    Route::delete('/messages/{messageId}', [ChatController::class, 'deleteMessage']);
    Route::get('/users', [ChatController::class, 'getUsers']);
    Route::get('/search', [ChatController::class, 'search']);

    // Online presence
    Route::post('/heartbeat', [ChatController::class, 'heartbeat']);

    // Message reactions
    Route::post('/messages/{messageId}/react', [ChatController::class, 'react']);

    // Pin / unpin messages
    Route::post('/messages/{messageId}/pin', [ChatController::class, 'pinMessage']);
    Route::delete('/messages/{messageId}/pin', [ChatController::class, 'unpinMessage']);

    // Typing indicator (cache-based, polled by clients)
    Route::post('/conversations/{conversationId}/typing', [ChatController::class, 'typing']);
    Route::get('/conversations/{conversationId}/typing-status', [ChatController::class, 'typingStatus']);
});

// Chargebacks — access controlled by role.permission:chargebacks,view
Route::group(['prefix' => 'chargebacks', 'as' => 'chargebacks.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [ChargebackController::class, 'index'])->name('index')->middleware('role.permission:chargebacks,view');
    Route::get('/show/{id}', [ChargebackController::class, 'show'])->name('show')->middleware('role.permission:chargebacks,view');
    Route::post('/{id}/send-to-retention', [ChargebackController::class, 'sendToRetention'])->name('sendToRetention')->middleware('role.permission:chargebacks,view');
    Route::post('/{id}/post-sales-return', [ChargebackController::class, 'postSalesReturn'])->name('postSalesReturn')->middleware('role.permission:accounting,edit');
});

// Retention — access controlled by role.permission:retention,level
Route::group(['prefix' => 'retention', 'as' => 'retention.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [RetentionController::class, 'index'])->name('index')->middleware('role.permission:retention,view');
    Route::post('/{id}/status', [RetentionController::class, 'updateStatus'])->name('updateStatus')->middleware('role.permission:retention,edit');
    Route::get('/incomplete', [RetentionController::class, 'incompleteIssuance'])->name('incomplete')->middleware('role.permission:retention,view');
    Route::get('/incomplete/{id}/details', [RetentionController::class, 'showIncompleteDetails'])->name('incompleteDetails')->middleware('role.permission:retention,view');
    Route::post('/{id}/disposition', [RetentionController::class, 'saveDisposition'])->name('saveDisposition')->middleware('role.permission:retention,edit');
    Route::get('/check-other-insurances/{id}', [RetentionController::class, 'checkOtherInsurances'])->name('checkOtherInsurances')->middleware('role.permission:retention,view');
    Route::post('/{id}/recall-to-closer', [RetentionController::class, 'recallToCloser'])->name('recallToCloser')->middleware('role.permission:retention,edit');
    Route::post('/{id}/action-status', [RetentionController::class, 'updateActionStatus'])->name('updateActionStatus')->middleware('role.permission:retention,edit');
    Route::put('/{id}', [RetentionController::class, 'update'])->name('update')->middleware('role.permission:retention,edit');
    Route::post('/{id}/set-disposition', [RetentionController::class, 'setDisposition'])->name('setDisposition')->middleware('role.permission:retention,edit');
    Route::post('/{id}/send-back-from-rewrite', [RetentionController::class, 'sendBackFromRewrite'])->name('sendBackFromRewrite')->middleware('role.permission:retention,edit');
});

// Retention Officer Dashboard
Route::get('/retention-dashboard', [RetentionDashboardController::class, 'index'])
    ->middleware(['auth', Roles::middleware(...Roles::ALL)])
    ->middleware('role.permission:retention,view')
    ->name('retention.dashboard');

// Revenue Analytics — access controlled by role.permission:revenue-analytics,level
Route::group(['prefix' => 'revenue-analytics', 'as' => 'revenue-analytics.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/', [\App\Http\Controllers\Admin\RevenueAnalyticsController::class, 'index'])->name('index')->middleware('role.permission:revenue-analytics,view');
    Route::get('/live-data', [\App\Http\Controllers\Admin\RevenueAnalyticsController::class, 'liveData'])->name('live-data')->middleware('role.permission:revenue-analytics,view');
});

// Ravens Routes — access controlled by role.permission:ravens*,level
Route::group(['prefix' => 'ravens', 'as' => 'ravens.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
    Route::get('/dashboard', [RavensDashboardController::class, 'index'])->name('dashboard')->middleware('role.permission:ravens-dashboard,view');
    Route::get('/calling', [RavensDashboardController::class, 'calling'])->name('calling')->middleware('role.permission:ravens-calling,view');
    Route::get('/leads/{leadId}/data', [RavensDashboardController::class, 'getLeadData'])->name('leads.data')->middleware('role.permission:ravens,view');
    Route::post('/leads/save', [RavensDashboardController::class, 'saveLead'])->name('leads.save')->middleware('role.permission:ravens,edit');
    Route::post('/leads/submit-sale', [RavensDashboardController::class, 'submitSale'])->name('leads.submit-sale')->middleware('role.permission:ravens,edit');
    Route::post('/leads/dispose', [RavensDashboardController::class, 'disposeLead'])->name('leads.dispose')->middleware('role.permission:ravens,edit');
    Route::post('/leads/call-dispose', [RavensDashboardController::class, 'callDispose'])->name('leads.call-dispose')->middleware('role.permission:ravens,edit');
    Route::post('/leads/restore', [RavensDashboardController::class, 'restoreLead'])->name('leads.restore')->middleware('role.permission:ravens,edit');
    Route::post('/leads/save-callback-note', [RavensDashboardController::class, 'saveCallbackNote'])->name('leads.save-callback-note');
    Route::post('/leads/record-dial', [RavensDashboardController::class, 'recordDial'])->name('leads.record-dial');
    Route::get('/leads/dial-status', [RavensDashboardController::class, 'getDialStatus'])->name('leads.dial-status');
    Route::post('/leads/acquire-lock', [RavensDashboardController::class, 'acquireLock'])->name('leads.acquire-lock');
    Route::post('/leads/release-lock', [RavensDashboardController::class, 'releaseLock'])->name('leads.release-lock');
    Route::get('/leads/my-calls-today', [RavensDashboardController::class, 'myCallsToday'])->name('leads.my-calls-today');
    Route::get('/leads/find-by-phone', [RavensDashboardController::class, 'findByPhone'])->name('leads.find-by-phone');
    Route::post('/leads/create-sale', [RavensDashboardController::class, 'createSale'])->name('leads.create-sale')->middleware('role.permission:ravens,edit');
    Route::get('/bad-leads', [RavensDashboardController::class, 'badLeads'])->name('bad-leads')->middleware('role.permission:ravens-bad-leads,view');

    // Ravens Validation — review approved/declined leads before Policy Submission
    Route::get('/validation', [\App\Http\Controllers\Admin\RavensValidationController::class, 'index'])->name('validation.index')->middleware('role.permission:ravens-validation,view');
    Route::post('/validation/{lead}/mark-valid', [\App\Http\Controllers\Admin\RavensValidationController::class, 'markValid'])->name('validation.mark-valid')->middleware('role.permission:ravens-validation,edit');
    Route::post('/validation/{lead}/keep-declined', [\App\Http\Controllers\Admin\RavensValidationController::class, 'keepDeclined'])->name('validation.keep-declined')->middleware('role.permission:ravens-validation,edit');
    Route::post('/validation/{lead}/undo', [\App\Http\Controllers\Admin\RavensValidationController::class, 'undoValidation'])->name('validation.undo')->middleware('role.permission:ravens-validation,edit');
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

    // Admin-managed app (call logs — all extensions)
    Route::get('/admin-authorize', [App\Http\Controllers\ZoomController::class, 'startAdminAuthorization'])->name('admin.authorize');

    // Recording proxy — fetches fresh signed URL from Zoom using admin token
    Route::get('/recording/{id}/play', [App\Http\Controllers\ZoomController::class, 'playRecording'])->name('recording.play');
    Route::post('/dial/{leadId}', [App\Http\Controllers\ZoomController::class, 'makeCall'])->name('dial');
    Route::get('/call-status/{leadId}', [App\Http\Controllers\ZoomController::class, 'getCallStatusByLead'])->name('call.status');    
});

// Zoom Webhook (public, no auth required)
Route::post('/zoom/webhook', [App\Http\Controllers\Admin\ZoomWebhookController::class, 'handleWebhook'])->name('zoom.webhook');

// Admin OAuth callback — must be public (session can drop during Zoom round-trip)
Route::get('/zoom/admin-callback', [App\Http\Controllers\ZoomController::class, 'adminCallback'])->name('zoom.admin.callback');

// Zoom Phone Smart Embed Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/zoom/phone', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'index'])->name('zoom.phone');
    Route::post('/zoom/phone/token', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'generateToken'])->name('zoom.phone.token');
    Route::get('/zoom/phone/search-leads', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'searchLeads'])->name('zoom.phone.search-leads');
    Route::post('/zoom/phone/match-contacts', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'matchContacts'])->name('zoom.phone.match-contacts');
    Route::post('/zoom/phone/auto-log', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'autoLog'])->name('zoom.phone.auto-log');
    Route::get('/zoom/phone/my-calls', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'myCallLogs'])->name('zoom.phone.my-calls');
    Route::get('/zoom/phone/my-dids', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'myDids'])->name('zoom.phone.my-dids');
    Route::post('/zoom/phone/set-active-number', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'setActiveNumber'])->name('zoom.phone.set-active-number');
    Route::get('/zoom/phone/recording/{id}', [App\Http\Controllers\Admin\ZoomPhoneEmbedController::class, 'getCallLogRecording'])->name('zoom.phone.call-recording');
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

// Project Authorization & Budget System (PABS) — access controlled by role.permission:pabs-tickets,level
Route::group(['prefix' => 'pabs', 'as' => 'pabs.', 'middleware' => ['auth', Roles::middleware(...Roles::ALL)]], function () {
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

// ═══════ QA SCORING SYSTEM ═══════════════════════════════════════════════════
// AI-powered call quality assurance dashboard
Route::group(['prefix' => 'qa', 'middleware' => ['auth']], function () {
    // Dashboard page (serves the SPA frontend)
    Route::get('/scoring', [\App\Http\Controllers\QA\QADashboardController::class, 'index'])->name('qa.scoring');

    // Personal QA report — closers see only their own scored calls
    Route::get('/my-report', [\App\Http\Controllers\QA\QADashboardController::class, 'myReport'])->name('qa.my-report');

    // Script editor page
    Route::get('/script', [\App\Http\Controllers\QA\QADashboardController::class, 'showScript'])->name('qa.script');

    // API endpoints
    Route::get('/api/overview', [\App\Http\Controllers\QA\QADashboardController::class, 'overview'])->name('qa.api.overview');
    Route::get('/api/agents/{id}', [\App\Http\Controllers\QA\QADashboardController::class, 'agentDetail'])->name('qa.api.agent');
    Route::get('/api/calls', [\App\Http\Controllers\QA\QADashboardController::class, 'calls'])->name('qa.api.calls');
    Route::get('/api/calls/{id}', [\App\Http\Controllers\QA\QADashboardController::class, 'callDetail'])->name('qa.api.call');
    Route::get('/api/sales', [\App\Http\Controllers\QA\QADashboardController::class, 'salesQA'])->name('qa.api.sales');
    Route::post('/api/rerun-today', [\App\Http\Controllers\QA\QADashboardController::class, 'rerunToday'])->name('qa.api.rerun-today');
    Route::get('/api/qa-status', [\App\Http\Controllers\QA\QADashboardController::class, 'qaStatus'])->name('qa.api.status');
    Route::post('/api/toggle', [\App\Http\Controllers\QA\QADashboardController::class, 'toggleQa'])->name('qa.api.toggle');
    Route::post('/api/script', [\App\Http\Controllers\QA\QADashboardController::class, 'saveScript'])->name('qa.api.script.save');
    Route::post('/api/script/reset', [\App\Http\Controllers\QA\QADashboardController::class, 'resetScript'])->name('qa.api.script.reset');

    // Manual transcript submission
    Route::get('/manual', [\App\Http\Controllers\QA\QADashboardController::class, 'showManualSubmit'])->name('qa.manual');
    Route::post('/api/manual-score', [\App\Http\Controllers\QA\QADashboardController::class, 'manualScore'])->name('qa.api.manual.score');
    Route::delete('/api/calls/{id}', [\App\Http\Controllers\QA\QADashboardController::class, 'deleteCall'])->name('qa.api.call.delete');

    // ── AssemblyAI Audio Upload & Scoring ─────────────────────────────────
    Route::get('/upload', [\App\Http\Controllers\QA\QADashboardController::class, 'showUploadScore'])->name('qa.upload');
    Route::post('/api/upload-transcribe', [\App\Http\Controllers\QA\QADashboardController::class, 'uploadAndTranscribe'])->name('qa.api.upload.transcribe');
    Route::get('/api/transcription/{qaCallId}/status', [\App\Http\Controllers\QA\QADashboardController::class, 'transcriptionStatus'])->name('qa.api.transcription.status');

    // ── Sale Linking ──────────────────────────────────────────────────────
    Route::get('/api/closer-sales', [\App\Http\Controllers\QA\QADashboardController::class, 'closerSales'])->name('qa.api.closer-sales');
    Route::post('/api/calls/{id}/link-sale', [\App\Http\Controllers\QA\QADashboardController::class, 'linkSale'])->name('qa.api.call.link-sale');
});

// Catch-all route - MUST BE LAST
Route::get('{any}', [DashboardController::class, 'index'])->where('any', '.*')->name('index');