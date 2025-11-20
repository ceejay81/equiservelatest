<?php

namespace Database\Factories;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = $product->selling_price;
        $subtotal = $quantity * $unitPrice;

        return [
            'sale_id' => Sale::query()->inRandomOrder()->value('id') ?? Sale::factory(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
        ];
    }
}
