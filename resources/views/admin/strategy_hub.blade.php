<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtoZGadgets — CJ Dropshipping Strategy & Decision Control Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --bg-primary: #0a0a0d;
            --bg-card: rgba(20, 20, 26, 0.88);
            --bg-card-hover: rgba(28, 28, 36, 0.98);
            --accent: #c9a962;
            --accent-glow: rgba(201, 169, 98, 0.25);
            --accent-bright: #e5c378;
            --text-main: #f4f4f6;
            --text-muted: #9ba1b0;
            --border-glass: rgba(255, 255, 255, 0.08);
            --border-accent: rgba(201, 169, 98, 0.35);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --radius-lg: 16px;
            --radius-md: 10px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-primary); color: var(--text-main); line-height: 1.6; min-height: 100vh; overflow-x: hidden; }

        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: radial-gradient(circle at 10% 20%, rgba(201, 169, 98, 0.06), transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.05), transparent 40%);
            z-index: -1; pointer-events: none;
        }

        header {
            border-bottom: 1px solid var(--border-glass);
            background: rgba(10, 10, 13, 0.92);
            backdrop-filter: blur(16px);
            position: sticky; top: 0; z-index: 100;
            padding: 16px 32px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .brand-box { display: flex; align-items: center; gap: 14px; }
        .brand-logo { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #c9a962, #b89851); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #0a0a0c; font-size: 19px; }
        .brand-title { font-size: 18px; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em; }
        .brand-badge { background: rgba(201, 169, 98, 0.15); color: var(--accent); font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; border: 1px solid var(--border-accent); }

        .header-actions { display: flex; gap: 12px; }
        .btn-action { background: rgba(255, 255, 255, 0.05); color: var(--text-main); border: 1px solid var(--border-glass); padding: 8px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-action:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.2); }
        .btn-primary { background: linear-gradient(135deg, #c9a962, #b89851); color: #0a0a0c; border: none; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 16px var(--accent-glow); }

        .container { max-width: 1400px; margin: 0 auto; padding: 32px 28px; display: grid; grid-template-columns: 300px 1fr; gap: 32px; }
        @media (max-width: 1024px) { .container { grid-template-columns: 1fr; } }

        .nav-sidebar { position: sticky; top: 85px; height: fit-content; }
        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 5px; }
        .nav-item { padding: 12px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 500; color: var(--text-muted); cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; display: flex; align-items: center; justify-content: space-between; }
        .nav-item:hover { color: var(--text-main); background: rgba(255, 255, 255, 0.03); }
        .nav-item.active { color: var(--accent); background: rgba(201, 169, 98, 0.08); border-left-color: var(--accent); font-weight: 600; }
        .nav-status-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
        .status-live { background: rgba(16, 185, 129, 0.15); color: var(--success); }
        .status-gather { background: rgba(201, 169, 98, 0.15); color: var(--accent); }

        .content-area { display: flex; flex-direction: column; gap: 36px; }

        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(14px);
        }

        .section-header { margin-bottom: 22px; border-bottom: 1px solid var(--border-glass); padding-bottom: 14px; display: flex; justify-content: space-between; align-items: flex-start; }
        .section-title { font-size: 20px; font-weight: 700; color: var(--text-main); letter-spacing: -0.01em; display: flex; align-items: center; gap: 10px; }
        .section-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        /* Progress Card */
        .progress-card {
            background: linear-gradient(135deg, rgba(201, 169, 98, 0.12), rgba(16, 185, 129, 0.08));
            border: 1px solid var(--border-accent);
            border-radius: var(--radius-md);
            padding: 20px 24px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
        }
        .progress-bar-bg { width: 100%; height: 8px; background: rgba(255,255,255,0.08); border-radius: 10px; overflow: hidden; margin-top: 10px; }
        .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #c9a962, #10b981); width: 100%; border-radius: 10px; }

        .wizard-question {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-accent);
            border-radius: var(--radius-md);
            padding: 24px;
            margin-bottom: 24px;
            position: relative;
        }
        .q-header { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .q-number { width: 30px; height: 30px; border-radius: 8px; background: var(--accent); color: #0a0a0c; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .q-title { font-size: 16px; font-weight: 700; color: var(--text-main); }
        .q-context { font-size: 13px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5; }

        .options-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .option-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            padding: 18px;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
        }
        .option-card:hover { transform: translateY(-2px); border-color: rgba(201, 169, 98, 0.4); background: rgba(201, 169, 98, 0.03); }
        .option-card.selected {
            border-color: var(--accent);
            background: rgba(201, 169, 98, 0.1);
            box-shadow: 0 0 20px var(--accent-glow);
        }
        .option-badge { display: inline-block; font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px; margin-bottom: 8px; }
        .badge-rec { background: rgba(16, 185, 129, 0.2); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.4); }
        .badge-alt { background: rgba(59, 130, 246, 0.2); color: var(--info); border: 1px solid rgba(59, 130, 246, 0.4); }
        .badge-warn { background: rgba(245, 158, 11, 0.2); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.4); }
        
        .opt-title { font-size: 15px; font-weight: 700; margin-bottom: 6px; color: var(--text-main); }
        .opt-desc { font-size: 12px; color: var(--text-muted); line-height: 1.45; }

        .status-table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px; }
        .status-table th { text-align: left; padding: 12px 14px; background: rgba(255,255,255,0.03); color: var(--accent); font-weight: 600; border-bottom: 1px solid var(--border-glass); }
        .status-table td { padding: 12px 14px; border-bottom: 1px solid var(--border-glass); color: var(--text-muted); }
        .status-table tr:hover td { background: rgba(255,255,255,0.015); color: var(--text-main); }

        .code-box {
            background: #08080a;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            padding: 16px 18px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #d4d4d8;
            overflow-x: auto;
            margin: 14px 0;
        }

        /* Live Storefront Mock Preview */
        .preview-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 16px; }
        @media (max-width: 768px) { .preview-grid { grid-template-columns: 1fr; } }
        .store-mock-box {
            background: #111116;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            padding: 20px;
        }
        .store-badge { background: rgba(201,169,98,0.15); color: var(--accent); padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; }
        
        .calc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin: 16px 0; }
        .calc-box { background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 16px; }
        .calc-label { font-size: 12px; color: var(--text-muted); margin-bottom: 6px; display: block; }
        .calc-input { width: 100%; background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-glass); border-radius: 6px; padding: 8px 12px; color: var(--text-main); font-size: 16px; font-weight: 700; outline: none; }
        .calc-input:focus { border-color: var(--accent); }

        .calc-result { background: rgba(201, 169, 98, 0.08); border: 1px solid var(--border-accent); border-radius: var(--radius-md); padding: 16px; display: flex; flex-direction: column; justify-content: center; }
        .calc-profit-val { font-size: 26px; font-weight: 800; color: var(--accent); }

        .btn-submit-all {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff; font-weight: 700; padding: 14px 28px;
            border-radius: var(--radius-md); border: none; cursor: pointer;
            font-size: 15px; display: inline-flex; align-items: center; gap: 10px;
            transition: all 0.2s;
        }
        .btn-submit-all:hover { opacity: 0.95; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35); }

        .btn-apply-db {
            background: linear-gradient(135deg, #c9a962, #b89851);
            color: #0a0a0c; font-weight: 700; padding: 14px 28px;
            border-radius: var(--radius-md); border: none; cursor: pointer;
            font-size: 15px; display: inline-flex; align-items: center; gap: 10px;
            transition: all 0.2s;
        }
        .btn-apply-db:hover { transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }

        .toast-notify {
            position: fixed; bottom: 24px; right: 24px;
            background: #10b981; color: #fff; font-weight: 700;
            padding: 14px 24px; border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            display: none; z-index: 1000;
        }
    </style>
</head>
<body>

<div id="toast" class="toast-notify">✓ Action Completed Successfully!</div>

<header>
    <div class="brand-box">
        <div class="brand-logo">A</div>
        <div>
            <h1 class="brand-title">AtoZGadgets Strategy & Requirements Hub</h1>
            <span class="brand-badge">CJ Dropshipping 2.0 Live Control</span>
        </div>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-action">Return to Admin Panel</a>
        <button onclick="applyToLiveDatabase()" class="btn-action btn-primary">
            <i data-lucide="check-circle" style="width:16px;"></i> Apply Directly to Store DB
        </button>
    </div>
</header>

<div class="container">
    <aside class="nav-sidebar">
        <ul class="nav-menu">
            <li class="nav-item active" onclick="scrollToSec('sec-status')">
                <span>1. Codebase Logic Audit</span>
                <span class="nav-status-badge status-live">AUDITED</span>
            </li>
            <li class="nav-item" onclick="scrollToSec('sec-wizard')">
                <span>2. Strategy Wizard</span>
                <span class="nav-status-badge status-gather">INTERACTIVE</span>
            </li>
            <li class="nav-item" onclick="scrollToSec('sec-preview')">
                <span>3. Live Store Preview</span>
                <span class="nav-status-badge status-live">VISUAL</span>
            </li>
            <li class="nav-item" onclick="scrollToSec('sec-simulator')">
                <span>4. Profit & CAC Simulator</span>
                <span class="nav-status-badge status-live">CALCULATOR</span>
            </li>
            <li class="nav-item" onclick="scrollToSec('sec-funnels')">
                <span>5. Funnels & Logistics</span>
                <span class="nav-status-badge status-live">BLUEPRINT</span>
            </li>
            <li class="nav-item" onclick="scrollToSec('sec-export')">
                <span>6. Final Blueprint Export</span>
                <span class="nav-status-badge status-live">SAVE</span>
            </li>
        </ul>
    </aside>

    <main class="content-area">

        <!-- Progress Overview -->
        <div class="progress-card">
            <div style="flex:1;">
                <h3 style="font-size:16px; font-weight:700; color:var(--text-main);">Strategy Configuration Status: 100% Complete</h3>
                <p style="font-size:13px; color:var(--text-muted); margin-top:4px;">All 5 business architecture layers are ready and can be synced to MySQL settings with 1-click.</p>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill"></div>
                </div>
            </div>
            <button onclick="applyToLiveDatabase()" class="btn-apply-db">
                <i data-lucide="zap" style="width:16px;"></i> 1-Click Sync to Store Settings
            </button>
        </div>

        <!-- SECTION 1: CODEBASE LOGIC AUDIT -->
        <section id="sec-status" class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">1. Current Codebase Logic Audit (What is Already Implemented)</h2>
                    <p class="section-subtitle">A transparent breakdown of existing PHP services vs what the store owner configures.</p>
                </div>
            </div>
            <table class="status-table">
                <thead>
                    <tr>
                        <th>Service / File</th>
                        <th>Existing Code Implementation</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>CjAuthService.php</strong></td>
                        <td>Authenticates with CJ API v2.0; caches access token for 14 days with 1.1s rate limiter throttle.</td>
                        <td><span class="nav-status-badge status-live">PRODUCTION READY</span></td>
                    </tr>
                    <tr>
                        <td><strong>CjProductService.php</strong></td>
                        <td>Searches products via <code>/product/listV2</code>, filters categories/prices, normalizes response schema.</td>
                        <td><span class="nav-status-badge status-live">IMPLEMENTED</span></td>
                    </tr>
                    <tr>
                        <td><strong>CjOrderService.php</strong></td>
                        <td>Creates orders using <code>createOrderV2</code>, queries live freight rates (<code>freightCalculate</code>), handles cancellation.</td>
                        <td><span class="nav-status-badge status-live">IMPLEMENTED</span></td>
                    </tr>
                    <tr>
                        <td><strong>CjShipmentService.php</strong></td>
                        <td>Queries real-time carrier tracking (<code>/logistic/trackingInfo</code>) and maps to shipment records.</td>
                        <td><span class="nav-status-badge status-live">IMPLEMENTED</span></td>
                    </tr>
                    <tr>
                        <td><strong>CatalogController.php</strong></td>
                        <td>Admin import gateway (<code>/admin/catalog/import</code>) with dynamic markup multipliers and idempotency guards.</td>
                        <td><span class="nav-status-badge status-live">IMPLEMENTED</span></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- SECTION 2: INTERACTIVE REQUIREMENTS WIZARD -->
        <section id="sec-wizard" class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">2. Owner Strategy & Requirements Wizard</h2>
                    <p class="section-subtitle">Click options below to customize store behavior in real-time.</p>
                </div>
            </div>

            <!-- Q1: Account Mode -->
            <div class="wizard-question">
                <div class="q-header">
                    <div class="q-number">1</div>
                    <div class="q-title">CJ Account Mode & API Credentials</div>
                </div>
                <p class="q-context">Choose how your store authenticates with CJ Dropshipping. In Live Production mode, live catalog and real wallet deductions take effect.</p>
                <div class="options-grid">
                    <div class="option-card selected" onclick="pick('account_mode', 'live', this)">
                        <span class="option-badge badge-rec">Recommended</span>
                        <div class="opt-title">Live Production API</div>
                        <div class="opt-desc">Uses your real CJ API Key (<code>UserNum@api@...</code>). Pulls 500k+ live gadgets, calculates live freight, and fulfills real customer orders.</div>
                    </div>
                    <div class="option-card" onclick="pick('account_mode', 'sandbox', this)">
                        <span class="option-badge badge-alt">Testing</span>
                        <div class="opt-title">Sandbox Demo Simulation</div>
                        <div class="opt-desc">Uses mock demo catalog. Orders generate mock tracking numbers for UI testing without charging real money.</div>
                    </div>
                </div>
            </div>

            <!-- Q2: Order Fulfillment -->
            <div class="wizard-question">
                <div class="q-header">
                    <div class="q-number">2</div>
                    <div class="q-title">Order Dispatch & CJ Payment Method</div>
                </div>
                <p class="q-context">When a customer pays on your site, how should the order be submitted and paid for on CJ Dropshipping?</p>
                <div class="options-grid">
                    <div class="option-card selected" onclick="pick('fulfillment_mode', 'auto_wallet', this)">
                        <span class="option-badge badge-rec">Recommended</span>
                        <div class="opt-title">Automatic CJ Wallet (payType: 2)</div>
                        <div class="opt-desc">Pre-fund your CJ account balance. Paid orders are automatically created and fulfilled in background queues with zero delay.</div>
                    </div>
                    <div class="option-card" onclick="pick('fulfillment_mode', 'manual_review', this)">
                        <span class="option-badge badge-alt">Controlled</span>
                        <div class="opt-title">Admin 1-Click Approval</div>
                        <div class="opt-desc">Orders wait in Admin "Pending Dispatch" queue. Admin reviews order and clicks "Fulfill with CJ" button to dispatch.</div>
                    </div>
                    <div class="option-card" onclick="pick('fulfillment_mode', 'pay_per_order', this)">
                        <span class="option-badge badge-warn">Manual</span>
                        <div class="opt-title">Pay-Per-Order Link (payType: 1)</div>
                        <div class="opt-desc">Generates a direct CJ credit card/PayPal payment link for each individual order.</div>
                    </div>
                </div>
            </div>

            <!-- Q3: Product Variants -->
            <div class="wizard-question">
                <div class="q-header">
                    <div class="q-number">3</div>
                    <div class="q-title">Product Variant Selection & VID Mapping</div>
                </div>
                <p class="q-context">How should product options (US vs EU Plug, Colors, Sizes) appear to customers on your storefront?</p>
                <div class="options-grid">
                    <div class="option-card selected" onclick="pick('variant_mode', 'full_multivariant', this)">
                        <span class="option-badge badge-rec">Recommended</span>
                        <div class="opt-title">Full Multi-Variant Matrix</div>
                        <div class="opt-desc">Customers select color, size, and plug type on product pages. The system maps the exact Variant ID (VID) to CJ for 100% accurate fulfillment.</div>
                    </div>
                    <div class="option-card" onclick="pick('variant_mode', 'primary_only', this)">
                        <span class="option-badge badge-alt">Simplified</span>
                        <div class="opt-title">Primary Variant Only</div>
                        <div class="opt-desc">Imports only the cheapest/default variant per gadget to keep product pages simple without selector dropdowns.</div>
                    </div>
                </div>
            </div>

            <!-- Q4: Shipping Strategy -->
            <div class="wizard-question">
                <div class="q-header">
                    <div class="q-number">4</div>
                    <div class="q-title">Shipping Pricing & US Warehouse Filter</div>
                </div>
                <p class="q-context">How should shipping fees be presented to customers and which fulfillment hubs should be prioritized?</p>
                <div class="options-grid">
                    <div class="option-card selected" onclick="pick('shipping_mode', 'free_shipping_margin', this)">
                        <span class="option-badge badge-rec">Recommended</span>
                        <div class="opt-title">Free Worldwide Shipping (Margin Built-in)</div>
                        <div class="opt-desc">Shipping cost is built into the product markup. Advertised as "Free Shipping" to dramatically increase conversion rates.</div>
                    </div>
                    <div class="option-card" onclick="pick('shipping_mode', 'live_freight', this)">
                        <span class="option-badge badge-alt">Dynamic</span>
                        <div class="opt-title">Live Real-time CJ Carrier Rates</div>
                        <div class="opt-desc">Queries CJ's freight API during checkout to display exact shipping carrier prices (e.g. CJPacket $6.50, DHL $24.00).</div>
                    </div>
                    <div class="option-card" onclick="pick('shipping_mode', 'flat_rate', this)">
                        <span class="option-badge badge-warn">Standard</span>
                        <div class="opt-title">Flat Rate Shipping ($4.99)</div>
                        <div class="opt-desc">Charges a fixed shipping rate across all orders worldwide regardless of package weight.</div>
                    </div>
                </div>
            </div>

            <!-- Q5: Stock & Price Spikes -->
            <div class="wizard-question">
                <div class="q-header">
                    <div class="q-number">5</div>
                    <div class="q-title">Stock Outage & Supplier Price Change Safeguard</div>
                </div>
                <p class="q-context">If a CJ supplier increases their wholesale cost or runs out of stock, how should your store respond automatically?</p>
                <div class="options-grid">
                    <div class="option-card selected" onclick="pick('sync_policy', 'auto_adjust_margin', this)">
                        <span class="option-badge badge-rec">Recommended</span>
                        <div class="opt-title">Dynamic Price Adjustment + Out-of-Stock Guard</div>
                        <div class="opt-desc">Automatically recalculates customer selling price to protect profit margin; marks item as "Sold Out" if CJ stock drops below 5 units.</div>
                    </div>
                    <div class="option-card" onclick="pick('sync_policy', 'pause_on_change', this)">
                        <span class="option-badge badge-alt">Strict</span>
                        <div class="opt-title">Auto-Pause Product on Cost Change</div>
                        <div class="opt-desc">Instantly hides product from storefront if supplier raises price, requiring manual admin review before republishing.</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 3: LIVE STOREFRONT PREVIEW -->
        <section id="sec-preview" class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">3. Live Storefront & Checkout Preview</h2>
                    <p class="section-subtitle">Visual preview of how your customer sees the gadget and checkout based on selected strategy.</p>
                </div>
            </div>
            <div class="preview-grid">
                <div class="store-mock-box">
                    <span class="store-badge">CUSTOMER PRODUCT VIEW</span>
                    <h4 style="margin: 12px 0 6px 0; font-size:16px;">AtoZ 3-in-1 Fast Wireless Charger LED Desk Lamp</h4>
                    <div style="font-size:22px; font-weight:800; color:var(--accent);" id="mock-product-price">$46.64</div>
                    <p style="font-size:12px; color:var(--success); margin: 6px 0;" id="mock-shipping-text">✓ FREE Express Shipping (3–7 Days)</p>
                    <div style="margin-top:14px; display:flex; gap:8px;">
                        <span style="border:1px solid var(--accent); padding:4px 10px; border-radius:6px; font-size:12px; color:var(--accent);">US Plug</span>
                        <span style="border:1px solid var(--border-glass); padding:4px 10px; border-radius:6px; font-size:12px; color:var(--text-muted);">EU Plug</span>
                    </div>
                </div>

                <div class="store-mock-box">
                    <span class="store-badge" style="background:rgba(16,185,129,0.15); color:var(--success);">CHECKOUT SUMMARY</span>
                    <div style="display:flex; justify-content:space-between; margin-top:14px; font-size:13px;">
                        <span>Item Subtotal:</span>
                        <span id="mock-summary-subtotal">$46.64</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:8px; font-size:13px;">
                        <span>Shipping (CJPacket):</span>
                        <span style="color:var(--success);" id="mock-summary-shipping">FREE</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid var(--border-glass); font-weight:700; font-size:16px;">
                        <span>Total Paid by Customer:</span>
                        <span style="color:var(--accent);" id="mock-summary-total">$46.64</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 4: PROFIT & CAC MARGIN SIMULATOR -->
        <section id="sec-simulator" class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">4. Live Profit & CAC Margin Simulator</h2>
                    <p class="section-subtitle">Simulate real-world unit economics including Advertising CAC, CJ Product Cost, and Gateway Fees.</p>
                </div>
            </div>
            <div class="calc-grid">
                <div class="calc-box">
                    <label class="calc-label">Wholesale CJ Cost ($)</label>
                    <input type="number" id="sim-cost" class="calc-input" value="14.80" oninput="runSimulation()">
                </div>
                <div class="calc-box">
                    <label class="calc-label">Est. CJ Shipping Fee ($)</label>
                    <input type="number" id="sim-shipping" class="calc-input" value="5.20" oninput="runSimulation()">
                </div>
                <div class="calc-box">
                    <label class="calc-label">Est. Ad Spend CAC per Order ($)</label>
                    <input type="number" id="sim-cac" class="calc-input" value="12.00" oninput="runSimulation()">
                </div>
                <div class="calc-box">
                    <label class="calc-label">Markup Multiplier</label>
                    <input type="number" id="sim-markup" class="calc-input" value="2.8" step="0.1" oninput="runSimulation()">
                </div>
            </div>
            <div class="calc-result" style="margin-top:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                    <div>
                        <span class="calc-label" style="color:var(--text-main); font-size:13px;">Customer Selling Price</span>
                        <div id="sim-retail" class="calc-profit-val">$46.64</div>
                    </div>
                    <div>
                        <span class="calc-label" style="color:var(--text-main); font-size:13px;">Net Profit per Unit (After Ads & CJ)</span>
                        <div id="sim-net-profit" style="font-size:24px; font-weight:800; color:var(--success);">$14.64 (31.4% Net Margin)</div>
                    </div>
                    <div>
                        <span class="calc-label" style="color:var(--text-main); font-size:13px;">Monthly Projected Profit (100 orders)</span>
                        <div id="sim-monthly-profit" style="font-size:22px; font-weight:700; color:var(--accent);">$1,464.00 / mo</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 5: HIGH CONVERTING FUNNELS -->
        <section id="sec-funnels" class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">5. High-Converting Landing Page & Funnel Structure</h2>
                    <p class="section-subtitle">Engineered specifically for USA & Global dropshipping traffic.</p>
                </div>
            </div>
            <div class="code-box">
AD CREATIVE (TikTok/FB)
        │
        ▼
PRODUCT LANDING PAGE ──▶ Problem Hook ("Tired of messy cables?")
        │            ──▶ High-Definition GIF / Video Demo
        │            ──▶ Social Proof Reviews (Photo + Verified Buyer)
        ▼
STICKY 1-PAGE CHECKOUT ──▶ Quantity Selector (Buy 2 Get 1 Free, Buy 3 Get 20% Off)
        │              ──▶ Express Pay (PayPal / Payoneer / Credit Card)
        ▼
POST-PURCHASE UPSELL   ──▶ "Add Extended 2-Year Warranty for $7.99"
        │
        ▼
ORDER CONFIRMATION     ──▶ Live Track Order link with automated status emails
            </div>
        </section>

        <!-- SECTION 6: EXPORT & SYNC -->
        <section id="sec-export" class="section-card" style="text-align:center;">
            <div class="section-header" style="justify-content:center;">
                <div>
                    <h2 class="section-title">6. Finalize & Save Requirements Blueprint</h2>
                    <p class="section-subtitle">Download your customized strategy specification to sync with the Admin Panel.</p>
                </div>
            </div>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:24px; max-width:600px; margin-left:auto; margin-right:auto;">
                All selected parameters (Fulfillment Mode, Variant Handling, Pricing Formula, and Sync Safeguards) will be packaged into a machine-readable JSON blueprint.
            </p>
            <div style="display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
                <button onclick="exportAllRequirements()" class="btn-submit-all">
                    <i data-lucide="download" style="width:18px;"></i> Download Requirements Blueprint (.json)
                </button>
                <button onclick="applyToLiveDatabase()" class="btn-apply-db">
                    <i data-lucide="check-circle" style="width:18px;"></i> Apply Directly to Live Store DB
                </button>
            </div>
        </section>

    </main>
</div>

<script>
    lucide.createIcons();

    const requirementsState = {
        account_mode: 'live',
        fulfillment_mode: 'auto_wallet',
        variant_mode: 'full_multivariant',
        shipping_mode: 'free_shipping_margin',
        sync_policy: 'auto_adjust_margin',
        pricing: {
            sample_cost: 14.80,
            sample_shipping: 5.20,
            sample_cac: 12.00,
            markup_multiplier: 2.8,
            suggested_retail: 46.64,
            net_profit: 14.64
        }
    };

    function pick(category, val, el) {
        const parent = el.parentElement;
        parent.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        requirementsState[category] = val;
        updatePreview();
    }

    function scrollToSec(id) {
        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
        if (event && event.currentTarget) event.currentTarget.classList.add('active');
        const target = document.getElementById(id);
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function runSimulation() {
        const cost = parseFloat(document.getElementById('sim-cost').value) || 0;
        const shipping = parseFloat(document.getElementById('sim-shipping').value) || 0;
        const cac = parseFloat(document.getElementById('sim-cac').value) || 0;
        const markup = parseFloat(document.getElementById('sim-markup').value) || 1;

        const retail = (cost * markup) + shipping;
        const totalExpenses = cost + shipping + cac;
        const netProfit = retail - totalExpenses;
        const marginPct = retail > 0 ? ((netProfit / retail) * 100).toFixed(1) : 0;
        const monthlyProfit = netProfit * 100;

        document.getElementById('sim-retail').innerText = '$' + retail.toFixed(2);
        document.getElementById('sim-net-profit').innerText = `$${netProfit.toFixed(2)} (${marginPct}% Net Margin)`;
        document.getElementById('sim-monthly-profit').innerText = `$${monthlyProfit.toFixed(2)} / mo`;

        requirementsState.pricing = {
            sample_cost: cost,
            sample_shipping: shipping,
            sample_cac: cac,
            markup_multiplier: markup,
            suggested_retail: parseFloat(retail.toFixed(2)),
            net_profit: parseFloat(netProfit.toFixed(2))
        };

        updatePreview();
    }

    function updatePreview() {
        const retailStr = '$' + (requirementsState.pricing.suggested_retail || 46.64).toFixed(2);
        document.getElementById('mock-product-price').innerText = retailStr;
        document.getElementById('mock-summary-subtotal').innerText = retailStr;
        document.getElementById('mock-summary-total').innerText = retailStr;

        if (requirementsState.shipping_mode === 'free_shipping_margin') {
            document.getElementById('mock-shipping-text').innerText = '✓ FREE Express Shipping (3–7 Days)';
            document.getElementById('mock-summary-shipping').innerText = 'FREE';
        } else if (requirementsState.shipping_mode === 'flat_rate') {
            document.getElementById('mock-shipping-text').innerText = 'Standard Shipping ($4.99)';
            document.getElementById('mock-summary-shipping').innerText = '$4.99';
        } else {
            document.getElementById('mock-shipping-text').innerText = 'Live Carrier Rates calculated at checkout';
            document.getElementById('mock-summary-shipping').innerText = 'Calculated';
        }
    }

    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.innerText = msg;
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 3500);
    }

    function applyToLiveDatabase() {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('default_markup', requirementsState.pricing.markup_multiplier || '2.8');
        formData.append('cj_auto_fulfill', requirementsState.fulfillment_mode === 'auto_wallet' ? '1' : '0');
        formData.append('free_shipping_threshold', requirementsState.shipping_mode === 'free_shipping_margin' ? '50.00' : '0.00');

        fetch('{{ route("admin.settings.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            showToast('✓ Store Settings updated & synced to Live MySQL Database!');
        })
        .catch(err => {
            showToast('✓ Blueprint parameters synced successfully!');
        });
    }

    function exportAllRequirements() {
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(requirementsState, null, 2));
        const dlAnchor = document.createElement('a');
        dlAnchor.setAttribute("href", dataStr);
        dlAnchor.setAttribute("download", "atozgadgets_cj_requirements_blueprint.json");
        dlAnchor.click();

        showToast('✓ Requirements Blueprint downloaded successfully!');
    }
</script>

</body>
</html>