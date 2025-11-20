<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Get date range
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::today();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::today();
        
        // Build query
        $query = Sale::with(['customer', 'user', 'loan'])
            ->when($request->sale_type, function($q) use ($request) {
                return $q->where('sale_type', $request->sale_type);
            })
            ->when($request->status, function($q) use ($request) {
                if ($request->status === 'paid') {
                    return $q->where('sale_type', 'cash')
                            ->orWhereHas('loan', fn($q) => $q->where('status', 'completed'));
                }
                return $q->whereHas('loan', fn($q) => $q->where('status', $request->status));
            })
            ->when($request->search, function($q) use ($request) {
                return $q->where('sale_number', 'like', "%{$request->search}%")
                    ->orWhereHas('customer', fn($q) => 
                        $q->where('full_name', 'like', "%{$request->search}%")
                         ->orWhere('contact', 'like', "%{$request->search}%")
                    )
                    ->orWhereHas('user', fn($q) => 
                        $q->where('name', 'like', "%{$request->search}%")
                    );
            });

        // Get statistics - Revenue (Total Sales Value)
        $todaySales = (clone $query)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $weekSales = (clone $query)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_amount');

        $monthSales = (clone $query)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $totalSales = (clone $query)->sum('total_amount');
        
        // Calculate Collections (Actual Money Received)
        $todayCollections = $this->calculateCollections(Carbon::today(), Carbon::today());
        $weekCollections = $this->calculateCollections(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $monthCollections = $this->calculateCollections(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        
        // Calculate Outstanding (Money Still Owed)
        $totalOutstanding = \App\Models\Loan::whereIn('status', ['active', 'overdue'])->sum('balance');

        // Get paginated results
        $sales = $query->latest()->paginate(15)->withQueryString();

        return view('sales.index', [
            'pageTitle' => 'Sales',
            'sales' => $sales,
            'todaySales' => $todaySales,
            'weekSales' => $weekSales,
            'monthSales' => $monthSales,
            'totalSales' => $totalSales,
            'todayCollections' => $todayCollections,
            'weekCollections' => $weekCollections,
            'monthCollections' => $monthCollections,
            'totalOutstanding' => $totalOutstanding,
        ]);
    }
    
    private function calculateCollections($dateFrom, $dateTo)
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
        $loanPayments = \App\Models\Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
            ->sum('amount_paid');
        
        return $cashSales + $downPayments + $loanPayments;
    }



    public function create()
    {
        $customers = Customer::orderBy('full_name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('sales.create', [
            'pageTitle' => 'Create Sale',
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required','exists:customers,id'],
            'sale_type' => ['required','in:cash,loan'],
            'payment_mode' => ['required','in:cash,online'],
            'amount_tendered' => ['nullable','numeric','min:0'],
            'discount_total' => ['nullable','numeric','min:0'],
            'discount_reason' => ['nullable','string','max:255'],
            'reference_number' => ['nullable','string','max:255'],
            'payment_bank' => ['nullable','string','max:255'],
            'payment_timestamp' => ['nullable','date'],
            'proof_image' => ['nullable','image','max:2048'],

            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','exists:products,id'],
            'items.*.quantity' => ['required','integer','min:1'],
            'items.*.unit_price' => ['required','numeric','min:0'],

            // Loan fields (optional; only when sale_type=loan)
            'down_payment' => ['nullable','numeric','min:0'],
            'term_months' => ['nullable','integer','min:1'],
            'interest_rate' => ['nullable','numeric','min:0'],
            
            // ID Verification fields (required for loan sales)
            'id_type' => ['nullable','string','max:100'],
            'id_number' => ['nullable','string','max:100'],
            'id_image' => ['nullable','image','mimes:jpeg,png,jpg','max:5120'],
        ]);
        
        // Validate ID fields are required for loan sales
        if ($validated['sale_type'] === 'loan') {
            if (empty($validated['id_type']) || empty($validated['id_number']) || !$request->hasFile('id_image')) {
                return back()->withErrors([
                    'id_type' => 'ID verification is required for loan sales.',
                    'id_number' => 'ID number is required for loan sales.',
                    'id_image' => 'ID image is required for loan sales.',
                ])->withInput();
            }
        }

        // Online banking requires reference number
        if ($validated['payment_mode'] === 'online' && empty($validated['reference_number'])) {
            return back()->withErrors(['reference_number' => 'Reference number is required for online payments.'])->withInput();
        }

        $discountTotal = (float)($validated['discount_total'] ?? 0);

        // Compute totals
        $grossTotal = 0;
        foreach ($validated['items'] as $item) {
            $grossTotal += ((int)$item['quantity']) * ((float)$item['unit_price']);
        }
        
        // No tax calculation - removed
        $taxAmount = 0;
        
        $totalAmount = max(0, $grossTotal - $discountTotal);

        // Validate cash payment: amount tendered must be >= total for cash sales
        if ($validated['sale_type'] === 'cash' && $validated['payment_mode'] === 'cash') {
            $amountTendered = (float)($validated['amount_tendered'] ?? 0);
            if ($amountTendered < $totalAmount) {
                return back()->withErrors([
                    'amount_tendered' => sprintf('Amount tendered (₱%.2f) is insufficient. Total amount is ₱%.2f', $amountTendered, $totalAmount)
                ])->withInput();
            }
        }

        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $imagePath = $request->file('proof_image')->store('sales', 'public');
        }

        $negativeStockOccurred = false;

        DB::beginTransaction();
        try {
            // Generate sale number: S-YYYYMMDD-####
            $datePart = now()->format('Ymd');
            $countToday = Sale::whereDate('created_at', today())->count() + 1;
            $saleNumber = sprintf('S-%s-%04d', $datePart, $countToday);

            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'customer_id' => $validated['customer_id'],
                'user_id' => $request->user()->id,
                'sale_type' => $validated['sale_type'],
                'total_amount' => $totalAmount,
                'payment_mode' => $validated['payment_mode'],
                'amount_tendered' => $validated['amount_tendered'] ?? null,
                'amount_paid' => $validated['payment_mode'] === 'cash' ? ($validated['amount_tendered'] ?? null) : null,
                'reference_number' => $validated['reference_number'] ?? null,
                'payment_bank' => $validated['payment_bank'] ?? null,
                'payment_timestamp' => $validated['payment_timestamp'] ?? null,
                'proof_image_path' => $imagePath,
                'discount_total' => $discountTotal ?: null,
                'discount_reason' => $validated['discount_reason'] ?? null,
            ]);

            // Create items and update stock
            foreach ($validated['items'] as $item) {
                $subtotal = ((int)$item['quantity']) * ((float)$item['unit_price']);
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                    'discount' => 0,
                ]);

                // Decrement stock
                $product = Product::lockForUpdate()->find($item['product_id']);
                $newStock = ($product->stock ?? 0) - (int)$item['quantity'];
                
                // Check if negative stock is allowed
                if ($newStock < 0 && !setting('modules.sales.allow_negative_stock', false)) {
                    throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock}, Required: {$item['quantity']}");
                }
                
                $product->stock = $newStock;
                if ($product->stock < 0) {
                    $negativeStockOccurred = true;
                }
                $product->save();

                // Log stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity_change' => -abs((int)$item['quantity']),
                    'reference_type' => 'sale_items',
                    'reference_id' => $saleItem->id,
                    'remarks' => 'Sale '.$sale->sale_number,
                    'performed_by' => $request->user()->id,
                ]);
            }

            // Optional: create loan if sale_type is loan
            if ($validated['sale_type'] === 'loan') {
                $loanAmount = $totalAmount;
                $downPayment = (float)($validated['down_payment'] ?? 0);
                $principal = max(0, $loanAmount - $downPayment);
                $termMonths = (int)($validated['term_months'] ?? setting('loan.max_term_months', 36));
                $interestRate = (float)($validated['interest_rate'] ?? setting('loan.default_interest_rate', 2));

                $monthlyRate = $interestRate > 0 ? ($interestRate / 100 / 12) : 0;
                if ($monthlyRate > 0 && $termMonths > 0) {
                    // Amortized monthly payment formula
                    $monthlyAmount = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
                } else {
                    $monthlyAmount = $termMonths > 0 ? ($principal / $termMonths) : $principal;
                }

                // Handle ID image upload
                $idImagePath = null;
                if ($request->hasFile('id_image')) {
                    $idImagePath = $request->file('id_image')->store('loan_ids', 'public');
                }

                Loan::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $validated['customer_id'],
                    'loan_amount' => $loanAmount,
                    'down_payment' => $downPayment,
                    'term_months' => $termMonths ?: 1,
                    'interest_rate' => $interestRate,
                    'monthly_amount' => $monthlyAmount,
                    'balance' => $principal,
                    'start_date' => now(),
                    'next_due_date' => now()->addMonth(),
                    'status' => 'active',
                    'id_type' => $validated['id_type'] ?? null,
                    'id_number' => $validated['id_number'] ?? null,
                    'id_image_path' => $idImagePath,
                    'id_verified_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // Clean up uploaded file on failure
            if ($imagePath) {
                Storage::delete($imagePath);
            }
            report($e);
            return back()->withErrors(['error' => 'Failed to create sale.'])->withInput();
        }

        $redirect = redirect()->route('sales.show', $sale->id)->with('success', 'Sale created successfully.');
        if ($negativeStockOccurred) {
            $redirect->with('warning', 'One or more items now have negative stock.');
        }
        return $redirect;
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product', 'loan']);
        return view('sales.show', [
            'pageTitle' => 'Sale '.$sale->sale_number,
            'sale' => $sale,
        ]);
    }

    public function print(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product', 'loan']);
        return view('sales.print', compact('sale'));
    }

    public function reconciliation(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : today();
        
        // Cash Sales (full payment received)
        $cashSales = Sale::where('sale_type', 'cash')
            ->where('payment_mode', 'cash')
            ->whereDate('created_at', $date)
            ->with(['customer:id,full_name'])
            ->orderBy('created_at')
            ->get();
        
        // Online Sales (full payment received)
        $onlineSales = Sale::where('sale_type', 'cash')
            ->where('payment_mode', 'online')
            ->whereDate('created_at', $date)
            ->with(['customer:id,full_name'])
            ->orderBy('created_at')
            ->get();
        
        // Loan Sales (only down payment received)
        $loanSales = Sale::where('sale_type', 'loan')
            ->whereDate('created_at', $date)
            ->with(['customer:id,full_name', 'loan'])
            ->orderBy('created_at')
            ->get();
        
        // Loan Payments (installment payments received today)
        $loanPayments = \App\Models\Payment::whereDate('payment_date', $date)
            ->with(['loan.sale.customer:id,full_name'])
            ->orderBy('payment_date')
            ->get();
        
        // Calculate totals
        $totalCashSales = $cashSales->sum('total_amount');
        $totalOnlineSales = $onlineSales->sum('total_amount');
        $totalDownPayments = $loanSales->sum(function($sale) {
            return $sale->loan ? $sale->loan->down_payment : 0;
        });
        $totalLoanPayments = $loanPayments->sum('amount_paid');
        
        // Separate loan payments by mode
        $cashLoanPayments = $loanPayments->where('mode_of_payment', 'Cash')->sum('amount_paid');
        $onlineLoanPayments = $loanPayments->where('mode_of_payment', '!=', 'Cash')->sum('amount_paid');
        
        // Total cash received (cash sales + cash down payments + cash loan payments)
        $totalCashReceived = $totalCashSales + 
            $loanSales->where('payment_mode', 'cash')->sum(function($sale) {
                return $sale->loan ? $sale->loan->down_payment : 0;
            }) + 
            $cashLoanPayments;
        
        // Total online received (online sales + online down payments + online loan payments)
        $totalOnlineReceived = $totalOnlineSales + 
            $loanSales->where('payment_mode', 'online')->sum(function($sale) {
                return $sale->loan ? $sale->loan->down_payment : 0;
            }) + 
            $onlineLoanPayments;
        
        // Total sales value (for revenue reporting)
        $totalSalesValue = $cashSales->sum('total_amount') + 
                          $onlineSales->sum('total_amount') + 
                          $loanSales->sum('total_amount');
        
        return view('sales.reconciliation', [
            'cashSales' => $cashSales,
            'onlineSales' => $onlineSales,
            'loanSales' => $loanSales,
            'loanPayments' => $loanPayments,
            'totalCashSales' => $totalCashSales,
            'totalOnlineSales' => $totalOnlineSales,
            'totalDownPayments' => $totalDownPayments,
            'totalLoanPayments' => $totalLoanPayments,
            'totalCashReceived' => $totalCashReceived,
            'totalOnlineReceived' => $totalOnlineReceived,
            'totalSalesValue' => $totalSalesValue,
            'date' => $date,
        ]);
    }

    public function checkReference($reference)
    {
        $existing = Sale::where('reference_number', $reference)
            ->where('payment_mode', 'online')
            ->with('customer:id,full_name')
            ->first();
        
        if ($existing) {
            return response()->json([
                'exists' => true,
                'sale' => [
                    'sale_number' => $existing->sale_number,
                    'customer' => $existing->customer->full_name,
                    'amount' => number_format($existing->total_amount, 2),
                    'date' => $existing->created_at->format('M d, Y'),
                ]
            ]);
        }
        
        return response()->json(['exists' => false]);
    }
}