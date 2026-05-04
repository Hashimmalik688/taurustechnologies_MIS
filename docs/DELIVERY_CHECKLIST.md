# Taurus MIS Delivery Checklist

Use this checklist before handing the project to future coders.

## 1) Documentation Package

- [ ] Share `docs/README.md`
- [ ] Share `docs/TAURUS_MIS_A_TO_Z.md`
- [ ] Highlight production safety rules and release runbook

## 2) Environment Confirmation

- [ ] Confirm app is in production mode
- [ ] Confirm `APP_DEBUG=false`
- [ ] Confirm queue workers are running
- [ ] Confirm config and route caches are enabled

## 3) Critical Module Smoke Test

- [ ] Login and dashboard load
- [ ] Leads and sales pages load
- [ ] Payroll page loads and edits work
- [ ] Reports pages load
- [ ] Chat/realtime updates function

## 4) Operational Notes to Hand Over

- [ ] Scheduler tasks live in `app/Console/Kernel.php`
- [ ] Route architecture starts in `routes/web.php`
- [ ] Primary risk areas: payroll, permissions, realtime, queue jobs
- [ ] Queue restart required after deploy: `php artisan queue:restart`

## 5) Final Signoff

- [ ] No pending unknown TODOs
- [ ] No unresolved production errors in logs
- [ ] Team knows rollback and escalation path
