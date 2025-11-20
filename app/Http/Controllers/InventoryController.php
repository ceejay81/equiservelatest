<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query();

        // Filters
        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $query->where('category', $category);
        }

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $status = trim((string) $request->query('status', ''));
        if ($status === 'low') {
            $lowThreshold = setting('inventory.low_stock_threshold', 10);
            $criticalThreshold = setting('inventory.critical_stock_threshold', 5);
            $query->where('stock', '>', 0)
                  ->where('stock', '<=', $lowThreshold)
                  ->where('stock', '>', $criticalThreshold);
        } elseif ($status === 'out') {
            $query->where('stock', '<=', 0);
        } elseif ($status === 'critical') {
            $criticalThreshold = setting('inventory.critical_stock_threshold', 5);
            $query->where('stock', '>', 0)
                  ->where('stock', '<=', $criticalThreshold);
        }

        $products = $query->orderBy('name')->paginate(10)->withQueryString();

        // Calculate stock status for each product using settings thresholds
        $products->getCollection()->transform(function ($product) {
            $stock = (int) $product->stock;
            $lowThreshold = setting('inventory.low_stock_threshold', 10);
            $criticalThreshold = setting('inventory.critical_stock_threshold', 5);
            
            if ($stock <= 0) {
                $product->stock_status = 'out';
            } elseif ($stock <= $criticalThreshold) {
                $product->stock_status = 'critical';
            } elseif ($stock <= $lowThreshold) {
                $product->stock_status = 'low';
            } else {
                $product->stock_status = 'high';
            }
            
            return $product;
        });

        // KPIs - use settings for thresholds
        $totalProducts = (int) Product::count();
        $lowThreshold = setting('inventory.low_stock_threshold', 10);
        $criticalThreshold = setting('inventory.critical_stock_threshold', 5);
        $lowStockCount = (int) Product::where('stock', '>', 0)
            ->where('stock', '<=', $lowThreshold)
            ->count();
        $outOfStockCount = (int) Product::where('stock', '<=', 0)->count();
        $criticalStockCount = (int) Product::where('stock', '>', 0)
            ->where('stock', '<=', $criticalThreshold)
            ->count();
        $inventoryValue = (float) Product::selectRaw('COALESCE(SUM(CASE WHEN stock > 0 THEN stock * unit_cost ELSE 0 END), 0) as val')->value('val');

        return view('inventory.index', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'criticalStockCount' => $criticalStockCount,
            'inventoryValue' => $inventoryValue,
            'filters' => [
                'category' => $category,
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $validated['stock'] = $validated['stock'] ?? 0;
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
            return back()->with('success', 'Product added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
        ]);

        try {
            $product->update($validated);
            return back()->with('success', 'Product updated successfully');
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
            return back()->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $quantityChange = $validated['adjustment_type'] === 'add' 
                ? $validated['quantity'] 
                : -$validated['quantity'];

            // Update stock
            $oldStock = $product->stock;
            $product->stock += $quantityChange;
            $product->save();

            // Log movement
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity_change' => $quantityChange,
                'remarks' => $validated['remarks'] . " (From {$oldStock} to {$product->stock})",
                'performed_by' => auth()->id(),
            ]);

            DB::commit();
            return back()->with('success', 'Stock adjusted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }

    public function receiveStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $oldStock = $product->stock;
            
            // Increase stock
            $product->stock += $validated['quantity'];
            $product->save();

            // Build remarks
            $remarks = 'Stock received';
            if (!empty($validated['supplier'])) {
                $remarks .= ' from ' . $validated['supplier'];
            }
            if (!empty($validated['reference_number'])) {
                $remarks .= ' (Ref: ' . $validated['reference_number'] . ')';
            }
            if (!empty($validated['remarks'])) {
                $remarks .= ' - ' . $validated['remarks'];
            }
            $remarks .= " (From {$oldStock} to {$product->stock})";

            // Log movement
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity_change' => $validated['quantity'],
                'remarks' => $remarks,
                'performed_by' => auth()->id(),
            ]);

            DB::commit();
            return back()->with('success', 'Stock received successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to receive stock: ' . $e->getMessage());
        }
    }

    public function getStockMovements(Product $product)
    {
        $movements = StockMovement::where('product_id', $product->id)
            ->with(['product:id,name,sku'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Add user names
        $movements->getCollection()->transform(function($movement) {
            $movement->user_name = $movement->user_name;
            $movement->formatted_date = $movement->created_at->format('M d, Y h:i A');
            return $movement;
        });

        return response()->json($movements);
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
            'stock_movements' => $product->stockMovements->map(function($movement) {
                return [
                    'id' => $movement->id,
                    'date' => $movement->created_at->format('M d, Y h:i A'),
                    'user' => $movement->user_name,
                    'type' => $movement->formatted_type,
                    'quantity_change' => $movement->quantity_change,
                    'remarks' => $movement->remarks,
                ];
            }),
        ]);
    }
}
