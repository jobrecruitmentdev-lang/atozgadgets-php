@extends('layouts.admin')

@section('title', 'Admin Control Tower')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    
    /* 1. ACTION REQUIRED BANNER */
    .action-required-panel { background: var(--bg-card); border: 1px solid #ef4444; border-radius: 14px; padding: 20px 24px; margin-bottom: 28px; box-shadow: 0 4px 12px rgba(239,68,68,0.08); }
    .action-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .action-title { font-size: 15px; font-weight: 800; color: #ef4444; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px; }
    .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; }
    .action-card { background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; text-decoration: none; color: inherit; transition: transform 0.2s, border-color 0.2s; display: flex; flex-direction: column; justify-content: space-between; }
    .action-card:hover { transform: translateY(-2px); border-color: var(--accent); }
    .action-count { font-size: 24px; font-weight: 800; }
    .action-label { font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-top: 4px; }
    
    /* 2. TODAY'S PULSE */
    .pulse-panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 20px 24px; margin-bottom: 28px; }
    .pulse-title { font-size: 14px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 14px; display: flex; align-items: center; gap: 6px; }
    .pulse-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; }
    .pulse-node { display: flex; flex-direction: column; gap: 4px; }
    .pulse-val { font-size: 22px; font-weight: 800; color: var(--text-primary); }
    .pulse-desc { font-size: 11px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; }

    /* Tables & Recent */
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    
    .badge-status { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-paid { background: rgba(16,185,129,0.15); color: #10b981; }
    .badge-pending { background: rgba(245,158,11,0.15); color: #d97706; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Admin Control Tower</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Live operations, exception dispatching, and commercial ledger.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.fulfillment.overview') }}" style="padding: 9px 16px; border-radius: 8px; background: var(--accent); color: #fff; font-weight: 700; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i data-lucide="truck" style="width: 15px;"></i> Fulfillment Hub
        </a>
    </div>
</div>

<!-- 1. ACTION REQUIRED -->
<div class="action-required-panel">
    <div class="action-header">
        <span class="action-title"><i data-lucide="alert-octagon" style="width:18px;"></i> Action Required — Operational Triage</span>
        <span style="font-size: 12px; color: var(--text-secondary);">Direct queue deep-links</span>
    </div>
    <div class="action-grid">
        <a href="{{ route('admin.fulfillment.exceptions') }}" class="action-card">
            <div class="action-count" style="color: {{ $actionRequired['fulfillment_exceptions'] > 0 ? '#ef4444' : '#10b981' }};">
                {{ $actionRequired['fulfillment_exceptions'] > 0 ? '🔴 ' . $actionRequired['fulfillment_exceptions'] : '🟢 0' }}
            </div>
            <div class="action-label">Fulfillment Exceptions</div>
        </a>

        <a href="{{ route('admin.commerce.payments', ['filter' => 'failed']) }}" class="action-card">
            <div class="action-count" style="color: {{ $actionRequired['payment_failures'] > 0 ? '#ef4444' : '#10b981' }};">
                {{ $actionRequired['payment_failures'] > 0 ? '🔴 ' . $actionRequired['payment_failures'] : '🟢 0' }}
            </div>
            <div class="action-label">Payment Failures</div>
        </a>

        <a href="{{ route('admin.fulfillment.queue', ['filter' => 'stale']) }}" class="action-card">
            <div class="action-count" style="color: {{ $actionRequired['pending_stale'] > 0 ? '#d97706' : '#10b981' }};">
                {{ $actionRequired['pending_stale'] > 0 ? '🟠 ' . $actionRequired['pending_stale'] : '🟢 0' }}
            </div>
            <div class="action-label">Orders Pending > 2h</div>
        </a>

        <a href="{{ route('admin.catalog.products') }}" class="action-card">
            <div class="action-count" style="color: {{ $actionRequired['stale_sync_products'] > 0 ? '#d97706' : '#10b981' }};">
                {{ $actionRequired['stale_sync_products'] > 0 ? '🟠 ' . $actionRequired['stale_sync_products'] : '🟢 0' }}
            </div>
            <div class="action-label">Supplier Sync > 48h</div>
        </a>

        <a href="{{ route('admin.analytics.profitability') }}" class="action-card">
            <div class="action-count" style="color: {{ $actionRequired['low_margin_products'] > 0 ? '#f59e0b' : '#10b981' }};">
                {{ $actionRequired['low_margin_products'] > 0 ? '🟡 ' . $actionRequired['low_margin_products'] : '🟢 0' }}
            </div>
            <div class="action-label">Margin Alerts (< 20%)</div>
        </a>
    </div>
</div>

<!-- 2. TODAY'S OPERATIONAL PULSE -->
<div class="pulse-panel">
    <div class="pulse-title"><i data-lucide="activity" style="width:16px;"></i> Today's Live Pulse</div>
    <div class="pulse-grid">
        <div class="pulse-node">
            <span class="pulse-desc">Orders Placed</span>
            <span class="pulse-val">{{ $todayPulse['orders'] }}</span>
        </div>
        <div class="pulse-node">
            <span class="pulse-desc">Paid Revenue</span>
            <span class="pulse-val" style="color: var(--accent);">${{ number_format($todayPulse['revenue'], 2) }}</span>
        </div>
        <div class="pulse-node">
            <span class="pulse-desc">Paid Orders</span>
            <span class="pulse-val" style="color: #10b981;">{{ $todayPulse['paid_orders'] }}</span>
        </div>
        <div class="pulse-node">
            <span class="pulse-desc">Dispatched</span>
            <span class="pulse-val">{{ $todayPulse['shipped'] }}</span>
        </div>
        <div class="pulse-node">
            <span class="pulse-desc">Delivered</span>
            <span class="pulse-val">{{ $todayPulse['delivered'] }}</span>
        </div>
        <div class="pulse-node">
            <span class="pulse-desc">Refunds Processed</span>
            <span class="pulse-val" style="color: {{ $todayPulse['refunded'] > 0 ? '#ef4444' : 'inherit' }};">{{ $todayPulse['refunded'] }}</span>
        </div>
    </div>
</div>

<!-- 3. RECENT COMMERCE ORDERS -->
<div class="panel-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 700;">Recent Commerce Orders</h3>
        <a href="{{ route('admin.orders') }}" style="font-size: 13px; color: var(--accent); font-weight: 600; text-decoration: none;">View All Orders →</a>
    </div>
    <table class="table-modern">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Payment</th>
                <th>Customer Status</th>
                <th>Total</th>
                <th>Placed</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentOrders as $ord)
                @php
                    $custStatus = \App\Services\Order\CustomerOrderStatusResolver::resolve($ord);
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $ord->id) }}" style="color: var(--accent); font-weight: 700;">{{ $ord->order_number }}</a>
                    </td>
                    <td>{{ $ord->orderAddress->first_name ?? ($ord->user->first_name ?? 'Guest') }} {{ $ord->orderAddress->last_name ?? '' }}</td>
                    <td>
                        <span class="badge-status {{ in_array(strtolower($ord->payment_status ?? ''), ['paid', 'completed', 'success']) ? 'badge-paid' : 'badge-pending' }}">
                            {{ $ord->payment_status ?? 'Pending' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-status {{ $custStatus['badge_class'] }}">{{ $custStatus['status'] }}</span>
                    </td>
                    <td style="font-weight: 700;">${{ number_format($ord->total_amount, 2) }}</td>
                    <td style="color: var(--text-secondary); font-size: 12px;">{{ $ord->created_at->format('M d, H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $ord->id) }}" style="color: var(--accent); font-weight: 600;">Control Tower →</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 24px;">No recent orders.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection