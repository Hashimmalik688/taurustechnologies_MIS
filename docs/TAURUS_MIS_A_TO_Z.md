# Taurus MIS: A-to-Z Engineering Walkthrough

This document is a delivery-ready technical handover for future coders.
It is written for a live production system and focuses on safe development.

## 1) System Purpose

Taurus MIS is a Laravel-based insurance CRM that manages:

- Lead lifecycle from intake to sale and post-sale states
- Role-based team workflows (Ravens, QA, Retention, HR, Partner, Admin)
- Attendance and payroll operations
- Realtime communication and dashboards
- Zoom call ingestion and QA scoring pipelines
- Reporting and accounting modules

Production URL currently points to `mis.taurustechnologies.co`.

## 2) Current Technology Stack

- Backend: Laravel 11
- Language/runtime: PHP 8.3
- Frontend: Blade + Vite + Bootstrap 5 + jQuery-compatible assets
- Realtime: Laravel Reverb + Echo + pusher-js client
- RBAC: spatie/laravel-permission
- Data import: maatwebsite/excel
- PDF generation: barryvdh/laravel-dompdf
- Queue/session/cache: Redis

## 3) Repository Layout

Top-level structure:

- `app/` core app code (controllers, models, services, events, jobs, policies, repositories)
- `bootstrap/` app bootstrap and cache
- `config/` framework and app configuration
- `database/` schema dump and seeders
- `public/` web root and built assets
- `resources/` Blade views, JS, SCSS, static resources
- `routes/` web/api/channel/console routes
- `storage/` logs, framework cache, app files
- `tests/` feature tests
- `graphify-out/` code graph artifacts

## 4) Domain Map (What Each Area Does)

### 4.1 Leads and Sales

- Central model: `Lead`
- Main controller path: `app/Http/Controllers/Admin/LeadController.php`
- Covers leads, sales, issuance/pending-contracts, sub-status transitions

### 4.2 Ravens

- Calling and validation workflows are separated from general sales
- Key controller: `app/Http/Controllers/Admin/RavensDashboardController.php`
- Realtime lead updates are broadcast for faster multi-user sync

### 4.3 Retention

- Handles post-sale recovery and retention dispositions
- Key controller: `app/Http/Controllers/Admin/RetentionController.php`

### 4.4 QA

- QA dashboard and call scoring logic
- Transcript processing and queue jobs for analysis
- Key areas: `app/Http/Controllers/QA/`, `app/Jobs/QA/`, `app/Services/QA/`

### 4.5 Attendance and Payroll

- Attendance is tracked in attendance module
- Payroll is the active salary path (legacy salary module routes are retired)
- Key controller: `app/Http/Controllers/Admin/SalaryController.php`
- Scheduler includes absence marking and related maintenance

### 4.6 Partner Portal

- Separate partner auth guard and routes under `/partner/*`
- Partner dashboard and ledger/sales visibility are scoped by partner auth

### 4.7 Chat and Announcements

- Realtime messaging using local Reverb stack
- Chat and announcement APIs plus UI integration

### 4.8 Reports and Accounting

- Reports hub and specialized analytics views
- Accounting includes ledger/journal and finance pages

## 5) Authentication and Authorization

This project uses layered access control:

1. Authentication guards:
   - `web` for users
   - `partner` for partner accounts

2. Role middleware:
   - Route groups use role restrictions via spatie middleware wrappers

3. Module-level permission middleware:
   - `role.permission:<module>,<level>` pattern on many routes

Rule of thumb:

- Enforce primary access at route layer.
- Keep controllers focused on business logic, not duplicated role checks.

## 6) Realtime Architecture

Realtime is used in chat and MIS dashboards/reports.

- Broadcasting driver: Reverb
- Client stack: Echo + pusher-js
- Shared listener bus is initialized in layout scripts
- Pages opt in to updates; no forced global page refresh should be introduced

Current guidance:

- Keep realtime handlers page-scoped and domain-filtered.
- Avoid global reload/fade behavior from shared layout scripts.

## 7) Scheduler, Queue, and Background Jobs

Scheduled tasks are defined in `app/Console/Kernel.php`.

Important scheduled operations include:

- Attendance absent marking (`attendance:mark-absent`)
- Zoom call-log sync (`zoom:sync-call-logs`)
- QA scoring fallback queue dispatch (`qa:score-transcribed`)
- Device pending purge (`device:purge-pending`)

Queue workers should be restarted after code deploys:

```bash
php artisan queue:restart
```

## 8) Data Layer and Schema Notes

- Application uses Eloquent models with service/repository support in selected domains.
- Migration history has been squashed previously; schema dump is the baseline source.
- Treat schema and payroll-related changes as high risk.

Safe practice:

- Back up database before destructive changes.
- Validate rollback path for any new migration.

## 9) Development Workflows

### 9.1 Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run dev
php artisan serve
```

### 9.2 Build and checks

```bash
npm run build
php artisan route:list
php artisan about
vendor/bin/phpunit
```

### 9.3 Cache operations

Use these intentionally:

```bash
php artisan config:cache
php artisan route:cache
php artisan config:clear
php artisan cache:clear
```

## 10) Coding Conventions for Future Developers

1. Keep controllers thin; move heavy logic to services/repositories.
2. Keep route naming consistent with module prefixes.
3. Add middleware at route groups; avoid scattered inline authorization.
4. Do not introduce broad global JS side effects in shared layouts.
5. Prefer incremental changes with verification after each patch.
6. Preserve production behavior over aggressive refactors.

## 11) Production Safety Rules

1. Never commit `.env` or backup env files.
2. Assume every change can impact live employees and payroll.
3. Use smallest reversible patch first.
4. Verify route registration and syntax after edits.
5. Restart queue workers after deploys.
6. Re-cache config/routes only after successful checks.

## 12) Release Runbook (Minimal-Risk)

1. Pull latest code and dependencies.
2. Run build/check commands.
3. Apply database changes (if any) with rollback plan.
4. Restart queue workers.
5. Rebuild config/route caches.
6. Run smoke tests on critical pages:
   - login
   - dashboard
   - leads/sales
   - payroll
   - reports
   - chat/realtime

Suggested command sequence:

```bash
php artisan queue:restart
php artisan config:cache
php artisan route:cache
php artisan about
```

## 13) Common Incident Playbook

### Queue-backed features not updating

- Check worker status
- Run `php artisan queue:restart`
- Inspect logs in `storage/logs/laravel.log`

### Realtime not receiving updates

- Verify Reverb service is up
- Confirm broadcast driver/env config
- Check browser console for websocket errors

### Route behavior mismatches after deploy

- Clear stale cache and rebuild:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

## 14) New Developer Onboarding (Day 1 to Day 3)

### Day 1

- Read this file end to end.
- Run app locally and verify login/dashboard.
- Inspect `routes/web.php` route group patterns.

### Day 2

- Trace one module deeply (Leads or Payroll).
- Follow controller -> service -> model flow.
- Run tests and learn failure output format.

### Day 3

- Implement a small safe change.
- Validate route list, app health, and affected screens.
- Document what changed and why.

## 15) A-to-Z Quick Reference

- A: Authentication guards (`web`, `partner`)
- B: Broadcasting via Reverb
- C: Controllers under `app/Http/Controllers`
- D: Domain services in `app/Services`
- E: Events in `app/Events`
- F: Feature tests in `tests/Feature`
- G: Graph insights in `graphify-out/`
- H: Hubs route users into module areas
- I: Imports via `maatwebsite/excel`
- J: Jobs under `app/Jobs`
- K: Kernel scheduler in `app/Console/Kernel.php`
- L: Leads model/controller as central business axis
- M: Middleware-driven authorization
- N: Notifications and announcement stack
- O: Operational safety first (live users)
- P: Payroll is the active salary path
- Q: QA pipeline uses queued processing
- R: Repositories where implemented
- S: Spatie permissions for RBAC
- T: Tests before high-risk deploys
- U: User and Partner flows are separated
- V: Vite for frontend assets
- W: Web routes in `routes/web.php`
- X: eXception handling via Laravel defaults + logs
- Y: Year/month payroll period logic in payroll traits/services
- Z: Zero-trust toward destructive changes in production

## 16) Files Future Coders Should Know First

- `routes/web.php`
- `app/Http/Controllers/Admin/LeadController.php`
- `app/Http/Controllers/Admin/SalaryController.php`
- `app/Http/Controllers/QA/QADashboardController.php`
- `app/Console/Kernel.php`
- `app/Services/`
- `resources/views/layouts/`
- `config/broadcasting.php`
- `config/permission.php`
- `phpunit.xml`

## 17) Final Notes for Handover

This is a living document. Update it whenever:

- route architecture changes
- auth/permission model changes
- scheduler/queue logic changes
- release or rollback procedures change

For live systems, clarity and reversibility are more important than speed of refactor.
