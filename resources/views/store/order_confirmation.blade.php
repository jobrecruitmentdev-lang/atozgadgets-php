@extends('layouts.store')

@section('title', 'Order Confirmed #' . $order->order_number . ' - AtoZGadgets')

@section('content')
<style>
    .confirm-container {
        max-width: 800px;
        margin: 40px auto 80px auto;
        padding: 0 16px;
    }
    .confirm-card {
        background: rgba(20, 20, 28, 0.85);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(12px);
    }
    .success-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.15);
        border: 2px solid #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px auto;
        color: #10b981;
    }
    .order-badge {
        display: inline-block;
        background: rgba(201, 169, 98, 0.15);
        border: 1px solid var(--accent);
        color: var(--accent);
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 0.5px;
        margin-top: 8px;
    }
    .section-box {
        background: rgba(10, 10, 15, 0.6);
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 20px;
        margin-top: 24px;
    }
    .item-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .item-row:last-child {
        border-bottom: none;
    }
    .item-img {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        object-fit: cover;
        background: #000;
        border: 1px solid var(--glass-border);
    }
    .action-btns {
        display: flex;
        gap: 16px;
        margin-top: 32px;
        justify-content: center;
        flex-wrap: wrap;
    }
</style>

<div class="confirm-container">
    <div class="confirm-card" data-aos="fade-up">
        <div style="text-align: center;">
            <div class="success-icon-wrap">
                <i data-lucide="check-circle-2" style="width: 40px; height: 40px;"></i>
            </div>
            <h1 style="font-size: 28px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
                Thank You for Your Order!
            </h1>
            <p style="color: var(--text-secondary); font-size: 15px; max-width: 500px; margin: 0 auto 16px auto;">
                Your payment has been successfully processed. We're getting your gadgets ready for shipment.
            </p>
            <div class="order-badge">
                ORDER #{{ $order->order_number }}
            </div>
        </div>

        <!-- Order Summary -->
        <div class="section-box">
            <h2 style="font-size: 16px; font-weight: 700; color: var(--accent); margin-bottom: 16px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="package" style="width: 18px;"></i> Items in Your Order
            </h2>
            @foreach($order->items as $item)
                <div class="item-row">
                    <img src="{{ $item->variant?->image_url ?: ($item->product?->customer_thumbnail ?: asset('favicon.png')) }}" 
                         alt="{{ $item->product?->name ?? 'Product' }}" 
                         class="item-img"
                         onerror="this.src='{{ asset('favicon.png') }}'">
                    <div style="flex-grow: 1;">
                        <p style="font-weight: 600; color: var(--text-primary); font-size: 14px; margin-bottom: 2px;">
                            {{ $item->product?->name ?? 'Product' }}
                        </p>
                        @if($item->variant)
                            <span style="font-size: 12px; color: var(--text-secondary);">Option: {{ $item->variant->name }}</span>
                        @endif
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 13px; color: var(--text-secondary);">Qty: {{ $item->quantity }}</span>
                        <p style="font-weight: 700; color: var(--accent); font-size: 14px; margin-top: 2px;">
                            ${{ number_format($item->unit_price * $item->quantity, 2) }}
                        </p>
                    </div>
                </div>
            @endforeach

            <div style="border-top: 1px solid var(--glass-border); padding-top: 16px; margin-top: 12px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight: 700; font-size: 16px; color: var(--text-primary);">Total Paid</span>
                <span style="font-weight: 800; font-size: 20px; color: var(--accent);">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Shipping & Timeline -->
        <div style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 16px;">
            @php $addr = $order->orderAddress ?: (is_string($order->shipping_address) ? json_decode($order->shipping_address) : (is_array($order->shipping_address) ? (object)$order->shipping_address : null)); @endphp
            @if($addr)
                <div class="section-box" style="margin-top: 0;">
                    <h2 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 10px; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="map-pin" style="width: 16px; color: var(--accent);"></i> Shipping Destination
                    </h2>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.6;">
                        {{ $addr->first_name ?? '' }} {{ $addr->last_name ?? '' }}<br>
                        {{ $addr->address_line1 ?? ($addr->address1 ?? '') }} {{ $addr->address_line2 ?? ($addr->address2 ?? '') }}<br>
                        {{ $addr->city ?? '' }}, {{ $addr->state ?? '' }} {{ $addr->postal_code ?? ($addr->zip ?? '') }}<br>
                        {{ $addr->country ?? ($addr->country_code ?? 'US') }}
                    </p>
                </div>
            @endif

            <div class="section-box" style="margin-top: 0; background: rgba(201, 169, 98, 0.05); border-color: rgba(201, 169, 98, 0.2);">
                <h2 style="font-size: 15px; font-weight: 700; color: var(--accent); margin-bottom: 8px; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="truck" style="width: 16px;"></i> Delivery Timeline
                </h2>
                <p style="color: var(--text-secondary); font-size: 13px; margin: 0;">
                    Standard Delivery: <strong>7–15 Business Days</strong>. You will receive real-time tracking updates via email once your parcel is dispatched by our logistics hub.
                </p>
            </div>
        </div>

        <div class="action-btns">
            <a href="{{ route('account.orders') }}" class="btn btn-primary" style="padding: 14px 28px; font-size: 14px; text-transform: uppercase;">
                View in My Orders
            </a>
            <a href="{{ route('store.shop') }}" class="btn btn-secondary" style="padding: 14px 28px; font-size: 14px; text-transform: uppercase;">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
