@extends('layouts.store')

@section('title', ($product->name ?? 'Product') . ' - AtoZGadgets')

@section('content')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; }
    .breadcrumb a { transition: color 0.3s; }
    .breadcrumb a:hover { color: var(--text-primary); }

    .product-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
    @media (max-width: 768px) { .product-layout { grid-template-columns: 1fr; gap: 40px; } }
    
    .product-gallery { position: sticky; top: 100px; }
    .product-image { width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 24px; border: 1px solid var(--glass-border); background: #000; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
    
    .product-info h1 { font-size: clamp(32px, 4vw, 48px); font-weight: 800; line-height: 1.1; letter-spacing: -1px; margin-bottom: 16px; }
    .price-wrap { display: flex; align-items: baseline; gap: 12px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--glass-border); }
    .price-tag { font-size: 40px; font-weight: 700; color: var(--accent); }
    .old-price { font-size: 20px; text-decoration: line-through; color: var(--text-secondary); }
    
    .description { font-size: 16px; color: rgba(255,255,255,0.8); line-height: 1.8; margin-bottom: 40px; font-weight: 300; }
    
    .action-row { display: flex; gap: 16px; margin-bottom: 32px; }
    .btn-cart { flex: 1; padding: 18px; font-size: 16px; font-weight: 600; border-radius: 12px; text-transform: uppercase; letter-spacing: 1px; }
    
    .trust-badges-vertical { display: flex; flex-direction: column; gap: 16px; background: rgba(255,255,255,0.02); padding: 24px; border-radius: 16px; border: 1px solid var(--glass-border); }
    .tb-item { display: flex; align-items: center; gap: 12px; font-size: 14px; color: var(--text-secondary); }
    .tb-item i { color: var(--accent); }
</style>

<div class="breadcrumb" data-aos="fade-right">
    <a href="{{ route('store.home') }}">Home</a> <i data-lucide="chevron-right" style="width:14px;"></i>
    <a href="{{ route('store.shop') }}">Products</a> <i data-lucide="chevron-right" style="width:14px;"></i>
    <span style="color: var(--text-primary);">{{ $product->name ?? 'Ultra Smart Watch' }}</span>
</div>

<div class="product-layout">
    <div class="product-gallery" data-aos="fade-up">
        <img fetchpriority="high" decoding="sync" src="{{ $product->thumbnail_image ?? 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name ?? 'Product Image' }}" class="product-image">
    </div>
    
    <div class="product-info" data-aos="fade-left" data-aos-delay="100">
        <h1>{{ $product->name ?? 'Ultra Smart Watch Series 9' }}</h1>
        
        <div class="price-wrap">
            <span class="price-tag">${{ $product->discount_price ?? $product->price ?? '29.99' }}</span>
            @if(isset($product->discount_price) && $product->discount_price < $product->price)
                <span class="old-price">${{ $product->price }}</span>
            @endif
        </div>
        
        <div class="description">
            {!! $product->description ?? 'Experience the next generation of smart wearables. Features include bluetooth calling, waterproof design, heart rate monitoring, and a stunning edge-to-edge display.' !!}
        </div>
        
        <form action="{{ route('store.cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id ?? 1 }}">
            <div class="action-row">
                <button type="submit" class="btn btn-primary btn-cart">
                    <i data-lucide="shopping-cart" style="display:inline; width:18px; margin-right:8px; vertical-align:middle;"></i> Add to Cart
                </button>
            </div>
        </form>

        <div class="trust-badges-vertical">
            <div class="tb-item">
                <i data-lucide="truck"></i>
                <span><strong>Free Worldwide Shipping</strong> on orders over $30</span>
            </div>
            <div class="tb-item">
                <i data-lucide="shield-check"></i>
                <span><strong>Secure Checkout</strong> via Razorpay & Stripe</span>
            </div>
            <div class="tb-item">
                <i data-lucide="rotate-ccw"></i>
                <span><strong>7-Day Returns</strong> for defective items</span>
            </div>
        </div>
    </div>
</div>
@endsection
