@extends('layouts.admin')

@section('title', 'Shipments & Tracking')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .badge-status { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-shipped { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .badge-in_transit { background: rgba(16,185,129,0.15); color: #10b981; }
    .badge-delivered { background: rgba(16,185,129,0.25); color: #059669; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Shipments & Logistics</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">White-labeled carrier tracking feed and delivery confirmations.</p>
    </div>
</div>

<div class="panel-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>White-Labeled Carrier</th>
                <th>Tracking Number</th>
                <th>Status</th>
                <th>Dispatched At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $s)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $s->order_id) }}" style="color: var(--accent); font-weight: 700;">{{ $s->order->order_number ?? 'Order #' . $s->order_id }}</a>
                    </td>
                    <td>{{ $s->order->orderAddress->first_name ?? ($s->order->user->first_name ?? 'Guest') }} {{ $s->order->orderAddress->last_name ?? '' }}</td>
                    <td><strong>{{ $s->customer_carrier_name ?? ($s->carrier->customer_name ?? 'Standard Delivery') }}</strong></td>
                    <td style="font-family: monospace; font-weight: 700; color: var(--accent);">
                        {{ $s->tracking_number ?? 'Awaiting Carrier Scan' }}
                    </td>
                    <td>
                        <span class="badge-status badge-{{ strtolower($s->status ?? $s->shipment_status ?? 'shipped') }}">{{ $s->status ?? $s->shipment_status ?? 'Shipped' }}</span>
                    </td>
                    <td style="color: var(--text-secondary);">{{ $s->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $s->order_id) }}" style="color: var(--accent); font-weight: 600;">Control Tower →</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">No active shipments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $shipments->links() }}
    </div>
</div>
@endsection
