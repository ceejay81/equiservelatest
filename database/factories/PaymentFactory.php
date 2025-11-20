<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'loan_id' => Loan::query()->inRandomOrder()->value('id') ?? Loan::factory(),
            'amount_paid' => fake()->randomFloat(2, 200, 5000),
            'payment_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'mode_of_payment' => fake()->randomElement(['Cash', 'GCash', 'Bank Transfer']),
        ];
    }
}
