<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Rebate;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SalesLoansPaymentsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there are customers and products first.
        $customers = Customer::query()->inRandomOrder()->get();
        $products = Product::all();
        
        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create sales for a subset of customers
        $customers->take(12)->each(function (Customer $customer) use ($products) {
            // 1-2 sales per customer
            $salesCount = fake()->numberBetween(1, 2);
            
            for ($i = 0; $i < $salesCount; $i++) {
                // Create sale
                $sale = Sale::factory()->create([
                    'customer_id' => $customer->id,
                ]);

                // Create 1-4 sale items for each sale
                $itemsCount = fake()->numberBetween(1, 4);
                $calculatedTotal = 0;
                
                for ($j = 0; $j < $itemsCount; $j++) {
                    $product = $products->random();
                    $quantity = fake()->numberBetween(1, 3);
                    $unitPrice = $product->selling_price;
                    $subtotal = $quantity * $unitPrice;
                    
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);
                    
                    $calculatedTotal += $subtotal;
                }
                
                // Update sale total to match items (minus discount if any)
                $sale->update([
                    'total_amount' => $calculatedTotal - ($sale->discount_total ?? 0),
                ]);

                // Optional loan for loan-type sales
                if ($sale->sale_type === 'loan') {
                    $loan = Loan::factory()->create([
                        'sale_id' => $sale->id,
                        'customer_id' => $customer->id,
                        'loan_amount' => $sale->total_amount,
                    ]);

                    // 2-5 payments per loan (only if not completed)
                    if ($loan->status !== 'completed') {
                        Payment::factory()->count(fake()->numberBetween(2, 5))->create([
                            'loan_id' => $loan->id,
                        ]);
                    }
                }

                // 0-2 rebates per sale (only for some sales)
                if (fake()->boolean(30)) {
                    Rebate::factory()->count(fake()->numberBetween(1, 2))->create([
                        'sale_id' => $sale->id,
                    ]);
                }
            }
        });
    }
}
