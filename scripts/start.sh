#!/bin/sh
set -e

: "${PORT:=8080}"
echo "Starting Laravel on port $PORT"

# if manifest missing try to run fix script (works in container/CI)
if [ ! -f public/build/manifest.json ]; then
  echo "Manifest missing — attempting to run scripts/fix-assets.sh"
  if [ -x ./scripts/fix-assets.sh ]; then
    ./scripts/fix-assets.sh || echo "fix-assets.sh failed"
  elif [ -x ./scripts/move-manifest.sh ]; then
    ./scripts/move-manifest.sh || echo "move-manifest.sh failed"
  else
    echo "No fix script executable found; assets may be missing"
  fi
fi

# only attempt chown when chown exists and www-data user exists
if command -v chown >/dev/null 2>&1 && id -u www-data >/dev/null 2>&1; then
  chown -R www-data:www-data storage bootstrap/cache || true
fi

# run in foreground so Railway healthcheck inafika
exec php artisan serve --host=0.0.0.0 --port="$PORT"