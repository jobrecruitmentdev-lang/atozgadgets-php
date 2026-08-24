@extends('layouts.admin')

@section('title', 'Product Performance Analytics')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Top Performing Products</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Top selling catalog products ranked by order item frequency.</p>
    </div>
</div>

<div class="panel-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Times Ordered</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $idx => $p)
                <tr>
                    <td style="font-weight: 700; color: var(--accent);">#{{ $idx + 1 }}</td>
                    <td style="font-weight: 600;">{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? 'Gadgets' }}</td>
                    <td style="font-weight: 700;">${{ number_format($p->price, 2) }}</td>
                    <td><strong>{{ $p->order_items_count }}</strong> orders</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 24px;">No product order data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
