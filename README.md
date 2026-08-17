# Courier by NETPACK

Courier by NETPACK is a Laravel courier-management platform for domestic, international, e-commerce, partner, and rider delivery operations in Nepal. The codebase includes shipment creation, service rates, pickup requests, customer and seller portals, partner workflows, rider operations, HAWB/manifest/POD records, tracking, payments, reminders, and administration.

> Project status: active stabilization. The database can be rebuilt and the current automated checks pass, but the product is not yet approved for a public production launch. See **Known gaps** below.

## Technology

- PHP 8.3+
- Laravel 12
- MariaDB 10.4+ or MySQL 8
- Blade, Vite, JavaScript, and CSS
- Laravel Sanctum for API tokens
- Endroid QR Code and Milon Barcode for shipment documents
- Dompdf for printable documents
- Laravel queues and scheduler for asynchronous notifications/reminders

## Application areas

- `app/Http/Controllers/Domestic` — domestic operations, rates, zones, pickups, and manifests
- `app/Http/Controllers/International` and `app/Http/Controllers/Overseas` — international rates, partners, hubs, and transit
- `app/Http/Controllers/Ecommerce`, `Seller`, and `Rider` — seller orders and last-mile delivery
- `app/Http/Controllers/Admin`, `Client`, and `Partner` — role-specific operational portals
- `app/Services` — pricing, payment, HAWB, search, reminder, and tracking-number services
- `app/Models` — Eloquent data model
- `resources/views` — Blade pages grouped by application area
- `routes/web.php` and `routes/api.php` — browser and reviewed API endpoints
- `database/migrations` and `database/seeders` — schema and local demo accounts
- `tests` — unit and feature tests

## Local installation

Requirements: PHP 8.3 with the Laravel-required extensions, Composer 2, Node.js 20+, npm, and MariaDB/MySQL.

```bash
git clone https://github.com/kirannetpack-ui/COURIER-by-NETPACK.git
cd COURIER-by-NETPACK
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Create a local database named `netpack_db`, set its connection values in `.env`, then run:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

For active frontend development, use `npm run dev`. Never commit `.env`, real API keys, uploaded customer documents, production data, or generated backups.

## Local demo accounts

These accounts are created only by `php artisan db:seed`. They are application logins, not real email mailboxes. Every account is approved but must change its temporary password on first login.

| Role | Email | Temporary password |
|---|---|---|
| Super administrator | `superadmin@netpack.test` | `Netpack!Admin#2026` |
| Domestic administrator | `domestic.admin@netpack.test` | `Netpack!Domestic#2026` |
| International administrator | `international.admin@netpack.test` | `Netpack!International#2026` |
| Operations staff | `staff@netpack.test` | `Netpack!Staff#2026` |
| Domestic partner | `partner@netpack.test` | `Netpack!Partner#2026` |
| Overseas partner | `overseas@netpack.test` | `Netpack!Overseas#2026` |
| E-commerce seller | `seller@netpack.test` | `Netpack!Seller#2026` |
| Delivery rider | `rider@netpack.test` | `Netpack!Rider#2026` |
| Customer | `customer@netpack.test` | `Netpack!Customer#2026` |
| Business client | `client@netpack.test` | `Netpack!Client#2026` |

Demo seeding is blocked in production unless `ALLOW_DEMO_SEEDING=true` is deliberately set. Do not enable that flag on a real deployment.

## Tracking and HAWB numbering

Tracking numbers are generated from an atomic database sequence and contain a service prefix, four-digit year, six-digit sequence, and check digit:

- Domestic: `NPD-2026-000001-8`
- E-commerce: `NPE-2026-000001-6`
- International: `NPI-2026-000001-4`

The final digit detects common transcription errors. Numbers are never generated with random retries.

International HAWBs use a destination prefix, year, and at least a three-digit sequence:

- USA and Canada: `USNP-2026-001`
- United Kingdom: `UKNP-2026-001`
- Europe: `EUNP-2026-001`
- Australia: `AUNP-2026-001`
- Configurable international fallback: `INNP-2026-001`

Mappings and widths are configured in `config/tracking.php`. QR codes are rendered locally and resolve to the exact public tracking URL. HAWB views and operational scan updates require an authenticated, authorized shipment relationship.

## Email and notifications

