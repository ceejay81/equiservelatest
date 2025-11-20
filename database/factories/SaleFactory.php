<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $saleType = fake()->randomElement(['cash', 'loan']);
        $paymentMode = fake()->randomElement(['cash', 'online']);
        $totalAmount = fake()->randomFloat(2, 2000, 120000);
        $amountTendered = $paymentMode === 'cash' ? $totalAmount + fake()->randomFloat(2, 0, 500) : null;
        
        return [
            'sale_number' => strtoupper(fake()->unique()->bothify('S-#####')),
            'customer_id' => Customer::query()->inRandomOrder()->value('id') ?? Customer::factory(),
            'user_id' => User::query()->inRandomOrder()->value('id') ?? 1,
            'sale_type' => $saleType,
            'payment_mode' => $paymentMode,
            'amount_tendered' => $amountTendered,
            'amount_paid' => $totalAmount,
            'reference_number' => $paymentMode === 'online' ? 'REF-' . strtoupper(fake()->bothify('##########')) : null,
            'proof_image_path' => $paymentMode === 'online' && fake()->boolean(60) ? 'proofs/' . fake()->uuid() . '.jpg' : null,
            'total_amount' => $totalAmount,
            'discount_total' => fake()->boolean(20) ? fake()->randomFloat(2, 100, 1000) : 0,
            'discount_reason' => fake()->boolean(20) ? fake()->randomElement(['Anniversary Sale', 'Loyalty Discount', 'Bulk Purchase', 'Promo']) : null,
        ];
    }
}
