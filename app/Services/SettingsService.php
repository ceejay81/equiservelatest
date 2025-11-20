<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Exception;

class SettingsService
{
    protected $settingsPath = 'settings/settings.json';
    protected $defaultsPath = 'settings/defaults.json';
    protected $auditPath = 'settings/audit.json';
    
    /**
     * In-memory cache for request lifecycle
     */
    protected static $settingsCache = null;
    
    /**
     * Load settings from file with error handling
     */
    protected function load(): array
    {
        try {
            // Return cached settings if available
            if (self::$settingsCache !== null) {
                return self::$settingsCache;
            }
            
            // Check if settings file exists
            if (!Storage::exists($this->settingsPath)) {
                // If settings file doesn't exist, copy from defaults
                $this->initializeSettings();
            }
            
            // Read settings file with file locking
            $contents = Storage::get($this->settingsPath);
            $settings = json_decode($contents, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in settings file: ' . json_last_error_msg());
            }
            
            // Cache the settings
            self::$settingsCache = $settings;
            
            return $settings;
        } catch (Exception $e) {
            // Log error and return defaults
            \Log::error('Failed to load settings: ' . $e->getMessage());
            return $this->loadDefaults();
        }
    }
    
    /**
     * Save settings to file with file locking
     */
    protected function save(array $settings): bool
    {
        try {
            // Update metadata
            $settings['_metadata'] = [
                'last_modified' => now()->format('Y-m-d H:i:s'),
                'modified_by' => auth()->check() ? auth()->user()->name : 'system'
            ];
            
            // Encode to JSON with pretty print
            $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            
            if ($json === false) {
                throw new Exception('Failed to encode settings to JSON');
            }
            
            // Write to file (Storage facade handles locking)
            Storage::put($this->settingsPath, $json);
            
            // Clear cache
            self::$settingsCache = null;
            
            return true;
        } catch (Exception $e) {
            \Log::error('Failed to save settings: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Load default settings
     */
    protected function loadDefaults(): array
    {
        try {
            if (!Storage::exists($this->defaultsPath)) {
                // Return hardcoded defaults if file doesn't exist
                return $this->getHardcodedDefaults();
            }
            
            $contents = Storage::get($this->defaultsPath);
            $defaults = json_decode($contents, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in defaults file');
            }
            
            return $defaults;
        } catch (Exception $e) {
            \Log::error('Failed to load defaults: ' . $e->getMessage());
            return $this->getHardcodedDefaults();
        }
    }
    
    /**
     * Initialize settings file from defaults
     */
    protected function initializeSettings(): void
    {
        try {
            $defaults = $this->loadDefaults();
            
            // Ensure directory exists
            if (!Storage::exists('settings')) {
                Storage::makeDirectory('settings');
            }
            
            // Copy defaults to settings
            $this->save($defaults);
        } catch (Exception $e) {
            \Log::error('Failed to initialize settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Get hardcoded default values as fallback
     */
    protected function getHardcodedDefaults(): array
    {
        return [
            'loan' => [
                'min_down_payment_percent' => 20.0,
                'max_term_months' => 36,
                'default_interest_rate' => 2.0
            ],
            'inventory' => [
                'low_stock_threshold' => 10,
                'critical_stock_threshold' => 5
            ],
            'notifications' => [
                'enabled' => true,
                'low_stock_alert' => true,
                'critical_stock_alert' => true,
                'loan_payment_due' => true
            ],
            'payment' => [
                'cash_enabled' => true,
                'bank_transfer_enabled' => true,
                'check_enabled' => true,
                'online_enabled' => false
            ],
            'modules' => [
                'products' => [
                    'require_image' => false,
                    'require_category' => false
                ],
                'sales' => [
                    'allow_negative_stock' => false
                ]
            ]
        ];
    }
    
    /**
     * Clear the settings cache
     */
    public function clearCache(): void
    {
        self::$settingsCache = null;
    }
    
    /**
     * Get setting value with fallback to default
     */
    public function get(string $key, $default = null)
    {
        try {
            $settings = $this->load();
            
            // Use dot notation to access nested values
            $value = Arr::get($settings, $key);
            
            // If value is null, try to get from defaults
            if ($value === null) {
                $defaults = $this->loadDefaults();
                $value = Arr::get($defaults, $key, $default);
            }
            
            return $value ?? $default;
        } catch (Exception $e) {
            \Log::error("Failed to get setting '{$key}': " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Set setting value
     */
    public function set(string $key, $value): bool
    {
        try {
            $settings = $this->load();
            
            // Get old value for audit
            $oldValue = Arr::get($settings, $key);
            
            // Set new value using dot notation
            Arr::set($settings, $key, $value);
            
            // Save settings
            $result = $this->save($settings);
            
            // Create audit entry if save was successful
            if ($result) {
                $this->audit($key, $oldValue, $value);
            }
            
            return $result;
        } catch (Exception $e) {
            \Log::error("Failed to set setting '{$key}': " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all settings
     */
    public function all(): array
    {
        try {
            $settings = $this->load();
            
            // Remove metadata from returned settings
            unset($settings['_metadata']);
            
            return $settings;
        } catch (Exception $e) {
            \Log::error('Failed to get all settings: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all settings in a group
     */
    public function getGroup(string $group): array
    {
        try {
            $settings = $this->load();
            
            // Get the group settings
            $groupSettings = Arr::get($settings, $group, []);
            
            // If group doesn't exist, try defaults
            if (empty($groupSettings)) {
                $defaults = $this->loadDefaults();
                $groupSettings = Arr::get($defaults, $group, []);
            }
            
            return $groupSettings;
        } catch (Exception $e) {
            \Log::error("Failed to get group '{$group}': " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update multiple settings at once (bulk update for a group)
     */
    public function updateGroup(string $group, array $newSettings): bool
    {
        try {
            $settings = $this->load();
            
            // Get current group settings for audit
            $oldGroupSettings = Arr::get($settings, $group, []);
            
            // Update each setting in the group
            foreach ($newSettings as $key => $value) {
                $fullKey = $group . '.' . $key;
                Arr::set($settings, $fullKey, $value);
            }
            
            // Save settings
            $result = $this->save($settings);
            
            // Create audit entries for each changed setting
            if ($result) {
                foreach ($newSettings as $key => $value) {
                    $fullKey = $group . '.' . $key;
                    $oldValue = Arr::get($oldGroupSettings, $key);
                    
                    // Only audit if value actually changed
                    if ($oldValue !== $value) {
                        $this->audit($fullKey, $oldValue, $value);
                    }
                }
            }
            
            return $result;
        } catch (Exception $e) {
            \Log::error("Failed to update group '{$group}': " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate setting value using Laravel validation
     */
    public function validate(string $key, $value): bool
    {
        try {
            // Get validation rules from config
            $rules = config('settings.validation', []);
            
            // Check if validation rule exists for this key
            if (!isset($rules[$key])) {
                // No validation rule defined, consider it valid
                return true;
            }
            
            // Create validator
            $validator = Validator::make(
                [$key => $value],
                [$key => $rules[$key]]
            );
            
            return !$validator->fails();
        } catch (Exception $e) {
            \Log::error("Failed to validate setting '{$key}': " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reset a settings group to defaults
     */
    public function resetGroup(string $group): bool
    {
        try {
            $settings = $this->load();
            $defaults = $this->loadDefaults();
            
            // Get default values for the group
            $defaultGroupSettings = Arr::get($defaults, $group, []);
            
            if (empty($defaultGroupSettings)) {
                \Log::warning("No defaults found for group '{$group}'");
                return false;
            }
            
            // Get current group settings for audit
            $oldGroupSettings = Arr::get($settings, $group, []);
            
            // Replace group settings with defaults
            Arr::set($settings, $group, $defaultGroupSettings);
            
            // Save settings
            $result = $this->save($settings);
            
            // Create audit entries for reset action
            if ($result) {
                foreach ($defaultGroupSettings as $key => $value) {
                    $fullKey = $group . '.' . $key;
                    $oldValue = Arr::get($oldGroupSettings, $key);
                    
                    // Only audit if value actually changed
                    if ($oldValue !== $value) {
                        $this->audit($fullKey, $oldValue, $value);
                    }
                }
            }
            
            return $result;
        } catch (Exception $e) {
            \Log::error("Failed to reset group '{$group}': " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reset all settings to defaults
     */
    public function resetAll(): bool
    {
        try {
            $defaults = $this->loadDefaults();
            
            // Save defaults as current settings
            $result = $this->save($defaults);
            
            // Create audit entry for reset all action
            if ($result) {
                $this->audit('_system', 'all_settings', 'reset_to_defaults');
            }
            
            return $result;
        } catch (Exception $e) {
            \Log::error('Failed to reset all settings: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create audit log entry
     */
    protected function audit(string $key, $oldValue, $newValue): void
    {
        try {
            // Load existing audit log
            $auditLog = $this->loadAuditLog();
            
            // Create new audit entry
            $entry = [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'user' => auth()->check() ? auth()->user()->name : 'system',
                'user_id' => auth()->check() ? auth()->id() : null,
                'setting_key' => $key,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'ip_address' => request()->ip() ?? '127.0.0.1'
            ];
            
            // Add entry to beginning of array
            array_unshift($auditLog, $entry);
            
            // Limit to last 1000 entries
            if (count($auditLog) > 1000) {
                $auditLog = array_slice($auditLog, 0, 1000);
            }
            
            // Save audit log
            $this->saveAuditLog($auditLog);
        } catch (Exception $e) {
            \Log::error("Failed to create audit entry for '{$key}': " . $e->getMessage());
        }
    }
    
    /**
     * Load audit log from file
     */
    protected function loadAuditLog(): array
    {
        try {
            if (!Storage::exists($this->auditPath)) {
                // Initialize empty audit log
                $this->saveAuditLog([]);
                return [];
            }
            
            $contents = Storage::get($this->auditPath);
            $auditLog = json_decode($contents, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in audit log file');
            }
            
            return $auditLog ?? [];
        } catch (Exception $e) {
            \Log::error('Failed to load audit log: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Save audit log to file
     */
    protected function saveAuditLog(array $auditLog): void
    {
        try {
            // Encode to JSON with pretty print
            $json = json_encode($auditLog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            
            if ($json === false) {
                throw new Exception('Failed to encode audit log to JSON');
            }
            
            // Write to file
            Storage::put($this->auditPath, $json);
        } catch (Exception $e) {
            \Log::error('Failed to save audit log: ' . $e->getMessage());
        }
    }
    
    /**
     * Get audit log entries
     */
    public function getAuditLog(int $limit = 100): array
    {
        try {
            $auditLog = $this->loadAuditLog();
            
            // Return limited number of entries
            return array_slice($auditLog, 0, $limit);
        } catch (Exception $e) {
            \Log::error('Failed to get audit log: ' . $e->getMessage());
            return [];
        }
    }
}