Local development defaults to Mailpit. A low-volume pilot can use `app.netpack@gmail.com` with Google two-step verification and an app password:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=app.netpack@gmail.com
MAIL_PASSWORD=replace-with-google-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=app.netpack@gmail.com
```

Do not use the normal Gmail password. For production, use a dedicated transactional provider such as Postmark or Amazon SES with a NETPACK-owned sending domain and configured SPF, DKIM, and DMARC. Gmail is not suitable for reliable high-volume operational email.

Queued mail and reminders require a running queue worker and scheduler:

```bash
php artisan queue:work --tries=3
php artisan schedule:work
```

## AI assistant architecture

The assistant is planned but is not yet implemented. The approved design is:

- OpenAI Responses API for text conversations and strict function tools
- A server-side action registry; the model never receives direct database or unrestricted HTTP access
- Server-side authentication, authorization, validation, idempotency, audit logging, and explicit confirmation for consequential actions
- OpenAI Realtime API over WebRTC for low-latency voice, using short-lived credentials minted by the Laravel backend
- A human-readable preview before creating shipments, scheduling pickups, changing delivery details, or submitting forms

Create the API key yourself in an OpenAI project. Store it only as `OPENAI_API_KEY` in the server `.env`; it must never be placed in browser JavaScript, chat, screenshots, or Git.

## Testing and quality checks

```bash
php artisan test
npm run build
composer validate --strict
composer audit --locked
npm audit
php artisan route:list
```

Before a release, also run a fresh migration against a disposable MariaDB/MySQL database and execute role/authorization and browser end-to-end tests. Do not run `migrate:fresh` against staging or production.

## Recommended deployment

The recommended first production topology is a Laravel Forge-managed Ubuntu application server in Singapore or Mumbai on a reputable cloud provider, with Nginx, PHP 8.3, MySQL 8/MariaDB, Redis, Supervisor-managed queue workers, the Laravel scheduler, TLS, automated database backups, and S3-compatible private object storage. Start as one appropriately sized application server, then separate the database/worker tiers only when measured load requires it.

Production environment requirements include:

- `APP_ENV=production` and `APP_DEBUG=false`
- a unique generated `APP_KEY`
- secure database credentials and least-privilege database user
- `SESSION_SECURE_COOKIE=true` behind HTTPS
- Redis-backed cache/queues for sustained use
- queue workers restarted after every deployment
- `php artisan optimize` during deployment
- health checks, error monitoring, centralized logs, uptime monitoring, and restore-tested backups
- a transactional email provider and verified sending domain
- private storage for KYC, invoice, customs, and POD documents

### Production release procedure

The repository includes a release gate at `.github/workflows/release-gate.yml`. It validates dependencies, runs the automated suite, rebuilds and seeds the schema on MySQL 8, builds frontend assets, and verifies Laravel production caches.

Before the first deployment, copy `deploy/production.env.example` to the server's managed environment configuration, replace every placeholder, and generate a unique application key. Never commit the resulting `.env` file.

On the server, run the production configuration guard before changing application state:

```bash
php artisan app:production-check
```

After taking a verified database backup, the repeatable deployment sequence is available in `deploy/deploy-production.sh`. It enables maintenance mode, installs locked production dependencies, builds assets, runs forward-only migrations, caches Laravel metadata, restarts queue workers, and restores service. Do not run `migrate:fresh` outside disposable test environments.

After every release, require successful responses from:

```text
/api/health
/api/readiness
```

The health endpoint confirms the PHP application is responding. Readiness additionally verifies database and cache access. Keep the previous release artifact and a tested database restore procedure available for rollback.

## Security notes

- Client validation is only a usability layer; controllers and policies must enforce server-side validation and authorization.
- Public tracking must use exact tracking-number lookup and a privacy-safe response model.
- The AI assistant is never a security boundary and cannot bypass user permissions.
- Payment secrets, OpenAI keys, SMTP passwords, cloud keys, and production credentials belong only in managed environment secrets.
- Temporary demo credentials must never be used as production credentials.
- Uploaded documents require MIME/content verification, randomized private paths, authorization checks, malware scanning, and retention rules before launch.

## Known gaps

- The existing automated suite is still too small for the number of routes and business roles.
- Several legacy controllers/routes require workflow-level repair and authorization tests.
- The `deliveries` schema combines older rider and newer e-commerce assumptions and needs a deliberate domain split or compatible consolidation.
- Real-time rider location broadcasting, POD chain-of-custody, partner scan auditing, and customer notification coverage need end-to-end tests.
- The scheduling/calendar user interface and comprehensive queued email notifications are not complete.
- The AI text/voice assistant and safe action tools are designed but not implemented.
- Blade templates contain significant inline CSS/JavaScript and CDN styling that should be migrated into the Vite asset pipeline by feature.
- Accessibility, responsive behavior, performance, SEO, observability, disaster recovery, and production load testing still require formal release acceptance.

Until these gaps are closed and validated, treat the project as **not production ready**.
