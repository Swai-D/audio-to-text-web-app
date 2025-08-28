# Railway Deployment Guide - Sermon Transcriber

## 🎯 Overview
Complete working solution for deploying Sermon Transcriber on Railway. This solution has been tested and works perfectly.

## ✅ Essential Files for Railway Deployment

### 1. `.nixpacks.toml` (CRITICAL)
This file ensures Railway installs the correct dependencies and builds assets properly:

```toml
# .nixpacks.toml

[phases.setup]
nixPkgs = [
  "php",
  "phpPackages.composer",
  "nodejs_18",
  "npm"
]

[phases.install]
cmds = [
  "composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs",
  "npm ci"
]

[phases.build]
cmds = [
  "npm run build"
]

[start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"
```

### 2. `Procfile`
```procfile
web: php artisan serve --host=0.0.0.0 --port=$PORT
```

**Alternative for Apache servers:**
```procfile
web: vendor/bin/heroku-php-apache2 public/
```

### 3. `.railwayignore`
```gitignore
# Dependencies
node_modules/
vendor/

# Environment files
.env
.env.local
.env.production

# Logs
storage/logs/
*.log

# Cache
storage/framework/cache/
storage/framework/sessions/
storage/framework/views/
bootstrap/cache/

# Testing
tests/
phpunit.xml

# IDE files
.vscode/
.idea/
*.swp
*.swo

# OS files
.DS_Store
Thumbs.db

# Git
.git/
.gitignore

# Temporary files
*.tmp
*.temp
```

## 🚀 Deployment Steps

