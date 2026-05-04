# Taurus MIS Feature Editing Guide (Detailed)

This guide explains what features exist, where they live in the codebase, and how to safely edit them in production-conscious workflows.

## 1) Golden Rules Before Editing Anything

1. Treat all edits as production-impacting.
2. Prefer small, reversible changes.
3. Validate route registration and app boot after changes.
4. Restart workers after queue/job code changes.
5. Rebuild config/route caches only after successful checks.

Recommended verify sequence:

```bash
php artisan route:list
php artisan about
php artisan queue:restart
php artisan config:cache
php artisan route:cache
```

## 2) Feature Inventory and Editing Map

### 2.1 Authentication, Guards, and Session Boundaries

What it does:
- Separates internal user access and partner access.
- Prevents cross-guard access leakage.

Where to edit:
- `routes/web.php` (auth route groups and middleware)
- `config/auth.php` (guards/providers)
- `app/Http/Middleware/` (access protection middleware)

Edit checklist:
1. Confirm guard target (`web` vs `partner`).
2. Verify redirect behavior for wrong-guard access.
3. Re-test login/logout and protected route access.

---

### 2.2 Role and Module Permissions (RBAC)

What it does:
- Controls who can view/edit/manage each module.
- Uses role and module permission middleware patterns.

Where to edit:
- `routes/web.php` (`role.permission:<module>,<level>` usage)
- `app/Support/Roles.php`
- `config/permission.php`
- `app/Http/Controllers/Admin/PermissionController.php`

Edit checklist:
1. Add middleware at route groups, not scattered business logic.
2. Verify permissions in both menu visibility and route access.
3. Test with at least one restricted role and one privileged role.

---

### 2.3 Leads and Sales Pipeline

What it does:
- Manages lead records and sales progression.
- Supports status transitions and post-sale operations.

Where to edit:
- `app/Http/Controllers/Admin/LeadController.php`
- `app/Models/Lead.php`
- `resources/views/admin/leads/`
- `resources/views/admin/sales/`
- `routes/web.php` (leads/sales groups)

Edit checklist:
1. Keep status enums/mappings consistent.
2. Confirm list filters and detail views still align.
3. Validate downstream modules consuming lead states.

---

### 2.4 Ravens Calling + Validation

What it does:
- Specialized high-velocity calling workflow.
- Validation path for specific role flows.

Where to edit:
- `app/Http/Controllers/Admin/RavensDashboardController.php`
- `app/Http/Controllers/Admin/RavensValidationController.php`
- `resources/views/ravens/`
- `app/Events/RavensLeadUpdated.php`

Edit checklist:
1. Preserve lock/dial/status concurrency behavior.
2. Ensure realtime event payload structure remains stable.
3. Test multi-user row updates for race-condition regressions.

---

### 2.5 Retention

What it does:
- Handles rewrite, recovery, and retention dispositions.

Where to edit:
- `app/Http/Controllers/Admin/RetentionController.php`
- `resources/views/admin/retention/`
- `routes/web.php` retention group

Edit checklist:
1. Keep disposition mappings and labels synchronized.
2. Verify transitions from chargeback/not-paid states.
3. Ensure role constraints prevent unauthorized actions.

---

### 2.6 QA Scoring Pipeline

What it does:
- Processes calls/transcripts and stores quality scoring.
- Uses queues/jobs and periodic fallback scoring dispatch.

Where to edit:
- `app/Http/Controllers/QA/QADashboardController.php`
- `app/Jobs/QA/DownloadAndProcessRecording.php`
- `app/Services/QA/`
- `app/Console/Commands/QaScoreTranscribed.php`
- `resources/views/qa/`

Edit checklist:
1. Keep queue-safe idempotent behavior.
2. Validate retries and stuck-state reset logic.
3. Confirm dashboard filters and score cards still match data model.

---

### 2.7 Attendance

What it does:
- Tracks attendance, adjustments, and reporting.

Where to edit:
- `app/Http/Controllers/Admin/AttendanceController.php`
- `app/Services/AttendanceService.php`
- `app/Models/Attendance.php`
- `resources/views/admin/attendance/`
- `routes/web.php` attendance group

Edit checklist:
1. Keep timezone assumptions explicit.
2. Validate mark/update/delete permission levels.
3. Re-check bulk operations and month-range reporting.

---

### 2.8 Payroll (Active Salary Path)

What it does:
- Calculates and updates payroll records over payroll periods.
- Supports manual entries and exports.

Where to edit:
- `app/Http/Controllers/Admin/SalaryController.php` (payroll methods)
- `app/Models/SalaryRecord.php`
- `app/Models/ManualPayrollEntry.php`
- `resources/views/admin/payroll/`
- `routes/web.php` payroll group

Important note:
- Legacy salary module views/routes are retired; payroll is the canonical path.

