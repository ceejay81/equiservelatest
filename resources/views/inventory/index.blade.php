@extends('layouts.app')

@php($pageTitle = 'Inventory Management')

@push('styles')
<style>
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 12px;
  }
  .stat-icon.blue { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; }
  .stat-icon.orange { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; }
  .stat-icon.red { background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white; }
  .stat-icon.green { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; }
  .stat-label {
    font-size: 0.875rem;
    color: #64748B;
    margin-bottom: 4px;
  }
  .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0F172A;
  }

  .table tbody tr:hover {
    background-color: #F8FAFC !important;
  }
  .stock-badge {
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
  }
  .stock-badge.high {
    background: #D1FAE5;
    color: #065F46;
  }
  .stock-badge.low {
    background: #FEF3C7;
    color: #92400E;
  }
  .stock-badge.out {
    background: #FEE2E2;
    color: #991B1B;
  }
  .filter-tabs {
    display: inline-flex;
    background: #F1F5F9;
    border-radius: 8px;
    padding: 4px;
  }
  .filter-tab {
    padding: 6px 16px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748B;
    text-decoration: none;
    transition: all 0.2s;
  }
  .filter-tab:hover {
    color: #0F172A;
    background: #E2E8F0;
  }
  .filter-tab.active {
    background: white;
    color: #3B82F6;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }
  .pagination {
    margin-bottom: 0;
  }
  .page-link {
    color: #3B82F6;
    border: 1px solid #E5E7EB;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }
  .page-link:hover {
    background-color: #F8FAFC;
    color: #2563EB;
  }
  .page-item.active .page-link {
    background-color: #3B82F6;
    border-color: #3B82F6;
    color: white;
  }
  .page-item.disabled .page-link {
    color: #9CA3AF;
    background-color: #F9FAFB;
  }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">Inventory Management</h1>
    <p class="text-muted mb-0">Track stock levels, adjust quantities, and monitor inventory in real-time</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i>
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-2">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<!-- Info Alert for Product Creation -->
<div class="alert alert-info mb-3">
  <i class="fas fa-info-circle mr-2"></i>
  <strong>Inventory Management:</strong> This module is for stock operations (adjustments, receiving, tracking). 
  To add new products to your catalog, go to <a href="/products" class="alert-link font-weight-bold">Product Catalog →</a>
</div>

@if($lowStockCount > 0)
  <div class="alert alert-warning alert-dismissible fade show">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>{{ $lowStockCount }} items</strong> are running low on stock. 
    <a href="{{ url('/inventory') }}?status=low" class="font-weight-bold">View details →</a>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-boxes"></i>
      </div>
      <div class="stat-label">Total Products</div>
      <div class="stat-value">{{ number_format($totalProducts) }}</div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="stat-label">Low Stock Items</div>
      <div class="stat-value">{{ number_format($lowStockCount) }}</div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon red">
        <i class="fas fa-times-circle"></i>
      </div>
      <div class="stat-label">Out of Stock</div>
      <div class="stat-value">{{ number_format($outOfStockCount) }}</div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fas fa-coins"></i>
      </div>
      <div class="stat-label">Inventory Value</div>
      <div class="stat-value">₱{{ number_format($inventoryValue / 1000, 1) }}K</div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ url('/inventory') }}">
      <div class="row align-items-end">
        <div class="col-md-3 mb-2">
          <label class="small text-muted mb-1">Category</label>
          <select name="category" class="form-control">
            <option value="">All Categories</option>
            <option value="Motorcycle" {{ ($filters['category'] ?? '') === 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
            <option value="Spare Part" {{ ($filters['category'] ?? '') === 'Spare Part' ? 'selected' : '' }}>Spare Part</option>
            <option value="Power Product" {{ ($filters['category'] ?? '') === 'Power Product' ? 'selected' : '' }}>Power Product</option>
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label class="small text-muted mb-1">Search</label>
          <input name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Search by SKU, name, or brand">
        </div>
        <div class="col-md-3 mb-2">
          <label class="small text-muted mb-1">Stock Status</label>
          <div class="filter-tabs">
            <a href="{{ url('/inventory') }}" class="filter-tab {{ empty($filters['status']) ? 'active' : '' }}">All</a>
            <a href="{{ url('/inventory') }}?status=low" class="filter-tab {{ ($filters['status'] ?? '') === 'low' ? 'active' : '' }}">Low</a>
            <a href="{{ url('/inventory') }}?status=out" class="filter-tab {{ ($filters['status'] ?? '') === 'out' ? 'active' : '' }}">Out</a>
          </div>
        </div>
        <div class="col-md-2 mb-2">
          <button class="btn btn-primary btn-block" type="submit">
            <i class="fas fa-filter mr-1"></i> Filter
          </button>
        </div>
      </div>
      @if(request()->hasAny(['category', 'q', 'status']))
        <div class="mt-2">
          <a href="{{ url('/inventory') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-times mr-1"></i> Clear Filters
          </a>
        </div>
      @endif
    </form>
  </div>
</div>

<!-- Products Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead style="background: #F8FAFC;">
          <tr>
            <th>Product Details</th>
            <th>Category</th>
            <th>Brand / Model</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Selling Price</th>
            <th class="text-center">Stock</th>
            <th class="text-right" style="width: 240px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            <tr>
              <td>
                <div style="font-weight: 600; color: #0F172A;">{{ $product->name }}</div>
                <div style="font-size: 0.8rem; color: #64748B;">SKU: {{ $product->sku }}</div>
                @if($product->location || $product->color)
                  <div style="font-size: 0.75rem; color: #94A3B8; margin-top: 2px;">
                    @if($product->location)
                      <i class="fas fa-map-marker-alt" style="font-size: 0.7rem;"></i> {{ $product->location }}
                    @endif
                    @if($product->location && $product->color) • @endif
                    @if($product->color)
                      <i class="fas fa-palette" style="font-size: 0.7rem;"></i> {{ $product->color }}
                    @endif
                  </div>
                @endif
              </td>
              <td>
                @if($product->category)
                  <span class="badge badge-secondary">{{ $product->category }}</span>
                @else
                  <span style="color: #94A3B8;">—</span>
                @endif
              </td>
              <td>
                <div style="font-weight: 500;">{{ $product->brand ?? '—' }}</div>
                @if($product->model)
                  <div style="font-size: 0.8rem; color: #64748B;">{{ $product->model }}</div>
                @endif
              </td>
              <td class="text-right">
                <div style="font-weight: 600;">₱{{ number_format($product->unit_cost, 2) }}</div>
              </td>
              <td class="text-right">
                <div style="font-weight: 700; color: #10B981; font-size: 1.05rem;">
                  ₱{{ number_format($product->selling_price, 2) }}
                </div>
              </td>
              <td class="text-center">
                <span class="stock-badge {{ $product->stock_status }}">{{ $product->stock }}</span>
              </td>
              <td class="text-right">
                <button class="btn btn-sm btn-outline-primary" onclick="viewProduct({{ $product->id }})" title="View Details">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" 
                        onclick='editProduct({{ $product->id }}, @json($product))' 
                        title="Edit Product">
                  <i class="fas fa-pen"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" 
                        onclick="openReceiveStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})" 
                        title="Receive Stock">
                  <i class="fas fa-truck"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" 
                        onclick="openAdjustStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})" 
                        title="Adjust Stock">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <div style="color: #94A3B8;">
                  <i class="fas fa-box-open fa-3x mb-3" style="opacity: 0.3;"></i>
                  <p class="mb-2" style="font-size: 1.1rem; font-weight: 500;">No products found</p>
                  <p class="mb-0" style="font-size: 0.875rem;">
                    @if(request()->hasAny(['category', 'q', 'status']))
                      Try adjusting your filters or <a href="{{ url('/inventory') }}">clear filters</a>
                    @else
                      Get started by adding your first product
                    @endif
                  </p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  
  @if($products->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="text-muted small">
        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
      </div>
      <div>
        {{ $products->links('pagination::bootstrap-4') }}
      </div>
    </div>
  @endif
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-edit mr-2" style="color: #3B82F6;"></i>
          Edit Product
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="editProductForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>SKU <span class="text-danger">*</span></label>
                <input type="text" name="sku" id="edit-sku" class="form-control" required>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" id="edit-brand" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Model</label>
                <input type="text" name="model" id="edit-model" class="form-control">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Category</label>
                <select name="category" id="edit-category" class="form-control">
                  <option value="">Select Category</option>
                  <option value="Motorcycle">Motorcycle</option>
                  <option value="Spare Part">Spare Part</option>
                  <option value="Power Product">Power Product</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Unit Cost <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input type="number" name="unit_cost" id="edit-unit-cost" step="0.01" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Selling Price <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input type="number" name="selling_price" id="edit-selling-price" step="0.01" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Reorder Level</label>
                <input type="number" name="reorder_level" id="edit-reorder-level" class="form-control">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Supplier</label>
                <input type="text" name="supplier" id="edit-supplier" class="form-control">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group mb-0">
                <label>Description</label>
                <textarea name="description" id="edit-description" class="form-control" rows="2"></textarea>
              </div>
            </div>
          </div>
          <div class="alert alert-info mt-3 mb-0">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Note:</strong> To adjust stock quantities, use the "Adjust Stock" or "Receive Stock" buttons.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Update Product
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-box mr-2" style="color: #3B82F6;"></i>
          <span id="view-modal-title">Product Details</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <!-- Current Stock Display - Prominent -->
        <div class="card mb-3" style="border: 2px solid #3B82F6;">
          <div class="card-body text-center py-3">
            <div class="mb-2">
              <i class="fas fa-warehouse" style="font-size: 1.5rem; color: #3B82F6;"></i>
            </div>
            <div class="text-muted small mb-1">CURRENT STOCK</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #0F172A; line-height: 1;">
              <span id="view-stock">-</span>
            </div>
            <div class="text-muted">units</div>
            <div class="mt-2">
              <small class="text-muted">Reorder Level: <strong id="view-reorder-level">-</strong> units</small>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Product Information Card -->
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-light py-2">
                <h6 class="mb-0 small"><i class="fas fa-info-circle mr-2"></i>Product Information</h6>
              </div>
              <div class="card-body">
                <div class="mb-2">
                  <small class="text-muted d-block">SKU</small>
                  <strong id="view-sku">-</strong>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Product Name</small>
                  <strong id="view-name">-</strong>
                </div>
                <div class="row">
                  <div class="col-6">
                    <div class="mb-2">
                      <small class="text-muted d-block">Brand</small>
                      <span id="view-brand">-</span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-2">
                      <small class="text-muted d-block">Model</small>
                      <span id="view-model">-</span>
                    </div>
                  </div>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Category</small>
                  <span id="view-category">-</span>
                </div>
                <div class="mb-0">
                  <small class="text-muted d-block">Supplier</small>
                  <span id="view-supplier">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Pricing Information Card -->
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-light py-2">
                <h6 class="mb-0 small"><i class="fas fa-dollar-sign mr-2"></i>Pricing</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-6">
                    <div class="text-center p-2 border rounded">
                      <small class="text-muted d-block mb-1">Unit Cost</small>
                      <strong id="view-unit-cost" style="font-size: 1rem;">-</strong>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center p-2 border rounded">
                      <small class="text-muted d-block mb-1">Selling Price</small>
                      <strong id="view-selling-price" class="text-success" style="font-size: 1rem;">-</strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stock Movement History -->
        <div class="card">
          <div class="card-header bg-light py-2">
            <h6 class="mb-0 small"><i class="fas fa-history mr-2"></i>Stock Movement History</h6>
          </div>
          <div class="card-body p-0">
            <div id="stock-movements-container" style="max-height: 300px; overflow-y: auto; padding: 15px;">
              <div class="text-center text-muted py-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-3">Loading movements...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i>Close
        </button>
        <button type="button" class="btn btn-success" onclick="openReceiveStockFromView()">
          <i class="fas fa-truck mr-1"></i>Receive Stock
        </button>
        <button type="button" class="btn btn-primary" onclick="openAdjustStockFromView()">
          <i class="fas fa-edit mr-1"></i>Adjust Stock
        </button>
      </div>
    </div>
  </div>
</div>

<style>
/* View Modal Enhancements */
#viewProductModal .card {
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  border-radius: 8px;
}

#viewProductModal .card-header {
  border-bottom: 2px solid #E5E7EB;
  padding: 12px 15px;
}

#viewProductModal .card-header h6 {
  font-weight: 600;
  color: #0F172A;
}

