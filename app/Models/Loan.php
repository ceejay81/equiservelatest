<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'customer_id',
        'loan_amount',
        'down_payment',
        'term_months',
        'interest_rate',
        'monthly_amount',
        'balance',
        'start_date',
        'next_due_date',
        'end_date',
        'status',
        'remarks',
        'late_penalty_rate',
        'maturity_penalty_rate',
        'grace_period_days',
        'accumulated_penalties',
        'maturity_date',
        'id_type',
        'id_number',
        'id_image_path',
        'id_verified_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'loan_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'start_date' => 'date',
        'next_due_date' => 'date',
        'end_date' => 'date',
        'late_penalty_rate' => 'decimal:2',
        'maturity_penalty_rate' => 'decimal:2',
        'grace_period_days' => 'integer',
        'accumulated_penalties' => 'decimal:2',
        'maturity_date' => 'date',
        'id_verified_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'down_payment' => 0,
        'interest_rate' => 0,
        'status' => 'active'
    ];

    /**
     * Get the sale associated with the loan.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the customer associated with the loan.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the payments for the loan.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date');
    }

    /**
     * Get the penalties for the loan.
     */
    public function penalties()
    {
        return $this->hasMany(LoanPenalty::class);
    }

    /**
     * Calculate total amount paid including down payment.
     */
    public function getTotalPaidAttribute()
    {
        return $this->down_payment + ($this->relationLoaded('payments') 
            ? $this->payments->sum('amount_paid') 
            : $this->payments()->sum('amount_paid'));
    }

    /**
     * Calculate payment progress as percentage.
     */
    public function getPaymentProgressAttribute()
    {
        return $this->loan_amount > 0 
            ? min(100, ($this->total_paid / $this->loan_amount) * 100) 
            : 0;
    }

    /**
     * Calculate remaining payments count.
     */
    public function getRemainingPaymentsAttribute()
    {
        return $this->monthly_amount > 0 
            ? ceil($this->balance / $this->monthly_amount) 
            : 0;
    }

    /**
     * Check if the loan is overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->status === 'active' 
            && $this->next_due_date 
            && $this->next_due_date->isPast();
    }

    /**
     * Get the next payment amount (considers remaining balance).
     */
    public function getNextPaymentAmountAttribute()
    {
        return min($this->monthly_amount, $this->balance);
    }

    /**
     * Update loan status based on current state.
     */
    public function updateLoanStatus()
    {
        if ($this->balance <= 0) {
            $this->update([
                'status' => 'completed',
                'end_date' => now(),
                'next_due_date' => null
            ]);
        } elseif ($this->is_overdue && $this->status !== 'overdue') {
            $this->update(['status' => 'overdue']);
        }
    }

    /**
     * Record a payment for this loan.
     */
    public function recordPayment($amount, $paymentDate = null, $modeOfPayment = 'Cash')
    {
        $payment = $this->payments()->create([
            'amount_paid' => $amount,
            'payment_date' => $paymentDate ?? now(),
            'mode_of_payment' => $modeOfPayment
        ]);

        // Update loan balance
        $this->balance = max(0, $this->balance - $amount);
        
        // If not the final payment, set next due date
        if ($this->balance > 0) {
            $this->next_due_date = Carbon::parse($paymentDate ?? now())->addMonth();
        }

        $this->save();
        $this->updateLoanStatus();

        return $payment;
    }

    /**
     * Check if payment is within grace period
     */
    public function isWithinGracePeriod($paymentDate, $dueDate)
    {
        $paymentDate = Carbon::parse($paymentDate);
        $dueDate = Carbon::parse($dueDate);
        $gracePeriodEnd = $dueDate->copy()->addDays($this->grace_period_days);
        
        return $paymentDate->lte($gracePeriodEnd);
    }

    /**
     * Calculate late payment penalty
     */
    public function calculateLatePaymentPenalty($installmentAmount)
    {
        return $installmentAmount * ($this->late_penalty_rate / 100);
    }

    /**
     * Calculate maturity penalty
     */
    public function calculateMaturityPenalty()
    {
        return $this->balance * ($this->maturity_penalty_rate / 100);
    }

    /**
     * Check if loan has reached maturity
     */
    public function hasReachedMaturity()
    {
        return $this->maturity_date && Carbon::parse($this->maturity_date)->isPast();
    }

    /**
     * Get total penalties charged
     */
    public function getTotalPenaltiesAttribute()
    {
        return $this->accumulated_penalties ?? 0;
    }

    /**
     * Calculate amortization schedule with penalties and rebates
     */
    public function getAmortizationSchedule()
    {
        $schedule = [];
        $balance = $this->loan_amount - $this->down_payment;
        $monthlyRate = $this->interest_rate / 100 / 12;
        $startDate = $this->start_date ?? now();

        for ($month = 1; $month <= $this->term_months; $month++) {
            $interest = $balance * $monthlyRate;
            $principal = $this->monthly_amount - $interest;
            $dueDate = $startDate->copy()->addMonths($month);
            $gracePeriodEnd = $dueDate->copy()->addDays($this->grace_period_days);
            
            $schedule[] = [
                'month' => $month,
                'due_date' => $dueDate,
                'grace_period_end' => $gracePeriodEnd,
                'payment' => $this->monthly_amount,
                'principal' => $principal,
                'interest' => $interest,
                'balance' => max(0, $balance - $principal),
                'late_penalty' => $this->calculateLatePaymentPenalty($this->monthly_amount),
                'late_penalty_rate' => $this->late_penalty_rate,
            ];

            $balance -= $principal;
            if ($balance <= 0) break;
        }

        return $schedule;
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($loan) {
            // Set customer_id from sale if not provided
            if (!$loan->customer_id && $loan->sale_id) {
                $loan->customer_id = Sale::find($loan->sale_id)->customer_id;
            }

            // Set start date if not provided
            if (!$loan->start_date) {
                $loan->start_date = now();
            }

            // Calculate end date if not provided
            if (!$loan->end_date && $loan->start_date && $loan->term_months) {
                $loan->end_date = Carbon::parse($loan->start_date)->addMonths($loan->term_months);
            }

            // Set initial balance if not provided
            if (is_null($loan->balance)) {
                $loan->balance = $loan->loan_amount - $loan->down_payment;
            }

            // Set maturity date if not provided
            if (!$loan->maturity_date && $loan->start_date && $loan->term_months) {
                $loan->maturity_date = Carbon::parse($loan->start_date)->addMonths($loan->term_months);
            }

            // Set penalty rates from settings if not provided
            $settingsService = app(\App\Services\SettingsService::class);
            
            if (is_null($loan->late_penalty_rate)) {
                $loan->late_penalty_rate = $settingsService->get('loan_penalty.late_penalty_rate', 3.00);
            }
            if (is_null($loan->maturity_penalty_rate)) {
                $loan->maturity_penalty_rate = $settingsService->get('loan_penalty.maturity_penalty_rate', 5.00);
            }
            if (is_null($loan->grace_period_days)) {
                $loan->grace_period_days = $settingsService->get('loan_penalty.grace_period_days', 3);
            }
        });
    }
}