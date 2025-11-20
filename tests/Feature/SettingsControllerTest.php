<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $staffUser;
    protected $settingsService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users without email_verified_at
        $this->adminUser = User::create([
            'role' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
        ]);
        
        $this->staffUser = User::create([
            'role' => 'staff',
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'username' => 'staff',
            'password' => bcrypt('password'),
        ]);
        
        // Use fake storage
        Storage::fake('local');
        
        // Initialize settings service
        $this->settingsService = app(SettingsService::class);
        
        // Create defaults file
        $this->createDefaultsFile();
        
        // Set up config
        $this->setUpConfig();
    }

    protected function createDefaultsFile()
    {
        $defaults = [
            'company' => [
                'name' => 'EquiServe',
                'email' => 'info@equiserve.com',
            ],
            'tax' => [
                'enabled' => false,
                'default_rate' => 12.0,
            ],
        ];

        Storage::put('settings/defaults.json', json_encode($defaults, JSON_PRETTY_PRINT));
    }

    protected function setUpConfig()
    {
        config([
            'settings.groups' => [
                'company' => [
                    'title' => 'Company Information',
                    'description' => 'Configure company details',
                    'icon' => 'building',
                    'order' => 1,
                ],
                'tax' => [
                    'title' => 'Tax Settings',
                    'description' => 'Configure tax rates',
                    'icon' => 'receipt',
                    'order' => 2,
                ],
            ],
            'settings.validation' => [
                'company.name' => 'required|string|max:255',
                'company.email' => 'required|email',
                'tax.default_rate' => 'required|numeric|min:0|max:100',
            ],
            'settings.fields' => [
                'company.name' => [
                    'label' => 'Company Name',
                    'type' => 'text',
                ],
                'company.email' => [
                    'label' => 'Email',
                    'type' => 'email',
                ],
            ],
        ]);
    }

    /**
     * Test index page loads for admin
     */
    public function test_index_page_loads_for_admin()
    {
        $response = $this->actingAs($this->adminUser)->get(route('settings.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('settings.index');
        $response->assertViewHas('groups');
    }

    /**
     * Test index page is forbidden for non-admin
     */
    public function test_index_page_forbidden_for_non_admin()
    {
        $response = $this->actingAs($this->staffUser)->get(route('settings.index'));
        
        $response->assertStatus(403);
    }

    /**
     * Test index page requires authentication
     */
    public function test_index_page_requires_authentication()
    {
        $response = $this->get(route('settings.index'));
        
        $response->assertRedirect(route('login'));
    }

    /**
     * Test group page loads correctly
     */
    public function test_group_page_loads_correctly()
    {
        $response = $this->actingAs($this->adminUser)->get(route('settings.show', 'company'));
        
        $response->assertStatus(200);
        $response->assertViewIs('settings.show');
        $response->assertViewHas('group', 'company');
        $response->assertViewHas('groupInfo');
        $response->assertViewHas('settings');
    }

    /**
     * Test group page returns 404 for invalid group
     */
    public function test_group_page_returns_404_for_invalid_group()
    {
        $response = $this->actingAs($this->adminUser)->get(route('settings.show', 'invalid_group'));
        
        $response->assertStatus(404);
    }

    /**
     * Test update operation saves settings
     */
    public function test_update_operation_saves_settings()
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('settings.update', 'company'), [
                'name' => 'Updated Company Name',
                'email' => 'updated@example.com',
            ]);
        
        $response->assertRedirect(route('settings.show', 'company'));
        $response->assertSessionHas('success');
        
        // Verify settings were saved
        $this->assertEquals('Updated Company Name', $this->settingsService->get('company.name'));
        $this->assertEquals('updated@example.com', $this->settingsService->get('company.email'));
    }

    /**
     * Test update operation validates input
     */
    public function test_update_operation_validates_input()
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('settings.update', 'company'), [
                'name' => '', // Required field
                'email' => 'invalid-email', // Invalid email
            ]);
        
        $response->assertSessionHasErrors(['name', 'email']);
    }

    /**
     * Test update operation validates numeric ranges
     */
    public function test_update_operation_validates_numeric_ranges()
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('settings.update', 'tax'), [
                'default_rate' => 150, // Exceeds max of 100
            ]);
        
        $response->assertSessionHasErrors(['default_rate']);
    }

    /**
     * Test reset operation restores defaults
     */
    public function test_reset_operation_restores_defaults()
    {
        // First, change a setting
        $this->settingsService->set('company.name', 'Changed Name');
        
        // Reset the group
        $response = $this->actingAs($this->adminUser)
            ->post(route('settings.reset', 'company'));
        
        $response->assertRedirect(route('settings.show', 'company'));
        $response->assertSessionHas('success');
        
        // Verify settings were reset
        $this->assertEquals('EquiServe', $this->settingsService->get('company.name'));
    }

    /**
     * Test export generates JSON file
     */
    public function test_export_generates_json_file()
    {
        // Set some settings
        $this->settingsService->set('company.name', 'Test Company');
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('settings.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        
        // Verify JSON structure
        $data = $response->json();
        $this->assertArrayHasKey('_export_info', $data);
        $this->assertArrayHasKey('settings', $data);
    }

    /**
     * Test import shows preview page
     */
    public function test_import_shows_preview_page()
    {
        // Create a test import file
        $importData = [
            'settings' => [
                'company' => [
                    'name' => 'Imported Company',
                    'email' => 'imported@example.com',
                ],
            ],
        ];
        
        $file = UploadedFile::fake()->createWithContent(
            'settings.json',
            json_encode($importData)
        );
        
        $response = $this->actingAs($this->adminUser)
            ->post(route('settings.import'), [
                'file' => $file,
            ]);
        
        $response->assertStatus(200);
        $response->assertViewIs('settings.import-preview');
        $response->assertViewHas('changes');
    }

    /**
     * Test import validates file format
     */
    public function test_import_validates_file_format()
    {
        $file = UploadedFile::fake()->create('invalid.txt', 100);
        
        $response = $this->actingAs($this->adminUser)
            ->post(route('settings.import'), [
                'file' => $file,
            ]);
        
        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test import applies settings when confirmed
     */
    public function test_import_applies_settings_when_confirmed()
    {
        $importData = [
            'settings' => [
                'company' => [
                    'name' => 'Imported Company',
                    'email' => 'imported@example.com',
                ],
            ],
        ];
        
        $encodedData = base64_encode(json_encode($importData));
        
        $response = $this->actingAs($this->adminUser)
            ->post(route('settings.import'), [
                'confirmed' => 'true',
                'import_data' => $encodedData,
            ]);
        
        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');
        
        // Verify settings were imported
        $this->assertEquals('Imported Company', $this->settingsService->get('company.name'));
    }

    /**
     * Test audit log page loads
     */
    public function test_audit_log_page_loads()
    {
        // Create some audit entries
        $this->settingsService->set('company.name', 'Test Company');
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('settings.audit'));
        
        $response->assertStatus(200);
        $response->assertViewIs('settings.audit');
        $response->assertViewHas('entries');
    }

    /**
     * Test audit log filters by date
     */
    public function test_audit_log_filters_by_date()
    {
        $this->settingsService->set('company.name', 'Test Company');
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('settings.audit', [
                'date_from' => now()->subDay()->format('Y-m-d'),
                'date_to' => now()->addDay()->format('Y-m-d'),
            ]));
        
        $response->assertStatus(200);
        $response->assertViewHas('entries');
    }

    /**
     * Test audit log filters by setting key
     */
    public function test_audit_log_filters_by_setting_key()
    {
        $this->settingsService->set('company.name', 'Test Company');
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('settings.audit', [
                'setting_key' => 'company.name',
            ]));
        
        $response->assertStatus(200);
        $response->assertViewHas('entries');
    }

    /**
     * Test non-admin cannot update settings
     */
    public function test_non_admin_cannot_update_settings()
    {
        $response = $this->actingAs($this->staffUser)
            ->put(route('settings.update', 'company'), [
                'name' => 'Unauthorized Update',
            ]);
        
        $response->assertStatus(403);
    }

    /**
     * Test non-admin cannot reset settings
     */
    public function test_non_admin_cannot_reset_settings()
    {
        $response = $this->actingAs($this->staffUser)
            ->post(route('settings.reset', 'company'));
        
        $response->assertStatus(403);
    }

    /**
     * Test non-admin cannot export settings
     */
    public function test_non_admin_cannot_export_settings()
    {
        $response = $this->actingAs($this->staffUser)
            ->get(route('settings.export'));
        
        $response->assertStatus(403);
    }

    /**
     * Test non-admin cannot import settings
     */
    public function test_non_admin_cannot_import_settings()
    {
        $file = UploadedFile::fake()->createWithContent(
            'settings.json',
            json_encode(['settings' => []])
        );
        
        $response = $this->actingAs($this->staffUser)
            ->post(route('settings.import'), [
                'file' => $file,
            ]);
        
        $response->assertStatus(403);
    }
}
