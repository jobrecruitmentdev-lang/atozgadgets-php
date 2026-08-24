@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number . ' - Control Tower')

@section('content')
<style>
    .tower-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
    .tower-title { font-size: 24px; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 12px; }
    .status-badge { padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 700; text-transform: uppercase; }
    .badge-paid { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .badge-pending { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .badge-refunded { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .health-strip { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; }
    .health-node { display: flex; flex-direction: column; gap: 4px; }
    .health-label { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; }
    .health-status { font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; }

    .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; margin-bottom: 24px; }
    .tower-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }
    .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; }
    .card-header h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }

    .data-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13.5px; border-bottom: 1px dashed rgba(128,128,128,0.15); }
    .data-label { color: var(--text-secondary); font-weight: 500; }
    .data-val { font-weight: 600; color: var(--text-primary); text-align: right; }

    .timeline-list { list-style: none; padding-left: 16px; border-left: 2px solid var(--border-color); margin-top: 12px; }
    .timeline-item { position: relative; margin-bottom: 16px; padding-left: 16px; }
    .timeline-dot { position: absolute; left: -23px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: var(--accent); }
    .timeline-time { font-size: 11px; color: var(--text-secondary); }
    .timeline-desc { font-size: 13px; font-weight: 600; color: var(--text-primary); }

    .actions-bar { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-action { padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; border: none; }
    .btn-primary { background: var(--accent); color: #ffffff; }
    .btn-danger { background: #ef4444; color: #ffffff; }
    .btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); }
</style>

@if(session('success'))
    <div style="padding: 12px 16px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 600; margin-bottom: 16px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="padding: 12px 16px; border-radius: 8px; background: rgba(239, 68, 68, 0.15); color: #ef4444; font-weight: 600; margin-bottom: 16px;">
        {{ session('error') }}
    </div>
@endif

<div class="tower-header">
    <div>
        <a href="{{ route('admin.orders') }}" class="btn-action btn-outline" style="margin-bottom: 8px; padding: 6px 12px; font-size: 12px;">← Back to Orders</a>
        <h1 class="tower-title">
            Order #{{ $order->order_number }}
            @php
                $pStat = strtolower($order->payment_status ?? 'pending');
                $bClass = in_array($pStat, ['paid', 'completed', 'success']) ? 'badge-paid' : ($pStat === 'refunded' ? 'badge-refunded' : 'badge-pending');
                $activeFulfillment = $order->fulfillments->first();
                $fStat = $activeFulfillment->fulfillment_status ?? 'UNFULFILLED';
            @endphp
            <span class="status-badge {{ $bClass }}">{{ $order->payment_status ?? 'Pending' }}</span>
            <span class="status-badge" style="background: rgba(59,130,246,0.15); color: #3b82f6;">{{ $fStat }}</span>
        </h1>
        <p style="font-size: 13px; color: var(--text-secondary);">Customer Facing: <strong>{{ $customerStatus['status'] ?? 'Processing' }}</strong> • Placed on {{ $order->created_at->format('M d, Y H:i:s') }}</p>
    </div>
    <div class="actions-bar">
        @if(in_array($pStat, ['paid', 'completed', 'success']) && (!in_array($fStat, ['SUBMITTED', 'PROCESSING'])))
            <form action="{{ route('admin.orders.fulfill', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-action btn-primary">
                    <i data-lucide="send" style="width: 14px;"></i> Fulfill Order
                </button>
            </form>
        @endif

        @if($order->cjOrder && $order->cjOrder->cj_order_id)
            <form action="{{ route('admin.orders.sync_cj', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-action btn-outline">
                    <i data-lucide="refresh-cw" style="width: 14px;"></i> Sync Tracking
                </button>
            </form>
        @endif

        @if($pStat === 'paid')
            <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST" onsubmit="return confirm('Process full refund of ${{ number_format($order->total_amount, 2) }}?');">
                @csrf
                <button type="submit" class="btn-action btn-danger">
                    <i data-lucide="rotate-ccw" style="width: 14px;"></i> Process Refund
                </button>
            </form>
        @endif
    </div>
</div>

<!-- 1. System Health Strip -->
<div class="health-strip">
    <div class="health-node">
        <span class="health-label">Payment Gateway</span>
        <span class="health-status" style="color: #059669;">● CAPTURED</span>
    </div>
    <div class="health-node">
        <span class="health-label">Execution Provider</span>
        <span class="health-status" style="color: #059669;">● {{ $activeFulfillment->provider->name ?? 'CJ Dropshipping' }}</span>
    </div>
    <div class="health-node">
        <span class="health-label">Fulfillment Ledger</span>
        <span class="health-status" style="color: #059669;">● {{ $activeFulfillment ? 'ACTIVE' : 'IDLE' }}</span>
    </div>
    <div class="health-node">
        <span class="health-label">Customer Status</span>
        <span class="health-status" style="color: var(--accent);">● {{ $customerStatus['status'] }}</span>
    </div>
    <div class="health-node">
        <span class="health-label">Address Verified</span>
        <span class="health-status" style="color: {{ $order->orderAddress ? '#059669' : '#ef4444' }};">
            {{ $order->orderAddress ? '● UN-TRUNCATED' : '○ MISSING' }}
        </span>
    </div>
</div>

<div class="cards-grid">
    <!-- 2. Customer & Shipping Card -->
    <div class="tower-card">
        <div class="card-header">
            <h3><i data-lucide="user" style="width: 16px;"></i> Customer & Verified Address</h3>
        </div>
        <div class="data-row">
            <span class="data-label">Customer Name</span>
            <span class="data-val">{{ $order->orderAddress->first_name ?? ($order->user->first_name ?? 'Guest') }} {{ $order->orderAddress->last_name ?? ($order->user->last_name ?? '') }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Email</span>
            <span class="data-val">{{ $order->orderAddress->email ?? ($order->user->email ?? 'N/A') }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Phone</span>
            <span class="data-val">{{ $order->orderAddress->phone ?? ($order->user->mobile ?? 'N/A') }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Address Line 1</span>
            <span class="data-val">{{ $order->orderAddress->address_line1 ?? 'N/A' }}</span>
        </div>
        @if(!empty($order->orderAddress->address_line2))
        <div class="data-row">
            <span class="data-label">Address Line 2</span>
            <span class="data-val">{{ $order->orderAddress->address_line2 }}</span>
        </div>
        @endif
        <div class="data-row">
            <span class="data-label">City, State ZIP</span>
            <span class="data-val">{{ $order->orderAddress->city ?? '' }}, {{ $order->orderAddress->state ?? '' }} {{ $order->orderAddress->postal_code ?? '' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Country</span>
            <span class="data-val">{{ $order->orderAddress->country ?? 'US' }}</span>
        </div>
    </div>

    <!-- 3. Payment Card -->
    <div class="tower-card">
        <div class="card-header">
            <h3><i data-lucide="credit-card" style="width: 16px;"></i> Payment & Financial Ledger</h3>
        </div>
        @php
            $latestPayment = $order->payments->first();
            $latestTx = $order->paymentTransactions->where('type', 'CAPTURE')->first();
        @endphp
        <div class="data-row">
            <span class="data-label">Payment Gateway</span>
            <span class="data-val">{{ strtoupper($latestPayment->payment_method ?? 'PAYPAL') }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Capture ID</span>
            <span class="data-val" style="font-family: monospace;">{{ $latestPayment->transaction_id ?? ($latestTx->provider_transaction_id ?? 'N/A') }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Amount Charged</span>
            <span class="data-val" style="color: var(--accent); font-weight: 700;">${{ number_format($order->total_amount, 2) }} USD</span>
        </div>
        <div class="data-row">
            <span class="data-label">Payment Status</span>
            <span class="data-val">{{ ucfirst($order->payment_status ?? 'Pending') }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Ledger Entries</span>
            <span class="data-val">{{ $order->paymentTransactions->count() }} records</span>
        </div>
    </div>

    <!-- 4. Line Items & Commercial Fidelity Card -->
    <div class="tower-card">
        <div class="card-header">
            <h3><i data-lucide="shopping-bag" style="width: 16px;"></i> Line Items & Variant Fidelity</h3>
        </div>
        @foreach($order->items as $item)
            @php
                $resolvedVid = \App\Services\Cj\CjOrderService::resolveVariantId($item);
                $cost = (float)($item->variant->cost_price ?? ($item->product->discount_price ? $item->product->discount_price * 0.4 : 0.00));
                $margin = (float)$item->unit_price - $cost;
            @endphp
            <div style="border-bottom: 1px solid var(--border-color); padding: 8px 0; margin-bottom: 8px;">
                <div style="font-weight: 700; font-size: 14px;">{{ $item->product->name ?? 'Product' }} (x{{ $item->quantity }})</div>
                <div class="data-row">
                    <span class="data-label">Merchant SKU</span>
                    <span class="data-val" style="font-family: monospace; color: var(--accent);">{{ $item->product->merchant_sku ?? 'AZG-001' }}</span>
                </div>
                @if($resolvedVid)
                <div class="data-row">
                    <span class="data-label">Mapped Provider VID</span>
                    <span class="data-val" style="font-family: monospace; color: #059669;">{{ $resolvedVid }}</span>
                </div>
                @endif
                <div class="data-row">
                    <span class="data-label">Selling Price</span>
                    <span class="data-val">${{ number_format($item->unit_price, 2) }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Estimated Margin</span>
                    <span class="data-val" style="color: #059669;">+${{ number_format($margin, 2) }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 5. Internal Supply Chain & Supplier Fulfillment Card (Admin Only) -->
    <div class="tower-card">
        <div class="card-header">
            <h3><i data-lucide="truck" style="width: 16px;"></i> Internal Supply Chain (Admin Only)</h3>
        </div>
        @php
            $supOrder = $order->supplierOrders->first();
            $cjOrd = $order->cjOrder;
            $supplierOrderId = $supOrder->external_order_id ?? ($cjOrd->cj_order_id ?? 'Not Dispatched');
            $prodCost = (float)($supOrder->product_cost ?? ($cjOrd->order_amount ?? 0.00));
            $shipCost = (float)($supOrder->shipping_cost ?? ($cjOrd->shipping_fee ?? 0.00));
            $totalSupplierCost = (float)($supOrder->total_cost ?? ($prodCost + $shipCost));
            $profitMargin = max(0, (float)$order->total_amount - $totalSupplierCost);
        @endphp
        <div class="data-row">
            <span class="data-label">Fulfillment Provider</span>
            <span class="data-val" style="font-weight: 700; color: #2563eb;">{{ $activeFulfillment->provider->name ?? 'CJ Dropshipping' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Supplier Order ID</span>
            <span class="data-val" style="font-family: monospace;">{{ $supplierOrderId }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Fulfillment Status</span>
            <span class="data-val">{{ $fStat }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Supplier Product Cost</span>
            <span class="data-val">${{ number_format($prodCost, 2) }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Supplier Freight Cost</span>
            <span class="data-val">${{ number_format($shipCost, 2) }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Net Profit Margin</span>
            <span class="data-val" style="color: #059669; font-weight: 700;">+${{ number_format($profitMargin, 2) }} USD</span>
        </div>
    </div>

    <!-- 6. White-Labeled Logistics & Customer Carrier -->
    <div class="tower-card">
        <div class="card-header">
            <h3><i data-lucide="navigation" style="width: 16px;"></i> White-Labeled Shipment & Tracking</h3>
        </div>
        @php
            $shipment = $order->shipment ?? ($order->shipments ? $order->shipments->first() : null);
        @endphp
        <div class="data-row">
            <span class="data-label">Customer Carrier Name</span>
            <span class="data-val">{{ $shipment->customer_carrier_name ?? 'Standard Delivery' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Shipment Status</span>
            <span class="data-val">{{ $shipment->status ?? ($shipment->shipment_status ?? 'NOT_SHIPPED') }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Tracking Number</span>
            <span class="data-val" style="font-family: monospace; font-weight: 700; color: var(--accent);">{{ $shipment->tracking_number ?? 'Pending Carrier Update' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Delivery SLA</span>
            <span class="data-val">7–15 Business Days</span>
        </div>
    </div>

    <!-- 7. Step-by-Step Chronological Audit & Attempt Timeline -->
    <div class="tower-card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h3><i data-lucide="clock" style="width: 16px;"></i> Step-by-Step Audit Timeline</h3>
        </div>
        <ul class="timeline-list">
            <li class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">{{ $order->created_at->format('M d, Y H:i:s') }}</div>
                <div class="timeline-desc">Order #{{ $order->order_number }} created in commercial ledger</div>
            </li>
            @foreach($order->paymentTransactions as $tx)
            <li class="timeline-item">
                <div class="timeline-dot" style="background: {{ $tx->type === 'CAPTURE' ? '#059669' : '#ef4444' }};"></div>
                <div class="timeline-time">{{ $tx->created_at->format('M d, Y H:i:s') }}</div>
                <div class="timeline-desc">
                    {{ $tx->type }} Verified (${{ number_format($tx->amount, 2) }} {{ $tx->currency }}) via {{ strtoupper($tx->provider) }} (Ref: {{ $tx->provider_transaction_id }})
                </div>
            </li>
            @endforeach
            @if($activeFulfillment)
                @foreach($activeFulfillment->attempts as $att)
                    <li class="timeline-item">
                        <div class="timeline-dot" style="background: {{ $att->status === 'SUCCESS' ? '#2563eb' : '#ef4444' }};"></div>
                        <div class="timeline-time">{{ $att->created_at->format('M d, Y H:i:s') }}</div>
                        <div class="timeline-desc">Fulfillment Attempt #{{ $att->attempt_number }} [{{ $att->idempotency_key }}]: {{ $att->status }} {{ $att->error_message ? ' - ' . $att->error_message : '' }}</div>
                    </li>
                @endforeach
                @foreach($activeFulfillment->exceptions as $exc)
                    <li class="timeline-item">
                        <div class="timeline-dot" style="background: #ef4444;"></div>
                        <div class="timeline-time">{{ $exc->created_at->format('M d, Y H:i:s') }}</div>
                        <div class="timeline-desc" style="color: #ef4444;">EXCEPTION LOGGED [{{ $exc->error_code }}]: {{ $exc->error_message }}</div>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection
