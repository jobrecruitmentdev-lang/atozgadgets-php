@extends('layouts.admin')

@section('title', 'CJ Dropshipping Gateway - AtoZGadgets Admin')

@section('content')
<style>
    /* Universal Icon & Typography Alignment */
    svg.lucide, .lucide, i[data-lucide] {
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        flex-shrink: 0;
        box-sizing: border-box;
    }

    .page-header { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; }
    .page-title { margin-bottom: 16px; }
    .page-title h1 { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .page-title p { color: var(--text-secondary); font-size: 14px; }
    
    .api-health { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 600; background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
    
    .tabs-container { display: flex; gap: 8px; background: rgba(128,128,128,0.05); padding: 6px; border-radius: 12px; border: 1px solid var(--border-color); display: inline-flex; }
    .tab-btn { padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; background: transparent; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .tab-btn.active { background: var(--accent); color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .tab-btn:hover:not(.active) { color: var(--text-primary); }

    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Clean Toolbar */
    .import-toolbar { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
    .toolbar-header { font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 16px; color: var(--text-primary); }
    
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: start; }
    .form-group { display: flex; flex-direction: column; gap: 6px; position: relative; }
    .form-group label { font-size: 12px; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; }
    .form-group input, .form-group select { height: 42px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border-color); background: rgba(15, 15, 20, 0.7); color: var(--text-primary); font-size: 13px; outline: none; transition: border 0.2s; box-sizing: border-box; }
    .form-group input:focus, .form-group select:focus { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(201, 169, 98, 0.2); }
    .form-group select option { background: #121217; color: #fff; }

    /* Searchable Category Combobox */
    .cat-combo-wrapper { position: relative; width: 100%; }
    .cat-input-box { position: relative; display: flex; align-items: center; }
    .cat-input-box input { width: 100%; height: 42px; padding: 0 32px 0 12px; border-radius: 10px; border: 1px solid var(--border-color); background: rgba(15, 15, 20, 0.7); color: var(--text-primary); font-size: 13px; outline: none; box-sizing: border-box; transition: all 0.2s; }
    .cat-input-box input:focus { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(201, 169, 98, 0.2); }
    .cat-clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: var(--text-secondary); font-size: 16px; cursor: pointer; padding: 4px; display: none; line-height: 1; }
    .cat-clear-btn:hover { color: var(--text-primary); }
    .cat-dropdown-panel { position: absolute; top: calc(100% + 4px); left: 0; right: 0; max-height: 250px; overflow-y: auto; background: #121217; border: 1px solid var(--border-color); border-radius: 10px; box-shadow: 0 12px 30px rgba(0,0,0,0.6); z-index: 999; display: none; }
    .cat-dropdown-panel.show { display: block; }
    .cat-item { padding: 9px 12px; font-size: 13px; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s; }
    .cat-item:hover, .cat-item.active { background: rgba(201, 169, 98, 0.15); color: var(--accent); }
    .cat-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; background: rgba(128,128,128,0.15); color: var(--text-secondary); }

    /* Quick Filter Chips */
    .quick-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 12px; align-items: center; }
    .quick-chip { font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 16px; background: rgba(128,128,128,0.08); border: 1px solid var(--border-color); color: var(--text-secondary); cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; }
    .quick-chip:hover, .quick-chip.active { background: rgba(201, 169, 98, 0.15); border-color: var(--accent); color: var(--accent); }
    
    /* Search Bar Area */
    .search-area { position: relative; margin-top: 18px; display: flex; gap: 12px; align-items: stretch; }
    .search-input-wrapper { flex: 1; position: relative; display: flex; align-items: center; }
    .search-input-wrapper .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); pointer-events: none; display: flex; align-items: center; justify-content: center; z-index: 2; }
    .search-input-wrapper input { width: 100%; height: 46px; padding: 0 16px 0 44px; border-radius: 10px; border: 1px solid var(--border-color); background: rgba(15, 15, 20, 0.7); color: var(--text-primary); font-size: 14px; font-weight: 500; outline: none; transition: all 0.2s; box-sizing: border-box; }
    .search-input-wrapper input:focus { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(201, 169, 98, 0.2); }
    
    .btn-search { padding: 0 24px; height: 46px; border-radius: 10px; font-weight: 700; font-size: 13px; border: none; background: var(--accent); color: #0a0a0c; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(201, 169, 98, 0.2); white-space: nowrap; flex-shrink: 0; }
    .btn-search:hover { opacity: 0.9; transform: translateY(-1px); }
    
    /* Results Grid */
    .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 24px; transition: opacity 0.2s; }
    .product-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; position: relative; transition: all 0.2s; }
    .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); border-color: var(--accent); }
    .product-img { width: 100%; height: 180px; object-fit: contain; padding: 16px; background: rgba(128,128,128,0.05); }
    .product-content { padding: 16px; }
    .product-category { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 8px; }
    .product-title { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; height: 38px; }
    
    .price-calc { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; background: rgba(128,128,128,0.05); padding: 12px; border-radius: 12px; margin-bottom: 16px; }
    .price-calc-item { display: flex; flex-direction: column; }
    .price-label { font-size: 10px; color: var(--text-secondary); }
    .price-value { font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .profit-row { grid-column: span 2; border-top: 1px solid rgba(128,128,128,0.1); padding-top: 8px; margin-top: 4px; display: flex; justify-content: space-between; align-items: center; }
    .profit-val { font-size: 12px; font-weight: 700; color: #059669; }
    
    .btn-import { width: 100%; padding: 10px; border-radius: 10px; background: var(--accent); color: #fff; border: none; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 6px; transition: all 0.2s; }
    .btn-import:hover { opacity: 0.9; }
    .btn-import.success { background: #059669; }

    /* Table Styles */
    .table-responsive { width: 100%; overflow-x: auto; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 16px 24px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); background: rgba(128,128,128,0.02); }
    td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .badge-cj { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); display: inline-flex; align-items: center; gap: 4px; }
    
    .action-btn { background: transparent; border: none; color: var(--text-secondary); cursor: pointer; padding: 4px; border-radius: 4px; display: inline-flex; }
    .action-btn:hover { color: var(--text-primary); background: rgba(128,128,128,0.1); }
    .action-btn.delete:hover { color: #ef4444; }

    /* Modal / Form */
    .form-container { display: none; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .form-header h2 { font-size: 18px; font-weight: 700; }
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px); }
    .modal-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 400px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .modal-header h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); }
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Product Import Pipeline & Quality Gate</h1>
        <div style="margin-top: 12px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @php
                $isSandbox = \App\Services\Cj\CjAuthService::isSandboxMode();
            @endphp
            <span id="gatewayStatusBadge" class="api-health" style="{{ $isSandbox ? 'background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);' : '' }}">
                <i data-lucide="{{ $isSandbox ? 'alert-triangle' : 'activity' }}" style="width:14px;"></i> 
                <span id="gatewayStatusText">{{ $isSandbox ? 'Supplier Provider · Sandbox Mode' : 'Supplier Provider · Live API' }}</span>
            </span>
            <button type="button" onclick="toggleSandboxGateway()" id="btnGatewayToggle" style="padding: 5px 14px; font-size: 12px; font-weight: 700; border-radius: 20px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-primary); cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <i data-lucide="refresh-cw" style="width:12px;"></i>
                <span id="btnGatewayToggleText">{{ $isSandbox ? 'Switch to Live API' : 'Switch to Sandbox' }}</span>
            </button>
        </div>
    </div>
    
    <div class="tabs-container">
        <button class="tab-btn active" onclick="switchTab('staged')" id="tab-btn-staged">
            <i data-lucide="database" style="width:16px;"></i> Staged Catalog Products ({{ $stagedProducts->count() }})
        </button>
        <button class="tab-btn" onclick="switchTab('fetch')" id="tab-btn-fetch">
            <i data-lucide="download-cloud" style="width:16px;"></i> Fetch from Supplier Catalog
        </button>
    </div>
