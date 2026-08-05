@extends('account.layout')

@section('account_content')
<div class="content-header">
    <h1 class="content-title">My Orders</h1>
    <p style="color: var(--text-secondary);">View and track your order history.</p>
</div>

<div class="card-dark">
    @if($orders->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 12px 0;">Order #</th>
                        <th style="padding: 12px 0;">Date</th>
                        <th style="padding: 12px 0;">Status</th>
                        <th style="padding: 12px 0;">Total</th>
                        <th style="padding: 12px 0;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 16px 0;">#{{ $order->order_number }}</td>
                            <td style="padding: 16px 0; color: var(--text-secondary);">{{ $order->created_at->format('M d, Y') }}</td>
                            <td style="padding: 16px 0;">
                                <span style="background: rgba(201,169,98,0.1); color: var(--accent); padding: 4px 10px; border-radius: 50px; font-size: 12px;">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td style="padding: 16px 0;">${{ number_format($order->total_amount, 2) }}</td>
                            <td style="padding: 16px 0;">
                                <button class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">View</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 40px 0;">
            <i data-lucide="shopping-bag" style="width: 48px; height: 48px; color: var(--text-secondary); margin-bottom: 16px; opacity: 0.5;"></i>
            <h3 style="margin-bottom: 8px;">No orders found</h3>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">You haven't placed any orders yet.</p>
            <a href="{{ route('store.shop') }}" class="btn btn-primary">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
