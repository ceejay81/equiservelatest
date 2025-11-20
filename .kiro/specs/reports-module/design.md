# Reports Module Design Document

## Overview

The Reports Module provides comprehensive business intelligence and reporting capabilities for the EquiServe system. It transforms transactional data from sales, inventory, loans, and customer modules into actionable insights through various report types. The module emphasizes performance, usability, and offline functionality while maintaining consistency with the existing application design.

### Design Principles

1. **Performance-First**: Optimized queries and caching for fast report generation
2. **Offline-Capable**: All reports work without internet connectivity
3. **Export-Ready**: Built-in Excel and PDF export for all reports
4. **Filter-Driven**: Flexible filtering to drill down into specific data
5. **Visual Analytics**: Charts and graphs for trend analysis
6. **Consistent UI**: Follows existing AdminLTE and Bootstrap patterns
7. **Mobile-Responsive**: Reports viewable on tablets and mobile devices

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Presentation Layer                    │
│     (Blade Templates + Bootstrap + Chart.js)            │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                   Application Layer                      │
│         (ReportsController + Request Validation)         │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                      Service Layer                       │
│              (ReportService + Calculations)              │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                      Data Layer                          │
│        (Eloquent Models + Query Optimization)            │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                      Export Layer                        │
│         (Excel Exports + PDF Generation)                 │
└─────────────────────────────────────────────────────────┘
```

### Technology Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: Blade Templates, Bootstrap 4, AdminLTE 3.2
- **Charts**: Chart.js 3.9
- **Excel Export**: rap2hpoutre/fast-excel (already installed)
- **PDF Export**: Laravel built-in or DomPDF
- **Database**: MySQL (via XAMPP)
- **Icons**: Font Awesome 5

## Components and Interfaces

### 1. Controller Layer

#### ReportsController

```php
class ReportsController extends Controller
{
    // Reports Dashboard
    public function index();
    
    // Sales Reports
    public function sales(Request $request);
    public function salesExport(Request $request);
    
    // Inventory Reports
    public function inventory(Request $request);
    public function inventoryExport(Request $request);
    public function stockMovements(Request $request);
    
    // Collection Reports
    public function collections(Request $request);
    public function collectionsExport(Request $request);
    public function aging(Request $request);
    
    // Customer Statements
    public function customerStatement(Request $request);
    public function customerStatementPdf($customerId);
    
    // Reconciliation
    public function reconciliation(Request $request);
    
    // Analytics
    public function salesTrends(Request $request);
    public function topProducts(Request $request);
    public function topCustomers(Request $request);
}
```

### 2. Service Layer

#### ReportService

```php
class ReportService
{
    // Sales Calculations
    public function calculateSalesMetrics($dateFrom, $dateTo, $filters = []);
    public function getSalesByDateRange($dateFrom, $dateTo, $filters = []);
    public function getSalesTrendData($dateFrom, $dateTo);
    
    // Inventory Calculations
    public function calculateInventoryMetrics();
    public function getStockMovements($dateFrom, $dateTo, $filters = []);
    public function getLowStockProducts();
    
    // Collection Calculations
    public function calculateCollectionsMetrics($dateFrom, $dateTo);
    public function getAgingAnalysis();
    public function getPaymentHistory($dateFrom, $dateTo, $filters = []);
    
    // Customer Calculations
    public function getCustomerStatement($customerId);
    public function getTopCustomers($dateFrom, $dateTo, $limit = 20);
    
    // Product Analytics
    public function getTopProducts($dateFrom, $dateTo, $limit = 10);
    
    // Reconciliation
    public function getDailyReconciliation($date);
    