#stock-movements-container::-webkit-scrollbar {
  width: 8px;
}

#stock-movements-container::-webkit-scrollbar-track {
  background: #F1F5F9;
  border-radius: 4px;
}

#stock-movements-container::-webkit-scrollbar-thumb {
  background: #CBD5E1;
  border-radius: 4px;
}

#stock-movements-container::-webkit-scrollbar-thumb:hover {
  background: #94A3B8;
}
</style>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-edit mr-2" style="color: #3B82F6;"></i>
          Adjust Stock
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="adjustStockForm" method="POST">
        @csrf
        <div class="modal-body">
          <!-- Product Name -->
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Product</small>
            <strong id="adjust-product-name" style="font-size: 1.1rem;">-</strong>
          </div>

          <!-- Current Stock Display -->
          <div class="stock-display-box mb-3">
            <div class="stock-label">Current Stock Level</div>
            <div class="stock-value">
              <span id="adjust-current-stock-display">0</span>
              <span class="stock-unit">units</span>
            </div>
          </div>
          <input type="hidden" id="adjust-current-stock" value="0">

          <!-- Adjustment Type Selector -->
          <div class="form-group">
            <label>Adjustment Type <span class="text-danger">*</span></label>
            <div class="adjustment-type-selector">
              <button type="button" class="adjustment-btn add-btn active" onclick="selectAdjustmentType('add')">
                <i class="fas fa-plus-circle mr-1"></i>
                Add Stock
              </button>
              <button type="button" class="adjustment-btn subtract-btn" onclick="selectAdjustmentType('subtract')">
                <i class="fas fa-minus-circle mr-1"></i>
                Subtract Stock
              </button>
            </div>
            <input type="hidden" name="adjustment_type" id="adjustment-type-input" value="add" required>
          </div>

          <!-- Quantity Input -->
          <div class="form-group">
            <label>Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantity" id="adjust-quantity-input" class="form-control form-control-lg" min="1" placeholder="Enter quantity" required onkeyup="updateAdjustPreview()">
          </div>

          <!-- Stock Preview -->
          <div class="stock-preview-box mb-3">
            <div class="preview-label">New Stock Level</div>
            <div class="preview-calculation">
              <span id="preview-current">0</span>
              <span id="preview-operator" class="text-success">+</span>
              <span id="preview-quantity">0</span>
              <span class="text-muted">=</span>
              <span id="preview-result" class="preview-result">0</span>
              <span class="preview-unit">units</span>
            </div>
          </div>

          <!-- Remarks -->
          <div class="form-group mb-0">
            <label>Remarks <span class="text-danger">*</span></label>
            <textarea name="remarks" class="form-control" rows="2" placeholder="Reason for adjustment (e.g., Damaged goods, Stock count correction)" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Adjust Stock
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.stock-display-box {
  background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
  border: 2px solid #CBD5E1;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
}

