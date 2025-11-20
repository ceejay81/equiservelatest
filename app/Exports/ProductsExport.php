<?php

namespace App\Exports;

use App\Models\Product;

class ProductsExport
{
    public static function generate()
    {
        $products = Product::orderBy('name')->get();
        
        return $products->map(function($product) {
            return [
                'SKU' => $product->sku,
                'Product Name' => $product->name,
                'Brand' => $product->brand ?? '',
                'Model' => $product->model ?? '',
                'Category' => $product->category ?? '',
                'Unit Cost' => $product->unit_cost,
                'Selling Price' => $product->selling_price,
                'Stock' => $product->stock,
                'Status' => $product->status,
                'Description' => $product->description ?? '',
                'Reorder Level' => $product->reorder_level ?? 5,
                'Supplier' => $product->supplier ?? '',
                'Location' => $product->location ?? '',
                'Color' => $product->color ?? '',
                'Created At' => $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : '',
                'Updated At' => $product->updated_at ? $product->updated_at->format('Y-m-d H:i:s') : '',
            ];
        });
    }
}
