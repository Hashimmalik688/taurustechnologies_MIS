<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CallLogController;
use App\Http\Controllers\Admin\ChargebackController;
use App\Http\Controllers\Admin\InsuranceCarrierController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\RetentionController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\EmployeeDashboardController;
use App\Http\Controllers\Admin\RavensDashboardController;
use App\Http\Controllers\Admin\RetentionDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\VerifierController;
use App\Http\Controllers\ParaguinsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication routes (without registration)
Auth::routes(['register' => false]);

// Logout GET route to prevent page expired error
Route::get('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout.get');

// Authenticated routes - Dashboard restricted to Super Admin and Manager
Route::group(['middleware' => ['auth', 'role:Super Admin|Manager']], function () {
    // Dashboard - redirects happen in controller based on role
    Route::get('/', [DashboardController::class, 'root'])->name('root');
});

// Team Dashboards - restricted access
Route::group(['middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor']], function () {
    Route::get('/team/paraguins', [TeamDashboardController::class, 'paraguinsTeam'])->name('team.paraguins');
    Route::get('/team/ravens', [TeamDashboardController::class, 'ravensTeam'])->name('team.ravens');
    Route::get('/closer/{userId}/details', [TeamDashboardController::class, 'closerDetails'])->name('closer.details');
});

// Employee & Ravens Closer Routes - Only Attendance and Chat access
Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['auth', 'role:Employee|Ravens Closer']], function () {
    // Redirect to attendance dashboard
    Route::get('/dashboard', function() {
        return redirect()->route('attendance.dashboard');
    })->name('dashboard');
});

// Users Management (Super Admin & Manager)
Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
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
});

