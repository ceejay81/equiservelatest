# 🚀 EquiServe Modernization - Deployment Checklist

## Pre-Deployment Checklist

### 1. Build Assets ✅
```bash
# Install dependencies
npm install

# Build production assets
npm run build
```

**Verify**: Check that `public/build/` directory is created with compiled assets.

---

### 2. Create PWA Icons ✅
Create two icon files in `public/images/`:
- [ ] `icon-192.png` (192x192 pixels)
- [ ] `icon-512.png` (512x512 pixels)

**Quick Method**: Use https://www.pwabuilder.com/imageGenerator

**Verify**: 
```bash
ls -la public/images/icon-*.png
```

---

### 3. Clear Laravel Caches ✅
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

### 4. Test Locally ✅

#### Start Development Server
```bash
php artisan serve
```

#### Test Checklist
- [ ] Dashboard loads without errors
- [ ] All existing features work
- [ ] No JavaScript errors in console
- [ ] Styles applied correctly
- [ ] Online/offline indicator visible
- [ ] Service Worker registers (check DevTools > Application)

#### Test Offline Mode
1. [ ] Open app in Chrome
2. [ ] Open DevTools (F12)
3. [ ] Go to Network tab
4. [ ] Check "Offline" checkbox
5. [ ] Refresh page
6. [ ] Verify offline page appears or cached content loads

#### Test PWA Installation
1. [ ] Look for install icon in address bar (Chrome/Edge)
2. [ ] Click "Install"
3. [ ] Verify app installs successfully
4. [ ] Check standalone window opens
5. [ ] Test offline functionality in installed app

---

## Production Deployment Checklist

### 1. Server Requirements ✅
- [ ] PHP 8.2 or higher
- [ ] Composer installed
- [ ] Node.js and npm installed
- [ ] **HTTPS enabled** (REQUIRED for PWA)
- [ ] MySQL/PostgreSQL database
- [ ] Web server (Apache/Nginx)

---

### 2. Deploy Code ✅
```bash
# Pull latest code
git pull origin main

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install --production

# Build assets
npm run build
```

---

### 3. Configure Environment ✅
```bash
# Copy environment file if needed
cp .env.example .env

# Generate application key
php artisan key:generate

# Update .env with production settings
# - APP_ENV=production
# - APP_DEBUG=false
# - APP_URL=https://yourdomain.com (MUST be HTTPS)
# - Database credentials
```

---

### 4. Database Migration ✅
```bash
# Run migrations
php artisan migrate --force

# Seed if needed
php artisan db:seed --force
```

---

### 5. Optimize for Production ✅
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

### 6. Set Permissions ✅
```bash
# Linux/Mac
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Or with specific user
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

---

### 7. Web Server Configuration ✅

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css application/javascript application/json
</IfModule>

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

#### Nginx
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /path/to/equiserve/public;

    # SSL Configuration
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

---

### 8. SSL/HTTPS Setup ✅

**REQUIRED for PWA functionality!**

#### Option 1: Let's Encrypt (Free)
```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-apache

# Get certificate
sudo certbot --apache -d yourdomain.com

# Auto-renewal
sudo certbot renew --dry-run
```

#### Option 2: Cloudflare (Free)
1. Sign up at https://cloudflare.com
2. Add your domain
3. Update nameservers
4. Enable SSL (Full or Full Strict)

#### Verify HTTPS
- [ ] Visit https://yourdomain.com
- [ ] Check for padlock icon in browser
- [ ] No mixed content warnings
- [ ] SSL certificate valid

---

### 9. Test Production Deployment ✅

#### Functionality Tests
- [ ] Homepage loads
- [ ] Login works
- [ ] Dashboard displays correctly
- [ ] All features functional
- [ ] No console errors

#### PWA Tests
- [ ] Service Worker registers
- [ ] Manifest loads correctly
- [ ] Icons display properly
- [ ] Install prompt appears
- [ ] Offline mode works

#### Performance Tests
- [ ] Page load < 3 seconds
- [ ] No layout shifts
- [ ] Images load properly
- [ ] Animations smooth

#### Browser Tests
- [ ] Chrome Desktop
- [ ] Chrome Mobile
- [ ] Edge Desktop
- [ ] Firefox Desktop
- [ ] Safari Desktop
- [ ] Safari iOS

---

### 10. Monitoring Setup ✅

#### Error Logging
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check web server logs
tail -f /var/log/nginx/error.log
# or
tail -f /var/log/apache2/error.log
```

