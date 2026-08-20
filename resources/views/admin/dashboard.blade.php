@extends('layouts.admin')

@section('title', 'Admin Dashboard Overview')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    
    .stats-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 20px; margin-bottom: 28px; }
    @media (min-width: 640px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .stats-grid { grid-template-columns: repeat(4, 1fr); } }
    
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s, border-color 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); border-color: var(--accent); }
    .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .stat-title { font-size: 13px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 28px; font-weight: 800; color: var(--text-primary); }
    .stat-icon-wrap { width: 40px; height: 40px; border-radius: 10px; background: rgba(201, 169, 98, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; }
    
    .trend { display: flex; align-items: center; font-size: 13px; margin-top: 6px; }
    .trend-up { color: #10b981; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .trend-alert { color: #f59e0b; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    
    .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 28px; }
    @media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } }
    
    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 24px;
    }
    .panel-title { font-size: 17px; font-weight: 700; margin-bottom: 18px; color: var(--text-primary); display: flex; justify-content: space-between; align-items: center; }
    
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    
    .badge-status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status-paid { background: rgba(16,185,129,0.15); color: #10b981; }
    .status-pending { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .status-processing { background: rgba(59,130,246,0.15); color: #3b82f6; }
    
    .btn-quick { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-quick:hover { background: rgba(201,169,98,0.1); border-color: var(--accent); color: var(--accent); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Live Dashboard Overview</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Real-time metrics connected to your live database.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.catalog.import') }}" class="btn-quick">
            <i data-lucide="plus-circle" style="width:16px;"></i> Import CJ Products
        </a>
        <a href="{{ route('admin.reports') }}" class="btn-quick">
            <i data-lucide="bar-chart-2" style="width:16px;"></i> View Full Reports
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Total Revenue</p>
                <h3 class="stat-value">${{ number_format($stats['totalRevenue'], 2) }}</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="dollar-sign"></i></div>
        </div>
        <div class="trend">
            <span class="trend-up"><i data-lucide="trending-up" style="width:14px;"></i> Live Paid Sales</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Total Orders</p>
                <h3 class="stat-value">{{ $stats['totalOrders'] }}</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="shopping-bag"></i></div>
        </div>
        <div class="trend">
            <span class="trend-up">{{ $stats['processingOrders'] }} pending/processing</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Active Customers</p>
                <h3 class="stat-value">{{ $stats['totalCustomers'] }}</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="users"></i></div>
        </div>
        <div class="trend">
            <span class="trend-up">Registered User Accounts</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Low Stock Alerts</p>
                <h3 class="stat-value">{{ $stats['lowStockCount'] }}</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="alert-triangle" style="color:var(--warning);"></i></div>
        </div>
        <div class="trend">
            <span class="{{ $stats['lowStockCount'] > 0 ? 'trend-alert' : 'trend-up' }}">
                {{ $stats['lowStockCount'] > 0 ? 'Items need restock (<5 units)' : 'All Inventory Healthy' }}
            </span>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Recent Live Orders Table -->
    <div class="panel-card">
        <div class="panel-title">
            <span>Recent Orders</span>
            <a href="{{ route('admin.orders') }}" class="btn-quick" style="font-size:12px; padding:4px 10px;">View All</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $ord)
                    <tr>
                        <td><strong>{{ $ord->order_number }}</strong></td>
                        <td>{{ $ord->user ? ($ord->user->first_name . ' ' . $ord->user->last_name) : 'Guest' }}</td>
                        <td><strong>${{ number_format($ord->total_amount, 2) }}</strong></td>
                        <td>
                            <span class="badge-status {{ in_array($ord->payment_status, ['paid', 'completed', 'success']) ? 'status-paid' : 'status-pending' }}">
                                {{ $ord->payment_status ?? 'pending' }}
                            </span>
                        </td>
                        <td>{{ $ord->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 24px;">No orders found yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="panel-card">
        <div class="panel-title">Top Products</div>
        <div style="display: flex; flex-direction: column; gap: 14px;">
            @forelse($stats['topProducts'] as $tp)
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid var(--border-color);">
                <div>
                    <p style="font-weight: 600; font-size: 14px;">{{ Str::limit($tp->name, 28) }}</p>
                    <p style="font-size: 12px; color: var(--text-secondary);">${{ number_format($tp->price, 2) }} | Stock: {{ $tp->stock }}</p>
                </div>
                <span class="nav-status-badge status-live" style="font-size:11px; padding:3px 8px; background:rgba(201,169,98,0.15); color:var(--accent); border-radius:6px; font-weight:700;">
                    {{ $tp->order_items_count ?? 0 }} sold
                </span>
            </div>
            @empty
            <p style="font-size: 13px; color: var(--text-secondary);">No sales history recorded.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection