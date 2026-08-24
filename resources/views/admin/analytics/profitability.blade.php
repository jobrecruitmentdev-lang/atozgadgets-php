@extends('layouts.admin')

@section('title', 'Unit Economics & Profitability')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .margin-healthy { color: #10b981; font-weight: 700; }
    .margin-warning { color: #f59e0b; font-weight: 700; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Unit Economics & Margins</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Monitor gross margin spreads, supplier costs, and low-margin warnings.</p>
    </div>
</div>

<div class="panel-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Product / SKU</th>
                <th>Selling Price</th>
                <th>Est. Supplier Cost</th>
                <th>Est. Net Margin</th>
                <th>Margin %</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                @php
                    $cost = (float)($p->variants->first()?->cost_price ?? ($p->discount_price ? $p->discount_price * 0.4 : $p->price * 0.4));
                    $margin = max(0, (float)$p->price - $cost);
                    $marginPct = $p->price > 0 ? round(($margin / $p->price) * 100) : 0;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $p->name }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); font-family: monospace;">{{ $p->merchant_sku }}</div>
                    </td>
                    <td style="font-weight: 700;">${{ number_format($p->price, 2) }}</td>
                    <td style="color: var(--text-secondary);">${{ number_format($cost, 2) }}</td>
                    <td class="{{ $marginPct >= 25 ? 'margin-healthy' : 'margin-warning' }}">+${{ number_format($margin, 2) }}</td>
                    <td>
                        <span class="{{ $marginPct >= 25 ? 'margin-healthy' : 'margin-warning' }}">{{ $marginPct }}%</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 24px;">No products in catalog.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $products->links() }}
    </div>
</div>
@endsection
