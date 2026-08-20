@extends('layouts.admin')

@section('title', 'Reports & Analytics - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    
    .filter-bar { display: flex; align-items: center; gap: 10px; }
    .filter-btn { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid var(--border-color); background: transparent; color: var(--text-secondary); text-decoration: none; }
    .filter-btn.active { background: var(--accent); color: #0a0a0c; font-weight: 700; border-color: var(--accent); }
    
    .reports-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px; }
    .report-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 22px; }
    .report-label { font-size: 13px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 8px; }
    .report-val { font-size: 28px; font-weight: 800; color: var(--text-primary); }
    
    .export-box { background: rgba(201,169,98,0.06); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; margin-top: 28px; }
    .btn-export { padding: 10px 18px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-export:hover { background: var(--accent); color: #0a0a0c; border-color: var(--accent); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Sales Analytics & Data Exports</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Live financial reports across the last {{ $days }} days.</p>
    </div>
    
    <div class="filter-bar">
        <a href="{{ route('admin.reports', ['range' => '7']) }}" class="filter-btn {{ $days == 7 ? 'active' : '' }}">7 Days</a>
        <a href="{{ route('admin.reports', ['range' => '30']) }}" class="filter-btn {{ $days == 30 ? 'active' : '' }}">30 Days</a>
        <a href="{{ route('admin.reports', ['range' => '90']) }}" class="filter-btn {{ $days == 90 ? 'active' : '' }}">90 Days</a>
        <a href="{{ route('admin.reports', ['range' => '365']) }}" class="filter-btn {{ $days == 365 ? 'active' : '' }}">Year</a>
    </div>
</div>

<div class="reports-grid">
    <div class="report-card">
        <p class="report-label">Period Revenue</p>
        <h3 class="report-val">${{ number_format($revenue, 2) }}</h3>
    </div>
    <div class="report-card">
        <p class="report-label">Total Orders Placed</p>
        <h3 class="report-val">{{ $orderCount }}</h3>
    </div>
    <div class="report-card">
        <p class="report-label">Average Order Value (AOV)</p>
        <h3 class="report-val">${{ number_format($aov, 2) }}</h3>
    </div>
</div>

<!-- Export Suite -->
<div class="export-box">
    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">Download Raw CSV Exports</h3>
    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 18px;">Export uncompressed transaction, inventory, and customer databases for accounting and tax reporting.</p>
    <div style="display: flex; gap: 14px; flex-wrap: wrap;">
        <a href="{{ route('admin.reports.export', ['type' => 'orders']) }}" class="btn-export">
            <i data-lucide="download" style="width:16px;"></i> Export Orders CSV
        </a>
        <a href="{{ route('admin.reports.export', ['type' => 'inventory']) }}" class="btn-export">
            <i data-lucide="download" style="width:16px;"></i> Export Inventory CSV
        </a>
        <a href="{{ route('admin.reports.export', ['type' => 'customers']) }}" class="btn-export">
            <i data-lucide="download" style="width:16px;"></i> Export Customers CSV
        </a>
    </div>
</div>
@endsection