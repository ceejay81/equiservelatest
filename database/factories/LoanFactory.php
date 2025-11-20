<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 5000, 120000);
        $down = round($amount * fake()->randomFloat(2, 0.1, 0.4), 2);
        $termMonths = fake()->randomElement([3, 6, 9, 12, 18, 24]);
        $interestRate = fake()->randomFloat(2, 0, 15); // 0-15% annual interest
        $principal = $amount - $down;
        
        // Calculate monthly payment with interest
        $monthlyRate = $interestRate / 100 / 12;
        if ($monthlyRate > 0) {
            $monthlyAmount = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        } else {
            $monthlyAmount = $principal / $termMonths;
        }
        
        $sale = Sale::query()->inRandomOrder()->first() ?? Sale::factory()->create();
        $status = fake()->randomElement(['active', 'completed', 'overdue']);
        $startDate = fake()->dateTimeBetween('-6 months', 'now');
        
        return [
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'loan_amount' => round($amount, 2),
            'down_payment' => $down,
            'term_months' => $termMonths,
            'interest_rate' => $interestRate,
            'monthly_amount' => round($monthlyAmount, 2),
            'balance' => $status === 'completed' ? 0 : round($principal - fake()->randomFloat(2, 0, $principal * 0.5), 2),
            'status' => $status,
            'start_date' => $startDate,
            'next_due_date' => $status === 'active' ? \Carbon\Carbon::parse($startDate)->addMonth() : null,
            'end_date' => \Carbon\Carbon::parse($startDate)->addMonths($termMonths),
            'remarks' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}
