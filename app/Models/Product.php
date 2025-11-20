<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'brand',
        'model',
        'category',
        'unit_cost',
        'selling_price',
        'stock',
        'status',
        'description',
        'reorder_level',
        'supplier',
        'location',
        'color',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $attributes = [
        'status' => 'active',
        'stock' => 0,
        'reorder_level' => 5,
    ];

    /**
     * Get all stock movements for this product
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get all sale items for this product
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get all rebates for this product
     */
    public function rebates()
    {
        return $this->hasMany(Rebate::class);
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->reorder_level;
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Check if product is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock', '>', 0)
                     ->whereRaw('stock <= reorder_level');
    }

    /**
     * Scope for out of stock products
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }
}
