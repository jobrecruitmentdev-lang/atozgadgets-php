@extends('layouts.admin')

@section('title', 'Fulfillment Overview')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px; }
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; text-decoration: none; color: inherit; transition: transform 0.2s, border-color 0.2s; }
    .stat-card:hover { transform: translateY(-2px); border-color: var(--accent); }
    .stat-title { font-size: 12px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: 800; color: var(--text-primary); }
    
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    
    .badge-status { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-pending { background: rgba(245,158,11,0.15); color: #d97706; }
    .badge-submitted { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .badge-exception { background: rgba(239,68,68,0.15); color: #ef4444; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Fulfillment Overview</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Monitor supplier execution throughput, pending queues, and active exceptions.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.fulfillment.queue') }}" style="padding: 10px 18px; border-radius: 8px; background: var(--accent); color: #fff; font-weight: 700; font-size: 13px; text-decoration: none;">View Pending Queue</a>
    </div>
</div>

<div class="stats-grid">
    <a href="{{ route('admin.fulfillment.queue') }}" class="stat-card">
        <div class="stat-title">Pending Dispatch</div>
        <div class="stat-value" style="color: #d97706;">{{ $counts['pending'] }}</div>
    </a>
    <a href="{{ route('admin.fulfillment.shipments') }}" class="stat-card">
        <div class="stat-title">Submitted / In Flight</div>
        <div class="stat-value" style="color: #3b82f6;">{{ $counts['submitted'] }}</div>
    </a>
    <a href="{{ route('admin.fulfillment.exceptions') }}" class="stat-card">
        <div class="stat-title">Open Exceptions</div>
        <div class="stat-value" style="color: #ef4444;">{{ $counts['exceptions'] }}</div>
    </a>
    <a href="{{ route('admin.fulfillment.shipments') }}" class="stat-card">
        <div class="stat-title">Total Active Shipments</div>
        <div class="stat-value">{{ $counts['shipments'] }}</div>
    </a>
</div>

<div class="panel-card">
    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Recent Fulfillment Activity</h3>
    <table class="table-modern">
        <thead>
            <tr>
                <th>Fulfillment ID</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Provider</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentFulfillments as $f)
                <tr>
                    <td style="font-family: monospace; font-weight: 700;">#FLM-{{ str_pad($f->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td><a href="{{ route('admin.orders.show', $f->order_id) }}" style="color: var(--accent); font-weight: 700;">{{ $f->order->order_number ?? 'Order #' . $f->order_id }}</a></td>
                    <td>{{ $f->order->orderAddress->first_name ?? ($f->order->user->first_name ?? 'Guest') }}</td>
                    <td>{{ $f->provider->name ?? 'CJ Dropshipping' }}</td>
                    <td>
                        <span class="badge-status badge-{{ strtolower($f->fulfillment_status) }}">{{ $f->fulfillment_status }}</span>
                    </td>
                    <td style="color: var(--text-secondary);">{{ $f->created_at->format('M d, H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $f->order_id) }}" style="color: var(--accent); font-weight: 600;">Control Tower →</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 24px;">No fulfillment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
