<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    protected $reportService;
    
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    
    /**
     * Reports Dashboard
     */
    public function index()
    {
        return view('reports.index', [
            'pageTitle' => 'Reports & Analytics',
        ]);
    }
    
    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'customer_id' => 'nullable|exists:customers,id',
            'product_id' => 'nullable|exists:products,id',
        ]);
        
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::today()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();
        
        $filters = [
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
        ];
        
        $metrics = $this->reportService->calculateSalesMetrics($dateFrom, $dateTo, $filters);
        $sales = $this->reportService->getSalesByDateRange($dateFrom, $dateTo, $filters);
        
        $customers = Customer::orderBy('full_name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('reports.sales', [
            'pageTitle' => 'Sales Report',
            'metrics' => $metrics,
            'sales' => $sales,
            'customers' => $customers,
            'products' => $products,
            'filters' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
            ],
        ]);
    }
    
    /**
     * Export Sales Report to Excel
     */
    public function salesExport(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::today()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();
        
        $filters = [
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
        ];
        
        $metrics = $this->reportService->calculateSalesMetrics($dateFrom, $dateTo, $filters);
        $sales = \App\Models\Sale::with(['customer:id,full_name', 'user:id,name', 'loan'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if (!empty($filters['customer_id'])) {
            $sales->where('customer_id', $filters['customer_id']);
        }
        
        if (!empty($filters['product_id'])) {
            $sales->whereHas('items', function($q) use ($filters) {
                $q->where('product_id', $filters['product_id']);
            });
        }
        
        $sales = $sales->latest()->get();
        
        $filename = 'sales_report_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Summary
        fputcsv($handle, ['Sales Report']);
        fputcsv($handle, ['Period', $dateFrom->format('M d, Y') . ' to ' . $dateTo->format('M d, Y')]);
        fputcsv($handle, ['Generated', now()->format('M d, Y h:i A')]);
        fputcsv($handle, []);
        fputcsv($handle, ['Summary']);
        fputcsv($handle, ['Total Sales', number_format($metrics['total_sales'], 2)]);
        fputcsv($handle, ['Total Transactions', $metrics['total_transactions']]);
        fputcsv($handle, ['Average Value', number_format($metrics['average_value'], 2)]);
        fputcsv($handle, ['Cash Sales', number_format($metrics['cash_sales'], 2)]);
        fputcsv($handle, ['Loan Sales', number_format($metrics['loan_sales'], 2)]);
        fputcsv($handle, []);
        
        // Headers
        fputcsv($handle, [
            'Date',
            'Sale Number',
            'Customer',
            'Sale Type',
            'Payment Mode',
            'Total Amount',
            'Status',
            'Processed By',
        ]);
        
        // Data
        foreach ($sales as $sale) {
            fputcsv($handle, [
                $sale->created_at->format('M d, Y'),
                $sale->sale_number,
                $sale->customer->full_name ?? 'N/A',
                ucfirst($sale->sale_type),
                ucfirst($sale->payment_mode),
                number_format($sale->total_amount, 2),
                $sale->sale_type === 'cash' ? 'Paid' : ($sale->loan ? ucfirst($sale->loan->status) : 'N/A'),
                $sale->user->name ?? 'N/A',
            ]);
        }
        
        fclose($handle);
        exit;
    }
    
    /**
     * Inventory Report
     */
    public function inventory(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:all,low,critical,negative,out',
            'category' => 'nullable|string',
        ]);
        
        $query = Product::query();
        
        if ($request->status === 'low') {
            $query->whereRaw('stock > 0 AND stock <= COALESCE(low_stock_threshold, 10)');
        } elseif ($request->status === 'critical') {
            $query->whereRaw('stock > 0 AND stock <= COALESCE(critical_stock_threshold, 5)');
        } elseif ($request->status === 'negative') {
            $query->where('stock', '<', 0);
        } elseif ($request->status === 'out') {
            $query->where('stock', 0);
        }
        
        if ($request->category) {
            $query->where('category', $request->category);
        }
        
        $products = $query->orderBy('name')->paginate(50);
        $metrics = $this->reportService->calculateInventoryMetrics();
        
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();
        
        return view('reports.inventory', [
            'pageTitle' => 'Inventory Report',
            'products' => $products,
            'metrics' => $metrics,
            'categories' => $categories,
            'filters' => [
                'status' => $request->status ?? 'all',
                'category' => $request->category,
            ],
        ]);
    }
    
    /**
     * Export Inventory Report to Excel
     */
    public function inventoryExport(Request $request)
    {
        $query = Product::query();
        
        if ($request->status === 'low') {
            $query->whereRaw('stock > 0 AND stock <= COALESCE(low_stock_threshold, 10)');
        } elseif ($request->status === 'critical') {
            $query->whereRaw('stock > 0 AND stock <= COALESCE(critical_stock_threshold, 5)');
        } elseif ($request->status === 'negative') {
            $query->where('stock', '<', 0);
        } elseif ($request->status === 'out') {
            $query->where('stock', 0);
        }
        
        if ($request->category) {
            $query->where('category', $request->category);
        }
        
        $products = $query->orderBy('name')->get();
        $metrics = $this->reportService->calculateInventoryMetrics();
        
        $filename = 'inventory_report_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Summary
        fputcsv($handle, ['Inventory Report']);
        fputcsv($handle, ['Generated', now()->format('M d, Y h:i A')]);
        fputcsv($handle, []);
        fputcsv($handle, ['Summary']);
        fputcsv($handle, ['Total Products', $metrics['total_products']]);
        fputcsv($handle, ['Total Value', number_format($metrics['total_value'], 2)]);
        fputcsv($handle, ['Low Stock Items', $metrics['low_stock_count']]);
        fputcsv($handle, ['Critical Stock Items', $metrics['critical_stock_count']]);
        fputcsv($handle, ['Out of Stock Items', $metrics['out_of_stock_count']]);
        fputcsv($handle, []);
        
        // Headers
        fputcsv($handle, [
            'SKU',
            'Product Name',
            'Category',
            'Brand',
            'Current Stock',
            'Low Stock Threshold',
            'Critical Stock Threshold',
            'Unit Price',
            'Stock Value',
            'Status',
        ]);
        
        // Data
        foreach ($products as $product) {
            $stockValue = $product->stock * $product->selling_price;
            
            if ($product->stock < 0) {
                $status = 'Negative Stock';
            } elseif ($product->stock == 0) {
                $status = 'Out of Stock';
            } elseif ($product->stock <= ($product->critical_stock_threshold ?? 5)) {
                $status = 'Critical';
            } elseif ($product->stock <= ($product->low_stock_threshold ?? 10)) {
                $status = 'Low Stock';
            } else {
                $status = 'In Stock';
            }
            
            fputcsv($handle, [
                $product->sku,
                $product->name,
                $product->category ?? 'N/A',
                $product->brand ?? 'N/A',
                $product->stock,
                $product->low_stock_threshold ?? 10,
                $product->critical_stock_threshold ?? 5,
                number_format($product->selling_price, 2),
                number_format($stockValue, 2),
                $status,
            ]);
        }
        
        fclose($handle);
        exit;
    }
    
    /**
     * Stock Movements Report
     */
    public function stockMovements(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'type' => 'nullable|in:sale,adjustment,receive',
            'product_id' => 'nullable|exists:products,id',
        ]);
        
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::today()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();
        
        $filters = [
            'type' => $request->type,
            'product_id' => $request->product_id,
        ];
        
        $movements = $this->reportService->getStockMovements($dateFrom, $dateTo, $filters);
        $products = Product::orderBy('name')->get();
        
        return view('reports.stock-movements', [
            'pageTitle' => 'Stock Movements',
            'movements' => $movements,
            'products' => $products,
            'filters' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'type' => $request->type,
                'product_id' => $request->product_id,
            ],
        ]);
    }
    
    /**
     * Collections Report
     */
    public function collections(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'nullable|in:all,active,overdue,completed',
        ]);
        
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::today()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();
        
        $filters = [
            'customer_id' => $request->customer_id,
        ];
        
        $metrics = $this->reportService->calculateCollectionsMetrics($dateFrom, $dateTo);
        $aging = $this->reportService->getAgingAnalysis();
        $payments = $this->reportService->getPaymentHistory($dateFrom, $dateTo, $filters);
        
        // Get loans based on status filter
        $loansQuery = \App\Models\Loan::with(['sale.customer:id,full_name']);
        
        if ($request->status && $request->status !== 'all') {
            $loansQuery->where('status', $request->status);
        } else {
            $loansQuery->whereIn('status', ['active', 'overdue']);
        }
        
        if ($request->customer_id) {
            $loansQuery->whereHas('sale', function($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }
        
        $loans = $loansQuery->latest()->paginate(50);
        
        $customers = Customer::orderBy('full_name')->get();
        
        return view('reports.collections', [
            'pageTitle' => 'Collection Report',
            'metrics' => $metrics,
            'aging' => $aging,
            'loans' => $loans,
            'payments' => $payments,
            'customers' => $customers,
            'filters' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'customer_id' => $request->customer_id,
                'status' => $request->status ?? 'all',
            ],
        ]);
    }

    
    /**
     * Export Collections Report to Excel
     */
    public function collectionsExport(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::today()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();
        
        $filters = [
            'customer_id' => $request->customer_id,
        ];
        
        $metrics = $this->reportService->calculateCollectionsMetrics($dateFrom, $dateTo);
        $aging = $this->reportService->getAgingAnalysis();
        
        $payments = \App\Models\Payment::with(['loan.sale.customer:id,full_name'])
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);
        
        if (!empty($filters['customer_id'])) {
            $payments->whereHas('loan.sale', function($q) use ($filters) {
                $q->where('customer_id', $filters['customer_id']);
            });
        }
        
        $payments = $payments->latest('payment_date')->get();
        
        $filename = 'collections_report_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Summary
        fputcsv($handle, ['Collections Report']);
        fputcsv($handle, ['Period', $dateFrom->format('M d, Y') . ' to ' . $dateTo->format('M d, Y')]);
        fputcsv($handle, ['Generated', now()->format('M d, Y h:i A')]);
        fputcsv($handle, []);
        fputcsv($handle, ['Collections Summary']);
        fputcsv($handle, ['Total Collected', number_format($metrics['total_collected'], 2)]);
        fputcsv($handle, ['Cash Collected', number_format($metrics['cash_collected'], 2)]);
        fputcsv($handle, ['Online Collected', number_format($metrics['online_collected'], 2)]);
        fputcsv($handle, []);
        fputcsv($handle, ['Aging Analysis']);
        fputcsv($handle, ['Current (Not Due)', number_format($aging['current']['amount'], 2), $aging['current']['count'] . ' loans']);
        fputcsv($handle, ['1-30 Days Overdue', number_format($aging['overdue_1_30']['amount'], 2), $aging['overdue_1_30']['count'] . ' loans']);
        fputcsv($handle, ['31-60 Days Overdue', number_format($aging['overdue_31_60']['amount'], 2), $aging['overdue_31_60']['count'] . ' loans']);
        fputcsv($handle, ['60+ Days Overdue', number_format($aging['overdue_60_plus']['amount'], 2), $aging['overdue_60_plus']['count'] . ' loans']);
        fputcsv($handle, ['Total Receivable', number_format($aging['total_receivable'], 2)]);
        fputcsv($handle, []);
        
        // Headers
        fputcsv($handle, [
            'Date',
            'Customer',
            'Loan Number',
            'Amount Paid',
            'Payment Mode',
            'Reference Number',
            'Received By',
        ]);
        
        // Data
        foreach ($payments as $payment) {
            fputcsv($handle, [
                $payment->payment_date->format('M d, Y'),
                $payment->loan->sale->customer->full_name ?? 'N/A',
                $payment->loan->sale->sale_number ?? 'N/A',
                number_format($payment->amount_paid, 2),
                $payment->mode_of_payment,
                $payment->reference_number ?? 'N/A',
                $payment->received_by ?? 'N/A',
            ]);
        }
        
        fclose($handle);
        exit;
    }
    
    /**
     * Customer Statement
     */
    public function customerStatement(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
        ]);
        
        $customers = Customer::orderBy('full_name')->get();
        
        if (!$request->customer_id) {
            return view('reports.customer-statement', [
                'pageTitle' => 'Customer Statement',
                'customers' => $customers,
                'statement' => null,
            ]);
        }
        
        $statement = $this->reportService->getCustomerStatement($request->customer_id);
        
        return view('reports.customer-statement', [
            'pageTitle' => 'Customer Statement',
            'customers' => $customers,
            'statement' => $statement,
        ]);
    }
    
    /**
     * Customer Statement PDF
     */
    public function customerStatementPdf($customerId)
    {
        $statement = $this->reportService->getCustomerStatement($customerId);
        
        return view('reports.customer-statement-pdf', [
            'statement' => $statement,
        ]);
    }
    
    /**
     * Daily Reconciliation
     */
    public function reconciliation(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
        ]);
        
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $data = $this->reportService->getDailyReconciliation($date);
        
        return view('reports.reconciliation', [
            'pageTitle' => 'Daily Reconciliation',
            'date' => $date,
            'cashSales' => $data['cash_sales'],
            'onlineSales' => $data['online_sales'],
            'loanSales' => $data['loan_sales'],
            'loanPayments' => $data['loan_payments'],
            'totalCashReceived' => $data['total_cash_received'],
            'totalOnlineReceived' => $data['total_online_received'],
            'totalCollections' => $data['total_collections'],
        ]);
    }
    
    /**
     * Top Products
     */
    public function topProducts(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::today()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::today();
        
        $topProducts = $this->reportService->getTopProducts($dateFrom, $dateTo, 10);
        
        return view('reports.top-products', [
            'pageTitle' => 'Top Products',
            'products' => $topProducts,
            'filters' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
            ],
        ]);
    }
    
}


