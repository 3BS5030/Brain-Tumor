#!/bin/bash
set -e

cd /var/www

# Make sure SQLite file exists
mkdir -p database
touch database/database.sqlite
chmod -R 777 storage bootstrap/cache

# If APP_KEY env var is set from Railway, update .env with it
if [ -n "$APP_KEY" ]; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
fi

# Run migrations
php artisan migrate --force

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Laravel on port 8080..."
exec php artisan serve --host=0.0.0.0 --port=8080
