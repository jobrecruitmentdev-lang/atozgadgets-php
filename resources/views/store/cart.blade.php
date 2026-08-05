@extends('layouts.store')

@section('title', 'Your Cart - AtoZGadgets')

@section('content')
<style>
    .cart-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-top: 20px; }
    .cart-items { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px; }
    .cart-summary { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px; height: fit-content; }
    .cart-item { display: flex; gap: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border); margin-bottom: 20px; }
    .cart-item:last-child { border: none; margin: 0; padding: 0; }
    .cart-item img { width: 100px; height: 100px; border-radius: 12px; object-fit: cover; }
    .item-details h3 { font-size: 18px; margin-bottom: 8px; }
    .item-price { font-weight: 700; color: #34d399; font-size: 16px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; color: var(--text-secondary); }
    .summary-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--glass-border); font-size: 24px; font-weight: 700; color: #fff; }
</style>

<h1 style="font-size: 36px; margin-bottom: 30px;">Shopping Cart</h1>

@if(empty($cart))
    <div style="text-align:center; padding: 80px 20px; background: var(--glass-bg); border-radius: 20px; border: 1px solid var(--glass-border);">
        <h2 style="margin-bottom: 15px; color: var(--text-secondary);">Your cart is empty</h2>
        <a href="{{ route('store.shop') }}" class="btn btn-primary">Continue Shopping</a>
    </div>
@else
    <div class="cart-layout">
        <div class="cart-items">
            @foreach($cart as $id => $item)
                <div class="cart-item">
                    <img src="{{ $item['image'] ?? 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=200&q=80' }}" alt="">
                    <div class="item-details">
                        <h3>{{ $item['name'] }}</h3>
                        <p class="item-price">${{ $item['price'] }} <span style="color:var(--text-secondary); font-size:14px; font-weight:400;">x {{ $item['quantity'] }}</span></p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="cart-summary">
            <h2 style="margin-bottom: 20px; font-size: 22px;">Order Summary</h2>
            <div class="summary-row"><span>Subtotal</span> <span>${{ number_format($total, 2) }}</span></div>
            <div class="summary-row"><span>Shipping</span> <span>Free</span></div>
            <div class="summary-total"><span>Total</span> <span>${{ number_format($total, 2) }}</span></div>
            <a href="{{ route('store.checkout') }}" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 30px; font-size: 18px; padding: 15px;">Proceed to Checkout</a>
        </div>
    </div>
@endif
@endsection
