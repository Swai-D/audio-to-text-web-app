#!/bin/bash

echo "🤖 Setting up AI Business Consultant for CICI Store..."
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the Laravel project root directory"
    exit 1
fi

echo "📦 Running migrations..."
php artisan migrate

echo "🌱 Running seeders..."
php artisan db:seed --class=SettingSeeder
php artisan db:seed --class=AiPermissionSeeder

echo "🔧 Checking dependencies..."
if ! composer show guzzlehttp/guzzle > /dev/null 2>&1; then
    echo "📥 Installing GuzzleHTTP..."
    composer require guzzlehttp/guzzle
else
    echo "✅ GuzzleHTTP already installed"
fi

echo "🗄️ Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "✅ AI Business Consultant setup complete!"
echo ""
echo "🎯 Next steps:"
echo "1. Visit /admin/ai to configure your OpenAI API key"
echo "2. Enable the AI feature"
echo "3. Visit /ai-chat to start using the AI Business Consultant"
echo ""
echo "📚 For more information, see AI_IMPLEMENTATION_GUIDE.md"
