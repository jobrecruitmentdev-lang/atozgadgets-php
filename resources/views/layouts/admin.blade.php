<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - AtoZGadgets</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('brand/atoz-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        :root {
            --bg-color: #f9fafb;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --sidebar-bg: #000000;
            --sidebar-text: #ffffff;
            --sidebar-hover: rgba(255,255,255,0.1);
            --border-color: #e5e7eb;
            --accent: #2563eb;
        }
        
        /* Dark Mode Support based on OS preference */
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #030712;
                --text-primary: #f9fafb;
                --text-secondary: #9ca3af;
                --sidebar-bg: #0f172a;
                --sidebar-text: #f8fafc;
                --sidebar-hover: rgba(255,255,255,0.08);
                --border-color: #1f2937;
                --accent: #3b82f6;
            }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-primary); display: flex; min-height: 100vh; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }

        /* Sidebar */
        .sidebar { width: 256px; background-color: var(--sidebar-bg); color: var(--sidebar-text); flex-shrink: 0; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; transition: transform 0.3s ease; z-index: 100; }
        .sidebar-header { padding: 24px; border-bottom: 1px solid rgba(128,128,128,0.2); display: flex; justify-content: space-between; align-items: center; }
        .sidebar-header h2 { font-size: 20px; font-weight: 700; letter-spacing: -0.5px; }
        .sidebar-close-btn { display: none; background: transparent; border: none; color: var(--sidebar-text); cursor: pointer; }
        
        .sidebar-nav { flex-grow: 1; overflow-y: auto; padding: 16px; }
        .nav-group { margin-bottom: 32px; }
        .nav-group h3 { font-size: 12px; font-weight: 600; color: rgba(128,128,128,0.8); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; padding: 0 12px; }
        
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: 6px; font-size: 14px; font-weight: 500; transition: all 0.2s; margin-bottom: 4px; }
        .nav-item:hover { background-color: var(--sidebar-hover); }
        .nav-item.active { background-color: var(--sidebar-hover); font-weight: 600; }
        
        /* Mobile Overlay */
        .sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90; display: none; opacity: 0; transition: opacity 0.3s ease; }
        .sidebar-overlay.active { display: block; opacity: 1; }

        /* Main Content */
        .main-wrapper { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; width: 100%; }
        .top-header { height: 64px; border-bottom: 1px solid var(--border-color); background: var(--bg-color); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 10; gap: 16px; }
        .mobile-toggle { display: none; background: transparent; border: none; color: var(--text-primary); cursor: pointer; }
        
        .header-search { display: flex; align-items: center; gap: 8px; background: rgba(128,128,128,0.1); padding: 8px 16px; border-radius: 8px; flex: 1; max-width: 300px; }
        .header-search input { background: transparent; border: none; outline: none; color: var(--text-primary); font-size: 14px; width: 100%; min-width: 0; }
        
        .header-actions { display: flex; align-items: center; gap: 16px; }
        .icon-btn { background: transparent; border: none; color: var(--text-secondary); cursor: pointer; transition: color 0.2s; }
        .icon-btn:hover { color: var(--text-primary); }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }

        .content { padding: 32px; flex-grow: 1; }
        
        /* Utility Classes */
        .card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .text-muted { color: var(--text-secondary); }
        
        /* Pagination CSS Fixes for Tailwind Default View */
        nav[role="navigation"] { display: flex; align-items: center; justify-content: space-between; font-size: 14px; margin-top: 10px; }
        nav[role="navigation"] svg { width: 20px; height: 20px; }
        nav[role="navigation"] p { display: none; } /* Hide the text "Showing 1 to x" if it messes up flex */
        nav[role="navigation"] .flex { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
        nav[role="navigation"] a, nav[role="navigation"] span { padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-color); color: var(--text-primary); transition: background 0.2s; text-decoration: none; }
        nav[role="navigation"] a:hover { background: rgba(128,128,128,0.1); }
        nav[role="navigation"] span[aria-current="page"] { background: var(--accent); color: white; border-color: var(--accent); }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar { position: fixed; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .sidebar-close-btn { display: block; }
            .mobile-toggle { display: block; }
            .top-header { padding: 0 16px; }
            .content { padding: 16px; }
            .header-actions .avatar { display: none; }
            .header-actions > a { display: none; } /* Hide 'View Storefront' text on mobile */
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" style="text-decoration:none; color:inherit;"><h2>AtoZ Admin</h2></a>
            <button class="sidebar-close-btn" id="closeSidebarBtn" aria-label="Close Sidebar"><i data-lucide="x"></i></button>
        </div>
        <div class="sidebar-nav">
            <div class="nav-group">
                <h3>Catalog</h3>
                <a href="{{ route('admin.catalog.products') }}" class="nav-item {{ request()->routeIs('admin.catalog.products') ? 'active' : '' }}"><i data-lucide="package" style="width:16px;"></i> Products</a>
                <a href="{{ route('admin.catalog.categories') }}" class="nav-item {{ request()->routeIs('admin.catalog.categories') ? 'active' : '' }}"><i data-lucide="layout-grid" style="width:16px;"></i> Categories</a>
                <a href="{{ route('admin.catalog.brands') }}" class="nav-item {{ request()->routeIs('admin.catalog.brands') ? 'active' : '' }}"><i data-lucide="tags" style="width:16px;"></i> Brands</a>
                <a href="{{ route('admin.catalog.import') }}" class="nav-item {{ request()->routeIs('admin.catalog.import') ? 'active' : '' }}"><i data-lucide="download" style="width:16px;"></i> CJ Import</a>
            </div>
            
            <div class="nav-group">
                <h3>Commerce</h3>
                <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}"><i data-lucide="shopping-cart" style="width:16px;"></i> Orders</a>
                <a href="{{ route('admin.customers') }}" class="nav-item {{ request()->routeIs('admin.customers') ? 'active' : '' }}"><i data-lucide="users" style="width:16px;"></i> Customers</a>
            </div>

            <div class="nav-group">
                <h3>System</h3>
                <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i data-lucide="bar-chart-3" style="width:16px;"></i> Reports</a>
                <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><i data-lucide="settings" style="width:16px;"></i> Settings</a>
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
            <!-- Search is now a functional form if needed -->
            <form action="{{ route('admin.catalog.products') }}" method="GET" class="header-search">
                <button type="submit" aria-label="Search" style="background:transparent; border:none; cursor:pointer; color:var(--text-secondary); display:flex; align-items:center;">
                    <i data-lucide="search" style="width:16px;" aria-hidden="true"></i>
                </button>
                <label for="adminSearch" class="sr-only" style="position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0;">Search</label>
                <input type="text" id="adminSearch" name="search" placeholder="Search products...">
            </form>
            <div class="header-actions">
                <a href="{{ route('store.home') }}" target="_blank" style="font-size:14px; font-weight:500; color:var(--accent); margin-right:12px; text-decoration:none;">View Storefront</a>
                <button class="icon-btn"><i data-lucide="bell" style="width:20px;"></i></button>
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

            // Mobile Sidebar Toggle
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
    </script>
</body>
</html>