    // Caching
    protected function cacheKey($prefix, $params);
    protected function getCached($key, $callback, $minutes = 5);
}
```

### 3. Export Classes

#### SalesReportExport

```php
class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sales;
    protected $metrics;
    
    public function __construct($sales, $metrics);
    public function collection();
    public function headings(): array;
    public function map($sale): array;
}
```

#### InventoryReportExport

```php
class InventoryReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $products;
    protected $metrics;
    
    public function __construct($products, $metrics);
    public function collection();
    public function headings(): array;
    public function map($product): array;
}
```

#### CollectionReportExport

```php
class CollectionReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payments;
    protected $aging;
    
    public function __construct($payments, $aging);
    public function collection();
    public function headings(): array;
    public function map($payment): array;
}
```

### 4. View Layer

#### Reports Dashboard (reports/index.blade.php)

```
┌─────────────────────────────────────────────────────────┐
│ Reports & Analytics                                     │
│ Generate and export business reports                    │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐      │
│ │   Sales     │ │  Inventory  │ │ Collections │      │
│ │   Reports   │ │   Reports   │ │   Reports   │      │
│ │             │ │             │ │             │      │
│ │ Last: Today │ │ Last: Today │ │ Last: Today │      │
│ └─────────────┘ └─────────────┘ └─────────────┘      │
│                                                         │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐      │
│ │  Customer   │ │    Daily    │ │   Sales     │      │
│ │ Statements  │ │Reconciliation│ │   Trends    │      │
│ │             │ │             │ │             │      │
│ │ Last: Today │ │ Last: Today │ │ Last: Today │      │
│ └─────────────┘ └─────────────┘ └─────────────┘      │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### Sales Report Page (reports/sales.blade.php)

```
┌─────────────────────────────────────────────────────────┐
│ ← Back to Reports                                       │
│                                                         │
│ Sales Report                                            │
│                                                         │
│ Filters:                                                │
│ From: [__________] To: [__________] [Apply]            │
│ Customer: [All Customers ▼]  Product: [All Products ▼] │
│                                                         │
│ [Export to Excel]                                       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Summary Statistics                                      │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │
│ │  Total   │ │  Total   │ │ Average  │ │   Cash   │  │
│ │  Sales   │ │  Trans.  │ │  Value   │ │   Sales  │  │
│ │ ₱150,000 │ │    45    │ │  ₱3,333  │ │ ₱80,000  │  │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘  │
│                                                         │
│ Sales Transactions                                      │
│ ┌─────────────────────────────────────────────────────┐│
│ │Date      │Customer    │Sale #  │Type │Amount      ││
│ ├─────────────────────────────────────────────────────┤│
│ │Nov 18    │John Doe    │S-001   │Cash │₱5,000      ││
│ │Nov 18    │Jane Smith  │S-002   │Loan │₱15,000     ││
│ │Nov 17    │Bob Johnson │S-003   │Cash │₱3,500      ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ [Previous] Page 1 of 3 [Next]                          │
└─────────────────────────────────────────────────────────┘
```

#### Inventory Report Page (reports/inventory.blade.php)

```
┌─────────────────────────────────────────────────────────┐
│ ← Back to Reports                                       │
│                                                         │
│ Inventory Report                                        │
│                                                         │
│ Filters:                                                │
│ Status: [All Products ▼]  Category: [All Categories ▼] │
│                                                         │
│ [Export to Excel]                                       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Summary Statistics                                      │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │
│ │  Total   │ │  Total   │ │   Low    │ │ Critical │  │
│ │ Products │ │  Value   │ │  Stock   │ │  Stock   │  │
│ │   150    │ │₱2,500,000│ │    12    │ │    3     │  │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘  │
│                                                         │
│ Product Stock Levels                                    │
│ ┌─────────────────────────────────────────────────────┐│
│ │Product       │SKU    │Stock │Price   │Value       ││
│ ├─────────────────────────────────────────────────────┤│
│ │Honda XRM 125 │XRM125 │  5 🔴│₱75,000 │₱375,000    ││
│ │Yamaha Mio    │MIO150 │ 15   │₱65,000 │₱975,000    ││
│ │Spark Plug    │SP001  │ 50   │₱150    │₱7,500      ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ 🔴 Critical Stock  🟡 Low Stock                        │
└─────────────────────────────────────────────────────────┘
```

#### Collection Report Page (reports/collections.blade.php)

