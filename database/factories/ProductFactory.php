<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $brands = ['Honda','Yamaha','Suzuki','Kawasaki'];
        $categories = ['Motorcycle','Helmet','Parts','Accessory'];
        $name = fake()->randomElement([
            'Beat 110', 'Click 125', 'Mio Sporty', 'Raider R150', 'Rouser 200',
            'Helmet Pro', 'Brake Pads Set', 'Chain Kit', 'Rider Jacket'
        ]);
        $brand = fake()->randomElement($brands);
        $category = $name === 'Helmet Pro' ? 'Helmet' : (in_array($name, ['Brake Pads Set','Chain Kit','Rider Jacket']) ? 'Accessory' : 'Motorcycle');
        $unitCost = fake()->randomFloat(2, 500, 80000);
        $price = $unitCost * fake()->randomFloat(2, 1.1, 1.35);

        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-#######')),
            'name' => $name,
            'brand' => $brand,
            'category' => $category,
            'unit_cost' => round($unitCost, 2),
            'selling_price' => round($price, 2),
            'stock' => fake()->numberBetween(1, 50),
        ];
    }
}
