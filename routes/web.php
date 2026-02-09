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
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\EmployeeDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RavensDashboardController;
use App\Http\Controllers\Admin\RetentionDashboardController;
use App\Http\Controllers\Admin\DupeCheckerController;
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
Route::group(['middleware' => ['auth', 'prevent.partner', 'role:CEO|Super Admin|Manager|Agent|Trainer|Employee|Ravens Closer|Peregrine Closer|Peregrine Validator|Verifier|QA|Retention Officer|Co-ordinator|HR']], function () {
    // Dashboard - redirects happen in controller based on role
    Route::get('/', [DashboardController::class, 'root'])->name('root');
    
    // API endpoint to fetch fresh KPI data for live updates
    Route::get('/dashboard/kpi-data', [DashboardController::class, 'getKpiData'])->name('dashboard.kpi-data');
});

// Team Dashboards - restricted access
Route::group(['middleware' => ['auth', 'role:CEO|Super Admin|Manager|Employee|Agent|Vendor']], function () {
    Route::get('/team/peregrine', [TeamDashboardController::class, 'peregrineTeam'])->name('team.peregrine');
    Route::get('/team/ravens', [TeamDashboardController::class, 'ravensTeam'])->name('team.ravens');
    Route::get('/closer/{userId}/details', [TeamDashboardController::class, 'closerDetails'])->name('closer.details');
});

// Agent Dashboard - Agent role only
Route::group(['prefix' => 'agent', 'as' => 'agent.', 'middleware' => ['auth', 'role:Agent']], function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
});

// Employee & Ravens Closer Routes - Only Attendance and Chat access
Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['auth', 'role:Employee|Ravens Closer']], function () {
    // Redirect to attendance dashboard
    Route::get('/dashboard', function() {
        return redirect()->route('attendance.dashboard');
    })->name('dashboard');
});

// HR Routes - Limited access to Dock, Attendance, and Public Holidays only
Route::group(['prefix' => 'hr', 'as' => 'hr.', 'middleware' => ['auth', 'role:HR']], function () {
    // HR Dashboard - redirect to attendance
    Route::get('/dashboard', function() {
        return redirect()->route('attendance.index');
    })->name('dashboard');
});

// Users Management (CEO & Super Admin & Co-ordinator)
Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['auth', 'role:CEO|Super Admin|Co-ordinator']], function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/show/{id}', [UserController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
    
    // Admin password reset (Super Admin & Manager only)
    Route::get('/{id}/reset-password', [UserController::class, 'resetPasswordForm'])->name('reset-password-form');
    Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    
    // Avatar upload
    Route::get('/{id}/upload-avatar', [UserController::class, 'uploadAvatarForm'])->name('upload-avatar-form');
    Route::post('/{id}/upload-avatar', [UserController::class, 'uploadAvatar'])->name('upload-avatar');
    
    // Update plain password via AJAX
    Route::post('/{id}/update-password', [UserController::class, 'updatePassword'])->name('update-password');
});

// Employee Management Sheet (E.M.S) - HR has view-only, others have full access
Route::group(['prefix' => 'ems', 'as' => 'employee.', 'middleware' => ['auth', 'role:CEO|Trainer|Super Admin|HR|Co-ordinator|Manager']], function () {
    // View and export - accessible to all roles in this group
    Route::get('/', [EmployeeController::class, 'index'])->name('ems');
    Route::get('/export', [EmployeeController::class, 'export'])->name('export');
    
    // Modification routes - accessible to all roles in this group (including HR)
    Route::post('/store', [EmployeeController::class, 'store'])->name('store');
    Route::post('/import', [EmployeeController::class, 'import'])->name('import');
    Route::post('/update/{employee}', [EmployeeController::class, 'update'])->name('update');
    Route::delete('/terminate/{id}', [EmployeeController::class, 'terminate'])->name('terminate');
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
});

