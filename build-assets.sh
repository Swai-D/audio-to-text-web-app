#!/bin/bash

# Build assets for production
echo "Building assets for production..."

# Install dependencies
npm ci

# Build assets
npm run build

# Move manifest to correct location
if [ -f "public/build/.vite/manifest.json" ]; then
    cp public/build/.vite/manifest.json public/build/manifest.json
    echo "✅ Manifest moved to correct location"
fi

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully"
