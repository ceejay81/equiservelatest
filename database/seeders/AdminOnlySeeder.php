<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminOnlySeeder extends Seeder
{
    /**
     * Seed only admin user for manual CRUD testing.
     * Use this when you want a clean database to test create/update/delete operations.
     */
    public function run(): void
    {
        $this->command->info('🧪 Creating admin user for testing...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear all data
        \App\Models\Payment::truncate();
        \App\Models\LoanPenalty::truncate();
        \App\Models\Loan::truncate();
        \App\Models\SaleItem::truncate();
        \App\Models\Sale::truncate();
        \App\Models\StockMovement::truncate();
        \App\Models\Rebate::truncate();
        \App\Models\Notification::truncate();
        \App\Models\Product::truncate();
        \App\Models\Customer::truncate();
        User::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Create only admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@equiserve.test',
            'username' => 'admin',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);
        
        $this->command->newLine();
        $this->command->info('✅ Admin user created successfully!');
        $this->command->newLine();
        
        $this->command->info('🔑 Login Credentials:');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Email', 'admin@equiserve.test'],
                ['Username', 'admin'],
                ['Password', 'password'],
                ['Role', 'Admin (Full Access)'],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('📝 Database Status:');
        $this->command->table(
            ['Table', 'Records'],
            [
                ['Users', '1 (Admin only)'],
                ['Customers', '0 (Empty - ready for testing)'],
                ['Products', '0 (Empty - ready for testing)'],
                ['Sales', '0 (Empty - ready for testing)'],
                ['Loans', '0 (Empty - ready for testing)'],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('🎯 Ready for CRUD Testing!');
        $this->command->info('   You can now manually test:');
        $this->command->info('   - Create customers, products, sales');
        $this->command->info('   - Update records');
        $this->command->info('   - Delete records');
        $this->command->info('   - Test validations');
        $this->command->info('   - Debug operations');
        $this->command->newLine();
    }
}
