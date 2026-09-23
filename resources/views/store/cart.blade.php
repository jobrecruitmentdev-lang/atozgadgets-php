@extends('layouts.store')

@section('title', 'Your Cart - AtoZGadgets')

@section('content')
<style>
    .cart-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-top: 20px; }
    .cart-items { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px; min-width: 0; }
    .cart-summary { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px; height: fit-content; min-width: 0; }
    .cart-item { display: flex; gap: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border); margin-bottom: 20px; min-width: 0; align-items: center; }
    .cart-item:last-child { border: none; margin: 0; padding: 0; }
    .cart-item img { width: 90px; height: 90px; border-radius: 12px; object-fit: cover; flex-shrink: 0; background: #111; }
    .item-details { min-width: 0; flex: 1; }
    .item-details h3 { font-size: 17px; margin-bottom: 6px; word-break: break-word; overflow-wrap: break-word; font-weight: 600; line-height: 1.3; }
    .item-price { font-weight: 700; color: #34d399; font-size: 16px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 14px; color: var(--text-secondary); font-size: 14px; }
    .summary-total { display: flex; justify-content: space-between; margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--glass-border); font-size: 22px; font-weight: 700; color: #fff; }

    /* Shipping Progress Bar */
    .shipping-bar-box { background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; }
    .shipping-bar-track { width: 100%; height: 6px; background: rgba(255, 255, 255, 0.08); border-radius: 4px; overflow: hidden; margin-top: 8px; }
    .shipping-bar-fill { height: 100%; background: linear-gradient(90deg, var(--accent), #10b981); border-radius: 4px; transition: width 0.3s ease; }

    /* Sticky Mobile Checkout CTA */
    .cart-mobile-cta { display: none; }

    @media (max-width: 768px) {
        .cart-layout { grid-template-columns: 1fr; gap: 20px; margin-top: 16px; padding-bottom: 60px; }
        .cart-items, .cart-summary { padding: 18px; border-radius: 16px; }
        .cart-item { gap: 14px; padding-bottom: 16px; margin-bottom: 16px; }
        .cart-item img { width: 70px; height: 70px; border-radius: 10px; }
        .item-details h3 { font-size: 15px; }
        .item-price { font-size: 15px; }

        .cart-mobile-cta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            left: 0;
            right: 0;
            bottom: calc(56px + env(safe-area-inset-bottom, 0px));
            background: rgba(14, 14, 18, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding: 10px 16px;
            z-index: 998;
            box-shadow: 0 -6px 20px rgba(0,0,0,0.5);
        }
    }

    @media (max-width: 380px) {
        .cart-items, .cart-summary { padding: 14px; }
        .cart-item img { width: 58px; height: 58px; }
        .item-details h3 { font-size: 13.5px; }
        .summary-total { font-size: 19px; }
    }
</style>

<h1 style="font-size: clamp(24px, 5vw, 36px); font-weight: 800; margin-bottom: 20px;">Shopping Cart</h1>

@if(empty($cart))
    <div style="text-align:center; padding: 60px 20px; background: var(--glass-bg); border-radius: 20px; border: 1px solid var(--glass-border);">
        <i data-lucide="shopping-bag" style="width: 48px; height: 48px; color: var(--text-secondary); margin-bottom: 16px; opacity: 0.5;"></i>
        <h2 style="margin-bottom: 12px; color: var(--text-secondary); font-size: 20px;">Your cart is empty</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px;">Browse our catalog of premium curated smart tech & gadgets.</p>
        <a href="{{ route('store.shop') }}" class="btn btn-primary" style="padding: 12px 28px;">Continue Shopping</a>
    </div>
@else
    @php
        $freeThreshold = (float)\App\Models\Setting::get('free_shipping_threshold', 50.00);
        $stdRate = (float)\App\Models\Setting::get('standard_shipping_rate', 5.99);
        $cartShipping = ($total >= $freeThreshold || $total == 0) ? 0 : $stdRate;
        $cartGrandTotal = $total + $cartShipping;
    @endphp

    <!-- Free Shipping Progress Tracker -->
    <div class="shipping-bar-box">
        @if($total >= $freeThreshold)
            <div style="display:flex; align-items:center; gap:8px; font-size: 13px; font-weight: 600; color: #10b981;">
                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                <span>Congratulations! You've unlocked <strong>Free Standard US Shipping</strong>.</span>
            </div>
            <div class="shipping-bar-track">
                <div class="shipping-bar-fill" style="width: 100%;"></div>
            </div>
        @else
            <div style="display:flex; justify-content:space-between; align-items:center; font-size: 13px;">
                <span style="color: var(--text-secondary);">Add <strong style="color: var(--accent);">${{ number_format($freeThreshold - $total, 2) }}</strong> more for <strong>Free Shipping</strong></span>
                <span style="font-weight: 700; color: var(--accent);">{{ round(($total / $freeThreshold) * 100) }}%</span>
            </div>
            <div class="shipping-bar-track">
                <div class="shipping-bar-fill" style="width: {{ min(100, round(($total / $freeThreshold) * 100)) }}%;"></div>
            </div>
        @endif
    </div>

    <div class="cart-layout">
        <div class="cart-items">
            @foreach($cart as $id => $item)
                <div class="cart-item">
                    <img src="{{ $item['image'] ?? 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=200&q=80' }}" alt="{{ $item['name'] }}">
                    <div class="item-details">
                        <h3>{{ $item['name'] }}</h3>
                        <p class="item-price">${{ $item['price'] }} <span style="color:var(--text-secondary); font-size:13px; font-weight:400;">&times; {{ $item['quantity'] }}</span></p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="cart-summary">
            <h2 style="margin-bottom: 20px; font-size: 20px; font-weight: 700;">Order Summary</h2>
            <div class="summary-row"><span>Subtotal</span> <span>${{ number_format($total, 2) }}</span></div>
            <div class="summary-row">
                <span>Shipping</span> 
                <span>{{ $cartShipping == 0 ? 'FREE' : '$' . number_format($cartShipping, 2) }}</span>
            </div>
            <div class="summary-total"><span>Total</span> <span style="color: var(--accent);">${{ number_format($cartGrandTotal, 2) }}</span></div>
            <a href="{{ route('store.checkout') }}" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 24px; font-size: 16px; padding: 14px; font-weight: 700; border-radius: 12px; display: block; box-sizing: border-box;">Proceed to Checkout &rarr;</a>
            
            <div style="margin-top: 18px; text-align: center; font-size: 12px; color: var(--text-secondary); display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i data-lucide="shield-check" style="width: 14px; height: 14px; color: var(--accent);"></i>
                <span>256-Bit SSL Encrypted & Verified Checkout</span>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Checkout Bottom Bar -->
    <div class="cart-mobile-cta">
        <div>
            <div style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Total</div>
            <div style="font-size: 17px; font-weight: 800; color: var(--accent);">${{ number_format($cartGrandTotal, 2) }}</div>
        </div>
        <a href="{{ route('store.checkout') }}" class="btn btn-primary" style="padding: 10px 20px; font-size: 14px; font-weight: 700; border-radius: 10px;">
            Checkout &rarr;
        </a>
    </div>
@endif
@endsection
