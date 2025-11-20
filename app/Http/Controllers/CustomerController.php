<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }

        // Simple sort options: name asc (default) or recently added
        $sort = $request->query('sort');
        if ($sort === 'recent') {
            $query->orderByDesc('created_at');
        } else {
            $query->orderBy('full_name');
        }

        // Count active loans via related sales having a loan with status=active
        $query->withCount(['sales as active_loans_count' => function ($q) {
            $q->whereHas('loan', function ($loan) {
                $loan->where('status', 'active');
            });
        }]);

        // Count overdue loans
        $query->withCount(['sales as overdue_loans_count' => function ($q) {
            $q->whereHas('loan', function ($loan) {
                $loan->where('status', 'overdue');
            });
        }]);

        // Get last purchase date
        $query->withMax('sales as last_purchase_date', 'created_at');

        $customers = $query->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function show(int $id): View
    {
        $customer = Customer::query()->findOrFail($id);
        
        // Load ALL sales with related data
        $customer->load([
            'sales' => function($query) {
                $query->with(['items.product', 'loan', 'user'])
                      ->orderByDesc('created_at');
            }
        ]);

        // All sales (cash + loan)
        $allSales = $customer->sales;
        $totalPurchases = $allSales->count();
        $totalSpent = $allSales->sum('total_amount');
        $lastPurchase = $allSales->first()?->created_at;
        $avgPurchase = $totalPurchases > 0 ? $totalSpent / $totalPurchases : 0;
        
        // Cash sales only
        $cashSales = $allSales->where('sale_type', 'cash');
        $totalCashSales = $cashSales->count();
        $totalCashAmount = $cashSales->sum('total_amount');
        
        // Loan sales
        $loans = $allSales->pluck('loan')->filter();
        $totalLoans = $loans->count();
        $activeLoans = $loans->whereIn('status', ['active', 'overdue'])->count();
        $overdueLoans = $loans->where(function($loan) {
            return in_array($loan->status, ['active', 'overdue']) 
                && $loan->next_due_date 
                && $loan->next_due_date->isPast();
        })->count();
        
        // Payment history (all payments from all loans)
        $payments = \App\Models\Payment::whereHas('loan', function($query) use ($customer) {
            $query->whereHas('sale', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            });
        })->with(['loan.sale'])->orderByDesc('payment_date')->get();

        // Combine all payments (down payments + loan payments)
        $allPayments = collect();
        
        foreach($loans as $loan) {
            if($loan->down_payment > 0) {
                $allPayments->push((object)[
                    'date' => $loan->start_date,
                    'type' => 'down_payment',
                    'amount' => $loan->down_payment,
                    'loan' => $loan,
                    'mode' => $loan->sale && $loan->sale->payment_mode ? ucfirst($loan->sale->payment_mode) : 'Cash',
                    'reference' => $loan->sale ? $loan->sale->reference_number : null,
                    'proof' => $loan->sale ? $loan->sale->proof_image_path : null,
                    'bank' => $loan->sale ? $loan->sale->payment_bank : null,
                ]);
            }
        }
        
        foreach($payments as $payment) {
            $allPayments->push((object)[
                'date' => $payment->payment_date,
                'type' => 'loan_payment',
                'amount' => $payment->amount_paid,
                'loan' => $payment->loan,
                'mode' => $payment->mode_of_payment,
                'reference' => $payment->reference_number,
                'proof' => $payment->proof_image_path,
                'bank' => $payment->payment_bank,
            ]);
        }
        
        $allPayments = $allPayments->sortByDesc('date');

        // Rebates
        $rebates = $customer->rebates()
            ->with('product', 'sale', 'appliedToLoan')
            ->orderByDesc('created_at')
            ->get();
        $totalRebatesAmount = $rebates->sum('rebate_amount');
        
        // Determine customer status
        $customerStatus = 'good_standing';
        if ($overdueLoans > 0) {
            $customerStatus = 'overdue';
        } elseif ($activeLoans > 0) {
            $customerStatus = 'active';
        }

        return view('customers.show', compact(
            'customer',
            'allSales',
            'totalPurchases',
            'totalSpent',
            'lastPurchase',
            'avgPurchase',
            'cashSales',
            'totalCashSales',
            'totalCashAmount',
            'loans',
            'totalLoans',
            'activeLoans',
            'overdueLoans',
            'payments',
            'allPayments',
            'rebates',
            'totalRebatesAmount',
            'customerStatus'
        ));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_number' => ['required', 'string', 'max:50', 'unique:customers,account_number'],
            'full_name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::create($validated);
        return redirect('/customers')->with('status', 'Customer created');
    }

    public function edit(int $id): View
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'account_number' => ['required', 'string', 'max:50', Rule::unique('customers', 'account_number')->ignore($customer->id)],
            'full_name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect('/customers')
                ->withErrors($validator)
                ->withInput($request->all() + ['mode' => 'edit', 'edit_id' => $customer->id]);
        }

        $customer->update($validator->validated());
        return redirect('/customers')->with('status', 'Customer updated');
    }

    public function destroy(int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect('/customers')->with('status', 'Customer deleted');
    }

    public function storeRebate(Request $request, int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);
        
        $validated = $request->validate([
            'rebate_amount' => ['required', 'numeric', 'min:0.01'],
            'sale_id' => ['nullable', 'exists:sales,id'],
            'product_note' => ['nullable', 'string', 'max:255'],
        ]);

        // If no sale_id provided, we need to create a rebate without a product
        // For now, we'll require a sale_id or handle it differently
        if (empty($validated['sale_id'])) {
            // Create a general rebate - we need to handle this case
            // For now, return error
            return back()->withErrors(['sale_id' => 'Please select a related sale or we will implement general rewards later.'])->withInput();
        }

        // Get the first product from the sale to link the rebate
        $sale = \App\Models\Sale::with('items.product')->findOrFail($validated['sale_id']);
        $firstProduct = $sale->items->first()?->product;

        if (!$firstProduct) {
            return back()->withErrors(['sale_id' => 'Selected sale has no products.'])->withInput();
        }

        \App\Models\Rebate::create([
            'sale_id' => $validated['sale_id'],
            'product_id' => $firstProduct->id,
            'rebate_amount' => $validated['rebate_amount'],
        ]);

        return redirect("/customers/{$id}")->with('status', 'Reward awarded successfully!');
    }

    public function applyRebateToLoan(Request $request, $customerId)
    {
        $validated = $request->validate([
            'rebate_id' => ['required', 'exists:rebates,id'],
            'loan_id' => ['required', 'exists:loans,id'],
        ]);

        $rebate = \App\Models\Rebate::findOrFail($validated['rebate_id']);
        $loan = \App\Models\Loan::findOrFail($validated['loan_id']);

        // Verify rebate belongs to this customer
        if ($rebate->sale->customer_id != $customerId) {
            return back()->withErrors(['error' => 'This rebate does not belong to this customer.']);
        }

        // Verify loan belongs to this customer
        if ($loan->sale->customer_id != $customerId) {
            return back()->withErrors(['error' => 'This loan does not belong to this customer.']);
        }

        // Check if rebate is available
        if (!$rebate->isAvailable()) {
            return back()->withErrors(['error' => 'This rebate has already been used.']);
        }

        // Check if loan is active
        if (!in_array($loan->status, ['active', 'overdue'])) {
            return back()->withErrors(['error' => 'This loan is not active.']);
        }

        DB::beginTransaction();
        try {
            // Apply rebate amount to loan balance
            $rebateAmount = $rebate->rebate_amount;
            $newBalance = max(0, $loan->balance - $rebateAmount);
            $actualApplied = $loan->balance - $newBalance;

            $loan->balance = $newBalance;
            
            // Update loan status if fully paid
            if ($newBalance == 0) {
                $loan->status = 'completed';
                $loan->next_due_date = null;
            }
            
            $loan->save();

            // Mark rebate as used
            $rebate->markAsUsed('loan_payment', $loan->id);

            // Create a payment record for tracking
            \App\Models\Payment::create([
                'loan_id' => $loan->id,
                'amount_paid' => $actualApplied,
                'payment_date' => now(),
                'mode_of_payment' => 'Rebate Credit',
                'reference_number' => 'REBATE-' . $rebate->id,
            ]);

            DB::commit();

            return redirect("/customers/{$customerId}")
                ->with('success', "Rebate of ₱" . number_format($actualApplied, 2) . " applied to loan successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to apply rebate: ' . $e->getMessage()]);
        }
    }
}
