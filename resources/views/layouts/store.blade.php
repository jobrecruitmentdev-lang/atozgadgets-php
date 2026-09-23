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
    <title>@yield('title', 'AtoZGadgets - Trending Gadgets, Smart Home & Electronics in USA')</title>
    <meta name="description" content="@yield('meta_description', 'Shop trending smart electronics, viral gadgets, and premium tech accessories at AtoZGadgets. Fast 3-7 day delivery across the USA, 30-day money-back guarantee, and secure checkout.')">
    <meta name="keywords" content="@yield('meta_keywords', 'viral gadgets, trending electronics, smart home devices, tech accessories, buy gadgets online USA, fast shipping electronics')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- US Geo-Targeting & Regional Search Signals -->
    <meta name="geo.region" content="US">
    <meta name="geo.placename" content="United States">
    <meta name="target" content="all">
    <meta name="audience" content="all">
    <meta name="coverage" content="Worldwide">
    <meta name="distribution" content="Global">
    <meta name="rating" content="General">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <!-- OpenGraph / Social Metadata -->
    <meta property="og:site_name" content="AtoZGadgets">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'AtoZGadgets - Trending Tech & Viral Gadgets Store')">
    <meta property="og:description" content="@yield('og_description', 'Discover viral tech, smart home devices, and premium gadgets with fast 3-7 day shipping across the United States.')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('brand/atoz-logo.png'))">

    <!-- Twitter / X Card Metadata -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'AtoZGadgets - Trending Tech & Viral Gadgets Store')">
    <meta name="twitter:description" content="@yield('og_description', 'Discover viral tech, smart home devices, and premium gadgets with fast 3-7 day shipping across the United States.')">
    <meta name="twitter:image" content="@yield('og_image', asset('brand/atoz-logo.png'))">

    <!-- Global Schema.org Structured Data (Organization & Sitelinks SearchBox) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "@id": "https://atozgadgetz.com/#organization",
          "name": "AtoZGadgets",
          "url": "https://atozgadgetz.com",
          "logo": {
            "@type": "ImageObject",
            "url": "https://atozgadgetz.com/brand/atoz-logo.png",
            "caption": "AtoZGadgets Logo"
          },
          "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Customer Service",
            "email": "support@atozgadgetz.com",
            "availableLanguage": ["English"]
          }
        },
        {
          "@type": "WebSite",
          "@id": "https://atozgadgetz.com/#website",
          "url": "https://atozgadgetz.com",
          "name": "AtoZGadgets",
          "description": "Premium trending electronics and smart gadgets boutique serving the United States.",
          "publisher": {
            "@id": "https://atozgadgetz.com/#organization"
          },
          "potentialAction": {
            "@type": "SearchAction",
            "target": {
              "@type": "EntryPoint",
              "urlTemplate": "https://atozgadgetz.com/shop?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>

    @yield('schema')
    @yield('meta')

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
        @media (max-width: 480px) { .container { padding-left: 14px; padding-right: 14px; } }
        
        /* Global Breadcrumb */
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--text-secondary); margin-bottom: 20px; flex-wrap: wrap; line-height: 1.4; }
        .breadcrumb a { color: var(--text-secondary); text-decoration: none; transition: color 0.2s ease; }
        .breadcrumb a:hover { color: var(--accent); }
        .breadcrumb i, .breadcrumb svg { width: 13px; height: 13px; color: var(--text-muted); flex-shrink: 0; }
        .breadcrumb .breadcrumb-current, .breadcrumb span:last-child { color: var(--accent); font-weight: 600; }
        
        /* Header Ported from Next.js */
        header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: transform 0.4s var(--ease-premium), background 0.3s var(--ease-premium), box-shadow 0.3s var(--ease-premium); background: var(--bg-base); border-bottom: 1px solid var(--border-color); }
        header.scrolled { background: var(--glass-bg); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); box-shadow: var(--glass-shadow); }
        header.header-hidden { transform: translateY(-100%) !important; }
        
        .top-banner { background: var(--selection-bg); text-align: center; padding: 7px 12px; font-size: 11px; font-weight: 600; color: var(--accent); display: block; border-bottom: 1px solid var(--border-color); overflow: hidden; white-space: nowrap; text-overflow: ellipsis; letter-spacing: 0.2px; }
        @media (min-width: 768px) { .top-banner { display: block; padding: 6px 0; font-size: 12px; color: var(--text-secondary); font-weight: 500; } }
        
        .nav-main { display: flex; align-items: center; justify-content: space-between; height: 60px; gap: 8px; }
        @media (min-width: 768px) { .nav-main { height: 90px; gap: 20px; } }
        
        .logo-container { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 18px; letter-spacing: -0.5px; }
        .logo-container img { width: auto; height: 38px; border-radius: 0; filter: none; mix-blend-mode: screen; max-width: 140px; object-fit: contain; }
        @media (min-width: 768px) { .logo-container img { height: 68px; max-width: none; } }
        
        .search-bar { flex: 1; max-width: 600px; position: relative; display: none; }
        @media (min-width: 768px) { .search-bar { display: block; } }
        .search-bar input { width: 100%; background: var(--input-bg); border: 1px solid var(--border-color); padding: 12px 20px 12px 45px; border-radius: 12px; color: var(--text-primary); outline: none; transition: all 0.3s; }
        .search-bar input:focus { border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--focus-ring); background: var(--bg-surface); }
        .search-bar button.search-btn { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: var(--text-secondary); display: flex; align-items: center; justify-content: center; z-index: 10; padding: 0; }
        .search-bar button.search-btn:hover { color: var(--accent); }
        .search-bar button.search-btn svg, .search-bar button.search-btn i { width: 18px; height: 18px; }

        /* Slide-down Mobile Search Bar */
        .mobile-search-bar-wrap {
            display: none;
            background: var(--bg-surface-elevated);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--accent);
            padding: 10px 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.6);
        }
        .mobile-search-bar-wrap.active {
            display: block;
            animation: slideDownSearch 0.25s var(--ease-premium);
        }
        @keyframes slideDownSearch {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .mobile-search-form {
            display: flex;
            align-items: center;
            position: relative;
            width: 100%;
        }
        .mobile-search-form input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            padding: 10px 38px 10px 36px;
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }
        .mobile-search-form input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px var(--focus-ring);
        }
        .mobile-search-form .search-icon-btn {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            cursor: pointer;
        }
        .mobile-search-form .search-close-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 4px;
        }

        .nav-icons { display: flex; align-items: center; gap: 4px; }
        @media (min-width: 768px) { .nav-icons { gap: 10px; } }
        .icon-btn { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; color: var(--text-primary); transition: all 0.3s; position: relative; border: none; background: transparent; cursor: pointer; }
        @media (min-width: 768px) { .icon-btn { width: 44px; height: 44px; } }
        .icon-btn:hover { background: var(--hover-subtle); color: var(--accent); }
        .mobile-search-btn { display: inline-flex; }
        @media (min-width: 768px) { .mobile-search-btn { display: none; } }
        .badge { position: absolute; top: 4px; right: 4px; background: var(--accent); color: var(--text-inverse); font-size: 10px; font-weight: bold; width: 17px; height: 17px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .badge-dot { position: absolute; top: 8px; right: 8px; width: 8px; height: 8px; background: var(--accent); border-radius: 50%; }
        
        .mobile-menu-btn { display: inline-flex; }
        @media (min-width: 768px) { .mobile-menu-btn { display: none; } }

        :root {
            --header-height: 160px;
        }

        /* Categories Row */
        .categories-row { border-top: 1px solid var(--border-color); display: none; background: var(--bg-base); backdrop-filter: blur(12px); position: relative; z-index: 1001; }
        @media (min-width: 768px) { .categories-row { display: block; } }
        .categories-nav { display: flex; justify-content: flex-start; align-items: center; gap: 3px; padding: 3px 0; overflow: visible; flex-wrap: wrap; }
        .cat-link { font-size: 12.5px; font-weight: 500; color: var(--text-secondary); padding: 5px 8px; min-height: 32px; border-radius: 6px; transition: all 0.2s; display: flex; align-items: center; gap: 4px; white-space: nowrap; text-decoration: none; border: 1px solid transparent; cursor: pointer; }
        .cat-link:hover { background: var(--hover-subtle); color: var(--text-primary); }
        .cat-link.active { color: var(--accent); background: var(--selection-bg); border-color: var(--focus-ring); font-weight: 600; }
        .mega-dropdown { position: relative; display: inline-block; }
        .mega-dropdown::after { content: ''; position: absolute; top: 100%; left: 0; right: 0; height: 12px; display: block; }
        .mega-menu { position: absolute; top: calc(100% + 2px); left: 0; min-width: 230px; background: #141419; border: 1px solid var(--border-strong); border-radius: 14px; padding: 10px; opacity: 0; visibility: hidden; transform: translateY(6px); transition: opacity 0.2s var(--ease-premium), transform 0.2s var(--ease-premium), visibility 0.2s; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.7); z-index: 1050; pointer-events: none; }
        
        /* Desktop Mega Menu Hover & Focus-within Trigger */
        .mega-dropdown:hover .mega-menu,
        .mega-dropdown:focus-within .mega-menu { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
        
        .mega-menu a { display: block; padding: 8px 12px; color: var(--text-secondary); font-size: 13px; font-weight: 500; border-radius: 8px; transition: background 0.15s, color 0.15s; white-space: nowrap; text-decoration: none; }
        .mega-menu a:hover, .mega-menu a:focus { color: var(--accent); background: rgba(201, 169, 98, 0.12); }

        /* Mobile Menu Drawer & Backdrop */
        .mobile-menu-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 1999; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .mobile-menu-backdrop.active { opacity: 1; visibility: visible; }
        .mobile-menu-overlay { position: fixed; top: 0; bottom: 0; left: 0; width: 85%; max-width: 320px; background: var(--bg-surface-elevated); border-right: 1px solid var(--border-color); z-index: 2000; transform: translateX(-100%); transition: transform 0.35s var(--ease-premium); display: flex; flex-direction: column; padding: 0; overflow-y: auto; box-shadow: 20px 0 50px rgba(0,0,0,0.6); }
        .mobile-menu-overlay.active { transform: translateX(0); }
        .mobile-menu-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: var(--bg-base); }
        .mobile-menu-header img { height: 36px; width: auto; mix-blend-mode: screen; }
        .mobile-menu-close { background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 8px; cursor: pointer; padding: 6px; display: flex; align-items: center; justify-content: center; }
        .mobile-menu-body { padding: 16px; display: flex; flex-direction: column; gap: 14px; flex: 1; }
        .mobile-nav-list { display: flex; flex-direction: column; gap: 3px; }
        .mobile-nav-link { font-size: 15px; font-weight: 600; color: var(--text-primary); padding: 10px 12px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; transition: background 0.2s, color 0.2s; border-bottom: none; }
        .mobile-nav-link:hover { background: var(--hover-subtle); color: var(--accent); }
        .mobile-nav-link.active { background: var(--selection-bg); color: var(--accent); }

        /* Main Content - Dynamically offsets below header */
        main { padding-top: calc(var(--header-height, 100px) + 16px); min-height: 70vh; }
        @media (min-width: 768px) { main { padding-top: calc(var(--header-height, 180px) + 24px); } }

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

        /* Responsive 2-Column Product Grid on Mobile Devices */
        @media (max-width: 640px) {
            .grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .card { padding: 12px 10px; border-radius: 14px; }
            .card img { height: 150px; margin-bottom: 10px; border-radius: 8px; }
            .card-title { font-size: 13px; margin-bottom: 6px; line-height: 1.35; height: 35px; -webkit-line-clamp: 2; }
            .card-price { font-size: 17px; margin-bottom: 10px; font-weight: 800; }
            .card .btn-primary { padding: 8px 6px; font-size: 11.5px; border-radius: 6px; letter-spacing: 0; }
        }
        @media (max-width: 360px) {
            .grid { gap: 6px; }
            .card { padding: 8px; }
            .card img { height: 130px; }
            .card-price { font-size: 15px; }
        }

        /* App-Like Mobile Bottom Navigation Bar */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: calc(56px + env(safe-area-inset-bottom, 0px));
            padding-bottom: env(safe-area-inset-bottom, 0px);
            background: rgba(14, 14, 18, 0.96);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 1050;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.5);
        }
        @media (min-width: 768px) {
            .mobile-bottom-nav { display: none !important; }
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            flex: 1;
            height: 100%;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 10.5px;
            font-weight: 500;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.2s, transform 0.15s;
            position: relative;
            -webkit-tap-highlight-color: transparent;
        }
        .bottom-nav-item i, .bottom-nav-item svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
            transition: transform 0.2s, color 0.2s;
        }
        .bottom-nav-item:active { transform: scale(0.92); }
        .bottom-nav-item.active, .bottom-nav-item:hover { color: var(--accent); }
        .bottom-nav-item.active i, .bottom-nav-item.active svg { stroke-width: 2.3; color: var(--accent); }
        .bottom-nav-badge {
            position: absolute;
            top: 5px;
            right: 50%;
            transform: translateX(14px);
            background: var(--accent);
            color: #000;
            font-size: 9.5px;
            font-weight: 800;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }
        @media (max-width: 767px) {
            body { padding-bottom: calc(60px + env(safe-area-inset-bottom, 0px)); }
            footer { padding: 40px 0 calc(90px + env(safe-area-inset-bottom, 0px)); }
        }

        /* Responsive Custom Storefront Pagination */
        .custom-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 40px auto 20px;
            padding: 0 10px;
            max-width: 100%;
            flex-wrap: wrap;
        }
        .page-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 8px;
            background: var(--bg-surface, #141414);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            min-height: 40px;
            user-select: none;
        }
        .page-btn:hover:not(.page-btn-disabled) {
            background: var(--selection-bg);
            border-color: var(--accent);
            color: var(--accent);
        }
        .page-btn-disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
        }
        .mobile-page-indicator {
            display: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            padding: 0 8px;
        }
        .desktop-page-numbers {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .page-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 10px;
            border-radius: 8px;
            background: var(--bg-surface, #141414);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .page-number:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--hover-subtle);
        }
        .page-number-active {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: var(--text-inverse) !important;
            font-weight: 800 !important;
        }
        .page-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 40px;
            color: var(--text-secondary);
        }
        @media (max-width: 640px) {
            .custom-pagination {
                justify-content: space-between;
                width: 100%;
                gap: 6px;
            }
            .desktop-page-numbers {
                display: none;
            }
            .mobile-page-indicator {
                display: block;
            }
            .page-btn {
                padding: 8px 12px;
                font-size: 12.5px;
                min-height: 38px;
            }
        }

        /* Fallback for native nav[role="navigation"] */
        nav[role="navigation"]:not(.custom-pagination) { display: flex; align-items: center; justify-content: center; font-size: 14px; margin-top: 40px; gap: 8px; flex-wrap: wrap; }
        nav[role="navigation"]:not(.custom-pagination) p { display: none; }
        nav[role="navigation"]:not(.custom-pagination) a, nav[role="navigation"]:not(.custom-pagination) span { padding: 9px 14px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-surface); color: var(--text-primary); transition: all 0.2s; text-decoration: none; }

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
        @php
            $globalFreeThreshold = (int)\App\Models\Setting::get('free_shipping_threshold', 50);
        @endphp
        <div class="top-banner">
            Free worldwide priority shipping on orders over ${{ $globalFreeThreshold }} · Express 3–7 Day Delivery · 100% Secure Checkout
        </div>
        
        <div class="container">
            <div class="nav-main">
                <button class="icon-btn mobile-menu-btn" id="mobileMenuToggleBtn" aria-label="Toggle Menu"><i data-lucide="menu"></i></button>
                
                <a href="{{ route('store.home') }}" class="logo-container">
                    <img src="{{ asset('brand/atoz-logo.png') }}" alt="AtoZ Gadgetz Logo">
                </a>

                <form action="{{ route('store.shop') }}" method="GET" class="search-bar">
                    <button type="submit" aria-label="Submit Search" class="search-btn">
                        <i data-lucide="search" aria-hidden="true"></i>
                    </button>
                    <label for="searchInput" class="sr-only" style="position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0;">Search</label>
                    <input type="text" id="searchInput" name="q" value="{{ request('q') }}" placeholder="Search for gadgets, accessories...">
                </form>

                <div class="nav-icons">
                    <button type="button" class="icon-btn mobile-search-btn" id="mobileSearchTrigger" aria-label="Search Catalog">
                        <i data-lucide="search"></i>
                    </button>

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

        <!-- Slide-down Mobile Search Bar -->
        <div id="mobileSearchBar" class="mobile-search-bar-wrap">
            <form action="{{ route('store.shop') }}" method="GET" class="mobile-search-form">
                <button type="submit" class="search-icon-btn" aria-label="Submit Search"><i data-lucide="search"></i></button>
                <input type="text" id="mobileSearchInput" name="q" value="{{ request('q') }}" placeholder="Search trending gadgets, smart devices..." aria-label="Search Catalog" autocomplete="off">
                <button type="button" class="search-close-btn" id="closeMobileSearch" aria-label="Close Search"><i data-lucide="x"></i></button>
            </form>
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
                                            @if($child->children->count() > 0)
                                                <div style="margin-bottom: 6px;">
                                                    <a href="{{ route('store.shop', ['category' => $child->slug]) }}" style="font-weight: 700; color: var(--accent); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">{{ $child->name }}</a>
                                                    @include('store.partials.mega_tree', ['categories' => $child->children, 'depth' => 0])
                                                </div>
                                            @else
                                                <a href="{{ route('store.shop', ['category' => $child->slug]) }}">{{ $child->name }}</a>
                                            @endif
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
                        <a href="{{ route('seo.price_hub', 50) }}" class="cat-link" style="{{ request()->routeIs('seo.price_hub') ? 'color: var(--accent); background: rgba(255, 255, 255, 0.05);' : '' }}">Deals <i data-lucide="chevron-down" style="width:14px;height:14px;"></i></a>
                        <div class="mega-menu">
                            <a href="{{ route('seo.price_hub', 10) }}">Under $10</a>
                            <a href="{{ route('seo.price_hub', 20) }}">Under $20</a>
                            <a href="{{ route('seo.price_hub', 50) }}">Under $50</a>
                            <a href="{{ route('seo.price_hub', 100) }}">Under $100</a>
                        </div>
                    </div>

                    <a href="{{ route('seo.usa_national') }}" class="cat-link {{ request()->routeIs('seo.usa*') ? 'active' : '' }}">🇺🇸 USA Delivery</a>

                    <div class="mega-dropdown">
                        <a href="#" class="cat-link {{ request()->routeIs('seo.gift*', 'seo.guide*', 'seo.faq*') ? 'active' : '' }}" onclick="return false;">Explore <i data-lucide="chevron-down" style="width:13px;height:13px;"></i></a>
                        <div class="mega-menu">
                            <a href="{{ route('seo.gifts_index') }}">Gift Guides</a>
                            <a href="{{ route('seo.guides_index') }}">Buying Guides</a>
                            <a href="{{ route('seo.faq_master') }}">FAQ & Help Center</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Drawer & Backdrop -->
    <div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>
    <div class="mobile-menu-overlay" id="mobileMenu">
        <div class="mobile-menu-header">
            <a href="{{ route('store.home') }}" class="logo-container">
                <img src="{{ asset('brand/atoz-logo.png') }}" alt="AtoZ Gadgetz Logo">
            </a>
            <button class="mobile-menu-close" id="closeMenuBtn" aria-label="Close Menu"><i data-lucide="x"></i></button>
        </div>
        <div class="mobile-menu-body">
            <form action="{{ route('store.shop') }}" method="GET" class="mobile-search-form">
                <button type="submit" class="search-icon-btn" aria-label="Search"><i data-lucide="search"></i></button>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." aria-label="Search products">
            </form>

            <div class="mobile-nav-list">
                <a href="{{ route('store.shop') }}" class="mobile-nav-link {{ request()->routeIs('store.shop') && !request()->hasAny(['category', 'max_price', 'q']) ? 'active' : '' }}">
                    <span><i data-lucide="grid" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:8px;color:var(--accent);"></i> All Products</span>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-secondary);"></i>
                </a>
                <a href="{{ route('seo.usa_national') }}" class="mobile-nav-link {{ request()->routeIs('seo.usa*') ? 'active' : '' }}">
                    <span>🇺🇸 USA 50-State Hub</span>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-secondary);"></i>
                </a>
                <a href="{{ route('seo.price_hub', 50) }}" class="mobile-nav-link {{ request()->routeIs('seo.price_hub') ? 'active' : '' }}">
                    <span>⚡ Under $50 Deals</span>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-secondary);"></i>
                </a>
                <a href="{{ route('seo.gifts_index') }}" class="mobile-nav-link {{ request()->routeIs('seo.gift*') ? 'active' : '' }}">
                    <span>🎁 Tech Gifts Guide</span>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-secondary);"></i>
                </a>
                <a href="{{ route('seo.guides_index') }}" class="mobile-nav-link {{ request()->routeIs('seo.guide*') ? 'active' : '' }}">
                    <span>📖 Buying Guides & Reviews</span>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-secondary);"></i>
                </a>
                <a href="{{ route('seo.faq_master') }}" class="mobile-nav-link {{ request()->routeIs('seo.faq*') ? 'active' : '' }}">
                    <span>❓ FAQ & Help Center</span>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-secondary);"></i>
                </a>

                @if(isset($globalCategories))
                    <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border-color);">
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px; padding-left: 8px; margin-bottom: 8px;">Categories</div>
                        @foreach($globalCategories as $cat)
                            @if($cat->children->count() > 0)
                                <div style="border-radius: 8px; border: 1px solid rgba(255,255,255,0.04); margin-bottom: 4px; background: rgba(255,255,255,0.015);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 2px 4px 2px 0;">
                                        <a href="{{ route('store.shop', ['category' => $cat->slug]) }}" class="mobile-nav-link" style="flex: 1; padding: 8px 10px;">{{ $cat->name }}</a>
                                        <button type="button" onclick="const sm = document.getElementById('sub-mobile-{{ $cat->id }}'); const open = sm.style.display !== 'none'; sm.style.display = open ? 'none' : 'block'; this.querySelector('svg, i').style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 8px 10px; display: flex; align-items: center;" aria-label="Toggle {{ $cat->name }}">
                                            <i data-lucide="chevron-down" style="width: 16px; height: 16px; transition: transform 0.2s;"></i>
                                        </button>
                                    </div>
                                    <div id="sub-mobile-{{ $cat->id }}" style="display: none; padding-left: 14px; margin: 0 10px 8px 10px; border-left: 2px solid var(--accent);">
                                        @foreach($cat->children as $child)
                                            <a href="{{ route('store.shop', ['category' => $child->slug]) }}" style="font-size: 13.5px; font-weight: 500; display: block; padding: 6px 0; color: var(--text-secondary); text-decoration: none;">{{ $child->name }}</a>
                                            @if($child->children->count() > 0)
                                                <div style="padding-left: 10px; border-left: 1px solid rgba(255,255,255,0.08); margin: 2px 0 6px 0;">
                                                    @foreach($child->children as $grandchild)
                                                        <a href="{{ route('store.shop', ['category' => $grandchild->slug]) }}" style="font-size: 12.5px; font-weight: 400; display: block; padding: 3px 0; color: var(--text-muted); text-decoration: none;">• {{ $grandchild->name }}</a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('store.shop', ['category' => $cat->slug]) }}" class="mobile-nav-link" style="padding: 8px 10px;">{{ $cat->name }}</a>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border-color);">
                    @auth
                        @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2)
                            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link">
                                <span><i data-lucide="shield" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:8px;color:var(--brand-admin);"></i> Admin Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('account.dashboard') }}" class="mobile-nav-link">
                                <span><i data-lucide="user" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:8px;color:var(--accent);"></i> My Account</span>
                            </a>
                        @endif
                        <a href="#" onclick="event.preventDefault(); document.getElementById('mobile-logout').submit();" class="mobile-nav-link" style="color: #ef4444;">
                            <span><i data-lucide="log-out" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:8px;"></i> Logout</span>
                        </a>
                        <form id="mobile-logout" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
                    @else
                        <a href="{{ route('login') }}" class="mobile-nav-link" style="color: var(--accent); font-weight: 700;">
                            <span><i data-lucide="log-in" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:8px;"></i> Login / Register</span>
                        </a>
                    @endauth
                </div>
            </div>
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
                    <h4>Shop Categories</h4>
                    <ul>
                        <li><a href="{{ route('store.shop') }}">All Products</a></li>
                        <li><a href="{{ route('seo.price_hub', 10) }}">Gadgets Under $10</a></li>
                        <li><a href="{{ route('seo.price_hub', 20) }}">Gadgets Under $20</a></li>
                        <li><a href="{{ route('seo.price_hub', 50) }}">Gadgets Under $50</a></li>
                        <li><a href="{{ route('seo.price_hub', 100) }}">Gadgets Under $100</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Collections & USA Delivery</h4>
                    <ul>
                        <li><a href="{{ route('seo.usa_national') }}">USA 50-State Hub</a></li>
                        <li><a href="{{ route('seo.gifts_index') }}">Tech Gifts Guide</a></li>
                        <li><a href="{{ route('seo.use_case', 'travel-gadgets') }}">Travel Tech</a></li>
                        <li><a href="{{ route('seo.use_case', 'car-gadgets') }}">Car Accessories</a></li>
                        <li><a href="{{ route('seo.use_case', 'home-office-gadgets') }}">Desk & Home Office</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Guides & Support</h4>
                    <ul>
                        <li><a href="{{ route('seo.guides_index') }}">Buying Guides & Reviews</a></li>
                        <li><a href="{{ route('seo.faq_master') }}">FAQ & Help Center</a></li>
                        <li><a href="{{ route('store.shipping') }}">Shipping & Payment Policy</a></li>
                        <li><a href="{{ route('store.returns') }}">30-Day Return Policy</a></li>
                        <li><a href="{{ route('store.about') }}">About Us</a></li>
                        <li><a href="{{ route('store.contact') }}">Contact Customer Care</a></li>
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
                <p>© 2026 Atoz Gadgetz · Premium Curated Gadgets · Created by <a href="https://prmarketingventures.com" target="_blank" rel="noopener noreferrer" style="color: var(--accent); font-weight: 600; text-decoration: underline;">PR Marketing Ventures</a></p>
                <div>
                    <span>24/7 Global Customer Support</span> · <span>Priority Worldwide Delivery (7–15 Business Days)</span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- App-Like Mobile Bottom Navigation Bar -->
    <nav class="mobile-bottom-nav" aria-label="Mobile Navigation">
        <a href="{{ route('store.home') }}" class="bottom-nav-item {{ request()->routeIs('store.home') ? 'active' : '' }}">
            <i data-lucide="home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('store.shop') }}" class="bottom-nav-item {{ request()->routeIs('store.shop') && !request('q') ? 'active' : '' }}">
            <i data-lucide="grid"></i>
            <span>Shop</span>
        </a>
        <button type="button" class="bottom-nav-item" id="bottomNavSearchBtn" aria-label="Search Catalog">
            <i data-lucide="search"></i>
            <span>Search</span>
        </button>
        <a href="{{ route('store.cart') }}" class="bottom-nav-item {{ request()->routeIs('store.cart') ? 'active' : '' }}" style="position: relative;">
            <i data-lucide="shopping-cart"></i>
            <span>Cart</span>
            @if(session('cart') && count(session('cart')) > 0)
                <span class="bottom-nav-badge">{{ count(session('cart')) }}</span>
            @endif
        </a>
        @auth
            @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2)
                <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i data-lucide="shield"></i>
                    <span>Admin</span>
                </a>
            @else
                <a href="{{ route('account.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('account.*') ? 'active' : '' }}">
                    <i data-lucide="user"></i>
                    <span>Account</span>
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="bottom-nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                <i data-lucide="user"></i>
                <span>Account</span>
            </a>
        @endauth
    </nav>
    
    <script>
        // Wait for DOM and Lucide (deferred load)
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof lucide !== 'undefined') {
                lucide.createIcons();
            } else {
                window.addEventListener('load', () => lucide.createIcons());
            }

            // Mobile Menu Drawer Logic
            const mobileBtn = document.getElementById('mobileMenuToggleBtn');
            const closeBtn = document.getElementById('closeMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuBackdrop = document.getElementById('mobileMenuBackdrop');
            
            function openMobileDrawer() {
                if (mobileMenu) mobileMenu.classList.add('active');
                if (menuBackdrop) menuBackdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            function closeMobileDrawer() {
                if (mobileMenu) mobileMenu.classList.remove('active');
                if (menuBackdrop) menuBackdrop.classList.remove('active');
                document.body.style.overflow = '';
            }

            if(mobileBtn) mobileBtn.addEventListener('click', openMobileDrawer);
            if(closeBtn) closeBtn.addEventListener('click', closeMobileDrawer);
            if(menuBackdrop) menuBackdrop.addEventListener('click', closeMobileDrawer);

            // Mobile Slide-down Search Bar Logic
            const searchTrigger = document.getElementById('mobileSearchTrigger');
            const bottomNavSearch = document.getElementById('bottomNavSearchBtn');
            const searchBarWrap = document.getElementById('mobileSearchBar');
            const closeSearchBtn = document.getElementById('closeMobileSearch');
            const searchInput = document.getElementById('mobileSearchInput');

            function toggleMobileSearch(e) {
                if (e) e.preventDefault();
                if (!searchBarWrap) return;
                const isOpen = searchBarWrap.classList.toggle('active');
                if (isOpen && searchInput) {
                    setTimeout(() => searchInput.focus(), 120);
                }
            }

            if (searchTrigger) searchTrigger.addEventListener('click', toggleMobileSearch);
            if (bottomNavSearch) bottomNavSearch.addEventListener('click', toggleMobileSearch);
        });

        // Dynamic Header Height Synchronization (prevents header from ever covering page content)
        function syncHeaderHeight() {
            const header = document.getElementById('main-header');
            if (header) {
                const h = header.getBoundingClientRect().height || header.offsetHeight;
                if (h > 0) {
                    document.documentElement.style.setProperty('--header-height', h + 'px');
                }
            }
        }
        syncHeaderHeight();
        window.addEventListener('DOMContentLoaded', syncHeaderHeight);
        window.addEventListener('load', syncHeaderHeight);
        window.addEventListener('resize', syncHeaderHeight);
        if (window.ResizeObserver) {
            const hdr = document.getElementById('main-header');
            if (hdr) new ResizeObserver(syncHeaderHeight).observe(hdr);
        }

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
            // Intercept Search Forms
            const searchForms = document.querySelectorAll('.search-bar, .mobile-search-form');
            searchForms.forEach(form => {
                form.addEventListener('submit', requireAuth);
            });
            
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
