# 🌱 Seeders Quick Reference

## Available Seeders

### 1. 🎓 DefenseSeeder (Default)
**For thesis defense and demonstrations**

```bash
php artisan db:seed --class=DefenseSeeder
# or
php artisan migrate:fresh --seed
```

**Creates:**
- 25 customers
- 39 products
- 300-500 sales
- 150-250 loans
- Complete transaction history

**Use when:** Presenting, demonstrating, defense

---

### 2. 🧪 AdminOnlySeeder
**For manual CRUD testing**

```bash
php artisan db:seed --class=AdminOnlySeeder
```

**Creates:**
- 1 admin user only
- Empty database (all tables cleared)

**Use when:** 
- Testing create operations
- Testing update operations
- Testing delete operations
- Debugging
- Need clean slate

**Login:**
- Username: `admin`
- Password: `password`

---

### 3. 📦 ComprehensiveSeeder
**For basic testing**

```bash
php artisan db:seed --class=ComprehensiveSeeder
```

**Creates:**
- 4 users
- 20 customers
- 25 products
- 90 days of sales

**Use when:** Quick testing, development

---

## Quick Commands

### Defense Presentation
```bash
php artisan migrate:fresh --seed
```

### Manual Testing
```bash
php artisan db:seed --class=AdminOnlySeeder
```

### Quick Test Data
```bash
php artisan db:seed --class=ComprehensiveSeeder
```

---

## Login Credentials

All seeders use the same credentials:

| Username | Password |
|----------|----------|
| admin    | password |

---

## Seeder Comparison

| Feature | DefenseSeeder | AdminOnlySeeder | ComprehensiveSeeder |
|---------|--------------|-----------------|---------------------|
| **Users** | 4 | 1 | 4 |
| **Customers** | 25 | 0 | 20 |
| **Products** | 39 | 0 | 25 |
| **Sales** | 300-500 | 0 | 100-200 |
| **History** | 120 days | None | 90 days |
| **Purpose** | Defense | Testing | Development |
| **Time** | 60s | 1s | 20s |

---

## When to Use Each

### 🎓 DefenseSeeder
- ✅ Thesis defense
- ✅ Stakeholder demo
- ✅ Feature showcase
- ✅ Final presentation

### 🧪 AdminOnlySeeder
- ✅ CRUD testing
- ✅ Debugging
- ✅ Manual testing
- ✅ Clean database needed

### 📦 ComprehensiveSeeder
- ✅ Quick testing
- ✅ Development
- ✅ Basic features

---

## File Locations

```
database/seeders/
├── DatabaseSeeder.php          # Main seeder
├── DefenseSeeder.php           # Defense presentation
├── AdminOnlySeeder.php         # Testing only
├── ComprehensiveSeeder.php     # Basic data
└── SEEDER_GUIDE.md            # Full documentation
```

---

**Quick Tip:** Use `AdminOnlySeeder` when you need to test creating customers, products, and sales from scratch without any existing data!
