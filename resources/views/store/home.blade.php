@extends('layouts.store')

@section('title', 'AtoZGadgets — Trending Tech, Smart Home & Viral Gadgets Store USA')
@section('meta_description', 'Discover viral gadgets, innovative smart home devices, and premium electronics at AtoZGadgets. Enjoy fast 3-7 day shipping across the USA, 30-day returns, and 24/7 customer support.')
@section('meta_keywords', 'trending gadgets USA, viral tech electronics, smart home devices, buy gadgets online, fast shipping gadgets, premium tech store')
@section('og_title', 'AtoZGadgets — Trending Tech & Viral Gadgets Store USA')
@section('og_description', 'Shop viral tech, smart home devices, and premium gadgets with fast 3-7 day delivery across the United States.')
@section('canonical', url('/'))

@section('meta')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How fast is shipping to the United States?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Orders shipped to the United States typically arrive within 3 to 7 business days via USPS Priority Mail or Express Direct carriers from our verified fulfillment hubs."
      }
    },
    {
      "@type": "Question",
      "name": "What is AtoZGadgets' return policy?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "We offer a 30-day money-back guarantee on all products. If you are not completely satisfied with your order, return it within 30 days for a full refund or replacement."
      }
    },
    {
      "@type": "Question",
      "name": "What payment methods are accepted?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "We accept all major credit and debit cards (Visa, MasterCard, American Express, Discover) as well as secure PayPal checkout with end-to-end buyer protection."
      }
    },
    {
      "@type": "Question",
      "name": "How can I track my order?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Once your order ships, you receive an automated confirmation email with your real-time tracking number. You can also track your shipment live on our Order Tracking page."
      }
    }
  ]
}
</script>
@endsection

