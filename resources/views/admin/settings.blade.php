@extends('layouts.admin')

@section('title', 'Settings - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
    .page-subtitle { color: var(--text-secondary); font-size: 14px; }
    
    .settings-layout { display: grid; grid-template-columns: 250px 1fr; gap: 32px; }
    @media (max-width: 768px) { .settings-layout { grid-template-columns: 1fr; } }
    
    .settings-nav { display: flex; flex-direction: column; gap: 4px; }
    .settings-nav-link { padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; }
    .settings-nav-link.active { background: rgba(128,128,128,0.1); color: var(--text-primary); font-weight: 600; }
    .settings-nav-link:hover:not(.active) { background: rgba(128,128,128,0.05); }
    
    .settings-panel { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); }
    .form-group input { width: 100%; max-width: 400px; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(128,128,128,0.05); color: var(--text-primary); outline: none; }
    .form-group input:focus { border-color: var(--accent); }
    
    .btn-save { padding: 10px 24px; border-radius: 8px; background: var(--accent); color: #fff; font-size: 14px; font-weight: 600; border: none; cursor: pointer; }
</style>

<div class="page-header">
    <h1 class="page-title">Settings</h1>
    <p class="page-subtitle">Manage your store configuration and integrations.</p>
</div>

<div class="settings-layout">
    <div class="settings-nav">
        <div class="settings-nav-link active">General</div>
        <div class="settings-nav-link">Payments</div>
        <div class="settings-nav-link">Shipping</div>
        <div class="settings-nav-link">CJ Dropshipping</div>
        <div class="settings-nav-link">API Keys</div>
    </div>
    
    <div class="settings-panel">
        <h2 class="panel-title">General Settings</h2>
        
        <div class="form-group">
            <label>Store Name</label>
            <input type="text" value="AtoZ Gadgetz">
        </div>
        
        <div class="form-group">
            <label>Support Email</label>
            <input type="email" value="support@atozgadgetz.com">
        </div>
        
        <div class="form-group">
            <label>Currency</label>
            <input type="text" value="USD ($)" disabled style="opacity: 0.7;">
        </div>
        
        <button class="btn-save">Save Changes</button>
    </div>
</div>
@endsection
