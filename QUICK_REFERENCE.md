# EquiServe Modernization - Quick Reference

## 🎨 Using New UI Components

### Status Badges
```blade
{{-- In your Blade templates --}}
<x-status-badge status="active" />
<x-status-badge status="overdue" />
<x-status-badge status="pending" />
<x-status-badge status="completed" />
<x-status-badge status="low-stock" />
<x-status-badge status="in-stock" />
```

### Modern Stat Cards
```blade
<x-modern-stat-card 
    title="Sales Today" 
    value="₱25,000" 
    icon="shopping-cart" 
    color="green"
    trend="12.5%"
    trendDirection="up"
    footer="This week: ₱150,000"
    link="/sales"
/>
```

**Available Colors**: `blue`, `green`, `amber`, `red`, `purple`

**Available Icons**: Any Font Awesome icon name (without `fa-` prefix)

---

## 🔔 Toast Notifications

### JavaScript Usage
```javascript
// Success
toast.success('Sale created successfully!');

// Error
toast.error('Failed to save data');

// Warning
toast.warning('Low stock alert for Product X');

// Info
toast.info('System update available');

// Custom duration (in milliseconds)
toast.success('Saved!', 2000); // Shows for 2 seconds
```

### From Laravel Controller
```php
// In your controller
return redirect()->back()->with('toast', [
    'type' => 'success',
    'message' => 'Customer created successfully!'
]);
```

Then in your Blade layout:
```blade
@if(session('toast'))
<script>
    toast.{{ session('toast.type') }}('{{ session('toast.message') }}');
</script>
@endif
```

---

## 📱 Offline Features

### Check Online Status
```javascript
if (navigator.onLine) {
    console.log('Online');
} else {
    console.log('Offline');
}

// Listen for changes
window.addEventListener('online', () => {
    toast.success('Connection restored!');
});

window.addEventListener('offline', () => {
    toast.warning('You are now offline');
});
```

### Save Data Offline
```javascript
// Initialize offline manager (already done on page load)
await offlineManager.init();

// Save products for offline access
await offlineManager.saveData('products', productsArray);

// Get cached products
const products = await offlineManager.getData('products');

// Save pending sale
await offlineManager.savePendingSale({
    customer_id: 123,
    items: [...],
    total: 5000
});

// Get pending sales
const pending = await offlineManager.getPendingSales();
```

---

## 🎨 CSS Classes

### Modern Buttons
```html
<button class="action-btn primary">Primary Action</button>
<button class="action-btn success">Success Action</button>
<button class="action-btn danger">Delete</button>
<button class="action-btn secondary">Cancel</button>
```

### Modern Inputs
```html
<input type="text" class="modern-input" placeholder="Enter text">
<input type="text" class="modern-input error" placeholder="Has error">
```

### Status Badges (HTML)
```html
<span class="status-badge active">Active</span>
<span class="status-badge overdue">Overdue</span>
<span class="status-badge pending">Pending</span>
<span class="status-badge completed">Completed</span>
```

### Loading Spinner
```html
<div class="spinner"></div>
```

### Progress Bar
```html
<div class="progress-bar-modern">
    <div class="progress-bar-fill" style="width: 75%"></div>
</div>
```

### Empty State
```html
<div class="empty-state">
    <div class="empty-state-icon">📦</div>
    <div class="empty-state-title">No Products Found</div>
    <div class="empty-state-message">Start by adding your first product</div>
    <button class="action-btn primary">Add Product</button>
</div>
```

### Skeleton Loader
```html
<div class="skeleton skeleton-title"></div>
<div class="skeleton skeleton-text"></div>
<div class="skeleton skeleton-text"></div>
```

---

## 🎯 Color System

### CSS Variables
```css
var(--primary)           /* #0F172A - Dark Navy */
var(--primary-variant)   /* #3B82F6 - Modern Blue */
var(--success)           /* #10B981 - Emerald */
var(--warning)           /* #F59E0B - Amber */
var(--error)             /* #EF4444 - Red */
var(--info)              /* #38BDF8 - Sky Blue */
var(--background)        /* #F3F4F6 - Light Gray */
var(--surface)           /* #FFFFFF - White */
var(--text-primary)      /* #0F172A - Charcoal */
var(--text-secondary)    /* #475569 - Medium Gray */
```

### Usage
```css
.my-element {
    background: var(--primary-variant);
    color: var(--on-primary);
    border: 1px solid var(--outline);
}
```

---

## 📊 Dashboard Enhancements

### Sales Chart (Already Implemented)
The dashboard includes an interactive Chart.js visualization showing the last 7 days of sales.

### Stat Cards (Already Implemented)
Four key metrics displayed with:
- Color-coded icons
- Hover animations
- Trend indicators
- Click-through links

### Quick Actions (Already Implemented)
Prominent action buttons for:
- Create New Sale
- Add Customer
- Accounts Receivable
- Manage Inventory
- Notifications

---

## 🔧 Service Worker Commands

### Check Registration
```javascript
// In browser console
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('Registered:', regs);
});
```

### Unregister (for testing)
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => reg.unregister());
});
```

### Force Update
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => reg.update());
});
```

### Clear Cache
```javascript
caches.keys().then(keys => {
    keys.forEach(key => caches.delete(key));
});
```

---

## 📱 PWA Installation

### Desktop (Chrome/Edge)
1. Look for install icon in address bar
2. Click "Install EquiServe"
3. App opens in standalone window

### Mobile (Chrome Android)
1. Tap menu (⋮)
2. Select "Add to Home screen"
3. Confirm installation
4. App icon added to home screen

### iOS (Safari)
1. Tap share button
2. Select "Add to Home Screen"
3. Confirm
4. App icon added to home screen

---

## 🎓 Best Practices

### When to Use Toast Notifications
✅ **DO**: Success confirmations, error messages, warnings
❌ **DON'T**: Long messages, critical errors (use modals instead)

### When to Use Status Badges
✅ **DO**: Order status, payment status, stock levels
❌ **DON'T**: Long text, multiple statuses at once

### When to Use Offline Storage
✅ **DO**: Product catalog, customer list, pending transactions
❌ **DON'T**: Sensitive data, large files, temporary data

### When to Use Modern Components
✅ **DO**: New features, redesigned pages
❌ **DON'T**: Mix with old styles (maintain consistency)

---

## 🚀 Performance Tips

### Optimize Images
```bash
# Use WebP format
# Compress before upload
# Lazy load images
```

### Minimize JavaScript
```javascript
// Use event delegation
document.addEventListener('click', (e) => {
    if (e.target.matches('.action-btn')) {
        // Handle click
    }
});
```

### Cache Strategically
```javascript
// Cache static assets aggressively
// Cache API responses with TTL
// Don't cache user-specific data
```

---

## 📞 Common Issues & Solutions

### Issue: Service Worker Not Working
**Solution**: Ensure HTTPS is enabled, check browser console

### Issue: Offline Page Not Showing
**Solution**: Verify `public/offline.html` exists, clear cache

### Issue: Toast Not Appearing
**Solution**: Check `toast-notifications.js` is loaded, verify no JS errors

### Issue: Icons Not Loading
**Solution**: Add icon files to `public/images/`, update manifest

### Issue: Styles Not Applied
**Solution**: Run `npm run build`, clear browser cache

---

## 📚 Additional Resources

- **Font Awesome Icons**: https://fontawesome.com/icons
- **Chart.js Docs**: https://www.chartjs.org/docs/
- **PWA Guide**: https://web.dev/progressive-web-apps/
- **Service Worker API**: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API

---

**Last Updated**: November 21, 2025
**Version**: 2.0.0