```
┌─────────────────────────────────────────────────────────┐
│ ← Back to Reports                                       │
│                                                         │
│ Collection Report (Accounts Receivable)                 │
│                                                         │
│ Filters:                                                │
│ From: [__________] To: [__________] [Apply]            │
│ Customer: [All Customers ▼]  Status: [All ▼]           │
│                                                         │
│ [Export to Excel]                                       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Aging Analysis                                          │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │
│ │ Current  │ │  1-30    │ │  31-60   │ │   60+    │  │
│ │ Not Due  │ │   Days   │ │   Days   │ │   Days   │  │
│ │ ₱500,000 │ │ ₱150,000 │ │ ₱75,000  │ │ ₱25,000  │  │
│ │  (25)    │ │   (8)    │ │   (4)    │ │   (2)    │  │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘  │
│                                                         │
│ Active Loans                                            │
│ ┌─────────────────────────────────────────────────────┐│
│ │Customer    │Loan Amt │Balance │Due Date │Status   ││
│ ├─────────────────────────────────────────────────────┤│
│ │John Doe    │₱50,000  │₱30,000 │Nov 25   │Active   ││
│ │Jane Smith  │₱75,000  │₱45,000 │Nov 20   │Overdue🔴││
│ │Bob Johnson │₱40,000  │₱20,000 │Dec 5    │Active   ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ Payment History                                         │
│ ┌─────────────────────────────────────────────────────┐│
│ │Date      │Customer    │Amount  │Mode  │Type        ││
│ ├─────────────────────────────────────────────────────┤│
│ │Nov 18    │John Doe    │₱5,000  │Cash  │Installment ││
│ │Nov 18    │Jane Smith  │₱15,000 │Online│Down Payment││
│ └─────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────┘
```

#### Customer Statement Page (reports/customer-statement.blade.php)

```
┌─────────────────────────────────────────────────────────┐
│ ← Back to Reports                                       │
│                                                         │
│ Customer Statement                                      │
│                                                         │
│ Select Customer: [Choose Customer ▼] [Generate]        │
│                                                         │
│ [Print] [Export to PDF]                                │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ EQUISERVE GENSAN                                        │
│ Customer Statement                                      │
│                                                         │
│ Customer: John Doe                                      │
│ Account #: CUST-001                                     │
│ Contact: 0912-345-6789                                  │
│ Statement Date: November 18, 2025                       │
│                                                         │
│ Account Summary                                         │
│ Total Purchases:    ₱150,000                            │
│ Total Paid:         ₱80,000                             │
│ Outstanding:        ₱70,000                             │
│                                                         │
│ Transaction History                                     │
│ ┌─────────────────────────────────────────────────────┐│
│ │Date      │Description        │Debit    │Credit     ││
│ ├─────────────────────────────────────────────────────┤│
│ │Nov 1     │Sale S-001         │₱50,000  │           ││
│ │Nov 1     │Down Payment       │         │₱10,000    ││
│ │Nov 15    │Installment        │         │₱5,000     ││
│ │Nov 18    │Sale S-015         │₱100,000 │           ││
│ │Nov 18    │Down Payment       │         │₱20,000    ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ Active Loans                                            │
│ Loan #1: Balance ₱35,000 | Next Due: Nov 30            │
│ Loan #2: Balance ₱35,000 | Next Due: Dec 18            │
└─────────────────────────────────────────────────────────┘
```

## Data Models

### Report Data Structures

#### Sales Report Data

```php
[
    'metrics' => [
        'total_sales' => 150000.00,
        'total_transactions' => 45,
        'average_value' => 3333.33,
        'cash_sales' => 80000.00,
        'loan_sales' => 70000.00,
        'cash_count' => 25,
        'loan_count' => 20,
    ],
    'sales' => Collection, // Paginated sales records
    'filters' => [
        'date_from' => '2025-11-01',
        'date_to' => '2025-11-18',
        'customer_id' => null,
        'product_id' => null,
    ],
]
```

#### Inventory Report Data

```php
[
    'metrics' => [
        'total_products' => 150,
        'total_value' => 2500000.00,
        'low_stock_count' => 12,
        'critical_stock_count' => 3,
        'out_of_stock_count' => 2,
        'negative_stock_count' => 1,
    ],
    'products' => Collection, // Product records with stock
    'filters' => [
        'status' => 'all', // all, low, critical, negative, out
        'category' => null,
    ],
]
```

