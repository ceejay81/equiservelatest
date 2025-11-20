<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Basic counts
        $customersCount = Customer::query()->count();
        $productsCount = Product::query()->count();
        $inventoryAlertsCount = Product::query()->where('stock', '<=', 5)->count();
        $urgentCount = \App\Models\Notification::urgent()->unactioned()->count();

        // Sales metrics
        $salesTodaySum = (float) Sale::query()
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        
        $salesThisWeek = (float) Sale::query()
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_amount');
        
        $salesThisMonth = (float) Sale::query()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');
        
        $salesLastMonth = (float) Sale::query()
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total_amount');
        
        // Calculate growth percentage
        $salesGrowth = $salesLastMonth > 0 
            ? (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100 
            : 0;

        // Recent sales for chart (last 7 days)
        $salesChartData = Sale::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->whereBetween('created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with 0
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D');
            $sale = $salesChartData->firstWhere('date', $date->format('Y-m-d'));
            $chartValues[] = $sale ? (float) $sale->total : 0;
        }

        // Accounts Receivable metrics
        $totalReceivables = (float) Loan::query()
            ->whereIn('status', ['active', 'overdue'])
            ->sum('balance');
        
        $overdueLoans = Loan::query()
            ->where('status', 'overdue')
            ->count();

        // Recent activity (last 5 sales)
        $recentSales = Sale::query()
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Top products (best sellers this month)
        try {
            $topProducts = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
                ->whereMonth('sales.created_at', Carbon::now()->month)
                ->whereYear('sales.created_at', Carbon::now()->year)
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $topProducts = collect();
        }

        return view('dashboard', [
            'customersCount' => $customersCount,
            'productsCount' => $productsCount,
            'salesTodaySum' => $salesTodaySum,
            'salesThisWeek' => $salesThisWeek,
            'salesThisMonth' => $salesThisMonth,
            'salesGrowth' => $salesGrowth,
            'inventoryAlertsCount' => $inventoryAlertsCount,
            'urgentCount' => $urgentCount,
            'totalReceivables' => $totalReceivables,
            'overdueLoans' => $overdueLoans,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
        ]);
    }
}
