<!DOCTYPE html>
<html lang="en" data-app="store">
<head>
    @include('partials.theme-init')
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LS0E52WE2D"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-LS0E52WE2D');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AtoZGadgets - Premium Electronics')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('brand/atoz-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Global Tokens -->
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    
    <!-- Defer render-blocking scripts -->
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        :root {
            --bg-color: var(--bg-base);
            --accent: var(--brand-primary);
            --accent-hover: var(--brand-primary-hover);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background-color var(--duration-fast), color var(--duration-fast);
        }
        /* Performance Fix: Use fixed pseudo-element instead of background-attachment: fixed on body */
        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
            background-image: radial-gradient(circle at 15% 50%, rgba(201, 169, 98, 0.05), transparent 25%),
                              radial-gradient(circle at 85% 30%, rgba(201, 169, 98, 0.08), transparent 25%);
            pointer-events: none;
        }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }
        
        /* Native Scroll Reveal Styles */
        [data-aos] {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s var(--ease-premium), transform 0.6s var(--ease-premium);
            will-change: opacity, transform;
        }
        [data-aos].aos-animate {
            opacity: 1;
            transform: translateY(0);
        }
        @media (prefers-reduced-motion: reduce) {
            [data-aos] { opacity: 1 !important; transform: none !important; transition: none !important; }
        }
        
        /* Utility */
        .container { max-width: 1200px; margin: 0 auto; padding-left: 20px; padding-right: 20px; }
        
        /* Header Ported from Next.js */
        header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: transform 0.4s var(--ease-premium), background 0.3s var(--ease-premium), box-shadow 0.3s var(--ease-premium); background: var(--bg-base); border-bottom: 1px solid var(--border-color); }
        header.scrolled { background: var(--glass-bg); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); box-shadow: var(--glass-shadow); }
        header.header-hidden { transform: translateY(-100%) !important; }
        
        .top-banner { background: var(--selection-bg); text-align: center; padding: 6px 0; font-size: 12px; font-weight: 500; color: var(--text-secondary); display: none; }
        @media (min-width: 768px) { .top-banner { display: block; } }
        
        .nav-main { display: flex; align-items: center; justify-content: space-between; height: 100px; gap: 20px; }
        .logo-container { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 20px; letter-spacing: -0.5px; }
        .logo-container img { width: 40px; height: 40px; border-radius: 50%; transform: translateZ(0); }
        
        .search-bar { flex: 1; max-width: 600px; position: relative; display: none; }
        @media (min-width: 768px) { .search-bar { display: block; } }
        .search-bar input { width: 100%; background: var(--input-bg); border: 1px solid var(--border-color); padding: 12px 20px 12px 45px; border-radius: 12px; color: var(--text-primary); outline: none; transition: all 0.3s; }
        .search-bar input:focus { border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--focus-ring); background: var(--bg-surface); }
        .search-bar button.search-btn { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: var(--text-secondary); display: flex; align-items: center; justify-content: center; z-index: 10; padding: 0; }
        .search-bar button.search-btn:hover { color: var(--accent); }
        .search-bar button.search-btn svg, .search-bar button.search-btn i { width: 18px; height: 18px; }

        .nav-icons { display: flex; align-items: center; gap: 10px; }
        .icon-btn { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; color: var(--text-primary); transition: all 0.3s; position: relative; border: none; background: transparent; cursor: pointer; }
        .icon-btn:hover { background: var(--hover-subtle); color: var(--accent); }
        .badge { position: absolute; top: 6px; right: 6px; background: var(--accent); color: var(--text-inverse); font-size: 10px; font-weight: bold; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .badge-dot { position: absolute; top: 8px; right: 8px; width: 8px; height: 8px; background: var(--accent); border-radius: 50%; }
        
        .mobile-menu-btn { display: inline-flex; }
        @media (min-width: 768px) { .mobile-menu-btn { display: none; } }

        /* Categories Row */
        .categories-row { border-top: 1px solid var(--border-color); display: none; background: var(--bg-base); backdrop-filter: blur(12px); }
        @media (min-width: 768px) { .categories-row { display: block; } }
        .categories-nav { display: flex; justify-content: flex-start; align-items: center; gap: 8px; padding: 6px 0; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; }
        .categories-nav::-webkit-scrollbar { display: none; }
        .cat-link { font-size: 13.5px; font-weight: 500; color: var(--text-secondary); padding: 8px 14px; min-height: 38px; border-radius: 8px; transition: all 0.2s; display: flex; align-items: center; gap: 6px; white-space: nowrap; text-decoration: none; border: 1px solid transparent; }
        .cat-link:hover { background: var(--hover-subtle); color: var(--text-primary); }
        .cat-link.active { color: var(--accent); background: var(--selection-bg); border-color: var(--focus-ring); font-weight: 600; }
        .mega-dropdown { position: relative; }
        .mega-menu { position: absolute; top: 100%; left: 0; min-width: 220px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 16px; padding: 15px; opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s var(--ease-premium); box-shadow: var(--glass-shadow); z-index: 100; }
        
        /* Touch Device Mega Menu Support */
        .mega-dropdown:hover .mega-menu,
        .mega-dropdown:focus-within .mega-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        
        .mega-menu a { display: block; padding: 10px 12px; color: var(--text-secondary); font-size: 14px; border-radius: 8px; transition: all 0.2s; }
        .mega-menu a:hover, .mega-menu a:focus { color: var(--text-primary); background: var(--hover-subtle); }

        /* Mobile Menu Overlay */
        .mobile-menu-overlay { position: fixed; inset: 0; background: var(--bg-base); z-index: 2000; transform: translateX(-100%); transition: transform 0.4s var(--ease-premium); display: flex; flex-direction: column; padding: 20px; overflow-y: auto; }
        .mobile-menu-overlay.active { transform: translateX(0); }
        .mobile-menu-close { align-self: flex-end; background: transparent; border: none; color: var(--text-primary); cursor: pointer; padding: 10px; }
        .mobile-nav-list { display: flex; flex-direction: column; gap: 15px; margin-top: 30px; }
        .mobile-nav-link { font-size: 18px; font-weight: 600; color: var(--text-primary); padding: 10px 0; border-bottom: 1px solid var(--border-color); display: block; }


        /* Main Content */
        main { padding-top: 200px; min-height: 70vh; }
        @media (max-width: 768px) { main { padding-top: 140px; } }

        /* Buttons & Cards */
        .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.4s var(--ease-premium); border: none; letter-spacing: 0.5px; }
        .btn-primary { background: var(--accent); color: var(--text-inverse); font-weight: 600; }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-2px); box-shadow: 0 10px 20px -10px var(--accent); }
        .btn-primary:active { transform: scale(0.97); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; }
        .card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 20px; padding: 20px; transition: all 0.5s var(--ease-premium); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); display: flex; flex-direction: column; position: relative; overflow: hidden; }
        .card::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, var(--selection-bg) 0%, transparent 60%); opacity: 0; transition: opacity 0.5s; z-index: -1; pointer-events: none; }
        .card:hover { transform: translateY(-8px) scale(1.01); box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 0 20px var(--focus-ring); border-color: var(--accent); }
        .card:hover::before { opacity: 1; }
        .card img { width: 100%; height: 240px; object-fit: cover; border-radius: 12px; margin-bottom: 24px; background: var(--hover-subtle); transition: transform 0.7s var(--ease-premium); }
        .card:hover img { transform: scale(1.05); }
        .card-title { font-size: 17px; font-weight: 600; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; letter-spacing: -0.2px; line-height: 1.4; color: var(--text-primary); }
        .card-price { font-size: 24px; font-weight: 700; color: var(--accent); margin-bottom: 20px; }

        /* Pagination CSS Fixes for Tailwind Default View */
        nav[role="navigation"] { display: flex; align-items: center; justify-content: space-between; font-size: 14px; margin-top: 40px; }
        nav[role="navigation"] svg { width: 20px; height: 20px; }
        nav[role="navigation"] p { display: none; }
        nav[role="navigation"] .flex { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        nav[role="navigation"] a, nav[role="navigation"] span { padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--hover-subtle); color: var(--text-primary); transition: all 0.3s; text-decoration: none; }
        nav[role="navigation"] a:hover { background: var(--selection-bg); border-color: var(--accent); }
        nav[role="navigation"] span[aria-current="page"] { background: var(--accent); color: var(--text-inverse); font-weight: 700; border-color: var(--accent); }

        /* Footer */
        footer { background: var(--bg-surface); color: var(--text-primary); padding: 60px 0 30px; margin-top: 80px; border-top: 1px solid var(--border-color); position: relative; overflow: hidden; }
        footer::before { content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 50%; height: 1px; background: linear-gradient(90deg, transparent, var(--accent), transparent); opacity: 0.5; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 50px; }
        @media (max-width: 768px) { .footer-grid { grid-template-columns: 1fr; } }
        .footer-brand p { color: var(--text-secondary); font-size: 14px; line-height: 1.6; margin: 15px 0; max-width: 300px; }
        .footer-brand .contact { display: flex; flex-direction: column; gap: 10px; font-size: 14px; color: var(--text-secondary); }
        .footer-brand .contact a:hover { color: var(--accent); }
        .footer-col h4 { font-weight: 600; margin-bottom: 20px; font-size: 16px; color: var(--text-primary); }
        .footer-col ul li { margin-bottom: 12px; }
        .footer-col ul a { color: var(--text-secondary); font-size: 14px; transition: color 0.3s; }
        .footer-col ul a:hover { color: var(--accent); }
        
        .footer-badges { border-top: 1px solid var(--border-color); padding: 30px 0; text-align: center; }
        .badges-list { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-bottom: 15px; }
        .badge-item { background: var(--hover-subtle); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; color: var(--text-secondary); border: 1px solid var(--border-color); }
        
        .footer-bottom { border-top: 1px solid var(--border-color); padding-top: 25px; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: var(--text-secondary); }
        @media (max-width: 768px) { .footer-bottom { flex-direction: column; gap: 15px; text-align: center; } }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="top-banner">
            Free worldwide shipping on orders over $30 · 7–15 day delivery · Secure checkout
        </div>
        
        <div class="container">
            <div class="nav-main">
                <button class="icon-btn mobile-menu-btn" aria-label="Toggle Menu"><i data-lucide="menu"></i></button>
                
                <a href="{{ route('store.home') }}" class="logo-container">
                    <img src="{{ asset('brand/atoz-logo.png') }}" alt="AtoZ Gadgetz Logo" style="width: auto; height: 80px; border-radius: 0; filter: none; mix-blend-mode: screen;">
                </a>

                <form action="{{ route('store.shop') }}" method="GET" class="search-bar">
                    <button type="submit" aria-label="Submit Search" class="search-btn">
                        <i data-lucide="search" aria-hidden="true"></i>
                    </button>
                    <label for="searchInput" class="sr-only" style="position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0;">Search</label>
                    <input type="text" id="searchInput" name="q" value="{{ request('q') }}" placeholder="Search for gadgets, accessories...">
                </form>

                <div class="nav-icons">
                    @auth
                        @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2)
                            <a href="{{ route('admin.dashboard') }}" class="icon-btn" aria-label="Admin Dashboard" title="Admin Dashboard" style="{{ request()->routeIs('admin.dashboard') ? 'color: var(--accent); background: rgba(255, 255, 255, 0.1);' : '' }}">
                                <i data-lucide="user-check"></i>
                            </a>
                        @else
                            <a href="{{ route('account.dashboard') }}" class="icon-btn" aria-label="Account" title="My Account" style="{{ request()->routeIs('account.dashboard') ? 'color: var(--accent); background: rgba(255, 255, 255, 0.1);' : '' }}">
                                <i data-lucide="user-check"></i>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="icon-btn" aria-label="Login" title="Login" style="{{ request()->routeIs('login') ? 'color: var(--accent); background: rgba(255, 255, 255, 0.1);' : '' }}">
                            <i data-lucide="user"></i>
                        </a>
                    @endauth

                    <a href="#" class="icon-btn" aria-label="Wishlist">
                        <i data-lucide="heart"></i>
                    </a>

                    <a href="{{ route('store.cart') }}" class="icon-btn" aria-label="Cart" style="{{ request()->routeIs('store.cart') ? 'color: var(--accent); background: rgba(255, 255, 255, 0.1);' : '' }}">
                        <i data-lucide="shopping-cart"></i>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="badge">{{ count(session('cart')) }}</span>
                        @endif
                    </a>

                    @auth
                    <form method="POST" action="{{ route('logout') }}" style="display: inline-flex; align-items: center;">
                        @csrf
                        <button type="submit" class="icon-btn" aria-label="Logout" title="Logout" style="border: none; background: transparent; cursor: pointer; padding: 0;">
                            <i data-lucide="log-out" style="color: #ef4444; width: 22px; height: 22px;"></i>
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>

        <div class="categories-row">
            <div class="container">
                <nav class="categories-nav">
                    <a href="{{ route('store.shop') }}" class="cat-link {{ request()->routeIs('store.shop') && !request()->hasAny(['category', 'sort', 'max_price']) ? 'active' : '' }}">All Products</a>
                    
                    @if(isset($globalCategories))
                        @foreach($globalCategories as $cat)
                            @if($cat->children->count() > 0)
                                <div class="mega-dropdown">
                                    <a href="{{ route('store.shop', ['category' => $cat->slug]) }}" class="cat-link {{ request('category') == $cat->slug ? 'active' : '' }}">
                                        {{ $cat->name }} <i data-lucide="chevron-down" style="width:14px;height:14px;"></i>
                                    </a>
                                    <div class="mega-menu">
                                        @foreach($cat->children as $child)
                                            <div>
                                                <a href="{{ route('store.shop', ['category' => $child->slug]) }}" style="padding: 10px 16px; font-weight: 700; color: var(--accent); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; display: block; text-decoration: none; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">{{ $child->name }}</a>
                                                @if($child->children->count() > 0)
                                                    @include('store.partials.mega_tree', ['categories' => $child->children, 'depth' => 0])
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('store.shop', ['category' => $cat->slug]) }}" class="cat-link {{ request('category') == $cat->slug ? 'active' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            @endif
                        @endforeach
                    @endif

                    <div class="mega-dropdown">
                        <a href="{{ route('store.shop', ['sort' => 'price_asc']) }}" class="cat-link" style="{{ request('sort') == 'price_asc' || request('sort') == 'discount_desc' || request()->has('max_price') ? 'color: var(--accent); background: rgba(255, 255, 255, 0.05);' : '' }}">Deals <i data-lucide="chevron-down" style="width:14px;height:14px;"></i></a>
                        <div class="mega-menu">
                            <a href="{{ route('store.shop', ['max_price' => 10]) }}" style="{{ request('max_price') == 10 ? 'color: var(--text-primary); background: rgba(255, 255, 255, 0.05);' : '' }}">Under $10</a>
                            <a href="{{ route('store.shop', ['max_price' => 50]) }}" style="{{ request('max_price') == 50 ? 'color: var(--text-primary); background: rgba(255, 255, 255, 0.05);' : '' }}">Under $50</a>
                            <a href="{{ route('store.shop', ['sort' => 'discount_desc']) }}" style="{{ request('sort') == 'discount_desc' ? 'color: var(--text-primary); background: rgba(255, 255, 255, 0.05);' : '' }}">Limited Offers</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Container -->
    <div class="mobile-menu-overlay" id="mobileMenu">
        <button class="mobile-menu-close" id="closeMenuBtn" aria-label="Close Menu"><i data-lucide="x"></i></button>
        <div class="mobile-nav-list">
            <a href="{{ route('store.shop') }}" class="mobile-nav-link">All Products</a>
            @if(isset($globalCategories))
                @foreach($globalCategories as $cat)
                    @if($cat->children->count() > 0)
                        <div style="border-bottom: 1px solid var(--glass-border); padding: 4px 0;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <a href="{{ route('store.shop', ['category' => $cat->slug]) }}" class="mobile-nav-link" style="border-bottom: none; padding: 8px 0; flex: 1;">{{ $cat->name }}</a>
                                <button type="button" onclick="const sm = document.getElementById('sub-mobile-{{ $cat->id }}'); const open = sm.style.display !== 'none'; sm.style.display = open ? 'none' : 'block'; this.querySelector('svg, i').style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 8px; display: flex; align-items: center;" aria-label="Toggle {{ $cat->name }} subcategories">
                                    <i data-lucide="chevron-down" style="width: 18px; height: 18px; transition: transform 0.2s;"></i>
                                </button>
                            </div>
                            <div id="sub-mobile-{{ $cat->id }}" style="display: none; padding-left: 16px; margin-bottom: 8px; border-left: 2px solid var(--accent);">
                                @foreach($cat->children as $child)
                                    <a href="{{ route('store.shop', ['category' => $child->slug]) }}" class="mobile-nav-link" style="font-size: 15px; font-weight: 500; border-bottom: none; padding: 6px 0; color: var(--text-secondary);">{{ $child->name }}</a>
                                    @if($child->children->count() > 0)
                                        <div style="padding-left: 12px; border-left: 1px solid rgba(255,255,255,0.1);">
                                            @foreach($child->children as $grandchild)
                                                <a href="{{ route('store.shop', ['category' => $grandchild->slug]) }}" class="mobile-nav-link" style="font-size: 13px; font-weight: 400; border-bottom: none; padding: 4px 0; color: var(--text-secondary); opacity: 0.85;">- {{ $grandchild->name }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route('store.shop', ['category' => $cat->slug]) }}" class="mobile-nav-link">{{ $cat->name }}</a>
                    @endif
                @endforeach
            @endif
            <a href="{{ route('store.shop', ['max_price' => 50]) }}" class="mobile-nav-link" style="color: var(--accent);">Under $50 Deals</a>
            @auth
                @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2)
                    <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link">Admin Dashboard</a>
                @else
                    <a href="{{ route('account.dashboard') }}" class="mobile-nav-link">My Account</a>
                @endif
                <a href="#" onclick="event.preventDefault(); document.getElementById('mobile-logout').submit();" class="mobile-nav-link" style="color: #ef4444;">Logout</a>
                <form id="mobile-logout" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
            @else
                <a href="{{ route('login') }}" class="mobile-nav-link" style="color: var(--accent);">Login / Register</a>
            @endauth
        </div>
    </div>

    <main class="container">
        @if(session('success'))
            <div style="background: rgba(52, 211, 153, 0.1); color: #34d399; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(52, 211, 153, 0.2);">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ route('store.home') }}" class="logo-container" style="color: #fff;">
                        <img src="{{ asset('brand/atoz-logo.png') }}" alt="AtoZ Gadgetz Logo" style="width: auto; height: 80px; border-radius: 0; filter: none; mix-blend-mode: screen;">
                    </a>
                    <p>Shop trending gadgets at affordable prices. Free shipping on qualifying orders. Worldwide delivery available on eligible products.</p>
                    <div class="contact">
                        <a href="mailto:contact@atozgadgetz.com"><i data-lucide="mail" style="width:14px;height:14px;display:inline;vertical-align:middle;"></i> contact@atozgadgetz.com</a>
                        <a href="https://instagram.com/atozgadgetzofficial" target="_blank" rel="noopener noreferrer"><i data-lucide="external-link" style="width:14px;height:14px;display:inline;vertical-align:middle;"></i> Instagram @atozgadgetzofficial</a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Shop</h4>
                    <ul>
                        <li><a href="{{ route('store.shop') }}">All Products</a></li>
                        <li><a href="{{ route('store.shop', ['category' => 'electronics']) }}">Electronics</a></li>
                        <li><a href="{{ route('store.shop', ['category' => 'smart-home']) }}">Smart Home</a></li>
                        <li><a href="{{ route('store.shop', ['category' => 'gaming']) }}">Gaming</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Shop by Price</h4>
                    <ul>
                        <li><a href="{{ route('store.shop', ['max_price' => 10]) }}">Under $10</a></li>
                        <li><a href="{{ route('store.shop', ['max_price' => 20]) }}">Under $20</a></li>
                        <li><a href="{{ route('store.shop', ['max_price' => 50]) }}">Under $50</a></li>
                        <li><a href="{{ route('store.shop', ['max_price' => 100]) }}">Under $100</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Support & Legal</h4>
                    <ul>
                        <li><a href="{{ route('store.about') }}">About Us</a></li>
                        <li><a href="{{ route('store.contact') }}">Contact Us</a></li>
                        <li><a href="{{ route('store.shipping') }}">Shipping & Payment Policy</a></li>
                        <li><a href="{{ route('store.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('store.returns') }}">Return & Refund Policy</a></li>
                        <li><a href="{{ route('store.terms') }}">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-badges">
                <div class="badges-list">
                    <span class="badge-item">PayPal</span>
                    <span class="badge-item">Visa</span>
                    <span class="badge-item">Mastercard</span>
                    <span class="badge-item">American Express</span>
                    <span class="badge-item">Discover</span>
                    <span class="badge-item">Payoneer</span>
                </div>
                <p style="font-size: 12px; color: var(--text-secondary);">Guaranteed Safe & Secure Checkout via 256-Bit Encrypted Gateways.</p>
            </div>

            <div class="footer-bottom">
                <p>© 2026 Atoz Gadgetz · Premium Gadgets with AtoZ · Created by <a href="https://prmarketingventures.com" target="_blank" rel="noopener noreferrer" style="color: var(--accent); font-weight: 600; text-decoration: underline;">PR Marketing Ventures</a></p>
                <div>
                    <span>All Days 11am – 9pm IST</span> · <span>Worldwide Delivery</span>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        // Wait for DOM and Lucide (deferred load)
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof lucide !== 'undefined') {
                lucide.createIcons();
            } else {
                // If deferred script is still loading
                window.addEventListener('load', () => lucide.createIcons());
            }

            // Mobile Menu Logic
            const mobileBtn = document.querySelector('.mobile-menu-btn');
            const closeBtn = document.getElementById('closeMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if(mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', () => {
                    mobileMenu.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevent background scrolling
                });
            }
            if(closeBtn && mobileMenu) {
                closeBtn.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }
        });

        // Header Scroll Effect
        let lastScrollY = window.scrollY;
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            if (!header) return;
            
            if (window.scrollY > 20) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            if (window.scrollY > lastScrollY && window.scrollY > 150) {
                header.classList.add('header-hidden');
            } else {
                header.classList.remove('header-hidden');
            }
            lastScrollY = window.scrollY;
        }, { passive: true });

        // Native IntersectionObserver for Scroll-Reveal Animations (Zero JS Dependencies)
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = { root: null, rootMargin: '0px 0px -50px 0px', threshold: 0.15 };
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('aos-animate');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('[data-aos]').forEach(el => {
                revealObserver.observe(el);
            });
        });
    </script>
    
    @guest
    <!-- Global Auth Modal Gate -->
    <div id="authRequiredModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
        <div style="background: #141414; border: 1px solid var(--accent); border-radius: 20px; width: 90%; max-width: 400px; padding: 40px 30px; text-align: center; box-shadow: 0 25px 50px -12px rgba(201,169,98,0.25); position: relative;">
            <button onclick="closeAuthModal()" style="position: absolute; top: 15px; right: 15px; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x" style="width: 24px;"></i></button>
            
            <div style="width: 60px; height: 60px; background: rgba(201,169,98,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--accent);">
                <i data-lucide="lock" style="width: 30px; height: 30px;"></i>
            </div>
            
            <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 10px; color: #fff;">Members Only Access</h2>
            <p style="color: var(--text-secondary); font-size: 15px; margin-bottom: 30px; line-height: 1.5;">To search our premium catalog or add items to your cart, please login or register an account.</p>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; text-align: center;">Login to Continue</a>
                <a href="{{ route('register') }}" class="btn" style="width: 100%; text-align: center; border: 1px solid var(--glass-border); color: #fff; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--accent)';" onmouseout="this.style.borderColor='var(--glass-border)';">Create Free Account</a>
            </div>
        </div>
    </div>

    <script>
        function requireAuth(e) {
            e.preventDefault();
            document.getElementById('authRequiredModal').style.display = 'flex';
        }
        function closeAuthModal() {
            document.getElementById('authRequiredModal').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Intercept Search Form
            const searchForm = document.querySelector('.search-bar');
            if (searchForm) {
                searchForm.addEventListener('submit', requireAuth);
            }
            
            // Intercept Add to Cart Forms
            const cartForms = document.querySelectorAll('form[action*="cart/add"]');
            cartForms.forEach(form => {
                form.addEventListener('submit', requireAuth);
            });
            
            // Intercept Direct Cart Buttons (if any are links)
            const cartBtns = document.querySelectorAll('.btn-cart, .add-to-cart');
            cartBtns.forEach(btn => {
                if (btn.tagName === 'A' || btn.type === 'button') {
                    btn.addEventListener('click', requireAuth);
                }
            });
        });
    </script>
    @endguest

    @include('store.partials.consent_banner')
</body>
</html>
