<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();
        
        // Use DefenseSeeder for comprehensive, realistic data
        // Perfect for thesis defense and demonstrations
        $this->call(DefenseSeeder::class);
        
        // Alternative seeders (uncomment to use):
        
        // AdminOnlySeeder - For manual CRUD testing (empty database + admin user)
        // $this->call(AdminOnlySeeder::class);
        
        // ComprehensiveSeeder - For basic testing data
        // $this->call(ComprehensiveSeeder::class);
    }
}

