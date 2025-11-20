<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'customer_id',
        'user_id',
        'sale_type',
        'total_amount',
        'payment_mode',
        'amount_tendered',
        'amount_paid',
        'reference_number',
        'payment_bank',
        'payment_timestamp',
        'proof_image_path',
        'discount_total',
        'discount_reason',
        'tax_amount',
    ];

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
