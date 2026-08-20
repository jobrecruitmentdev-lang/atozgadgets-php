@extends('layouts.admin')

@section('title', 'Store Settings - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    
    .settings-layout { display: grid; grid-template-columns: 240px 1fr; gap: 28px; }
    @media (max-width: 768px) { .settings-layout { grid-template-columns: 1fr; } }
    
    .settings-nav { display: flex; flex-direction: column; gap: 6px; }
    .settings-tab { padding: 12px 16px; border-radius: 10px; font-size: 14px; font-weight: 600; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
    .settings-tab:hover { background: rgba(128,128,128,0.08); color: var(--text-primary); }
    .settings-tab.active { background: rgba(201, 169, 98, 0.1); color: var(--accent); border-left-color: var(--accent); font-weight: 700; }
    
    .settings-panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 28px; }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px; color: var(--text-primary); }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); }
    .form-group input, .form-group select { width: 100%; max-width: 500px; padding: 12px 14px; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(128,128,128,0.06); color: var(--text-primary); outline: none; font-size: 14px; }
    .form-group input:focus, .form-group select:focus { border-color: var(--accent); }
    .form-help { font-size: 12px; color: var(--text-secondary); margin-top: 6px; }
    
    .btn-save { padding: 12px 28px; border-radius: 8px; background: var(--accent); color: #0a0a0c; font-size: 14px; font-weight: 700; border: none; cursor: pointer; transition: opacity 0.2s; }
    .btn-save:hover { opacity: 0.9; }
    
    .alert-success { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
</style>

<div class="page-header">
    <h1 class="page-title">Store Configuration & Integrations</h1>
    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Manage store parameters, CJ API credentials, and payment gateways.</p>
</div>

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    <div class="settings-layout">
        <div class="settings-nav">
            <div class="settings-tab active" onclick="showTab('tab-general', this)">General Settings</div>
            <div class="settings-tab" onclick="showTab('tab-cj', this)">CJ Dropshipping</div>
            <div class="settings-tab" onclick="showTab('tab-payments', this)">Payment Gateways</div>
            <div class="settings-tab" onclick="showTab('tab-shipping', this)">Shipping & Margins</div>
        </div>
        
        <div class="settings-panel">
            <!-- Tab 1: General -->
            <div id="tab-general" class="tab-pane">
                <h2 class="panel-title">General Settings</h2>
                <div class="form-group">
                    <label>Store Name</label>
                    <input type="text" name="store_name" value="{{ $settings['store_name'] }}" required>
                </div>
                <div class="form-group">
                    <label>Customer Support Email</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] }}" required>
                </div>
                <div class="form-group">
                    <label>Currency Code</label>
                    <input type="text" name="currency" value="{{ $settings['currency'] }}" required>
                    <p class="form-help">Default currency for catalog and checkout (e.g. USD, EUR).</p>
                </div>
                <div class="form-group">
                    <label>Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] }}" required>
                </div>
            </div>

            <!-- Tab 2: CJ Dropshipping -->
            <div id="tab-cj" class="tab-pane" style="display:none;">
                <h2 class="panel-title">CJ Dropshipping 2.0 Integration</h2>
                <div class="form-group">
                    <label>CJ Account Email</label>
                    <input type="email" name="cj_api_email" value="{{ $settings['cj_api_email'] }}" placeholder="e.g. user@cjdropshipping.com">
                </div>
                <div class="form-group">
                    <label>CJ API Key</label>
                    <input type="password" name="cj_api_key" value="{{ $settings['cj_api_key'] }}" placeholder="UserNum@api@xxxxxxxxxxxxxxx">
                    <p class="form-help">Found in My CJ > Authorization > API > API Key.</p>
                </div>
                <div class="form-group">
                    <label>Fulfillment Mode</label>
                    <select name="cj_auto_fulfill">
                        <option value="1" {{ $settings['cj_auto_fulfill'] == '1' ? 'selected' : '' }}>Automatic CJ Wallet Balance (payType: 2)</option>
                        <option value="0" {{ $settings['cj_auto_fulfill'] == '0' ? 'selected' : '' }}>Manual Admin Review Before Dispatch</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Low Wallet Balance Alert Threshold ($)</label>
                    <input type="number" name="cj_wallet_alert" value="{{ $settings['cj_wallet_alert'] }}" step="5">
                </div>
            </div>

            <!-- Tab 3: Payments -->
            <div id="tab-payments" class="tab-pane" style="display:none;">
                <h2 class="panel-title">Payment Gateways</h2>
                <div class="form-group">
                    <label>PayPal Mode</label>
                    <select name="paypal_mode">
                        <option value="sandbox" {{ $settings['paypal_mode'] == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                        <option value="live" {{ $settings['paypal_mode'] == 'live' ? 'selected' : '' }}>Live (Production)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>PayPal Client ID</label>
                    <input type="text" name="paypal_client_id" value="{{ $settings['paypal_client_id'] }}">
                </div>
                <div class="form-group">
                    <label>Payoneer Receiving Account / ID</label>
                    <input type="text" name="payoneer_account" value="{{ $settings['payoneer_account'] }}">
                </div>
            </div>

            <!-- Tab 4: Shipping & Margins -->
            <div id="tab-shipping" class="tab-pane" style="display:none;">
                <h2 class="panel-title">Pricing Margins & Shipping Rules</h2>
                <div class="form-group">
                    <label>Default Catalog Markup Multiplier</label>
                    <input type="number" name="default_markup" value="{{ $settings['default_markup'] }}" step="0.1" min="1.0">
                    <p class="form-help">Applied when importing new CJ products (e.g. 2.5 = 150% profit margin).</p>
                </div>
                <div class="form-group">
                    <label>Free Shipping Threshold ($)</label>
                    <input type="number" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold'] }}" step="1">
                    <p class="form-help">Orders above this subtotal qualify for free shipping.</p>
                </div>
            </div>

            <div style="margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn-save">Save Settings</button>
            </div>
        </div>
    </div>
</form>

<script>
    function showTab(tabId, el) {
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        el.classList.add('active');
        document.getElementById(tabId).style.display = 'block';
    }
</script>
@endsection