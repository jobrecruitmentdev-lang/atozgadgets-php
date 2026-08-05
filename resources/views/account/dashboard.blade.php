@extends('account.layout')

@section('account_content')
<div class="content-header">
    <h1 class="content-title">Dashboard</h1>
    <p style="color: var(--text-secondary);">Welcome back, {{ $user->first_name }}! Here's an overview of your account.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card-dark" style="text-align: center; margin-bottom: 0;">
        <i data-lucide="package" style="width: 32px; height: 32px; color: var(--accent); margin-bottom: 12px;"></i>
        <h3>Orders</h3>
        <p style="color: var(--text-secondary); margin-top: 8px;"><a href="{{ route('account.orders') }}" style="color: var(--accent);">View history</a></p>
    </div>
    <div class="card-dark" style="text-align: center; margin-bottom: 0;">
        <i data-lucide="map-pin" style="width: 32px; height: 32px; color: var(--accent); margin-bottom: 12px;"></i>
        <h3>Addresses</h3>
        <p style="color: var(--text-secondary); margin-top: 8px;"><a href="{{ route('account.addresses') }}" style="color: var(--accent);">Manage addresses</a></p>
    </div>
</div>

<div class="card-dark">
    <h3 style="margin-bottom: 20px; font-weight: 600; font-size: 20px;">Recent Orders</h3>
    @if($recentOrders->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 12px 0;">Order #</th>
                        <th style="padding: 12px 0;">Date</th>
                        <th style="padding: 12px 0;">Status</th>
                        <th style="padding: 12px 0;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 16px 0;">#{{ $order->order_number }}</td>
                            <td style="padding: 16px 0; color: var(--text-secondary);">{{ $order->created_at->format('M d, Y') }}</td>
                            <td style="padding: 16px 0;">
                                <span style="background: rgba(201,169,98,0.1); color: var(--accent); padding: 4px 10px; border-radius: 50px; font-size: 12px;">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td style="padding: 16px 0;">${{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            <a href="{{ route('account.orders') }}" style="color: var(--accent); font-size: 14px;">View all orders &rarr;</a>
        </div>
    @else
        <p style="color: var(--text-secondary);">You haven't placed any orders yet.</p>
        <a href="{{ route('store.shop') }}" class="btn btn-primary" style="display: inline-block; margin-top: 16px;">Start Shopping</a>
    @endif
</div>
@endsection
