@extends('layouts.admin')

@section('title', 'Pending Fulfillment Queue')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .btn-action { padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; border: none; text-decoration: none; }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Pending Fulfillment Queue</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Orders captured and awaiting dispatch to execution providers.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.fulfillment.queue') }}" class="btn-action {{ request('filter') !== 'stale' ? 'btn-primary' : 'btn-outline' }}">All Pending</a>
        <a href="{{ route('admin.fulfillment.queue', ['filter' => 'stale']) }}" class="btn-action {{ request('filter') === 'stale' ? 'btn-primary' : 'btn-outline' }}" style="{{ request('filter') === 'stale' ? 'background:#d97706;' : 'color:#d97706;' }}">
            Pending > 2h
        </a>
    </div>
</div>

<div class="panel-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer & Address</th>
                <th>Items to Fulfill</th>
                <th>Provider</th>
                <th>Age</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fulfillments as $f)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $f->order_id) }}" style="color: var(--accent); font-weight: 700;">{{ $f->order->order_number ?? 'Order #' . $f->order_id }}</a>
                    </td>
                    <td>
                        <div><strong>{{ $f->order->orderAddress->first_name ?? ($f->order->user->first_name ?? 'Guest') }} {{ $f->order->orderAddress->last_name ?? '' }}</strong></div>
                        <div style="font-size: 12px; color: var(--text-secondary);">{{ $f->order->orderAddress->city ?? '' }}, {{ $f->order->orderAddress->country ?? 'US' }}</div>
                    </td>
                    <td>
                        @foreach($f->items as $it)
                            <div style="font-size: 13px;">{{ $it->orderItem->product->name ?? 'Product' }} (x{{ $it->quantity }})</div>
                        @endforeach
                    </td>
                    <td>{{ $f->provider->name ?? 'CJ Dropshipping' }}</td>
                    <td style="color: var(--text-secondary);">{{ $f->created_at->diffForHumans() }}</td>
                    <td>
                        <form action="{{ route('admin.fulfillment.retry', $f->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-primary">Dispatch Now</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 32px;">No pending orders in fulfillment queue. All paid orders are currently dispatched.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $fulfillments->links() }}
    </div>
</div>
@endsection
