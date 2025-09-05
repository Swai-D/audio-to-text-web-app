#!/bin/bash

echo "🔧 Fixing assets for Railway deployment..."

# Futa kabisa build ya zamani
rm -rf public/build

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Build assets
echo "📦 Building assets..."
npm run build:prod

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/build/

# Verify assets exist (more flexible)
echo "✅ Verifying assets..."
if ls public/build/assets/*.css 1> /dev/null 2>&1; then
    echo "✅ CSS files found:"
    ls public/build/assets/*.css
else
    echo "❌ CSS files missing"
fi

if ls public/build/assets/*.js 1> /dev/null 2>&1; then
    echo "✅ JS files found:"
    ls public/build/assets/*.js
else
    echo "❌ JS files missing"
fi

# Check manifest - handle both old and new Vite locations
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Manifest file found at public/build/manifest.json"
    ls -la public/build/manifest.json
elif [ -f "public/build/.vite/manifest.json" ]; then
    echo "✅ Vite manifest file found at public/build/.vite/manifest.json"
    ls -la public/build/.vite/manifest.json
    # Copy to expected location for Laravel
    cp public/build/.vite/manifest.json public/build/manifest.json
    echo "✅ Copied manifest to expected location"
else
    echo "❌ Manifest file missing"
    echo "📁 Contents of public/build/:"
    ls -la public/build/ || echo "Directory doesn't exist"
    if [ -d "public/build/.vite" ]; then
        echo "📁 Contents of public/build/.vite/:"
        ls -la public/build/.vite/
    fi
fi

echo "🎉 Asset setup complete!" 