@extends('layouts.admin')

@section('title', 'CJ Dropshipping Gateway - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; }
    .page-title { margin-bottom: 16px; }
    .page-title h1 { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .page-title p { color: var(--text-secondary); font-size: 14px; }
    
    .api-health { display: inline-flex; items-center; gap: 6px; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 600; background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
    
    .tabs-container { display: flex; gap: 8px; background: rgba(128,128,128,0.05); padding: 6px; border-radius: 12px; border: 1px solid var(--border-color); display: inline-flex; }
    .tab-btn { padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; background: transparent; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .tab-btn.active { background: var(--accent); color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .tab-btn:hover:not(.active) { color: var(--text-primary); }

    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Configuration Panel */
    .config-panel { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .config-title { font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 16px; color: var(--text-primary); }
    
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); }
    .form-group input, .form-group select { padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); font-size: 14px; outline: none; transition: border 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2); }
    
    /* Search Bar Area */
    .search-area { position: relative; margin-top: 24px; display: flex; gap: 16px; }
    .search-input-wrapper { flex-grow: 1; position: relative; }
    .search-input-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
    .search-input-wrapper input { width: 100%; padding: 14px 16px 14px 44px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); font-size: 15px; font-weight: 500; outline: none; transition: all 0.2s; }
    .search-input-wrapper input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    
    .btn-search { padding: 0 32px; border-radius: 12px; font-weight: 700; font-size: 14px; border: none; background: linear-gradient(to right, var(--accent), #1d4ed8); color: #fff; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .btn-search:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3); }
    
    /* Results Grid */
    .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 24px; }
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
        <h1>CJDropshipping Catalog Gateway</h1>
        <p>Manage CJ products staged in your local MySQL database, or search and import new trending items.</p>
        <div style="margin-top: 12px;">
            <span class="api-health"><i data-lucide="activity" style="width:14px;"></i> CJ API Connected · Live</span>
        </div>
    </div>
    
    <div class="tabs-container">
        <button class="tab-btn active" onclick="switchTab('staged')" id="tab-btn-staged">
            <i data-lucide="database" style="width:16px;"></i> Staged Products in DB ({{ $stagedProducts->count() }})
        </button>
        <button class="tab-btn" onclick="switchTab('fetch')" id="tab-btn-fetch">
            <i data-lucide="sparkles" style="width:16px;"></i> Fetch New Products from CJ
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
                        <th>Fulfillment</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stagedProducts as $prod)
                        <tr>
                            <td style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ $prod->thumbnail_image }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);" alt="">
                                <span style="font-weight: 600;">{{ $prod->name }}</span>
                            </td>
                            <td style="font-family: monospace; font-size: 12px; color: var(--text-secondary);">{{ $prod->sku }}</td>
                            <td style="font-weight: 700; color: var(--accent);">${{ number_format($prod->price, 2) }}</td>
                            <td><span class="badge-cj"><i data-lucide="sparkles" style="width:10px;"></i> CJ Dropship</span></td>
                            <td style="text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                <button class="action-btn" onclick="openEditProduct({{ json_encode($prod) }})"><i data-lucide="edit" style="width:16px;"></i></button>
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
    <div class="config-panel">
        <div class="config-title">
            <i data-lucide="layers" style="width:20px; color:var(--accent);"></i>
            Target Catalog & Pricing Markup Gateway
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Storefront Category</label>
                <select id="importCategory">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Markup Multiplier (e.g. 2.0)</label>
                <input type="number" id="importMarkup" value="2.0" step="0.1" min="1.1" onchange="updateLiveMargins()">
            </div>
        </div>
        
        <div class="search-area">
            <div class="search-input-wrapper">
                <i data-lucide="search" style="width:18px;"></i>
                <input type="text" id="searchInput" placeholder="Search CJ Dropshipping catalog (e.g. projector, drone, smart watch)..." onkeypress="if(event.key === 'Enter') { searchCJ(); }">
            </div>
            <button class="btn-search" onclick="searchCJ()" id="searchBtn">
                <i data-lucide="trending-up" style="width:16px;" id="searchIcon"></i> Fetch Products
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
    }

    let searchResults = [];

    async function searchCJ() {
        const keyword = document.getElementById('searchInput').value || '';
        const searchBtn = document.getElementById('searchBtn');
        const searchIcon = document.getElementById('searchIcon');
        const resultsGrid = document.getElementById('resultsGrid');
        
        searchBtn.disabled = true;
        searchBtn.innerHTML = `<i data-lucide="loader-2" class="lucide-spin" style="width:16px;"></i> Searching...`;
        lucide.createIcons();
        resultsGrid.innerHTML = '';
        
        try {
            const response = await fetch(`/admin/api/catalog/search?keyword=${encodeURIComponent(keyword)}`);
            const data = await response.json();
            
            if(data.data && data.data.list && data.data.list.length > 0) {
                searchResults = data.data.list;
                document.getElementById('resultsCount').innerText = `${searchResults.length} Products Found`;
                sortAndRenderResults();
            } else {
                document.getElementById('resultsCount').innerText = `0 Products Found`;
                resultsGrid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-secondary);">No products found matching your search.</div>`;
            }
        } catch (error) {
            resultsGrid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #ef4444;">Error fetching data from CJ API.</div>`;
        }
        
        searchBtn.disabled = false;
        searchBtn.innerHTML = `<i data-lucide="trending-up" style="width:16px;"></i> Fetch Products`;
        lucide.createIcons();
    }
    
    function sortAndRenderResults() {
        const sortBy = document.getElementById('sortSelect').value;
        
        searchResults.sort((a, b) => {
            const priceA = parseFloat(a.sellPrice) || 0;
            const priceB = parseFloat(b.sellPrice) || 0;
            const listedA = parseInt(a.listedNum) || 0;
            const listedB = parseInt(b.listedNum) || 0;
            const createTimeA = parseInt(a.createTime) || 0;
            const createTimeB = parseInt(b.createTime) || 0;
            const inventoryA = parseFloat(a.productWeight) || 0; 
            const inventoryB = parseFloat(b.productWeight) || 0; 
            
            switch(sortBy) {
                case 'price_asc': return priceA - priceB;
                case 'price_desc': return priceB - priceA;
                case 'newest': return createTimeB - createTimeA;
                case 'lists': return listedB - listedA;
                case 'inventory': return inventoryB - inventoryA; // CJ doesn't expose exact inventory in list, fallback to best match
                case 'best_match':
                default:
                    return 0; // Keep original order
            }
        });
        
        renderResults();
    }
    
    function renderResults() {
        const markup = parseFloat(document.getElementById('importMarkup').value) || 2.0;
        const resultsGrid = document.getElementById('resultsGrid');
        resultsGrid.innerHTML = '';
        
        searchResults.forEach(item => {
            const costPrice = parseFloat(item.sellPrice) || 10.0;
            const msrpPrice = (costPrice * markup);
            const retailPrice = (msrpPrice * 0.75).toFixed(2); // The actual selling price (discounted)
            const profit = (retailPrice - costPrice).toFixed(2);
            const cleanTitle = item.productNameEn.replace(/'/g, "\\'");
            
            // Highlight loss if profit is negative
            const profitColor = profit < 0 ? '#ef4444' : 'var(--accent)';
            const profitPrefix = profit < 0 ? '' : '+';
            
            const card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML = `
                <img src="${item.productImage}" class="product-img" alt="">
                <div class="product-content">
                    <div class="product-category">${item.categoryName}</div>
                    <div class="product-title" title="${cleanTitle}">${item.productNameEn}</div>
                    
                    <div class="price-calc">
                        <div class="price-calc-item">
                            <span class="price-label">Supplier Cost</span>
                            <span class="price-value">$${costPrice.toFixed(2)}</span>
                        </div>
                        <div class="price-calc-item" style="text-align: right;">
                            <span class="price-label">Selling Price</span>
                            <span class="price-value" style="color: var(--accent);">$${retailPrice} <span style="font-size: 10px; text-decoration: line-through; color: #9ca3af;">$${msrpPrice.toFixed(2)}</span></span>
                        </div>
                        <div class="profit-row">
                            <span class="price-label">Est. Profit/Unit:</span>
                            <span class="profit-val" style="color: ${profitColor};">${profitPrefix}$${profit}</span>
                        </div>
                    </div>
                    
                    <button class="btn-import" id="btn-import-${item.pid}" onclick="importProduct('${item.pid}', '${cleanTitle}', ${costPrice}, '${item.productImage}', '${item.categoryName}')">
                        <i data-lucide="download" style="width:14px;"></i> Import to Database
                    </button>
                </div>
            `;
            resultsGrid.appendChild(card);
        });
        lucide.createIcons();
    }
    
    function updateLiveMargins() {
        if (searchResults.length > 0) {
            renderResults();
        }
    }
    
    async function importProduct(pid, title, costPrice, image, category) {
        const btn = document.getElementById(`btn-import-${pid}`);
        const categoryId = document.getElementById('importCategory').value;
        const markup = parseFloat(document.getElementById('importMarkup').value) || 2.0;
        
        btn.disabled = true;
        btn.innerHTML = `<i data-lucide="loader-2" class="lucide-spin" style="width:14px;"></i> Importing...`;
        lucide.createIcons();
        
        try {
            const response = await fetch('/admin/api/catalog/import-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    pid: pid,
                    title: title,
                    price: costPrice,
                    image: image,
                    category: category,
                    categoryId: categoryId,
                    markup: markup
                })
            });
            
            const result = await response.json();
            
            if(result.success) {
                btn.className = 'btn-import success';
                btn.innerHTML = `<i data-lucide="check-circle" style="width:14px;"></i> Imported`;
                lucide.createIcons();
            } else {
                throw new Error('Import failed');
            }
        } catch (error) {
            btn.style.background = '#ef4444';
            btn.innerHTML = `<i data-lucide="x-circle" style="width:14px;"></i> Error`;
            lucide.createIcons();
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
