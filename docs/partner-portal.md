# CC Partners — Outsource Sales Companies & Sale Intake

Lets **CC Partners** (outsource sales companies) submit sales into the Taurus
pipeline through the partner portal, without any access to the employee CRM.
CC Partners are **distinct** from the existing affiliate `partner`/`agent`
records — they have no ledger and no commission tracking; they only submit and
track sales.

## Concepts

The feature reuses the existing `partners` table, `partner` auth guard, and
`parent_partner_id` / `type` hierarchy — **no new tables, no schema migration**.

| `type`       | Who                          | Can do                                                        |
|--------------|------------------------------|--------------------------------------------------------------|
| `cc_partner` | CC Partner **company**       | Manage its closers, submit sales, see company submissions roll-up |
| `closer`     | A CC Partner's closer (PJC / Peregrine) | Submit sales, track own submissions               |
| `partner`    | (existing) affiliate partner | Revenue dashboard, sales, ledger — **unchanged**             |
| `agent`      | (existing) commission downline | **unchanged**                                              |

- A **CC Partner** is a `partners` row with `type='cc_partner'` and login
  credentials, created by our staff (see below).
- Its **closers** are child rows (`type='closer'`, `parent_partner_id = cc_partner.id`),
  created by the CC Partner itself from the portal's **Closers** tab. Each closer logs
  in on the same partner login.

## Where staff create CC Partners

**Sidebar → Partner Management → CC Partners** (`/admin/cc-partners`).
`Admin\CcPartnerController` — index / create / edit / toggle. Gated by the existing
`role.permission:partners` module permission (whoever manages Partners manages CC
Partners), so **no new permission/module seed is required**.

Creating a CC Partner requires a **login email + password** (unlike affiliate
partners, which are login-optional). The CC Partner then self-manages its closers
from the portal.

## Data flow of a submitted sale

A CC Partner / closer submission becomes a normal `Lead` so it enters the existing pipeline:

- `partner_id` = the submitting partner's id (closer's own id, or CC Partner id)
- `assigned_partner` = CC Partner (company) name
- `closer_name` = submitter's name, `closer_id` = `null` (partner closers are not employee `users`)
- `source = 'Partner Portal'`, `source_type = team = 'peregrine'`
- `status = 'closed'` (means *form submitted* — matching internal intake, **not** a completed sale)

The company roll-up (`Partner::salesScopeIds()`) shows the CC Partner + all its closers;
a closer sees only their own. Validation reuses `StoreLeadRequest` (via
`StorePartnerSaleRequest`), so the CC Partner form is identical to internal intake.

## Portal routes (all under `/partner`, `partner.auth` guard)

| Method | URL                          | Name                          | Who                  |
|--------|------------------------------|-------------------------------|----------------------|
| GET    | `/partner/submit-sale`       | `partner.sales.create`        | cc_partner, closer   |
| POST   | `/partner/submit-sale`       | `partner.sales.store`         | cc_partner, closer   |
| GET    | `/partner/submissions`       | `partner.submissions`         | cc_partner, closer   |
| GET    | `/partner/closers`           | `partner.closers.index`       | cc_partner only      |
| POST   | `/partner/closers`           | `partner.closers.store`       | cc_partner only      |
| PATCH  | `/partner/closers/{id}/toggle` | `partner.closers.toggle`    | cc_partner only      |
| PATCH  | `/partner/closers/{id}/reset-password` | `partner.closers.reset-password` | cc_partner only |

A `cc_partner` or `closer` logging in is redirected from the affiliate dashboard
straight to their **Submissions** roll-up.

## Branded subdomain — `cc.taurustechnologies.co`

The CC portal runs inside the **same** Laravel app — no separate codebase or database.
The subdomain is branding only; isolation is enforced by the `partner` guard
(`prevent.user` / `prevent.partner`).

1. **DNS**: point an `A`/`CNAME` record for `cc.taurustechnologies.co` at this server.
2. **Nginx**: serve the subdomain from the *same* document root as `mis.taurustechnologies.co`
   (`/var/www/taurus-crm/public`) and send its root to the partner login:

   ```nginx
   server {
       server_name cc.taurustechnologies.co;
       root /var/www/taurus-crm/public;
       index index.php;

       # Land CC Partners straight on their login
       location = / { return 302 /partner/login; }

       location / { try_files $uri $uri/ /index.php?$query_string; }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/run/php/php8.2-fpm.sock;
       }
       # add certbot TLS block as for the main site
   }
   ```
3. Leave `SESSION_DOMAIN` unset (`null`) so `cc.taurustechnologies.co` keeps its own,
   host-scoped session cookie — no change to employee sessions on `mis.` .

`TrustHosts` already trusts subdomains of `APP_URL`, so no app-config change is needed.

## Deploy notes

- **No migration** ships with this feature (`cc_partner` / `closer` are just values in
  the existing `type` string column).
- Run `php artisan config:cache && php artisan route:cache && php artisan view:cache`
  after deploy so the new routes/views register.
