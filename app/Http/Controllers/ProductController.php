<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Search
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        // Brand filter
        if ($brand = $request->get('brand')) {
            $query->where('brand', $brand);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        // Calculate margin and stock status for each product
        $products->getCollection()->transform(function ($product) {
            // Calculate profit margin
            $product->margin = $product->unit_cost > 0 
                ? (($product->selling_price - $product->unit_cost) / $product->unit_cost) * 100 
                : 0;
            
            // Determine margin color
            if ($product->margin >= 30) {
                $product->margin_color = '#10B981'; // green
            } elseif ($product->margin >= 15) {
                $product->margin_color = '#F59E0B'; // orange
            } else {
                $product->margin_color = '#EF4444'; // red
            }
            
            // Stock status
            $stock = (int) ($product->stock ?? 0);
            $reorderLevel = $product->reorder_level ?? 5;
            
            if ($stock <= 0) {
                $product->stock_status = 'out';
            } elseif ($stock <= $reorderLevel) {
                $product->stock_status = 'low';
            } else {
                $product->stock_status = 'high';
            }
            
            return $product;
        });

        // Get unique categories and brands for filters
        $categories = Product::distinct()->whereNotNull('category')->pluck('category')->sort();
        $brands = Product::distinct()->whereNotNull('brand')->pluck('brand')->sort();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function store(Request $request)
    {
        // Build validation rules dynamically based on settings
        $rules = [
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'category' => setting('modules.products.require_category', false) ? 'required|string|max:100' : 'nullable|string|max:100',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'description' => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:100',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $product = Product::create($validated);

            // Log initial stock if > 0
            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'adjustment',
                    'quantity_change' => $product->stock,
                    'remarks' => 'Initial stock',
                    'performed_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load(['stockMovements' => function($query) {
            $query->with('user:id,name')
                  ->orderBy('created_at', 'desc')
                  ->limit(50);
        }]);

        return response()->json([
            'product' => $product,
            'stock_movements' => $product->stockMovements,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // Build validation rules dynamically based on settings
        $rules = [
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'category' => setting('modules.products.require_category', false) ? 'required|string|max:100' : 'nullable|string|max:100',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'description' => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:100',
        ];

        $validated = $request->validate($rules);

        try {
            $product->update($validated);

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        // Check if product has been sold
        if ($product->saleItems()->exists()) {
            return back()->with('error', 
                'Cannot delete product that has been sold. Consider marking as inactive instead.');
        }

        try {
            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Export products to Excel
     */
    public function export()
    {
        try {
            $data = ProductsExport::generate();
            $filename = 'products_' . date('Y-m-d_His') . '.xlsx';
            
            return (new \Rap2hpoutre\FastExcel\FastExcel($data))->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export products: ' . $e->getMessage());
        }
    }

    /**
     * Import products from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // 5MB max
        ]);

        try {
            $import = new ProductsImport();
            
            $rows = (new \Rap2hpoutre\FastExcel\FastExcel)->import($request->file('file'));
            
            foreach ($rows as $row) {
                $import->processRow($row);
            }

            $message = "Import completed! ";
            $message .= "Created: {$import->getImported()}, ";
            $message .= "Updated: {$import->getUpdated()}, ";
            $message .= "Skipped: {$import->getSkipped()}";

            if (count($import->getErrors()) > 0) {
                $errorMessage = $message . " | Errors: " . implode(', ', array_slice($import->getErrors(), 0, 3));
                if (count($import->getErrors()) > 3) {
                    $errorMessage .= " (and " . (count($import->getErrors()) - 3) . " more)";
                }
                return back()->with('warning', $errorMessage);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import products: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        try {
            $sampleData = collect([
                [
                    'SKU' => 'MC-001',
                    'Product Name' => 'Sample Motorcycle',
                    'Brand' => 'Yamaha',
                    'Model' => 'YZF-R15',
                    'Category' => 'Motorcycle',
                    'Unit Cost' => 45000,
                    'Selling Price' => 55000,
                    'Stock' => 10,
                    'Status' => 'active',
                    'Description' => 'Sample product description',
                    'Reorder Level' => 5,
                    'Supplier' => 'ABC Motors',
                ],
                [
                    'SKU' => 'PART-001',
                    'Product Name' => 'Sample Part',
                    'Brand' => 'Honda',
                    'Model' => 'CB150R',
                    'Category' => 'Parts',
                    'Unit Cost' => 500,
                    'Selling Price' => 750,
                    'Stock' => 50,
                    'Status' => 'active',
                    'Description' => 'Sample part description',
                    'Reorder Level' => 10,
                    'Supplier' => 'XYZ Parts',
                ],
            ]);

            $filename = 'products_import_template.xlsx';
            
            return (new \Rap2hpoutre\FastExcel\FastExcel($sampleData))->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download template: ' . $e->getMessage());
        }
    }
}
