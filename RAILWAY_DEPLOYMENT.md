# 🚀 Railway Deployment Guide - Sermon Transcriber

## 📋 **Pre-Deployment Checklist**

### ✅ **Local Setup Complete**
- [x] Laravel project configured
- [x] Database migrations ready
- [x] Assets built for production
- [x] Environment variables configured
- [x] Dependencies installed

## 🔧 **Railway Configuration**

### **1. Environment Variables (Railway Dashboard)**

Set these environment variables in your Railway project dashboard:

```env
# Application
APP_NAME="Sermon Transcriber"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

# Database (Railway MySQL)
DB_CONNECTION=mysql
DB_HOST=your-mysql-host.railway.app
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your-mysql-password

# File Storage
FILESYSTEM_DISK=public

# OpenAI API (REQUIRED)
OPENAI_API_KEY=sk-your-openai-api-key-here

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (Optional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@your-app.railway.app"
MAIL_FROM_NAME="Sermon Transcriber"
```

### **2. Build Configuration**

The `railway.json` file is already configured with:
- **Builder**: NIXPACKS (automatic PHP detection)
- **Build Command**: `npm run build:production`
- **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

### **3. Asset Building**

Assets are automatically built during deployment:
- **CSS**: Optimized and minified
- **JavaScript**: Bundled and minified
- **Manifest**: Generated for asset versioning

## 🚀 **Deployment Steps**

### **Step 1: Connect to Railway**

1. **Install Railway CLI** (optional):
   ```bash
   npm install -g @railway/cli
   ```

2. **Login to Railway**:
   ```bash
   railway login
   ```

3. **Initialize Railway project**:
   ```bash
   railway init
   ```

### **Step 2: Configure Environment**

1. **Set environment variables** in Railway dashboard
2. **Add MySQL database** service
3. **Link database** to your app

### **Step 3: Deploy**

1. **Push to Railway**:
   ```bash
   railway up
   ```

2. **Or connect GitHub** for automatic deployments

### **Step 4: Post-Deployment Setup**

After deployment, run these commands in Railway shell:

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

## 🔍 **Troubleshooting**

### **CSS/JS Not Loading**

1. **Check asset manifest**:
   ```bash
   cat public/build/manifest.json
   ```

2. **Verify Vite assets**:
   ```bash
   ls -la public/build/assets/
   ```

3. **Clear Laravel cache**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

### **Database Issues**

1. **Check database connection**:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

2. **Run migrations**:
   ```bash
   php artisan migrate:status
   php artisan migrate --force
   ```

### **File Upload Issues**

1. **Check storage permissions**:
   ```bash
   ls -la storage/
   chmod -R 755 storage/
   ```

2. **Verify storage link**:
   ```bash
   php artisan storage:link
   ```

## 📊 **Performance Optimization**

### **Production Optimizations**

1. **Enable OPcache** (if available):
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.interned_strings_buffer=8
   opcache.max_accelerated_files=4000
   ```

2. **Configure caching**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Optimize Composer**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

### **Asset Optimization**

- **CSS**: Minified and optimized (53.89 kB → 8.88 kB gzipped)
- **JavaScript**: Bundled and minified (78.88 kB → 28.52 kB gzipped)
- **Images**: Optimized and compressed
- **Fonts**: Optimized loading

## 🔒 **Security Checklist**

### **Production Security**

- [x] `APP_DEBUG=false`
- [x] `APP_ENV=production`
- [x] Secure database credentials
- [x] HTTPS enabled
- [x] CSRF protection enabled
- [x] File upload validation
- [x] Input sanitization

### **Environment Variables**

- [x] No sensitive data in code
- [x] API keys properly configured
- [x] Database credentials secure
- [x] Mail configuration set

## 📱 **Testing After Deployment**

### **Functionality Tests**

1. **Homepage**: Check if assets load properly
2. **Authentication**: Test login/register
3. **File Upload**: Test audio upload
4. **Transcription**: Test OpenAI integration
5. **Export**: Test PDF/Word download
6. **Responsive**: Test on mobile devices

### **Performance Tests**

1. **Page Load**: Check loading times
2. **Asset Loading**: Verify CSS/JS load
3. **File Upload**: Test large files
4. **Database**: Check query performance

## 🎯 **Monitoring**

### **Railway Metrics**

- **CPU Usage**: Monitor application performance
- **Memory Usage**: Check for memory leaks
- **Response Time**: Monitor API performance
- **Error Rate**: Track application errors

### **Application Logs**

```bash
# View application logs
railway logs

# Check Laravel logs
tail -f storage/logs/laravel.log
```

## 🔄 **Continuous Deployment**

### **GitHub Integration**

1. **Connect GitHub repository**
2. **Enable automatic deployments**
3. **Set up branch protection**
4. **Configure deployment triggers**

### **Deployment Pipeline**

```yaml
# .github/workflows/railway.yml
name: Deploy to Railway
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: railway/deploy@v1
        with:
          service: your-service-name
```

## 📞 **Support**

### **Common Issues**

1. **Assets not loading**: Check manifest.json and build process
2. **Database connection**: Verify environment variables
3. **File uploads**: Check storage permissions and disk space
4. **Performance**: Monitor resource usage and optimize

### **Getting Help**

- **Railway Documentation**: https://docs.railway.app/
- **Laravel Documentation**: https://laravel.com/docs
- **GitHub Issues**: Create issue in repository

---

## ✅ **Deployment Complete!**

Your Sermon Transcriber app is now deployed on Railway with:
- ✅ **Production-optimized assets**
- ✅ **Secure configuration**
- ✅ **Database integration**
- ✅ **File storage**
- ✅ **OpenAI API integration**
- ✅ **Responsive design**
- ✅ **Modern UI/UX**

**URL**: `https://your-app-name.railway.app`

**Next Steps**:
1. Test all functionality
2. Monitor performance
3. Set up monitoring
4. Configure backups
5. Set up SSL certificate (if needed)
