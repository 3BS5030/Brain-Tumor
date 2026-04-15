#!/bin/sh
set -eu

cd /app

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    export DB_DATABASE="${DB_DATABASE:-/app/storage/app/database.sqlite}"
    touch "$DB_DATABASE"
fi

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is not set. Add APP_KEY in Railway Variables before deploying."
    exit 1
fi

php artisan package:discover --ansi --no-interaction
php artisan config:clear --no-interaction || true
php artisan storage:link --no-interaction || true
php artisan migrate --force --no-interaction

export BRAIN_TUMOR_SERVICE_URL="${BRAIN_TUMOR_SERVICE_URL:-http://127.0.0.1:5001}"
export BRAIN_TUMOR_SERVICE_HOST="${BRAIN_TUMOR_SERVICE_HOST:-127.0.0.1}"
export BRAIN_TUMOR_SERVICE_PORT="${BRAIN_TUMOR_SERVICE_PORT:-5001}"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
