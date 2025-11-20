<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value from the settings service
     * 
     * @param string $key The setting key in dot notation (e.g., 'company.name')
     * @param mixed $default The default value to return if setting is not found
     * @return mixed The setting value or default
     * 
     * @example
     * // Get company name with default
     * $companyName = setting('company.name', 'EquiServe');
     * 
     * // Get tax rate
     * $taxRate = setting('tax.default_rate', 0);
     * 
     * // Get boolean setting
     * $taxEnabled = setting('tax.enabled', false);
     */
    function setting(string $key, $default = null)
    {
        try {
            return app(\App\Services\SettingsService::class)->get($key, $default);
        } catch (\Exception $e) {
            // Log error and return default
            \Log::error("Helper function 'setting' failed for key '{$key}': " . $e->getMessage());
            return $default;
        }
    }
}