#### Collection Report Data

```php
[
    'aging' => [
        'current' => ['amount' => 500000.00, 'count' => 25],
        'overdue_1_30' => ['amount' => 150000.00, 'count' => 8],
        'overdue_31_60' => ['amount' => 75000.00, 'count' => 4],
        'overdue_60_plus' => ['amount' => 25000.00, 'count' => 2],
        'total_receivable' => 750000.00,
    ],
    'loans' => Collection, // Active loans
    'payments' => Collection, // Payment history
    'metrics' => [
        'total_collected' => 500000.00,
        'cash_collected' => 300000.00,
        'online_collected' => 200000.00,
    ],
    'filters' => [
        'date_from' => '2025-11-01',
        'date_to' => '2025-11-18',
        'customer_id' => null,
        'status' => 'all',
    ],
]
```

## Error Handling

### Validation Rules

```php
// Sales Report
'date_from' => 'required|date',
'date_to' => 'required|date|after_or_equal:date_from',
'customer_id' => 'nullable|exists:customers,id',
'product_id' => 'nullable|exists:products,id',

// Inventory Report
'status' => 'nullable|in:all,low,critical,negative,out',
'category' => 'nullable|string',

// Collection Report
'date_from' => 'required|date',
'date_to' => 'required|date|after_or_equal:date_from',
'customer_id' => 'nullable|exists:customers,id',
'status' => 'nullable|in:all,active,overdue,completed',

// Customer Statement
'customer_id' => 'required|exists:customers,id',
```

### Error Messages

```php
'date_from.required' => 'Start date is required',
'date_to.after_or_equal' => 'End date must be after or equal to start date',
'customer_id.exists' => 'Selected customer does not exist',
'product_id.exists' => 'Selected product does not exist',
```

## Testing Strategy

### Unit Tests

1. **ReportService Tests**
   - Test sales metrics calculation
   - Test inventory metrics calculation
   - Test aging analysis calculation
   - Test caching functionality

2. **Export Tests**
   - Test Excel export generation
   - Test PDF export generation
   - Test export data accuracy

### Integration Tests

1. **ReportsController Tests**
   - Test report page loads
   - Test filter application
   - Test export downloads
   - Test permission checks

2. **Query Performance Tests**
   - Test report generation time
   - Test pagination performance
   - Test large dataset handling

### Feature Tests

1. **Report Workflow Tests**
   - Test complete sales report generation
   - Test complete inventory report generation
   - Test complete collection report generation
   - Test customer statement generation

2. **Export Workflow Tests**
   - Test Excel export workflow
   - Test PDF export workflow
   - Test filename generation

## Performance Considerations

### Query Optimization

```php
// Use eager loading to prevent N+1 queries
$sales = Sale::with(['customer:id,full_name', 'user:id,name', 'loan'])
    ->whereBetween('created_at', [$dateFrom, $dateTo])
    ->get();

// Use select to limit columns
$products = Product::select('id', 'name', 'sku', 'stock', 'price')
    ->get();

// Use indexes on frequently queried columns
// - sales.created_at
// - sales.customer_id
// - loans.status
// - loans.next_due_date
// - payments.payment_date
```

### Caching Strategy

```php
// Cache report data for 5 minutes
$cacheKey = "sales_report_{$dateFrom}_{$dateTo}_{$customerId}_{$productId}";
$data = Cache::remember($cacheKey, 300, function() {
    return $this->generateSalesReport();
});

// Clear cache on data changes
// - When new sale is created
// - When payment is recorded
// - When stock is adjusted
```

### Pagination

```php
// Paginate large result sets
$sales = Sale::whereBetween('created_at', [$dateFrom, $dateTo])
    ->paginate(50);

// Use cursor pagination for better performance
$sales = Sale::whereBetween('created_at', [$dateFrom, $dateTo])
    ->cursorPaginate(50);
```

## Security Considerations

### Permission Checks

