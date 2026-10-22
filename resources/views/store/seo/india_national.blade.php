@extends('layouts.store')

@section('title', 'Online Gadget Store India — Express Doorstep Delivery Across 28 States | AtoZGadgets')
@section('meta_description', 'Shop trending smart electronics, viral gadgets, and home accessories delivered directly to your doorstep in India. Transparent USD ($) checkout, express courier tracking, and 30-day money-back guarantee.')
@section('meta_keywords', 'gadgets online India, buy gadgets online India, online gadget store India, express gadgets delivery India, smart electronics India, atozgadgetz')

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url()->current() }}#webpage",
      "name": "Online Gadget Store India — 28 States Express Delivery Hub",
      "description": "Express door-to-door tracked delivery for innovative gadgets and electronics across India, billed in transparent USD.",
      "url": "{{ url()->current() }}",
      "isPartOf": {
        "@id": "{{ url('/') }}#website"
      }
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "{{ url('/') }}"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "India Express Delivery Hub",
          "item": "{{ url()->current() }}"
        }
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($shippingFaqs as $index => $faq)
        {
          "@type": "Question",
          "name": "{{ addslashes($faq['q']) }}",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "{{ addslashes($faq['a']) }}"
          }
        }@if(!$loop->last),@endif
        @endforeach
      ]
    }
  ]
}
</script>
@endsection

@section('content')
<div style="max-width: 1280px; margin: 0 auto; padding: clamp(1.25rem, 3vw, 2.5rem) clamp(1rem, 2vw, 1.5rem);">

    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('store.home') }}">Home</a>
        <i data-lucide="chevron-right" style="width:13px;height:13px;"></i>
        <span class="breadcrumb-current">India Delivery Hub</span>
    </nav>

    <!-- Hero Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.15) 0%, rgba(20, 20, 28, 0.9) 100%); border: 1px solid rgba(201, 169, 98, 0.35); border-radius: var(--radius-lg); padding: clamp(2rem, 5vw, 3.5rem) clamp(1rem, 3vw, 2.5rem); margin-bottom: 2.5rem; text-align: center;">
        <span style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--brand-primary); margin-bottom: 1rem;">
            🇮🇳 India Nationwide Coverage Directory
        </span>
        <h1 style="font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 800; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Trending Smart Gadgets & Electronics Delivered Across India
        </h1>
        <p style="font-size: clamp(0.95rem, 2vw, 1.15rem); color: var(--text-secondary); max-width: 820px; margin: 0 auto 1.75rem; line-height: 1.6;">
            Discover curated innovative tech, luxury quartz timepieces, smart home utilities, and viral lifestyle gadgets. Transparent USD ($) pricing, end-to-end tracked courier delivery, and 30-day money-back guarantee.
        </p>
        <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap;">
            <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="plane" style="width:16px;height:16px;color:var(--brand-primary);"></i> Priority Tracked Air Shipping
            </span>
            <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="dollar-sign" style="width:16px;height:16px;color:var(--brand-primary);"></i> Transparent USD ($) Checkout
            </span>
            <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="shield-check" style="width:16px;height:16px;color:var(--brand-primary);"></i> 30-Day Money-Back Guarantee
            </span>
        </div>
    </div>

    <!-- Top Indian Metros Quick Grid -->
    <div style="margin-bottom: 3.5rem;">
        <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
            Fastest Delivery to Major Tech & Commercial Metros
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap: 1rem;">
            @foreach($topCities as $city)
                <a href="{{ route('seo.india_city', [$city['state_slug'], $city['slug']]) }}" 
                   style="display: block; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); text-decoration: none; color: inherit; transition: all 0.2s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                        <span style="font-weight: 700; font-size: 1.05rem; color: var(--text-primary);">{{ $city['name'] }}</span>
                        <span style="font-size: 0.75rem; font-weight: 600; color: var(--brand-primary); background: rgba(201, 169, 98, 0.1); padding: 0.15rem 0.5rem; border-radius: var(--radius-sm);">{{ $city['state_code'] }}</span>
                    </div>
                    <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 0.5rem; line-height: 1.4;">
                        {{ $city['focus'] }}
                    </p>
                    <span style="font-size: 0.75rem; color: #10B981; font-weight: 500;">
                        ✓ Tracked Express: {{ $city['transit_days'] }} Days
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Trending Products in India Grid (Strictly USD Pricing) -->
    <div style="margin-bottom: 3.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 8px;">
            <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary);">
                Trending Products Available for Delivery in India
            </h2>
            <a href="{{ route('store.shop') }}" style="font-size: 0.875rem; color: var(--brand-primary); font-weight: 600; text-decoration: none;">View All Catalog (USD) →</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr)); gap: 1.25rem;">
            @foreach($trendingProducts as $product)
                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column;">
                    <a href="{{ route('store.product', $product->slug) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div style="position: relative; padding-top: 100%; background: #0a0a0f;">
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; padding: 1rem;">
                        </div>
                        <div style="padding: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.7rem;">
                                {{ $product->name }}
                            </h3>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color); flex-wrap: wrap; gap: 6px;">
                                <div style="display: flex; align-items: baseline; gap: 6px;">
                                    <span style="font-size: 1.25rem; font-weight: 700; color: var(--accent);">${{ number_format($product->effective_price, 2) }}</span>
                                    @if($product->has_active_discount)
                                        <span style="font-size: 0.85rem; text-decoration: line-through; color: var(--text-secondary);">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <span style="font-size: 0.75rem; color: #10B981; font-weight: 500;">✓ Ships to India</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- All 28 States Directory -->
    <div style="margin-bottom: 3.5rem;">
        <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
            Browse by State & Union Territory
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 180px), 1fr)); gap: 0.75rem;">
            @foreach($states as $slug => $state)
                <a href="{{ route('seo.india_state', $slug) }}" 
                   style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); text-decoration: none; color: inherit; font-size: 0.875rem;">
                    <span style="font-weight: 500; color: var(--text-primary);">{{ $state['name'] }}</span>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ $state['code'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- India Shipping & USD Billing FAQs -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem; margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;">
            Frequently Asked Questions — India Orders & Delivery
        </h2>
        <div style="display: grid; gap: 1.25rem; max-width: 800px; margin: 0 auto;">
            @foreach($shippingFaqs as $faq)
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: var(--radius-md); padding: 1.25rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                        {{ $faq['q'] }}
                    </h3>
                    <p style="font-size: 0.9375rem; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                        {{ $faq['a'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
