<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'full_name',
        'contact',
        'address',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function rebates()
    {
        return $this->hasManyThrough(
            Rebate::class, // Final model
            Sale::class,   // Intermediate model
            'customer_id', // Foreign key on sales table referencing customers.id
            'sale_id',     // Foreign key on rebates table referencing sales.id
            'id',          // Local key on customers
            'id'           // Local key on sales
        );
    }
}
