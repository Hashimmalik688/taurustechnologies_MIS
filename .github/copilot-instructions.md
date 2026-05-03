# Taurus CRM - AI Agent Instructions

Laravel 11 insurance CRM with role-based access, real-time chat (Laravel Reverb), CSV lead imports, and automated salary calculations.

## Architecture & Key Components

**Tech Stack:** Laravel 11, PHP 8.3, Livewire 3, Vite, Bootstrap 5, Laravel Reverb (WebSockets)

**Core Domain Areas:**
- Lead Management (`app/Http/Controllers/Admin/LeadController.php`, `app/Models/Lead.php`)
- Sales Pipeline (`app/Http/Controllers/Admin/`) with specialized dashboards (Ravens, Retention, Employee)
- Employee Attendance (`app/Services/AttendanceService.php`, `app/Listeners/MarkAttendanceOnLogin.php`)
- Salary System (`app/Http/Controllers/Admin/SalaryController.php`) - auto-calculates based on attendance/performance
- Real-time Chat (`app/Events/MessageSent.php`, `ChatController.php`) - 100% local via Reverb, no external SaaS

**Repository Pattern:** Use `app/Repositories/*Repository.php` (implements `Contracts/*Interface.php`) for data access. Keep controllers thin - delegate to services/repositories. Example: `LeadRepository::createLead()`, `AttendanceService::isInOfficeNetwork()`.

## Role-Based Access Control

Uses `spatie/laravel-permission`. Routes use `role:` middleware extensively. Key roles:
- `Super Admin` - full access
- `Manager` - leads, reports, settings
- `Employee|Agent|Ravens Closer|Peregrine Closer` - scoped dashboards
- `Verifier|Peregrine Validator|QA|Retention Officer` - specialized functions

**Pattern:** Check `routes/web.php` for role middleware groups. Controllers inherit access from route groups. Don't add role checks in controllers unless overriding route-level rules.

## CSV Lead Import System

Located in `app/Imports/LeadsImport.php`. Uses `maatwebsite/excel` with flexible header mapping:
- Normalizes headers to snake_case (e.g., "Phone Number" → `phone_number`)
- Handles 50+ lead fields (SSN, DOB, carrier, premium dates, bank details, beneficiary, medical info)
- Uses `ImportSanitizer` utility for data cleaning
- To add fields: extend `LeadsImport::collection()` and update `Lead` model fillable

## Real-time Features (Laravel Reverb)

**Broadcasting:** Uses Laravel Reverb (local WebSocket server, not Pusher). Config: `config/broadcasting.php` → `reverb` driver.

**Events:** `app/Events/MessageSent.php`, `CallStatusChanged.php` implement `ShouldBroadcast`.

**Dev workflow:**
1. Terminal 1: `php artisan reverb:start` (WebSocket server on port 8080)
2. Terminal 2: `php artisan serve`
3. Frontend auto-connects via `laravel-echo` + `pusher-js` (despite name, works with Reverb)

**Production:** Reverb runs as systemd service. See `DEPLOY.md` for configuration. No external API keys needed.

## Developer Workflows

**Setup:**
```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run dev  # Hot reload assets
php artisan serve
```

**Chat system (local dev):**
```bash
php artisan reverb:start  # Keep running
php artisan serve         # Separate terminal
# Navigate to /chat
```

**After Blade changes:** Just refresh browser (no cache clear needed).

**After PHP controller/service changes:** `php artisan cache:clear`

**Production build:**
```bash
composer install --no-dev
npm run build  # Or npm run build-rtl for RTL languages
php artisan migrate --force
php artisan config:cache
```

**Testing:** `vendor/bin/phpunit` (uses `phpunit.xml` with in-memory cache/sync queue)

## Project Structure Patterns

**Controllers:** `app/Http/Controllers/Admin/` - organized by domain (LeadController, AttendanceController, SalaryController, etc.)

**Services:** `app/Services/` - business logic (AttendanceService, FileUploadService, ZoomService, NotificationService)

**Models:** `app/Models/` - includes ChatConversation, ChatMessage, ChatAttachment for messaging; Lead, User, Attendance, SalaryRecord, etc.

**Views:** `resources/views/` - Blade templates. `ravens/`, `employee/`, `admin/` subdirectories mirror role-based sections.

**Assets:** `resources/scss/app.scss` → `public/build/css/app.min.css` via Vite. Config: `vite.config.js` handles SCSS compilation and static copying (fonts, images).

**Imports:** `app/Imports/LeadsImport.php` - extend this pattern for other CSV imports using `ToCollection` + `WithHeadingRow` concerns.

## Common Tasks

