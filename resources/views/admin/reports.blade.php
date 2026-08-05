@extends('layouts.admin')

@section('title', 'Reports - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    
    .btn-outline { padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-outline:hover { background: rgba(128,128,128,0.1); }

    .reports-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
    .report-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; display: flex; flex-direction: column; }
    .report-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(128,128,128,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: var(--accent); }
    .report-title { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
    .report-desc { font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; flex-grow: 1; }
    
    .btn-primary { width: 100%; padding: 10px; border-radius: 8px; background: var(--accent); color: #fff; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-align: center; }
    .btn-primary:hover { opacity: 0.9; }
</style>

<div class="page-header">
    <h1 class="page-title">Reports</h1>
    <button class="btn-outline">
        <i data-lucide="download" style="width:16px;"></i> Export All Data
    </button>
</div>

<div class="reports-grid">
    <div class="report-card">
        <div class="report-icon"><i data-lucide="trending-up"></i></div>
        <h3 class="report-title">Sales Analytics</h3>
        <p class="report-desc">View detailed breakdowns of your daily, weekly, and monthly revenue trends.</p>
        <button class="btn-primary">View Report</button>
    </div>
    
    <div class="report-card">
        <div class="report-icon"><i data-lucide="users"></i></div>
        <h3 class="report-title">Customer Acquisition</h3>
        <p class="report-desc">Track new signups, conversion rates, and lifetime value metrics.</p>
        <button class="btn-primary">View Report</button>
    </div>
    
    <div class="report-card">
        <div class="report-icon"><i data-lucide="package"></i></div>
        <h3 class="report-title">Inventory Status</h3>
        <p class="report-desc">Identify low stock items, top performing products, and CJ Dropshipping sync health.</p>
        <button class="btn-primary">View Report</button>
    </div>
    
    <div class="report-card">
        <div class="report-icon"><i data-lucide="pie-chart"></i></div>
        <h3 class="report-title">Financial Summary</h3>
        <p class="report-desc">Profit margin analysis, calculated from supplier cost versus retail price.</p>
        <button class="btn-primary">View Report</button>
    </div>
</div>
@endsection
