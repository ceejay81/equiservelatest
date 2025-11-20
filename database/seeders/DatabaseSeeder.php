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
        $this->command->info('🌱 Starting comprehensive database seeding...');
        $this->command->newLine();
        
        // Run comprehensive seeder
        $this->call(ComprehensiveSeeder::class);
        
        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        
        // Display login credentials
        $this->command->info('📝 Login Credentials:');
        $this->command->table(
            ['Role', 'Email', 'Username', 'Password'],
            [
                ['Admin', 'admin@equiserve.test', 'admin', 'password'],
                ['Manager', 'manager@equiserve.test', 'manager', 'password'],
                ['Staff', 'staff@equiserve.test', 'staff', 'password'],
                ['Staff', 'staff2@equiserve.test', 'staff2', 'password'],
            ]
        );
    }
}

