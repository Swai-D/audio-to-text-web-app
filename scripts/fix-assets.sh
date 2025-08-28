#!/bin/bash

echo "🔧 Fixing assets for production..."

# Build assets for production
echo "📦 Building assets..."
npm run build:prod

# Check if build was successful
if [ ! -f "public/build/manifest.json" ]; then
    echo "❌ Build failed - manifest.json not found"
    exit 1
fi

echo "✅ Assets built successfully"

# Clear Laravel caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "✅ Caches cleared"

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

echo "✅ Permissions set"

# Create storage link if not exists
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
    echo "✅ Storage link created"
fi

echo "🎉 Asset fix completed successfully!"
echo "📁 Assets location: public/build/assets/"
echo "📄 Manifest: public/build/manifest.json"
