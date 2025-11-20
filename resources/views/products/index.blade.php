@extends('layouts.app')

@php($pageTitle = 'Products')

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
  .stat-icon.green { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; }
  .stat-icon.orange { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; }
  .stat-icon.purple { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white; }
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
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">Product Catalog</h1>
    <p class="text-muted mb-0">Master product database - manage pricing, attributes, and product information</p>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
      <i class="fas fa-plus mr-1"></i> Add Product
    </button>
    <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#importModal">
      <i class="fas fa-file-import mr-1"></i> Import
    </button>
    <a href="{{ route('products.export') }}" class="btn btn-outline-secondary">
      <i class="fas fa-file-export mr-1"></i> Export
    </a>
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

@if(session('warning'))
  <div class="alert alert-warning alert-dismissible fade show">
    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
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

<!-- Info Alert -->
<div class="alert alert-info mb-4">
  <i class="fas fa-info-circle mr-2"></i>
  <strong>Product Catalog:</strong> This is your master product database. Manage product details, pricing, and attributes here. 
  For stock operations (adjustments, receiving), go to <a href="/inventory" class="alert-link font-weight-bold">Inventory Management →</a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-boxes"></i>
      </div>
      <div class="stat-label">Total Products</div>
      <div class="stat-value">{{ $products->total() }}</div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="stat-label">Active Products</div>
      <div class="stat-value">{{ $products->where('status', 'active')->count() }}</div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fas fa-tags"></i>
      </div>
      <div class="stat-label">Categories</div>
      <div class="stat-value">{{ $categories->count() }}</div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fas fa-copyright"></i>
      </div>
      <div class="stat-label">Brands</div>
      <div class="stat-value">{{ $brands->count() }}</div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET">
      <div class="row align-items-end">
        <div class="col-md-2 mb-2">
          <label class="small text-muted mb-1">Category</label>
          <select name="category" class="form-control">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 mb-2">
          <label class="small text-muted mb-1">Brand</label>
          <select name="brand" class="form-control">
            <option value="">All Brands</option>
            @foreach($brands as $brand)
              <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 mb-2">
          <label class="small text-muted mb-1">Status</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label class="small text-muted mb-1">Search</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="SKU, name, brand, or model">
        </div>
        <div class="col-md-2 mb-2">
          <button class="btn btn-primary btn-block" type="submit">
            <i class="fas fa-filter mr-1"></i> Filter
          </button>
        </div>
      </div>
      @if(request()->hasAny(['category', 'brand', 'status', 'search']))
        <div class="mt-2">
          <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
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
            <th class="text-center">Status</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Selling Price</th>
            <th class="text-right">Margin</th>
            <th class="text-center">Stock</th>
            <th class="text-right" style="width: 200px;">Actions</th>
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
              <td class="text-center">
                @if($product->status === 'active')
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-secondary">Inactive</span>
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
              <td class="text-right">
                <div style="font-weight: 600; color: {{ $product->margin_color }};">
                  {{ number_format($product->margin, 1) }}%
                </div>
              </td>
              <td class="text-center">
                <span class="stock-badge {{ $product->stock_status }}">{{ $product->stock ?? 0 }}</span>
              </td>
              <td class="text-right">
                <button class="btn btn-sm btn-outline-primary" onclick="viewProduct({{ $product->id }})" title="View Details">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick='editProduct({{ $product->id }}, @json($product))' title="Edit Product">
                  <i class="fas fa-pen"></i>
                </button>
                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;" 
                      onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-5">
                <div style="color: #94A3B8;">
                  <i class="fas fa-box-open fa-3x mb-3" style="opacity: 0.3;"></i>
                  <p class="mb-2" style="font-size: 1.1rem; font-weight: 500;">No products found</p>
                  <p class="mb-0" style="font-size: 0.875rem;">
                    @if(request()->hasAny(['category', 'brand', 'status', 'search']))
                      Try adjusting your filters or <a href="{{ route('products.index') }}">clear filters</a>
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-plus-circle mr-2" style="color: #3B82F6;"></i>
          Add New Product
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="POST" action="{{ route('products.store') }}">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>SKU <span class="text-danger">*</span></label>
                <input type="text" name="sku" class="form-control" placeholder="e.g., MC-125-BLK" required>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g., MotoX 125" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" class="form-control" placeholder="e.g., Yamato">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Model</label>
                <input type="text" name="model" class="form-control" placeholder="e.g., 125cc">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
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
                  <input type="number" name="unit_cost" step="0.01" class="form-control" placeholder="0.00" required>
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
                  <input type="number" name="selling_price" step="0.01" class="form-control" placeholder="0.00" required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Initial Stock</label>
                <input type="number" name="stock" class="form-control" placeholder="0" value="0">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Supplier (note only)</label>
                <input type="text" name="supplier" class="form-control" placeholder="Supplier name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Location <i class="fas fa-map-marker-alt text-muted" style="font-size: 0.8rem;"></i></label>
                <input type="text" name="location" class="form-control" placeholder="e.g., Shelf A3, Warehouse 2">
                <small class="form-text text-muted">Where the item is stored</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Color/Variant <i class="fas fa-palette text-muted" style="font-size: 0.8rem;"></i></label>
                <input type="text" name="color" class="form-control" placeholder="e.g., Red, Black, Blue">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Reorder Level</label>
                <input type="number" name="reorder_level" class="form-control" placeholder="5" value="5">
                <small class="form-text text-muted">Alert when stock reaches this level</small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group mb-0">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Optional product description"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Save Product
          </button>
        </div>
      </form>
    </div>
  </div>
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
                <label>Status</label>
                <select name="status" id="edit-status" class="form-control">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Supplier</label>
                <input type="text" name="supplier" id="edit-supplier" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Reorder Level</label>
                <input type="number" name="reorder_level" id="edit-reorder-level" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Location <i class="fas fa-map-marker-alt text-muted" style="font-size: 0.8rem;"></i></label>
                <input type="text" name="location" id="edit-location" class="form-control" placeholder="e.g., Shelf A3, Warehouse 2">
                <small class="form-text text-muted">Where the item is stored</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Color/Variant <i class="fas fa-palette text-muted" style="font-size: 0.8rem;"></i></label>
                <input type="text" name="color" id="edit-color" class="form-control" placeholder="e.g., Red, Black, Blue">
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
            <strong>Note:</strong> Stock quantities are managed in the Inventory section. Go to Inventory to adjust stock levels.
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
          Product Details
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <!-- Product Status Badge -->
        <div class="mb-3 text-center">
          <span id="view-status-badge" class="badge badge-lg" style="font-size: 1rem; padding: 8px 20px;">-</span>
        </div>

        <!-- Basic Information -->
        <div class="card mb-3">
          <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Basic Information</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-2">
                  <small class="text-muted d-block">SKU</small>
                  <strong id="view-sku">-</strong>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Product Name</small>
                  <strong id="view-name">-</strong>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Category</small>
                  <span id="view-category">-</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-2">
                  <small class="text-muted d-block">Brand</small>
                  <span id="view-brand">-</span>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Model</small>
                  <span id="view-model">-</span>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Supplier</small>
                  <span id="view-supplier">-</span>
                </div>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6">
                <div class="mb-2">
                  <small class="text-muted d-block"><i class="fas fa-map-marker-alt mr-1"></i>Storage Location</small>
                  <span id="view-location" class="font-weight-bold text-primary">-</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-2">
                  <small class="text-muted d-block"><i class="fas fa-palette mr-1"></i>Color/Variant</small>
                  <span id="view-color" class="font-weight-bold text-info">-</span>
                </div>
              </div>
            </div>
            <div class="mt-2">
              <small class="text-muted d-block">Description</small>
              <p id="view-description" class="mb-0">-</p>
            </div>
          </div>
        </div>

        <!-- Pricing Information -->
        <div class="card mb-3">
          <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-dollar-sign mr-2"></i>Pricing Information</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="text-center p-3 border rounded">
                  <small class="text-muted d-block mb-1">Unit Cost</small>
                  <h5 class="mb-0 font-weight-bold" id="view-unit-cost">-</h5>
                </div>
              </div>
              <div class="col-md-4">
                <div class="text-center p-3 border rounded">
                  <small class="text-muted d-block mb-1">Selling Price</small>
                  <h5 class="mb-0 font-weight-bold text-success" id="view-selling-price">-</h5>
                </div>
              </div>
              <div class="col-md-4">
                <div class="text-center p-3 border rounded">
                  <small class="text-muted d-block mb-1">Profit Margin</small>
                  <h5 class="mb-0 font-weight-bold" id="view-margin">-</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Inventory Reference -->
        <div class="card mb-3">
          <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-warehouse mr-2"></i>Inventory Reference</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-2">
                  <small class="text-muted d-block">Current Stock</small>
                  <strong id="view-stock" style="font-size: 1.25rem;">-</strong>
                  <span class="text-muted small">units</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-2">
                  <small class="text-muted d-block">Reorder Level</small>
                  <span id="view-reorder-level">-</span>
                  <span class="text-muted small">units</span>
                </div>
              </div>
            </div>
            <div class="alert alert-info mb-0 mt-3">
              <i class="fas fa-info-circle mr-2"></i>
              <small>
                <strong>Stock Management:</strong> For stock adjustments, receiving, and movement history, 
                go to <a href="/inventory" class="alert-link font-weight-bold">Inventory Management →</a>
              </small>
            </div>
          </div>
        </div>

        <!-- Product History -->
        <div class="card">
          <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Product History</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <small class="text-muted d-block">Created</small>
                <span id="view-created">-</span>
              </div>
              <div class="col-md-6">
                <small class="text-muted d-block">Last Modified</small>
                <span id="view-updated">-</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
        <a href="/inventory" class="btn btn-success">
          <i class="fas fa-warehouse mr-1"></i>Go to Inventory
        </a>
        <button type="button" class="btn btn-primary" onclick="editProductFromView()">
          <i class="fas fa-edit mr-1"></i>Edit Product
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let currentProductId = null;
let currentProductData = null;

