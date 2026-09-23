@extends('layouts.store')

@section('title', (isset($currentCategory) ? $currentCategory->name . ' - ' : '') . 'Shop All Gadgets - AtoZGadgets')
@section('meta_description', isset($currentCategory) ? 'Explore the best ' . $currentCategory->name . ' at AtoZGadgets. Fast 3-7 day shipping across the USA, 30-day returns, and top rated electronics.' : 'Browse the full catalog of trending viral tech, smart home devices, and innovative electronics at AtoZGadgets. Fast USA shipping.')
@section('meta_keywords', (isset($currentCategory) ? $currentCategory->name . ', ' : '') . 'trending gadgets, buy electronics online, viral tech store USA, smart devices')
@section('og_title', (isset($currentCategory) ? $currentCategory->name . ' - ' : '') . 'Shop All Gadgets - AtoZGadgets')
@section('og_description', isset($currentCategory) ? 'Shop premium ' . $currentCategory->name . ' with fast USA delivery.' : 'Discover trending electronics, smart devices, and viral gadgets.')
@section('canonical', url()->current())

@section('meta')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "url": "{{ url()->current() }}",
      "name": "{{ (isset($currentCategory) ? $currentCategory->name : 'All Products') }} - AtoZGadgets",
      "description": "Trending gadgets and innovative electronics available for fast shipping across the United States."
    },
    {
      "@type": "BreadcrumbList",
      "@id": "{{ url()->current() }}#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "{{ url('/') }}"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "{{ isset($currentCategory) ? $currentCategory->name : 'Shop All' }}",
          "item": "{{ url()->current() }}"
        }
      ]
    }
  ]
}
</script>
@endsection

