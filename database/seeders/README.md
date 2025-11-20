# Database Seeder Documentation

## Overview
The ComprehensiveSeeder provides a complete dataset for the EquiServe system, covering all tables with realistic data for testing and demonstration purposes.

## What Gets Seeded

### 1. Users (4 users)
- **Admin** - Full system access
  - Email: `admin@equiserve.test`
  - Username: `admin`
  - Password: `password`

- **Manager** - Management access
  - Email: `manager@equiserve.test`
  - Username: `manager`
  - Password: `password`

- **Staff 1** - Staff access
  - Email: `staff@equiserve.test`
  - Username: `staff`
  - Password: `password`

- **Staff 2** - Staff access
  - Email: `staff2@equiserve.test`
  - Username: `staff2`
  - Password: `password`

### 2. Customers (20 customers)
- Realistic Filipino names
- General Santos City addresses
- Various statuses: good_standing, active, overdue
- Created over the past 30-180 days

### 3. Products (25 products)
**Motorcycles (8 units)**
- Honda XRM 125, Yamaha Mio i 125, Suzuki Raider R150, etc.
- Price range: ₱58,000 - ₱88,000
- Stock levels: 4-12 units

**Spare Parts (13 items)**
- Engine oil, spark plugs, filters, brake pads, chains, batteries, tires, etc.
- Price range: ₱150 - ₱1,800
- Stock levels: 18-100 units

**Accessories (4 items)**
- Helmets, jackets, gloves, rain coats
- Price range: ₱500 - ₱4,500
- Stock levels: 10-35 units

### 4. Sales & Loans (200+ transactions)
- **Sales Period**: Past 90 days
- **Sales per Day**: 1-5 transactions (70% of days have sales)
- **Sale Types**: 60% cash, 40% loan
- **Payment Modes**: 70% cash, 30% online
- **Items per Sale**: 1-3 products
- **Discounts**: 0-10% random discounts

**Loan Details:**
- Down Payment: 20-40% of total
- Terms: 6, 12, 18, 24, or 36 months
- Interest Rate: 2% monthly
- Payment Rate: 80% of due payments are paid
- Statuses: active, overdue, completed

### 5. Sale Items (400+ items)
- Linked to sales
- Quantity: 1-2 per product
- Proper pricing and subtotals

### 6. Payments (100+ payments)
- Linked to loans
- Payment modes: 70% Cash, 30% GCash
- Reference numbers for online payments
- Realistic payment dates

### 7. Stock Movements (400+ movements)
- Type: sale (stock decreases)
- Tracks all inventory changes
- Links to sales and products
- User tracking

### 8. Rebates (20-30 rebates)
- 40% of customers have rebates
- Amounts: ₱500, ₱1,000, ₱1,500, ₱2,000
- Statuses: available, used, expired
- Expiry dates: 30-90 days from issue

### 9. Notifications (50+ notifications)
**Types:**
- **Overdue Payment** (high priority)
  - For all overdue loans
  - 30% marked as read
  - 20% actioned

- **Low Stock Alert** (medium/high priority)
  - For products at or below reorder level
  - Priority based on stock level
  - 50% marked as read

- **Upcoming Payment** (medium priority)
  - For payments due within 7 days
  - 60% marked as read

## Usage

### Fresh Seed (Recommended)
```bash
php artisan migrate:fresh --seed
```

This will:
1. Drop all tables
2. Run all migrations
3. Seed all data

### Seed Only (Keep existing data)
```bash
php artisan db:seed
```

**Warning:** This will truncate most tables except users (keeps admin).

### Seed Specific Seeder
```bash
php artisan db:seed --class=ComprehensiveSeeder
```

## Data Statistics

After seeding, you should have approximately:
- **Users**: 4
- **Customers**: 20
- **Products**: 25
- **Sales**: 200+
- **Sale Items**: 400+
- **Loans**: 80+ (40% of sales)
- **Payments**: 100+
- **Stock Movements**: 400+
- **Rebates**: 20-30
- **Notifications**: 50+

## Data Characteristics

### Realistic Patterns
- Sales concentrated on weekdays
- More cash sales than loan sales
- Higher payment compliance for newer loans
- Stock levels reflect sales activity
- Notifications reflect actual system events

### Date Distribution
- Sales: Past 90 days
- Customers: Created 30-180 days ago
- Loans: Various stages (new, mid-term, near completion)
- Payments: Follow loan schedules
- Notifications: Recent (past 7 days)

### Financial Data
- Total Sales Value: ~₱2,000,000 - ₱3,000,000
- Total Accounts Receivable: ~₱800,000 - ₱1,200,000
- Total Inventory Value: ~₱1,500,000 - ₱2,000,000
- Average Sale: ₱10,000 - ₱15,000

## Testing Scenarios

The seeded data supports testing of:

### Sales Module
- ✅ Cash sales processing
- ✅ Loan sales processing
- ✅ Online payment validation
- ✅ Sales reconciliation
- ✅ Sales filtering and search

### Loan Module
- ✅ Active loans management
- ✅ Overdue loan tracking
- ✅ Payment processing
- ✅ Aging analysis (current, 1-30, 31-60, 60+ days)
- ✅ Loan completion

### Inventory Module
- ✅ Stock level monitoring
- ✅ Low stock alerts
- ✅ Stock movement tracking
- ✅ Product search and filtering

### Customer Module
- ✅ Customer management
- ✅ Purchase history
- ✅ Payment history
- ✅ Rebate management
- ✅ Customer statements

### Reports Module
- ✅ Sales reports (by date, customer, product)
- ✅ Inventory reports (stock levels, movements)
- ✅ Collection reports (AR, aging, payments)
- ✅ Customer statements
- ✅ Daily reconciliation
- ✅ Sales trends
- ✅ Top products
- ✅ Top customers

### Notifications Module
- ✅ Overdue payment alerts
- ✅ Low stock alerts
- ✅ Upcoming payment reminders
- ✅ Notification filtering
- ✅ Mark as read/actioned

## Customization

To modify the seeded data:

1. **Change quantities**: Edit the loops in `seedSalesAndLoans()`
2. **Add products**: Add to `$productData` array in `seedProducts()`
3. **Add customers**: Add to `$customerData` array in `seedCustomers()`
4. **Adjust date range**: Change `$day` range in `seedSalesAndLoans()`
5. **Modify ratios**: Adjust percentages (e.g., cash vs loan ratio)

## Notes

- All passwords are `password` for easy testing
- Reference numbers are unique (using `uniqid()`)
- Stock levels are automatically updated based on sales
- Loan statuses are automatically calculated
- Notifications are generated based on actual data
- All timestamps are realistic and sequential

## Troubleshooting

### Foreign Key Errors
Make sure to run migrations first:
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Duplicate Entry Errors
The seeder truncates tables before seeding. If you get duplicates, run:
```bash
php artisan migrate:fresh --seed
```

### Performance
Seeding takes 10-30 seconds depending on your system. This is normal due to the large amount of data being created.

## Support

For issues or questions about the seeder, check:
1. Migration files are up to date
2. Database connection is configured
3. All models exist and are properly configured
4. Foreign key constraints are properly set up
