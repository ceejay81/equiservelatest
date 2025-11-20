<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Admin: full access
        Gate::before(function ($user, $ability) {
            return $user->role === 'admin' ? true : null;
        });

        // Users
        Gate::define('view-users', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('manage-users', function ($user) {
            return $user->role === 'admin' || $user->role === 'manager';
        });

        // Customers
        Gate::define('view-customers', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'staff']);
        });
        Gate::define('manage-customers', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // Products / Inventory
        Gate::define('view-products', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'staff']);
        });
        Gate::define('manage-products', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('view-inventory', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'staff']);
        });
        Gate::define('manage-inventory', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('adjust-stock', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // Sales / Rebates
        Gate::define('view-sales', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'staff']);
        });
        Gate::define('manage-sales', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('view-rebates', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('manage-rebates', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // Loans / Payments
        Gate::define('view-loans', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'staff']);
        });
        Gate::define('manage-loans', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('view-payments', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'staff']);
        });
        Gate::define('manage-payments', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // Audit Logs
        Gate::define('view-audit-logs', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // Reports
        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // Backup
        Gate::define('perform-backup', function ($user) {
            return $user->role === 'admin';
        });

        // Settings
        Gate::define('manage-settings', function ($user) {
            return $user->role === 'admin';
        });
    }
}
