@extends('account.layout')

@section('account_content')
<style>
    .dashboard-stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (min-width: 600px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }
    }
    
    .stat-card {
        background: rgba(18, 18, 20, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 18px;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .stat-card:hover {
        border-color: rgba(201, 169, 98, 0.4);
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.03);
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(201, 169, 98, 0.12);
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
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
</style>

<div class="content-header">
    <h1 class="content-title">My Profile</h1>
    <p style="color: var(--text-secondary); font-size: 14px;">Welcome back, <strong style="color: #fff;">{{ $user->first_name }}</strong>! Overview of your AtoZGadgets account.</p>
</div>

<div class="dashboard-stats-grid">
    <a href="{{ route('account.orders') }}" class="stat-card">
        <div class="stat-icon-wrapper">
            <i data-lucide="package" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: var(--text-secondary);">Orders & Tracking</div>
            <div style="font-size: 17px; font-weight: 700; color: #fff; margin-top: 2px;">View Orders &rarr;</div>
        </div>
    </a>
    <a href="{{ route('account.addresses') }}" class="stat-card">
        <div class="stat-icon-wrapper">
            <i data-lucide="map-pin" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: var(--text-secondary);">Delivery Address</div>
            <div style="font-size: 17px; font-weight: 700; color: #fff; margin-top: 2px;">Manage &rarr;</div>
        </div>
    </a>
</div>

<div class="card-dark" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-weight: 700; font-size: 18px; color: #fff; margin: 0;">Recent Orders</h3>
        @if($recentOrders->count() > 0)
            <a href="{{ route('account.orders') }}" style="color: var(--accent); font-size: 13px; font-weight: 500; text-decoration: none;">View All &rarr;</a>
        @endif
    </div>

    @if($recentOrders->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($recentOrders as $order)
                @php
                    $statusClass = 'status-' . strtolower($order->status);
                @endphp
                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 14px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <div>
                        <div style="font-weight: 600; font-size: 14.5px; color: #fff;">#{{ $order->order_number }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">{{ $order->created_at->format('M d, Y') }}</div>
                    </div>
                    <div style="text-align: right; display: flex; align-items: center; gap: 14px;">
                        <div>
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            <div style="font-weight: 700; color: var(--accent); font-size: 14px; margin-top: 3px;">
                                ${{ number_format($order->total_amount, 2) }}
                            </div>
                        </div>
                        <a href="{{ route('store.order_confirmation', $order->order_number) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">
                            Track
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 30px 10px;">
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">You haven't placed any orders yet.</p>
            <a href="{{ route('store.shop') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; text-decoration: none;">
                <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i> <span>Start Shopping</span>
            </a>
        </div>
    @endif
</div>
@endsection
