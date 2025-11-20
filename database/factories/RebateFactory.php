<?php

namespace Database\Factories;

use App\Models\Rebate;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class RebateFactory extends Factory
{
    protected $model = Rebate::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::query()->inRandomOrder()->value('id') ?? Sale::factory(),
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'rebate_amount' => fake()->randomFloat(2, 100, 2000),
        ];
    }
}
