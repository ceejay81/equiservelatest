<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Rebate;
use App\Models\StockMovement;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        $this->command->info('Clearing existing data...');
        Payment::truncate();
        Loan::truncate();
        SaleItem::truncate();
        Sale::truncate();
        StockMovement::truncate();
        Rebate::truncate();
        Product::truncate();
        Customer::truncate();
        Notification::truncate();
        User::where('email', '!=', 'admin@equiserve.test')->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Seeding users...');
        $this->seedUsers();
        
        $this->command->info('Seeding customers...');
        $customers = $this->seedCustomers();
        
        $this->command->info('Seeding products...');
        $products = $this->seedProducts();
        
        $this->command->info('Seeding sales and loans...');
        $this->seedSalesAndLoans($customers, $products);
        
        $this->command->info('Seeding rebates...');
        $this->seedRebates($customers);
        
        $this->command->info('Seeding notifications...');
        $this->seedNotifications($customers);
        
        $this->command->info('Comprehensive seeding completed successfully!');
    }
    
    private function seedUsers()
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@equiserve.test'],
            [
                'name' => 'Admin Juan',
                'username' => 'admin',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]
        );
        
        // Manager
        User::updateOrCreate(
            ['email' => 'manager@equiserve.test'],
            [
                'name' => 'Manager Maria',
                'username' => 'manager',
                'role' => 'manager',
                'password' => bcrypt('password'),
            ]
        );
        
        // Staff members
        User::updateOrCreate(
            ['email' => 'staff@equiserve.test'],
            [
                'name' => 'Staff Tom',
                'username' => 'staff',
                'role' => 'staff',
                'password' => bcrypt('password'),
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'staff2@equiserve.test'],
            [
                'name' => 'Staff Sarah',
                'username' => 'staff2',
                'role' => 'staff',
                'password' => bcrypt('password'),
            ]
        );
    }

    
    private function seedCustomers()
    {
        $customers = [];
        
        $customerData = [
            ['Juan Dela Cruz', '09171234567', 'Purok 1, Barangay Fatima, General Santos City'],
            ['Maria Santos', '09281234567', 'Purok 2, Barangay Calumpang, General Santos City'],
            ['Pedro Reyes', '09391234567', 'Purok 3, Barangay Lagao, General Santos City'],
            ['Ana Garcia', '09171234568', 'Purok 4, Barangay Apopong, General Santos City'],
            ['Jose Mendoza', '09281234568', 'Purok 5, Barangay Bula, General Santos City'],
            ['Rosa Cruz', '09391234568', 'Purok 6, Barangay Dadiangas North, General Santos City'],
            ['Carlos Ramos', '09171234569', 'Purok 7, Barangay Dadiangas South, General Santos City'],
            ['Linda Torres', '09281234569', 'Purok 8, Barangay San Isidro, General Santos City'],
            ['Miguel Flores', '09391234569', 'Purok 9, Barangay Tambler, General Santos City'],
            ['Sofia Gonzales', '09171234570', 'Purok 10, Barangay Siguel, General Santos City'],
            ['Roberto Diaz', '09281234570', 'Purok 11, Barangay Conel, General Santos City'],
            ['Carmen Morales', '09391234570', 'Purok 12, Barangay Katangawan, General Santos City'],
            ['Antonio Castillo', '09171234571', 'Purok 13, Barangay Labangal, General Santos City'],
            ['Elena Jimenez', '09281234571', 'Purok 14, Barangay Mabuhay, General Santos City'],
            ['Francisco Ruiz', '09391234571', 'Purok 15, Barangay Olympog, General Santos City'],
            ['Isabel Hernandez', '09171234572', 'Purok 16, Barangay San Jose, General Santos City'],
            ['Manuel Rivera', '09281234572', 'Purok 17, Barangay Sinawal, General Santos City'],
            ['Teresa Gomez', '09391234572', 'Purok 18, Barangay Tinagacan, General Santos City'],
            ['Ricardo Perez', '09171234573', 'Purok 19, Barangay Upper Labay, General Santos City'],
            ['Patricia Sanchez', '09281234573', 'Purok 20, Barangay City Heights, General Santos City'],
        ];
        
        foreach ($customerData as $index => $data) {
            $customers[] = Customer::create([
                'account_number' => 'CUST-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'full_name' => $data[0],
                'contact' => $data[1],
                'address' => $data[2],
                'created_at' => now()->subDays(rand(30, 180)),
            ]);
        }
        
        return $customers;
    }

    
    private function seedProducts()
    {
        $products = [];
        
        $productData = [
            // Motorcycles
            ['Honda XRM 125', 'XRM125-2024', 'Honda', 'XRM 125', 'Motorcycle', 65000, 75000, 8, 5, 3],
            ['Yamaha Mio i 125', 'MIO125-2024', 'Yamaha', 'Mio i 125', 'Motorcycle', 55000, 65000, 12, 5, 3],
            ['Suzuki Raider R150', 'RAIDER150-2024', 'Suzuki', 'Raider R150', 'Motorcycle', 70000, 82000, 6, 5, 2],
            ['Kawasaki Barako 175', 'BARAKO175-2024', 'Kawasaki', 'Barako 175', 'Motorcycle', 75000, 88000, 4, 3, 2],
            ['Honda TMX 155', 'TMX155-2024', 'Honda', 'TMX 155', 'Motorcycle', 68000, 79000, 7, 5, 3],
            ['Yamaha Sniper 155', 'SNIPER155-2024', 'Yamaha', 'Sniper 155', 'Motorcycle', 72000, 84000, 5, 4, 2],
            ['Suzuki Smash 115', 'SMASH115-2024', 'Suzuki', 'Smash 115', 'Motorcycle', 48000, 58000, 10, 5, 3],
            ['Honda Wave 110', 'WAVE110-2024', 'Honda', 'Wave 110', 'Motorcycle', 52000, 62000, 9, 5, 3],
            
            // Spare Parts
            ['Engine Oil 10W-40', 'OIL-10W40-1L', 'Shell', '1 Liter', 'Spare Parts', 250, 350, 50, 20, 10],
            ['Spark Plug NGK', 'SPARK-NGK-001', 'NGK', 'Standard', 'Spare Parts', 80, 150, 100, 30, 15],
            ['Air Filter', 'FILTER-AIR-001', 'Generic', 'Universal', 'Spare Parts', 120, 200, 75, 25, 12],
            ['Brake Pads Front', 'BRAKE-FRONT-001', 'Generic', 'Universal', 'Spare Parts', 180, 300, 60, 20, 10],
            ['Brake Pads Rear', 'BRAKE-REAR-001', 'Generic', 'Universal', 'Spare Parts', 150, 250, 65, 20, 10],
            ['Chain Set', 'CHAIN-SET-001', 'RK', '428', 'Spare Parts', 800, 1200, 30, 10, 5],
            ['Battery 12V', 'BATTERY-12V-001', 'Motolite', '12V 7Ah', 'Spare Parts', 1200, 1800, 25, 10, 5],
            ['Tire Front 70/90-17', 'TIRE-F-70-90-17', 'IRC', '70/90-17', 'Spare Parts', 1000, 1500, 20, 8, 4],
            ['Tire Rear 80/90-17', 'TIRE-R-80-90-17', 'IRC', '80/90-17', 'Spare Parts', 1200, 1800, 18, 8, 4],
            ['Headlight Bulb', 'BULB-HEAD-001', 'Osram', 'H4', 'Spare Parts', 150, 250, 80, 25, 12],
            ['Side Mirror Left', 'MIRROR-L-001', 'Generic', 'Universal', 'Spare Parts', 200, 350, 40, 15, 8],
            ['Side Mirror Right', 'MIRROR-R-001', 'Generic', 'Universal', 'Spare Parts', 200, 350, 40, 15, 8],
            
            // Accessories
            ['Helmet Full Face', 'HELMET-FF-001', 'Shoei', 'Full Face', 'Accessories', 2500, 3500, 15, 5, 3],
            ['Helmet Half Face', 'HELMET-HF-001', 'Generic', 'Half Face', 'Accessories', 800, 1200, 25, 8, 4],
            ['Riding Jacket', 'JACKET-001', 'Alpinestars', 'Large', 'Accessories', 3000, 4500, 10, 4, 2],
            ['Gloves', 'GLOVES-001', 'Generic', 'Medium', 'Accessories', 400, 600, 30, 10, 5],
            ['Rain Coat', 'RAINCOAT-001', 'Generic', 'XL', 'Accessories', 300, 500, 35, 12, 6],
        ];
        
        foreach ($productData as $data) {
            $products[] = Product::create([
                'name' => $data[0],
                'sku' => $data[1],
                'brand' => $data[2],
                'model' => $data[3],
                'category' => $data[4],
                'unit_cost' => $data[5],
                'selling_price' => $data[6],
                'stock' => $data[7],
                'reorder_level' => $data[8],
                'status' => 'active',
                'description' => 'High quality ' . $data[0],
            ]);
        }
        
        return $products;
    }

    
    private function seedSalesAndLoans($customers, $products)
    {
        $users = User::all();
        $saleNumber = 1;
        
        // Generate sales for the past 90 days
        for ($day = 90; $day >= 0; $day--) {
            $date = now()->subDays($day);
            
            // Skip some days randomly
            if ($day > 0 && rand(1, 100) > 70) {
                continue;
            }
            
            // Generate 1-5 sales per day
            $salesCount = rand(1, 5);
            
            for ($i = 0; $i < $salesCount; $i++) {
                $customer = $customers[array_rand($customers)];
                $user = $users->random();
                $saleType = rand(1, 100) <= 60 ? 'cash' : 'loan'; // 60% cash, 40% loan
                $paymentMode = rand(1, 100) <= 70 ? 'cash' : 'online'; // 70% cash, 30% online
                
                // Select 1-3 products
                $itemCount = rand(1, 3);
                $selectedProducts = [];
                for ($j = 0; $j < $itemCount; $j++) {
                    $selectedProducts[] = $products[array_rand($products)];
                }
                
                // Calculate total
                $subtotal = 0;
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 2);
                    $subtotal += $product->selling_price * $quantity;
                }
                
                // Apply random discount (0-10%)
                $discountPercent = rand(0, 10);
                $discountAmount = $subtotal * ($discountPercent / 100);
                $totalAmount = $subtotal - $discountAmount;
                
                // Create sale
                $sale = Sale::create([
                    'sale_number' => sprintf('S-%s-%04d', $date->format('Ymd'), $saleNumber++),
                    'customer_id' => $customer->id,
                    'user_id' => $user->id,
                    'sale_type' => $saleType,
                    'total_amount' => $totalAmount,
                    'payment_mode' => $paymentMode,
                    'amount_tendered' => $saleType === 'cash' && $paymentMode === 'cash' ? $totalAmount + rand(0, 500) : null,
                    'reference_number' => $paymentMode === 'online' ? 'REF-' . strtoupper(uniqid()) : null,
                    'discount_total' => $discountAmount > 0 ? $discountAmount : null,
                    'discount_reason' => $discountAmount > 0 ? 'Customer discount' : null,
                    'created_at' => $date->addHours(rand(8, 18))->addMinutes(rand(0, 59)),
                ]);
                
                // Create sale items
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 2);
                    $itemSubtotal = $product->selling_price * $quantity;
                    
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $itemSubtotal,
                        'discount' => 0,
                    ]);
                    
                    // Update stock
                    $product->decrement('stock', $quantity);
                    
                    // Create stock movement
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'sale',
                        'quantity_change' => -$quantity,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'remarks' => 'Sale ' . $sale->sale_number,
                        'performed_by' => $user->id,
                        'created_at' => $sale->created_at,
                    ]);
                }
                
                // Create loan if sale type is loan
                if ($saleType === 'loan') {
                    $downPaymentPercent = rand(20, 40); // 20-40% down payment
                    $downPayment = $totalAmount * ($downPaymentPercent / 100);
                    $principal = $totalAmount - $downPayment;
                    $termMonths = [6, 12, 18, 24, 36][array_rand([6, 12, 18, 24, 36])];
                    $interestRate = 2.0; // 2% monthly
                    
                    $monthlyRate = $interestRate / 100;
                    $monthlyAmount = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
                    
                    $loan = Loan::create([
                        'sale_id' => $sale->id,
                        'customer_id' => $customer->id,
                        'loan_amount' => $totalAmount,
                        'down_payment' => $downPayment,
                        'term_months' => $termMonths,
                        'interest_rate' => $interestRate,
                        'monthly_amount' => $monthlyAmount,
                        'balance' => $principal,
                        'start_date' => $sale->created_at,
                        'next_due_date' => $sale->created_at->copy()->addMonth(),
                        'status' => 'active',
                        'created_at' => $sale->created_at,
                    ]);
                    
                    // Create some payments for older loans
                    if ($day > 30) {
                        $paymentCount = min(floor($day / 30), $termMonths);
                        
                        for ($p = 1; $p <= $paymentCount; $p++) {
                            if (rand(1, 100) <= 80) { // 80% payment rate
                                $paymentDate = $sale->created_at->copy()->addMonths($p);
                                $amountPaid = $monthlyAmount;
                                
                                $modeOfPayment = rand(1, 100) <= 70 ? 'Cash' : 'GCash';
                                Payment::create([
                                    'loan_id' => $loan->id,
                                    'amount_paid' => $amountPaid,
                                    'payment_date' => $paymentDate,
                                    'mode_of_payment' => $modeOfPayment,
                                    'reference_number' => $modeOfPayment !== 'Cash' ? 'PAY-' . strtoupper(uniqid()) : null,
                                    'payment_bank' => $modeOfPayment === 'GCash' ? 'GCash' : null,
                                    'payment_timestamp' => $paymentDate,
                                    'created_at' => $paymentDate,
                                ]);
                                
                                // Update loan
                                $loan->balance -= $amountPaid;
                                $loan->next_due_date = $paymentDate->copy()->addMonth();
                                
                                if ($loan->balance <= 0) {
                                    $loan->balance = 0;
                                    $loan->status = 'completed';
                                }
                                $loan->save();
                            }
                        }
                        
                        // Check if overdue
                        if ($loan->status === 'active' && $loan->next_due_date < now()) {
                            $loan->status = 'overdue';
                            $loan->save();
                        }
                    }
                }
            }
        }
    }

    
    private function seedRebates($customers)
    {
        // Get some sales with products
        $sales = Sale::with('items.product')->take(20)->get();
        
        foreach ($sales as $sale) {
            // 30% chance of having rebates
            if (rand(1, 100) <= 30 && $sale->items->count() > 0) {
                foreach ($sale->items as $item) {
                    // 50% chance each product has a rebate
                    if (rand(1, 100) <= 50) {
                        $rebateAmount = $item->subtotal * (rand(5, 15) / 100); // 5-15% rebate
                        $status = rand(1, 100) <= 70 ? 'available' : 'used'; // 70% available
                        
                        Rebate::create([
                            'sale_id' => $sale->id,
                            'product_id' => $item->product_id,
                            'rebate_amount' => $rebateAmount,
                            'status' => $status,
                            'used_for' => $status === 'used' ? (rand(1, 100) <= 50 ? 'purchase' : 'loan_payment') : null,
                            'applied_to_loan_id' => $status === 'used' && $sale->loan ? $sale->loan->id : null,
                            'used_at' => $status === 'used' ? now()->subDays(rand(1, 30)) : null,
                        ]);
                    }
                }
            }
        }
    }
    
    private function seedNotifications($customers)
    {
        $users = User::all();
        
        // Create overdue payment notifications
        $overdueLoans = Loan::with(['customer', 'sale'])->where('status', 'overdue')->get();
        
        foreach ($overdueLoans as $loan) {
            $isActioned = rand(1, 100) <= 20;
            Notification::create([
                'type' => 'overdue_payment',
                'title' => 'Overdue Payment',
                'message' => "Customer {$loan->customer->full_name} has an overdue payment of ₱" . number_format($loan->monthly_amount, 2),
                'priority' => 'critical',
                'related_type' => 'App\Models\Loan',
                'related_id' => $loan->id,
                'data' => [
                    'customer_name' => $loan->customer->full_name,
                    'customer_phone' => $loan->customer->contact,
                    'sale_number' => $loan->sale->sale_number ?? null,
                    'amount' => $loan->monthly_amount,
                    'due_date' => $loan->next_due_date->format('Y-m-d'),
                ],
                'is_read' => rand(1, 100) <= 30,
                'is_actioned' => $isActioned,
                'actioned_by' => $isActioned ? $users->random()->id : null,
                'actioned_at' => $isActioned ? now()->subDays(rand(1, 5)) : null,
                'created_at' => $loan->next_due_date->copy()->addDays(1),
            ]);
        }
        
        // Create low stock notifications
        $lowStockProducts = Product::whereRaw('stock <= reorder_level')->get();
        
        foreach ($lowStockProducts as $product) {
            $criticalThreshold = max(1, floor($product->reorder_level / 2));
            $isActioned = rand(1, 100) <= 30;
            Notification::create([
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'message' => "Product {$product->name} is running low on stock. Current: {$product->stock}, Reorder level: {$product->reorder_level}",
                'priority' => $product->stock <= $criticalThreshold ? 'high' : 'medium',
                'related_type' => 'App\Models\Product',
                'related_id' => $product->id,
                'data' => [
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $product->stock,
                    'reorder_level' => $product->reorder_level,
                ],
                'is_read' => rand(1, 100) <= 50,
                'is_actioned' => $isActioned,
                'actioned_by' => $isActioned ? $users->random()->id : null,
                'actioned_at' => $isActioned ? now()->subDays(rand(1, 3)) : null,
                'created_at' => now()->subDays(rand(1, 7)),
            ]);
        }
        
        // Create upcoming payment notifications
        $upcomingLoans = Loan::with(['customer', 'sale'])
            ->where('status', 'active')
            ->whereBetween('next_due_date', [now(), now()->addDays(7)])
            ->get();
        
        foreach ($upcomingLoans as $loan) {
            Notification::create([
                'type' => 'upcoming_payment',
                'title' => 'Upcoming Payment',
                'message' => "Customer {$loan->customer->full_name} has a payment due on {$loan->next_due_date->format('M d, Y')} - ₱" . number_format($loan->monthly_amount, 2),
                'priority' => 'medium',
                'related_type' => 'App\Models\Loan',
                'related_id' => $loan->id,
                'data' => [
                    'customer_name' => $loan->customer->full_name,
                    'customer_phone' => $loan->customer->contact,
                    'sale_number' => $loan->sale->sale_number ?? null,
                    'amount' => $loan->monthly_amount,
                    'due_date' => $loan->next_due_date->format('Y-m-d'),
                ],
                'is_read' => rand(1, 100) <= 60,
                'is_actioned' => false,
                'created_at' => now()->subDays(rand(0, 3)),
            ]);
        }
    }
}