</div>

@if(session('success'))
    <div style="padding: 12px 16px; background: rgba(16,185,129,0.1); color: #059669; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(16,185,129,0.2);">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="padding: 12px 16px; background: rgba(239,68,68,0.1); color: #ef4444; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(239,68,68,0.2);">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li style="font-size: 13px;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div style="padding: 12px 16px; background: rgba(239,68,68,0.1); color: #dc2626; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(239,68,68,0.2);">
        {{ session('error') }}
    </div>
@endif

<div class="form-container" id="productForm">
    <div class="form-header">
        <h2>Edit Product</h2>
        <button class="action-btn" onclick="toggleForm()"><i data-lucide="x" style="width:20px;"></i></button>
    </div>
    <form action="" method="POST">
        @csrf
        @method('PUT')
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
        <div class="form-group" style="margin-bottom: 16px; margin-top: 16px;">
            <label>Description</label>
            <textarea name="description" rows="3" style="padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); font-size: 14px; outline: none;"></textarea>
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label>Image URL</label>
            <input type="url" name="thumbnail_image" placeholder="https://example.com/image.jpg">
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
        <div class="form-grid" style="margin-top: 16px;">
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
                <label>Brand (Optional)</label>
                <select name="brand_id">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" style="margin-top: 24px; padding: 10px 24px; border-radius: 8px; font-weight: 600; background: var(--accent); color: white; border: none; cursor: pointer;">Update Product</button>
    </form>