**Add route + controller:**
1. Route: `routes/web.php` in appropriate role middleware group
2. Controller: `app/Http/Controllers/Admin/YourController.php`
3. Business logic: `app/Services/YourService.php` (if complex)
4. Data access: `app/Repositories/YourRepository.php` with interface

**Modify lead import:**
- Edit `app/Imports/LeadsImport.php` collection method
- Add column mappings using `$normalizedKey` pattern
- Update `Lead` model `$fillable` if adding DB fields

**Add real-time event:**
1. Create event: `php artisan make:event YourEvent` implementing `ShouldBroadcast`
2. Define `broadcastOn()` with channel (`PresenceChannel`, `PrivateChannel`)
3. Fire event in controller/service: `event(new YourEvent($data))`
4. Frontend: listen via Laravel Echo (see `resources/js` for examples)

**Debug chat not working:**
- Check `php artisan reverb:start` is running
- Verify `.env`: `BROADCAST_DRIVER=reverb`, `REVERB_APP_KEY`, etc.
- Check browser console for WebSocket errors
- Logs: `storage/logs/laravel.log`

## Critical Files

- `routes/web.php` - all web routes with role-based grouping
- `app/Imports/LeadsImport.php` - CSV import with header normalization
- `config/broadcasting.php` - Reverb WebSocket config
- `DEPLOY.md` - complete production deployment guide (DNS, Nginx, SSL, Reverb systemd)
- `CHAT_SETUP.md` - local chat development setup
- `IMPLEMENTATION_TASKS.md` - pending feature work (Sales filters, retention fields)

## Safety Rules

- **Never commit `.env`** - proprietary project, contains production credentials
- **DEPLOY.md changes** → flag for review (production impact)
- **DB migrations** → always test rollback before deploying
- **Broadcasting changes** → test with Reverb running locally first
- **Role/permission changes** → verify route middleware and existing role checks

## Debugging

- **Laravel logs:** `storage/logs/laravel.log`
- **Vite build errors:** Check `vite.config.js`, ensure `npm install` ran
- **Reverb WebSocket:** Check port 8080 isn't blocked, verify `.env` REVERB_* vars
- **Import failures:** Check `storage/logs/laravel.log` for row-level errors in LeadsImport
- **Permission denied:** User missing role assignment - check `spatie/laravel-permission` tables

## Knowledge Graph (RAG)

This codebase has a live knowledge graph at `graphify-out/graph.json` (3,286 nodes, 5,507 edges, 428 communities — app code only, third-party libs excluded). Use it. It saves reading raw files.

### Mandatory rules — follow these every session

**Before using grep_search or file_search, ALWAYS run graphify first:**
```bash
# Find connections between two things
graphify path "LeadController" "Lead" --graph /var/www/taurus-crm/graphify-out/graph.json

# Broad architecture question
graphify query "how does salary calculation work" --graph /var/www/taurus-crm/graphify-out/graph.json --budget 500

# What does a class/file do and what does it connect to
graphify explain "AttendanceService" --graph /var/www/taurus-crm/graphify-out/graph.json
```

**At the start of any architecture or multi-file question, read the report first:**
```
read_file: graphify-out/GRAPH_REPORT.md  (lines 100–300)
```
This gives god nodes (highest-degree = most important classes), community clusters, and cross-module connections in one read — no grepping needed.

**Decision tree — use this order, stop as soon as you have enough:**
1. `graphify path A B` — for "how does X connect to Y" questions
2. `graphify query "..."` — for "what does X do / what touches X" questions
3. `graphify explain "X"` — for "what is X and what does it relate to"
4. Read `graphify-out/GRAPH_REPORT.md` — for session-start orientation
5. `grep_search` / `file_search` — ONLY if graph didn't give enough detail
6. `read_file` on specific file — ONLY after you know from graph which file to read

**Never** scan directories blindly or read multiple files speculatively when the graph can tell you which one to open.

**After modifying code files**, run to keep the graph current (AST-only, no API cost):
```bash
cd /var/www/taurus-crm && graphify update .
```

### Key nodes to know (top god nodes by connection count)
- `Lead` — central model, 51 connections
- `LeadController` — main entry point, 42 connections
- `EPMSProject` — project management module, 54 connections
- `Attendance` — attendance tracking, 50 connections
- `QaCall` — QA pipeline, 43 connections
- `AuditLog` — audit trail, 35 connections
- `QADashboardController` — QA reporting hub, 34 connections

### graphify

Type `/graphify` in Copilot Chat to rebuild the knowledge graph.
Obsidian vault (visual): `/var/www/taurus-crm/obsidian-vaults/taurus-crm/` — open in Obsidian, start from `index.md`.