// Agents Management
Route::group(['prefix' => 'agents', 'as' => 'agents.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
    Route::get('/', [AgentController::class, 'index'])->name('index');
    Route::get('/create', [AgentController::class, 'create'])->name('create');
    Route::post('/store', [AgentController::class, 'store'])->name('store');
    Route::get('/show/{id}', [AgentController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [AgentController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [AgentController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [AgentController::class, 'destroy'])->name('delete');
});

// Insurance Carriers Management (Super Admin & Manager)
Route::group(['prefix' => 'admin/insurance-carriers', 'as' => 'admin.insurance-carriers.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
    Route::get('/', [InsuranceCarrierController::class, 'index'])->name('index');
    Route::get('/create', [InsuranceCarrierController::class, 'create'])->name('create');
    Route::post('/store', [InsuranceCarrierController::class, 'store'])->name('store');
    Route::get('/{insuranceCarrier}', [InsuranceCarrierController::class, 'show'])->name('show');
    Route::get('/{insuranceCarrier}/edit', [InsuranceCarrierController::class, 'edit'])->name('edit');
    Route::put('/{insuranceCarrier}', [InsuranceCarrierController::class, 'update'])->name('update');
    Route::delete('/{insuranceCarrier}', [InsuranceCarrierController::class, 'destroy'])->name('destroy');
});

// Leads Management (Add/Import only - no actions)
Route::group(['prefix' => 'leads', 'as' => 'leads.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
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
});

// Sales Management (with actions and status management)
Route::group(['prefix' => 'sales', 'as' => 'sales.', 'middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor']], function () {
    Route::get('/', [LeadController::class, 'sales'])->name('index');
    Route::get('/show/{id}', [LeadController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [LeadController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [LeadController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [LeadController::class, 'destroy'])->name('delete');
    Route::post('/{id}/status', [LeadController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{id}/comment', [LeadController::class, 'updateComment'])->name('updateComment');
    Route::post('/carriers/{carrierId}/status', [LeadController::class, 'updateCarrierStatus'])->name('updateCarrierStatus');
    Route::post('/update-during-call', [LeadController::class, 'updateDuringCall'])->name('updateDuringCall');
    Route::post('/forward', [LeadController::class, 'forwardLead'])->name('forwardLead');
    Route::post('/{id}/qa-status', [LeadController::class, 'updateQaStatus'])->name('updateQaStatus');
    Route::post('/{id}/manager-status', [LeadController::class, 'updateManagerStatus'])->name('updateManagerStatus');
    Route::post('/{id}/update-manager-reason', [LeadController::class, 'updateManagerReason'])->name('updateManagerReason');
    Route::post('/{id}/retention-sale', [LeadController::class, 'markRetentionSale'])->name('markRetentionSale');
});

// QA Review Routes
Route::group(['prefix' => 'qa', 'as' => 'qa.', 'middleware' => ['auth', 'role:Super Admin|Manager|QA']], function () {
    Route::get('/review', [LeadController::class, 'qaReview'])->name('review');
});

// Verifier Routes (only Verifier role)
Route::group([
    'prefix' => 'verifier',
    'as' => 'verifier.',
    'middleware' => ['auth', 'role:Verifier|Super Admin']
], function () {
    // Dashboard
    Route::get('/dashboard', [VerifierController::class, 'dashboard'])->name('dashboard');
    
    // Default to Paraguins for backwards compatibility
    Route::get('/create', [VerifierController::class, 'create'])->name('create');
    Route::post('/store', [VerifierController::class, 'store'])->name('store');

    // Team-specific endpoints
    Route::get('/{team}/create', [VerifierController::class, 'create'])->name('create.team');
    Route::post('/{team}/store', [VerifierController::class, 'store'])->name('store.team');
});

// Paraguins Closers
Route::group([
    'prefix' => 'paraguins/closers',
    'as' => 'paraguins.closers.',
    'middleware' => ['auth', 'role:Paraguins Closer|Super Admin']
], function () {
    Route::get('/', [ParaguinsController::class, 'closersIndex'])->name('index');
    Route::get('/{lead}/edit', [ParaguinsController::class, 'closerEdit'])->name('edit');
    Route::put('/{lead}/update', [ParaguinsController::class, 'closerUpdate'])->name('update');
    Route::put('/{lead}/mark-failed', [ParaguinsController::class, 'closerMarkFailed'])->name('mark-failed');
    Route::put('/{lead}/mark-pending', [ParaguinsController::class, 'closerMarkPending'])->name('mark-pending');
});

// Paraguins Validator
Route::group([
    'prefix' => 'validator',
    'as' => 'validator.',
    'middleware' => ['auth', 'role:Paraguins Validator|Manager|Super Admin']
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

// Dock Section - QA and HR only
Route::group(['prefix' => 'dock', 'as' => 'dock.', 'middleware' => ['auth', 'role:Super Admin|Manager|QA|HR']], function () {
    Route::get('/', [\App\Http\Controllers\Admin\DockController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Admin\DockController::class, 'store'])->name('store');
    Route::put('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'update'])->name('update');
    Route::patch('/{dockRecord}/cancel', [\App\Http\Controllers\Admin\DockController::class, 'cancel'])->name('cancel');
    Route::delete('/{dockRecord}', [\App\Http\Controllers\Admin\DockController::class, 'destroy'])->name('destroy');
    Route::get('/history/{userId}', [\App\Http\Controllers\Admin\DockController::class, 'history'])->name('history');
});

// Attendance
Route::group(['prefix' => 'attendance', 'as' => 'attendance.', 'middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor|Retention Officer']], function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('/history', [AttendanceController::class, 'history'])->name('history');
    Route::get('/mark-manual', [AttendanceController::class, 'index'])->name('mark-manual');
    // POST endpoint for users to mark attendance manually (supports force_office flag)
    Route::post('/mark-manual', [AttendanceController::class, 'markManual'])->name('mark-manual.post');
    Route::get('/employee-report', [AttendanceController::class, 'index'])->name('employee-report');
    Route::get('/export', [AttendanceController::class, 'index'])->name('export');
});

// Notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware(['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor']);

// API routes for AJAX requests
Route::prefix('api/notifications')->name('api.notifications.')->middleware(['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor'])->group(function () {
    Route::get('/topbar', [NotificationController::class, 'topbar'])->name('topbar');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::patch('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::patch('/{notification}/mark-unread', [NotificationController::class, 'markAsUnread'])->name('mark-unread');
    Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::post('/test', [NotificationController::class, 'createTest'])->name('test');
});

// Salary Management (Super Admin & Manager)
Route::group(['prefix' => 'salary', 'as' => 'salary.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
    Route::get('/', [SalaryController::class, 'index'])->name('index');
    Route::post('/calculate', [SalaryController::class, 'calculate'])->name('calculate');
    Route::get('/records', [SalaryController::class, 'records'])->name('records');
    Route::get('/records/{salaryRecord}', [SalaryController::class, 'show'])->name('show');
    Route::get('/employees', [SalaryController::class, 'employees'])->name('employees');
    Route::put('/employees/{user}', [SalaryController::class, 'updateEmployee'])->name('employees.update');
    Route::post('/records/{salaryRecord}/deductions', [SalaryController::class, 'addDeduction'])->name('deductions.store');
    Route::delete('/deductions/{deduction}', [SalaryController::class, 'removeDeduction'])->name('deductions.destroy');
    Route::patch('/records/{salaryRecord}/approve', [SalaryController::class, 'approve'])->name('approve');
    Route::patch('/records/{salaryRecord}/mark-paid', [SalaryController::class, 'markPaid'])->name('mark-paid');
    Route::get('/records/{salaryRecord}/payslip', [SalaryController::class, 'downloadPayslip'])->name('payslip');
});

// Vendors
Route::group(['prefix' => 'vendors', 'as' => 'vendors.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
    Route::get('/', [VendorController::class, 'index'])->name('index');
    Route::get('/create', [VendorController::class, 'create'])->name('create');
    Route::post('/store', [VendorController::class, 'store'])->name('store');
    Route::get('/show/{id}', [VendorController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [VendorController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [VendorController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [VendorController::class, 'destroy'])->name('delete');
});

// Ledger
Route::group(['prefix' => 'ledger', 'as' => 'ledger.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
    Route::get('/', [LedgerController::class, 'index'])->name('index');
    Route::get('/create', [LedgerController::class, 'create'])->name('create');
    Route::post('/store', [LedgerController::class, 'store'])->name('store');
    Route::get('/show/{id}', [LedgerController::class, 'show'])->name('show');
    Route::get('/vendor/{vendorId}', [LedgerController::class, 'vendorLedger'])->name('vendor');
    Route::get('/export', [LedgerController::class, 'export'])->name('export');
    Route::get('/summary', [LedgerController::class, 'summary'])->name('summary');
});

// Reports - Temporarily Disabled to avoid confusion with multiple reports pages
// Route::group(['prefix' => 'reports', 'as' => 'reports.', 'middleware' => ['auth', 'role:Super Admin|Manager']], function () {
//     Route::get('/', [ReportsController::class, 'index'])->name('index');
//     Route::get('/sales-analytics', [ReportsController::class, 'salesAnalytics'])->name('sales-analytics');
//     Route::get('/agent-performance', [ReportsController::class, 'agentPerformance'])->name('agent-performance');
//     Route::get('/revenue-tracking', [ReportsController::class, 'revenueTracking'])->name('revenue-tracking');
//     Route::get('/conversion-rates', [ReportsController::class, 'conversionRates'])->name('conversion-rates');
//     Route::get('/vendor-commissions', [ReportsController::class, 'vendorCommissions'])->name('vendor-commissions');
//     Route::get('/custom', [ReportsController::class, 'customReport'])->name('custom');
//     Route::get('/export', [ReportsController::class, 'export'])->name('export');
// });

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

// Audit Logs (Super Admin only)
Route::group(['prefix' => 'audit-logs', 'as' => 'audit-logs.', 'middleware' => ['auth', 'role:Super Admin']], function () {
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
});

// Chat API Routes (in web.php to use web session auth)
Route::group(['prefix' => 'api/chat', 'middleware' => ['auth']], function () {
    Route::get('/conversations', [ChatController::class, 'getConversations']);
    Route::post('/conversations/direct', [ChatController::class, 'getOrCreateConversation']);
    Route::post('/conversations/group', [ChatController::class, 'createGroupConversation']);
    Route::post('/groups', [ChatController::class, 'createGroup']); // Alternative endpoint
    
    // Group management routes
    Route::get('/conversations/{id}', [ChatController::class, 'getConversation']);
    Route::put('/conversations/{id}', [ChatController::class, 'updateConversation']);
    Route::delete('/conversations/{id}', [ChatController::class, 'deleteConversation']);
    Route::get('/conversations/{id}/members', [ChatController::class, 'getConversationMembers']);
    Route::post('/conversations/{id}/members', [ChatController::class, 'addMember']);
    Route::delete('/conversations/{id}/members/{userId}', [ChatController::class, 'removeMember']);
    
    Route::get('/conversations/{id}/messages', [ChatController::class, 'getMessages']);
    Route::post('/messages', [ChatController::class, 'sendMessage']);
    Route::delete('/messages/{id}', [ChatController::class, 'deleteMessage']);
    Route::get('/users', [ChatController::class, 'getUsers']);
    Route::get('/search', [ChatController::class, 'search']);
});

// Chargebacks
Route::group(['prefix' => 'chargebacks', 'as' => 'chargebacks.', 'middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor']], function () {
    Route::get('/', [ChargebackController::class, 'index'])->name('index');
    Route::get('/show/{id}', [ChargebackController::class, 'show'])->name('show');
});

// Retention
Route::group(['prefix' => 'retention', 'as' => 'retention.', 'middleware' => ['auth', 'role:Super Admin|Manager|Employee|Agent|HR|Vendor|Retention Officer']], function () {
    Route::get('/', [RetentionController::class, 'index'])->name('index');
    Route::post('/{id}/status', [RetentionController::class, 'updateStatus'])->name('updateStatus');
});

// Retention Officer Dashboard
Route::get('/retention-dashboard', [RetentionDashboardController::class, 'index'])
    ->middleware(['auth', 'role:Retention Officer|Super Admin'])
    ->name('retention.dashboard');

// Ravens Routes
Route::group(['prefix' => 'ravens', 'as' => 'ravens.', 'middleware' => ['auth', 'role:Super Admin|Manager|Ravens Closer']], function () {
    Route::get('/dashboard', [RavensDashboardController::class, 'index'])->name('dashboard');
    Route::get('/calling', [RavensDashboardController::class, 'calling'])->name('calling');
});

// Public (authenticated) attendance endpoints for all users - MUST BE BEFORE CATCH-ALL
Route::group(['middleware' => ['auth']], function () {
    // Check-in and check-out API (AJAX)
    Route::post('/attendance/check-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [\App\Http\Controllers\Admin\AttendanceController::class, 'checkOut'])->name('attendance.checkout');

    // Personal attendance dashboard (calendar + stats)
    Route::get('/attendance/dashboard', [\App\Http\Controllers\Admin\AttendanceController::class, 'dashboard'])->name('attendance.dashboard');
});

// Catch-all route - MUST BE LAST
Route::get('{any}', [DashboardController::class, 'index'])->where('any', '.*')->name('index');