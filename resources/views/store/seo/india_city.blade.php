@extends('layouts.store')

@section('title', "Buy Gadgets Online in {$cityData['name']}, {$stateData['code']} — Express Delivery | AtoZGadgets")
@section('meta_description', "Shop trending smart home devices, watches, and electronics delivered directly to your doorstep in {$cityData['name']}, {$stateData['name']} in {$cityData['transit_days']} business days. Billed in USD ($) with 30-day guarantee.")
@section('meta_keywords', "gadgets {$cityData['name']}, electronics {$cityData['name']} {$stateData['code']}, buy gadgets {$cityData['name']}, smart tech {$cityData['name']}, gadgets delivered {$cityData['name']}, atozgadgetz")

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "name": "Shop Gadgets Online in {{ addslashes($cityData['name']) }}, {{ $stateData['code'] }}",
      "description": "Express door-to-door gadget delivery to {{ addslashes($cityData['name']) }}, {{ addslashes($stateData['name']) }} with full online tracking and transparent USD pricing.",
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
          "name": "{{ addslashes($stateData['name']) }}",
          "item": "{{ route('seo.india_state', $stateData['slug']) }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ addslashes($cityData['name']) }}",
          "item": "{{ url()->current() }}"
        }
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($faqs as $index => $faq)
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
        <a href="{{ route('seo.india_national') }}">India Hub</a>
        <i data-lucide="chevron-right" style="width:13px;height:13px;"></i>
        <a href="{{ route('seo.india_state', $stateData['slug']) }}">{{ $stateData['name'] }}</a>
        <i data-lucide="chevron-right" style="width:13px;height:13px;"></i>
        <span class="breadcrumb-current">{{ $cityData['name'] }}</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.15) 0%, rgba(20, 20, 28, 0.9) 100%); border: 1px solid rgba(201, 169, 98, 0.35); border-radius: var(--radius-lg); padding: clamp(1.75rem, 4vw, 3rem) clamp(1rem, 3vw, 2rem); margin-bottom: 2.5rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            🇮🇳 {{ $cityData['name'] }} • {{ $stateData['name'] }} Express Hub
        </div>
        <h1 style="font-size: clamp(1.85rem, 4vw, 3rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Trending Gadgets & Electronics Delivered to {{ $cityData['name'] }}
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto 1.5rem; line-height: 1.6;">
            Specializing in <strong>{{ $cityData['focus'] }}</strong> with direct doorstep delivery across {{ $cityData['name'] }}. Billed in transparent USD ($) with full 30-day buyer protection.
        </p>
        <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap;">
            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="truck" style="width:15px;height:15px;display:inline;color:var(--brand-primary);"></i> Estimated Transit: <strong>{{ $cityData['transit_days'] }} Business Days</strong>
            </span>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="dollar-sign" style="width:15px;height:15px;display:inline;color:var(--brand-primary);"></i> Charged in USD ($)
            </span>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="shield-check" style="width:15px;height:15px;display:inline;color:var(--brand-primary);"></i> 30-Day Money-Back Guarantee
            </span>
        </div>
    </div>

    <!-- Curated Products Grid (Strictly USD Pricing) -->
    <div style="margin-bottom: 3.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 8px;">
            <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary);">
                Popular Gadgets Delivering to {{ $cityData['name'] }}
            </h2>
            <a href="{{ route('store.shop') }}" style="font-size: 0.875rem; color: var(--brand-primary); font-weight: 600; text-decoration: none;">View All Catalog (USD) →</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr)); gap: 1.25rem;">
            @foreach($products as $product)
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
                                <span style="font-size: 0.75rem; color: #10B981; font-weight: 500;">✓ Delivered to {{ $cityData['name'] }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- City FAQs -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem; margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;">
            Frequently Asked Questions — {{ $cityData['name'] }}, {{ $stateData['code'] }} Delivery
        </h2>
        <div style="display: grid; gap: 1.25rem; max-width: 800px; margin: 0 auto;">
            @foreach($faqs as $faq)
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

    <!-- Nearby Sibling Cities in Same State -->
    @if(count($siblingCities) > 0)
        <div style="border-top: 1px solid var(--border-color); padding-top: 2.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                Other Popular Cities in {{ $stateData['name'] }}
            </h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                @foreach($siblingCities as $sibling)
                    <a href="{{ route('seo.india_city', [$stateData['slug'], $sibling['slug']]) }}" 
                       style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">
                        {{ $sibling['name'] }}
                    </a>
                @endforeach
                <a href="{{ route('seo.india_state', $stateData['slug']) }}" 
                   style="padding: 0.4rem 0.9rem; background: rgba(201, 169, 98, 0.1); border: 1px solid var(--brand-primary); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--brand-primary); text-decoration: none; font-weight: 600;">
                    All {{ $stateData['name'] }} Delivery Hub →
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
