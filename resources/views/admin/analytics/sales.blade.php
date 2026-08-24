@extends('layouts.admin')

@section('title', 'Sales & Revenue Analytics')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; }
    .stat-title { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 6px; }
    .stat-value { font-size: 28px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Sales & Revenue Analytics</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Historical sales volume, order throughput, and daily run rate.</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Period Revenue ({{ $days }}d)</div>
        <div class="stat-value" style="color: var(--accent);">${{ number_format($metrics['total_revenue'], 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Paid Orders</div>
        <div class="stat-value">{{ $metrics['total_orders'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Average Order Value (AOV)</div>
        <div class="stat-value" style="color: #10b981;">${{ number_format($metrics['avg_order_val'], 2) }}</div>
    </div>
</div>

<div class="panel-card">
    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Daily Sales History</h3>
    <table class="table-modern">
        <thead>
            <tr>
                <th>Date</th>
                <th>Paid Orders</th>
                <th>Daily Gross Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesData as $d)
                <tr>
                    <td style="font-weight: 600;">{{ $d->date }}</td>
                    <td>{{ $d->total_orders }} orders</td>
                    <td style="font-weight: 700; color: #10b981;">${{ number_format($d->total_revenue, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: var(--text-secondary); padding: 24px;">No sales data available for this time range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
