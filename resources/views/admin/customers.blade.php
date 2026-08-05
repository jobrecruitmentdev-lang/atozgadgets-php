@extends('layouts.admin')

@section('title', 'Customers - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    
    .btn-outline { padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-outline:hover { background: rgba(128,128,128,0.1); }

    .data-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    
    .filter-bar { padding: 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; gap: 16px; }
    .search-wrapper { position: relative; flex-grow: 1; max-width: 400px; }
    .search-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); width: 16px; }
    .search-wrapper input { width: 100%; padding: 12px 16px 12px 42px; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(128,128,128,0.05); color: var(--text-primary); font-size: 14px; outline: none; }
    .search-wrapper input:focus { border-color: var(--accent); }

    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 16px 24px; font-size: 13px; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); background: rgba(128,128,128,0.05); }
    td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    
    .empty-state { text-align: center; padding: 64px 24px; color: var(--text-secondary); font-size: 14px; }
</style>

<div class="page-header">
    <h1 class="page-title">Customers</h1>
    <button class="btn-outline">
        <i data-lucide="filter" style="width:16px;"></i> Advanced Filters
    </button>
</div>

<div class="data-card">
    <div class="filter-bar">
        <div class="search-wrapper">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Search customers...">
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Total Orders</th>
                <th>Total Spent</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>#{{ $customer->id }}</td>
                    <td style="font-weight: 600;">{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->orders()->count() ?? 0 }}</td>
                    <td style="font-weight: 700; color: var(--accent);">${{ number_format($customer->orders()->sum('total_amount') ?? 0, 2) }}</td>
                    <td><span style="padding: 4px 8px; border-radius: 4px; background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 12px; font-weight: 600;">Active</span></td>
                    <td style="text-align: right;">
                        <button style="background:transparent; border:none; color:var(--text-secondary); cursor:pointer;"><i data-lucide="eye" style="width:16px;"></i></button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No customers found for the current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
