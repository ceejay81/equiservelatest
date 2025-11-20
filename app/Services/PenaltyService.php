<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanPenalty;
use App\Models\Payment;
use Carbon\Carbon;

class PenaltyService
{
    /**
     * Check if payment is late and calculate penalty
     */
    public function checkAndCalculatePenalty(Loan $loan, $paymentDate, $dueDate, $installmentAmount)
    {
        $paymentDate = Carbon::parse($paymentDate);
        $dueDate = Carbon::parse($dueDate);
        $gracePeriodEnd = $dueDate->copy()->addDays($loan->grace_period_days);
        
        // Check if payment is within grace period
        if ($paymentDate->lte($gracePeriodEnd)) {
            return [
                'is_late' => false,
                'penalty_amount' => 0,
                'days_late' => 0,
            ];
        }
        
        // Calculate penalty
        $daysLate = $dueDate->diffInDays($paymentDate);
        $penaltyAmount = $loan->calculateLatePaymentPenalty($installmentAmount);
        
        return [
            'is_late' => true,
            'penalty_amount' => $penaltyAmount,
            'days_late' => $daysLate,
        ];
    }
    
    /**
     * Record a late payment penalty
     */
    public function recordLatePaymentPenalty(Loan $loan, Payment $payment, $dueDate, $daysLate)
    {
        $penaltyAmount = $loan->calculateLatePaymentPenalty($loan->monthly_amount);
        
        $penalty = LoanPenalty::create([
            'loan_id' => $loan->id,
            'payment_id' => $payment->id,
            'type' => 'late_payment',
            'rate' => $loan->late_penalty_rate,
            'base_amount' => $loan->monthly_amount,
            'penalty_amount' => $penaltyAmount,
            'due_date' => $dueDate,
            'charged_date' => $payment->payment_date,
            'days_late' => $daysLate,
            'notes' => "Late payment penalty - {$daysLate} days late",
        ]);
        
        // Update loan accumulated penalties
        $loan->increment('accumulated_penalties', $penaltyAmount);
        
        return $penalty;
    }
    
    /**
     * Check and apply maturity penalty if loan is past maturity date
     */
    public function checkAndApplyMaturityPenalty(Loan $loan)
    {
        // Check if loan has reached maturity and still has balance
        if (!$loan->hasReachedMaturity() || $loan->balance <= 0) {
            return null;
        }
        
        // Check if maturity penalty already applied
        $existingMaturityPenalty = $loan->penalties()
            ->where('type', 'maturity')
            ->exists();
            
        if ($existingMaturityPenalty) {
            return null;
        }
        
        // Calculate and record maturity penalty
        $penaltyAmount = $loan->calculateMaturityPenalty();
        
        $penalty = LoanPenalty::create([
            'loan_id' => $loan->id,
            'payment_id' => null,
            'type' => 'maturity',
            'rate' => $loan->maturity_penalty_rate,
            'base_amount' => $loan->balance,
            'penalty_amount' => $penaltyAmount,
            'due_date' => $loan->maturity_date,
            'charged_date' => now(),
            'days_late' => Carbon::parse($loan->maturity_date)->diffInDays(now()),
            'notes' => "Maturity penalty - loan unpaid by maturity date",
        ]);
        
        // Update loan accumulated penalties
        $loan->increment('accumulated_penalties', $penaltyAmount);
        
        return $penalty;
    }
    
    /**
     * Get total penalties for a loan
     */
    public function getTotalPenalties(Loan $loan)
    {
        return [
            'late_payment_penalties' => $loan->penalties()->latePenalties()->sum('penalty_amount'),
            'maturity_penalties' => $loan->penalties()->maturityPenalties()->sum('penalty_amount'),
            'total_penalties' => $loan->penalties()->sum('penalty_amount'),
        ];
    }
    
    /**
     * Get penalty breakdown for a loan
     */
    public function getPenaltyBreakdown(Loan $loan)
    {
        return $loan->penalties()
            ->orderBy('charged_date')
            ->get()
            ->map(function($penalty) {
                return [
                    'id' => $penalty->id,
                    'type' => $penalty->type,
                    'type_label' => $penalty->type === 'late_payment' ? 'Late Payment' : 'Maturity',
                    'rate' => $penalty->rate,
                    'base_amount' => $penalty->base_amount,
                    'penalty_amount' => $penalty->penalty_amount,
                    'due_date' => $penalty->due_date,
                    'charged_date' => $penalty->charged_date,
                    'days_late' => $penalty->days_late,
                    'notes' => $penalty->notes,
                ];
            });
    }
}
