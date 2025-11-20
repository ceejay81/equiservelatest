<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rebate extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'rebate_amount',
        'status',
        'used_for',
        'applied_to_loan_id',
        'used_at',
        'applied_to_payment_id',
        'applied_date',
        'expiry_date',
    ];

    protected $casts = [
        'rebate_amount' => 'decimal:2',
        'used_at' => 'datetime',
        'applied_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function appliedToLoan()
    {
        return $this->belongsTo(Loan::class, 'applied_to_loan_id');
    }

    public function appliedToPayment()
    {
        return $this->belongsTo(Payment::class, 'applied_to_payment_id');
    }

    public function isAvailable()
    {
        return $this->status === 'available' && 
               (!$this->expiry_date || $this->expiry_date->isFuture());
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function markAsUsed($paymentId = null)
    {
        $this->update([
            'status' => 'used',
            'applied_to_payment_id' => $paymentId,
            'applied_date' => now(),
        ]);
    }

    public function markAsForfeited()
    {
        $this->update([
            'status' => 'forfeited',
        ]);
    }

    /**
     * Scope for available rebates
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                     ->where(function($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>', now());
                     });
    }

    /**
     * Scope for expired rebates
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<=', now())
                     ->where('status', 'available');
    }
}
