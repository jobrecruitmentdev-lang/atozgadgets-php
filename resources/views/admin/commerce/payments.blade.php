@extends('layouts.admin')

@section('title', 'Payment Ledger & Transactions')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px; }
    .stat-title { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 6px; }
    .stat-value { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .badge-paid { background: rgba(16,185,129,0.15); color: #10b981; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
    .badge-failed { background: rgba(239,68,68,0.15); color: #ef4444; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Commerce Payment Ledger</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Immutable payment capture, authorization, and refund transactions.</p>
    </div>
    <div>
        <a href="{{ route('admin.commerce.payments', ['filter' => 'failed']) }}" style="padding: 8px 14px; border-radius: 6px; background: rgba(239,68,68,0.15); color: #ef4444; font-weight: 700; font-size: 12px; text-decoration: none;">
            View Failed Transactions ({{ $stats['failed_count'] }})
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Total Captured Volume</div>
        <div class="stat-value" style="color: #10b981;">${{ number_format($stats['total_volume'], 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Refunded</div>
        <div class="stat-value" style="color: #ef4444;">${{ number_format($stats['total_refunded'], 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Failed Payments</div>
        <div class="stat-value" style="color: {{ $stats['failed_count'] > 0 ? '#ef4444' : 'inherit' }};">{{ $stats['failed_count'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Transactions</div>
        <div class="stat-value">{{ $stats['total_count'] }}</div>
    </div>
</div>

<div class="panel-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Tx ID</th>
                <th>Order #</th>
                <th>Gateway</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td style="font-family: monospace; font-weight: 700;">{{ $tx->provider_transaction_id ?: '#TX-' . $tx->id }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $tx->order_id) }}" style="color: var(--accent); font-weight: 700;">{{ $tx->order->order_number ?? 'Order #' . $tx->order_id }}</a>
                    </td>
                    <td><strong>{{ strtoupper($tx->provider) }}</strong></td>
                    <td><span style="font-size: 11px; font-weight: 700; text-transform: uppercase;">{{ $tx->type }}</span></td>
                    <td style="font-weight: 700;">${{ number_format($tx->amount, 2) }} {{ $tx->currency }}</td>
                    <td>
                        <span class="{{ $tx->status === 'SUCCESS' ? 'badge-paid' : 'badge-failed' }}">{{ $tx->status }}</span>
                    </td>
                    <td style="color: var(--text-secondary);">{{ $tx->created_at->format('M d, Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">No payment transactions recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
