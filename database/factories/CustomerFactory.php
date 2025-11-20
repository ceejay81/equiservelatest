<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'account_number' => 'ACCT-' . sprintf('%06d', fake()->unique()->numberBetween(1, 999999)),
            'full_name' => fake()->name(),
            'contact' => fake()->phoneNumber(),
            'address' => fake()->address(),
        ];
    }
}