function viewProduct(productId) {
  currentProductId = productId;
  
  // Show modal
  $('#viewProductModal').modal('show');
  
  // Fetch product details
  fetch(`/products/${productId}`)
    .then(response => response.json())
    .then(data => {
      const product = data.product;
      currentProductData = product;
      
      // Calculate margin
      const margin = product.unit_cost > 0 
        ? ((product.selling_price - product.unit_cost) / product.unit_cost) * 100 
        : 0;
      
      // Status badge
      const statusBadge = document.getElementById('view-status-badge');
      if (product.status === 'active') {
        statusBadge.className = 'badge badge-lg badge-success';
        statusBadge.textContent = 'Active Product';
      } else {
        statusBadge.className = 'badge badge-lg badge-secondary';
        statusBadge.textContent = 'Inactive Product';
      }
      
      // Basic Information
      document.getElementById('view-sku').textContent = product.sku || '-';
      document.getElementById('view-name').textContent = product.name || '-';
      document.getElementById('view-category').textContent = product.category || '-';
      document.getElementById('view-brand').textContent = product.brand || '-';
      document.getElementById('view-model').textContent = product.model || '-';
      document.getElementById('view-supplier').textContent = product.supplier || '-';
      document.getElementById('view-location').textContent = product.location || '-';
      document.getElementById('view-color').textContent = product.color || '-';
      document.getElementById('view-description').textContent = product.description || 'No description provided';
      
      // Pricing Information
      document.getElementById('view-unit-cost').textContent = product.unit_cost 
        ? `₱${parseFloat(product.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}` 
        : '-';
      document.getElementById('view-selling-price').textContent = product.selling_price 
        ? `₱${parseFloat(product.selling_price).toLocaleString('en-US', {minimumFractionDigits: 2})}` 
        : '-';
      
      // Margin with color
      const marginColor = margin >= 30 ? '#10B981' : margin >= 15 ? '#F59E0B' : '#EF4444';
      document.getElementById('view-margin').innerHTML = `<span style="color: ${marginColor};">${margin.toFixed(1)}%</span>`;
      
      // Inventory Reference
      document.getElementById('view-stock').textContent = product.stock || '0';
      document.getElementById('view-reorder-level').textContent = product.reorder_level || '5';
      
      // Product History
      const createdDate = new Date(product.created_at).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit'
      });
      const updatedDate = new Date(product.updated_at).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit'
      });
      document.getElementById('view-created').textContent = createdDate;
      document.getElementById('view-updated').textContent = updatedDate;
    })
    .catch(error => {
      console.error('Error fetching product:', error);
      alert('Failed to load product details');
      $('#viewProductModal').modal('hide');
    });
}

