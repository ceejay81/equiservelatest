<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\SettingsService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add penalty settings to JSON-based settings
        $settingsService = app(SettingsService::class);
        
        $penaltySettings = [
            'late_penalty_rate' => 3.00,
            'maturity_penalty_rate' => 5.00,
            'grace_period_days' => 3,
            'rebate_on_time_only' => true,
        ];
        
        $settingsService->updateGroup('loan_penalty', $penaltySettings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove penalty settings
        $settingsService = app(SettingsService::class);
        
        $penaltySettings = [
            'late_penalty_rate' => null,
            'maturity_penalty_rate' => null,
            'grace_period_days' => null,
            'rebate_on_time_only' => null,
        ];
        
        $settingsService->updateGroup('loan_penalty', $penaltySettings);
    }
};