Edit checklist:
1. Avoid changing historical record semantics.
2. Test a full cycle: view -> update -> print/export.
3. Validate employee inclusion rules and period boundaries.

---

### 2.9 Partner Portal

What it does:
- Gives external partners controlled access to their analytics/sales/ledger.

Where to edit:
- `app/Http/Controllers/Partner/`
- `resources/views/partner/`
- `routes/web.php` `partner/*` routes

Edit checklist:
1. Confirm partner guard required on all partner routes.
2. Prevent internal-user access crossover.
3. Verify login throttling and logout flow.

---

### 2.10 Reports and Analytics

What it does:
- Provides leadership and operational insights.
- Includes realtime-aware dashboards.

Where to edit:
- `app/Http/Controllers/Admin/ReportController.php`
- `app/Http/Controllers/Admin/RevenueAnalyticsController.php`
- `app/Http/Controllers/Admin/AnalyticsController.php`
- `resources/views/admin/reports/`
- `resources/views/admin/revenue-analytics/`
- `resources/views/analytics/`

Edit checklist:
1. Keep report route names and frontend callers aligned.
2. Validate date-range filters and totals.
3. For realtime, prefer scoped listeners and debounced refresh.

---

### 2.11 Accounting (Ledger / Journal)

What it does:
- Manages accounting entries and ledger views.

Where to edit:
- `app/Http/Controllers/Admin/LedgerController.php`
- `app/Http/Controllers/Admin/LedgerJournalController.php`
- `resources/views/admin/accounting/`
- `routes/web.php` accounting groups

Edit checklist:
1. Preserve accounting entry integrity.
2. Validate print/export and filter paths.
3. Confirm role permissions on accounting routes.

---

### 2.12 Chat and Community

What it does:
- Internal communication with realtime updates.

Where to edit:
- `app/Http/Controllers/ChatController.php`
- `app/Http/Controllers/ChatNotificationController.php`
- `app/Events/MessageSent.php`
- `resources/views/chat/`

Edit checklist:
1. Avoid introducing global polling regressions.
2. Confirm channel subscriptions are scoped and cleaned up.
3. Test unread counts and conversation refresh paths.

---

### 2.13 Zoom Integration

What it does:
- OAuth/tokenized Zoom integration and call-log/recording operations.

Where to edit:
- `app/Http/Controllers/ZoomController.php`
- `app/Http/Controllers/Admin/ZoomWebhookController.php`
- `app/Services/ZoomPhoneApiService.php`
- `app/Console/Commands/SyncZoomCallLogs.php`
- `app/Console/Commands/SyncZoomRecordings.php`

Edit checklist:
1. Validate webhook auth and callback stability.
2. Ensure token refresh paths are resilient.
3. Verify scheduler + queue jobs in production.

## 3) Shared Layout and Global Behavior

High-risk global locations:

- `resources/views/layouts/master.blade.php`
- `resources/views/layouts/vendor-scripts.blade.php`

Rules:
1. Do not add global forced reload patterns for domain updates.
2. Keep visual transitions local to component/page intent.
3. Any shared JS changes require smoke tests across multiple modules.

## 4) Route Editing Workflow (Safe Pattern)

1. Add/modify route in `routes/web.php` with proper prefix/as/middleware.
2. Ensure controller method exists and returns expected shape.
3. Validate with:

```bash
php artisan route:list
php artisan about
```

4. Test exact URL + permission behavior.
5. Re-cache routes only after successful checks.

## 5) Background Task Editing Workflow

When editing scheduled commands or queue jobs:

1. Update code in `app/Console/Kernel.php` or job class.
2. Validate command signature and execution manually.
3. Restart workers:

```bash
php artisan queue:restart
```

4. Confirm no errors in logs after next schedule run window.

## 6) Adding a New Feature (Template)

1. Define route group, name prefix, and middleware.
2. Add controller endpoint(s) under proper domain folder.
3. Add service/repository if logic is non-trivial.
4. Add Blade or API response layer.
5. Add feature tests for route + auth + expected output.
6. Add documentation update in `docs/`.

## 7) Troubleshooting Quick Index

- Route missing or stale: run `php artisan route:list`, rebuild route cache.
- Realtime update not arriving: check Reverb service and browser WS logs.
- Queue behavior stale: `php artisan queue:restart`.
- Permissions mismatch: inspect role middleware + module permission middleware.
- Payroll discrepancy: verify payroll period boundaries and attendance source rows.

## 8) Delivery Expectations for Future Coders

Before merging changes:

1. Explain impacted modules and routes.
2. Provide rollback notes if business-critical.
3. Include smoke-test results in change notes.
4. Update docs in this `docs/` folder.

This ensures continuity and safe evolution of a live MIS platform.
