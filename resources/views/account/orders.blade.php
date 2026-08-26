@extends('account.layout')

@section('account_content')
<style>
    .orders-table-wrapper {
        display: none;
    }
    @media (min-width: 768px) {
        .orders-table-wrapper {
            display: block;
            overflow-x: auto;
        }
    }
    
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .orders-table th {
        padding: 14px 16px;
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .orders-table td {
        padding: 18px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        font-size: 14px;
        vertical-align: middle;
    }
    .orders-table tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .mobile-orders-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    @media (min-width: 768px) {
        .mobile-orders-list {
            display: none;
        }
    }
    
    .order-mobile-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .order-mobile-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .order-mobile-num {
        font-weight: 700;
        font-size: 16px;
        color: #fff;
    }
    .order-mobile-date {
        font-size: 12.5px;
        color: var(--text-secondary);
        margin-top: 2px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-paid, .status-delivered {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.25);
    }
    .status-processing, .status-in_transit, .status-shipped {
        background: rgba(201, 169, 98, 0.15);
        color: var(--accent);
        border: 1px solid rgba(201, 169, 98, 0.3);
    }
    .status-pending {
        background: rgba(234, 179, 8, 0.12);
        color: #eab308;
        border: 1px solid rgba(234, 179, 8, 0.25);
    }
    .status-cancelled, .status-refunded {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }
</style>

<div class="content-header">
    <h1 class="content-title">My Orders</h1>
    <p style="color: var(--text-secondary); font-size: 14px;">View, track and manage your order history.</p>
</div>

<div class="card-dark" style="padding: 0; overflow: hidden;">
    @if($orders->count() > 0)
        <!-- Desktop Table View -->
        <div class="orders-table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        @php
                            $statusClass = 'status-' . strtolower($order->status);
                            $itemsCount = $order->items ? $order->items->sum('quantity') : 1;
                        @endphp
                        <tr>
                            <td style="font-weight: 600; color: #fff;">
                                #{{ $order->order_number }}
                            </td>
                            <td style="color: var(--text-secondary);">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td style="color: var(--text-secondary);">
                                {{ $itemsCount }} {{ Str::plural('item', $itemsCount) }}
                            </td>
                            <td style="font-weight: 700; color: var(--accent);">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('store.order_confirmation', $order->order_number) }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <span>Track</span> <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Responsive Cards -->
        <div class="mobile-orders-list" style="padding: 16px;">
            @foreach($orders as $order)
                @php
                    $statusClass = 'status-' . strtolower($order->status);
                    $itemsCount = $order->items ? $order->items->sum('quantity') : 1;
                @endphp
                <div class="order-mobile-card">
                    <div class="order-mobile-header">
                        <div>
                            <div class="order-mobile-num">#{{ $order->order_number }}</div>
                            <div class="order-mobile-date">{{ $order->created_at->format('M d, Y · h:i A') }}</div>
                        </div>
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-top: 1px solid rgba(255,255,255,0.04); border-bottom: 1px solid rgba(255,255,255,0.04);">
                        <span style="color: var(--text-secondary); font-size: 13px;">{{ $itemsCount }} {{ Str::plural('item', $itemsCount) }}</span>
                        <span style="font-weight: 700; font-size: 17px; color: var(--accent);">${{ number_format($order->total_amount, 2) }}</span>
                    </div>

                    <a href="{{ route('store.order_confirmation', $order->order_number) }}" class="btn btn-primary" style="width: 100%; text-align: center; justify-content: center; padding: 12px; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <span>View Order & Tracking</span> <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(201, 169, 98, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: var(--accent);">
                <i data-lucide="shopping-bag" style="width: 32px; height: 32px;"></i>
            </div>
            <h3 style="margin-bottom: 8px; font-size: 20px; font-weight: 700;">No Orders Yet</h3>
            <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px; max-width: 360px; margin-left: auto; margin-right: auto;">
                When you purchase gadgets on AtoZGadgets, your orders and real-time tracking will appear here.
            </p>
            <a href="{{ route('store.shop') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; text-decoration: none;">
                <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i> <span>Start Shopping</span>
            </a>
        </div>
    @endif
</div>
@endsection
