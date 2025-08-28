#!/bin/bash

# Build assets for production
echo "Building assets for production..."

# Install dependencies
npm ci

# Build assets
npm run build

# Verify manifest exists
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Vite manifest created successfully"
    cat public/build/manifest.json
else
    echo "❌ Vite manifest not found"
    exit 1
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