</div>

<!-- STAGED PRODUCTS TAB -->
<div id="tab-staged" class="tab-content active">
    @if($stagedProducts->count() == 0)
        <div style="text-align: center; padding: 64px 24px; background: var(--bg-color); border: 1px dashed var(--border-color); border-radius: 16px;">
            <i data-lucide="database" style="width:48px; height:48px; color: var(--accent); opacity: 0.2; margin-bottom: 16px;"></i>
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">No CJ Products Staged Yet</h2>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">Old test products have been purged. Switch to the Fetch tab to search and import fresh items into your website database!</p>
            <button class="btn-import" style="width: auto; margin: 0 auto; padding: 12px 24px;" onclick="switchTab('fetch')">
                <i data-lucide="plus" style="width:16px;"></i> Fetch Products from CJ Catalog
            </button>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Selling Price</th>
                        <th>Status</th>
                        <th>Fulfillment</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stagedProducts as $prod)
                        <tr>
                            <td style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ $prod->thumbnail_image ?: asset('favicon.png') }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);" alt="">
                                <div style="display:flex; flex-direction:column;">
                                    <span style="font-weight: 600;">{{ $prod->name }}</span>
                                    <a href="{{ route('store.product', $prod->slug) }}" target="_blank" style="font-size: 11px; color: var(--accent); text-decoration: none; display: inline-flex; align-items: center; gap: 3px; margin-top: 2px;">
                                        View on Storefront <i data-lucide="external-link" style="width:10px !important; height:10px !important;"></i>
                                    </a>
                                </div>
                            </td>
                            <td style="font-family: monospace; font-size: 12px; color: var(--text-secondary);">{{ $prod->sku }}</td>
                            <td style="font-weight: 700; color: var(--accent);">${{ number_format($prod->price, 2) }}</td>
                            <td>
                                @if($prod->status === 'active' && $prod->is_active)
                                    <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); display: inline-flex; align-items: center; gap: 4px;">
                                        ● Live
                                    </span>
                                @else
                                    <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); display: inline-flex; align-items: center; gap: 4px;">
                                        ● Draft
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge-cj"><i data-lucide="sparkles" style="width:10px;"></i> CJ Dropship</span></td>
                            <td style="text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                <form action="{{ route('admin.catalog.products.toggle_status', $prod->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn" title="{{ $prod->status === 'active' ? 'Unpublish (Move to Draft)' : 'Publish to Live Storefront' }}" style="{{ $prod->status === 'active' ? 'color: #10b981;' : 'color: var(--text-secondary);' }}">
                                        <i data-lucide="{{ $prod->status === 'active' ? 'eye' : 'eye-off' }}" style="width:16px;"></i>
                                    </button>
                                </form>
                                <button class="action-btn" onclick="openEditProduct({{ json_encode($prod) }})" title="Edit Product"><i data-lucide="edit" style="width:16px;"></i></button>
                                <button class="action-btn delete" onclick="showDeleteConfirm({{ $prod->id }}, '{{ addslashes($prod->name) }}')" title="Delete Product"><i data-lucide="trash-2" style="width:16px;"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- FETCH FROM CJ TAB -->
<div id="tab-fetch" class="tab-content">
    <div class="import-toolbar">
        <div class="toolbar-header">
            <i data-lucide="layers" style="width:18px; color:var(--accent);"></i>
            Catalog Import & Pricing Parameters
        </div>        
        <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="form-group">
                <label><i data-lucide="filter"></i> CJ Supplier Category (680+ Types)</label>
                <div class="cat-combo-wrapper" id="catComboWrapper">
                    <div class="cat-input-box">
                        <input type="text" id="cjCategorySearchInput" placeholder="Search 680+ categories..." autocomplete="off">
                        <button type="button" class="cat-clear-btn" id="btnClearCategory" title="Clear category">&times;</button>
                    </div>
                    <input type="hidden" id="cjCategoryFilter" value="">
                    <div class="cat-dropdown-panel" id="cjCategoryDropdown">
                        <div class="cat-item active" data-id="" data-name="all cj categories">
                            <span class="cat-item-name">All CJ Categories (Entire Catalog)</span>
                            <span class="cat-badge">All</span>
                        </div>
                        @if(isset($cjCategories))
                            @foreach($cjCategories as $cjCat)
                                <div class="cat-item" data-id="{{ $cjCat['id'] }}" data-name="{{ strtolower($cjCat['name']) }}">
                                    <span class="cat-item-name">{{ $cjCat['name'] }}</span>
                                    <span class="cat-badge">{{ ($cjCat['level'] ?? 1) == 1 ? 'Main' : 'Sub' }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label><i data-lucide="map-pin"></i> Warehouse / Country</label>
                <select id="countryFilter">
                    <option value="">All Global Warehouses</option>
                    <option value="US">🇺🇸 US Warehouse (Fast Ship)</option>
                    <option value="CN">🇨🇳 CN Central Warehouse</option>
                    <option value="DE">🇩🇪 DE European Warehouse</option>
                    <option value="GB">🇬🇧 GB United Kingdom</option>
                </select>
            </div>
            <div class="form-group">
                <label><i data-lucide="dollar-sign"></i> Price Range ($)</label>
                <div style="display:flex; gap:6px; align-items:center;">
                    <input type="number" id="minPrice" placeholder="Min" style="width:50%;" min="0">
                    <span style="color:var(--text-secondary);">-</span>
                    <input type="number" id="maxPrice" placeholder="Max" style="width:50%;" min="0">
                </div>
            </div>
            <div class="form-group">
                <label><i data-lucide="tag"></i> Storefront Destination</label>
                <select id="importCategory">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Quick Filter Chips -->
        <div class="quick-chips">
            <span style="font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-right: 4px;">Quick Categories:</span>
            <button type="button" class="quick-chip active" onclick="selectQuickCategory('', 'All Categories')">🌐 All</button>
            <button type="button" class="quick-chip" onclick="selectQuickCategory('sweatshirt', 'Sweatshirts & Apparel')">👕 Sweatshirts</button>
            <button type="button" class="quick-chip" onclick="selectQuickCategory('projector', 'Projectors')">📽️ Projectors</button>
            <button type="button" class="quick-chip" onclick="selectQuickCategory('watch', 'Smart Watches')">⌚ Smart Watches</button>
            <button type="button" class="quick-chip" onclick="selectQuickCategory('drone', 'Drones & Toys')">🚁 Drones</button>
            <button type="button" class="quick-chip" onclick="selectQuickCategory('lamp', 'Smart Lamps')">💡 Smart Lamps</button>
            <button type="button" class="quick-chip" onclick="selectQuickCategory('speaker', 'Audio & Speakers')">🔊 Speakers</button>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:14px; padding-top:14px; border-top:1px solid var(--border-color);">
            <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-primary); cursor:pointer;">
                <input type="checkbox" id="publishNow" style="accent-color:var(--accent); width:16px; height:16px; cursor:pointer;">
                <span>Publish Immediately to Live Store (Default: <strong>Draft / Review</strong>)</span>
            </label>
            <div style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:6px;">
                <i data-lucide="shield-check" style="width:14px; color:var(--accent);"></i> PID vs VID Auto-Variant Splitting & Tiered Pricing Active
            </div>
        </div>
        
        <div class="search-area" style="margin-top:14px;">
            <div class="search-input-wrapper">
                <span class="search-icon"><i data-lucide="search"></i></span>
                <input type="text" id="searchInput" placeholder="Search CJ Dropshipping catalog (e.g. smartwatch, projector, drone)..." onkeypress="if(event.key === 'Enter') { searchCJ(); }">
            </div>
            <button class="btn-search" onclick="searchCJ()" id="searchBtn">
                <i data-lucide="sparkles" id="searchIcon"></i> Fetch Products
            </button>
        </div>
    </div>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 600;" id="resultsCount">0 Products Found</h3>
        <div style="display: flex; align-items: center; gap: 12px;">
            <label style="font-size: 13px; color: var(--text-secondary); font-weight: 500;">Sort by:</label>
            <select id="sortSelect" onchange="sortAndRenderResults()" style="padding: 8px 12px; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px; outline: none; cursor: pointer;">
                <option value="best_match">Best Match</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="newest">Newest First</option>
                <option value="lists">Most Listed (Lists)</option>
                <option value="inventory">Inventory (Highest)</option>
            </select>
        </div>
    </div>

    <div id="resultsGrid" class="results-grid"></div>
</div>

<script>
    function toggleForm() {
        const form = document.getElementById('productForm');
        form.style.display = 'none';
    }

    function openEditProduct(product) {
        const form = document.getElementById('productForm');
        const formEl = form.querySelector('form');
        
        formEl.action = `/admin/catalog/products/${product.id}`;
        
        formEl.querySelector('input[name="name"]').value = product.name;
        formEl.querySelector('input[name="slug"]').value = product.slug;
        formEl.querySelector('textarea[name="description"]').value = product.description || '';
        formEl.querySelector('input[name="price"]').value = product.price;
        formEl.querySelector('input[name="stock_quantity"]').value = product.stock_quantity;
        formEl.querySelector('select[name="category_id"]').value = product.category_id;
        formEl.querySelector('select[name="brand_id"]').value = product.brand_id || '';
        formEl.querySelector('input[name="thumbnail_image"]').value = product.thumbnail_image || '';
        
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    }

    function showDeleteConfirm(id, name) {
        const modal = document.getElementById('deleteConfirmModal');
        document.getElementById('deleteProductName').innerText = name;
        document.getElementById('deleteForm').action = `/admin/catalog/products/${id}`;
        modal.style.display = 'flex';
    }

    function closeDeleteConfirm() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }

    function switchTab(tab) {
        document.getElementById('tab-staged').classList.remove('active');
        document.getElementById('tab-fetch').classList.remove('active');
        document.getElementById('tab-btn-staged').classList.remove('active');
        document.getElementById('tab-btn-fetch').classList.remove('active');
        
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('tab-btn-' + tab).classList.add('active');

        if (tab === 'fetch' && searchResults.length === 0) {
            searchCJ();
        }
    }

    let searchResults = [];
    let activeSearchController = null;
    let searchRequestId = 0;

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const country = document.getElementById('countryFilter');

        const catSearchInput = document.getElementById('cjCategorySearchInput');
        const catDropdown = document.getElementById('cjCategoryDropdown');
        const catHidden = document.getElementById('cjCategoryFilter');
        const btnClearCat = document.getElementById('btnClearCategory');

        const debouncedSearch = debounce(() => searchCJ(), 300);

        if (searchInput) searchInput.addEventListener('input', debouncedSearch);
        if (minPrice) minPrice.addEventListener('input', debouncedSearch);
        if (maxPrice) maxPrice.addEventListener('input', debouncedSearch);
        if (country) country.addEventListener('change', () => searchCJ());

        // Category Combobox Dropdown Logic
        if (catSearchInput && catDropdown) {
            catSearchInput.addEventListener('focus', () => {
                catDropdown.classList.add('show');
            });

            catSearchInput.addEventListener('input', () => {
                catDropdown.classList.add('show');
                const term = catSearchInput.value.toLowerCase().trim();
                if (term) {
                    btnClearCat.style.display = 'block';
                } else {
                    btnClearCat.style.display = 'none';
                    catHidden.value = '';
                }

                const items = catDropdown.querySelectorAll('.cat-item');
                let matchCount = 0;
                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    if (name.includes(term) || !term) {
                        item.style.display = 'flex';
                        matchCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                debouncedSearch();
            });

            // Click option in category dropdown
            catDropdown.addEventListener('click', (e) => {
                const item = e.target.closest('.cat-item');
                if (!item) return;

                const catId = item.getAttribute('data-id') || '';
                const catName = item.querySelector('.cat-item-name').innerText;

                catHidden.value = catId;
                catSearchInput.value = catId ? catName : '';
                btnClearCat.style.display = catId ? 'block' : 'none';

                catDropdown.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
                item.classList.add('active');
                catDropdown.classList.remove('show');

                searchCJ();
            });

            // Clear Category Button
            if (btnClearCat) {
                btnClearCat.addEventListener('click', () => {
                    catHidden.value = '';
                    catSearchInput.value = '';
                    btnClearCat.style.display = 'none';
                    catDropdown.querySelectorAll('.cat-item').forEach(el => {
                        el.style.display = 'flex';
                        el.classList.remove('active');
                    });
                    const allItem = catDropdown.querySelector('.cat-item[data-id=""]');
                    if (allItem) allItem.classList.add('active');
                    searchCJ();
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                const wrapper = document.getElementById('catComboWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    catDropdown.classList.remove('show');
                }
            });
        }
    });

    function selectQuickCategory(kw, label) {
        document.querySelectorAll('.quick-chip').forEach(btn => btn.classList.remove('active'));
        if (event && event.target) {
            event.target.classList.add('active');
        }

        const catHidden = document.getElementById('cjCategoryFilter');
        const catSearchInput = document.getElementById('cjCategorySearchInput');
        const btnClearCat = document.getElementById('btnClearCategory');
        const searchInput = document.getElementById('searchInput');

        if (!kw) {
            // All
            if (catHidden) catHidden.value = '';
            if (catSearchInput) catSearchInput.value = '';
            if (btnClearCat) btnClearCat.style.display = 'none';
            if (searchInput) searchInput.value = '';
        } else {
            if (searchInput) searchInput.value = kw;
        }

        searchCJ();
    }

    function calculateTieredPrice(cost) {
        let mult = 2.0;
        let ship = 5.0;
        if (cost < 10.0) {
            mult = 2.5;
            ship = 3.0;
        } else if (cost <= 50.0) {
            mult = 2.0;
            ship = 5.0;
        } else {
            mult = 1.6;
            ship = 8.0;
        }
        let raw = (cost * mult) + ship;
        let rounded = Math.floor(raw) + 0.99;
        if (rounded < raw) rounded += 1.0;
        let profit = rounded - cost;
        let msrp = rounded * 1.35;
        return {
            sellingPrice: rounded.toFixed(2),
            msrp: msrp.toFixed(2),
            profit: profit.toFixed(2),
            marginPercent: ((profit / rounded) * 100).toFixed(0)
        };
    }

    async function searchCJ() {
        const keyword = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
        const cjCategory = document.getElementById('cjCategoryFilter') ? document.getElementById('cjCategoryFilter').value : '';
        const country = document.getElementById('countryFilter') ? document.getElementById('countryFilter').value : '';
        const minPrice = document.getElementById('minPrice') ? document.getElementById('minPrice').value : '';
        const maxPrice = document.getElementById('maxPrice') ? document.getElementById('maxPrice').value : '';
        const searchBtn = document.getElementById('searchBtn');
        const resultsGrid = document.getElementById('resultsGrid');
        
        // 1. Cancel previous pending request to prevent race conditions
        if (activeSearchController) {
            activeSearchController.abort();
        }
        activeSearchController = new AbortController();
        const signal = activeSearchController.signal;
        const currentId = ++searchRequestId;

        if (searchBtn) {
            searchBtn.disabled = true;
            searchBtn.innerHTML = `<i data-lucide="loader-2" class="lucide-spin" style="width:16px;"></i> Fetching...`;
            if (window.lucide) lucide.createIcons();
        }

        // 2. Non-destructive loading indicator
        if (searchResults.length === 0) {
            resultsGrid.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 48px 24px; color: var(--text-secondary);">
                    <div style="margin-bottom: 12px;"><i data-lucide="loader-2" class="lucide-spin" style="width:32px; height:32px; color: var(--accent);"></i></div>
                    <div style="font-size: 15px; font-weight: 600; color: var(--text-primary);">Querying CJDropshipping Catalog...</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Applying category, price range, and warehouse filters</div>
                </div>
            `;
            if (window.lucide) lucide.createIcons();
        } else {
            resultsGrid.style.opacity = '0.5';
            resultsGrid.style.pointerEvents = 'none';
        }
        
        try {
            let url = `/admin/api/catalog/search?keyword=${encodeURIComponent(keyword)}`;
            if (cjCategory) url += `&categoryId=${encodeURIComponent(cjCategory)}`;
            if (country) url += `&countryCode=${encodeURIComponent(country)}`;
            if (minPrice) url += `&minPrice=${encodeURIComponent(minPrice)}`;
            if (maxPrice) url += `&maxPrice=${encodeURIComponent(maxPrice)}`;

            const response = await fetch(url, { signal });
            const data = await response.json();
            
            // Discard stale response if a newer search was initiated
            if (currentId !== searchRequestId) {
                return;
            }

            const items = (data.data && data.data.list) ? data.data.list : [];
            
            if(items.length > 0) {
                searchResults = items;
                const totalText = data.data.total || searchResults.length;
                document.getElementById('resultsCount').innerText = `${totalText} Products Found`;
                sortAndRenderResults();
            } else {
                searchResults = [];
                document.getElementById('resultsCount').innerText = `0 Products Found`;
                resultsGrid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 48px 24px; color: var(--text-secondary);">
                        <i data-lucide="package-search" style="width:36px; height:36px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <div style="font-size: 15px; font-weight: 600; color: var(--text-primary);">No products found matching your criteria.</div>
                        <div style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Try clearing filters or searching for terms like "smartwatch", "projector", "drone", or "speaker".</div>
                    </div>
                `;
                if (window.lucide) lucide.createIcons();
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return; // Silently ignore canceled stale requests
            }
            if (currentId !== searchRequestId) {
                return;
            }
            resultsGrid.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #ef4444;">
                    <i data-lucide="alert-circle" style="width:32px; height:32px; margin-bottom: 8px;"></i>
                    <div style="font-weight: 600;">Error fetching data from CJ API</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Please verify your connection or switch to Sandbox mode.</div>
                </div>
            `;
            if (window.lucide) lucide.createIcons();
        } finally {
            if (currentId === searchRequestId) {
                resultsGrid.style.opacity = '1';
                resultsGrid.style.pointerEvents = 'auto';
                if (searchBtn) {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = `<i data-lucide="sparkles" style="width:16px;"></i> Fetch Products`;
                    if (window.lucide) lucide.createIcons();
                }
            }
        }
    }
    
    function sortAndRenderResults() {
        const sortType = document.getElementById('sortSelect').value;
        
        if(sortType === 'price_asc') {
            searchResults.sort((a, b) => (parseFloat(a.sellPrice) || 0) - (parseFloat(b.sellPrice) || 0));
        } else if(sortType === 'price_desc') {
            searchResults.sort((a, b) => (parseFloat(b.sellPrice) || 0) - (parseFloat(a.sellPrice) || 0));
        } else if(sortType === 'newest') {
            searchResults.reverse();
        }
        
        renderResults();
    }
    
    function renderResults() {
        const resultsGrid = document.getElementById('resultsGrid');
        resultsGrid.innerHTML = '';
        
        searchResults.forEach(item => {
            const costPrice = parseFloat(item.sellPrice) || 10.0;
            const pricing = calculateTieredPrice(costPrice);
            const cleanTitle = (item.productNameEn || '').replace(/</g, "&lt;").replace(/>/g, "&gt;");
            
            const imgUrl = item.productImage || 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?q=80&w=600&auto=format&fit=crop';
            
            const card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML = `
                <img src="${imgUrl}" class="product-img" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1526738549149-8e07eca6c147?q=80&w=600&auto=format&fit=crop';" alt="">
                <div class="product-content">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="product-category">${item.categoryName || 'Consumer Electronics'}</span>
                        <span style="font-size:10px; font-weight:700; color:var(--accent); background:rgba(201,169,98,0.15); padding:2px 6px; border-radius:4px;">PID Mapped</span>
                    </div>
                    <div class="product-title" title="${cleanTitle}">${cleanTitle}</div>
                    
                    <div class="price-calc">
                        <div class="price-calc-item">
                            <span class="price-label">Supplier Cost</span>
                            <span class="price-value">$${costPrice.toFixed(2)}</span>
                        </div>
                        <div class="price-calc-item" style="text-align: right;">
                            <span class="price-label">Selling Price</span>
                            <span class="price-value" style="color: var(--accent);">$${pricing.sellingPrice} <span style="font-size: 10px; text-decoration: line-through; color: #9ca3af;">$${pricing.msrp}</span></span>
                        </div>
                        <div class="profit-row">
                            <span class="price-label">Est. Profit/Unit:</span>
                            <span class="profit-val" style="color: var(--accent);">+$${pricing.profit} (${pricing.marginPercent}%)</span>
                        </div>
                    </div>
                    
                    <button class="btn-import" id="btn-import-${item.pid}" onclick="importProductByPid('${item.pid}')">
                        <i data-lucide="download" style="width:14px;"></i> Import Product & Variants
                    </button>
                </div>
            `;
            resultsGrid.appendChild(card);
        });
        lucide.createIcons();
    }
    
    async function importProductByPid(pid) {
        const item = searchResults.find(p => String(p.pid) === String(pid));
        if (!item) return;

        const btn = document.getElementById(`btn-import-${pid}`);
        const categoryId = document.getElementById('importCategory') ? document.getElementById('importCategory').value : null;
        const publishNow = document.getElementById('publishNow') ? document.getElementById('publishNow').checked : false;
        const costPrice = parseFloat(item.sellPrice) || 10.0;
        
        btn.disabled = true;
        btn.innerHTML = `<i data-lucide="loader-2" class="lucide-spin" style="width:14px;"></i> Importing...`;
        lucide.createIcons();
        
        try {
            const response = await fetch('/admin/api/catalog/import-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    pid: String(item.pid),
                    title: item.productNameEn || 'AtoZ Smart Gadget',
                    price: costPrice,
                    image: item.productImage || '',
                    category: item.categoryName || 'Consumer Electronics',
                    categoryId: categoryId ? parseInt(categoryId) : null,
                    publish_now: publishNow
                })
            });
            
            const result = await response.json();
            
            if(result.success) {
                btn.className = 'btn-import success';
                let label = 'Saved Draft';
                if (result.status === 'active') {
                    label = 'Published to Store';
                } else if (result.status === 'already_imported') {
                    label = 'Already in Catalog';
                }
                btn.innerHTML = `<i data-lucide="check-circle" style="width:14px;"></i> ${label}`;
                lucide.createIcons();
            } else {
                throw new Error(result.message || 'Import failed');
            }
        } catch (error) {
            btn.style.background = '#ef4444';
            btn.innerHTML = `<i data-lucide="x-circle" style="width:14px;"></i> Error`;
            lucide.createIcons();
            console.error('Import error:', error);
        }
    }

    async function toggleSandboxGateway() {
        const btn = document.getElementById('btnGatewayToggle');
        const btnText = document.getElementById('btnGatewayToggleText');
        const badge = document.getElementById('gatewayStatusBadge');
        const badgeText = document.getElementById('gatewayStatusText');
        
        btn.disabled = true;
        btnText.innerText = 'Switching...';

        try {
            const res = await fetch('{{ route('admin.settings.toggle_cj_sandbox') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                if (data.sandbox_mode) {
                    badge.style = 'background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);';
                    badge.innerHTML = `<i data-lucide="alert-triangle" style="width:14px;"></i> <span id="gatewayStatusText">CJ Sandbox Mode · Mock Data</span>`;
                    btnText.innerText = 'Switch to Live API';
                } else {
                    badge.style = '';
                    badge.innerHTML = `<i data-lucide="activity" style="width:14px;"></i> <span id="gatewayStatusText">CJ API Connected · Live</span>`;
                    btnText.innerText = 'Switch to Sandbox';
                }
                lucide.createIcons();
            }
        } catch (e) {
            alert('Failed to toggle sandbox mode');
        } finally {
            btn.disabled = false;
        }
    }
</script>
<style>
    .lucide-spin { animation: lucide-spin 2s linear infinite; }
    @keyframes lucide-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<!-- Custom Delete Modal -->
<div class="modal-overlay" id="deleteConfirmModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button onclick="closeDeleteConfirm()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        <div style="margin-bottom: 24px; color: var(--text-secondary); font-size: 14px; line-height: 1.5;">
            Are you sure you want to delete <strong id="deleteProductName" style="color: var(--text-primary);"></strong>? This action cannot be undone.
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display:flex; justify-content:flex-end; gap: 12px;">
                <button type="button" onclick="closeDeleteConfirm()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer; font-weight: 600; font-size: 13px;">Cancel</button>
                <button type="submit" style="background: #ef4444; color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer;">Delete Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