@section('content')
<style>
    .shop-header { margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 18px; }
    @media (min-width: 768px) { .shop-header { margin-bottom: 32px; padding-bottom: 24px; } }
    .shop-header h1 { font-size: clamp(24px, 4vw, 42px); font-weight: 800; letter-spacing: -1px; margin-bottom: 8px; color: var(--text-primary); }
    
    .shop-toolbar { display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px; }
    @media (min-width: 768px) { .shop-toolbar { flex-direction: row; justify-content: space-between; align-items: center; } }
    
    /* Horizontal Swipeable Price Pills */
    .filter-pills { display: flex; gap: 8px; align-items: center; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch; }
    .filter-pills::-webkit-scrollbar { display: none; }
    .filter-pill { font-size: 12.5px; padding: 6px 14px; border-radius: 50px; background: var(--hover-subtle); border: 1px solid var(--border-color); color: var(--text-secondary); text-decoration: none; transition: all 0.2s; white-space: nowrap; flex-shrink: 0; }
    .filter-pill.active, .filter-pill:hover { border-color: var(--accent); color: var(--accent); background: var(--selection-bg); }

    .shop-actions-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; width: 100%; }
    @media (min-width: 768px) { .shop-actions-row { width: auto; } }

    .sort-select { padding: 8px 12px; border-radius: 8px; background: var(--bg-surface); border: 1px solid var(--border-color); color: var(--text-primary); font-size: 13px; cursor: pointer; outline: none; width: 100%; max-width: 190px; }
    .sort-select:focus { border-color: var(--accent); }
    
    .mobile-filter-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; background: var(--selection-bg); border: 1px solid var(--accent); color: var(--accent); font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; }
    @media (min-width: 768px) { .mobile-filter-btn { display: none; } }

    .shop-layout { display: flex; flex-direction: column; gap: 24px; }
    @media (min-width: 768px) { .shop-layout { flex-direction: row; gap: 40px; } }
    
    /* Responsive Sidebar & Slide-Up Mobile Bottom Sheet */
    @media (max-width: 767px) {
        .sidebar-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 2001; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .sidebar-backdrop.active { opacity: 1; visibility: visible; }
        .sidebar { position: fixed; bottom: 0; left: 0; right: 0; max-height: 80vh; background: var(--bg-surface-elevated); border-top: 1px solid var(--border-color); border-radius: 20px 20px 0 0; z-index: 2002; padding: 20px 16px; overflow-y: auto; transform: translateY(100%); transition: transform 0.35s var(--ease-premium); box-shadow: 0 -10px 40px rgba(0,0,0,0.7); width: 100%; }
        .sidebar.active { transform: translateY(0); }
        .sidebar-header-mobile { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); }
        .sidebar-card { background: transparent !important; border: none !important; padding: 0 !important; margin: 0 !important; }
    }
    @media (min-width: 768px) {
        .sidebar-backdrop, .sidebar-header-mobile { display: none !important; }
        .sidebar { width: 260px; flex-shrink: 0; position: sticky; top: 100px; max-height: calc(100vh - 120px); overflow-y: auto; }
        .sidebar-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; margin-bottom: 20px; }
    }
    
    .sidebar h3 { font-size: 16px; font-weight: 700; margin-bottom: 14px; letter-spacing: -0.3px; color: var(--text-primary); }
    .cat-list { display: flex; flex-direction: column; gap: 6px; }
    .cat-list a { padding: 8px 12px; border-radius: 8px; font-size: 14px; color: var(--text-secondary); transition: all 0.2s; display: flex; justify-content: space-between; align-items: center; text-decoration: none; min-height: 38px; }
    .cat-list a:hover { background: var(--hover-subtle); color: var(--text-primary); }
    .cat-list a.active { background: var(--selection-bg); color: var(--accent); font-weight: 600; }

    /* Card Micro-Tags */
    .card-meta-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 11px; }
    .sku-chip { font-weight: 600; color: var(--text-secondary); background: var(--hover-subtle); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border-color); }
    .avail-indicator { display: inline-flex; align-items: center; gap: 4px; font-weight: 600; }
    .avail-instock { color: #10b981; }
    .avail-lowstock { color: #f59e0b; }
    .avail-outofstock { color: #ef4444; }
    .avail-confirming { color: #3b82f6; }

    .price-row { display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px; }
    .price-main { font-size: 18px; font-weight: 800; color: var(--accent); }
    @media (min-width: 640px) { .price-main { font-size: 20px; } }
    .price-old { font-size: 13px; text-decoration: line-through; color: var(--text-secondary); }
    
    .main-content { flex-grow: 1; min-width: 0; }
</style>

<div class="shop-header" data-aos="fade-up">
    @if(isset($currentCategory))
        <div class="breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; flex-wrap: wrap;">
            <a href="{{ route('store.home') }}" style="transition: color 0.3s;">Home</a> <i data-lucide="chevron-right" style="width:13px;height:13px;"></i>
            <a href="{{ route('store.shop') }}" style="transition: color 0.3s;">Products</a> <i data-lucide="chevron-right" style="width:13px;height:13px;"></i>
            
            @php
                $cat = $currentCategory;
                $hierarchy = [];
                while($cat) {
                    array_unshift($hierarchy, $cat);
                    $cat = $cat->parent;
                }
            @endphp
            @foreach($hierarchy as $h)
                @if($loop->last)
                    <span style="color: var(--accent); font-weight: 600;">{{ $h->name }}</span>
                @else
                    <a href="{{ route('store.shop', ['category' => $h->slug]) }}" style="transition: color 0.3s;">{{ $h->name }}</a> <i data-lucide="chevron-right" style="width:13px;height:13px;"></i>
                @endif
            @endforeach
        </div>
        <h1>{{ $currentCategory->name }}</h1>
        <p style="color: var(--text-secondary); font-size: 14.5px;">Curated selection of {{ strtolower($currentCategory->name) }} ready to ship worldwide.</p>
    @else
        <div class="breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; flex-wrap: wrap;">
            <a href="{{ route('store.home') }}" style="transition: color 0.3s;">Home</a> <i data-lucide="chevron-right" style="width:13px;height:13px;"></i>
            <span style="color: var(--accent); font-weight: 600;">All Products</span>
        </div>
        <h1>All Products</h1>
        <p style="color: var(--text-secondary); font-size: 14.5px;">Browse our complete catalog of innovative gadgets and electronics.</p>
    @endif
</div>

<div class="shop-toolbar">
    <div class="filter-pills">
        <a href="{{ route('store.shop') }}" class="filter-pill {{ !request('max_price') && !request('min_price') ? 'active' : '' }}">All Prices</a>
        <a href="{{ route('store.shop', array_merge(request()->query(), ['max_price' => 20])) }}" class="filter-pill {{ request('max_price') == 20 ? 'active' : '' }}">Under $20</a>
        <a href="{{ route('store.shop', array_merge(request()->query(), ['min_price' => 20, 'max_price' => 50])) }}" class="filter-pill {{ request('min_price') == 20 && request('max_price') == 50 ? 'active' : '' }}">$20 to $50</a>
        <a href="{{ route('store.shop', array_merge(request()->query(), ['min_price' => 50])) }}" class="filter-pill {{ request('min_price') == 50 ? 'active' : '' }}">$50+</a>
    </div>

    <div class="shop-actions-row">
        <button type="button" class="mobile-filter-btn" id="openShopFilterBtn">
            <i data-lucide="sliders-horizontal" style="width:15px;height:15px;"></i>
            <span>Filter Categories</span>
        </button>

        <form method="GET" action="{{ route('store.shop') }}" id="sortForm" style="flex: 1; display:flex; justify-content: flex-end;">
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
            @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
            @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
            
            <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()" aria-label="Sort products">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Sort by: Latest</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
            </select>
        </form>
    </div>
</div>

<div class="shop-layout">
    <!-- Mobile Filter Backdrop -->
    <div class="sidebar-backdrop" id="shopFilterBackdrop"></div>

    <aside class="sidebar" id="shopSidebar">
        <div class="sidebar-header-mobile">
            <h3 style="margin: 0; font-size: 17px;">Select Category</h3>
            <button type="button" id="closeShopFilterBtn" style="background: none; border: none; color: var(--text-primary); cursor: pointer; padding: 6px;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>

        <div class="sidebar-card">
            <h3 class="desktop-only-heading">Categories</h3>
            <div class="cat-list">
                <a href="{{ route('store.shop') }}" class="{{ !request('category') ? 'active' : '' }}">
                    <span>All Categories</span>
                </a>
                @if(isset($globalCategories))
                    <div style="margin-top: 6px;">
                        @include('store.partials.category_tree', ['categories' => $globalCategories, 'depth' => 0])
                    </div>
                @endif
            </div>
        </div>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openFilterBtn = document.getElementById('openShopFilterBtn');
            const closeFilterBtn = document.getElementById('closeShopFilterBtn');
            const filterBackdrop = document.getElementById('shopFilterBackdrop');
            const shopSidebar = document.getElementById('shopSidebar');

            function openFilter() {
                if (shopSidebar) shopSidebar.classList.add('active');
                if (filterBackdrop) filterBackdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            function closeFilter() {
                if (shopSidebar) shopSidebar.classList.remove('active');
                if (filterBackdrop) filterBackdrop.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (openFilterBtn) openFilterBtn.addEventListener('click', openFilter);
            if (closeFilterBtn) closeFilterBtn.addEventListener('click', closeFilter);
            if (filterBackdrop) filterBackdrop.addEventListener('click', closeFilter);
        });
    </script>

    <div class="main-content">
        <div class="grid">
            @forelse($products as $index => $product)
                @php $avail = $product->availability; @endphp
                <a href="{{ route('store.product', $product->slug) }}" class="card" data-aos="fade-up" data-aos-delay="{{ ($index % 6) * 80 }}">
                    <img loading="lazy" decoding="async" src="{{ $product->customer_thumbnail }}" alt="{{ $product->name }}">
                    
                    <div class="card-meta-row">
                        <span class="sku-chip">{{ $product->merchant_sku }}</span>
                        <span class="avail-indicator avail-{{ str_replace('_', '', $avail['status']) }}">
                            <i data-lucide="{{ $avail['icon'] }}" style="width:12px;height:12px;"></i>
                            {{ $avail['label'] }}
                        </span>
                    </div>

                    <div class="card-title">{{ $product->name }}</div>
                    
                    <div class="price-row">
                        <span class="price-main">${{ number_format($product->effective_price, 2) }}</span>
                        @if($product->has_active_discount)
                            <span class="price-old">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>

                    <button class="btn btn-primary" style="width:100%; padding: 10px; font-size: 13px; text-transform: uppercase;">View Product</button>
                </a>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 64px 24px; color: var(--text-secondary); background: rgba(128,128,128,0.03); border: 1px dashed var(--border-color); border-radius: 16px;">
                    <i data-lucide="package-search" style="width:48px; height:48px; stroke-width:1.5; color: var(--text-secondary); margin-bottom: 12px; display:inline-block;"></i>
                    <p style="font-size: 16px; font-weight: 600; margin-bottom: 6px;">No products found</p>
                    <p style="font-size: 14px;">Try adjusting your filters or selecting another category.</p>
                </div>
            @endforelse
        </div>
        
        @if(method_exists($products, 'links') && $products->hasPages())
            <div style="margin-top: 40px; display:flex; justify-content:center;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
