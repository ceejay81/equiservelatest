<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Offline page (accessible without auth for PWA)
Route::view('/offline', 'offline')->name('offline');

Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Notification routes
    Route::controller(\App\Http\Controllers\NotificationController::class)->group(function () {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::get('/notifications/unread', 'getUnread')->name('notifications.unread');
        Route::post('/notifications/{id}/read', 'markAsRead')->name('notifications.read');
        Route::post('/notifications/{id}/action', 'markAsActioned')->name('notifications.action');
        Route::post('/notifications/mark-all-read', 'markAllAsRead')->name('notifications.markAllRead');
    });
    
    // Settings routes
    Route::middleware('can:manage-settings')->controller(SettingsController::class)->group(function () {
        Route::get('/settings', 'index')->name('settings.index');
        Route::get('/settings/audit', 'auditLog')->name('settings.audit');
        Route::get('/settings/export', 'export')->name('settings.export');
        Route::post('/settings/import', 'import')->name('settings.import');
        Route::get('/settings/loan-penalty', 'loanPenalty')->name('settings.loan-penalty');
        Route::put('/settings/loan-penalty', 'updateLoanPenalty')->name('settings.loan-penalty.update');
        Route::get('/settings/{group}', 'show')->name('settings.show');
        Route::put('/settings/{group}', 'update')->name('settings.update');
        Route::post('/settings/{group}/reset', 'reset')->name('settings.reset');
    });
    
    Route::get('/customers', [CustomerController::class, 'index'])->middleware('can:view-customers');
    Route::get('/customers/create', [CustomerController::class, 'create'])->middleware('can:manage-customers');
    Route::post('/customers', [CustomerController::class, 'store'])->middleware('can:manage-customers');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->middleware('can:view-customers');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->middleware('can:manage-customers');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->middleware('can:manage-customers');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware('can:manage-customers');
    Route::post('/customers/{id}/rebates', [CustomerController::class, 'storeRebate'])->middleware('can:manage-customers');
    Route::post('/customers/{id}/rebates/apply-to-loan', [CustomerController::class, 'applyRebateToLoan'])->middleware('can:manage-customers');
    
    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index')->middleware('can:view-products');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store')->middleware('can:manage-products');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show')->middleware('can:view-products');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('can:manage-products');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('can:manage-products');
    Route::get('/products/export/excel', [ProductController::class, 'export'])->name('products.export')->middleware('can:view-products');
    Route::post('/products/import/excel', [ProductController::class, 'import'])->name('products.import')->middleware('can:manage-products');
    Route::get('/products/template/download', [ProductController::class, 'downloadTemplate'])->name('products.template')->middleware('can:manage-products');
    
    // Sales routes
    Route::controller(SaleController::class)->middleware('can:view-sales')->group(function () {
        Route::get('/sales', 'index')->name('sales.index');
        Route::get('/sales/reconciliation', 'reconciliation')->name('sales.reconciliation');
        Route::get('/sales/check-reference/{reference}', 'checkReference')->middleware('can:manage-sales');
        Route::get('/sales/create', 'create')->name('sales.create')->middleware('can:manage-sales');
        Route::post('/sales', 'store')->name('sales.store')->middleware('can:manage-sales');
        Route::get('/sales/{sale}', 'show')->name('sales.show');
        Route::get('/sales/{sale}/print', 'print')->name('sales.print');
    });
    
    // Loan routes
    Route::controller(LoanController::class)->group(function () {
        Route::get('/loans', 'index')->name('loans.index');
        Route::get('/loans/create', 'create')->name('loans.create');
        Route::post('/loans', 'store')->name('loans.store');
        Route::get('/loans/{loan}', 'show')->name('loans.show');
        Route::get('/loans/{loan}/amortization', 'amortization')->name('loans.amortization');
        Route::get('/loans/{loan}/amortization/print', 'amortizationPrint')->name('loans.amortization.print');
        Route::delete('/loans/{loan}', 'destroy')->name('loans.destroy');
        Route::post('/loans/{loan}/payments', 'storePayment')->name('loans.payments.store');
    });

    // Payment routes (wireframe only)
    Route::view('/payments', 'payments.index')->middleware('can:view-payments');
        
    // Inventory routes
    Route::get('/inventory', [InventoryController::class, 'index'])->middleware('can:view-inventory');
    Route::post('/inventory/products', [InventoryController::class, 'store'])->name('inventory.products.store')->middleware('can:manage-products');
    Route::get('/inventory/products/{product}', [InventoryController::class, 'show'])->name('inventory.products.show')->middleware('can:view-inventory');
    Route::put('/inventory/products/{product}', [InventoryController::class, 'update'])->name('inventory.products.update')->middleware('can:manage-products');
    Route::delete('/inventory/products/{product}', [InventoryController::class, 'destroy'])->name('inventory.products.destroy')->middleware('can:manage-products');
    Route::post('/inventory/products/{product}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.products.adjust')->middleware('can:adjust-stock');
    Route::post('/inventory/products/{product}/receive', [InventoryController::class, 'receiveStock'])->name('inventory.products.receive')->middleware('can:adjust-stock');
    Route::get('/inventory/products/{product}/movements', [InventoryController::class, 'getStockMovements'])->name('inventory.products.movements')->middleware('can:view-inventory');
    Route::view('/rebates', 'rebates.index')->middleware('can:view-rebates');
    Route::view('/audit-logs', 'audit_logs.index')->middleware('can:view-audit-logs');
    Route::get('/users', [UserController::class, 'index'])->middleware('can:view-users');
    
    // Profile routes - all authenticated users can view/edit their own profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Reports routes
    Route::controller(\App\Http\Controllers\ReportsController::class)->middleware('can:view-reports')->group(function () {
        Route::get('/reports', 'index')->name('reports.index');
        Route::get('/reports/sales', 'sales')->name('reports.sales');
        Route::get('/reports/sales/export', 'salesExport')->name('reports.sales.export');
        Route::get('/reports/inventory', 'inventory')->name('reports.inventory');
        Route::get('/reports/inventory/export', 'inventoryExport')->name('reports.inventory.export');
        Route::get('/reports/stock-movements', 'stockMovements')->name('reports.stock-movements');
        Route::get('/reports/collections', 'collections')->name('reports.collections');
        Route::get('/reports/collections/export', 'collectionsExport')->name('reports.collections.export');
        Route::get('/reports/customer-statement', 'customerStatement')->name('reports.customer-statement');
        Route::get('/reports/customer-statement/{customerId}/pdf', 'customerStatementPdf')->name('reports.customer-statement.pdf');
        Route::get('/reports/reconciliation', 'reconciliation')->name('reports.reconciliation');
        Route::get('/reports/top-products', 'topProducts')->name('reports.top-products');
    });
    
    Route::view('/backup', 'backup.index')->middleware('can:perform-backup');
});

require __DIR__.'/auth.php';


