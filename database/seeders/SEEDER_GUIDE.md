# EquiServe Database Seeders Guide

## Overview

This guide explains the available database seeders for the EquiServe system.

---

## 🎓 DefenseSeeder (RECOMMENDED for Defense)

**File**: `DefenseSeeder.php`

### Purpose
Comprehensive, realistic seeder specifically designed for thesis defense presentations. Creates detailed, interconnected data that showcases all system features.

### What It Creates

#### Users (4)
- 1 Admin
- 1 Manager  
- 2 Staff members

#### Customers (25)
- **VIP Customers** (3): High-value, excellent payment history
- **Regular Customers** (12): Good payment history
- **New Customers** (3): Recent signups
- **Overdue Customers** (3): Payment issues for demonstration
- **Additional Customers** (4): Variety

#### Products (40)
- **Motorcycles** (8): Honda, Yamaha, Suzuki, Kawasaki models
- **Spare Parts** (25): 
  - Engine oils (3 brands)
  - Spark plugs (2 types)
  - Filters (2 types)
  - Brake parts (3 types)
  - Chains (2 brands)
  - Batteries (2 brands)
  - Tires (4 types)
  - Lights (3 types)
  - Mirrors (2)
- **Accessories** (7):
  - Helmets (3 types)
  - Riding jacket
  - Gloves
  - Rain coat
  - Motorcycle cover
  - Disc lock

#### Sales Transactions (300-500)
- **120 days of history**
- Mix of cash and loan sales
- Various payment modes (cash, online)
- Realistic discounts based on customer type
- Multiple items per sale
- Stock movements tracked

#### Loans (150-250)
- Various terms: 6, 12, 18, 24, 36 months
- Down payments: 20-45%
- Interest rate: 2% monthly
- Payment histories
- Some overdue accounts
- Penalties for late payments
- ID verification data

#### Payments (500-1000)
- Regular monthly payments
- Some late payments
- Various payment modes
- Reference numbers for online payments

#### Rebates (50-100)
- 3-8% of motorcycle sales
- 60% available, 40% used
- Applied to loans or purchases

#### Stock Movements (1000+)
- Sales transactions
- Stock replenishments
- Complete audit trail

#### Notifications (50-100)
- Overdue payment alerts
- Upcoming payment reminders
- Low stock warnings
- High-value sale notifications

### Usage

```bash
# Fresh migration and seed
php artisan migrate:fresh --seed

# Or seed only
php artisan db:seed --class=DefenseSeeder
```

### Features Demonstrated

✅ **Complete Sales Cycle**
- Cash sales
- Loan/installment sales
- Online payments
- Discounts and promotions

✅ **Loan Management**
- Loan creation
- Payment tracking
- Overdue handling
- Penalties
- ID verification

✅ **Inventory Management**
- Stock tracking
- Low stock alerts
- Stock movements
- Reorder levels

✅ **Customer Management**
- Different customer types
- Payment behaviors
- Rebates system

✅ **Reporting Data**
- Sales trends
- Payment collections
- Inventory status
- Customer analytics

✅ **Notifications System**
- Critical alerts
- Payment reminders
- Stock warnings

---

## 🧪 AdminOnlySeeder (For Testing)

**File**: `AdminOnlySeeder.php`

### Purpose
Creates only an admin user with empty database for manual CRUD testing and debugging.

### What It Creates
- 1 Admin user
- Empty database (all other tables cleared)

### Usage
```bash
php artisan db:seed --class=AdminOnlySeeder
```

### When to Use
✅ Testing create operations
✅ Testing update operations
✅ Testing delete operations
✅ Testing validations
✅ Debugging CRUD operations
✅ Clean slate for manual testing

---

## 📦 ComprehensiveSeeder (Alternative)

**File**: `ComprehensiveSeeder.php`

### Purpose
Basic comprehensive seeder with simpler data structure (kept for backward compatibility).

### What It Creates
- 4 Users
- 20 Customers
- 25 Products
- 90 days of sales
- Loans with payments
- Rebates
- Notifications

### Usage
```bash
php artisan db:seed --class=ComprehensiveSeeder
```

---

## 🎯 Which Seeder to Use?

### Use DefenseSeeder When:
✅ Preparing for thesis defense
✅ Need realistic, detailed data
✅ Want to showcase all features
✅ Need variety in scenarios
✅ Demonstrating to stakeholders
✅ Final presentation

### Use AdminOnlySeeder When:
✅ Testing CRUD operations
✅ Debugging features
✅ Need clean database
✅ Manual testing
✅ Development work

### Use ComprehensiveSeeder When:
✅ Quick testing
✅ Development environment
✅ Basic functionality testing

---

## 📊 Data Statistics (DefenseSeeder)