function editProduct(productId, product) {
  currentProductId = productId;
  currentProductData = product;
  
  // Populate form
  document.getElementById('edit-sku').value = product.sku || '';
  document.getElementById('edit-name').value = product.name || '';
  document.getElementById('edit-brand').value = product.brand || '';
  document.getElementById('edit-model').value = product.model || '';
  document.getElementById('edit-category').value = product.category || '';
  document.getElementById('edit-unit-cost').value = product.unit_cost || '';
  document.getElementById('edit-selling-price').value = product.selling_price || '';
  document.getElementById('edit-status').value = product.status || 'active';
  document.getElementById('edit-reorder-level').value = product.reorder_level || '';
  document.getElementById('edit-supplier').value = product.supplier || '';
  document.getElementById('edit-location').value = product.location || '';
  document.getElementById('edit-color').value = product.color || '';
  document.getElementById('edit-description').value = product.description || '';
  
  // Set form action
  document.getElementById('editProductForm').action = `/products/${productId}`;
  
  // Show modal
  $('#editProductModal').modal('show');
}

function editProductFromView() {
  if (currentProductData) {
    $('#viewProductModal').modal('hide');
    setTimeout(() => {
      editProduct(currentProductId, currentProductData);
    }, 300);
  }
}
</script>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fas fa-file-import mr-2"></i>Import Products from Excel
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Import Instructions:</strong>
            <ul class="mb-0 mt-2">
              <li>Download the template file to see the required format</li>
              <li>Fill in your product data (SKU and Product Name are required)</li>
              <li>If SKU exists, the product will be updated; otherwise, a new product will be created</li>
              <li>Supported formats: Excel (.xlsx, .xls) or CSV (.csv) - max 5MB</li>
            </ul>
          </div>

          <div class="mb-3">
            <a href="{{ route('products.template') }}" class="btn btn-outline-primary btn-block">
              <i class="fas fa-download mr-2"></i>Download Excel Template
            </a>
          </div>

          <div class="form-group">
            <label for="import-file">Select Excel File</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="import-file" name="file" accept=".xlsx,.xls,.csv" required>
              <label class="custom-file-label" for="import-file">Choose file...</label>
            </div>
            <small class="form-text text-muted">
              Accepted formats: Excel (.xlsx, .xls) or CSV (.csv)
            </small>
          </div>

          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Warning:</strong> This will update existing products with matching SKUs. Make sure your data is correct before importing.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-upload mr-1"></i>Import Products
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Update file input label with selected filename
document.getElementById('import-file').addEventListener('change', function(e) {
  const fileName = e.target.files[0]?.name || 'Choose file...';
  const label = e.target.nextElementSibling;
  label.textContent = fileName;
});
</script>

@endpush
