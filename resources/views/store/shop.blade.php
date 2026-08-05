@extends('layouts.store')

@section('title', 'Shop - AtoZGadgets')

@section('content')
<style>
    .shop-header { margin-bottom: 40px; border-bottom: 1px solid var(--glass-border); padding-bottom: 24px; }
    .shop-header h1 { font-size: 48px; font-weight: 800; letter-spacing: -1px; margin-bottom: 12px; }
    
    .shop-layout { display: flex; flex-direction: column; gap: 32px; }
    @media (min-width: 768px) { .shop-layout { flex-direction: row; gap: 48px; } }
    
    .sidebar { width: 100%; flex-shrink: 0; }
    @media (min-width: 768px) { .sidebar { width: 250px; position: sticky; top: 100px; height: calc(100vh - 120px); overflow-y: auto; } }
    
    .sidebar h3 { font-size: 18px; font-weight: 600; margin-bottom: 16px; letter-spacing: -0.5px; }
    .cat-list { display: flex; flex-direction: column; gap: 8px; }
    .cat-list a { padding: 8px 12px; border-radius: 8px; font-size: 14px; color: var(--text-secondary); transition: all 0.3s; display: flex; justify-content: space-between; align-items: center; }
    .cat-list a:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .cat-list a.active { background: rgba(201, 169, 98, 0.1); color: var(--accent); font-weight: 500; }
    
    .main-content { flex-grow: 1; }
</style>

<div class="shop-header" data-aos="fade-up">
    <h1>All Products</h1>
    <p style="color: var(--text-secondary); font-size: 18px;">Browse our full catalog of premium gadgets.</p>
</div>

<div class="shop-layout">
    <aside class="sidebar" data-aos="fade-right" data-aos-delay="100">
        <h3>Categories</h3>
        <div class="cat-list">
            <a href="{{ route('store.shop') }}" class="{{ !request('category') ? 'active' : '' }}">
                All Products
            </a>
            @foreach($categories as $category)
                <a href="{{ route('store.shop', ['category' => $category->slug]) }}" class="{{ request('category') == $category->slug ? 'active' : '' }}">
                    {{ $category->name }}
                </a>
            @endforeach
            @if($categories->isEmpty())
                <a href="#">Smartwatches</a>
                <a href="#">Audio & Sound</a>
                <a href="#">Gaming</a>
                <a href="#">Smart Home</a>
            @endif
        </div>
    </aside>

    <div class="main-content">
        <div class="grid">
            @forelse($products as $index => $product)
                <a href="{{ route('store.product', $product->slug) }}" class="card" data-aos="fade-up" data-aos-delay="{{ ($index % 6) * 100 }}">
                    <img loading="lazy" decoding="async" src="{{ $product->thumbnail_image ?? 'https://images.unsplash.com/photo-1600080972464-8e5f35f63d08?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $product->name }}">
                    <div class="card-title">{{ $product->name }}</div>
                    <div class="card-price">${{ $product->discount_price ?? $product->price }}</div>
                    <button class="btn btn-primary" style="width:100%; padding: 12px; font-size: 14px; text-transform: uppercase;">View Details</button>
                </a>
            @empty
                <!-- Fallback mock -->
                <a href="/product/mock" class="card" data-aos="fade-up">
                    <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1600080972464-8e5f35f63d08?auto=format&fit=crop&w=400&q=80" alt="Controller">
                    <div class="card-title">RGB Mechanical Wireless Gaming Controller Pro</div>
                    <div class="card-price">$49.00</div>
                    <button class="btn btn-primary" style="width:100%; padding: 12px; font-size: 14px; text-transform: uppercase;">View Details</button>
                </a>
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
