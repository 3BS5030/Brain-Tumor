#!/bin/bash
set -e

cd /var/www

# Run migrations
php artisan migrate --force

# Cache config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP built-in server on port 8080
exec php artisan serve --host=0.0.0.0 --port=8080
