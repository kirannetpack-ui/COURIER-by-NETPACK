#!/usr/bin/env bash

set -Eeuo pipefail

cd "$(dirname "$0")/.."

if [[ ! -f .env ]]; then
    echo "Missing production .env file." >&2
    exit 1
fi

php artisan app:production-check

maintenance_enabled=0
restore_application() {
    if [[ "$maintenance_enabled" -eq 1 ]]; then
        php artisan up || true
    fi
}
trap restore_application EXIT

php artisan down --retry=60
maintenance_enabled=1

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build

php artisan migrate --force

if [[ ! -e public/storage ]]; then
    php artisan storage:link
fi

php artisan optimize
php artisan queue:restart
php artisan up
maintenance_enabled=0

echo "Deployment completed. Verify /api/health and /api/readiness before enabling traffic."
