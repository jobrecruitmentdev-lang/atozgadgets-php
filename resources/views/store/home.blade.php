@extends('layouts.store')

@section('title', 'AtoZ Gadgetz — Shop Gadgets Worldwide | Electronics, Smart Home & Tech')

@section('content')
<style>
    /* Hero */
    .hero {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 80px 20px;
        margin-top: 20px;
        overflow: hidden;
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
        display: inline-flex; items-align: center; gap: 8px; padding: 8px 16px; border-radius: 50px;
        background: rgba(201, 169, 98, 0.1); color: var(--accent); font-size: 14px; font-weight: 500;
        border: 1px solid rgba(201, 169, 98, 0.2); margin-bottom: 24px;
    }
    .hero h1 { font-size: clamp(40px, 6vw, 80px); font-weight: 800; letter-spacing: -2px; margin-bottom: 24px; max-width: 900px; line-height: 1.1; }
    .hero p { font-size: clamp(18px, 2vw, 24px); color: var(--text-secondary); margin-bottom: 40px; max-width: 650px; line-height: 1.6; }
    .hero-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
    .btn-hero-primary { background: var(--accent); color: #fff; padding: 16px 32px; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 10px 25px rgba(201, 169, 98, 0.25); border: none; transition: all 0.3s var(--ease-premium); cursor: pointer; }
    .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(201, 169, 98, 0.4); }
    .btn-hero-secondary { background: rgba(255,255,255,0.05); color: var(--text-primary); padding: 16px 32px; border-radius: 50px; font-weight: 500; font-size: 16px; border: 1px solid rgba(255,255,255,0.1); transition: all 0.3s var(--ease-premium); cursor: pointer; }
    .btn-hero-secondary:hover { background: rgba(255,255,255,0.1); transform: translateY(-3px); }
    
    .hero-features { display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; margin-top: 60px; font-size: 14px; color: var(--text-secondary); }
    .hero-features span { display: flex; align-items: center; gap: 6px; }

    /* Marquee */
    .marquee-container { 
        overflow: hidden; 
        padding: 20px 0; 
        background: rgba(255,255,255,0.02); 
        border-top: 1px solid var(--glass-border); 
        border-bottom: 1px solid var(--glass-border); 
        display: flex;
        flex-wrap: nowrap;
    }
    .marquee-content { 
        flex-shrink: 0;
        animation: marquee 30s linear infinite; 
        font-size: 14px; 
        font-weight: 500; 
        color: var(--text-primary); 
        letter-spacing: 2px; 
        text-transform: uppercase; 
        white-space: nowrap;
    }
    @keyframes marquee { 
        0% { transform: translateX(0); } 
        100% { transform: translateX(-100%); } 
    }

    /* Sections */
    section { padding: 80px 0; }
    .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; }
    .section-title { font-size: 36px; font-weight: 700; letter-spacing: -1px; margin: 0; }
    .view-all { font-size: 14px; font-weight: 500; color: var(--text-secondary); transition: color 0.3s; }
    .view-all:hover { color: var(--text-primary); }

    /* Categories Grid */
    .cat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    @media (min-width: 640px) { .cat-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1024px) { .cat-grid { grid-template-columns: repeat(5, 1fr); } }
    .cat-card { display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 32px 16px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; transition: all 0.4s var(--ease-premium); text-align: center; backdrop-filter: blur(10px); position: relative; overflow: hidden; }
    .cat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(201,169,98,0.05) 0%, transparent 100%); opacity: 0; transition: opacity 0.4s; }
    .cat-card:hover { border-color: rgba(201, 169, 98, 0.4); transform: translateY(-6px); box-shadow: 0 15px 30px rgba(0,0,0,0.5), 0 0 20px rgba(201, 169, 98, 0.1); }
    .cat-card:hover::before { opacity: 1; }
    .cat-icon-wrap { width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; transition: all 0.4s; border: 1px solid rgba(255,255,255,0.05); z-index: 1; }
    .cat-card:hover .cat-icon-wrap { background: linear-gradient(135deg, var(--accent), #e3c887); color: #000; transform: scale(1.1); box-shadow: 0 10px 20px rgba(201, 169, 98, 0.3); border-color: transparent; }
    .cat-card span { font-weight: 600; font-size: 15px; letter-spacing: 0.5px; z-index: 1; transition: color 0.3s; }
    .cat-card:hover span { color: var(--accent); }

    /* Shop by Price */
    .price-section { background: rgba(255,255,255,0.01); }
    .price-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
    @media (min-width: 640px) { .price-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .price-grid { grid-template-columns: repeat(4, 1fr); } }
    .price-card { position: relative; overflow: hidden; border-radius: 24px; padding: 40px 32px; display: flex; flex-direction: column; gap: 12px; transition: all 0.5s var(--ease-premium); border: 1px solid rgba(255,255,255,0.1); }
    .price-card::after { content: ''; position: absolute; top: 0; right: 0; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(40px); transform: translate(30%, -30%); }
    .price-card:hover { transform: translateY(-8px) scale(1.02); box-shadow: 0 25px 50px rgba(0,0,0,0.5); border-color: rgba(255,255,255,0.3); }
    .pc-tag { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.7); z-index: 1; }
    .pc-title { font-size: 32px; font-weight: 800; letter-spacing: -1px; color: #fff; z-index: 1; }
    .pc-link { font-size: 15px; font-weight: 500; color: rgba(255,255,255,0.7); margin-top: 8px; transition: color 0.3s, transform 0.3s; z-index: 1; display: flex; align-items: center; gap: 6px; }
    .price-card:hover .pc-link { color: #fff; transform: translateX(5px); }
    
    .bg-green { background: linear-gradient(135deg, #0f766e, #042f2e); }
    .bg-blue { background: linear-gradient(135deg, #2563eb, #1e3a8a); }
    .bg-purple { background: linear-gradient(135deg, #8b5cf6, #4c1d95); }
    .bg-amber { background: linear-gradient(135deg, #f59e0b, #78350f); }

    /* Trust Signals */
    .trust-section { background: rgba(255,255,255,0.01); }
    .trust-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
    @media (min-width: 768px) { .trust-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .trust-grid { grid-template-columns: repeat(4, 1fr); } }
    .trust-card { border-radius: 20px; padding: 32px; border: 1px solid rgba(255,255,255,0.05); transition: all 0.4s; position: relative; overflow: hidden; backdrop-filter: blur(10px); z-index: 1; }
    .trust-card:hover { transform: translateY(-6px); border-color: rgba(255,255,255,0.15); box-shadow: 0 15px 30px rgba(0,0,0,0.4); }
    .trust-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; opacity: 0.8; transition: all 0.5s var(--ease-premium); z-index: -1; }
    .trust-card:hover::before { width: 100%; opacity: 0.05; }
    .trust-card.blue::before { background: #60a5fa; }
    .trust-card.green::before { background: #34d399; }
    .trust-card.purple::before { background: #a78bfa; }
    .trust-card.amber::before { background: #fbbf24; }
    .trust-card.blue { background: rgba(59,130,246,0.03); } .trust-card.blue i { color: #60a5fa; }
    .trust-card.green { background: rgba(16,185,129,0.03); } .trust-card.green i { color: #34d399; }
    .trust-card.purple { background: rgba(139,92,246,0.03); } .trust-card.purple i { color: #a78bfa; }
    .trust-card.amber { background: rgba(245,158,11,0.03); } .trust-card.amber i { color: #fbbf24; }
    .trust-icon { width: 56px; height: 56px; border-radius: 14px; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.05); box-shadow: inset 0 2px 10px rgba(255,255,255,0.05); }
    .trust-card h3 { font-weight: 700; font-size: 19px; margin-bottom: 12px; color: #fff; letter-spacing: -0.2px; }
    .trust-card p { font-size: 15px; color: var(--text-secondary); line-height: 1.7; }

    /* About Strip */
    .about-strip { text-align: center; max-width: 900px; margin: 0 auto; }
    .about-strip h2 { font-size: 36px; font-weight: 700; letter-spacing: -1px; margin-bottom: 24px; }
    .about-strip p { font-size: 18px; color: var(--text-secondary); line-height: 1.8; margin-bottom: 32px; }
    .about-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 24px; }
    .about-links a { color: var(--accent); font-weight: 500; }
    .about-links a:hover { text-decoration: underline; }

    /* Payments Strip */
    .payment-strip { padding: 40px 0; border-top: 1px solid var(--glass-border); text-align: center; }
    .payment-wrap { display: flex; flex-direction: column; align-items: center; gap: 24px; }
    @media (min-width: 768px) { .payment-wrap { flex-direction: row; justify-content: center; } }
    .payment-label { font-weight: 500; font-size: 14px; }
    .payment-methods { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
    .payment-method { padding: 6px 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; font-size: 12px; font-weight: 500; }
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
        <a href="{{ route('store.shop', ['max_price' => 10]) }}" class="price-card bg-green" data-aos="fade-up" data-aos-delay="0">
            <span class="pc-tag">Budget Buys</span>
            <span class="pc-title">Under $10</span>
            <span class="pc-link">Shop now &rarr;</span>
        </a>
        <a href="{{ route('store.shop', ['max_price' => 20]) }}" class="price-card bg-blue" data-aos="fade-up" data-aos-delay="100">
            <span class="pc-tag">Best Value</span>
            <span class="pc-title">Under $20</span>
            <span class="pc-link">Shop now &rarr;</span>
        </a>
        <a href="{{ route('store.shop', ['max_price' => 50]) }}" class="price-card bg-purple" data-aos="fade-up" data-aos-delay="200">
            <span class="pc-tag">Popular</span>
            <span class="pc-title">Under $50</span>
            <span class="pc-link">Shop now &rarr;</span>
        </a>
        <a href="{{ route('store.shop', ['max_price' => 100]) }}" class="price-card bg-amber" data-aos="fade-up" data-aos-delay="300">
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
            @if($index < 4)
                <a href="{{ route('store.product', $product->slug) }}" class="card" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <img loading="lazy" decoding="async" src="{{ $product->thumbnail_image ?? 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $product->name }}">
                    <div class="card-title">{{ $product->name }}</div>
                    <div class="card-price">${{ $product->discount_price ?? $product->price }}</div>
                    <span class="btn btn-primary" style="display:block; text-align:center; width:100%; padding: 12px; font-size: 14px; text-transform: uppercase;">View Details</span>
                </a>
            @endif
        @empty
            <a href="/product/mock-watch" class="card" data-aos="fade-up" data-aos-delay="100">
                <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=400&q=80" alt="Smart Watch">
                <div class="card-title">Ultra Smart Watch Series 9</div>
                <div class="card-price">$29.99</div>
                <span class="btn btn-primary" style="display:block; text-align:center; width:100%; padding: 12px; font-size: 14px; text-transform: uppercase;">View Details</span>
            </a>
            <a href="/product/mock-earbuds" class="card" data-aos="fade-up" data-aos-delay="200">
                <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?auto=format&fit=crop&w=400&q=80" alt="Earbuds">
                <div class="card-title">TWS Pro Wireless Earbuds</div>
                <div class="card-price">$19.99</div>
                <span class="btn btn-primary" style="display:block; text-align:center; width:100%; padding: 12px; font-size: 14px; text-transform: uppercase;">View Details</span>
            </a>
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
            <p>SSL encrypted payments. Razorpay, Visa, Mastercard, Amex & more accepted.</p>
        </div>
        <div class="trust-card purple" data-aos="fade-up" data-aos-delay="200">
            <div class="trust-icon"><i data-lucide="rotate-ccw"></i></div>
            <h3>7-Day Exchange</h3>
            <p>Received a defective item? Contact us within 7 days for an easy exchange.</p>
        </div>
        <div class="trust-card amber" data-aos="fade-up" data-aos-delay="300">
            <div class="trust-icon"><i data-lucide="star"></i></div>
            <h3>100% Trusted</h3>
            <p>Verified seller with thousands of happy customers across 50+ countries.</p>
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
        From cutting-edge <strong>smartwatches</strong> and high-fidelity <strong>audio gear</strong> to essential <strong>mobile accessories</strong> and smart home gadgets — AtoZ Gadgetz is your one-stop destination. We source directly from 50+ global warehouses so you get the best prices, fast dispatch, and delivery worldwide.
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