// Dupe Checker (CEO, Super Admin and Co-ordinator)
Route::group(['prefix' => 'admin/dupe-checker', 'as' => 'admin.dupe-checker.', 'middleware' => ['auth', 'role:CEO|Super Admin|Co-ordinator']], function () {
    Route::get('/', [DupeCheckerController::class, 'index'])->name('index');
    Route::post('/self-check', [DupeCheckerController::class, 'selfCheck'])->name('self-check');
    Route::post('/file-comparison', [DupeCheckerController::class, 'fileComparison'])->name('file-comparison');
    Route::post('/run-deduplication', [DupeCheckerController::class, 'runDeduplication'])->name('run-deduplication');
});

// Account Switching Log (CEO, Super Admin and Co-ordinator)
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:CEO|Super Admin|Co-ordinator']], function () {
    Route::get('/account-switching-log', [AuditLogController::class, 'accountSwitchingLog'])->name('admin.account-switching-log');
});

// Communities Management (CEO, Managers, Super Admins, and Co-ordinators)
Route::group(['prefix' => 'admin/communities', 'as' => 'admin.communities.', 'middleware' => ['auth', 'role:CEO|Manager|Super Admin|Co-ordinator']], function () {
    Route::get('/', [\App\Http\Controllers\Admin\CommunityController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\CommunityController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\Admin\CommunityController::class, 'store'])->name('store');
    Route::get('/{community}/edit', [\App\Http\Controllers\Admin\CommunityController::class, 'edit'])->name('edit');
    Route::put('/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'update'])->name('update');
    Route::delete('/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'destroy'])->name('destroy');
});

// Communities API routes
Route::middleware('auth')->group(function () {
    Route::post('/api/communities', [\App\Http\Controllers\Admin\CommunityController::class, 'store']); // Create community via API
    Route::delete('/api/communities/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'destroy']); // Delete community
    Route::get('/api/communities/{community}/members', [\App\Http\Controllers\Admin\CommunityController::class, 'getMembers']); // Get members
    Route::post('/api/communities/{community}/members', [\App\Http\Controllers\Admin\CommunityController::class, 'addMember']); // Add member
    Route::delete('/api/communities/{community}/members/{user}', [\App\Http\Controllers\Admin\CommunityController::class, 'removeMember']); // Remove member
    Route::patch('/api/communities/{community}/members/{user}/toggle-post', [\App\Http\Controllers\Admin\CommunityController::class, 'toggleMemberPost']); // Toggle posting permission

});

// Agents Management - Redirects to Partners (agents are now managed as partners)
Route::group(['prefix' => 'agents', 'as' => 'agents.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/', function() { return redirect()->route('admin.partners.index'); })->name('index');
    Route::get('/create', function() { return redirect()->route('admin.partners.create'); })->name('create');
    Route::post('/store', function() { return redirect()->route('admin.partners.index'); })->name('store');
    Route::get('/show/{id}', function($id) { return redirect()->route('admin.partners.show', $id); })->name('show');
    Route::get('/edit/{id}', function($id) { return redirect()->route('admin.partners.edit', $id); })->name('edit');
    Route::put('/update/{id}', function($id) { return redirect()->route('admin.partners.index'); })->name('update');
    Route::delete('/delete/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'destroy'])->name('delete');
});

// Partners Management (CEO & Super Admin & Manager & Co-ordinator)
Route::group(['prefix' => 'admin/partners', 'as' => 'admin.partners.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/', [App\Http\Controllers\Admin\PartnerController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\PartnerController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\Admin\PartnerController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\Admin\PartnerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\Admin\PartnerController::class, 'destroy'])->name('destroy');
    Route::delete('/{partnerId}/carriers/{carrierId}', [App\Http\Controllers\Admin\PartnerController::class, 'removeCarrierAssignment'])->name('remove-carrier-assignment');
});

// Insurance Carriers Management (CEO & Super Admin & Manager & Co-ordinator)
Route::group(['prefix' => 'admin/insurance-carriers', 'as' => 'admin.insurance-carriers.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/', [InsuranceCarrierController::class, 'index'])->name('index');
    Route::get('/create', [InsuranceCarrierController::class, 'create'])->name('create');
    Route::post('/store', [InsuranceCarrierController::class, 'store'])->name('store');
    Route::post('/{insuranceCarrier}/toggle-active', [InsuranceCarrierController::class, 'toggleActive'])->name('toggle-active');
    Route::get('/{insuranceCarrier}', [InsuranceCarrierController::class, 'show'])->name('show');
    Route::get('/{insuranceCarrier}/edit', [InsuranceCarrierController::class, 'edit'])->name('edit');
    Route::put('/{insuranceCarrier}', [InsuranceCarrierController::class, 'update'])->name('update');
    Route::delete('/{insuranceCarrier}', [InsuranceCarrierController::class, 'destroy'])->name('destroy');
});

// Leads Management (Add/Import only - no actions)
Route::group(['prefix' => 'leads', 'as' => 'leads.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager']], function () {
    Route::get('/', [LeadController::class, 'index'])->name('index');
    Route::get('/create', [LeadController::class, 'create'])->name('create');
    Route::post('/store', [LeadController::class, 'store'])->name('store');
    Route::post('/import', [LeadController::class, 'import'])->name('import');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [LeadController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [LeadController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [LeadController::class, 'destroy'])->name('delete');
    Route::post('/{id}/status', [LeadController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{id}/update-comment', [LeadController::class, 'updateComment'])->name('updateComment');
    Route::post('/{id}/unassign-partner', [LeadController::class, 'unassignPartner'])->name('unassignPartner');
});

// Sales Management (with actions and status management)
Route::group(['prefix' => 'sales', 'as' => 'sales.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Employee|Agent|Vendor|Co-ordinator|QA']], function () {
    Route::get('/', [LeadController::class, 'sales'])->name('index');
    Route::get('/pretty-print/{id}', [LeadController::class, 'prettyPrint'])->name('prettyPrint');
    Route::post('/manual-entry', [LeadController::class, 'storeManualSale'])->name('storeManual');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [LeadController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [LeadController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [LeadController::class, 'destroy'])->name('delete');
    Route::post('/{id}/status', [LeadController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{id}/update-field', [LeadController::class, 'updateSalesField'])->name('updateField');
    Route::post('/{id}/comment', [LeadController::class, 'updateComment'])->name('updateComment');
    Route::post('/carriers/{carrierId}/status', [LeadController::class, 'updateCarrierStatus'])->name('updateCarrierStatus');
    Route::post('/update-during-call', [LeadController::class, 'updateDuringCall'])->name('updateDuringCall');
    Route::post('/forward', [LeadController::class, 'forwardLead'])->name('forwardLead');
    Route::post('/{id}/qa-status', [LeadController::class, 'updateQaStatus'])->name('updateQaStatus');
    Route::post('/{id}/qa-status/reset', [LeadController::class, 'resetQaStatus'])->name('resetQaStatus');
    Route::post('/{id}/manager-status', [LeadController::class, 'updateManagerStatus'])->name('updateManagerStatus');
    Route::post('/{id}/manager-status/reset', [LeadController::class, 'resetManagerStatus'])->name('resetManagerStatus');
    Route::post('/{id}/update-manager-reason', [LeadController::class, 'updateManagerReason'])->name('updateManagerReason');
    Route::post('/{id}/retention-sale', [LeadController::class, 'markRetentionSale'])->name('markRetentionSale');
});

// Issuance Management Routes
Route::group(['prefix' => 'issuance', 'as' => 'issuance.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/', [LeadController::class, 'issuance'])->name('index');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show');
    Route::post('/{id}/issuance-status', [LeadController::class, 'updateIssuanceStatus'])->name('updateIssuanceStatus');
    Route::post('/{id}/issuance-status/reset', [LeadController::class, 'resetIssuanceStatus'])->name('resetIssuanceStatus');
    Route::post('/{id}/unlock-field', [LeadController::class, 'unlockIssuanceField'])->name('unlockField');
    Route::post('/{id}/recalculate-commission', [LeadController::class, 'recalculateCommission'])->name('recalculateCommission');
    Route::post('/bulk-recalculate-commission', [LeadController::class, 'bulkRecalculateCommission'])->name('bulkRecalculateCommission');
});

// QA Review Routes
Route::group(['prefix' => 'qa', 'as' => 'qa.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|QA|Co-ordinator']], function () {
    Route::get('/review', [LeadController::class, 'qaReview'])->name('review');
});

// Followup Routes (everyone except Vendor/partners)
Route::group(['prefix' => 'followup', 'as' => 'followup.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator|Employee|Agent|Trainer|Ravens Closer|Peregrine Closer|Peregrine Validator|Verifier|QA|Retention Officer|HR']], function () {
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
    'middleware' => ['auth', 'role:CEO|Verifier|Super Admin|Co-ordinator']
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
    'middleware' => ['auth', 'role:CEO|Peregrine Closer|Super Admin|Co-ordinator']
], function () {
    Route::get('/', [PeregrineController::class, 'closersIndex'])->name('index');
    Route::get('/{lead}/edit', [PeregrineController::class, 'closerEdit'])->name('edit');
    Route::put('/{lead}/update', [PeregrineController::class, 'closerUpdate'])->name('update');
    Route::put('/{lead}/mark-failed', [PeregrineController::class, 'closerMarkFailed'])->name('mark-failed');
    Route::put('/{lead}/mark-pending', [PeregrineController::class, 'closerMarkPending'])->name('mark-pending');
});

// Peregrine Validator
Route::group([
    'prefix' => 'validator',
    'as' => 'validator.',
    'middleware' => ['auth', 'role:CEO|Peregrine Validator|Manager|Super Admin|Co-ordinator']
], function () {
    Route::get('/', [\App\Http\Controllers\ValidatorController::class, 'index'])->name('index');
    Route::get('/{lead}/edit', [\App\Http\Controllers\ValidatorController::class, 'edit'])->name('edit');
    Route::put('/{lead}/update', [\App\Http\Controllers\ValidatorController::class, 'update'])->name('update');
    Route::put('/{lead}/mark-sale', [\App\Http\Controllers\ValidatorController::class, 'markAsSale'])->name('mark-sale');
    Route::put('/{lead}/mark-forwarded', [\App\Http\Controllers\ValidatorController::class, 'markAsForwarded'])->name('mark-forwarded');
    Route::put('/{lead}/mark-failed', [\App\Http\Controllers\ValidatorController::class, 'markAsFailed'])->name('mark-failed');
    Route::put('/{lead}/mark-simple-declined', [\App\Http\Controllers\ValidatorController::class, 'markAsSimpleDeclined'])->name('mark-simple-declined');
    Route::put('/{lead}/mark-home-office-sale', [\App\Http\Controllers\ValidatorController::class, 'markHomeOfficeSale'])->name('mark-home-office-sale');
    Route::put('/{lead}/return-to-closer', [\App\Http\Controllers\ValidatorController::class, 'returnToCloser'])->name('return-to-closer');
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
Route::group(['prefix' => 'dock', 'as' => 'dock.', 'middleware' => ['auth', 'role:Super Admin|Manager|QA|HR|Co-ordinator|CEO']], function () {
    Route::get('/', [\App\Http\Controllers\Admin\DockController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Admin\DockController::class, 'store'])->name('store');
    Route::get('/history/{userId}', [\App\Http\Controllers\Admin\DockController::class, 'history'])->name('history');
});

// Dock Section - EDIT and DELETE access for Super Admin, Manager, QA, Co-ordinator only (NOT HR)
Route::group(['prefix' => 'dock', 'as' => 'dock.', 'middleware' => ['auth', 'role:Super Admin|Manager|QA|Co-ordinator']], function () {
    Route::put('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'update'])->name('update');
    Route::patch('/{dockRecord}/cancel', [\App\Http\Controllers\Admin\DockController::class, 'cancel'])->name('cancel');
    Route::delete('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'destroy'])->name('destroy');
});

// Employee Dock View - Read-only access for employees to view their own dock records
Route::get('/my-dock-records', [\App\Http\Controllers\Admin\DockController::class, 'myDockRecords'])->name('my-dock-records')->middleware(['auth', 'role:Super Admin|Manager|Employee|Agent|Ravens Closer|Peregrine Closer|Peregrine Validator|Verifier|QA|Retention Officer|HR|Vendor|Co-ordinator']);

// Attendance
Route::group(['prefix' => 'attendance', 'as' => 'attendance.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Employee|Agent|HR|Vendor|Retention Officer|Ravens Closer|Peregrine Closer|Peregrine Validator|Verifier|QA|Co-ordinator']], function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('/history', [AttendanceController::class, 'history'])->name('history');
    Route::get('/employee-report/{userId}', [AttendanceController::class, 'employeeReport'])->name('employee-report');
    Route::get('/export', [AttendanceController::class, 'index'])->name('export');
    Route::get('/{id}/json', [AttendanceController::class, 'json'])->name('json');
    
    // Manual entry, editing, and deleting - CEO, Super Admin & Co-ordinator
    Route::middleware(['role:CEO|Super Admin|Co-ordinator'])->group(function () {
        Route::get('/mark-manual', [AttendanceController::class, 'index'])->name('mark-manual');
        Route::post('/mark-manual', [AttendanceController::class, 'markManual'])->name('mark-manual.post');
        Route::post('/{id}/update', [AttendanceController::class, 'updateAjax'])->name('update');
        Route::delete('/{id}', [AttendanceController::class, 'delete'])->name('delete');
    });
});

// Notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware(['auth', 'role:CEO|Super Admin|Manager|Employee|Agent|Vendor|Co-ordinator']);

// API routes for AJAX requests
Route::prefix('api/notifications')->name('api.notifications.')->middleware(['auth', 'role:Super Admin|Manager|Employee|Agent|Vendor|Ravens Closer|Peregrine Closer|Peregrine Validator|Verifier|Retention Officer|QA|Co-ordinator'])->group(function () {
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
Route::group(['prefix' => 'epms', 'as' => 'epms.', 'middleware' => ['auth', 'role:CEO|Super Admin']], function () {
    // Project CRUD
    Route::get('/', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'destroy'])->name('destroy');
    
    // Milestones
    Route::post('/{id}/milestones', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addMilestone'])->name('milestones.store');
    Route::post('/{id}/milestones/{milestoneId}/update-date', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'updateMilestoneDate'])->name('milestones.update-date');
    
    // Tasks
    Route::post('/{id}/tasks', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addTask'])->name('tasks.store');
    Route::post('/{id}/tasks/{taskId}/status', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'updateTaskStatus'])->name('tasks.update-status');
    Route::post('/{id}/tasks/{taskId}/dates', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'updateTaskDates'])->name('tasks.update-dates');
    
    // Task Dependencies
    Route::post('/{id}/dependencies', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addTaskDependency'])->name('dependencies.store');
    
    // External Costs
    Route::post('/{id}/costs', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'addExternalCost'])->name('costs.store');
    
    // Gantt Data API
    Route::get('/{id}/gantt-data', [\App\Http\Controllers\Admin\EPMSProjectController::class, 'getGanttData'])->name('gantt-data');
});

// Payroll - View Access (CEO, Super Admin, Co-ordinator & Manager can view)
Route::group(['prefix' => 'payroll', 'as' => 'payroll.', 'middleware' => ['auth', 'role:CEO|Super Admin|Co-ordinator|Manager']], function () {
    Route::get('/', [SalaryController::class, 'payroll'])->name('index');
    Route::get('/print', [SalaryController::class, 'printPayroll'])->name('print');
});

// Payroll - Edit Access (Only CEO, Super Admin & Co-ordinator can edit)
Route::group(['prefix' => 'payroll', 'as' => 'payroll.', 'middleware' => ['auth', 'role:CEO|Super Admin|Co-ordinator']], function () {
    Route::post('/working-days', [SalaryController::class, 'updateWorkingDays'])->name('working-days.update');
    
    // Manual Payroll Entries (for non-system users like ex-employees) - MUST come before /{userId} route
    Route::post('/manual', [SalaryController::class, 'storeManualEntry'])->name('manual.store');
    Route::put('/manual/{id}', [SalaryController::class, 'updateManualEntry'])->name('manual.update');
    Route::delete('/manual/{id}', [SalaryController::class, 'destroyManualEntry'])->name('manual.destroy');
    
    Route::match(['post', 'put'], '/{userId}', [SalaryController::class, 'updatePayroll'])->name('update');
});

// Vendors
Route::group(['prefix' => 'vendors', 'as' => 'vendors.', 'middleware' => ['auth', 'role:Super Admin|Manager|Co-ordinator|Manager']], function () {
    Route::get('/', [VendorController::class, 'index'])->name('index');
    Route::get('/create', [VendorController::class, 'create'])->name('create');
    Route::post('/store', [VendorController::class, 'store'])->name('store');
    Route::get('/show/{id}', [VendorController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [VendorController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [VendorController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [VendorController::class, 'destroy'])->name('delete');
});

// Chart of Accounts
Route::group(['prefix' => 'chart-of-accounts', 'as' => 'chart-of-accounts.', 'middleware' => ['auth', 'role:Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/', [ChartOfAccountController::class, 'index'])->name('index');
    Route::get('/create', [ChartOfAccountController::class, 'create'])->name('create');
    Route::post('/store', [ChartOfAccountController::class, 'store'])->name('store');
    Route::get('/show/{id}', [ChartOfAccountController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [ChartOfAccountController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [ChartOfAccountController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [ChartOfAccountController::class, 'destroy'])->name('delete');
});

// Ledger
Route::group(['prefix' => 'ledger', 'as' => 'ledger.', 'middleware' => ['auth', 'role:Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/', [LedgerController::class, 'index'])->name('index');
    Route::get('/create', [LedgerController::class, 'create'])->name('create');
    Route::post('/store', [LedgerController::class, 'store'])->name('store');
    Route::get('/show/{id}', [LedgerController::class, 'show'])->name('show');
    Route::get('/vendor/{vendorId}', [LedgerController::class, 'vendorLedger'])->name('vendor');
    Route::get('/export', [LedgerController::class, 'export'])->name('export');
    Route::get('/summary', [LedgerController::class, 'summary'])->name('summary');
});

// Petty Cash Ledger (CEO, Super Admin & Co-ordinator only)
Route::group(['prefix' => 'petty-cash', 'as' => 'petty-cash.', 'middleware' => ['auth', 'role:CEO|Super Admin|Co-ordinator']], function () {
    Route::get('/', [LedgerController::class, 'pettyCashIndex'])->name('index');
    Route::post('/store', [LedgerController::class, 'pettyCashStore'])->name('store');
    Route::get('/{id}/edit', [LedgerController::class, 'pettyCashEdit'])->name('edit');
    Route::put('/{id}', [LedgerController::class, 'pettyCashUpdate'])->name('update');
    Route::delete('/{id}', [LedgerController::class, 'pettyCashDestroy'])->name('destroy');
    Route::get('/print', [LedgerController::class, 'pettyCashPrint'])->name('print');
    Route::get('/export', [LedgerController::class, 'pettyCashExport'])->name('export');
});

// Revenue Analytics (Super Admin & Manager only)
Route::get('/revenue', [DashboardController::class, 'revenue'])
    ->name('revenue.index')
    ->middleware(['auth', 'role:Super Admin|Manager']);

// Live Analytics Dashboard (CEO, Super Admin, Manager & Co-ordinator)
Route::group(['prefix' => 'analytics', 'as' => 'analytics.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/live', [\App\Http\Controllers\Admin\AnalyticsController::class, 'live'])->name('live');
    Route::get('/live/data', [\App\Http\Controllers\Admin\AnalyticsController::class, 'getLiveData'])->name('live.data');
    Route::get('/historical', [\App\Http\Controllers\Admin\AnalyticsController::class, 'getHistoricalData'])->name('historical');
    Route::get('/drill-down', [\App\Http\Controllers\Admin\AnalyticsController::class, 'getDrillDown'])->name('drill-down');
});

// Utility Routes
Route::get('/check-my-ip', function () {
    return response()->json([
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'message' => 'This is your current IP address.',
    ]);
});

// Settings (Super Admin only)
Route::group(['prefix' => 'settings', 'as' => 'settings.', 'middleware' => ['auth', 'role:Super Admin']], function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('/', [SettingsController::class, 'update'])->name('update');
    Route::post('/test-network', [SettingsController::class, 'testNetwork'])->name('test-network');
});

// Holidays (Super Admin and Manager and Co-ordinator)
Route::group(['prefix' => 'holidays', 'as' => 'admin.holidays.', 'middleware' => ['auth', 'role:Super Admin|Manager|Co-ordinator']], function () {
    Route::get('/', [HolidayController::class, 'index'])->name('index');
    Route::get('/create', [HolidayController::class, 'create'])->name('create');
    Route::post('/', [HolidayController::class, 'store'])->name('store');
    Route::get('/{holiday}/edit', [HolidayController::class, 'edit'])->name('edit');
    Route::put('/{holiday}', [HolidayController::class, 'update'])->name('update');
    Route::delete('/{holiday}', [HolidayController::class, 'destroy'])->name('destroy');
    Route::post('/check-date', [HolidayController::class, 'checkDate'])->name('check-date');
});

// Public Holidays Management (Super Admin only - HR and Co-ordinator can view)
Route::group(['prefix' => 'admin/public-holidays', 'as' => 'admin.public-holidays.', 'middleware' => ['auth', 'role:Super Admin|HR|Co-ordinator']], function () {
    Route::get('/', [PublicHolidayController::class, 'index'])->name('index');
    Route::get('/create', [PublicHolidayController::class, 'create'])->name('create');
    Route::post('/', [PublicHolidayController::class, 'store'])->name('store');
    Route::get('/{holiday}/edit', [PublicHolidayController::class, 'edit'])->name('edit');
    Route::put('/{holiday}', [PublicHolidayController::class, 'update'])->name('update');
    Route::delete('/{holiday}', [PublicHolidayController::class, 'destroy'])->name('destroy');
    Route::post('/{holiday}/toggle', [PublicHolidayController::class, 'toggle'])->name('toggle');
    Route::post('/check-date', [PublicHolidayController::class, 'checkDate'])->name('check-date');
    Route::get('/month', [PublicHolidayController::class, 'getMonthHolidays'])->name('month');
});

// Audit Logs (Super Admin and Co-ordinator)
Route::group(['prefix' => 'audit-logs', 'as' => 'audit-logs.', 'middleware' => ['auth', 'role:Super Admin|Co-ordinator']], function () {
    Route::get('/', [AuditLogController::class, 'index'])->name('index');
    Route::get('/{id}', [AuditLogController::class, 'show'])->name('show');
    Route::get('/export/csv', [AuditLogController::class, 'export'])->name('export');
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
Route::group(['prefix' => 'chargebacks', 'as' => 'chargebacks.', 'middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|Vendor|Co-ordinator']], function () {
    Route::get('/', [ChargebackController::class, 'index'])->name('index');
    Route::get('/show/{id}', [ChargebackController::class, 'show'])->name('show');
});

// Retention
Route::group(['prefix' => 'retention', 'as' => 'retention.', 'middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|Vendor|Retention Officer|Co-ordinator|CEO']], function () {
    Route::get('/', [RetentionController::class, 'index'])->name('index');
    Route::post('/{id}/status', [RetentionController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/incomplete', [RetentionController::class, 'incompleteIssuance'])->name('incomplete');
    Route::get('/incomplete/{id}/details', [RetentionController::class, 'showIncompleteDetails'])->name('incompleteDetails');
    Route::post('/{id}/disposition', [RetentionController::class, 'saveDisposition'])->name('saveDisposition');
    Route::get('/check-other-insurances/{id}', [RetentionController::class, 'checkOtherInsurances'])->name('checkOtherInsurances');
});

// Retention Officer Dashboard
Route::get('/retention-dashboard', [RetentionDashboardController::class, 'index'])
    ->middleware(['auth', 'role:Retention Officer|Super Admin|Co-ordinator|CEO'])
    ->name('retention.dashboard');

// Bank Verification (Super Admin Only)
Route::group(['prefix' => 'bank-verification', 'as' => 'bank-verification.', 'middleware' => ['auth', 'role:Super Admin|CEO|Manager|Co-ordinator']], function () {
    Route::get('/', [\App\Http\Controllers\Admin\BankVerificationController::class, 'index'])->name('index');
    Route::get('/{id}/show', [\App\Http\Controllers\Admin\BankVerificationController::class, 'show'])->name('show');
    Route::post('/{id}/update', [\App\Http\Controllers\Admin\BankVerificationController::class, 'updateVerification'])->name('update');
    Route::post('/{id}/assign-verifier', [\App\Http\Controllers\Admin\BankVerificationController::class, 'assignVerifier'])->name('assignVerifier');
    Route::post('/{id}/update-assignment', [\App\Http\Controllers\Admin\BankVerificationController::class, 'updateAssignmentDetails'])->name('updateAssignment');
});

// Revenue Analytics (Super Admin Only)
Route::group(['prefix' => 'revenue-analytics', 'as' => 'revenue-analytics.', 'middleware' => ['auth', 'role:Super Admin|CEO|Co-ordinator|Manager']], function () {
    Route::get('/', [\App\Http\Controllers\Admin\RevenueAnalyticsController::class, 'index'])->name('index');
});

// Ravens Routes
Route::group(['prefix' => 'ravens', 'as' => 'ravens.', 'middleware' => ['auth', 'role:Super Admin|Manager|Ravens Closer|Co-ordinator']], function () {
    Route::get('/dashboard', [RavensDashboardController::class, 'index'])->name('dashboard');
    Route::get('/calling', [RavensDashboardController::class, 'calling'])->name('calling');
    Route::get('/leads/{leadId}/data', [RavensDashboardController::class, 'getLeadData'])->name('leads.data');
    Route::post('/leads/save', [RavensDashboardController::class, 'saveLead'])->name('leads.save');
    Route::post('/leads/submit-sale', [RavensDashboardController::class, 'submitSale'])->name('leads.submit-sale');
    Route::post('/leads/dispose', [RavensDashboardController::class, 'disposeLead'])->name('leads.dispose');
    Route::get('/bad-leads', [RavensDashboardController::class, 'badLeads'])->name('bad-leads');
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
Route::group(['prefix' => 'pabs', 'as' => 'pabs.', 'middleware' => ['auth', 'role:CEO|Super Admin|Manager|Co-ordinator']], function () {
    // Tickets
    Route::get('/tickets', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [App\Http\Controllers\Admin\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [App\Http\Controllers\Admin\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::put('/tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::post('/tickets/{ticket}/add-comment', [App\Http\Controllers\Admin\TicketController::class, 'addComment'])->name('tickets.addComment');
    Route::post('/tickets/{ticket}/approve', [App\Http\Controllers\Admin\TicketController::class, 'approve'])->name('tickets.approve');
    Route::post('/tickets/{ticket}/reject', [App\Http\Controllers\Admin\TicketController::class, 'reject'])->name('tickets.reject');
    Route::post('/tickets/{ticket}/resolve', [App\Http\Controllers\Admin\TicketController::class, 'resolve'])->name('tickets.resolve');
    Route::post('/tickets/{ticket}/close', [App\Http\Controllers\Admin\TicketController::class, 'close'])->name('tickets.close');
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