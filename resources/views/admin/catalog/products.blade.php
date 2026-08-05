@extends('layouts.admin')

@section('title', 'Products Catalog - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title h1 { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    .page-title p { color: var(--text-secondary); font-size: 14px; margin-top: 4px; }
    
    .btn { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; border: none; transition: all 0.2s; text-decoration: none; }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-primary:hover { opacity: 0.9; }
    .btn-secondary { background: var(--text-primary); color: var(--bg-color); }
    .btn-secondary:hover { opacity: 0.9; }
    
    .card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    
    .search-box { position: relative; width: 300px; }
    .search-box input { width: 100%; padding: 8px 16px 8px 36px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 14px; }
    .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
    
    .table-responsive { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 12px 24px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; background: rgba(128,128,128,0.02); border-bottom: 1px solid var(--border-color); }
    td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    tr:last-child td { border-bottom: none; }
    tr:hover { background: rgba(128,128,128,0.02); }
    
    .product-info { display: flex; align-items: center; gap: 12px; }
    .product-img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color); }
    .product-details { display: flex; flex-direction: column; }
    .product-name { font-weight: 600; color: var(--text-primary); }
    .product-sku { font-size: 11px; font-family: monospace; color: var(--text-secondary); }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
    .badge-cj { background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); }
    .badge-own { background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
    
    .action-btn { background: transparent; border: none; color: var(--text-secondary); cursor: pointer; padding: 4px; border-radius: 4px; }
    .action-btn:hover { color: var(--text-primary); background: rgba(128,128,128,0.1); }
    .action-btn.delete:hover { color: #ef4444; }

    /* Modal / Form */
    .form-container { display: none; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 4px; }
    .form-group label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); }
    .form-group input, .form-group select, .form-group textarea { padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 14px; }
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Products Catalog</h1>
        <p>Manage your local products or import trending items from CJDropshipping.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.catalog.import') }}" class="btn btn-primary">
            <i data-lucide="download" style="width:16px;"></i> Import Products from CJ
        </a>
        <button class="btn btn-secondary" onclick="toggleForm()">
            <i data-lucide="plus" style="width:16px;"></i> Manual Add Product
        </button>
    </div>
</div>

@if(session('success'))
    <div style="padding: 12px; background: rgba(16, 185, 129, 0.1); color: #059669; border-radius: 8px; margin-bottom: 24px; border: 1px solid rgba(16, 185, 129, 0.2);">
        {{ session('success') }}
    </div>
@endif

<div class="form-container" id="productForm">
    <div class="form-header">
        <h2 style="font-size: 18px; font-weight: 700;">Add Manual Product</h2>
        <button class="action-btn" onclick="toggleForm()"><i data-lucide="x" style="width:20px;"></i></button>
    </div>
    <form action="{{ route('admin.catalog.products.store') }}" method="POST">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" required>
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock_quantity" required>
            </div>
        </div>
        <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Subcategory (Optional)</label>
                <select name="subcategory_id">
                    <option value="">None</option>
                </select>
            </div>
            <div class="form-group">
                <label>Brand</label>
                <select name="brand_id">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 16px;">Create Product</button>
    </form>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 16px;">
        <form method="GET" action="{{ route('admin.catalog.products') }}" style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; flex-grow: 1;">
            <div class="search-box">
                <i data-lucide="search" style="width:16px;"></i>
                <input type="text" name="search" placeholder="Search by name or SKU..." value="{{ request('search') }}">
            </div>

            <select name="category_id" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); font-size: 13px; outline: none;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id || request('category') == $cat->id || request('category') == $cat->slug ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="brand_id" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); font-size: 13px; outline: none;">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>

            <select name="fulfillment_type" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); font-size: 13px; outline: none;">
                <option value="">All Fulfillment</option>
                <option value="cj" {{ request('fulfillment_type') == 'cj' ? 'selected' : '' }}>CJ Dropshipping</option>
                <option value="own" {{ request('fulfillment_type') == 'own' ? 'selected' : '' }}>Own Inventory</option>
            </select>

            @if(request()->hasAny(['search', 'category_id', 'category', 'brand_id', 'fulfillment_type']))
                <a href="{{ route('admin.catalog.products') }}" style="padding: 6px 12px; border-radius: 6px; background: rgba(239,68,68,0.1); color: #ef4444; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    <i data-lucide="x" style="width:14px;"></i> Clear Filters
                </a>
            @endif
        </form>

        <span style="font-size: 12px; color: var(--text-secondary); font-weight: 500;">
            Showing {{ $products->count() }} of {{ $products->total() }} products
        </span>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Fulfillment</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="{{ $product->thumbnail_image ?? 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=400&q=80' }}" class="product-img" alt="">
                                <div class="product-details">
                                    <span class="product-name">{{ $product->name }}</span>
                                    <span class="product-sku">{{ $product->sku }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 700; color: var(--accent);">${{ number_format($product->price, 2) }}</td>
                        <td style="font-weight: 500;">{{ $product->stock_quantity ?? 0 }}</td>
                        <td>
                            @if($product->fulfillment_type == 'cj')
                                <span class="badge badge-cj"><i data-lucide="sparkles" style="width:10px;"></i> CJ Dropship</span>
                            @else
                                <span class="badge badge-own">Own Inventory</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <button class="action-btn"><i data-lucide="edit" style="width:16px;"></i></button>
                            <form action="{{ route('admin.catalog.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete" onclick="return confirm('Delete this product?')"><i data-lucide="trash" style="width:16px;"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 48px;">
                            No products found. Click <strong>Import Products from CJ</strong> to fetch items.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
            {{ $products->links() }}
        </div>
    @endif
</div>

<script>
    function toggleForm() {
        const form = document.getElementById('productForm');
        if(form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            form.style.display = 'block';
        }
    }
</script>
@endsection
