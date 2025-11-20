<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Customer;
use Carbon\Carbon;

class AmortizationService
{
    /**
     * Generate complete amortization schedule with penalties and rebates
     */
    public function generateSchedule(Loan $loan)
    {
        $schedule = [];
        $balance = $loan->loan_amount - $loan->down_payment;
        $monthlyRate = $loan->interest_rate / 100 / 12;
        $startDate = Carbon::parse($loan->start_date ?? now());
        
        // Get customer rebates
        $customer = $loan->customer;
        $availableRebates = $customer ? $customer->rebates()
            ->available()
            ->orderBy('created_at')
            ->get() : collect();
        
        $rebateBalance = $availableRebates->sum('rebate_amount');
        
        for ($month = 1; $month <= $loan->term_months; $month++) {
            $interest = $balance * $monthlyRate;
            $principal = $loan->monthly_amount - $interest;
            $dueDate = $startDate->copy()->addMonths($month);
            $gracePeriodEnd = $dueDate->copy()->addDays($loan->grace_period_days);
            
            // Calculate potential rebate for this payment
            $potentialRebate = min($rebateBalance, $loan->monthly_amount);
            $amountAfterRebate = $loan->monthly_amount - $potentialRebate;
            
            // Calculate late penalty
            $latePenalty = $loan->calculateLatePaymentPenalty($loan->monthly_amount);
            $amountIfLate = $loan->monthly_amount + $latePenalty;
            
            $schedule[] = [
                'month' => $month,
                'due_date' => $dueDate,
                'grace_period_end' => $gracePeriodEnd,
                'payment' => $loan->monthly_amount,
                'principal' => $principal,
                'interest' => $interest,
                'balance' => max(0, $balance - $principal),
                'available_rebate' => $potentialRebate,
                'amount_after_rebate' => $amountAfterRebate,
                'late_penalty' => $latePenalty,
                'late_penalty_rate' => $loan->late_penalty_rate,
                'amount_if_late' => $amountIfLate,
                'is_maturity' => $month == $loan->term_months,
            ];
            
            $balance -= $principal;
            if ($balance <= 0) break;
        }
        
        return [
            'loan' => $loan,
            'customer' => $customer,
            'schedule' => $schedule,
            'summary' => $this->generateSummary($loan, $schedule, $rebateBalance),
            'available_rebates' => $availableRebates,
            'total_rebate_balance' => $rebateBalance,
        ];
    }
    
    /**
     * Generate summary information
     */
    protected function generateSummary(Loan $loan, array $schedule, $rebateBalance)
    {
        $totalPayments = collect($schedule)->sum('payment');
        $totalInterest = collect($schedule)->sum('interest');
        $totalPrincipal = collect($schedule)->sum('principal');
        $totalPotentialPenalties = collect($schedule)->sum('late_penalty');
        
        // Calculate best case (all on-time with rebates)
        $bestCaseTotal = $totalPayments - $rebateBalance;
        
        // Calculate worst case (all late + maturity penalty)
        $worstCaseTotal = $totalPayments + $totalPotentialPenalties;
        if ($loan->balance > 0) {
            $worstCaseTotal += $loan->calculateMaturityPenalty();
        }
        
        return [
            'principal_amount' => $loan->loan_amount - $loan->down_payment,
            'down_payment' => $loan->down_payment,
            'total_payments' => $totalPayments,
            'total_interest' => $totalInterest,
            'total_principal' => $totalPrincipal,
            'number_of_payments' => count($schedule),
            'monthly_payment' => $loan->monthly_amount,
            'total_rebate_available' => $rebateBalance,
            'best_case_total' => $bestCaseTotal,
            'worst_case_total' => $worstCaseTotal,
            'potential_savings' => $worstCaseTotal - $bestCaseTotal,
            'maturity_penalty_rate' => $loan->maturity_penalty_rate,
            'maturity_penalty_amount' => $loan->calculateMaturityPenalty(),
        ];
    }
    
    /**
     * Generate payment projection based on payment behavior
     */
    public function generatePaymentProjection(Loan $loan, $onTimePayments = 0, $latePayments = 0)
    {
        $schedule = $this->generateSchedule($loan);
        $totalPaid = 0;
        $totalPenalties = 0;
        $totalRebatesUsed = 0;
        
        foreach ($schedule['schedule'] as $index => $payment) {
            if ($index < $onTimePayments) {
                // On-time payment with rebate
                $totalPaid += $payment['amount_after_rebate'];
                $totalRebatesUsed += $payment['available_rebate'];
            } elseif ($index < ($onTimePayments + $latePayments)) {
                // Late payment
                $totalPaid += $payment['amount_if_late'];
                $totalPenalties += $payment['late_penalty'];
            }
        }
        
        return [
            'total_paid' => $totalPaid,
            'total_penalties' => $totalPenalties,
            'total_rebates_used' => $totalRebatesUsed,
            'net_amount' => $totalPaid - $totalRebatesUsed + $totalPenalties,
        ];
    }
}
