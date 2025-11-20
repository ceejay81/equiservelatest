<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    protected $settingsService;

    /**
     * Create a new controller instance.
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display settings overview page.
     */
    public function index()
    {
        // Get all settings groups from config
        $groups = config('settings.groups') ?? [];
        
        // Ensure $groups is an array
        if (!is_array($groups)) {
            $groups = [];
        }
        
        // Sort groups by order
        if (!empty($groups)) {
            uasort($groups, function($a, $b) {
                return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
            });
        }
        
        // Add color to each group
        $colors = ['blue', 'green', 'orange', 'purple', 'indigo', 'pink', 'teal', 'cyan'];
        $colorIndex = 0;
        foreach ($groups as $key => $group) {
            $groups[$key]['color'] = $colors[$colorIndex % count($colors)];
            $colorIndex++;
        }
        
        // Get current settings for metadata
        $allSettings = $this->settingsService->all();
        $metadata = $this->settingsService->get('_metadata', []);
        
        return view('settings.index', [
            'groups' => $groups,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Display specific settings group page.
     */
    public function show($group)
    {
        // Validate that group exists
        $groups = config('settings.groups', []);
        if (!isset($groups[$group])) {
            abort(404, 'Settings group not found');
        }
        
        // Get group metadata
        $groupInfo = $groups[$group];
        
        // Get current settings for this group
        $settings = $this->settingsService->getGroup($group);
        
        // Get field definitions for this group
        $fields = config('settings.fields', []);
        $groupFields = [];
        
        foreach ($fields as $key => $field) {
            // Check if field belongs to this group
            if (strpos($key, $group . '.') === 0) {
                $fieldKey = substr($key, strlen($group) + 1);
                $groupFields[$fieldKey] = array_merge($field, [
                    'key' => $key,
                    'value' => $settings[$fieldKey] ?? null,
                ]);
            }
        }
        
        // Get metadata
        $metadata = $this->settingsService->get('_metadata', []);
        
        return view('settings.show', [
            'group' => $group,
            'groupInfo' => $groupInfo,
            'settings' => $settings,
            'fields' => $groupFields,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Update settings for a specific group.
     */
    public function update(Request $request, $group)
    {
        // Validate that group exists
        $groups = config('settings.groups', []);
        if (!isset($groups[$group])) {
            abort(404, 'Settings group not found');
        }
        
        // Get validation rules for this group
        $allRules = config('settings.validation', []);
        $groupRules = [];
        $inputData = [];
        
        foreach ($allRules as $key => $rule) {
            // Check if rule belongs to this group
            if (strpos($key, $group . '.') === 0) {
                $fieldKey = substr($key, strlen($group) + 1);
                $groupRules[$fieldKey] = $rule;
                
                // Get input value, handling checkboxes/toggles
                $inputData[$fieldKey] = $request->input($fieldKey);
                
                // Convert checkbox values to boolean
                if (strpos($rule, 'boolean') !== false) {
                    $inputData[$fieldKey] = $request->has($fieldKey) ? true : false;
                }
            }
        }
        
        // Custom validation messages
        $messages = config('settings.messages', []);
        
        // Additional validation for inventory thresholds
        if ($group === 'inventory') {
            $groupRules['critical_stock_threshold'][] = 'lte:low_stock_threshold';
        }
        
        // Validate input
        $validator = Validator::make($inputData, $groupRules, $messages);
        
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $validated = $validator->validated();
        
        // Additional validation for payment methods (at least one must be enabled)
        if ($group === 'payment') {
            $atLeastOneEnabled = false;
            foreach ($validated as $key => $value) {
                if (strpos($key, '_enabled') !== false && $value === true) {
                    $atLeastOneEnabled = true;
                    break;
                }
            }
            
            if (!$atLeastOneEnabled) {
                return back()
                    ->withErrors(['error' => 'At least one payment method must be enabled.'])
                    ->withInput();
            }
        }
        
        // Update settings
        try {
            $result = $this->settingsService->updateGroup($group, $validated);
            
            if ($result) {
                return redirect()
                    ->route('settings.show', $group)
                    ->with('success', ucfirst($groups[$group]['title']) . ' updated successfully');
            } else {
                return back()
                    ->withErrors(['error' => 'Failed to update settings. Please try again.'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update settings: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Reset settings group to defaults.
     */
    public function reset($group)
    {
        // Validate that group exists
        $groups = config('settings.groups', []);
        if (!isset($groups[$group])) {
            abort(404, 'Settings group not found');
        }
        
        try {
            $result = $this->settingsService->resetGroup($group);
            
            if ($result) {
                return redirect()
                    ->route('settings.show', $group)
                    ->with('success', ucfirst($groups[$group]['title']) . ' reset to defaults successfully');
            } else {
                return back()
                    ->with('error', 'Failed to reset settings. Please try again.');
            }
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    /**
     * Export all settings as JSON file.
     */
    public function export()
    {
        try {
            // Get all settings
            $settings = $this->settingsService->all();
            
            // Add export metadata
            $export = [
                '_export_info' => [
                    'exported_at' => now()->format('Y-m-d H:i:s'),
                    'exported_by' => auth()->user()->name,
                    'version' => '1.0',
                ],
                'settings' => $settings,
            ];
            
            // Generate filename with timestamp
            $filename = 'settings_export_' . now()->format('Y-m-d_His') . '.json';
            
            // Create JSON response
            return response()->json($export, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    /**
     * Import settings from JSON file.
     */
    public function import(Request $request)
    {
        // If confirmed, apply the import
        if ($request->input('confirmed') === 'true') {
            try {
                $importData = base64_decode($request->input('import_data'));
                $data = json_decode($importData, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return redirect()
                        ->route('settings.index')
                        ->with('error', 'Invalid import data: ' . json_last_error_msg());
                }
                
                // Extract settings from import data
                $importSettings = $data['settings'] ?? $data;
                
                // Remove metadata and export info
                unset($importSettings['_metadata']);
                unset($importSettings['_export_info']);
                
                // Apply each setting group
                $imported = 0;
                $errors = [];
                
                foreach ($importSettings as $group => $groupSettings) {
                    if ($group === '_metadata' || $group === '_export_info') {
                        continue; // Skip metadata
                    }
                    
                    try {
                        $result = $this->settingsService->updateGroup($group, $groupSettings);
                        if ($result) {
                            $imported++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Failed to import {$group}: " . $e->getMessage();
                    }
                }
                
                if (count($errors) > 0) {
                    $message = "Import completed with errors. Imported {$imported} groups. Errors: " . implode(', ', $errors);
                    return redirect()
                        ->route('settings.index')
                        ->with('warning', $message);
                }
                
                return redirect()
                    ->route('settings.index')
                    ->with('success', "Settings imported successfully. {$imported} groups updated.");
                    
            } catch (\Exception $e) {
                return redirect()
                    ->route('settings.index')
                    ->with('error', 'Failed to import settings: ' . $e->getMessage());
            }
        }
        
        // Validate file upload for preview
        $request->validate([
            'file' => 'required|file|mimes:json,txt|max:2048', // 2MB max
        ]);
        
        try {
            // Read uploaded file
            $file = $request->file('file');
            $contents = file_get_contents($file->getRealPath());
            $data = json_decode($contents, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()
                    ->with('error', 'Invalid JSON file: ' . json_last_error_msg());
            }
            
            // Extract settings from import data
            $importSettings = $data['settings'] ?? $data;
            
            // Remove metadata and export info
            unset($importSettings['_metadata']);
            unset($importSettings['_export_info']);
            
            if (empty($importSettings)) {
                return back()
                    ->with('error', 'No settings found in import file.');
            }
            
            // Validate that the import file contains valid setting groups
            $validGroups = array_keys(config('settings.groups', []));
            $hasValidGroup = false;
            
            foreach ($importSettings as $group => $groupSettings) {
                if (in_array($group, $validGroups)) {
                    $hasValidGroup = true;
                    break;
                }
            }
            
            if (!$hasValidGroup) {
                return back()
                    ->with('error', 'Import file does not contain any valid setting groups.');
            }
            
            // Show preview page
            $currentSettings = $this->settingsService->all();
            $changes = $this->compareSettings($currentSettings, $importSettings);
            
            return view('settings.import-preview', [
                'changes' => $changes,
                'importData' => base64_encode($contents),
            ]);
            
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to import settings: ' . $e->getMessage());
        }
    }

    /**
     * Compare current settings with import settings to show changes.
     */
    protected function compareSettings($current, $import)
    {
        $changes = [];
        $groups = config('settings.groups', []);
        
        foreach ($import as $group => $groupSettings) {
            // Skip metadata and export info
            if ($group === '_metadata' || $group === '_export_info') {
                continue;
            }
            
            // Skip if group doesn't exist in config
            if (!isset($groups[$group])) {
                continue;
            }
            
            $groupChanges = [];
            $currentGroup = $current[$group] ?? [];
            
            // Ensure groupSettings is an array
            if (!is_array($groupSettings)) {
                continue;
            }
            
            foreach ($groupSettings as $key => $value) {
                $currentValue = $currentGroup[$key] ?? null;
                
                // Compare values (handle type differences)
                $isDifferent = false;
                
                if ($currentValue === null && $value !== null) {
                    $isDifferent = true;
                } elseif ($currentValue !== null && $value === null) {
                    $isDifferent = true;
                } elseif (is_bool($currentValue) || is_bool($value)) {
                    // Handle boolean comparison
                    $isDifferent = (bool)$currentValue !== (bool)$value;
                } elseif (is_numeric($currentValue) && is_numeric($value)) {
                    // Handle numeric comparison
                    $isDifferent = (float)$currentValue !== (float)$value;
                } else {
                    // String comparison
                    $isDifferent = (string)$currentValue !== (string)$value;
                }
                
                if ($isDifferent) {
                    $groupChanges[] = [
                        'key' => $key,
                        'current' => $currentValue,
                        'new' => $value,
                    ];
                }
            }
            
            if (!empty($groupChanges)) {
                $changes[$group] = [
                    'title' => $groups[$group]['title'] ?? ucfirst($group),
                    'changes' => $groupChanges,
                ];
            }
        }
        
        return $changes;
    }

    /**
     * Display loan penalty settings page.
     */
    public function loanPenalty()
    {
        $settings = $this->settingsService->getGroup('loan_penalty');
        
        return view('settings.loan-penalty', [
            'pageTitle' => 'Loan & Penalty Settings',
            'settings' => $settings,
        ]);
    }

    /**
     * Update loan penalty settings.
     */
    public function updateLoanPenalty(Request $request)
    {
        $validated = $request->validate([
            'late_penalty_rate' => 'required|numeric|min:0|max:100',
            'maturity_penalty_rate' => 'required|numeric|min:0|max:100',
            'grace_period_days' => 'required|integer|min:0|max:30',
            'rebate_on_time_only' => 'boolean',
        ], [
            'late_penalty_rate.required' => 'Late payment penalty rate is required',
            'late_penalty_rate.numeric' => 'Late payment penalty rate must be a number',
            'late_penalty_rate.min' => 'Late payment penalty rate cannot be negative',
            'late_penalty_rate.max' => 'Late payment penalty rate cannot exceed 100%',
            'maturity_penalty_rate.required' => 'Maturity penalty rate is required',
            'maturity_penalty_rate.numeric' => 'Maturity penalty rate must be a number',
            'maturity_penalty_rate.min' => 'Maturity penalty rate cannot be negative',
            'maturity_penalty_rate.max' => 'Maturity penalty rate cannot exceed 100%',
            'grace_period_days.required' => 'Grace period is required',
            'grace_period_days.integer' => 'Grace period must be a whole number',
            'grace_period_days.min' => 'Grace period cannot be negative',
            'grace_period_days.max' => 'Grace period cannot exceed 30 days',
        ]);

        // Handle checkbox
        $validated['rebate_on_time_only'] = $request->has('rebate_on_time_only') ? 1 : 0;

        try {
            $result = $this->settingsService->updateGroup('loan_penalty', $validated);
            
            if ($result) {
                return redirect()
                    ->route('settings.loan-penalty')
                    ->with('success', 'Loan & Penalty settings updated successfully');
            } else {
                return back()
                    ->withErrors(['error' => 'Failed to update settings. Please try again.'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update settings: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display audit log with filtering and pagination.
     */
    public function auditLog(Request $request)
    {
        try {
            // Get all audit log entries
            $allEntries = $this->settingsService->getAuditLog(1000);
            
            // Apply filters
            $entries = collect($allEntries);
            
            // Filter by date range
            if ($request->has('date_from')) {
                $dateFrom = $request->input('date_from');
                $entries = $entries->filter(function($entry) use ($dateFrom) {
                    return $entry['timestamp'] >= $dateFrom;
                });
            }
            
            if ($request->has('date_to')) {
                $dateTo = $request->input('date_to') . ' 23:59:59';
                $entries = $entries->filter(function($entry) use ($dateTo) {
                    return $entry['timestamp'] <= $dateTo;
                });
            }
            
            // Filter by setting key
            if ($request->has('setting_key') && $request->input('setting_key') !== '') {
                $settingKey = $request->input('setting_key');
                $entries = $entries->filter(function($entry) use ($settingKey) {
                    return stripos($entry['setting_key'], $settingKey) !== false;
                });
            }
            
            // Filter by user
            if ($request->has('user') && $request->input('user') !== '') {
                $user = $request->input('user');
                $entries = $entries->filter(function($entry) use ($user) {
                    return stripos($entry['user'], $user) !== false;
                });
            }
            
            // Paginate results manually
            $perPage = 20;
            $currentPage = $request->input('page', 1);
            $total = $entries->count();
            $entries = $entries->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            // Create pagination object
            $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
                $entries,
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            return view('settings.audit', [
                'entries' => $pagination,
                'filters' => [
                    'date_from' => $request->input('date_from'),
                    'date_to' => $request->input('date_to'),
                    'setting_key' => $request->input('setting_key'),
                    'user' => $request->input('user'),
                ],
            ]);
            
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to load audit log: ' . $e->getMessage());
        }
    }
}
