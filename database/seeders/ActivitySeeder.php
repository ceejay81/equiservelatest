<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->get();
        foreach ($customers as $customer) {
            $rows = [];
            $n = rand(2, 5);
            for ($i = 0; $i < $n; $i++) {
                $rows[] = [
                    'user_id' => null,
                    'action' => fake()->randomElement(['created','updated','payment_recorded','loan_created']),
                    'table_name' => 'customers',
                    'record_id' => $customer->id,
                    'timestamp' => now()->subDays(rand(0, 60)),
                ];
            }
            DB::table('audit_logs')->insert($rows);
        }
    }
}
