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
    .settings-tab.active { background: rgba(201, 169, 98, 0.12); color: var(--accent); border-left-color: var(--accent); font-weight: 700; }
    
    .settings-panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px; color: var(--text-primary); display: flex; align-items: center; justify-content: space-between; }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); }
    .form-group input, .form-group select { width: 100%; max-width: 520px; padding: 12px 16px; border-radius: 10px; border: 1px solid var(--border-color); background: rgba(15, 15, 20, 0.6); color: var(--text-primary); outline: none; font-size: 14px; transition: all 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(201, 169, 98, 0.15); background: rgba(20, 20, 28, 0.9); }
    .form-group select option { background: #121217; color: #fff; }
    .form-help { font-size: 12px; color: var(--text-secondary); margin-top: 6px; line-height: 1.4; }
    
    /* Toggle Card */
    .mode-card { background: rgba(20, 20, 28, 0.6); border: 1px solid var(--border-color); border-radius: 14px; padding: 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .mode-info h4 { font-size: 15px; font-weight: 700; margin-bottom: 4px; color: var(--text-primary); }
    .mode-info p { font-size: 12px; color: var(--text-secondary); margin: 0; max-width: 480px; }
    
    /* Modern Switch Switcher */
    .switch-container { display: flex; align-items: center; gap: 12px; }
    .badge-mode { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; transition: all 0.3s; }
    .badge-sandbox { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .badge-live { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    
    .btn-toggle-mode { padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s; border: 1px solid var(--border-color); background: rgba(255,255,255,0.05); color: var(--text-primary); display: inline-flex; align-items: center; gap: 6px; }
    .btn-toggle-mode:hover { background: rgba(201, 169, 98, 0.15); border-color: var(--accent); color: var(--accent); }
    
    /* Action Buttons */
    .tab-actions { margin-top: 28px; border-top: 1px solid var(--border-color); padding-top: 20px; display: flex; align-items: center; gap: 12px; }
    .btn-save { padding: 12px 28px; border-radius: 10px; background: var(--accent); color: #0a0a0c; font-size: 14px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(201, 169, 98, 0.2); }
    .btn-save:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(201, 169, 98, 0.3); }
    .btn-save:disabled { opacity: 0.5; cursor: not-allowed; }
    
    .btn-secondary { padding: 11px 20px; border-radius: 10px; background: rgba(255,255,255,0.06); color: var(--text-primary); font-size: 13px; font-weight: 600; border: 1px solid var(--border-color); cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary:hover { background: rgba(255,255,255,0.1); border-color: var(--text-secondary); }
    
    /* Test Connection Box */
    .connection-status-box { margin-top: 12px; padding: 12px 16px; border-radius: 10px; font-size: 13px; display: none; }
    .status-success { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; }
    .status-error { background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; }

    /* Toast Notification */
    .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; }
    .toast-msg { padding: 14px 22px; border-radius: 12px; font-size: 14px; font-weight: 600; background: #181820; color: #fff; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.5); animation: toastIn 0.3s ease; display: flex; align-items: center; gap: 10px; }
    .toast-msg.success { border-color: #10b981; color: #10b981; }
    .toast-msg.error { border-color: #ef4444; color: #ef4444; }
    @keyframes toastIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="page-header">
    <h1 class="page-title">Store Configuration & Integrations</h1>
    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Manage store parameters, live CJ API integration, and payment gateways.</p>
</div>

<div class="toast-container" id="toastContainer"></div>

<form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" onsubmit="handleFormSubmit(event)">
    @csrf
    <input type="hidden" name="cj_sandbox_mode" id="cjSandboxInput" value="{{ $settings['cj_sandbox_mode'] }}">
    
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
                <div class="panel-title">
                    <span>General Settings</span>
                </div>
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
                
                <div class="tab-actions">
                    <button type="submit" class="btn-save">Save General Settings</button>
                </div>
            </div>

            <!-- Tab 2: CJ Dropshipping -->
            <div id="tab-cj" class="tab-pane" style="display:none;">
                <div class="panel-title">
                    <span>CJ Dropshipping 2.0 Integration</span>
                </div>

                <!-- Mode Card -->
                <div class="mode-card">
                    <div class="mode-info">
                        <h4>Environment Gateway Mode</h4>
                        <p>Toggle between Mock Sandbox (safe catalog testing without rate-limits) and Live Production API.</p>
                    </div>
                    <div class="switch-container">
                        <span class="badge-mode {{ $settings['cj_sandbox_mode'] == '1' ? 'badge-sandbox' : 'badge-live' }}" id="sandboxStatusBadge">
                            {{ $settings['cj_sandbox_mode'] == '1' ? '⚠️ Sandbox Active' : '🟢 Live API Active' }}
                        </span>
                        <button type="button" onclick="toggleSandboxAjax()" class="btn-toggle-mode" id="btnToggleSandbox">
                            <i data-lucide="refresh-cw" style="width:14px;"></i>
                            <span id="btnToggleText">{{ $settings['cj_sandbox_mode'] == '1' ? 'Switch to Live API' : 'Switch to Sandbox' }}</span>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>CJ Account Email</label>
                    <input type="email" name="cj_api_email" id="cjApiEmail" value="{{ $settings['cj_api_email'] }}" placeholder="e.g. your-email@cjdropshipping.com">
                    <p class="form-help">The registered email of your CJ Dropshipping developer account.</p>
                </div>

                <div class="form-group">
                    <label>CJ API Key</label>
                    <input type="password" name="cj_api_key" id="cjApiKey" value="{{ $settings['cj_api_key'] }}" placeholder="UserNum@api@xxxxxxxxxxxxxxx">
                    <p class="form-help">Found in My CJ > Authorization > API > API Key.</p>
                </div>

                <div style="margin-bottom: 24px;">
                    <button type="button" onclick="testCjApiConnection()" class="btn-secondary" id="btnTestConn">
                        <i data-lucide="activity" style="width:14px;"></i> Test CJ API Connection
                    </button>
                    <div id="connectionStatusBox" class="connection-status-box"></div>
                </div>

                <div class="form-group">
                    <label>Fulfillment Dispatch Mode</label>
                    <select name="cj_auto_fulfill">
                        <option value="1" {{ $settings['cj_auto_fulfill'] == '1' ? 'selected' : '' }}>Automatic Wallet Fulfillment (PayType: 2)</option>
                        <option value="0" {{ $settings['cj_auto_fulfill'] == '0' ? 'selected' : '' }}>Manual Admin Review Before Dispatch</option>
                    </select>
                    <p class="form-help">Choose whether customer orders are automatically auto-submitted to CJ or held for admin manual approval.</p>
                </div>

                <div class="tab-actions">
                    <button type="submit" class="btn-save">Save CJ Settings</button>
                </div>
            </div>

            <!-- Tab 3: Payments -->
            <div id="tab-payments" class="tab-pane" style="display:none;">
                <div class="panel-title">
                    <span>Payment Gateways Configuration</span>
                </div>

                <!-- PayPal Environment Mode -->
                <div class="mode-card" style="margin-bottom: 24px;">
                    <div class="mode-info">
                        <h4>PayPal Active Gateway Mode</h4>
                        <p>Toggle between Sandbox (testing with virtual accounts) and Live (real financial transactions).</p>
                    </div>
                    <div class="switch-container">
                        <span class="badge-mode {{ $settings['paypal_mode'] == 'sandbox' ? 'badge-sandbox' : 'badge-live' }}" id="paypalStatusBadge">
                            {{ $settings['paypal_mode'] == 'sandbox' ? '🟡 Sandbox Active' : '🟢 Live Active' }}
                        </span>
                        <select name="paypal_mode" id="paypalModeSelect" onchange="updatePaypalBadge(this)" style="padding: 10px 16px; border-radius: 10px; border: 1px solid var(--border-color); background: rgba(15, 15, 20, 0.85); color: #ffffff; font-weight: 600; font-size: 13px; cursor: pointer; outline: none;">
                            <option value="sandbox" {{ $settings['paypal_mode'] == 'sandbox' ? 'selected' : '' }}>🟡 Sandbox Mode (Testing)</option>
                            <option value="live" {{ $settings['paypal_mode'] == 'live' ? 'selected' : '' }}>🟢 Live Mode (Production)</option>
                        </select>
                    </div>
                </div>

                <!-- Sandbox Credentials Box -->
                <div style="background: rgba(234, 179, 8, 0.05); border: 1px solid rgba(234, 179, 8, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="margin-bottom: 16px; font-size: 15px; font-weight: 700; color: #eab308; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="flask-conical" style="width: 18px;"></i> PayPal Sandbox Credentials
                    </h4>
                    <div class="form-group">
                        <label>Sandbox Client ID</label>
                        <input type="text" name="paypal_sandbox_client_id" value="{{ $settings['paypal_sandbox_client_id'] }}" placeholder="e.g. A21AA...sandbox_client_id">
                        <p class="form-help">Found in PayPal Developer Dashboard > Apps & Credentials (Sandbox).</p>
                    </div>
                    <div class="form-group">
                        <label>Sandbox Client Secret</label>
                        <input type="password" name="paypal_sandbox_client_secret" value="{{ $settings['paypal_sandbox_client_secret'] }}" placeholder="••••••••••••••••••••••••">
                        <p class="form-help">Your sandbox application client secret.</p>
                    </div>
                </div>

                <!-- Live Credentials Box -->
                <div style="background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="margin-bottom: 16px; font-size: 15px; font-weight: 700; color: #22c55e; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="shield-check" style="width: 18px;"></i> PayPal Live Production Credentials
                    </h4>
                    <div class="form-group">
                        <label>Live Client ID</label>
                        <input type="text" name="paypal_live_client_id" value="{{ $settings['paypal_live_client_id'] }}" placeholder="e.g. AX_live_client_id...">
                        <p class="form-help">Found in PayPal Developer Dashboard > Apps & Credentials (Live).</p>
                    </div>
                    <div class="form-group">
                        <label>Live Client Secret</label>
                        <input type="password" name="paypal_live_client_secret" value="{{ $settings['paypal_live_client_secret'] }}" placeholder="••••••••••••••••••••••••">
                        <p class="form-help">Your live production application client secret.</p>
                    </div>
                </div>

                <!-- Alternative Gateways -->
                <div class="form-group">
                    <label>Payoneer Receiving Account / ID</label>
                    <input type="text" name="payoneer_account" value="{{ $settings['payoneer_account'] }}" placeholder="e.g. billing@atozgadgetz.com">
                    <p class="form-help">Receiving account for manual/Payoneer bank wire payments.</p>
                </div>

                <div class="tab-actions">
                    <button type="submit" class="btn-save">Save Payment Settings</button>
                </div>
            </div>

            <!-- Tab 4: Shipping & Margins -->
            <div id="tab-shipping" class="tab-pane" style="display:none;">
                <div class="panel-title">
                    <span>Pricing Margins & Shipping Rules</span>
                </div>
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

                <div class="tab-actions">
                    <button type="submit" class="btn-save">Save Shipping Rules</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast-msg ${type}`;
        toast.innerHTML = `${type === 'success' ? '✓' : '✕'} ${message}`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    function showTab(tabId, el) {
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        el.classList.add('active');
        document.getElementById(tabId).style.display = 'block';
    }

    function updatePaypalBadge(selectElem) {
        const badge = document.getElementById('paypalStatusBadge');
        if (!badge) return;
        if (selectElem.value === 'sandbox') {
            badge.className = 'badge-mode badge-sandbox';
            badge.innerText = '🟡 Sandbox Active';
        } else {
            badge.className = 'badge-mode badge-live';
            badge.innerText = '🟢 Live Active';
        }
    }

    async function handleFormSubmit(event) {
        event.preventDefault();
        const form = document.getElementById('settingsForm');
        const formData = new FormData(form);
        const submitBtns = form.querySelectorAll('.btn-save');
        
        submitBtns.forEach(btn => {
            btn.disabled = true;
            btn.innerText = 'Saving...';
        });

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message || 'Settings saved successfully!', 'success');
            } else {
                showToast('Failed to save settings', 'error');
            }
        } catch (e) {
            showToast('Network error while saving settings', 'error');
        } finally {
            submitBtns.forEach(btn => {
                btn.disabled = false;
                btn.innerText = 'Save Settings';
            });
        }
    }

    async function toggleSandboxAjax() {
        const btn = document.getElementById('btnToggleSandbox');
        const btnText = document.getElementById('btnToggleText');
        const badge = document.getElementById('sandboxStatusBadge');
        const input = document.getElementById('cjSandboxInput');
        btn.disabled = true;
        btnText.innerText = 'Switching...';

        try {
            const res = await fetch('{{ route('admin.settings.toggle_cj_sandbox') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                if (data.sandbox_mode) {
                    badge.innerText = '⚠️ Sandbox Active';
                    badge.className = 'badge-mode badge-sandbox';
                    btnText.innerText = 'Switch to Live API';
                    if (input) input.value = '1';
                    showToast('Switched to CJ Sandbox Mode (Mock Data)', 'success');
                } else {
                    badge.innerText = '🟢 Live API Active';
                    badge.className = 'badge-mode badge-live';
                    btnText.innerText = 'Switch to Sandbox';
                    if (input) input.value = '0';
                    showToast('Switched to CJ Live Production Mode', 'success');
                }
            }
        } catch (e) {
            showToast('Failed to toggle sandbox mode', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    async function testCjApiConnection() {
        const btn = document.getElementById('btnTestConn');
        const box = document.getElementById('connectionStatusBox');
        const email = document.getElementById('cjApiEmail').value;
        const apiKey = document.getElementById('cjApiKey').value;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Testing...';
        box.style.display = 'none';

        try {
            const res = await fetch('{{ route('admin.settings.test_cj_connection') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cj_api_email: email,
                    cj_api_key: apiKey
                })
            });

            const data = await res.json();
            box.style.display = 'block';

            if (res.ok && data.success) {
                box.className = 'connection-status-box status-success';
                box.innerHTML = `<strong>✓ ${data.message}</strong> (Latency: ${data.latency_ms}ms)`;
                showToast('CJ API Connection Verified!', 'success');
            } else {
                box.className = 'connection-status-box status-error';
                box.innerHTML = `<strong>✕ Connection Failed:</strong> ${data.message || 'Invalid Credentials'}`;
                showToast('CJ API Connection Failed', 'error');
            }
        } catch (e) {
            box.style.display = 'block';
            box.className = 'connection-status-box status-error';
            box.innerHTML = `<strong>✕ Error:</strong> Unable to communicate with server.`;
            showToast('Connection test error', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="activity" style="width:14px;"></i> Test CJ API Connection';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }
</script>
@endsection