@section('content')
<style>
    /* Hero */
    .hero {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 40px 14px 30px;
        margin-top: 10px;
        overflow: hidden;
    }
    @media (min-width: 768px) {
        .hero { padding: 80px 20px; margin-top: 20px; }
    }
    .hero-bg-blur {
        position: absolute; top: 25%; left: 25%; width: 400px; height: 400px;
        background: rgba(201, 169, 98, 0.1); border-radius: 50%; filter: blur(100px); z-index: -1;
    }
    .hero-bg-blur-2 {
        position: absolute; bottom: 25%; right: 25%; width: 300px; height: 300px;
        background: rgba(59, 130, 246, 0.1); border-radius: 50%; filter: blur(100px); z-index: -1;
    }
    .shipping-badge {
        display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 50px;
        background: rgba(201, 169, 98, 0.1); color: var(--accent); font-size: 12.5px; font-weight: 600;
        border: 1px solid rgba(201, 169, 98, 0.25); margin-bottom: 18px;
    }
    .hero h1 { font-size: clamp(28px, 6vw, 68px); font-weight: 800; letter-spacing: -1px; margin-bottom: 16px; max-width: 900px; line-height: 1.15; }
    .hero p { font-size: clamp(15px, 2.2vw, 22px); color: var(--text-secondary); margin-bottom: 28px; max-width: 650px; line-height: 1.6; }
    .hero-buttons { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; width: 100%; }
    .btn-hero-primary { background: var(--accent); color: #000; padding: 14px 28px; border-radius: 50px; font-weight: 700; font-size: 15px; box-shadow: 0 10px 25px rgba(201, 169, 98, 0.25); border: none; transition: all 0.3s var(--ease-premium); cursor: pointer; }
    .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(201, 169, 98, 0.4); }
    .btn-hero-secondary { background: rgba(255,255,255,0.05); color: var(--text-primary); padding: 14px 28px; border-radius: 50px; font-weight: 600; font-size: 15px; border: 1px solid rgba(255,255,255,0.12); transition: all 0.3s var(--ease-premium); cursor: pointer; }
    .btn-hero-secondary:hover { background: rgba(255,255,255,0.1); transform: translateY(-3px); }
    @media (max-width: 480px) {
        .btn-hero-primary, .btn-hero-secondary { width: 100%; max-width: 320px; }
    }
    
    .hero-features { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 16px; margin-top: 36px; font-size: 12px; color: var(--text-secondary); }
    @media (min-width: 640px) {
        .hero-features { display: flex; gap: 24px; justify-content: center; flex-wrap: wrap; margin-top: 50px; font-size: 14px; }
    }
    .hero-features span { display: flex; align-items: center; justify-content: center; gap: 6px; }

    /* Marquee */
    .marquee-container { 
        overflow: hidden; 
        padding: 14px 0; 
        background: var(--hover-subtle); 
        border-top: 1px solid var(--border-color); 
        border-bottom: 1px solid var(--border-color); 
        display: flex; 
        flex-wrap: nowrap; 
    }
    .marquee-content { 
        flex-shrink: 0; 
        animation: marquee 30s linear infinite; 
        font-size: 12px; 
        font-weight: 600; 
        color: var(--text-primary); 
        letter-spacing: 1.5px; 
        text-transform: uppercase; 
        white-space: nowrap; 
    }
    @media (min-width: 768px) {
        .marquee-container { padding: 20px 0; }
        .marquee-content { font-size: 14px; letter-spacing: 2px; }
    }
    @keyframes marquee { 
        0% { transform: translateX(0); } 
        100% { transform: translateX(-100%); } 
    }

    /* Sections */
    section { padding: 48px 0; }
    @media (min-width: 768px) { section { padding: 80px 0; } }
    .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; gap: 12px; }
    @media (min-width: 768px) { .section-header { margin-bottom: 40px; } }
    .section-title { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; margin: 0; color: var(--text-primary); }
    @media (min-width: 768px) { .section-title { font-size: 36px; letter-spacing: -1px; } }
    .view-all { font-size: 13px; font-weight: 600; color: var(--accent); transition: color 0.3s; white-space: nowrap; }
    .view-all:hover { text-decoration: underline; }

    /* Categories Grid */
    .cat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    @media (min-width: 480px) { .cat-grid { grid-template-columns: repeat(3, 1fr); gap: 12px; } }
    @media (min-width: 1024px) { .cat-grid { grid-template-columns: repeat(5, 1fr); gap: 20px; } }
    .cat-card { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 20px 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; transition: all 0.4s var(--ease-premium); text-align: center; backdrop-filter: blur(10px); position: relative; overflow: hidden; text-decoration: none; }
    @media (min-width: 768px) { .cat-card { padding: 32px 16px; gap: 16px; border-radius: 20px; } }
    .cat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, var(--selection-bg) 0%, transparent 100%); opacity: 0; transition: opacity 0.4s; }
    .cat-card:hover { border-color: var(--accent); transform: translateY(-4px); box-shadow: 0 15px 30px rgba(0,0,0,0.15), 0 0 20px var(--focus-ring); }
    .cat-card:hover::before { opacity: 1; }
    .cat-icon-wrap { width: 50px; height: 50px; border-radius: 50%; background: var(--hover-subtle); display: flex; align-items: center; justify-content: center; transition: all 0.4s; border: 1px solid var(--border-color); z-index: 1; color: var(--text-primary); }
    @media (min-width: 768px) { .cat-icon-wrap { width: 64px; height: 64px; } }
    .cat-icon-wrap i, .cat-icon-wrap svg { width: 22px; height: 22px; }
    @media (min-width: 768px) { .cat-icon-wrap i, .cat-icon-wrap svg { width: 28px; height: 28px; } }
    .cat-card:hover .cat-icon-wrap { background: var(--accent); color: #000; transform: scale(1.1); box-shadow: 0 10px 20px var(--focus-ring); border-color: transparent; }
    .cat-card span { font-weight: 600; font-size: 13px; letter-spacing: 0.2px; z-index: 1; transition: color 0.3s; color: var(--text-primary); }
    @media (min-width: 768px) { .cat-card span { font-size: 15px; } }
    .cat-card:hover span { color: var(--accent); }

    /* Shop by Price (2-Column on Mobile!) */
    .price-section { background: var(--hover-subtle); }
    .price-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    @media (min-width: 768px) { .price-grid { grid-template-columns: repeat(4, 1fr); gap: 20px; } }
    .price-card { position: relative; overflow: hidden; border-radius: 16px; padding: 22px 16px; display: flex; flex-direction: column; gap: 6px; transition: all 0.5s var(--ease-premium); border: 1px solid var(--border-color); text-decoration: none; }
    @media (min-width: 768px) { .price-card { border-radius: 24px; padding: 40px 32px; gap: 12px; } }
    .price-card::after { content: ''; position: absolute; top: 0; right: 0; width: 120px; height: 120px; background: rgba(255,255,255,0.15); border-radius: 50%; filter: blur(35px); transform: translate(30%, -30%); }
    .price-card:hover { transform: translateY(-6px) scale(1.02); box-shadow: 0 20px 40px rgba(0,0,0,0.25); border-color: var(--accent); }
    .pc-tag { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: rgba(255,255,255,0.85); z-index: 1; }
    .pc-title { font-size: 20px; font-weight: 800; letter-spacing: -0.5px; color: #fff; z-index: 1; }
    @media (min-width: 768px) { .pc-title { font-size: 32px; } }
    .pc-link { font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.9); margin-top: 4px; transition: color 0.3s, transform 0.3s; z-index: 1; display: flex; align-items: center; gap: 4px; }
    .price-card:hover .pc-link { color: #fff; transform: translateX(4px); }
    
    .bg-green { background: linear-gradient(135deg, #0f766e, #042f2e); }
    .bg-blue { background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .bg-purple { background: linear-gradient(135deg, #8b5cf6, #4c1d95); }
    .bg-amber { background: linear-gradient(135deg, #f59e0b, #78350f); }

    /* Trust Signals (2-Column on Mobile!) */
    .trust-section { background: var(--hover-subtle); }
    .trust-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    @media (min-width: 768px) { .trust-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; } }
    @media (min-width: 1024px) { .trust-grid { grid-template-columns: repeat(4, 1fr); gap: 24px; } }
    .trust-card { border-radius: 14px; padding: 18px 14px; background: var(--bg-surface); border: 1px solid var(--border-color); transition: all 0.4s; position: relative; overflow: hidden; backdrop-filter: blur(10px); z-index: 1; }
    @media (min-width: 768px) { .trust-card { border-radius: 20px; padding: 32px; } }
    .trust-card:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    .trust-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; opacity: 0.8; transition: all 0.5s var(--ease-premium); z-index: -1; }
    .trust-card:hover::before { width: 100%; opacity: 0.05; }
    .trust-card.blue::before { background: #3b82f6; }
    .trust-card.green::before { background: #10b981; }
    .trust-card.purple::before { background: #8b5cf6; }
    .trust-card.amber::before { background: #f59e0b; }
    .trust-card.blue i, .trust-card.blue svg { color: #3b82f6; }
    .trust-card.green i, .trust-card.green svg { color: #10b981; }
    .trust-card.purple i, .trust-card.purple svg { color: #8b5cf6; }
    .trust-card.amber i, .trust-card.amber svg { color: #f59e0b; }
    .trust-icon { width: 42px; height: 42px; border-radius: 10px; background: var(--hover-subtle); display: flex; align-items: center; justify-content: center; margin-bottom: 12px; border: 1px solid var(--border-color); }
    @media (min-width: 768px) { .trust-icon { width: 56px; height: 56px; border-radius: 14px; margin-bottom: 20px; } }
    .trust-card h3 { font-weight: 700; font-size: 14px; margin-bottom: 6px; color: var(--text-primary); letter-spacing: -0.2px; line-height: 1.3; }
    @media (min-width: 768px) { .trust-card h3 { font-size: 19px; margin-bottom: 12px; } }
    .trust-card p { font-size: 12px; color: var(--text-secondary); line-height: 1.5; }
    @media (min-width: 768px) { .trust-card p { font-size: 15px; line-height: 1.7; } }

    /* About Strip */
    .about-strip { text-align: center; max-width: 900px; margin: 0 auto; padding: 0 10px; }
    .about-strip h2 { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 16px; color: var(--text-primary); line-height: 1.3; }
    @media (min-width: 768px) { .about-strip h2 { font-size: 36px; letter-spacing: -1px; margin-bottom: 24px; } }
    .about-strip p { font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 24px; }
    @media (min-width: 768px) { .about-strip p { font-size: 18px; line-height: 1.8; margin-bottom: 32px; } }
    .about-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 16px; }
    .about-links a { color: var(--accent); font-weight: 600; font-size: 14px; }
    .about-links a:hover { text-decoration: underline; }

    /* Payments Strip */
    .payment-strip { padding: 30px 0; border-top: 1px solid var(--border-color); text-align: center; }
    @media (min-width: 768px) { .payment-strip { padding: 40px 0; } }
    .payment-wrap { display: flex; flex-direction: column; align-items: center; gap: 16px; }
    @media (min-width: 768px) { .payment-wrap { flex-direction: row; justify-content: center; gap: 24px; } }
    .payment-label { font-weight: 600; font-size: 13px; color: var(--text-secondary); }
    .payment-methods { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
    .payment-method { padding: 6px 10px; background: var(--hover-subtle); border: 1px solid var(--border-color); border-radius: 6px; font-size: 11.5px; font-weight: 500; color: var(--text-secondary); }
</style>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg-blur"></div>
    <div class="hero-bg-blur-2"></div>
    
    <div class="shipping-badge" data-aos="fade-down">
        <i data-lucide="globe" style="width:14px;height:14px;"></i>
        <span>Worldwide Shipping to 50+ Countries</span>
    </div>
    
    <h1 data-aos="fade-up" data-aos-delay="100">You Deserve Gadgets Today!!</h1>
    
    <p data-aos="fade-up" data-aos-delay="200">
        Get all the gadgets under one Roof — 1,000+ curated products from Electronics
        to Smart Home devices, delivered worldwide.
    </p>
    
    <div class="hero-buttons" data-aos="fade-up" data-aos-delay="300">
        <button class="btn-hero-primary" onclick="window.location.href='{{ route('store.shop') }}'">Shop All Products</button>
        <button class="btn-hero-secondary" onclick="window.location.href='{{ route('store.shop', ['category' => 'electronics']) }}'">Browse Electronics</button>
    </div>

    <div class="hero-features" data-aos="fade-up" data-aos-delay="400">
        <span><i data-lucide="shield-check" style="width:14px;color:#34d399;"></i> SSL Secure</span>
        <span><i data-lucide="star" style="width:14px;color:#fbbf24;"></i> 10,000+ Reviews</span>
        <span><i data-lucide="truck" style="width:14px;color:#60a5fa;"></i> 10–15 Day Delivery</span>
        <span><i data-lucide="credit-card" style="width:14px;color:#a78bfa;"></i> Visa · Mastercard</span>
    </div>
</section>

<!-- Marquee -->
<div class="marquee-container">
    <div class="marquee-content">
        Premium Gadgets &nbsp;·&nbsp; Worldwide Shipping &nbsp;·&nbsp; Secure Checkout &nbsp;·&nbsp; 7-Day Exchange &nbsp;·&nbsp; 10,000+ Customers &nbsp;·&nbsp;
    </div>
    <div class="marquee-content" aria-hidden="true">
        Premium Gadgets &nbsp;·&nbsp; Worldwide Shipping &nbsp;·&nbsp; Secure Checkout &nbsp;·&nbsp; 7-Day Exchange &nbsp;·&nbsp; 10,000+ Customers &nbsp;·&nbsp;
    </div>
    <div class="marquee-content" aria-hidden="true">
        Premium Gadgets &nbsp;·&nbsp; Worldwide Shipping &nbsp;·&nbsp; Secure Checkout &nbsp;·&nbsp; 7-Day Exchange &nbsp;·&nbsp; 10,000+ Customers &nbsp;·&nbsp;
    </div>
    <div class="marquee-content" aria-hidden="true">
        Premium Gadgets &nbsp;·&nbsp; Worldwide Shipping &nbsp;·&nbsp; Secure Checkout &nbsp;·&nbsp; 7-Day Exchange &nbsp;·&nbsp; 10,000+ Customers &nbsp;·&nbsp;
    </div>
</div>

<!-- Categories Grid -->
<section>
    <div class="section-header" data-aos="fade-up">
        <h2 class="section-title">Explore Categories</h2>
        <a href="{{ route('store.shop') }}" class="view-all">View all &rarr;</a>
    </div>
    
    <div class="cat-grid">
        <a href="{{ route('store.shop', ['category' => 'electronics']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="0">
            <div class="cat-icon-wrap"><i data-lucide="smartphone"></i></div>
            <span>Electronics</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'audio']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="50">
            <div class="cat-icon-wrap"><i data-lucide="headphones"></i></div>
            <span>Audio & Sound</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'cameras']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="100">
            <div class="cat-icon-wrap"><i data-lucide="camera"></i></div>
            <span>Cameras</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'laptops-pcs']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="150">
            <div class="cat-icon-wrap"><i data-lucide="laptop"></i></div>
            <span>Laptops & PCs</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'smartwatches']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="200">
            <div class="cat-icon-wrap"><i data-lucide="watch"></i></div>
            <span>Smartwatches</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'gaming']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="250">
            <div class="cat-icon-wrap"><i data-lucide="gamepad-2"></i></div>
            <span>Gaming</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'smart-home']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="300">
            <div class="cat-icon-wrap"><i data-lucide="wifi"></i></div>
            <span>Smart Home</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'car-accessories']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="350">
            <div class="cat-icon-wrap"><i data-lucide="car"></i></div>
            <span>Car Accessories</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'mobile-accessories']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="400">
            <div class="cat-icon-wrap"><i data-lucide="zap"></i></div>
            <span>Mobile Accessories</span>
        </a>
        <a href="{{ route('store.shop', ['category' => 'home-gadgets']) }}" class="cat-card" data-aos="fade-up" data-aos-delay="450">
            <div class="cat-icon-wrap"><i data-lucide="home"></i></div>
            <span>Home Gadgets</span>
        </a>
    </div>
</section>

<!-- Shop by Price -->
<section class="price-section">
    <div style="text-align: center; margin-bottom: 48px;" data-aos="fade-up">
        <h2 class="section-title" style="margin-bottom: 12px;">Shop by Price</h2>
        <p style="color: var(--text-secondary); font-size: 18px;">Gadgets for every budget — from daily deals to premium picks.</p>
    </div>
    
    <div class="price-grid">
        <a href="{{ route('seo.price_hub', 10) }}" class="price-card bg-green" data-aos="fade-up" data-aos-delay="0">
            <span class="pc-tag">Budget Buys</span>
            <span class="pc-title">Under $10</span>
            <span class="pc-link">Shop now &rarr;</span>
        </a>
        <a href="{{ route('seo.price_hub', 20) }}" class="price-card bg-blue" data-aos="fade-up" data-aos-delay="100">
            <span class="pc-tag">Best Value</span>
            <span class="pc-title">Under $20</span>
            <span class="pc-link">Shop now &rarr;</span>
        </a>
        <a href="{{ route('seo.price_hub', 50) }}" class="price-card bg-purple" data-aos="fade-up" data-aos-delay="200">
            <span class="pc-tag">Popular</span>
            <span class="pc-title">Under $50</span>
            <span class="pc-link">Shop now &rarr;</span>
        </a>
        <a href="{{ route('seo.price_hub', 100) }}" class="price-card bg-amber" data-aos="fade-up" data-aos-delay="300">
            <span class="pc-tag">Premium</span>
            <span class="pc-title">Under $100</span>
            <span class="pc-link">Shop now &rarr;</span>
        </a>
    </div>
</section>

<!-- Featured Products -->
<section>
    <div class="section-header" data-aos="fade-up">
        <div>
            <h2 class="section-title">Featured Collection</h2>
            <p style="color: var(--text-secondary); margin-top: 8px;">Hand-picked products trending right now.</p>
        </div>
        <a href="{{ route('store.shop') }}" class="view-all">View all &rarr;</a>
    </div>

    <div class="grid">
        @forelse($featuredProducts as $index => $product)
            <a href="{{ route('store.product', $product->slug) }}" class="card" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 100 }}">
                <img loading="lazy" decoding="async" src="{{ $product->customer_thumbnail }}" alt="{{ $product->name }}">
                <div class="card-title">{{ $product->name }}</div>
                <div class="card-price">${{ number_format($product->effective_price, 2) }}</div>
                <span class="btn btn-primary" style="display:block; text-align:center; width:100%; padding: 12px; font-size: 14px; text-transform: uppercase;">View Details</span>
            </a>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 48px 24px; color: var(--text-secondary); background: rgba(128,128,128,0.03); border: 1px dashed var(--border-color); border-radius: 16px;">
                <p style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">New trending gadgets are on the way!</p>
                <p style="font-size: 14px;">Browse our full catalog to explore available tech.</p>
                <a href="{{ route('store.shop') }}" class="btn btn-primary" style="display:inline-block; margin-top: 16px; padding: 10px 24px;">Explore Catalog</a>
            </div>
        @endforelse
    </div>
</section>

<!-- Trust Signals -->
<section class="trust-section">
    <div style="text-align: center; margin-bottom: 48px;" data-aos="fade-up">
        <h2 class="section-title" style="margin-bottom: 12px;">Why Shop with AtoZ Gadgetz?</h2>
        <p style="color: var(--text-secondary); font-size: 18px;">Your Destination for Premium Tech — Worldwide</p>
    </div>
    
    <div class="trust-grid">
        <div class="trust-card blue" data-aos="fade-up" data-aos-delay="0">
            <div class="trust-icon"><i data-lucide="truck"></i></div>
            <h3>Free Worldwide Shipping</h3>
            <p>Free shipping on orders over $30. Delivered in 10–15 days — as fast as possible for our beloved customers.</p>
        </div>
        <div class="trust-card green" data-aos="fade-up" data-aos-delay="100">
            <div class="trust-icon"><i data-lucide="shield-check"></i></div>
            <h3>Secure Checkout</h3>
            <p>256-Bit SSL encrypted payments. PayPal, Visa, Mastercard, Amex & Payoneer accepted.</p>
        </div>
        <div class="trust-card purple" data-aos="fade-up" data-aos-delay="200">
            <div class="trust-icon"><i data-lucide="rotate-ccw"></i></div>
            <h3>7-Day Returns</h3>
            <p>Received a defective item? Contact us within 7 days for an easy replacement.</p>
        </div>
        <div class="trust-card amber" data-aos="fade-up" data-aos-delay="300">
            <div class="trust-icon"><i data-lucide="headphones"></i></div>
            <h3>Verified Support</h3>
            <p>Dedicated customer assistance and 24/7 online shipment tracking.</p>
        </div>
    </div>
</section>

<!-- About Strip -->
<section class="about-strip" data-aos="fade-up">
    <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-bottom:16px;">
        <i data-lucide="award" style="color:var(--accent); width:20px;"></i>
        <span style="color:var(--accent); font-weight:600; font-size:14px; letter-spacing:1px; text-transform:uppercase;">AtoZ Gadgetz</span>
    </div>
    <h2>Get all the trending gadgets under one Roof</h2>
    <p>
        From cutting-edge <strong>smartwatches</strong> and high-fidelity <strong>audio gear</strong> to essential <strong>mobile accessories</strong> and smart home gadgets — AtoZ Gadgetz is your one-stop destination. Worldwide delivery available on eligible products with fast dispatch and online tracking.
    </p>
    <div class="about-links">
        <a href="{{ route('store.about') }}">About Us &rarr;</a>
        <a href="{{ route('store.contact') }}">Contact Support &rarr;</a>
        <a href="{{ route('store.shipping') }}">Shipping Policy &rarr;</a>
    </div>
</section>

<!-- Payment Strip -->
<section class="payment-strip" data-aos="fade-up">
    <div class="payment-wrap">
        <span class="payment-label">We Accept:</span>
        <div class="payment-methods">
            <span class="payment-method">Visa</span>
            <span class="payment-method">Mastercard</span>
            <span class="payment-method">Amex</span>
            <span class="payment-method">UPI</span>
            <span class="payment-method">Net Banking</span>
            <span class="payment-method">Maestro</span>
            <span class="payment-method">Debit Card</span>
            <span class="payment-method">IMPS</span>
        </div>
    </div>
</section>

@endsection
