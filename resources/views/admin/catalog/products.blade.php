@extends('layouts.admin')

@section('title', 'Products Catalog - AtoZGadgets Admin')

@section('content')
<style>
    /* Catalog Layout & Spacing */
    .catalog-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .page-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title h1 { 
        font-size: 22px; 
        font-weight: 800; 
        color: var(--text-primary); 
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .page-title p { 
        color: var(--text-secondary); 
        font-size: 13.5px; 
        margin-top: 2px; 
    }
    
    .btn { 
        padding: 9px 16px; 
        border-radius: 8px; 
        font-size: 13px; 
        font-weight: 600; 
        cursor: pointer; 
        display: inline-flex; 
        align-items: center; 
        gap: 8px; 
        border: none; 
        transition: all 0.2s ease; 
        text-decoration: none; 
    }
    .btn:active { transform: scale(0.98); }
    .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(201,169,98,0.25); }
    .btn-primary:hover { opacity: 0.92; color: #fff; }
    .btn-secondary { background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-color); }
    .btn-secondary:hover { background: var(--hover-subtle); color: var(--text-primary); }
    
    /* Stats Bar (KPIs) */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: border-color 0.2s ease;
    }
    .stat-card:hover { border-color: rgba(201,169,98,0.4); }
    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-content { display: flex; flex-direction: column; }
    .stat-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); }
    .stat-value { font-size: 22px; font-weight: 800; color: var(--text-primary); line-height: 1.2; margin-top: 2px; }

    /* Filter & Search Bar */
    .catalog-card { 
        background: var(--bg-card); 
        border: 1px solid var(--border-color); 
        border-radius: 14px; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
        overflow: hidden; 
    }
    .catalog-toolbar { 
        padding: 16px 20px; 
        border-bottom: 1px solid var(--border-color); 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        flex-wrap: wrap; 
        gap: 14px; 
        background: rgba(255,255,255,0.01);
    }
    .filters-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        flex: 1;
    }
    .search-input-wrap { 
        position: relative; 
        min-width: 240px; 
        flex: 1; 
        max-width: 340px; 
    }
    .search-input-wrap input { 
        width: 100%; 
        padding: 8px 14px 8px 36px; 
        border-radius: 8px; 
        border: 1px solid var(--border-color); 
        background: var(--bg-base); 
        color: var(--text-primary); 
        font-size: 13.5px; 
        outline: none; 
        transition: border-color 0.2s;
    }
    .search-input-wrap input:focus { border-color: var(--accent); }
    .search-input-wrap i { 
        position: absolute; 
        left: 11px; 
        top: 50%; 
        transform: translateY(-50%); 
        color: var(--text-secondary); 
    }
    
    .filter-select { 
        padding: 8px 12px; 
        border-radius: 8px; 
        border: 1px solid var(--border-color); 
        background: var(--bg-base); 
        color: var(--text-primary); 
        font-size: 13px; 
        outline: none; 
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .filter-select:focus { border-color: var(--accent); }

    /* Bulk Actions Bar */
    .bulk-bar {
        display: none;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        background: rgba(201,169,98,0.1);
        border-bottom: 1px solid rgba(201,169,98,0.3);
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }
    .bulk-bar.active { display: flex; }
    .bulk-actions { display: flex; gap: 8px; }

    /* Products Table */
    .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { 
        padding: 14px 20px; 
        font-size: 11px; 
        text-transform: uppercase; 
        color: var(--text-secondary); 
        font-weight: 700; 
        letter-spacing: 0.8px;
        background: rgba(128,128,128,0.03); 
        border-bottom: 1px solid var(--border-color); 
    }
    td { 
        padding: 14px 20px; 
        border-bottom: 1px solid var(--border-color); 
        font-size: 13.5px; 
        vertical-align: middle; 
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: rgba(128,128,128,0.025); }
    
    .product-info-cell { display: flex; align-items: center; gap: 14px; min-width: 280px; }
    .product-thumb-box { 
        width: 48px; 
        height: 48px; 
        border-radius: 10px; 
        overflow: hidden; 
        border: 1px solid var(--border-color); 
        background: rgba(128,128,128,0.1); 
        display: flex; 
        align-items: center; 
        justify-content: center;
        flex-shrink: 0;
    }
    .product-img { width: 100%; height: 100%; object-fit: cover; }
    .product-meta { display: flex; flex-direction: column; gap: 3px; }
    .product-title { 
        font-weight: 600; 
        color: var(--text-primary); 
        line-height: 1.35;
        font-size: 13.5px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-tags { display: flex; align-items: center; gap: 8px; font-size: 11px; }
    .product-sku-code { font-family: ui-monospace, monospace; color: var(--text-secondary); }

    /* Badges & Pills */
    .pill { 
        padding: 4px 10px; 
        border-radius: 20px; 
        font-size: 11px; 
        font-weight: 700; 
        display: inline-flex; 
        align-items: center; 
        gap: 5px; 
    }
    .pill-live { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .pill-draft { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .pill-stock-in { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.25); }
    .pill-stock-out { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25); }
    
    .badge-cj { background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.25); }
    .badge-own { background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); }

    /* Actions */
    .table-actions { display: flex; justify-content: flex-end; align-items: center; gap: 6px; }
    .action-btn { 
        width: 32px; 
        height: 32px; 
        border-radius: 8px; 
        border: 1px solid var(--border-color); 
        background: var(--bg-surface); 
        color: var(--text-secondary); 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        cursor: pointer; 
        transition: all 0.2s ease; 
    }
    .action-btn:hover { color: var(--text-primary); border-color: var(--accent); background: var(--hover-subtle); }
    .action-btn.btn-toggle-live:hover { color: #10b981; border-color: rgba(16,185,129,0.4); }
    .action-btn.btn-delete:hover { color: #ef4444; border-color: rgba(239,68,68,0.4); }

    /* Slide-over Drawer / Modal */
    .drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(4px);
        z-index: 999;
        display: none;
        justify-content: flex-end;
        transition: opacity 0.3s ease;
    }
    .drawer-overlay.active { display: flex; }
    .drawer-content {
        width: 100%;
        max-width: 520px;
        background: var(--bg-card);
        height: 100vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        box-shadow: -10px 0 30px rgba(0,0,0,0.4);
        animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes slideInRight {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    .drawer-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        background: var(--bg-card);
        z-index: 10;
    }
    .drawer-header h2 { font-size: 17px; font-weight: 700; color: var(--text-primary); }
    .drawer-body { padding: 24px; flex-grow: 1; }
    .drawer-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        position: sticky;
        bottom: 0;
        background: var(--bg-card);
        z-index: 10;
    }
    
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
    .form-group label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.5px; }
    .form-group input, .form-group select, .form-group textarea { 
        padding: 10px 14px; 
        border-radius: 8px; 
        border: 1px solid var(--border-color); 
        background: var(--bg-base); 
        color: var(--text-primary); 
        font-size: 13.5px; 
        outline: none;
        transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--accent); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
