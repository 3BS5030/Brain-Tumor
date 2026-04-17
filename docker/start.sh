#!/bin/bash
set -e

cd /var/www

# Make sure SQLite file exists
mkdir -p database
touch database/database.sqlite
mkdir -p storage/app/public
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
php artisan storage:link || true

export BRAIN_TUMOR_SERVICE_URL="${BRAIN_TUMOR_SERVICE_URL:-http://127.0.0.1:5001}"
export BRAIN_TUMOR_SERVICE_HOST="${BRAIN_TUMOR_SERVICE_HOST:-127.0.0.1}"
export BRAIN_TUMOR_SERVICE_PORT="${BRAIN_TUMOR_SERVICE_PORT:-5001}"
export MODEL_PATH="${MODEL_PATH:-app/Infrastructure/Prediction/Python/best_model.pth}"

echo "Starting Python prediction service on port ${BRAIN_TUMOR_SERVICE_PORT}..."
/venv/bin/python app/Infrastructure/Prediction/Python/prediction_server.py &

echo "Starting Laravel on port 8080..."
exec php artisan serve --host=0.0.0.0 --port=8080
