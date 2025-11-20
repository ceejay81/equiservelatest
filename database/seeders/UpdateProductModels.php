<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class UpdateProductModels extends Seeder
{
    public function run()
    {
        $updates = [
            'SKU-2766626' => 'Chain Kit',
            'SKU-4614039' => 'Beat 110',
            'SKU-2147372' => 'Brake Pads Set',
            'SKU-1704959' => 'Chain Kit',
            'SKU-3762759' => 'Beat 110',
            'SKU-3161064' => 'Mio Sporty',
            'SKU-1122550' => 'Helmet Pro',
            'SKU-3365059' => 'Click 125',
            'SKU-2850858' => 'Raider R150',
            'SKU-1141595' => 'Mio Soul',
        ];

        foreach ($updates as $sku => $model) {
            $product = Product::where('sku', $sku)->first();
            if ($product) {
                $product->model = $model;
                $product->save();
                $this->command->info("Updated {$product->name} - Model: {$model}");
            }
        }

        $this->command->info('All product models updated successfully!');
    }
}