#### Performance Monitoring
- [ ] Set up uptime monitoring (UptimeRobot, Pingdom)
- [ ] Configure error tracking (Sentry, Bugsnag)
- [ ] Enable analytics (Google Analytics)

---

## Post-Deployment Checklist

### 1. User Training ✅
- [ ] Train staff on new UI
- [ ] Explain offline functionality
- [ ] Show PWA installation process
- [ ] Demonstrate new features

### 2. Documentation ✅
- [ ] Share QUICK_REFERENCE.md with developers
- [ ] Provide user guide for staff
- [ ] Document any custom configurations

### 3. Backup ✅
- [ ] Database backup configured
- [ ] File backup configured
- [ ] Test restore process

### 4. Security ✅
- [ ] Update all dependencies
- [ ] Review .env file (no sensitive data exposed)
- [ ] Configure firewall rules
- [ ] Set up fail2ban (optional)
- [ ] Enable CSRF protection (already enabled in Laravel)

---

## Rollback Plan

If issues occur:

### Quick Rollback
```bash
# Revert to previous version
git checkout previous-version-tag

# Rebuild assets
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Database Rollback
```bash
# Restore database backup
mysql -u username -p database_name < backup.sql
```

---

## Troubleshooting

### Issue: Service Worker Not Working
**Symptoms**: Offline mode doesn't work, install prompt doesn't appear
**Solutions**:
1. Verify HTTPS is enabled
2. Check browser console for errors
3. Clear browser cache
4. Check `public/sw.js` exists
5. Verify manifest.json is accessible

### Issue: Assets Not Loading
**Symptoms**: Styles missing, JavaScript errors
**Solutions**:
1. Run `npm run build`
2. Clear Laravel caches
3. Check file permissions
4. Verify Vite build completed successfully

### Issue: Icons Not Displaying
**Symptoms**: Default browser icon shows instead
**Solutions**:
1. Verify icon files exist in `public/images/`
2. Check file names match manifest.json
3. Clear browser cache
4. Test manifest.json directly: `https://yourdomain.com/manifest.json`

### Issue: Offline Page Not Showing
**Symptoms**: Browser offline page appears
**Solutions**:
1. Verify `public/offline.html` exists
2. Check Service Worker is registered
3. Clear Service Worker cache
4. Test in incognito mode

---

## Performance Optimization

### After Deployment

#### 1. Enable OPcache (PHP)
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

#### 2. Enable Redis Cache (Optional)
```bash
# Install Redis
sudo apt-get install redis-server

# Update .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

#### 3. Enable CDN (Optional)
- Upload static assets to CDN
- Update asset URLs in config
- Configure CORS headers

---

## Success Criteria

### Technical
- ✅ All tests passing
- ✅ No console errors
- ✅ HTTPS enabled
- ✅ Service Worker registered
- ✅ PWA installable
- ✅ Offline mode working

### Performance
- ✅ Page load < 3 seconds
- ✅ Lighthouse score > 90
- ✅ No layout shifts
- ✅ Smooth animations

### User Experience
- ✅ Intuitive navigation
- ✅ Clear visual feedback
- ✅ Mobile-friendly
- ✅ Accessible

---

## Final Verification

### Production Checklist
- [ ] HTTPS enabled and working
- [ ] Service Worker registered
- [ ] PWA installable
- [ ] Offline mode functional
- [ ] All features working
- [ ] No console errors
- [ ] Performance acceptable
- [ ] Backups configured
- [ ] Monitoring active
- [ ] Documentation complete

### Sign-off
- [ ] Technical lead approval
- [ ] Stakeholder approval
- [ ] User acceptance testing complete
- [ ] Go-live date confirmed

---

## 🎉 Deployment Complete!

Once all items are checked:
1. ✅ System is live
2. ✅ Users can access
3. ✅ Offline mode works
4. ✅ PWA installable
5. ✅ Monitoring active

**Congratulations! Your modernized EquiServe system is now in production! 🚀**

---

## Support Contacts

### Technical Issues
- Check documentation files
- Review Laravel logs
- Check browser console
- Test in incognito mode

### Emergency Rollback
- Follow rollback plan above
- Restore database backup
- Notify users of maintenance

---

**Last Updated**: November 21, 2025
**Version**: 2.0.0
**Status**: Production Ready ✅
