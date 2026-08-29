@extends('layouts.store')

@section('title', 'Online Gadget Store USA — Fast Shipping Across All 50 States | AtoZGadgets')
@section('meta_description', 'Shop trending smart home devices, electronics, car accessories, and mobile gadgets with fast 3-7 day shipping across all 50 US States. 30-day money-back guarantee.')
@section('meta_keywords', 'gadgets online USA, buy gadgets online USA, online gadget store USA, fast shipping gadgets USA, electronics store USA')

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url()->current() }}#webpage",
      "name": "Online Gadget Store USA — 50 States Delivery Hub",
      "description": "Fast domestic US priority shipping for smart electronics, viral tech gadgets, and accessories.",
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
          "name": "USA Fast Delivery Hub",
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
<div style="max-width: 1280px; margin: 0 auto; padding: 2.5rem 1.5rem;">

    <!-- Breadcrumb -->
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <span style="color: var(--brand-primary); font-weight: 600;">USA Delivery Hub</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.15) 0%, rgba(20, 20, 28, 0.9) 100%); border: 1px solid rgba(201, 169, 98, 0.35); border-radius: var(--radius-lg); padding: 3rem 2rem; margin-bottom: 3.5rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            🇺🇸 Nationwide Priority Fulfillment
        </div>
        <h1 style="font-size: clamp(1.85rem, 4vw, 3rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Shop Trending Gadgets Online — Shipped Anywhere in the USA
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 760px; margin: 0 auto 2rem; line-height: 1.6;">
            AtoZGadgets delivers premium viral electronics, smart home devices, and mobile accessories directly to your doorstep across all 50 US States with reliable 3–7 business day USPS & UPS delivery.
        </p>

        <!-- US Delivery Features Badges -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1.5rem; max-width: 800px; margin: 0 auto;">
            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: var(--radius-md); font-size: 0.875rem; color: var(--text-primary);">
                ⚡ <strong>3–7 Days</strong> USPS / UPS Transit
            </div>
            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: var(--radius-md); font-size: 0.875rem; color: var(--text-primary);">
                🛡️ <strong>30-Day</strong> Money-Back Guarantee
            </div>
            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: var(--radius-md); font-size: 0.875rem; color: var(--text-primary);">
                📍 <strong>Live GPS</strong> Tracking on All Orders
            </div>
        </div>
    </div>

    <!-- Top US Commercial Metros -->
    <div style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem;">
            Popular City Delivery Destinations
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem;">
            @foreach($topCities as $city)
                <a href="{{ route('seo.usa_city', [$city['state_slug'], $city['slug']]) }}" 
                   style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; text-decoration: none; color: inherit; transition: all 0.2s; display: flex; flex-direction: column;"
                   onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-3px)'"
                   onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                    <div style="font-weight: 700; color: var(--text-primary); font-size: 1.05rem; margin-bottom: 0.25rem;">
                        {{ $city['name'] }}, {{ $city['state_code'] }}
                    </div>
                    <div style="font-size: 0.8125rem; color: var(--brand-primary); margin-bottom: 0.5rem; font-weight: 500;">
                        {{ $city['focus'] }}
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: auto;">
                        Est. Delivery: <strong>{{ $city['transit_days'] }} business days</strong>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- All 50 US States Directory -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem; margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem;">
            All 50 US States Coverage Directory
        </h2>
        <p style="font-size: 0.9375rem; color: var(--text-secondary); margin-bottom: 2rem;">
            Click on your state to view local shipping timelines, top-rated categories, and eligible delivery zones.
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 0.75rem;">
            @foreach($states as $slug => $state)
                <a href="{{ route('seo.usa_state', $slug) }}" 
                   style="padding: 0.6rem 0.85rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--text-secondary); text-decoration: none; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s;"
                   onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.color='var(--brand-primary)'"
                   onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'; this.style.color='var(--text-secondary)'">
                    <span>{{ $state['name'] }}</span>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">{{ $state['code'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Trending Products in USA -->
    <div style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem;">
            Trending Gadgets Across the United States
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
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
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                                <span style="font-size: 1.25rem; font-weight: 700; color: var(--brand-primary);">${{ number_format($product->price, 2) }}</span>
                                <span style="font-size: 0.75rem; color: #10B981; font-weight: 500;">✓ Free Delivery</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- US Shipping FAQs -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;">
            Frequently Asked Questions — Shipping to the USA
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
