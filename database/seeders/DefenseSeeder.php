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
use App\Models\LoanPenalty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DefenseSeeder extends Seeder
{
    private $users;
    private $customers;
    private $products;
    private $saleCounter = 1;
    
    /**
     * Run the database seeds for defense presentation.
     * This seeder creates realistic, comprehensive data showcasing all system features.
     */
    public function run(): void
    {
        $this->command->info('🎓 Starting Defense Presentation Seeder...');
        $this->command->newLine();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        $this->command->info('📦 Clearing existing data...');
        $this->clearExistingData();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Seed in logical order
        $this->command->info('👥 Creating users...');
        $this->seedUsers();
        
        $this->command->info('🏪 Creating customers...');
        $this->seedCustomers();
        
        $this->command->info('📦 Creating products...');
        $this->seedProducts();
        
        $this->command->info('💰 Creating sales transactions...');
        $this->seedSalesTransactions();
        
        $this->command->info('🎁 Creating rebates...');
        $this->seedRebates();
        
        $this->command->info('🔔 Creating notifications...');
        $this->seedNotifications();
        
        $this->command->newLine();
        $this->command->info('✅ Defense seeding completed successfully!');
        $this->displaySummary();
    }

    
    private function clearExistingData()
    {
        LoanPenalty::truncate();
        Payment::truncate();
        Loan::truncate();
        SaleItem::truncate();
        Sale::truncate();
        StockMovement::truncate();
        Rebate::truncate();
        Notification::truncate();
        Product::truncate();
        Customer::truncate();
        User::where('email', '!=', 'admin@equiserve.test')->delete();
    }
    
    private function seedUsers()
    {
        $this->users = collect([
            User::updateOrCreate(
                ['email' => 'admin@equiserve.test'],
                [
                    'name' => 'Admin Rodriguez',
                    'username' => 'admin',
                    'role' => 'admin',
                    'password' => bcrypt('password'),
                ]
            ),
            User::updateOrCreate(
                ['email' => 'manager@equiserve.test'],
                [
                    'name' => 'Manager Santos',
                    'username' => 'manager',
                    'role' => 'manager',
                    'password' => bcrypt('password'),
                ]
            ),
            User::updateOrCreate(
                ['email' => 'staff@equiserve.test'],
                [
                    'name' => 'Staff Reyes',
                    'username' => 'staff',
                    'role' => 'staff',
                    'password' => bcrypt('password'),
                ]
            ),
            User::updateOrCreate(
                ['email' => 'staff2@equiserve.test'],
                [
                    'name' => 'Staff Garcia',
                    'username' => 'staff2',
                    'role' => 'staff',
                    'password' => bcrypt('password'),
                ]
            ),
        ]);
        
        $this->command->info("   ✓ Created {$this->users->count()} users");
    }

    
    private function seedCustomers()
    {
        $customerData = [
            // VIP Customers (High value, good payment history)
            ['Juan Dela Cruz', '09171234567', 'Purok 1, Barangay Fatima, General Santos City', 'vip'],
            ['Maria Santos', '09281234567', 'Purok 2, Barangay Calumpang, General Santos City', 'vip'],
            ['Pedro Reyes', '09391234567', 'Purok 3, Barangay Lagao, General Santos City', 'vip'],
            
            // Regular Customers (Good payment history)
            ['Ana Garcia', '09171234568', 'Purok 4, Barangay Apopong, General Santos City', 'regular'],
            ['Jose Mendoza', '09281234568', 'Purok 5, Barangay Bula, General Santos City', 'regular'],
            ['Rosa Cruz', '09391234568', 'Purok 6, Barangay Dadiangas North, General Santos City', 'regular'],
            ['Carlos Ramos', '09171234569', 'Purok 7, Barangay Dadiangas South, General Santos City', 'regular'],
            ['Linda Torres', '09281234569', 'Purok 8, Barangay San Isidro, General Santos City', 'regular'],
            ['Miguel Flores', '09391234569', 'Purok 9, Barangay Tambler, General Santos City', 'regular'],
            ['Sofia Gonzales', '09171234570', 'Purok 10, Barangay Siguel, General Santos City', 'regular'],
            ['Roberto Diaz', '09281234570', 'Purok 11, Barangay Conel, General Santos City', 'regular'],
            ['Carmen Morales', '09391234570', 'Purok 12, Barangay Katangawan, General Santos City', 'regular'],
            ['Antonio Castillo', '09171234571', 'Purok 13, Barangay Labangal, General Santos City', 'regular'],
            ['Elena Jimenez', '09281234571', 'Purok 14, Barangay Mabuhay, General Santos City', 'regular'],
            ['Francisco Ruiz', '09391234571', 'Purok 15, Barangay Olympog, General Santos City', 'regular'],
            
            // New Customers (Recent signups)
            ['Isabel Hernandez', '09171234572', 'Purok 16, Barangay San Jose, General Santos City', 'new'],
            ['Manuel Rivera', '09281234572', 'Purok 17, Barangay Sinawal, General Santos City', 'new'],
            ['Teresa Gomez', '09391234572', 'Purok 18, Barangay Tinagacan, General Santos City', 'new'],
            
            // Customers with payment issues
            ['Ricardo Perez', '09171234573', 'Purok 19, Barangay Upper Labay, General Santos City', 'overdue'],
            ['Patricia Sanchez', '09281234573', 'Purok 20, Barangay City Heights, General Santos City', 'overdue'],
            ['Fernando Lopez', '09391234573', 'Purok 21, Barangay Ligaya, General Santos City', 'overdue'],
            
            // Additional customers for variety
            ['Gloria Martinez', '09171234574', 'Purok 22, Barangay Buayan, General Santos City', 'regular'],
            ['Rodrigo Fernandez', '09281234574', 'Purok 23, Barangay Baluan, General Santos City', 'regular'],
            ['Angelina Ramirez', '09391234574', 'Purok 24, Barangay Apopong, General Santos City', 'regular'],
            ['Benjamin Torres', '09171234575', 'Purok 25, Barangay Fatima, General Santos City', 'regular'],
        ];
        
        $this->customers = collect();
        
        foreach ($customerData as $index => $data) {
            $daysAgo = match($data[3]) {
                'vip' => rand(180, 365),
                'regular' => rand(60, 180),
                'new' => rand(1, 30),
                'overdue' => rand(90, 180),
            };
            
            $customer = Customer::create([
                'account_number' => 'CUST-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'full_name' => $data[0],
                'contact' => $data[1],
                'address' => $data[2],
                'created_at' => now()->subDays($daysAgo),
                'updated_at' => now()->subDays($daysAgo),
            ]);
            
            $customer->customer_type = $data[3]; // Store for later use
            $this->customers->push($customer);
        }
        
        $this->command->info("   ✓ Created {$this->customers->count()} customers");
    }

    
    private function seedProducts()
    {
        $productData = [
            // Premium Motorcycles (High value items)
            [
                'name' => 'Honda XRM 125 DS',
                'sku' => 'XRM125DS-2024',
                'brand' => 'Honda',
                'model' => 'XRM 125 DS',
                'category' => 'Motorcycle',
                'unit_cost' => 68000,
                'selling_price' => 79000,
                'stock' => 5,
                'reorder_level' => 3,
                'description' => 'Dual Sport motorcycle perfect for city and off-road riding',
                'location' => 'Warehouse A',
                'color' => 'Red',
            ],
            [
                'name' => 'Yamaha Mio i 125',
                'sku' => 'MIO125-2024',
                'brand' => 'Yamaha',
                'model' => 'Mio i 125',
                'category' => 'Motorcycle',
                'unit_cost' => 58000,
                'selling_price' => 68000,
                'stock' => 8,
                'reorder_level' => 4,
                'description' => 'Stylish and fuel-efficient automatic scooter',
                'location' => 'Warehouse A',
                'color' => 'Blue',
            ],
            [
                'name' => 'Suzuki Raider R150 Fi',
                'sku' => 'RAIDER150-2024',
                'brand' => 'Suzuki',
                'model' => 'Raider R150 Fi',
                'category' => 'Motorcycle',
                'unit_cost' => 75000,
                'selling_price' => 88000,
                'stock' => 4,
                'reorder_level' => 2,
                'description' => 'High-performance underbone with fuel injection',
                'location' => 'Warehouse A',
                'color' => 'Black',
            ],
            [
                'name' => 'Kawasaki Barako 175',
                'sku' => 'BARAKO175-2024',
                'brand' => 'Kawasaki',
                'model' => 'Barako 175',
                'category' => 'Motorcycle',
                'unit_cost' => 78000,
                'selling_price' => 92000,
                'stock' => 3,
                'reorder_level' => 2,
                'description' => 'Powerful cruiser-style motorcycle',
                'location' => 'Warehouse A',
                'color' => 'Green',
            ],
            [
                'name' => 'Honda TMX 155 Supremo',
                'sku' => 'TMX155-2024',
                'brand' => 'Honda',
                'model' => 'TMX 155 Supremo',
                'category' => 'Motorcycle',
                'unit_cost' => 72000,
                'selling_price' => 84000,
                'stock' => 6,
                'reorder_level' => 3,
                'description' => 'Reliable underbone for daily commute',
                'location' => 'Warehouse A',
                'color' => 'Red',
            ],
            [
                'name' => 'Yamaha Sniper 155',
                'sku' => 'SNIPER155-2024',
                'brand' => 'Yamaha',
                'model' => 'Sniper 155',
                'category' => 'Motorcycle',
                'unit_cost' => 76000,
                'selling_price' => 89000,
                'stock' => 4,
                'reorder_level' => 2,
                'description' => 'Sporty underbone with excellent performance',
                'location' => 'Warehouse A',
                'color' => 'Blue',
            ],
            [
                'name' => 'Suzuki Smash 115',
                'sku' => 'SMASH115-2024',
                'brand' => 'Suzuki',
                'model' => 'Smash 115',
                'category' => 'Motorcycle',
                'unit_cost' => 52000,
                'selling_price' => 62000,
                'stock' => 10,
                'reorder_level' => 5,
                'description' => 'Economical and reliable daily rider',
                'location' => 'Warehouse A',
                'color' => 'Black',
            ],
            [
                'name' => 'Honda Wave 110',
                'sku' => 'WAVE110-2024',
                'brand' => 'Honda',
                'model' => 'Wave 110',
                'category' => 'Motorcycle',
                'unit_cost' => 55000,
                'selling_price' => 65000,
                'stock' => 12,
                'reorder_level' => 6,
                'description' => 'Best-selling underbone motorcycle',
                'location' => 'Warehouse A',
                'color' => 'Red',
            ],
            
            // Engine Oil & Lubricants
            [
                'name' => 'Shell Advance AX7 10W-40',
                'sku' => 'OIL-SHELL-10W40-1L',
                'brand' => 'Shell',
                'model' => '10W-40 1L',
                'category' => 'Spare Parts',
                'unit_cost' => 280,
                'selling_price' => 380,
                'stock' => 45,
                'reorder_level' => 20,
                'description' => 'Premium synthetic blend engine oil',
                'location' => 'Shelf B1',
                'color' => null,
            ],
            [
                'name' => 'Motul 3000 4T 20W-50',
                'sku' => 'OIL-MOTUL-20W50-1L',
                'brand' => 'Motul',
                'model' => '20W-50 1L',
                'category' => 'Spare Parts',
                'unit_cost' => 320,
                'selling_price' => 450,
                'stock' => 38,
                'reorder_level' => 20,
                'description' => 'High-performance mineral engine oil',
                'location' => 'Shelf B1',
                'color' => null,
            ],
            [
                'name' => 'Castrol Power1 4T 10W-40',
                'sku' => 'OIL-CASTROL-10W40-1L',
                'brand' => 'Castrol',
                'model' => '10W-40 1L',
                'category' => 'Spare Parts',
                'unit_cost' => 300,
                'selling_price' => 420,
                'stock' => 42,
                'reorder_level' => 20,
                'description' => 'Advanced synthetic technology oil',
                'location' => 'Shelf B1',
                'color' => null,
            ],
            
            // Spark Plugs
            [
                'name' => 'NGK Iridium Spark Plug',
                'sku' => 'SPARK-NGK-IRIDIUM',
                'brand' => 'NGK',
                'model' => 'Iridium IX',
                'category' => 'Spare Parts',
                'unit_cost' => 180,
                'selling_price' => 280,
                'stock' => 85,
                'reorder_level' => 30,
                'description' => 'Premium iridium spark plug for better performance',
                'location' => 'Shelf B2',
                'color' => null,
            ],
            [
                'name' => 'Denso Standard Spark Plug',
                'sku' => 'SPARK-DENSO-STD',
                'brand' => 'Denso',
                'model' => 'Standard',
                'category' => 'Spare Parts',
                'unit_cost' => 95,
                'selling_price' => 160,
                'stock' => 120,
                'reorder_level' => 40,
                'description' => 'Reliable standard spark plug',
                'location' => 'Shelf B2',
                'color' => null,
            ],
            
            // Filters
            [
                'name' => 'Air Filter Universal',
                'sku' => 'FILTER-AIR-UNIV',
                'brand' => 'Generic',
                'model' => 'Universal',
                'category' => 'Spare Parts',
                'unit_cost' => 135,
                'selling_price' => 220,
                'stock' => 68,
                'reorder_level' => 25,
                'description' => 'High-quality air filter for most motorcycles',
                'location' => 'Shelf B3',
                'color' => null,
            ],
            [
                'name' => 'Oil Filter Honda',
                'sku' => 'FILTER-OIL-HONDA',
                'brand' => 'Honda',
                'model' => 'Genuine',
                'category' => 'Spare Parts',
                'unit_cost' => 145,
                'selling_price' => 240,
                'stock' => 55,
                'reorder_level' => 20,
                'description' => 'Genuine Honda oil filter',
                'location' => 'Shelf B3',
                'color' => null,
            ],
            
            // Brake Parts
            [
                'name' => 'Brake Pads Front Set',
                'sku' => 'BRAKE-FRONT-SET',
                'brand' => 'TDR',
                'model' => 'Universal',
                'category' => 'Spare Parts',
                'unit_cost' => 195,
                'selling_price' => 320,
                'stock' => 52,
                'reorder_level' => 20,
                'description' => 'High-quality front brake pads',
                'location' => 'Shelf C1',
                'color' => null,
            ],
            [
                'name' => 'Brake Pads Rear Set',
                'sku' => 'BRAKE-REAR-SET',
                'brand' => 'TDR',
                'model' => 'Universal',
                'category' => 'Spare Parts',
                'unit_cost' => 165,
                'selling_price' => 280,
                'stock' => 58,
                'reorder_level' => 20,
                'description' => 'High-quality rear brake pads',
                'location' => 'Shelf C1',
                'color' => null,
            ],
            [
                'name' => 'Brake Fluid DOT 3',
                'sku' => 'BRAKE-FLUID-DOT3',
                'brand' => 'Motul',
                'model' => 'DOT 3 500ml',
                'category' => 'Spare Parts',
                'unit_cost' => 185,
                'selling_price' => 290,
                'stock' => 35,
                'reorder_level' => 15,
                'description' => 'Premium brake fluid',
                'location' => 'Shelf C1',
                'color' => null,
            ],
            
            // Drive Chain & Sprockets
            [
                'name' => 'Chain Set RK 428',
                'sku' => 'CHAIN-RK-428',
                'brand' => 'RK',
                'model' => '428 Standard',
                'category' => 'Spare Parts',
                'unit_cost' => 850,
                'selling_price' => 1300,
                'stock' => 28,
                'reorder_level' => 10,
                'description' => 'Durable chain set with sprockets',
                'location' => 'Shelf C2',
                'color' => null,
            ],
            [
                'name' => 'Chain Set DID 428',
                'sku' => 'CHAIN-DID-428',
                'brand' => 'DID',
                'model' => '428 Heavy Duty',
                'category' => 'Spare Parts',
                'unit_cost' => 950,
                'selling_price' => 1450,
                'stock' => 22,
                'reorder_level' => 10,
                'description' => 'Premium heavy-duty chain set',
                'location' => 'Shelf C2',
                'color' => null,
            ],
            
            // Batteries
            [
                'name' => 'Motolite Battery 12V 7Ah',
                'sku' => 'BATTERY-MOTOLITE-12V7AH',
                'brand' => 'Motolite',
                'model' => '12V 7Ah',
                'category' => 'Spare Parts',
                'unit_cost' => 1350,
                'selling_price' => 1950,
                'stock' => 18,
                'reorder_level' => 8,
                'description' => 'Reliable maintenance-free battery',
                'location' => 'Shelf D1',
                'color' => null,
            ],
            [
                'name' => 'GS Battery 12V 9Ah',
                'sku' => 'BATTERY-GS-12V9AH',
                'brand' => 'GS',
                'model' => '12V 9Ah',
                'category' => 'Spare Parts',
                'unit_cost' => 1550,
                'selling_price' => 2200,
                'stock' => 15,
                'reorder_level' => 8,
                'description' => 'High-capacity maintenance-free battery',
                'location' => 'Shelf D1',
                'color' => null,
            ],
            
            // Tires
            [
                'name' => 'IRC Tire Front 70/90-17',
                'sku' => 'TIRE-IRC-F-70-90-17',
                'brand' => 'IRC',
                'model' => '70/90-17',
                'category' => 'Spare Parts',
                'unit_cost' => 1100,
                'selling_price' => 1650,
                'stock' => 16,
                'reorder_level' => 8,
                'description' => 'Premium front tire with excellent grip',
                'location' => 'Tire Rack A',
                'color' => null,
            ],
            [
                'name' => 'IRC Tire Rear 80/90-17',
                'sku' => 'TIRE-IRC-R-80-90-17',
                'brand' => 'IRC',
                'model' => '80/90-17',
                'category' => 'Spare Parts',
                'unit_cost' => 1300,
                'selling_price' => 1950,
                'stock' => 14,
                'reorder_level' => 8,
                'description' => 'Premium rear tire with long life',
                'location' => 'Tire Rack A',
                'color' => null,
            ],
            [
                'name' => 'FDR Tire Front 70/90-17',
                'sku' => 'TIRE-FDR-F-70-90-17',
                'brand' => 'FDR',
                'model' => '70/90-17',
                'category' => 'Spare Parts',
                'unit_cost' => 950,
                'selling_price' => 1450,
                'stock' => 20,
                'reorder_level' => 10,
                'description' => 'Quality front tire at affordable price',
                'location' => 'Tire Rack A',
                'color' => null,
            ],
            [
                'name' => 'FDR Tire Rear 80/90-17',
                'sku' => 'TIRE-FDR-R-80-90-17',
                'brand' => 'FDR',
                'model' => '80/90-17',
                'category' => 'Spare Parts',
                'unit_cost' => 1150,
                'selling_price' => 1750,
                'stock' => 18,
                'reorder_level' => 10,
                'description' => 'Quality rear tire at affordable price',
                'location' => 'Tire Rack A',
                'color' => null,
            ],
            
            // Lights & Electrical
            [
                'name' => 'Headlight Bulb H4 Osram',
                'sku' => 'BULB-HEAD-H4-OSRAM',
                'brand' => 'Osram',
                'model' => 'H4 12V 60/55W',
                'category' => 'Spare Parts',
                'unit_cost' => 165,
                'selling_price' => 280,
                'stock' => 72,
                'reorder_level' => 25,
                'description' => 'Bright and long-lasting headlight bulb',
                'location' => 'Shelf E1',
                'color' => null,
            ],
            [
                'name' => 'LED Headlight Bulb',
                'sku' => 'BULB-HEAD-LED',
                'brand' => 'Generic',
                'model' => 'H4 LED 6000K',
                'category' => 'Spare Parts',
                'unit_cost' => 385,
                'selling_price' => 650,
                'stock' => 35,
                'reorder_level' => 15,
                'description' => 'Super bright LED headlight upgrade',
                'location' => 'Shelf E1',
                'color' => null,
            ],
            [
                'name' => 'Turn Signal Light Set',
                'sku' => 'LIGHT-SIGNAL-SET',
                'brand' => 'Generic',
                'model' => 'Universal',
                'category' => 'Spare Parts',
                'unit_cost' => 245,
                'selling_price' => 420,
                'stock' => 48,
                'reorder_level' => 20,
                'description' => 'Complete turn signal light set',
                'location' => 'Shelf E1',
                'color' => null,
            ],
            
            // Mirrors & Body Parts
            [
                'name' => 'Side Mirror Left Universal',
                'sku' => 'MIRROR-L-UNIV',
                'brand' => 'Generic',
                'model' => 'Universal',
                'category' => 'Spare Parts',
                'unit_cost' => 215,
                'selling_price' => 380,
                'stock' => 38,
                'reorder_level' => 15,
                'description' => 'Universal left side mirror',
                'location' => 'Shelf F1',
                'color' => 'Black',
            ],
            [
                'name' => 'Side Mirror Right Universal',
                'sku' => 'MIRROR-R-UNIV',
                'brand' => 'Generic',
                'model' => 'Universal',
                'category' => 'Spare Parts',
                'unit_cost' => 215,
                'selling_price' => 380,
                'stock' => 38,
                'reorder_level' => 15,
                'description' => 'Universal right side mirror',
                'location' => 'Shelf F1',
                'color' => 'Black',
            ],
            
            // Helmets & Safety Gear
            [
                'name' => 'Shoei Full Face Helmet',
                'sku' => 'HELMET-SHOEI-FF',
                'brand' => 'Shoei',
                'model' => 'RF-1400',
                'category' => 'Accessories',
                'unit_cost' => 2800,
                'selling_price' => 3950,
                'stock' => 12,
                'reorder_level' => 5,
                'description' => 'Premium full face helmet with DOT certification',
                'location' => 'Display A',
                'color' => 'Matte Black',
            ],
            [
                'name' => 'AGV K3 SV Helmet',
                'sku' => 'HELMET-AGV-K3SV',
                'brand' => 'AGV',
                'model' => 'K3 SV',
                'category' => 'Accessories',
                'unit_cost' => 2200,
                'selling_price' => 3200,
                'stock' => 15,
                'reorder_level' => 6,
                'description' => 'Sport touring full face helmet',
                'location' => 'Display A',
                'color' => 'Black/Red',
            ],
            [
                'name' => 'Generic Half Face Helmet',
                'sku' => 'HELMET-GENERIC-HF',
                'brand' => 'Generic',
                'model' => 'Half Face',
                'category' => 'Accessories',
                'unit_cost' => 850,
                'selling_price' => 1350,
                'stock' => 28,
                'reorder_level' => 10,
                'description' => 'Affordable half face helmet',
                'location' => 'Display A',
                'color' => 'Black',
            ],
            [
                'name' => 'Alpinestars Riding Jacket',
                'sku' => 'JACKET-ALPINESTARS',
                'brand' => 'Alpinestars',
                'model' => 'T-GP Plus R v3',
                'category' => 'Accessories',
                'unit_cost' => 3200,
                'selling_price' => 4800,
                'stock' => 8,
                'reorder_level' => 4,
                'description' => 'Premium textile riding jacket with armor',
                'location' => 'Display B',
                'color' => 'Black/White',
            ],
            [
                'name' => 'Riding Gloves Pro',
                'sku' => 'GLOVES-PRO',
                'brand' => 'Taichi',
                'model' => 'RST437',
                'category' => 'Accessories',
                'unit_cost' => 480,
                'selling_price' => 750,
                'stock' => 32,
                'reorder_level' => 12,
                'description' => 'Professional riding gloves with knuckle protection',
                'location' => 'Display B',
                'color' => 'Black',
            ],
            [
                'name' => 'Rain Coat Heavy Duty',
                'sku' => 'RAINCOAT-HD',
                'brand' => 'Generic',
                'model' => 'XL',
                'category' => 'Accessories',
                'unit_cost' => 320,
                'selling_price' => 550,
                'stock' => 42,
                'reorder_level' => 15,
                'description' => 'Waterproof rain coat for riders',
                'location' => 'Display B',
                'color' => 'Yellow',
            ],
            [
                'name' => 'Motorcycle Cover Large',
                'sku' => 'COVER-LARGE',
                'brand' => 'Generic',
                'model' => 'Large',
                'category' => 'Accessories',
                'unit_cost' => 385,
                'selling_price' => 650,
                'stock' => 25,
                'reorder_level' => 10,
                'description' => 'UV-resistant motorcycle cover',
                'location' => 'Shelf G1',
                'color' => 'Silver',
            ],
            [
                'name' => 'Disc Lock with Alarm',
                'sku' => 'LOCK-DISC-ALARM',
                'brand' => 'Xena',
                'model' => 'XX6',
                'category' => 'Accessories',
                'unit_cost' => 1250,
                'selling_price' => 1950,
                'stock' => 18,
                'reorder_level' => 8,
                'description' => 'Security disc lock with 120dB alarm',
                'location' => 'Display C',
                'color' => 'Yellow',
            ],
        ];
        
        $this->products = collect();
        
        foreach ($productData as $data) {
            $product = Product::create($data);
            $this->products->push($product);
        }
        
        $this->command->info("   ✓ Created {$this->products->count()} products");
    }

    
    private function seedSalesTransactions()
    {
        $motorcycles = $this->products->where('category', 'Motorcycle');
        $spareParts = $this->products->where('category', 'Spare Parts');
        $accessories = $this->products->where('category', 'Accessories');
        
        $salesCount = 0;
        $loansCount = 0;
        
        // Generate sales for the past 120 days
        for ($day = 120; $day >= 0; $day--) {
            $date = now()->subDays($day);
            
            // Skip some days (weekends, holidays)
            if ($day > 0 && $date->dayOfWeek == 0) continue; // Skip Sundays
            if ($day > 0 && rand(1, 100) > 85) continue; // Random days off
            
            // More sales on recent days
            $dailySalesCount = $day < 30 ? rand(3, 8) : rand(1, 5);
            
            for ($i = 0; $i < $dailySalesCount; $i++) {
                $customer = $this->customers->random();
                $user = $this->users->random();
                
                // Determine sale type based on customer type
                $saleType = match($customer->customer_type ?? 'regular') {
                    'vip' => rand(1, 100) <= 70 ? 'cash' : 'loan',
                    'regular' => rand(1, 100) <= 55 ? 'cash' : 'loan',
                    'new' => rand(1, 100) <= 80 ? 'cash' : 'loan',
                    'overdue' => 'loan',
                };
                
                $paymentMode = rand(1, 100) <= 75 ? 'cash' : 'online';
                
                // Determine what to sell
                $saleCategory = rand(1, 100);
                $items = [];
                
                if ($saleCategory <= 25) {
                    // Motorcycle sale (25%)
                    $items[] = ['product' => $motorcycles->random(), 'quantity' => 1];
                    // Add some accessories
                    if (rand(1, 100) <= 70) {
                        $items[] = ['product' => $accessories->random(), 'quantity' => 1];
                    }
                } elseif ($saleCategory <= 70) {
                    // Spare parts sale (45%)
                    $itemCount = rand(1, 4);
                    for ($j = 0; $j < $itemCount; $j++) {
                        $items[] = ['product' => $spareParts->random(), 'quantity' => rand(1, 3)];
                    }
                } else {
                    // Accessories sale (30%)
                    $itemCount = rand(1, 2);
                    for ($j = 0; $j < $itemCount; $j++) {
                        $items[] = ['product' => $accessories->random(), 'quantity' => 1];
                    }
                }
                
                // Calculate totals
                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal += $item['product']->selling_price * $item['quantity'];
                }
                
                // Apply discount
                $discountPercent = match($customer->customer_type ?? 'regular') {
                    'vip' => rand(5, 15),
                    'regular' => rand(0, 5),
                    'new' => rand(0, 3),
                    'overdue' => 0,
                };
                
                $discountAmount = $subtotal * ($discountPercent / 100);
                $totalAmount = $subtotal - $discountAmount;
                
                $saleTime = $date->copy()->setTime(rand(8, 18), rand(0, 59));
                
                // Create sale
                $sale = Sale::create([
                    'sale_number' => sprintf('S-%s-%04d', $saleTime->format('Ymd'), $this->saleCounter++),
                    'customer_id' => $customer->id,
                    'user_id' => $user->id,
                    'sale_type' => $saleType,
                    'total_amount' => $totalAmount,
                    'payment_mode' => $paymentMode,
                    'amount_tendered' => ($saleType === 'cash' && $paymentMode === 'cash') 
                        ? ceil($totalAmount / 100) * 100 
                        : null,
                    'reference_number' => $paymentMode === 'online' 
                        ? 'REF-' . strtoupper(substr(md5($saleTime->timestamp . $customer->id), 0, 12))
                        : null,
                    'payment_bank' => $paymentMode === 'online' 
                        ? ['GCash', 'PayMaya', 'BPI', 'BDO', 'UnionBank'][array_rand(['GCash', 'PayMaya', 'BPI', 'BDO', 'UnionBank'])]
                        : null,
                    'payment_timestamp' => $paymentMode === 'online' ? $saleTime : null,
                    'discount_total' => $discountAmount > 0 ? $discountAmount : null,
                    'discount_reason' => $discountAmount > 0 
                        ? match($customer->customer_type ?? 'regular') {
                            'vip' => 'VIP Customer Discount',
                            'regular' => 'Regular Customer Discount',
                            'new' => 'New Customer Promo',
                            default => 'Promotional Discount',
                        }
                        : null,
                    'created_at' => $saleTime,
                    'updated_at' => $saleTime,
                ]);
                
                $salesCount++;
                
                // Create sale items and update stock
                foreach ($items as $item) {
                    $product = $item['product'];
                    $quantity = $item['quantity'];
                    
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $product->selling_price * $quantity,
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
                        'created_at' => $saleTime,
                        'updated_at' => $saleTime,
                    ]);
                }
                
                // Create loan if sale type is loan
                if ($saleType === 'loan') {
                    $this->createLoanWithPayments($sale, $customer, $saleTime, $day);
                    $loansCount++;
                }
            }
        }
        
        // Add some stock replenishments
        $this->addStockReplenishments();
        
        $this->command->info("   ✓ Created {$salesCount} sales ({$loansCount} with loans)");
    }

    
    private function createLoanWithPayments($sale, $customer, $saleTime, $daysAgo)
    {
        $totalAmount = $sale->total_amount;
        
        // Down payment based on customer type
        $downPaymentPercent = match($customer->customer_type ?? 'regular') {
            'vip' => rand(20, 30),
            'regular' => rand(25, 35),
            'new' => rand(30, 40),
            'overdue' => rand(35, 45),
        };
        
        $downPayment = $totalAmount * ($downPaymentPercent / 100);
        $principal = $totalAmount - $downPayment;
        
        // Term based on amount
        if ($totalAmount >= 50000) {
            $termMonths = [18, 24, 36][array_rand([18, 24, 36])];
        } elseif ($totalAmount >= 20000) {
            $termMonths = [12, 18, 24][array_rand([12, 18, 24])];
        } else {
            $termMonths = [6, 12][array_rand([6, 12])];
        }
        
        $interestRate = 2.0; // 2% monthly
        $monthlyRate = $interestRate / 100;
        $monthlyAmount = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        
        // Create loan
        $loan = Loan::create([
            'sale_id' => $sale->id,
            'customer_id' => $customer->id,
            'loan_amount' => $totalAmount,
            'down_payment' => $downPayment,
            'term_months' => $termMonths,
            'interest_rate' => $interestRate,
            'monthly_amount' => $monthlyAmount,
            'balance' => $principal,
            'start_date' => $saleTime,
            'next_due_date' => $saleTime->copy()->addMonth(),
            'status' => 'active',
            'id_type' => ['Driver\'s License', 'Passport', 'National ID', 'SSS ID'][array_rand(['Driver\'s License', 'Passport', 'National ID', 'SSS ID'])],
            'id_number' => 'ID-' . strtoupper(substr(md5($customer->id . $saleTime->timestamp), 0, 12)),
            'id_image_path' => 'loans/ids/sample-id-' . $customer->id . '.jpg',
            'created_at' => $saleTime,
            'updated_at' => $saleTime,
        ]);
        
        // Create payments for older loans
        if ($daysAgo > 30) {
            $monthsPassed = floor($daysAgo / 30);
            $paymentsToMake = min($monthsPassed, $termMonths);
            
            // Payment behavior based on customer type
            $paymentReliability = match($customer->customer_type ?? 'regular') {
                'vip' => 95,
                'regular' => 80,
                'new' => 70,
                'overdue' => 40,
            };
            
            for ($month = 1; $month <= $paymentsToMake; $month++) {
                $shouldPay = rand(1, 100) <= $paymentReliability;
                
                if ($shouldPay) {
                    $paymentDate = $saleTime->copy()->addMonths($month)->addDays(rand(-3, 7));
                    $amountPaid = $monthlyAmount;
                    
                    // Sometimes pay more
                    if (rand(1, 100) <= 15) {
                        $amountPaid += rand(500, 2000);
                    }
                    
                    $modeOfPayment = rand(1, 100) <= 70 ? 'Cash' : ['GCash', 'Bank Transfer', 'PayMaya'][array_rand(['GCash', 'Bank Transfer', 'PayMaya'])];
                    
                    $payment = Payment::create([
                        'loan_id' => $loan->id,
                        'amount_paid' => $amountPaid,
                        'payment_date' => $paymentDate,
                        'mode_of_payment' => $modeOfPayment,
                        'reference_number' => $modeOfPayment !== 'Cash' 
                            ? 'PAY-' . strtoupper(substr(md5($paymentDate->timestamp . $loan->id), 0, 12))
                            : null,
                        'payment_bank' => $modeOfPayment !== 'Cash' ? $modeOfPayment : null,
                        'payment_timestamp' => $paymentDate,
                        'created_at' => $paymentDate,
                        'updated_at' => $paymentDate,
                    ]);
                    
                    // Update loan balance
                    $loan->balance -= $amountPaid;
                    $loan->next_due_date = $paymentDate->copy()->addMonth();
                    
                    // Check if loan is paid off
                    if ($loan->balance <= 0) {
                        $loan->balance = 0;
                        $loan->status = 'completed';
                        $loan->save();
                        break;
                    }
                } else {
                    // Missed payment - add penalty for overdue customers
                    if ($customer->customer_type === 'overdue' && rand(1, 100) <= 60) {
                        $dueDate = $saleTime->copy()->addMonths($month);
                        $penaltyDate = $dueDate->copy()->addDays(rand(8, 15));
                        $daysLate = $penaltyDate->diffInDays($dueDate);
                        $penaltyRate = 5.0; // 5% penalty rate
                        $penaltyAmount = $monthlyAmount * ($penaltyRate / 100);
                        
                        LoanPenalty::create([
                            'loan_id' => $loan->id,
                            'payment_id' => null,
                            'type' => 'late_payment',
                            'rate' => $penaltyRate,
                            'base_amount' => $monthlyAmount,
                            'penalty_amount' => $penaltyAmount,
                            'due_date' => $dueDate->format('Y-m-d'),
                            'charged_date' => $penaltyDate->format('Y-m-d'),
                            'days_late' => $daysLate,
                            'notes' => 'Late payment penalty - ' . $daysLate . ' days overdue',
                            'created_at' => $penaltyDate,
                            'updated_at' => $penaltyDate,
                        ]);
                        
                        $loan->balance += $penaltyAmount;
                    }
                }
            }
            
            // Update loan status
            if ($loan->status === 'active') {
                if ($loan->next_due_date < now()->subDays(7)) {
                    $loan->status = 'overdue';
                }
            }
            
            $loan->save();
        }
    }

    
    private function addStockReplenishments()
    {
        $user = $this->users->first();
        
        // Add stock replenishments for low stock items
        foreach ($this->products as $product) {
            if ($product->stock < $product->reorder_level) {
                $replenishQty = rand($product->reorder_level * 2, $product->reorder_level * 4);
                
                $product->increment('stock', $replenishQty);
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'adjustment',
                    'quantity_change' => $replenishQty,
                    'reference_type' => 'purchase_order',
                    'reference_id' => null,
                    'remarks' => 'Stock replenishment - PO-' . strtoupper(substr(md5($product->id . time()), 0, 8)),
                    'performed_by' => $user->id,
                    'created_at' => now()->subDays(rand(1, 5)),
                ]);
            }
        }
    }
    
    private function seedRebates()
    {
        // Get motorcycle sales for rebates
        $motorcycleSales = Sale::with(['items.product', 'customer'])
            ->whereHas('items.product', function($q) {
                $q->where('category', 'Motorcycle');
            })
            ->where('created_at', '>=', now()->subDays(90))
            ->get();
        
        $rebateCount = 0;
        
        foreach ($motorcycleSales as $sale) {
            // 40% chance of having rebates
            if (rand(1, 100) <= 40) {
                foreach ($sale->items as $item) {
                    if ($item->product->category === 'Motorcycle') {
                        // Rebate amount: 3-8% of product price
                        $rebatePercent = rand(3, 8);
                        $rebateAmount = $item->subtotal * ($rebatePercent / 100);
                        
                        // 60% available, 40% used
                        $status = rand(1, 100) <= 60 ? 'available' : 'used';
                        
                        $rebate = Rebate::create([
                            'sale_id' => $sale->id,
                            'product_id' => $item->product_id,
                            'rebate_amount' => $rebateAmount,
                            'status' => $status,
                            'used_for' => $status === 'used' 
                                ? (rand(1, 100) <= 60 ? 'loan_payment' : 'purchase')
                                : null,
                            'applied_to_loan_id' => ($status === 'used' && $sale->loan) 
                                ? $sale->loan->id 
                                : null,
                            'used_at' => $status === 'used' 
                                ? $sale->created_at->copy()->addDays(rand(7, 30))
                                : null,
                            'created_at' => $sale->created_at,
                            'updated_at' => $status === 'used' 
                                ? $sale->created_at->copy()->addDays(rand(7, 30))
                                : $sale->created_at,
                        ]);
                        
                        $rebateCount++;
                    }
                }
            }
        }
        
        $this->command->info("   ✓ Created {$rebateCount} rebates");
    }

    
    private function seedNotifications()
    {
        $notificationCount = 0;
        
        // 1. Overdue payment notifications
        $overdueLoans = Loan::with(['customer', 'sale'])
            ->where('status', 'overdue')
            ->get();
        
        foreach ($overdueLoans as $loan) {
            $daysOverdue = now()->diffInDays($loan->next_due_date);
            $isActioned = rand(1, 100) <= 25;
            
            Notification::create([
                'type' => 'overdue_payment',
                'title' => 'Overdue Payment - ' . $loan->customer->full_name,
                'message' => sprintf(
                    'Customer %s has an overdue payment of ₱%s. Due date was %s (%d days ago).',
                    $loan->customer->full_name,
                    number_format($loan->monthly_amount, 2),
                    $loan->next_due_date->format('M d, Y'),
                    $daysOverdue
                ),
                'priority' => $daysOverdue > 14 ? 'critical' : 'high',
                'related_type' => 'App\Models\Loan',
                'related_id' => $loan->id,
                'data' => [
                    'customer_id' => $loan->customer_id,
                    'customer_name' => $loan->customer->full_name,
                    'customer_phone' => $loan->customer->contact,
                    'sale_number' => $loan->sale->sale_number ?? null,
                    'amount' => $loan->monthly_amount,
                    'due_date' => $loan->next_due_date->format('Y-m-d'),
                    'days_overdue' => $daysOverdue,
                    'balance' => $loan->balance,
                ],
                'is_read' => rand(1, 100) <= 40,
                'is_actioned' => $isActioned,
                'actioned_by' => $isActioned ? $this->users->random()->id : null,
                'actioned_at' => $isActioned ? now()->subDays(rand(1, 3)) : null,
                'created_at' => $loan->next_due_date->copy()->addDay(),
                'updated_at' => now(),
            ]);
            
            $notificationCount++;
        }
        
        // 2. Upcoming payment notifications (next 7 days)
        $upcomingLoans = Loan::with(['customer', 'sale'])
            ->where('status', 'active')
            ->whereBetween('next_due_date', [now(), now()->addDays(7)])
            ->get();
        
        foreach ($upcomingLoans as $loan) {
            $daysUntilDue = now()->diffInDays($loan->next_due_date, false);
            
            Notification::create([
                'type' => 'upcoming_payment',
                'title' => 'Upcoming Payment - ' . $loan->customer->full_name,
                'message' => sprintf(
                    'Customer %s has a payment of ₱%s due on %s (in %d days).',
                    $loan->customer->full_name,
                    number_format($loan->monthly_amount, 2),
                    $loan->next_due_date->format('M d, Y'),
                    $daysUntilDue
                ),
                'priority' => $daysUntilDue <= 2 ? 'high' : 'medium',
                'related_type' => 'App\Models\Loan',
                'related_id' => $loan->id,
                'data' => [
                    'customer_id' => $loan->customer_id,
                    'customer_name' => $loan->customer->full_name,
                    'customer_phone' => $loan->customer->contact,
                    'sale_number' => $loan->sale->sale_number ?? null,
                    'amount' => $loan->monthly_amount,
                    'due_date' => $loan->next_due_date->format('Y-m-d'),
                    'days_until_due' => $daysUntilDue,
                ],
                'is_read' => rand(1, 100) <= 60,
                'is_actioned' => false,
                'created_at' => now()->subDays(rand(0, 2)),
                'updated_at' => now(),
            ]);
            
            $notificationCount++;
        }
        
        // 3. Low stock notifications
        $lowStockProducts = Product::whereRaw('stock <= reorder_level')->get();
        
        foreach ($lowStockProducts as $product) {
            $criticalThreshold = max(1, floor($product->reorder_level / 2));
            $isCritical = $product->stock <= $criticalThreshold;
            $isActioned = rand(1, 100) <= 35;
            
            Notification::create([
                'type' => 'low_stock',
                'title' => ($isCritical ? 'CRITICAL: ' : '') . 'Low Stock - ' . $product->name,
                'message' => sprintf(
                    'Product "%s" (SKU: %s) is %s. Current stock: %d, Reorder level: %d',
                    $product->name,
                    $product->sku,
                    $isCritical ? 'critically low' : 'running low',
                    $product->stock,
                    $product->reorder_level
                ),
                'priority' => $isCritical ? 'critical' : 'medium',
                'related_type' => 'App\Models\Product',
                'related_id' => $product->id,
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $product->stock,
                    'reorder_level' => $product->reorder_level,
                    'category' => $product->category,
                    'is_critical' => $isCritical,
                ],
                'is_read' => rand(1, 100) <= 50,
                'is_actioned' => $isActioned,
                'actioned_by' => $isActioned ? $this->users->random()->id : null,
                'actioned_at' => $isActioned ? now()->subDays(rand(1, 3)) : null,
                'created_at' => now()->subDays(rand(1, 5)),
                'updated_at' => now(),
            ]);
            
            $notificationCount++;
        }
        
        // 4. High value sale notifications (sales > 50000)
        $highValueSales = Sale::with(['customer', 'user'])
            ->where('total_amount', '>', 50000)
            ->where('created_at', '>=', now()->subDays(7))
            ->get();
        
        foreach ($highValueSales as $sale) {
            Notification::create([
                'type' => 'high_value_sale',
                'title' => 'High Value Sale - ₱' . number_format($sale->total_amount, 2),
                'message' => sprintf(
                    'High value %s sale of ₱%s to %s. Sale #%s by %s.',
                    $sale->sale_type,
                    number_format($sale->total_amount, 2),
                    $sale->customer->full_name,
                    $sale->sale_number,
                    $sale->user->name
                ),
                'priority' => 'medium',
                'related_type' => 'App\Models\Sale',
                'related_id' => $sale->id,
                'data' => [
                    'sale_id' => $sale->id,
                    'sale_number' => $sale->sale_number,
                    'customer_name' => $sale->customer->full_name,
                    'amount' => $sale->total_amount,
                    'sale_type' => $sale->sale_type,
                    'user_name' => $sale->user->name,
                ],
                'is_read' => true,
                'is_actioned' => false,
                'created_at' => $sale->created_at->copy()->addMinutes(5),
                'updated_at' => now(),
            ]);
            
            $notificationCount++;
        }
        
        $this->command->info("   ✓ Created {$notificationCount} notifications");
    }

    
    private function displaySummary()
    {
        $this->command->newLine();
        $this->command->info('📊 Database Summary:');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Users', User::count()],
                ['Customers', Customer::count()],
                ['Products', Product::count()],
                ['Sales', Sale::count()],
                ['  - Cash Sales', Sale::where('sale_type', 'cash')->count()],
                ['  - Loan Sales', Sale::where('sale_type', 'loan')->count()],
                ['Loans', Loan::count()],
                ['  - Active', Loan::where('status', 'active')->count()],
                ['  - Overdue', Loan::where('status', 'overdue')->count()],
                ['  - Completed', Loan::where('status', 'completed')->count()],
                ['Payments', Payment::count()],
                ['Rebates', Rebate::count()],
                ['  - Available', Rebate::where('status', 'available')->count()],
                ['  - Used', Rebate::where('status', 'used')->count()],
                ['Stock Movements', StockMovement::count()],
                ['Notifications', Notification::count()],
                ['  - Unread', Notification::where('is_read', false)->count()],
                ['  - Urgent', Notification::where('priority', 'critical')->count()],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('💰 Financial Summary:');
        $totalSales = Sale::sum('total_amount');
        $totalLoans = Loan::sum('loan_amount');
        $totalReceivables = Loan::whereIn('status', ['active', 'overdue'])->sum('balance');
        $totalPayments = Payment::sum('amount_paid');
        
        $this->command->table(
            ['Metric', 'Amount'],
            [
                ['Total Sales', '₱' . number_format($totalSales, 2)],
                ['Total Loans Issued', '₱' . number_format($totalLoans, 2)],
                ['Total Receivables', '₱' . number_format($totalReceivables, 2)],
                ['Total Payments Collected', '₱' . number_format($totalPayments, 2)],
                ['Today\'s Sales', '₱' . number_format(Sale::whereDate('created_at', today())->sum('total_amount'), 2)],
                ['This Month\'s Sales', '₱' . number_format(Sale::whereMonth('created_at', now()->month)->sum('total_amount'), 2)],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('🔑 Login Credentials:');
        $this->command->table(
            ['Role', 'Email', 'Username', 'Password'],
            [
                ['Admin', 'admin@equiserve.test', 'admin', 'password'],
                ['Manager', 'manager@equiserve.test', 'manager', 'password'],
                ['Staff', 'staff@equiserve.test', 'staff', 'password'],
                ['Staff', 'staff2@equiserve.test', 'staff2', 'password'],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('✨ Defense Presentation Data Ready!');
        $this->command->info('   - Realistic customer data with different payment behaviors');
        $this->command->info('   - Comprehensive product catalog (motorcycles, parts, accessories)');
        $this->command->info('   - 120 days of sales history with various scenarios');
        $this->command->info('   - Active loans with payment histories');
        $this->command->info('   - Overdue accounts for demonstration');
        $this->command->info('   - Low stock alerts');
        $this->command->info('   - Rebates system data');
        $this->command->info('   - Stock movement tracking');
        $this->command->newLine();
    }
}
