# Taurus MIS Documentation Index

This folder contains handover documentation for future developers working on Taurus MIS.

## Start Here

1. `TAURUS_MIS_A_TO_Z.md`
   - Complete system walkthrough from architecture to operations.
   - Covers coding conventions, release process, troubleshooting, and production guardrails.
2. `EXECUTIVE_SUMMARY_ONE_PAGE.md`
   - One-page leadership summary for delivery meetings and stakeholder handoff.
3. `FEATURE_EDITING_GUIDE.md`
   - Detailed feature inventory plus exact guidance on where and how to edit safely.
4. `DELIVERY_CHECKLIST.md`
   - Final handoff checklist for release communication and signoff.

## Quick Notes

- App is already live in production and used daily.
- Treat all database writes and route changes as production-impacting.
- Use small, reversible changes and verify with Artisan checks after edits.

## Maintenance Commands

```bash
php artisan queue:restart
php artisan config:cache
php artisan route:cache
php artisan about
```

## Core Runtime Facts

- Framework: Laravel 11
- PHP: 8.3
- Frontend build: Vite
- Realtime: Laravel Reverb + Echo
- Auth/RBAC: spatie/laravel-permission
- Queue/cache/session drivers: Redis
