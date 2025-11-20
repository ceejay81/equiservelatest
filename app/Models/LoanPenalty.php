<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPenalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'payment_id',
        'type',
        'rate',
        'base_amount',
        'penalty_amount',
        'due_date',
        'charged_date',
        'days_late',
        'notes',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'due_date' => 'date',
        'charged_date' => 'date',
        'days_late' => 'integer',
    ];

    /**
     * Get the loan that owns the penalty
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the payment associated with this penalty
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope for late payment penalties
     */
    public function scopeLatePenalties($query)
    {
        return $query->where('type', 'late_payment');
    }

    /**
     * Scope for maturity penalties
     */
    public function scopeMaturityPenalties($query)
    {
        return $query->where('type', 'maturity');
    }
}
