<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ProductsImport
{
    private $errors = [];
    private $imported = 0;
    private $updated = 0;
    private $skipped = 0;
    private $rowNumber = 0;

    public function processRow($row)
    {
        $this->rowNumber++;
        
        // Normalize keys (handle both "Product Name" and "product_name")
        $row = collect($row)->mapWithKeys(function($value, $key) {
            $normalizedKey = strtolower(str_replace(' ', '_', trim($key)));
            return [$normalizedKey => $value];
        })->toArray();

        // Validate required fields
        if (empty($row['sku']) || empty($row['product_name'])) {
            $this->skipped++;
            $this->errors[] = "Row {$this->rowNumber}: SKU and Product Name are required";
            return;
        }

        DB::beginTransaction();
        try {
            $product = Product::where('sku', $row['sku'])->first();

            $productData = [
                'name' => $row['product_name'],
                'brand' => $row['brand'] ?? null,
                'model' => $row['model'] ?? null,
                'category' => $row['category'] ?? null,
                'unit_cost' => floatval($row['unit_cost'] ?? 0),
                'selling_price' => floatval($row['selling_price'] ?? 0),
                'stock' => intval($row['stock'] ?? 0),
                'status' => $row['status'] ?? 'active',
                'description' => $row['description'] ?? null,
                'reorder_level' => intval($row['reorder_level'] ?? 5),
                'supplier' => $row['supplier'] ?? null,
                'location' => $row['location'] ?? null,
                'color' => $row['color'] ?? null,
            ];

            if ($product) {
                // Update existing
                $oldStock = $product->stock;
                $product->update($productData);

                // Log stock change
                $newStock = $productData['stock'];
                if ($oldStock != $newStock) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'adjustment',
                        'quantity_change' => $newStock - $oldStock,
                        'remarks' => 'Stock updated via Excel import',
                        'performed_by' => auth()->id(),
                    ]);
                }

                $this->updated++;
            } else {
                // Create new
                $productData['sku'] = $row['sku'];
                $product = Product::create($productData);

                // Log initial stock
                if ($product->stock > 0) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'adjustment',
                        'quantity_change' => $product->stock,
                        'remarks' => 'Initial stock via Excel import',
                        'performed_by' => auth()->id(),
                    ]);
                }

                $this->imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->skipped++;
            $this->errors[] = "Row {$this->rowNumber} (SKU: {$row['sku']}): " . $e->getMessage();
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getImported()
    {
        return $this->imported;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getSkipped()
    {
        return $this->skipped;
    }
}
