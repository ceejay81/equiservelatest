<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity_change',
        'reference_type',
        'reference_id',
        'remarks',
        'performed_by',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get formatted user name
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'System';
    }

    /**
     * Get formatted type
     */
    public function getFormattedTypeAttribute()
    {
        return ucfirst($this->type);
    }
}