.stock-label {
  font-size: 0.875rem;
  color: #64748B;
  margin-bottom: 8px;
  font-weight: 500;
}

.stock-value {
  font-size: 2.5rem;
  font-weight: 700;
  color: #0F172A;
  line-height: 1;
}

.stock-unit {
  font-size: 1rem;
  color: #64748B;
  font-weight: 400;
}

.adjustment-type-selector {
  display: flex;
  gap: 10px;
}

.adjustment-btn {
  flex: 1;
  padding: 15px;
  border: 2px solid #E2E8F0;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}

.adjustment-btn:hover {
  border-color: #CBD5E1;
  background: #F8FAFC;
}

.adjustment-btn.add-btn.active {
  border-color: #10B981;
  background: #D1FAE5;
  color: #065F46;
}

.adjustment-btn.subtract-btn.active {
  border-color: #EF4444;
  background: #FEE2E2;
  color: #991B1B;
}

.stock-preview-box {
  background: #F0F9FF;
  border: 2px solid #BAE6FD;
  border-radius: 8px;
  padding: 15px;
  text-align: center;
}

.preview-label {
  font-size: 0.875rem;
  color: #0369A1;
  margin-bottom: 8px;
  font-weight: 500;
}

.preview-calculation {
  font-size: 1.5rem;
  font-weight: 600;
  color: #0F172A;
}

.preview-result {
  font-size: 1.75rem;
  font-weight: 700;
}

.preview-unit {
  font-size: 0.875rem;
  color: #64748B;
  font-weight: 400;
}
</style>

<!-- Receive Stock Modal -->
<div class="modal fade" id="receiveStockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fas fa-truck mr-2"></i>
          Receive Stock from Supplier
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="receiveStockForm" method="POST">
        @csrf
        <div class="modal-body">
          <!-- Product Name -->
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Product</small>
            <strong id="receive-product-name" style="font-size: 1.1rem;">-</strong>
          </div>

          <!-- Current Stock Display -->
          <div class="stock-display-box mb-3" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border-color: #86EFAC;">
            <div class="stock-label" style="color: #166534;">Current Stock Level</div>
            <div class="stock-value" style="color: #065F46;">
              <span id="receive-current-stock-display">0</span>
              <span class="stock-unit">units</span>
            </div>
          </div>
          <input type="hidden" id="receive-current-stock" value="0">

          <!-- Quantity Received -->
          <div class="form-group">
            <label>Quantity Received <span class="text-danger">*</span></label>
            <input type="number" name="quantity" id="receive-quantity-input" class="form-control form-control-lg" min="1" placeholder="Enter quantity received" required onkeyup="updateReceivePreview()">
          </div>

          <!-- Stock Preview -->
          <div class="stock-preview-box mb-3" style="background: #F0FDF4; border-color: #86EFAC;">
            <div class="preview-label" style="color: #166534;">
              <i class="fas fa-check-circle mr-1"></i>New Stock Level
            </div>
            <div class="preview-calculation">
              <span id="receive-preview-current">0</span>
              <span class="text-success">+</span>
              <span id="receive-preview-quantity">0</span>
              <span class="text-muted">=</span>
              <span id="receive-preview-result" class="preview-result text-success">0</span>
              <span class="preview-unit">units</span>
            </div>
          </div>

          <!-- Supplier Information -->
          <div class="card mb-3">
            <div class="card-header bg-light py-2">
              <small class="font-weight-bold text-muted">Supplier Information</small>
            </div>
            <div class="card-body">
              <div class="form-group mb-2">
                <label class="small">Supplier Name</label>
                <input type="text" name="supplier" class="form-control" placeholder="Supplier name (optional)">
              </div>
              <div class="form-group mb-0">
                <label class="small">Reference Number</label>
                <input type="text" name="reference_number" class="form-control" placeholder="PO or delivery number (optional)">
              </div>
            </div>
          </div>

          <!-- Remarks -->
          <div class="form-group mb-0">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="2" placeholder="Additional notes (optional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check mr-1"></i>Receive Stock
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let currentProductId = null;

function viewProduct(productId) {
  currentProductId = productId;
  
  // Show modal
  $('#viewProductModal').modal('show');
  
  // Fetch product details
  fetch(`/inventory/products/${productId}`)
    .then(response => response.json())
    .then(data => {
      const product = data.product;
      
      // Update modal title
      document.getElementById('view-modal-title').textContent = product.name || 'Product Details';
      
      // Populate product info
      document.getElementById('view-sku').textContent = product.sku || '-';
      document.getElementById('view-name').textContent = product.name || '-';
      document.getElementById('view-brand').textContent = product.brand || '-';
      document.getElementById('view-model').textContent = product.model || '-';
      document.getElementById('view-category').textContent = product.category || '-';
      document.getElementById('view-unit-cost').textContent = product.unit_cost ? `₱${parseFloat(product.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}` : '-';
      document.getElementById('view-selling-price').textContent = product.selling_price ? `₱${parseFloat(product.selling_price).toLocaleString('en-US', {minimumFractionDigits: 2})}` : '-';
      document.getElementById('view-stock').textContent = product.stock || '0';
      document.getElementById('view-reorder-level').textContent = product.reorder_level || '5';
      document.getElementById('view-supplier').textContent = product.supplier || '-';
      
      // Populate stock movements
      const container = document.getElementById('stock-movements-container');
      if (data.stock_movements && data.stock_movements.length > 0) {
        let html = '<div class="list-group list-group-flush">';
        data.stock_movements.forEach(movement => {
          const isPositive = movement.quantity_change > 0;
          const badgeClass = isPositive ? 'badge-success' : 'badge-danger';
          const icon = isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
          
          html += `
            <div class="list-group-item px-0">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <span class="badge ${badgeClass}">
                    <i class="fas ${icon} mr-1"></i>${isPositive ? '+' : ''}${movement.quantity_change}
                  </span>
                  <span class="ml-2 text-muted small">${movement.type}</span>
                  <div class="small text-muted mt-1">${movement.remarks || '-'}</div>
                </div>
                <div class="text-right">
                  <div class="small font-weight-bold">${movement.user}</div>
                  <div class="small text-muted">${movement.date}</div>
                </div>
              </div>
            </div>
          `;
        });
        html += '</div>';
        container.innerHTML = html;
      } else {
        container.innerHTML = `
          <div class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.3;"></i>
            <p>No stock movements yet</p>
          </div>
        `;
      }
    })
    .catch(error => {
      console.error('Error fetching product:', error);
      alert('Failed to load product details');
      $('#viewProductModal').modal('hide');
    });
}

function editProduct(productId, product) {
  currentProductId = productId;
  
  // Populate form
  document.getElementById('edit-sku').value = product.sku || '';
  document.getElementById('edit-name').value = product.name || '';
  document.getElementById('edit-brand').value = product.brand || '';
  document.getElementById('edit-model').value = product.model || '';
  document.getElementById('edit-category').value = product.category || '';
  document.getElementById('edit-unit-cost').value = product.unit_cost || '';
  document.getElementById('edit-selling-price').value = product.selling_price || '';
  document.getElementById('edit-reorder-level').value = product.reorder_level || '';
  document.getElementById('edit-supplier').value = product.supplier || '';
  document.getElementById('edit-description').value = product.description || '';
  
  // Set form action
  document.getElementById('editProductForm').action = `/inventory/products/${productId}`;
  
  // Show modal
  $('#editProductModal').modal('show');
}

function openAdjustStockModal(productId, productName, currentStock) {
  currentProductId = productId;
  document.getElementById('adjust-product-name').textContent = productName;
  document.getElementById('adjust-current-stock').value = currentStock;
  document.getElementById('adjust-current-stock-display').textContent = currentStock;
  document.getElementById('adjustStockForm').action = `/inventory/products/${productId}/adjust`;
  
  // Reset form
  document.getElementById('adjustment-type-input').value = 'add';
  document.getElementById('adjust-quantity-input').value = '';
  document.querySelector('.adjustment-btn.add-btn').classList.add('active');
  document.querySelector('.adjustment-btn.subtract-btn').classList.remove('active');
  
  // Reset preview
  updateAdjustPreview();
  
  $('#adjustStockModal').modal('show');
}

function selectAdjustmentType(type) {
  document.getElementById('adjustment-type-input').value = type;
  
  // Update button states
  if (type === 'add') {
    document.querySelector('.adjustment-btn.add-btn').classList.add('active');
    document.querySelector('.adjustment-btn.subtract-btn').classList.remove('active');
  } else {
    document.querySelector('.adjustment-btn.add-btn').classList.remove('active');
    document.querySelector('.adjustment-btn.subtract-btn').classList.add('active');
  }
  
  updateAdjustPreview();
}

function updateAdjustPreview() {
  const current = parseInt(document.getElementById('adjust-current-stock').value) || 0;
  const quantity = parseInt(document.getElementById('adjust-quantity-input').value) || 0;
  const type = document.getElementById('adjustment-type-input').value;
  
  const operator = type === 'add' ? '+' : '-';
  const result = type === 'add' ? current + quantity : current - quantity;
  
  document.getElementById('preview-current').textContent = current;
  document.getElementById('preview-operator').textContent = operator;
  document.getElementById('preview-operator').className = type === 'add' ? 'text-success' : 'text-danger';
  document.getElementById('preview-quantity').textContent = quantity;
  document.getElementById('preview-result').textContent = result;
  
  // Color code result
  const resultEl = document.getElementById('preview-result');
  if (result < 0) {
    resultEl.style.color = '#EF4444'; // Red
  } else if (result <= 5) {
    resultEl.style.color = '#F59E0B'; // Orange
  } else {
    resultEl.style.color = '#10B981'; // Green
  }
}

function openAdjustStockFromView() {
  const productName = document.getElementById('view-name').textContent;
  const currentStock = document.getElementById('view-stock').textContent;
  $('#viewProductModal').modal('hide');
  setTimeout(() => {
    openAdjustStockModal(currentProductId, productName, currentStock);
  }, 300);
}

function openReceiveStockModal(productId, productName, currentStock) {
  currentProductId = productId;
  document.getElementById('receive-product-name').textContent = productName;
  document.getElementById('receive-current-stock').value = currentStock;
  document.getElementById('receive-current-stock-display').textContent = currentStock;
  document.getElementById('receiveStockForm').action = `/inventory/products/${productId}/receive`;
  
  // Reset form
  document.getElementById('receive-quantity-input').value = '';
  
  // Reset preview
  updateReceivePreview();
  
  $('#receiveStockModal').modal('show');
}

function updateReceivePreview() {
  const current = parseInt(document.getElementById('receive-current-stock').value) || 0;
  const quantity = parseInt(document.getElementById('receive-quantity-input').value) || 0;
  const result = current + quantity;
  
  document.getElementById('receive-preview-current').textContent = current;
  document.getElementById('receive-preview-quantity').textContent = quantity;
  document.getElementById('receive-preview-result').textContent = result;
}

function openReceiveStockFromView() {
  const productName = document.getElementById('view-name').textContent;
  const currentStock = document.getElementById('view-stock').textContent;
  $('#viewProductModal').modal('hide');
  setTimeout(() => {
    openReceiveStockModal(currentProductId, productName, currentStock);
  }, 300);
}
</script>
@endpush
