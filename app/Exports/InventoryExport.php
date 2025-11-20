<?php

namespace App\Exports;

use App\Models\Product;

class InventoryExport
{
    public function export()
    {
        $products = Product::orderBy('name')->get();
        
        $filename = 'inventory_report_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        // Set headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Add CSV headers
        fputcsv($handle, [
            'SKU',
            'Product Name',
            'Category',
            'Brand',
            'Current Stock',
            'Reorder Level',
            'Stock Status',
            'Unit Cost',
            'Stock Value',
        ]);
        
        // Add data rows
        foreach ($products as $product) {
            $stockValue = $product->stock * $product->unit_cost;
            
            // Determine stock status
            if ($product->stock <= 0) {
                $stockStatus = 'Out of Stock';
            } elseif ($product->stock <= ($product->reorder_level ?? 5)) {
                $stockStatus = 'Low Stock';
            } else {
                $stockStatus = 'In Stock';
            }
            
            fputcsv($handle, [
                $product->sku,
                $product->name,
                $product->category,
                $product->brand,
                $product->stock,
                $product->reorder_level ?? 5,
                $stockStatus,
                $product->unit_cost,
                $stockValue,
            ]);
        }
        
        fclose($handle);
        exit;
    }
}