| Entity | Approximate Count |
|--------|------------------|
| Users | 4 |
| Customers | 25 |
| Products | 40 |
| Sales | 300-500 |
| Loans | 150-250 |
| Payments | 500-1000 |
| Rebates | 50-100 |
| Stock Movements | 1000+ |
| Notifications | 50-100 |

### Financial Data
- Total Sales: ₱5-10 Million
- Total Loans: ₱3-7 Million
- Receivables: ₱1-3 Million
- Payments Collected: ₱2-5 Million

---

## 🔑 Login Credentials

All seeders create the same user accounts:

| Role | Email | Username | Password |
|------|-------|----------|----------|
| Admin | admin@equiserve.test | admin | password |
| Manager | manager@equiserve.test | manager | password |
| Staff | staff@equiserve.test | staff | password |
| Staff | staff2@equiserve.test | staff2 | password |

---

## 🎓 Defense Presentation Tips

### 1. Dashboard Demonstration
- Show sales trends (120 days of data)
- Highlight key metrics
- Show urgent notifications

### 2. Sales Management
- Create a cash sale
- Create a loan sale with ID verification
- Show payment modes (cash, online)
- Demonstrate discount application

### 3. Loan Management
- Show active loans
- Demonstrate payment recording
- Show overdue accounts
- Display amortization schedule

### 4. Inventory Management
- Show low stock alerts
- Demonstrate stock movements
- Show product categories
- Display reorder levels

### 5. Customer Management
- Show different customer types
- Display payment history
- Show rebates system
- Demonstrate account statements

### 6. Reports
- Sales reports (daily, monthly)
- Collection reports
- Inventory reports
- Customer statements

### 7. Notifications
- Show overdue alerts
- Upcoming payment reminders
- Low stock warnings
- High-value sale notifications

---

## 🔄 Reseeding

### Fresh Start
```bash
# Drop all tables, migrate, and seed
php artisan migrate:fresh --seed
```

### Keep Structure, Reseed Data
```bash
# Truncate tables and reseed
php artisan db:seed --class=DefenseSeeder
```

### Specific Seeder
```bash
# Run only customers
php artisan db:seed --class=CustomerSeeder

# Run only products
php artisan db:seed --class=ProductSeeder
```

---

## ⚠️ Important Notes

### Before Defense
1. ✅ Run `php artisan migrate:fresh --seed`
2. ✅ Verify all data loaded correctly
3. ✅ Test login with all user roles
4. ✅ Check dashboard displays properly
5. ✅ Verify notifications appear
6. ✅ Test offline functionality

### During Defense
1. ✅ Use Admin account for full access
2. ✅ Have backup data ready
3. ✅ Know where key features are
4. ✅ Prepare example scenarios
5. ✅ Test before presentation

### Data Characteristics
- **Realistic**: Based on actual motorcycle shop operations
- **Varied**: Different scenarios and edge cases
- **Complete**: All relationships properly connected
- **Demonstrable**: Easy to showcase features
- **Professional**: Proper naming and formatting

---

## 🐛 Troubleshooting

### Issue: Foreign Key Errors
**Solution**: Run `php artisan migrate:fresh --seed` instead of just seeding

### Issue: Duplicate Entries
**Solution**: DefenseSeeder clears existing data automatically

### Issue: Slow Seeding
**Solution**: Normal for DefenseSeeder (creates 1000+ records). Takes 30-60 seconds.

### Issue: Memory Limit
**Solution**: Increase PHP memory limit in php.ini or run:
```bash
php -d memory_limit=512M artisan db:seed
```

---

## 📝 Customization

### Adjust Data Volume
Edit `DefenseSeeder.php`:

```php
// Line ~XXX: Change days of history
for ($day = 120; $day >= 0; $day--) {
    // Change 120 to desired days
}

// Line ~XXX: Change daily sales count
$dailySalesCount = $day < 30 ? rand(3, 8) : rand(1, 5);
    // Adjust ranges as needed
```

### Add More Products
Edit the `$productData` array in `seedProducts()` method.

### Modify Customer Types
Edit the `$customerData` array in `seedCustomers()` method.

---

## ✅ Verification Checklist

After seeding, verify:

- [ ] Dashboard loads without errors
- [ ] All stat cards show data
- [ ] Sales chart displays
- [ ] Notifications appear
- [ ] Products have stock
- [ ] Customers have data
- [ ] Loans show correctly
- [ ] Payments recorded
- [ ] Rebates visible
- [ ] Stock movements tracked

---

## 🎉 Ready for Defense!

With DefenseSeeder, you have:
- ✅ Comprehensive, realistic data
- ✅ All features demonstrated
- ✅ Various scenarios covered
- ✅ Professional presentation
- ✅ Complete audit trail

**Good luck with your defense!** 🎓

---

**Last Updated**: November 21, 2025
**Version**: 2.0.0