```php
// In routes/web.php
Route::middleware(['auth', 'can:view-reports'])->group(function () {
    Route::get('/reports', [ReportsController::class, 'index']);
    Route::get('/reports/sales', [ReportsController::class, 'sales']);
    // ... other report routes
});

// Additional permission for customer statements
Route::get('/reports/customer-statement', [ReportsController::class, 'customerStatement'])
    ->middleware(['auth', 'can:view-customers']);
```

### Data Access Control

```php
// Ensure users can only access data they have permission for
if (!auth()->user()->can('view-reports')) {
    abort(403, 'Unauthorized access to reports');
}

// Log report access
\Log::info('Report accessed', [
    'user_id' => auth()->id(),
    'report_type' => 'sales',
    'filters' => $request->all(),
]);
```

## Integration Points

### Integration with Existing Modules

#### Sales Module
- Read sales data for sales reports
- Read payment data for collection reports
- Use existing SaleController::reconciliation() logic

#### Inventory Module
- Read product stock levels
- Read stock movements
- Use existing low stock thresholds from settings

#### Loan Module
- Read loan data for collection reports
- Read payment data for payment history
- Use existing aging analysis logic

#### Customer Module
- Read customer data for statements
- Read customer purchase history
- Read customer payment history

#### Settings Module
- Read company information for report headers
- Read currency and date format settings
- Read stock thresholds for inventory reports

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── ReportsController.php
├── Services/
│   └── ReportService.php
└── Exports/
    ├── SalesReportExport.php
    ├── InventoryReportExport.php
    └── CollectionReportExport.php

resources/
└── views/
    └── reports/
        ├── index.blade.php
        ├── sales.blade.php
        ├── inventory.blade.php
        ├── stock-movements.blade.php
        ├── collections.blade.php
        ├── customer-statement.blade.php
        ├── customer-statement-pdf.blade.php
        ├── reconciliation.blade.php
        ├── sales-trends.blade.php
        ├── top-products.blade.php
        └── top-customers.blade.php

routes/
└── web.php (add reports routes)

tests/
├── Unit/
│   └── ReportServiceTest.php
└── Feature/
    └── ReportsControllerTest.php
```

## Implementation Notes

### Phase 1: Core Reports
1. Create ReportsController
2. Create ReportService
3. Implement sales report
4. Implement inventory report
5. Implement collection report

### Phase 2: Exports
1. Create Excel export classes
2. Implement PDF generation
3. Add export buttons to reports

### Phase 3: Advanced Features
1. Implement customer statements
2. Implement sales trends
3. Implement top products/customers
4. Add charts and visualizations

### Phase 4: Polish
1. Optimize queries
2. Add caching
3. Improve UI/UX
4. Write tests

## Charts and Visualizations

### Sales Trend Chart (Chart.js)

```javascript
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Nov 1', 'Nov 2', 'Nov 3', ...],
        datasets: [{
            label: 'Cash Sales',
            data: [5000, 7000, 6500, ...],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
        }, {
            label: 'Loan Sales',
            data: [15000, 12000, 18000, ...],
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Sales Trend' }
        }
    }
});
```

### Aging Analysis Chart (Chart.js)

```javascript
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Current', '1-30 Days', '31-60 Days', '60+ Days'],
        datasets: [{
            label: 'Outstanding Balance',
            data: [500000, 150000, 75000, 25000],
            backgroundColor: [
                '#10B981', // Green
                '#F59E0B', // Amber
                '#EF4444', // Red
                '#7F1D1D', // Dark Red
            ],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'AR Aging Analysis' }
        }
    }
});
```

## Future Enhancements

### Potential Future Features (Not in Current Scope)

1. **Scheduled Reports**: Email reports automatically on schedule
2. **Report Templates**: Save custom report configurations
3. **Dashboard Widgets**: Add report widgets to main dashboard
4. **Comparative Analysis**: Compare periods (this month vs last month)
5. **Forecasting**: Predict future sales based on trends
6. **Custom Reports**: User-defined report builder
7. **Report Sharing**: Share reports with external stakeholders
8. **Mobile App**: Native mobile app for viewing reports
9. **Real-time Updates**: Live report updates using WebSockets
10. **Advanced Filters**: More granular filtering options

