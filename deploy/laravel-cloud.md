# Laravel Cloud deployment runbook

This runbook is for the NETPACK application on Laravel Cloud. Use a separate **staging** environment first; do not expose the first deployment as the public production service.

## 1. Create the staging environment

In the Laravel Cloud project, connect the GitHub repository `kirannetpack-ui/COURIER-by-NETPACK` and create a **staging** environment from the `main` branch. Choose a region close to the intended users (Mumbai or Singapore are sensible options for Nepal) and start with one application instance.

Attach these managed resources to staging:

- a MySQL database cluster;
- a Key-Value/Redis store for cache, sessions, and queues;
- private Object Storage for POD, KYC, invoice, and customs-document uploads;
- one worker cluster for queued mail and reminders;
- a scheduler for `php artisan schedule:run`.

Laravel Cloud injects the connection credentials for attached resources. Do not add or override the Cloud-managed database, Redis, or object-storage credential variables manually.

## 2. Runtime and build settings

Set PHP to **8.3** and Node.js to **20**. Configure these build commands:

```text
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan optimize
```

Configure the deployment command as:

```text
php artisan app:production-check --allow-staging
php artisan migrate --force
```

Do not use `deploy-production.sh` on Laravel Cloud. Laravel Cloud handles deployment lifecycle actions itself; do not add `php artisan queue:restart`, `php artisan optimize:clear`, or `php artisan storage:link` to the Cloud deployment command.

## 3. Staging environment variables

Set these values in Laravel Cloud's environment-variable UI. Generate `APP_KEY` locally with `php artisan key:generate --show`, then paste the result as a secret.

```dotenv
APP_NAME="Courier by NETPACK"
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://<your-staging-cloud-domain>
APP_TIMEZONE=Asia/Kathmandu
LOG_CHANNEL=stack
LOG_LEVEL=warning

CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
QUEUE_CONNECTION=redis
FILESYSTEM_DISK=s3

MAIL_MAILER=<your-staging-transactional-mailer>
MAIL_FROM_ADDRESS=<verified-staging-sender>
MAIL_FROM_NAME="Courier by NETPACK"

KHALTI_LIVE=false
KHALTI_PUBLIC_KEY=<sandbox-key>
KHALTI_SECRET_KEY=<sandbox-secret>
ESEWA_LIVE=false
ESEWA_MERCHANT_CODE=<sandbox-code>
ESEWA_SECRET_KEY=<sandbox-secret>
ALLOW_DEMO_SEEDING=false
```

Enter mail and payment credentials as secrets. Use sandbox credentials only in staging. Never add demo accounts, real customer data, production payment credentials, or `ALLOW_DEMO_SEEDING=true` to the production environment.

## 4. First staging release and smoke test

Deploy `main` from Laravel Cloud. Once the deployment is green, run the following using the environment's command runner, then turn `ALLOW_DEMO_SEEDING` back to `false` if you temporarily enable it for test users:

```text
php artisan db:seed --class=UserSeeder --force
php artisan app:production-check --allow-staging
```

Verify these URLs return HTTP 200:

```text
https://<your-staging-cloud-domain>/api/health
https://<your-staging-cloud-domain>/api/readiness
```

Then test, using only staging accounts and sandbox payments:

1. Login and password-change flow for each key role.
2. Domestic and e-commerce shipment/order creation.
3. Rider order claim, status progression, COD settlement, and duplicate-claim prevention.
4. Partner and overseas transit-point authorization boundaries.
5. Email, queue, scheduler, uploaded-document privacy, and payment callbacks.

## 5. Production promotion

Only after staging acceptance: create a separate production environment, attach separate managed resources, use a production custom HTTPS domain, set `APP_ENV=production`, replace sandbox keys with live verified credentials, and deploy with:

```text
php artisan app:production-check
php artisan migrate --force
```

Do not seed production demo users. Take and verify a database backup before every production migration. Require `/api/health` and `/api/readiness` to pass after deployment, and keep the previous deployment plus tested restore procedure available for rollback.
