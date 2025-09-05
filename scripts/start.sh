#!/bin/sh
set -e

PORT="${PORT:-8080}"
echo "Starting Laravel on port $PORT"

# ensure permissions (optional)
chown -R www-data:www-data storage bootstrap/cache || true

# run in foreground so Railway healthcheck inafika
exec php artisan serve --host=0.0.0.0 --port="$PORT"