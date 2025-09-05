#!/usr/bin/env bash
set -euo pipefail

echo "🤖 Project setup for Sermon Transcriber"
echo "======================================="

# ensure running from project root
if [ ! -f "artisan" ]; then
  echo "❌ artisan not found. Run this from the Laravel project root."
  exit 1
fi

# prevent accidental production runs
APP_ENV="${APP_ENV:-$(php -r 'echo getenv(\"APP_ENV\") ?: \"local\";')}"
if [ "$APP_ENV" = "production" ] && [ "${FORCE_SETUP:-0}" != "1" ]; then
  echo "⚠️  APP_ENV=production. Set FORCE_SETUP=1 to proceed."
  exit 1
fi

echo "📦 Installing PHP dependencies if missing..."
if [ ! -d "vendor" ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "✅ vendor present"
fi

echo "🧩 Installing Node dependencies if missing..."
if [ ! -d "node_modules" ]; then
  npm ci --silent
else
  echo "✅ node_modules present"
fi

echo "🔨 Building frontend assets (production)..."
npm run build:production --silent || npm run build --silent || true

echo "🔐 Ensure APP_KEY exists..."
if [ -z "$(php -r 'echo env(\"APP_KEY\");')" ]; then
  php artisan key:generate --force
  echo "✅ APP_KEY generated"
fi

echo "🗄️ Create storage link and set permissions..."
rm -f public/storage || true
php artisan storage:link || true
chmod -R 755 storage bootstrap/cache public || true

echo "🌱 Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --class=SettingSeeder --force || true
php artisan db:seed --class=AiPermissionSeeder --force || true

echo "🧹 Clearing caches (safe)..."
php artisan optimize:clear || true

echo "✅ Setup complete."
echo "Next: configure env variables (APP_URL, DB, MAIL, AI keys) and deploy."