### 1. Connect GitHub Repository
1. Go to [Railway Dashboard](https://railway.app/dashboard)
2. Click "New Project" → "Deploy from GitHub repo"
3. Select your Sermon Transcriber repository
4. Choose the main branch

### 2. Environment Variables Setup
Add these environment variables in Railway dashboard:

```env
APP_NAME="Sermon Transcriber"
APP_ENV="production"
APP_KEY="your-generated-app-key-here"
APP_DEBUG="false"
APP_URL="https://your-app-name.up.railway.app"
LOG_CHANNEL="stack"
LOG_DEPRECATIONS_CHANNEL="null"
LOG_LEVEL="debug"
DB_CONNECTION="mysql"
DB_HOST="your-railway-db-host"
DB_PORT="your-railway-db-port"
DB_DATABASE="your-railway-db-name"
DB_USERNAME="your-railway-db-username"
DB_PASSWORD="your-railway-db-password"
BROADCAST_DRIVER="log"
CACHE_DRIVER="file"
FILESYSTEM_DISK="local"
QUEUE_CONNECTION="sync"
SESSION_DRIVER="file"
SESSION_LIFETIME="120"
MAIL_MAILER="smtp"
MAIL_HOST="mailpit"
MAIL_PORT="1025"
MAIL_USERNAME="null"
MAIL_PASSWORD="null"
MAIL_ENCRYPTION="null"
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
VITE_APP_NAME="${APP_NAME}"
APP_TIMEZONE="Africa/Dar_es_Salaam"
APP_LOCALE="en"
APP_FALLBACK_LOCALE="en"
OPENAI_API_KEY="your-openai-api-key-here"
```

**Important Security Notes:**
- Generate a new APP_KEY using: `php artisan key:generate`
- Railway will automatically provide database credentials when you add MySQL service
- Never commit real credentials to Git
- Add your OpenAI API key for transcription functionality

### 3. Database Setup
1. Add MySQL service in Railway
2. Railway will automatically set DB_* environment variables
3. Run migrations after deployment:
   ```bash
   php artisan migrate --force
   ```

### 4. Post-Deployment Commands
In Railway Console, run:
```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 🎨 CSS/Assets Configuration (WORKING SOLUTION)

### Layouts Configuration
**IMPORTANT:** Use `@vite()` only, don't use fallback methods.

#### App Layout (`resources/views/layouts/app.blade.php`):
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sermon Transcriber') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <!-- ... rest of the layout ... -->
</html>
```

#### Guest Layout (`resources/views/layouts/guest.blade.php`):
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sermon Transcriber') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <!-- ... rest of the layout ... -->
</html>
```

### Package.json Scripts
```json
{
    "scripts": {
        "build": "vite build",
        "build:prod": "vite build --mode production",
        "dev": "vite"
    }
}
```

## ❌ Failed Approaches (DON'T USE)

### 1. Environment-Based Fallback
```blade
<!-- DON'T USE THIS -->
@if(app()->environment('local', 'development'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <link rel="stylesheet" href="{{ asset('build/assets/app-xyz.css') }}">
@endif
```

### 2. Direct Asset References
```blade
<!-- DON'T USE THIS -->
<link rel="stylesheet" href="{{ asset('build/assets/app-7dFzyK7f.css') }}">
```

### 3. Complex Asset Helpers
```php
// DON'T USE THIS - Unnecessary complexity
class AssetHelper {
    // Complex fallback logic
}
```

## ✅ Working Solution Summary

### 1. Required Files:
- ✅ `.nixpacks.toml` - Build configuration
- ✅ `Procfile` - Web server configuration
- ✅ `.railwayignore` - Exclude unnecessary files
- ✅ `@vite()` directive in layouts

### 2. Build Process:
1. Railway installs PHP, Node.js 18, npm
2. Runs `composer install`
3. Runs `npm ci`
4. Runs `npm run build` (Vite builds assets)
5. Starts Laravel server

### 3. Asset Loading:
- ✅ Use `@vite(['resources/css/app.css', 'resources/js/app.js'])` only
- ✅ No fallback methods needed
- ✅ Vite handles everything automatically

## 🔧 Troubleshooting

### CSS Not Displaying
1. Ensure `.nixpacks.toml` exists
2. Check Railway build logs
3. Verify `npm run build` succeeded
4. Clear caches: `php artisan view:clear`

### 502 Bad Gateway
1. Check Railway logs
2. Verify environment variables
3. Run post-deployment commands

### Database Connection
1. Verify DB_* environment variables
2. Run migrations: `php artisan migrate --force`

### OpenAI API Issues
1. Verify OPENAI_API_KEY is set
2. Check API key permissions
3. Test transcription functionality

## 📊 Monitoring

### Railway Dashboard
- Monitor build logs
- Check deployment status
- View application logs

### Useful Commands (Railway Console)
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan migrate:status
```

## 🔄 Updates

### Adding New Assets
1. Add to `resources/css/` or `resources/js/`
2. Update `@vite()` directive
3. Push to GitHub
4. Railway auto-rebuilds

### Environment Variables
1. Update in Railway dashboard
2. Redeploy application

## ✅ Success Checklist

- [ ] `.nixpacks.toml` exists
- [ ] `Procfile` exists
- [ ] `.railwayignore` exists
- [ ] Environment variables set
- [ ] Database connected
- [ ] Migrations run
- [ ] CSS styles displaying
- [ ] Application accessible
- [ ] OpenAI API key configured
- [ ] Transcription functionality working

## 🎯 Sermon Transcriber Specific Features

### Audio Upload
- ✅ 300MB file size limit
- ✅ Multiple format support (MP3, WAV, M4A, MP4, WEBM)
- ✅ Drag and drop functionality
- ✅ Progress indicators

### Audio Recording
- ✅ Browser-based recording
- ✅ Real-time timer
- ✅ Audio preview
- ✅ Download functionality

### AI Transcription
- ✅ OpenAI Whisper integration
- ✅ Language auto-detection
- ✅ High-quality transcription

### AI Summarization
- ✅ OpenAI GPT integration
- ✅ Language-specific summaries
- ✅ Clean formatting

### Export Features
- ✅ PDF export (Dompdf)
- ✅ DOCX export (PhpWord)
- ✅ Professional formatting

### CRUD Operations
- ✅ Create transcripts
- ✅ Read/View transcripts
- ✅ Update/Edit transcripts
- ✅ Delete transcripts
- ✅ User-specific data

---

**This solution has been tested and works perfectly on Railway!** 🎉

**Note:** Replace placeholder values with your actual Railway configuration values.
