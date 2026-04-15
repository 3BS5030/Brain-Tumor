#!/bin/sh
set -eu

cd /app

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    export DB_DATABASE="${DB_DATABASE:-/app/storage/app/database.sqlite}"
    touch "$DB_DATABASE"
fi

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-interaction
fi

php artisan package:discover --ansi --no-interaction
php artisan config:clear --no-interaction || true
php artisan storage:link --no-interaction || true
php artisan migrate --force --no-interaction

export BRAIN_TUMOR_SERVICE_URL="${BRAIN_TUMOR_SERVICE_URL:-http://127.0.0.1:5001}"
export BRAIN_TUMOR_SERVICE_HOST="${BRAIN_TUMOR_SERVICE_HOST:-127.0.0.1}"
export BRAIN_TUMOR_SERVICE_PORT="${BRAIN_TUMOR_SERVICE_PORT:-5001}"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
