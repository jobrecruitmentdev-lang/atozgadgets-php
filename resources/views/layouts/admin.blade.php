<!DOCTYPE html>
<html lang="en" data-app="admin">
<head>
    @include('partials.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Control Tower') - AtoZGadgets</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('brand/atoz-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Global Tokens -->
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        :root {
            --bg-color: var(--bg-base);
            --bg-card: var(--bg-surface);
            --sidebar-bg: var(--bg-surface);
            --sidebar-text: var(--text-primary);
            --sidebar-hover: var(--hover-subtle);
            --accent: var(--brand-primary);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-base); color: var(--text-primary); display: flex; min-height: 100vh; overflow-x: hidden; transition: background-color var(--duration-fast), color var(--duration-fast); }
        a { text-decoration: none; color: inherit; }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text); flex-shrink: 0; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; transition: transform 0.3s ease, background-color var(--duration-fast); z-index: 100; border-right: 1px solid var(--border-color); }
        .sidebar-header { padding: 20px 24px; border-bottom: 1px solid rgba(128,128,128,0.2); display: flex; justify-content: space-between; align-items: center; }
        .sidebar-header h2 { font-size: 18px; font-weight: 800; letter-spacing: -0.5px; display: flex; align-items: center; gap: 8px; }
        .sidebar-close-btn { display: none; background: transparent; border: none; color: var(--sidebar-text); cursor: pointer; }
        
        .sidebar-nav { flex-grow: 1; overflow-y: auto; padding: 16px 12px; }
        .nav-group { margin-bottom: 24px; }
        .nav-group h3 { font-size: 11px; font-weight: 700; color: rgba(128,128,128,0.8); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; padding: 0 12px; }
        
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 8px; font-size: 13.5px; font-weight: 500; transition: all 0.2s; margin-bottom: 2px; color: rgba(255,255,255,0.8); }
        .nav-item:hover { background-color: var(--sidebar-hover); color: #fff; }
        .nav-item.active { background-color: var(--accent); color: #fff; font-weight: 600; }
        
        /* Mobile Overlay */
        .sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90; display: none; opacity: 0; transition: opacity 0.3s ease; }
        .sidebar-overlay.active { display: block; opacity: 1; }

        /* Main Content */
        .main-wrapper { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; width: 100%; }
        .top-header { height: 64px; border-bottom: 1px solid var(--border-color); background: var(--bg-card); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 10; gap: 16px; }
        .mobile-toggle { display: none; background: transparent; border: none; color: var(--text-primary); cursor: pointer; }
        
        .header-search { display: flex; align-items: center; gap: 8px; background: rgba(128,128,128,0.1); padding: 8px 16px; border-radius: 8px; flex: 1; max-width: 320px; }
        .header-search input { background: transparent; border: none; outline: none; color: var(--text-primary); font-size: 14px; width: 100%; min-width: 0; }
        
        .header-actions { display: flex; align-items: center; gap: 16px; }
        .icon-btn { background: transparent; border: none; color: var(--text-secondary); cursor: pointer; transition: color 0.2s; }
        .icon-btn:hover { color: var(--text-primary); }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }

        .content { padding: 32px; flex-grow: 1; min-width: 0; }
        
        .card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); min-width: 0; }
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: thin; }
        .table-responsive table { min-width: 720px; }
        
        @media (max-width: 768px) {
            .sidebar { position: fixed; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .sidebar-close-btn { display: block; }
            .mobile-toggle { display: block; }
            .top-header { padding: 0 16px; }
            .content { padding: 16px; }
            .header-actions .avatar { display: none; }
            .header-actions > a { display: none; }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" style="text-decoration:none; color:inherit;">
                <h2><i data-lucide="shield-check" style="width:20px; color:var(--accent);"></i> AtoZ Control</h2>
            </a>
            <button class="sidebar-close-btn" id="closeSidebarBtn" aria-label="Close Sidebar"><i data-lucide="x"></i></button>
        </div>
        <div class="sidebar-nav">
            <div class="nav-group">
                <h3>Dashboard</h3>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i data-lucide="layout-dashboard" style="width:16px;"></i> Overview</a>
            </div>

            <div class="nav-group">
                <h3>Catalog</h3>
                <a href="{{ route('admin.catalog.products') }}" class="nav-item {{ request()->routeIs('admin.catalog.products') ? 'active' : '' }}"><i data-lucide="package" style="width:16px;"></i> Products</a>
                <a href="{{ route('admin.catalog.categories') }}" class="nav-item {{ request()->routeIs('admin.catalog.categories') ? 'active' : '' }}"><i data-lucide="layout-grid" style="width:16px;"></i> Categories</a>
                <a href="{{ route('admin.catalog.brands') }}" class="nav-item {{ request()->routeIs('admin.catalog.brands') ? 'active' : '' }}"><i data-lucide="tags" style="width:16px;"></i> Brands</a>
                <a href="{{ route('admin.catalog.import') }}" class="nav-item {{ request()->routeIs('admin.catalog.import') ? 'active' : '' }}"><i data-lucide="download-cloud" style="width:16px;"></i> Product Import</a>
                <a href="{{ route('admin.strategy_hub') }}" class="nav-item {{ request()->routeIs('admin.strategy_hub') ? 'active' : '' }}" style="color: #c9a962; font-weight: 600;"><i data-lucide="compass" style="width:16px;"></i> Strategy Hub <span style="font-size:9px; background:rgba(201,169,98,0.2); color:#c9a962; padding:2px 6px; border-radius:4px; margin-left:auto;">2.0</span></a>
            </div>
            
            <div class="nav-group">
                <h3>Commerce</h3>
                <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"><i data-lucide="shopping-cart" style="width:16px;"></i> Orders</a>
                <a href="{{ route('admin.customers') }}" class="nav-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}"><i data-lucide="users" style="width:16px;"></i> Customers</a>
                <a href="{{ route('admin.commerce.payments') }}" class="nav-item {{ request()->routeIs('admin.commerce.payments*') ? 'active' : '' }}"><i data-lucide="credit-card" style="width:16px;"></i> Payments</a>
                <a href="{{ route('admin.commerce.reviews') }}" class="nav-item {{ request()->routeIs('admin.commerce.reviews*') ? 'active' : '' }}"><i data-lucide="star" style="width:16px;"></i> Reviews</a>
            </div>

            <div class="nav-group">
                <h3>Fulfillment</h3>
                <a href="{{ route('admin.fulfillment.overview') }}" class="nav-item {{ request()->routeIs('admin.fulfillment.overview') ? 'active' : '' }}"><i data-lucide="truck" style="width:16px;"></i> Overview</a>
                <a href="{{ route('admin.fulfillment.queue') }}" class="nav-item {{ request()->routeIs('admin.fulfillment.queue') ? 'active' : '' }}"><i data-lucide="clock" style="width:16px;"></i> Pending Queue</a>
                <a href="{{ route('admin.fulfillment.shipments') }}" class="nav-item {{ request()->routeIs('admin.fulfillment.shipments') ? 'active' : '' }}"><i data-lucide="navigation" style="width:16px;"></i> Shipments</a>
                <a href="{{ route('admin.fulfillment.exceptions') }}" class="nav-item {{ request()->routeIs('admin.fulfillment.exceptions') ? 'active' : '' }}"><i data-lucide="alert-triangle" style="width:16px; color:#ef4444;"></i> Exceptions</a>
            </div>

            <div class="nav-group">
                <h3>Analytics</h3>
                <a href="{{ route('admin.analytics.sales') }}" class="nav-item {{ request()->routeIs('admin.analytics.sales') ? 'active' : '' }}"><i data-lucide="trending-up" style="width:16px;"></i> Sales</a>
                <a href="{{ route('admin.analytics.products') }}" class="nav-item {{ request()->routeIs('admin.analytics.products') ? 'active' : '' }}"><i data-lucide="bar-chart-2" style="width:16px;"></i> Products</a>
                <a href="{{ route('admin.analytics.profitability') }}" class="nav-item {{ request()->routeIs('admin.analytics.profitability') ? 'active' : '' }}"><i data-lucide="dollar-sign" style="width:16px;"></i> Profitability</a>
            </div>

            <div class="nav-group">
                <h3>System</h3>
                <a href="{{ route('admin.system.health') }}" class="nav-item {{ request()->routeIs('admin.system.health') ? 'active' : '' }}"><i data-lucide="activity" style="width:16px;"></i> Health</a>
                <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><i data-lucide="settings" style="width:16px;"></i> Settings</a>
                <a href="{{ route('admin.system.audit_logs') }}" class="nav-item {{ request()->routeIs('admin.system.audit_logs') ? 'active' : '' }}"><i data-lucide="shield" style="width:16px;"></i> Audit Logs</a>
                <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i data-lucide="file-text" style="width:16px;"></i> Reports</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #ef4444;">
                    <i data-lucide="log-out" style="width:16px;"></i> Logout
                </a>
            </div>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="top-header">
            <button class="mobile-toggle" id="openSidebarBtn" aria-label="Open Sidebar"><i data-lucide="menu"></i></button>
            <form action="{{ route('admin.catalog.products') }}" method="GET" class="header-search">
                <button type="submit" aria-label="Search" style="background:transparent; border:none; cursor:pointer; color:var(--text-secondary); display:flex; align-items:center;">
                    <i data-lucide="search" style="width:16px;" aria-hidden="true"></i>
                </button>
                <label for="adminSearch" class="sr-only" style="position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0;">Search</label>
                <input type="text" id="adminSearch" name="search" placeholder="Search products...">
            </form>
            <div class="header-actions">
                <a href="{{ route('store.home') }}" target="_blank" style="font-size:14px; font-weight:500; color:var(--accent); margin-right:12px; text-decoration:none;">View Storefront</a>
                <button class="icon-btn" aria-label="Notifications"><i data-lucide="bell" style="width:20px;"></i></button>
                <div class="avatar">A</div>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof lucide !== 'undefined') {
                lucide.createIcons();
            } else {
                window.addEventListener('load', () => lucide.createIcons());
            }

            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar(show) {
                if (show) {
                    sidebar.classList.add('active');
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }

            if(openBtn) openBtn.addEventListener('click', () => toggleSidebar(true));
            if(closeBtn) closeBtn.addEventListener('click', () => toggleSidebar(false));
            if(overlay) overlay.addEventListener('click', () => toggleSidebar(false));
        });

        window.showAdminToast = function(message, type = 'success') {
            let container = document.getElementById('adminToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'adminToastContainer';
                container.style.cssText = 'position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none;';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            const bg = type === 'error' ? 'rgba(239,68,68,0.95)' : 'rgba(16,185,129,0.95)';
            toast.style.cssText = `background:${bg}; color:#fff; padding:12px 20px; border-radius:10px; font-size:13px; font-weight:600; box-shadow:0 10px 25px -5px rgba(0,0,0,0.3); transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1); transform:translateY(10px); opacity:0; pointer-events:auto; display:flex; align-items:center; gap:8px;`;
            toast.innerHTML = `<span style="font-size:16px;">${type === 'error' ? '⚠️' : '✅'}</span> <span>${message}</span>`;
            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            });

            setTimeout(() => {
                toast.style.transform = 'translateY(10px)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        };
    </script>
</body>
</html>
