#!/bin/bash

# Build assets for production
echo "Building assets for production..."

# Install dependencies
npm ci

# Clear previous build
rm -rf public/build

# Build assets
npm run build

# Verify manifest exists
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Vite manifest created successfully"
    cat public/build/manifest.json
    echo ""
    echo "📁 Build directory contents:"
    ls -la public/build/
    echo ""
    echo "📁 Assets directory contents:"
    ls -la public/build/assets/
else
    echo "❌ Vite manifest not found"
    echo "📁 Current directory:"
    pwd
    echo "📁 Public directory contents:"
    ls -la public/
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
