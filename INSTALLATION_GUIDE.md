# EquiServe Modernization - Installation Guide

## Quick Start

### 1. Build Assets
```bash
npm install
npm run build
```

### 2. Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### 3. Generate Icons (Optional)
Create PWA icons in `public/images/`:
- `icon-192.png` (192x192px)
- `icon-512.png` (512x512px)

You can use any logo or create simple colored squares with your brand colors.

### 4. Test PWA
1. Serve the application: `php artisan serve`
2. Open in Chrome/Edge: `http://localhost:8000`
3. Check DevTools > Application > Service Workers
4. Verify manifest loads correctly
5. Test offline mode (DevTools > Network > Offline)

### 5. Deploy to Production

#### Requirements
- ✅ HTTPS enabled (required for Service Workers)
- ✅ Modern web server (Apache/Nginx)
- ✅ PHP 8.2+
- ✅ Composer dependencies installed
- ✅ Node.js assets built

#### Deployment Steps
```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# 3. Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Set permissions
chmod -R 755 storage bootstrap/cache
```

## Testing Offline Functionality

### Test Scenario 1: Basic Offline Access
1. Open app while online
2. Navigate to dashboard
3. Disconnect internet
4. Refresh page - should load from cache
5. Navigate to other pages - should work

### Test Scenario 2: Offline Sales Creation
1. Open sales page while online
2. Disconnect internet
3. Create a sale
4. Sale stored in IndexedDB
5. Reconnect - sale syncs automatically

### Test Scenario 3: PWA Installation
1. Open app in Chrome
2. Look for install icon in address bar
3. Click "Install"
4. App opens in standalone window
5. Works like native app

## Troubleshooting

### Service Worker Not Registering
**Problem**: SW registration fails
**Solution**: 
- Ensure HTTPS is enabled
- Check browser console for errors
- Verify `public/sw.js` exists
- Clear browser cache

### Offline Page Not Showing
**Problem**: Shows browser offline page instead
**Solution**:
- Check `public/offline.html` exists
- Verify SW is registered
- Test in incognito mode

### Assets Not Loading Offline
**Problem**: CSS/JS missing offline
**Solution**:
- Run `npm run build` to generate assets
- Check SW cache list includes all assets
- Verify asset paths in manifest

### IndexedDB Errors
**Problem**: Data not saving offline
**Solution**:
- Check browser supports IndexedDB
- Verify no private browsing mode
- Clear IndexedDB in DevTools

## Browser Testing Checklist

- [ ] Chrome Desktop - PWA install works
- [ ] Chrome Mobile - Add to home screen works
- [ ] Edge Desktop - PWA install works
- [ ] Firefox Desktop - Offline mode works
- [ ] Safari Desktop - Basic offline works
- [ ] Safari iOS - Add to home screen works

## Performance Optimization

### Enable Compression (Nginx)
```nginx
gzip on;
gzip_vary on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml;
```

### Enable Compression (Apache)
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css application/javascript
</IfModule>
```

### Cache Headers
```nginx
location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

## Security Checklist

- [ ] HTTPS enabled
- [ ] CSP headers configured
- [ ] Secure cookies enabled
- [ ] CORS properly configured
- [ ] Service Worker scope limited
- [ ] IndexedDB data encrypted

## Monitoring

### Check Service Worker Status
```javascript
// In browser console
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('Active SWs:', regs.length);
    regs.forEach(reg => console.log(reg.scope));
});
```

### Check Cache Status
```javascript
// In browser console
caches.keys().then(keys => {
    console.log('Cached:', keys);
    keys.forEach(key => {
        caches.open(key).then(cache => {
            cache.keys().then(reqs => {
                console.log(`${key}: ${reqs.length} items`);
            });
        });
    });
});
```

### Check IndexedDB
```javascript
// In browser console
indexedDB.databases().then(dbs => {
    console.log('Databases:', dbs);
});
```

## Support

If you encounter issues:
1. Check browser console for errors
2. Verify all files are in place
3. Test in incognito mode
4. Clear all caches and try again
5. Check HTTPS is properly configured

## Next Steps

After installation:
1. ✅ Test all offline features
2. ✅ Customize PWA icons with your branding
3. ✅ Configure push notifications (optional)
4. ✅ Set up analytics tracking
5. ✅ Train users on offline capabilities

---

**Need Help?** Check the MODERNIZATION_SUMMARY.md for detailed feature documentation.
