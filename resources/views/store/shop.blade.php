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
    .shop-header { margin-bottom: 32px; border-bottom: 1px solid var(--border-color); padding-bottom: 24px; }
    .shop-header h1 { font-size: clamp(32px, 4vw, 44px); font-weight: 800; letter-spacing: -1px; margin-bottom: 10px; color: var(--text-primary); }
    
    .shop-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
    .filter-pills { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .filter-pill { font-size: 13px; padding: 6px 14px; border-radius: 50px; background: var(--hover-subtle); border: 1px solid var(--border-color); color: var(--text-secondary); text-decoration: none; transition: all 0.2s; }
    .filter-pill.active, .filter-pill:hover { border-color: var(--accent); color: var(--accent); background: var(--selection-bg); }

    .sort-select { padding: 8px 14px; border-radius: 8px; background: var(--bg-surface); border: 1px solid var(--border-color); color: var(--text-primary); font-size: 13px; cursor: pointer; outline: none; }
    .sort-select:focus { border-color: var(--accent); }
    
    .mobile-filter-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; background: var(--selection-bg); border: 1px solid var(--accent); color: var(--accent); font-size: 13px; font-weight: 600; cursor: pointer; }
    @media (min-width: 768px) { .mobile-filter-btn { display: none; } }

    .shop-layout { display: flex; flex-direction: column; gap: 32px; }
    @media (min-width: 768px) { .shop-layout { flex-direction: row; gap: 40px; } }
    
    .sidebar { width: 100%; flex-shrink: 0; }
    @media (max-width: 767px) {
        .sidebar { display: none; }
        .sidebar.active { display: block; animation: fadeIn 0.2s ease; }
    }
    @media (min-width: 768px) { .sidebar { width: 260px; position: sticky; top: 100px; max-height: calc(100vh - 120px); overflow-y: auto; } }
    
    .sidebar-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; margin-bottom: 20px; }
    .sidebar h3 { font-size: 16px; font-weight: 700; margin-bottom: 14px; letter-spacing: -0.3px; color: var(--text-primary); }
    .cat-list { display: flex; flex-direction: column; gap: 6px; }
    .cat-list a { padding: 8px 12px; border-radius: 8px; font-size: 14px; color: var(--text-secondary); transition: all 0.2s; display: flex; justify-content: space-between; align-items: center; text-decoration: none; min-height: 40px; }
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
    .price-main { font-size: 20px; font-weight: 800; color: var(--accent); }
    .price-old { font-size: 14px; text-decoration: line-through; color: var(--text-secondary); }
    
    .main-content { flex-grow: 1; min-width: 0; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="shop-header" data-aos="fade-up">
    @if(isset($currentCategory))
        <div class="breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-secondary); margin-bottom: 16px; flex-wrap: wrap;">
            <a href="{{ route('store.home') }}" style="transition: color 0.3s;">Home</a> <i data-lucide="chevron-right" style="width:14px;"></i>
            <a href="{{ route('store.shop') }}" style="transition: color 0.3s;">Products</a> <i data-lucide="chevron-right" style="width:14px;"></i>
            
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
                    <a href="{{ route('store.shop', ['category' => $h->slug]) }}" style="transition: color 0.3s;">{{ $h->name }}</a> <i data-lucide="chevron-right" style="width:14px;"></i>
                @endif
            @endforeach
        </div>
        <h1>{{ $currentCategory->name }}</h1>
        <p style="color: var(--text-secondary); font-size: 16px;">Curated selection of {{ strtolower($currentCategory->name) }} ready to ship worldwide.</p>
    @else
        <div class="breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-secondary); margin-bottom: 16px; flex-wrap: wrap;">
            <a href="{{ route('store.home') }}" style="transition: color 0.3s;">Home</a> <i data-lucide="chevron-right" style="width:14px;"></i>
            <span style="color: var(--accent); font-weight: 600;">All Products</span>
        </div>
        <h1>All Products</h1>
        <p style="color: var(--text-secondary); font-size: 16px;">Browse our complete catalog of innovative gadgets and electronics.</p>
    @endif
</div>

<div class="shop-toolbar">
    <div class="filter-pills">
        <a href="{{ route('store.shop') }}" class="filter-pill {{ !request('max_price') && !request('min_price') ? 'active' : '' }}">All Prices</a>
        <a href="{{ route('store.shop', array_merge(request()->query(), ['max_price' => 20])) }}" class="filter-pill {{ request('max_price') == 20 ? 'active' : '' }}">Under $20</a>
        <a href="{{ route('store.shop', array_merge(request()->query(), ['min_price' => 20, 'max_price' => 50])) }}" class="filter-pill {{ request('min_price') == 20 && request('max_price') == 50 ? 'active' : '' }}">$20 to $50</a>
        <a href="{{ route('store.shop', array_merge(request()->query(), ['min_price' => 50])) }}" class="filter-pill {{ request('min_price') == 50 ? 'active' : '' }}">$50+</a>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <button type="button" class="mobile-filter-btn" id="mobileFilterToggle" onclick="document.querySelector('.sidebar').classList.toggle('active'); this.innerText = document.querySelector('.sidebar').classList.contains('active') ? 'Hide Filters ✕' : 'Filter Categories ☰';">
            Filter Categories ☰
        </button>

        <form method="GET" action="{{ route('store.shop') }}" id="sortForm">
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
            @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
            @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
            
            <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Sort by: Latest</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
            </select>
        </form>
    </div>
</div>

<div class="shop-layout">
    <aside class="sidebar" data-aos="fade-right" data-aos-delay="100">
        <div class="sidebar-card">
            <h3>Categories</h3>
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