</style>

<div class="catalog-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1><i data-lucide="package" style="color:var(--accent); width:24px;"></i> Products Catalog</h1>
            <p>Manage, review, and stage storefront products across CJ Dropshipping and in-house inventory.</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.catalog.import') }}" class="btn btn-primary">
                <i data-lucide="download-cloud" style="width:16px;"></i> Product Import
            </a>
            <button class="btn btn-secondary" onclick="openAddProductDrawer()">
                <i data-lucide="plus" style="width:16px;"></i> Add Product
            </button>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(201,169,98,0.12); color: var(--accent);">
                <i data-lucide="layers" style="width:20px;"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Total Catalog</span>
                <span class="stat-value">{{ $stats['total'] ?? $products->total() }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16,185,129,0.12); color: #10b981;">
                <i data-lucide="globe" style="width:20px;"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Live on Store</span>
                <span class="stat-value">{{ $stats['live'] ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245,158,11,0.12); color: #f59e0b;">
                <i data-lucide="file-edit" style="width:20px;"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Draft / Staged</span>
                <span class="stat-value">{{ $stats['draft'] ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59,130,246,0.12); color: #3b82f6;">
                <i data-lucide="truck" style="width:20px;"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">CJ Supplier Sourced</span>
                <span class="stat-value">{{ $stats['cj'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Success & Error Alerts -->
    @if(session('success'))
        <div style="padding: 12px 16px; background: rgba(16,185,129,0.1); color: #10b981; border-radius: 10px; border: 1px solid rgba(16,185,129,0.25); display:flex; align-items:center; gap:8px;">
            <i data-lucide="check-circle" style="width:18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 12px 16px; background: rgba(239,68,68,0.1); color: #ef4444; border-radius: 10px; border: 1px solid rgba(239,68,68,0.25);">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li style="font-size: 13px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Catalog Table Card -->
    <div class="catalog-card">
        <!-- Filter Toolbar -->
        <div class="catalog-toolbar">
            <form method="GET" action="{{ route('admin.catalog.products') }}" class="filters-form" id="filtersForm">
                <div class="search-input-wrap">
                    <i data-lucide="search" style="width:16px;"></i>
                    <input type="text" name="search" placeholder="Search name or SKU..." value="{{ request('search') }}">
                </div>

                <select name="category_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id || request('category') == $cat->id || request('category') == $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="brand_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Live Only</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft Only</option>
                </select>

                <select name="fulfillment_type" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Fulfillment</option>
                    <option value="cj" {{ request('fulfillment_type') == 'cj' ? 'selected' : '' }}>Supplier Fulfillment (CJ)</option>
                    <option value="own" {{ request('fulfillment_type') == 'own' ? 'selected' : '' }}>In-House Inventory</option>
                </select>

                @if(request()->hasAny(['search', 'category_id', 'category', 'brand_id', 'status', 'fulfillment_type']))
                    <a href="{{ route('admin.catalog.products') }}" style="padding: 7px 12px; border-radius: 8px; background: rgba(239,68,68,0.1); color: #ef4444; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="x" style="width:14px;"></i> Reset
                    </a>
                @endif
            </form>

            <span style="font-size: 12px; color: var(--text-secondary); font-weight: 500; white-space: nowrap;">
                Showing {{ $products->count() }} of {{ $products->total() }} items
            </span>
        </div>

        <!-- Bulk Action Bar -->
        <div class="bulk-bar" id="bulkBar">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="check-square" style="width:16px; color:var(--accent);"></i>
                <span id="selectedCount">0 products selected</span>
            </div>
            <div class="bulk-actions">
                <button type="button" class="btn btn-secondary" onclick="executeBulkAction('publish')" style="padding: 6px 12px; font-size: 12px;">
                    <i data-lucide="eye" style="width:14px; color:#10b981;"></i> Publish to Live
                </button>
                <button type="button" class="btn btn-secondary" onclick="executeBulkAction('draft')" style="padding: 6px 12px; font-size: 12px;">
                    <i data-lucide="eye-off" style="width:14px; color:#f59e0b;"></i> Move to Draft
                </button>
                <button type="button" class="btn btn-secondary" onclick="executeBulkAction('delete')" style="padding: 6px 12px; font-size: 12px; color:#ef4444;">
                    <i data-lucide="trash-2" style="width:14px;"></i> Delete Selected
                </button>
            </div>
        </div>

        <!-- Table Data -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor: pointer;">
                        </th>
                        <th>Product Info</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Fulfillment</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr id="product-row-{{ $product->id }}">
                            <td style="text-align: center;">
                                <input type="checkbox" class="product-select-checkbox" value="{{ $product->id }}" onchange="updateBulkBarState()" style="cursor: pointer;">
                            </td>
                            <td>
                                <div class="product-info-cell">
                                    <div class="product-thumb-box">
                                        <img src="{{ $product->customer_thumbnail }}" class="product-img" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ asset('favicon.png') }}';">
                                    </div>
                                    <div class="product-meta">
                                        <span class="product-title" title="{{ $product->name }}">{{ $product->name }}</span>
                                        <div class="product-tags">
                                            <span class="product-sku-code">{{ $product->merchant_sku }}</span>
                                            @if($product->category)
                                                <span style="color: var(--text-secondary);">•</span>
                                                <span style="color: var(--accent); font-weight: 500;">{{ $product->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 700; color: var(--accent); font-size: 14.5px;">
                                ${{ number_format($product->price, 2) }}
                                @if($product->discount_price && $product->discount_price > 0 && $product->discount_price < $product->price)
                                    <div style="font-size: 11px; color: var(--text-secondary); text-decoration: line-through;">
                                        ${{ number_format($product->discount_price, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($product->stock_quantity > 0)
                                    <span class="pill pill-stock-in">
                                        <i data-lucide="check" style="width:11px;"></i> {{ $product->stock_quantity }} In Stock
                                    </span>
                                @else
                                    <span class="pill pill-stock-out">
                                        <i data-lucide="alert-circle" style="width:11px;"></i> Out of Stock
                                    </span>
                                @endif
                            </td>
                            <td id="status-cell-{{ $product->id }}">
                                @if($product->status === 'active' && $product->is_active)
                                    <span class="pill pill-live">● Live</span>
                                @else
                                    <span class="pill pill-draft">● Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($product->fulfillment_type == 'cj')
                                    <span class="pill badge-cj">
                                        <i data-lucide="truck" style="width:11px;"></i> Supplier (CJ)
                                    </span>
                                @else
                                    <span class="pill badge-own">
                                        <i data-lucide="home" style="width:11px;"></i> In-House
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="/product/{{ $product->slug }}" target="_blank" class="action-btn" title="View Storefront Page">
                                        <i data-lucide="external-link" style="width:14px;"></i>
                                    </a>
                                    <button type="button" class="action-btn btn-toggle-live" id="status-btn-{{ $product->id }}" onclick="toggleProductLiveStatus({{ $product->id }}, this)" title="{{ $product->status === 'active' && $product->is_active ? 'Unpublish (Move to Draft)' : 'Publish to Live Storefront' }}" style="{{ $product->status === 'active' && $product->is_active ? 'color: #10b981;' : 'color: var(--text-secondary);' }}">
                                        <i data-lucide="{{ $product->status === 'active' && $product->is_active ? 'eye' : 'eye-off' }}" style="width:15px;"></i>
                                    </button>
                                    <button type="button" class="action-btn" onclick="openEditProductDrawer({{ json_encode($product) }})" title="Edit Product">
                                        <i data-lucide="edit" style="width:14px;"></i>
                                    </button>
                                    <button type="button" class="action-btn btn-delete" onclick="showDeleteConfirm({{ $product->id }}, '{{ addslashes($product->name) }}')" title="Delete Product">
                                        <i data-lucide="trash-2" style="width:14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 56px 20px;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                                    <i data-lucide="package-search" style="width:36px; color:var(--text-secondary); opacity:0.6;"></i>
                                    <span style="font-size:15px; font-weight:600; color:var(--text-primary);">No products match your criteria</span>
                                    <p style="font-size:13px; margin:0;">Try clearing filters or stage new products from the import gateway.</p>
                                    <a href="{{ route('admin.catalog.import') }}" class="btn btn-primary" style="margin-top:8px;">
                                        <i data-lucide="download-cloud" style="width:15px;"></i> Product Import
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color); background: rgba(255,255,255,0.01);">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Slide-Out Product Drawer (Add / Edit) -->
<div class="drawer-overlay" id="productDrawerOverlay" onclick="handleDrawerOverlayClick(event)">
    <div class="drawer-content" id="productDrawerContent">
        <div class="drawer-header">
            <h2 id="drawerTitle">Add Manual Product</h2>
            <button type="button" class="action-btn" onclick="closeProductDrawer()"><i data-lucide="x" style="width:16px;"></i></button>
        </div>
        
        <form id="drawerForm" action="{{ route('admin.catalog.products.store') }}" method="POST">
            @csrf
            <div class="drawer-body">
                <div class="form-group">
                    <label>Product Title *</label>
                    <input type="text" name="name" id="formName" required placeholder="e.g. Titanium Smart Ring Health Tracker">
                </div>

                <div class="form-group">
                    <label>URL Slug *</label>
                    <input type="text" name="slug" id="formSlug" required placeholder="e.g. titanium-smart-ring-health-tracker">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="formDescription" rows="4" placeholder="Full product description..."></textarea>
                </div>

                <div class="form-group">
                    <label>Image Thumbnail URL</label>
                    <input type="url" name="thumbnail_image" id="formThumbnail" placeholder="https://example.com/product.jpg">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" step="0.01" name="price" id="formPrice" required placeholder="49.99">
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock_quantity" id="formStock" required placeholder="100" value="100">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" id="formCategory" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" id="formBrand">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="drawer-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProductDrawer()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="drawerSubmitBtn">Create Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Delete Modal -->
<div class="modal-overlay" id="deleteConfirmModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.65); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);">
    <div class="modal-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 440px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 17px; font-weight: 700; color: var(--text-primary); display:flex; align-items:center; gap:8px;">
                <i data-lucide="alert-triangle" style="width:20px; color:#ef4444;"></i> Confirm Delete
            </h3>
            <button onclick="closeDeleteConfirm()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:18px;"></i></button>
        </div>
        <div style="margin-bottom: 24px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.5;">
            Are you sure you want to permanently delete <strong id="deleteProductName" style="color: var(--text-primary);"></strong>? This will remove product variants and media.
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display:flex; justify-content:flex-end; gap: 10px;">
                <button type="button" onclick="closeDeleteConfirm()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444; border-color:#ef4444; color: white;">Delete Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Drawer Management
    function openAddProductDrawer() {
        const overlay = document.getElementById('productDrawerOverlay');
        const title = document.getElementById('drawerTitle');
        const form = document.getElementById('drawerForm');
        const submitBtn = document.getElementById('drawerSubmitBtn');

        form.action = "{{ route('admin.catalog.products.store') }}";
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();

        title.innerText = 'Add Manual Product';
        submitBtn.innerText = 'Create Product';

        document.getElementById('formName').value = '';
        document.getElementById('formSlug').value = '';
        document.getElementById('formDescription').value = '';
        document.getElementById('formPrice').value = '';
        document.getElementById('formStock').value = '100';
        document.getElementById('formCategory').value = '';
        document.getElementById('formBrand').value = '';
        document.getElementById('formThumbnail').value = '';

        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function openEditProductDrawer(product) {
        const overlay = document.getElementById('productDrawerOverlay');
        const title = document.getElementById('drawerTitle');
        const form = document.getElementById('drawerForm');
        const submitBtn = document.getElementById('drawerSubmitBtn');

        form.action = `/admin/catalog/products/${product.id}`;
        
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);
        } else {
            methodInput.value = 'PUT';
        }

        title.innerText = 'Edit Product: ' + (product.name.length > 25 ? product.name.substring(0, 25) + '...' : product.name);
        submitBtn.innerText = 'Update Product';

        document.getElementById('formName').value = product.name || '';
        document.getElementById('formSlug').value = product.slug || '';
        document.getElementById('formDescription').value = product.description || '';
        document.getElementById('formPrice').value = product.price || '';
        document.getElementById('formStock').value = product.stock_quantity ?? 100;
        document.getElementById('formCategory').value = product.category_id || '';
        document.getElementById('formBrand').value = product.brand_id || '';
        document.getElementById('formThumbnail').value = product.thumbnail_image || '';

        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeProductDrawer() {
        const overlay = document.getElementById('productDrawerOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function handleDrawerOverlayClick(e) {
        if (e.target.id === 'productDrawerOverlay') {
            closeProductDrawer();
        }
    }

    // Auto-generate slug from name in Add mode
    document.getElementById('formName').addEventListener('input', function() {
        const methodInput = document.getElementById('drawerForm').querySelector('input[name="_method"]');
        if (!methodInput) { // only in add mode
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
            document.getElementById('formSlug').value = slug;
        }
    });

    // Delete Modal
    function showDeleteConfirm(id, name) {
        const modal = document.getElementById('deleteConfirmModal');
        document.getElementById('deleteProductName').innerText = name;
        document.getElementById('deleteForm').action = `/admin/catalog/products/${id}`;
        modal.style.display = 'flex';
    }

    function closeDeleteConfirm() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }

    // Live / Draft Status Switcher
    async function toggleProductLiveStatus(productId, btn) {
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i data-lucide="loader-2" class="lucide-spin" style="width:14px;"></i>`;
        if (window.lucide) lucide.createIcons();

        try {
            const res = await fetch(`/admin/catalog/products/${productId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                const statusCell = document.getElementById(`status-cell-${productId}`);
                const isNowActive = data.is_active || data.status === 'active';
                
                if (statusCell) {
                    if (isNowActive) {
                        statusCell.innerHTML = `<span class="pill pill-live">● Live</span>`;
                    } else {
                        statusCell.innerHTML = `<span class="pill pill-draft">● Draft</span>`;
                    }
                }

                btn.title = isNowActive ? 'Unpublish (Move to Draft)' : 'Publish to Live Storefront';
                btn.style.color = isNowActive ? '#10b981' : 'var(--text-secondary)';
                btn.innerHTML = `<i data-lucide="${isNowActive ? 'eye' : 'eye-off'}" style="width:15px;"></i>`;
                if (window.lucide) lucide.createIcons();

                if (window.showAdminToast) {
                    window.showAdminToast(data.message || (isNowActive ? 'Product published to live storefront 🟢' : 'Product moved to draft 🟡'));
                }
            } else {
                alert(data.message || 'Failed to update status');
                btn.innerHTML = origHtml;
                if (window.lucide) lucide.createIcons();
            }
        } catch (err) {
            console.error('Status toggle error:', err);
            alert('Network error while updating product status.');
            btn.innerHTML = origHtml;
            if (window.lucide) lucide.createIcons();
        } finally {
            btn.disabled = false;
        }
    }

    // Bulk Select & Actions
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.product-select-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateBulkBarState();
    }

    function updateBulkBarState() {
        const checked = document.querySelectorAll('.product-select-checkbox:checked');
        const bulkBar = document.getElementById('bulkBar');
        const countSpan = document.getElementById('selectedCount');
        const master = document.getElementById('selectAllCheckbox');

        if (checked.length > 0) {
            bulkBar.classList.add('active');
            countSpan.innerText = `${checked.length} product${checked.length > 1 ? 's' : ''} selected`;
        } else {
            bulkBar.classList.remove('active');
            if (master) master.checked = false;
        }
    }

    async function executeBulkAction(action) {
        const checked = Array.from(document.querySelectorAll('.product-select-checkbox:checked')).map(cb => parseInt(cb.value));
        if (checked.length === 0) return;

        if (action === 'delete') {
            if (!confirm(`Are you sure you want to permanently delete ${checked.length} selected products?`)) {
                return;
            }
        }

        try {
            const res = await fetch("{{ route('admin.catalog.products.bulk_action') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    ids: checked
                })
            });

            const data = await res.json();
            if (data.success) {
                if (window.showAdminToast) {
                    window.showAdminToast(data.message, 'success');
                }
                setTimeout(() => window.location.reload(), 700);
            } else {
                alert(data.message || 'Bulk action failed.');
            }
        } catch (e) {
            console.error('Bulk error:', e);
            alert('Failed to execute bulk action.');
        }
    }
</script>
@endsection
