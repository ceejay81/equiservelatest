<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Groups Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines all settings groups, their metadata,
    | validation rules, and field definitions for the Settings Module.
    |
    */

    'groups' => [
        'loan' => [
            'title' => 'Loan Policies',
            'description' => 'Configure lending rules and terms',
            'icon' => 'hand-holding-usd',
            'order' => 4,
        ],
        'inventory' => [
            'title' => 'Inventory Thresholds',
            'description' => 'Configure stock alert levels',
            'icon' => 'warehouse',
            'order' => 5,
        ],
        'notifications' => [
            'title' => 'Notification Preferences',
            'description' => 'Configure in-app alerts',
            'icon' => 'bell',
            'order' => 6,
        ],
        'payment' => [
            'title' => 'Payment Methods',
            'description' => 'Configure accepted payment types',
            'icon' => 'credit-card',
            'order' => 7,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define Laravel validation rules for each setting key.
    | These rules are applied when updating settings.
    |
    */

    'validation' => [
        // Loan Policies
        'loan.min_down_payment_percent' => 'required|numeric|min:0|max:100',
        'loan.max_term_months' => 'required|integer|min:1|max:120',
        'loan.default_interest_rate' => 'required|numeric|min:0|max:100',
        'loan.allow_zero_interest' => 'nullable|boolean',
        'loan.require_guarantor' => 'nullable|boolean',

        // Inventory Thresholds
        'inventory.low_stock_threshold' => 'required|integer|min:0',
        'inventory.critical_stock_threshold' => 'required|integer|min:0',
        'inventory.auto_reorder_enabled' => 'nullable|boolean',
        'inventory.default_reorder_quantity' => 'nullable|integer|min:1',

        // Notification Preferences
        'notifications.enabled' => 'required|boolean',
        'notifications.low_stock_alert' => 'nullable|boolean',
        'notifications.critical_stock_alert' => 'nullable|boolean',
        'notifications.loan_payment_due' => 'nullable|boolean',
        'notifications.loan_overdue' => 'nullable|boolean',
        'notifications.daily_summary' => 'nullable|boolean',

        // Payment Methods
        'payment.cash_enabled' => 'nullable|boolean',
        'payment.bank_transfer_enabled' => 'nullable|boolean',
        'payment.check_enabled' => 'nullable|boolean',
        'payment.online_enabled' => 'nullable|boolean',


    ],


    /*
    |--------------------------------------------------------------------------
    | Field Definitions
    |--------------------------------------------------------------------------
    |
    | Define field types, labels, and options for UI rendering.
    | Each field definition includes:
    | - type: input type (text, email, number, select, toggle, etc.)
    | - label: Display label for the field
    | - required: Whether the field is required
    | - help: Optional help text
    | - options: For select/radio fields
    |
    */

    'fields' => [
        // Loan Policies Fields
        'loan.min_down_payment_percent' => [
            'type' => 'number',
            'label' => 'Minimum Down Payment (%)',
            'required' => true,
            'help' => 'Minimum down payment percentage required',
            'min' => 0,
            'max' => 100,
            'step' => 0.01,
        ],
        'loan.max_term_months' => [
            'type' => 'number',
            'label' => 'Maximum Loan Term (Months)',
            'required' => true,
            'help' => 'Maximum loan duration in months',
            'min' => 1,
            'max' => 120,
            'step' => 1,
        ],
        'loan.default_interest_rate' => [
            'type' => 'number',
            'label' => 'Default Interest Rate (%)',
            'required' => true,
            'help' => 'Default monthly interest rate',
            'min' => 0,
            'max' => 100,
            'step' => 0.01,
        ],
        'loan.allow_zero_interest' => [
            'type' => 'toggle',
            'label' => 'Allow Zero Interest Loans',
            'required' => false,
            'help' => 'Permit loans with 0% interest',
        ],
        'loan.require_guarantor' => [
            'type' => 'toggle',
            'label' => 'Require Guarantor',
            'required' => false,
            'help' => 'Require a guarantor for all loans',
        ],

        // Inventory Thresholds Fields
        'inventory.low_stock_threshold' => [
            'type' => 'number',
            'label' => 'Low Stock Threshold',
            'required' => true,
            'help' => 'Quantity that triggers low stock warning',
            'min' => 0,
            'step' => 1,
        ],
        'inventory.critical_stock_threshold' => [
            'type' => 'number',
            'label' => 'Critical Stock Threshold',
            'required' => true,
            'help' => 'Quantity that triggers critical stock alert',
            'min' => 0,
            'step' => 1,
        ],
        'inventory.auto_reorder_enabled' => [
            'type' => 'toggle',
            'label' => 'Enable Auto-Reorder',
            'required' => false,
            'help' => 'Automatically create reorder alerts',
        ],
        'inventory.default_reorder_quantity' => [
            'type' => 'number',
            'label' => 'Default Reorder Quantity',
            'required' => false,
            'help' => 'Default quantity for reorder suggestions',
            'min' => 1,
            'step' => 1,
        ],

        // Notification Preferences Fields
        'notifications.enabled' => [
            'type' => 'toggle',
            'label' => 'Enable Notifications',
            'required' => true,
            'help' => 'Master switch for all notifications',
        ],
        'notifications.low_stock_alert' => [
            'type' => 'toggle',
            'label' => 'Low Stock Alerts',
            'required' => false,
            'help' => 'Notify when stock reaches low threshold',
        ],
        'notifications.critical_stock_alert' => [
            'type' => 'toggle',
            'label' => 'Critical Stock Alerts',
            'required' => false,
            'help' => 'Notify when stock reaches critical threshold',
        ],
        'notifications.loan_payment_due' => [
            'type' => 'toggle',
            'label' => 'Loan Payment Due Alerts',
            'required' => false,
            'help' => 'Notify when loan payments are due',
        ],
        'notifications.loan_overdue' => [
            'type' => 'toggle',
            'label' => 'Loan Overdue Alerts',
            'required' => false,
            'help' => 'Notify when loan payments are overdue',
        ],
        'notifications.daily_summary' => [
            'type' => 'toggle',
            'label' => 'Daily Summary',
            'required' => false,
            'help' => 'Receive daily activity summary',
        ],

        // Payment Methods Fields
        'payment.cash_enabled' => [
            'type' => 'toggle',
            'label' => 'Cash Payments',
            'required' => false,
            'help' => 'Accept cash payments',
        ],
        'payment.bank_transfer_enabled' => [
            'type' => 'toggle',
            'label' => 'Bank Transfer',
            'required' => false,
            'help' => 'Accept bank transfers',
        ],
        'payment.check_enabled' => [
            'type' => 'toggle',
            'label' => 'Check Payments',
            'required' => false,
            'help' => 'Accept check payments',
        ],
        'payment.online_enabled' => [
            'type' => 'toggle',
            'label' => 'Online Payments',
            'required' => false,
            'help' => 'Accept online/digital payments',
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Messages
    |--------------------------------------------------------------------------
    |
    | Custom error messages for validation failures.
    |
    */

    'messages' => [
        'loan.min_down_payment_percent.min' => 'Down payment percentage cannot be negative.',
        'loan.min_down_payment_percent.max' => 'Down payment percentage cannot exceed 100%.',
        'loan.max_term_months.min' => 'Loan term must be at least 1 month.',
        'loan.max_term_months.max' => 'Loan term cannot exceed 120 months.',
        'inventory.critical_stock_threshold.lte' => 'Critical threshold must be less than or equal to low stock threshold.',
    ],

];
