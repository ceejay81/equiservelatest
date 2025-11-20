<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Customer;
use App\Services\PenaltyService;
use App\Services\AmortizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LoanController extends Controller
{
    protected $penaltyService;
    protected $amortizationService;

    public function __construct(PenaltyService $penaltyService, AmortizationService $amortizationService)
    {
        $this->penaltyService = $penaltyService;
        $this->amortizationService = $amortizationService;
    }

    public function index(Request $request)
    {
        // Build query with filters
        $query = Loan::with([
            'sale' => function($query) {
                $query->with(['customer' => function($q) {
                    $q->select('id', 'account_number', 'full_name', 'contact');
                }]);
            },
            'payments'
        ]);

        // Date range filter
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Aging filter
        if ($request->aging) {
            $now = now();
            switch ($request->aging) {
                case 'current':
                    $query->whereIn('status', ['active', 'overdue'])
                          ->where(function($q) use ($now) {
                              $q->whereNull('next_due_date')
                                ->orWhere('next_due_date', '>=', $now);
                          });
                    break;
                case '1-30':
                    $query->whereIn('status', ['active', 'overdue'])
                          ->whereBetween('next_due_date', [$now->copy()->subDays(30), $now->copy()->subDay()]);
                    break;
                case '31-60':
                    $query->whereIn('status', ['active', 'overdue'])
                          ->whereBetween('next_due_date', [$now->copy()->subDays(60), $now->copy()->subDays(31)]);
                    break;
                case '60+':
                    $query->whereIn('status', ['active', 'overdue'])
                          ->where('next_due_date', '<', $now->copy()->subDays(60));
                    break;
            }
        }

        // Search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('sale', function($sq) use ($search) {
                    $sq->where('sale_number', 'like', "%{$search}%")
                       ->orWhereHas('customer', function($cq) use ($search) {
                           $cq->where('full_name', 'like', "%{$search}%")
                              ->orWhere('contact', 'like', "%{$search}%")
                              ->orWhere('account_number', 'like', "%{$search}%");
                       });
                });
            });
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Calculate statistics
        $totalPaid = \DB::table('payments')->sum('amount_paid');
        $totalDownPayments = Loan::sum('down_payment');
        $totalReceivable = Loan::whereIn('status', ['active', 'overdue'])->sum('balance');
        $overdueCount = Loan::whereIn('status', ['active', 'overdue'])
            ->where('next_due_date', '<', now())
            ->count();
        
        // Aging analysis
        $now = now();
        $currentNotDue = Loan::whereIn('status', ['active', 'overdue'])
            ->where(function($q) use ($now) {
                $q->whereNull('next_due_date')
                  ->orWhere('next_due_date', '>=', $now);
            })
            ->sum('balance');
        
        $overdue1to30 = Loan::whereIn('status', ['active', 'overdue'])
            ->whereBetween('next_due_date', [$now->copy()->subDays(30), $now->copy()->subDay()])
            ->sum('balance');
        
        $overdue31to60 = Loan::whereIn('status', ['active', 'overdue'])
            ->whereBetween('next_due_date', [$now->copy()->subDays(60), $now->copy()->subDays(31)])
            ->sum('balance');
        
        $overdue60plus = Loan::whereIn('status', ['active', 'overdue'])
            ->where('next_due_date', '<', $now->copy()->subDays(60))
            ->sum('balance');
        
        $stats = [
            'total_receivable' => $totalReceivable,
            'current_not_due' => $currentNotDue,
            'total_overdue' => $overdue1to30 + $overdue31to60 + $overdue60plus,
            'overdue_count' => $overdueCount,
            'active_loans' => Loan::whereIn('status', ['active', 'overdue'])->count(),
            'total_loan_amount' => Loan::sum('loan_amount'),
            'total_collected' => $totalDownPayments + $totalPaid,
            'due_this_month' => Loan::whereIn('status', ['active', 'overdue'])
                ->whereMonth('next_due_date', now()->month)
                ->whereYear('next_due_date', now()->year)
                ->sum('monthly_amount'),
        ];

        $aging = [
            'current' => $currentNotDue,
            'overdue_1_30' => $overdue1to30,
            'overdue_31_60' => $overdue31to60,
            'overdue_60_plus' => $overdue60plus,
        ];

        return view('loans.index', compact('loans', 'stats', 'aging'));
    }

    public function create()
    {
        $sales = Sale::whereDoesntHave('loan')
            ->where('status', 'completed')
            ->with('customer')
            ->get();

        return view('loans.create', compact('sales'));
    }

    public function store(Request $request)
    {
        // Get settings for validation
        $minDownPaymentPercent = setting('loan.min_down_payment_percent', 20);
        $maxTermMonths = setting('loan.max_term_months', 36);
        $defaultInterestRate = setting('loan.default_interest_rate', 2);
        
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'loan_amount' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => "required|integer|min:1|max:{$maxTermMonths}",
            'remarks' => 'nullable|string|max:1000',
            'id_type' => 'required|string|max:100',
            'id_number' => 'required|string|max:100',
            'id_image' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);
        
        // Validate minimum down payment percentage
        $downPaymentPercent = ($validated['down_payment'] / $validated['loan_amount']) * 100;
        if ($downPaymentPercent < $minDownPaymentPercent) {
            return back()->withErrors([
                'down_payment' => "Down payment must be at least {$minDownPaymentPercent}% of the loan amount (₱" . number_format($validated['loan_amount'] * $minDownPaymentPercent / 100, 2) . ")"
            ])->withInput();
        }

        // Get customer_id from the sale
        $sale = Sale::findOrFail($validated['sale_id']);
        
        // Calculate monthly amount and balance
        $principal = $validated['loan_amount'] - $validated['down_payment'];
        $monthlyInterest = $validated['interest_rate'] / 100 / 12;
        $monthlyAmount = 0;
        
        if ($monthlyInterest > 0) {
            // Use amortization formula if there's interest
            $monthlyAmount = $principal * ($monthlyInterest * pow(1 + $monthlyInterest, $validated['term_months'])) 
                            / (pow(1 + $monthlyInterest, $validated['term_months']) - 1);
        } else {
            // Simple division if no interest
            $monthlyAmount = $principal / $validated['term_months'];
        }

        // Handle ID image upload
        $idImagePath = null;
        if ($request->hasFile('id_image')) {
            $idImagePath = $request->file('id_image')->store('loan_ids', 'public');
        }

        $loan = Loan::create([
            'sale_id' => $validated['sale_id'],
            'customer_id' => $sale->customer_id,
            'loan_amount' => $validated['loan_amount'],
            'down_payment' => $validated['down_payment'],
            'interest_rate' => $validated['interest_rate'],
            'term_months' => $validated['term_months'],
            'monthly_amount' => round($monthlyAmount, 2),
            'balance' => $principal,
            'start_date' => now(),
            'next_due_date' => now()->addMonth(),
            'end_date' => now()->addMonths($validated['term_months']),
            'status' => 'active',
            'remarks' => $validated['remarks'] ?? null,
            'id_type' => $validated['id_type'],
            'id_number' => $validated['id_number'],
            'id_image_path' => $idImagePath,
            'id_verified_at' => now()
        ]);

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', 'Loan created successfully.');
    }

    public function show(Loan $loan)
    {
        $loan->load(['sale.customer', 'payments', 'penalties']);
        
        // Get penalty breakdown
        $penaltyBreakdown = $this->penaltyService->getPenaltyBreakdown($loan);
        $penaltyTotals = $this->penaltyService->getTotalPenalties($loan);
        
        // Check for maturity penalty
        $this->penaltyService->checkAndApplyMaturityPenalty($loan);
        
        // Get customer rebates
        $customer = $loan->customer;
        $availableRebates = $customer ? $customer->rebates()->available()->get() : collect();
        $totalRebateBalance = $availableRebates->sum('rebate_amount');
        
        return view('loans.show', compact(
            'loan', 
            'penaltyBreakdown', 
            'penaltyTotals',
            'availableRebates',
            'totalRebateBalance'
        ));
    }

    /**
     * Display amortization schedule
     */
    public function amortization(Loan $loan)
    {
        $scheduleData = $this->amortizationService->generateSchedule($loan);
        
        return view('loans.amortization-schedule', $scheduleData);
    }

    /**
     * Print amortization schedule
     */
    public function amortizationPrint(Loan $loan)
    {
        $scheduleData = $this->amortizationService->generateSchedule($loan);
        
        return view('loans.amortization-print', $scheduleData);
    }

    public function destroy(Loan $loan)
    {
        if ($loan->payments()->exists()) {
            return back()->with('error', 'Cannot delete loan with existing payments.');
        }

        $loan->delete();
        return redirect()
            ->route('loans.index')
            ->with('success', 'Loan deleted successfully.');
    }

    public function storePayment(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'mode_of_payment' => 'required|string|max:50',
            'apply_rebate' => 'nullable|boolean',
            'rebate_amount' => 'nullable|numeric|min:0',
            
            // Enhanced validation for online payments
            'reference_number' => [
                'required_if:mode_of_payment,Bank Transfer,GCash,PayMaya',
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Z0-9\-]{6,20}$/',
                \Illuminate\Validation\Rule::unique('payments', 'reference_number')
                    ->whereNotNull('reference_number')
            ],
            'proof_image' => [
                'required_if:mode_of_payment,Bank Transfer,GCash,PayMaya',
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],
            'payment_bank' => ['nullable','string','max:100'],
            'payment_timestamp' => ['nullable','date'],
        ], [
            'reference_number.required_if' => 'Reference number is required for online payments.',
            'reference_number.unique' => 'This reference number has already been used.',
            'reference_number.regex' => 'Invalid reference format. Example: BPI-123456789',
            'proof_image.required_if' => 'Proof of payment screenshot is required for online payments.',
            'proof_image.image' => 'Proof must be an image file (JPG, PNG, GIF).',
            'proof_image.max' => 'Proof image must not exceed 2MB.',
        ]);

        // Check if payment is late and calculate penalty
        $paymentDate = Carbon::parse($validated['payment_date']);
        $dueDate = $loan->next_due_date ?? now();
        
        $penaltyCheck = $this->penaltyService->checkAndCalculatePenalty(
            $loan,
            $paymentDate,
            $dueDate,
            $loan->monthly_amount
        );

        // Check rebate eligibility (only if payment is on time)
        $rebateAmount = 0;
        $settingsService = app(\App\Services\SettingsService::class);
        $rebateOnTimeOnly = $settingsService->get('loan_penalty.rebate_on_time_only', true);
        
        if ($request->apply_rebate && $validated['rebate_amount'] > 0) {
            if ($rebateOnTimeOnly && $penaltyCheck['is_late']) {
                return back()
                    ->withErrors(['rebate_amount' => 'Rebates can only be applied to on-time payments. This payment is late.'])
                    ->withInput();
            }
            $rebateAmount = $validated['rebate_amount'];
        }

        // Calculate total amount with penalty
        $totalAmount = $validated['amount_paid'];
        if ($penaltyCheck['is_late']) {
            $totalAmount += $penaltyCheck['penalty_amount'];
        }

        // Handle proof image upload
        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $imagePath = $request->file('proof_image')->store('payments', 'public');
        }

        // Create the payment
        $payment = Payment::create([
            'loan_id' => $loan->id,
            'amount_paid' => $validated['amount_paid'],
            'payment_date' => $validated['payment_date'],
            'mode_of_payment' => $validated['mode_of_payment'],
            'reference_number' => $validated['reference_number'] ?? null,
            'proof_image_path' => $imagePath,
            'payment_bank' => $validated['payment_bank'] ?? null,
            'payment_timestamp' => $validated['payment_timestamp'] ?? null,
        ]);

        // Record penalty if late
        if ($penaltyCheck['is_late']) {
            $this->penaltyService->recordLatePaymentPenalty(
                $loan,
                $payment,
                $dueDate,
                $penaltyCheck['days_late']
            );
        }

        // Apply rebate if eligible
        if ($rebateAmount > 0 && !$penaltyCheck['is_late']) {
            $customer = $loan->customer;
            $availableRebates = $customer->rebates()->available()->orderBy('created_at')->get();
            
            $remainingRebate = $rebateAmount;
            foreach ($availableRebates as $rebate) {
                if ($remainingRebate <= 0) break;
                
                $useAmount = min($rebate->rebate_amount, $remainingRebate);
                $rebate->markAsUsed($payment->id);
                $remainingRebate -= $useAmount;
            }
        }

        // Update loan balance and next due date
        $amountToApply = $validated['amount_paid'] - $rebateAmount;
        $loan->balance = max(0, $loan->balance - $amountToApply);
        
        // If balance is 0, mark loan as completed
        if ($loan->balance <= 0) {
            $loan->status = 'completed';
            $loan->next_due_date = null;
        } else {
            // Set next due date to one month after this payment
            $loan->next_due_date = Carbon::parse($validated['payment_date'])->addMonth();
            
            // Update status if overdue
            if ($loan->status === 'overdue' && !$penaltyCheck['is_late']) {
                $loan->status = 'active';
            }
        }
        
        $loan->save();

        $message = 'Payment recorded successfully.';
        if ($penaltyCheck['is_late']) {
            $message .= ' Late payment penalty of ₱' . number_format($penaltyCheck['penalty_amount'], 2) . ' was applied.';
        }
        if ($rebateAmount > 0) {
            $message .= ' Rebate of ₱' . number_format($rebateAmount, 2) . ' was applied.';
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }
}