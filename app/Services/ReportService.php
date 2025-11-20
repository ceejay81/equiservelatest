<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Calculate sales metrics for a given date range
     */
    public function calculateSalesMetrics($dateFrom, $dateTo, $filters = [])
    {
        $query = Sale::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }
        
        if (!empty($filters['product_id'])) {
            $query->whereHas('items', function($q) use ($filters) {
                $q->where('product_id', $filters['product_id']);
            });
        }
        
        $sales = $query->get();
        
        return [
            'total_sales' => $sales->sum('total_amount'),
            'total_transactions' => $sales->count(),
            'average_value' => $sales->count() > 0 ? $sales->sum('total_amount') / $sales->count() : 0,
            'cash_sales' => $sales->where('sale_type', 'cash')->sum('total_amount'),
            'loan_sales' => $sales->where('sale_type', 'loan')->sum('total_amount'),
            'cash_count' => $sales->where('sale_type', 'cash')->count(),
            'loan_count' => $sales->where('sale_type', 'loan')->count(),
            'cash_mode_sales' => $sales->where('payment_mode', 'cash')->sum('total_amount'),
            'online_mode_sales' => $sales->where('payment_mode', 'online')->sum('total_amount'),
        ];
    }
    
    /**
     * Get sales by date range with filters
     */
    public function getSalesByDateRange($dateFrom, $dateTo, $filters = [])
    {
        $query = Sale::with(['customer:id,full_name', 'user:id,name', 'loan'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }
        
        if (!empty($filters['product_id'])) {
            $query->whereHas('items', function($q) use ($filters) {
                $q->where('product_id', $filters['product_id']);
            });
        }
        
        return $query->latest()->paginate(50);
    }
    
    /**
     * Calculate inventory metrics
     */
    public function calculateInventoryMetrics()
    {
        $products = Product::all();
        
        return [
            'total_products' => $products->count(),
            'total_value' => $products->sum(function($p) {
                return $p->stock * $p->selling_price;
            }),
            'low_stock_count' => $products->filter(function($p) {
                return $p->stock > 0 && $p->stock <= ($p->low_stock_threshold ?? 10);
            })->count(),
            'critical_stock_count' => $products->filter(function($p) {
                return $p->stock > 0 && $p->stock <= ($p->critical_stock_threshold ?? 5);
            })->count(),
            'out_of_stock_count' => $products->where('stock', 0)->count(),
            'negative_stock_count' => $products->where('stock', '<', 0)->count(),
        ];
    }
    
    /**
     * Get stock movements with filters
     */
    public function getStockMovements($dateFrom, $dateTo, $filters = [])
    {
        $query = StockMovement::with(['product:id,name,sku', 'user:id,name'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        
        return $query->latest()->paginate(50);
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts()
    {
        return Product::where(function($q) {
            $q->whereRaw('stock <= COALESCE(low_stock_threshold, 10)')
              ->where('stock', '>', 0);
        })->orderBy('stock')->get();
    }
    
    /**
     * Calculate collections metrics
     */
    public function calculateCollectionsMetrics($dateFrom, $dateTo)
    {
        // Cash sales (full amount)
        $cashSales = Sale::where('sale_type', 'cash')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount');
        
        // Down payments from loan sales
        $downPayments = Sale::where('sale_type', 'loan')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('loan')
            ->get()
            ->sum(function($sale) {
                return $sale->loan ? $sale->loan->down_payment : 0;
            });
        
        // Loan installment payments
        $loanPayments = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount_paid');
        
        // Separate by payment mode
        $cashModeTotal = Sale::where('sale_type', 'cash')
            ->where('payment_mode', 'cash')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount');
        
        $cashDownPayments = Sale::where('sale_type', 'loan')
            ->where('payment_mode', 'cash')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('loan')
            ->get()
            ->sum(function($sale) {
                return $sale->loan ? $sale->loan->down_payment : 0;
            });
        
        $cashLoanPayments = Payment::where('mode_of_payment', 'Cash')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount_paid');
        
        $onlineModeTotal = Sale::where('sale_type', 'cash')
            ->where('payment_mode', 'online')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount');
        
        $onlineDownPayments = Sale::where('sale_type', 'loan')
            ->where('payment_mode', 'online')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('loan')
            ->get()
            ->sum(function($sale) {
                return $sale->loan ? $sale->loan->down_payment : 0;
            });
        
        $onlineLoanPayments = Payment::where('mode_of_payment', '!=', 'Cash')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount_paid');
        
        return [
            'total_collected' => $cashSales + $downPayments + $loanPayments,
            'cash_collected' => $cashModeTotal + $cashDownPayments + $cashLoanPayments,
            'online_collected' => $onlineModeTotal + $onlineDownPayments + $onlineLoanPayments,
            'cash_sales' => $cashSales,
            'down_payments' => $downPayments,
            'loan_payments' => $loanPayments,
        ];
    }
    
    /**
     * Get aging analysis for accounts receivable
     */
    public function getAgingAnalysis()
    {
        $loans = Loan::whereIn('status', ['active', 'overdue'])->get();
        
        $current = ['amount' => 0, 'count' => 0];
        $overdue_1_30 = ['amount' => 0, 'count' => 0];
        $overdue_31_60 = ['amount' => 0, 'count' => 0];
        $overdue_60_plus = ['amount' => 0, 'count' => 0];
        
        foreach ($loans as $loan) {
            $daysOverdue = $loan->next_due_date < now() ? now()->diffInDays($loan->next_due_date) : 0;
            
            if ($daysOverdue == 0) {
                $current['amount'] += $loan->balance;
                $current['count']++;
            } elseif ($daysOverdue <= 30) {
                $overdue_1_30['amount'] += $loan->balance;
                $overdue_1_30['count']++;
            } elseif ($daysOverdue <= 60) {
                $overdue_31_60['amount'] += $loan->balance;
                $overdue_31_60['count']++;
            } else {
                $overdue_60_plus['amount'] += $loan->balance;
                $overdue_60_plus['count']++;
            }
        }
        
        return [
            'current' => $current,
            'overdue_1_30' => $overdue_1_30,
            'overdue_31_60' => $overdue_31_60,
            'overdue_60_plus' => $overdue_60_plus,
            'total_receivable' => $loans->sum('balance'),
        ];
    }
    
    /**
     * Get payment history with filters
     */
    public function getPaymentHistory($dateFrom, $dateTo, $filters = [])
    {
        $query = Payment::with(['loan.sale.customer:id,full_name'])
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);
        
        if (!empty($filters['customer_id'])) {
            $query->whereHas('loan.sale', function($q) use ($filters) {
                $q->where('customer_id', $filters['customer_id']);
            });
        }
        
        return $query->latest('payment_date')->paginate(50);
    }
    
    /**
     * Get customer statement
     */
    public function getCustomerStatement($customerId)
    {
        $customer = Customer::with(['rebates'])->findOrFail($customerId);
        
        // Get all sales
        $sales = Sale::where('customer_id', $customerId)
            ->with(['items.product', 'loan'])
            ->orderBy('created_at')
            ->get();
        
        // Get all payments
        $payments = Payment::whereHas('loan.sale', function($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })->with('loan')->orderBy('payment_date')->get();
        
        // Get active loans
        $activeLoans = Loan::whereHas('sale', function($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })->whereIn('status', ['active', 'overdue'])->get();
        
        $totalPurchases = $sales->sum('total_amount');
        $totalPaid = $sales->where('sale_type', 'cash')->sum('total_amount') +
                     $sales->where('sale_type', 'loan')->sum(function($sale) {
                         return $sale->loan ? $sale->loan->down_payment : 0;
                     }) +
                     $payments->sum('amount_paid');
        $outstanding = $activeLoans->sum('balance');
        
        return [
            'customer' => $customer,
            'sales' => $sales,
            'payments' => $payments,
            'active_loans' => $activeLoans,
            'total_purchases' => $totalPurchases,
            'total_paid' => $totalPaid,
            'outstanding' => $outstanding,
        ];
    }
    
    /**
     * Get top products by sales
     */
    public function getTopProducts($dateFrom, $dateTo, $limit = 10)
    {
        return Product::select(
                'products.id',
                'products.name',
                'products.sku',
                'products.category',
                'products.brand',
                'products.selling_price'
            )
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.category', 'products.brand', 'products.selling_price')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity')
            ->selectRaw('SUM(sale_items.subtotal) as total_revenue')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get daily reconciliation data
     */
    public function getDailyReconciliation($date)
    {
        $date = Carbon::parse($date);
        
        // Cash Sales
        $cashSales = Sale::where('sale_type', 'cash')
            ->where('payment_mode', 'cash')
            ->whereDate('created_at', $date)
            ->with(['customer:id,full_name'])
            ->get();
        
        // Online Sales
        $onlineSales = Sale::where('sale_type', 'cash')
            ->where('payment_mode', 'online')
            ->whereDate('created_at', $date)
            ->with(['customer:id,full_name'])
            ->get();
        
        // Loan Sales (down payments)
        $loanSales = Sale::where('sale_type', 'loan')
            ->whereDate('created_at', $date)
            ->with(['customer:id,full_name', 'loan'])
            ->get();
        
        // Loan Payments
        $loanPayments = Payment::whereDate('payment_date', $date)
            ->with(['loan.sale.customer:id,full_name'])
            ->get();
        
        $totalCashReceived = $cashSales->sum('total_amount') +
            $loanSales->where('payment_mode', 'cash')->sum(function($sale) {
                return $sale->loan ? $sale->loan->down_payment : 0;
            }) +
            $loanPayments->where('mode_of_payment', 'Cash')->sum('amount_paid');
        
        $totalOnlineReceived = $onlineSales->sum('total_amount') +
            $loanSales->where('payment_mode', 'online')->sum(function($sale) {
                return $sale->loan ? $sale->loan->down_payment : 0;
            }) +
            $loanPayments->where('mode_of_payment', '!=', 'Cash')->sum('amount_paid');
        
        return [
            'cash_sales' => $cashSales,
            'online_sales' => $onlineSales,
            'loan_sales' => $loanSales,
            'loan_payments' => $loanPayments,
            'total_cash_received' => $totalCashReceived,
            'total_online_received' => $totalOnlineReceived,
            'total_collections' => $totalCashReceived + $totalOnlineReceived,
        ];
    }
    
    /**
     * Generate cache key for reports
     */
    protected function cacheKey($prefix, $params)
    {
        return $prefix . '_' . md5(json_encode($params));
    }
    
    /**
     * Get cached data or execute callback
     */
    protected function getCached($key, $callback, $minutes = 5)
    {
        return Cache::remember($key, $minutes * 60, $callback);
    }
}
