@extends('layouts.store')

@section('title', "Shop Gadgets Online in {$stateData['name']} — Express Delivery to {$stateData['code']} | AtoZGadgets")
@section('meta_description', "Buy trending gadgets, smart home devices, and electronics delivered across {$stateData['name']} in {$stateData['transit_days']} business days. Billed in transparent USD ($) with 30-day guarantee.")
@section('meta_keywords', "gadgets {$stateData['name']}, electronics {$stateData['name']}, buy gadgets {$stateData['name']}, smart home {$stateData['code']}, gadgets online {$stateData['name']}, atozgadgetz")

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "name": "Shop Gadgets Online in {{ addslashes($stateData['name']) }}",
      "description": "Express door-to-door gadget delivery across {{ addslashes($stateData['name']) }} ({{ $stateData['code'] }}) billed in transparent USD.",
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
          "name": "India Hub",
          "item": "{{ route('seo.india_national') }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ addslashes($stateData['name']) }}",
          "item": "{{ url()->current() }}"
        }
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
        <span class="breadcrumb-current">{{ $stateData['name'] }}</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.12) 0%, rgba(20, 20, 28, 0.85) 100%); border: 1px solid rgba(201, 169, 98, 0.3); border-radius: var(--radius-lg); padding: clamp(1.75rem, 4vw, 3rem) clamp(1rem, 3vw, 2rem); margin-bottom: 2.5rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            {{ $stateData['code'] }} • {{ $stateData['region'] }} India Delivery Zone
        </div>
        <h1 style="font-size: clamp(1.85rem, 4vw, 3rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Shop Trending Gadgets Delivered to {{ $stateData['name'] }}
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto 1.5rem; line-height: 1.6;">
            Fast priority doorstep delivery across {{ $stateData['name'] }}, including residential and commercial addresses in all districts. All prices strictly in transparent USD ($) with full buyer protection.
        </p>
        <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap;">
            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="clock" style="width:15px;height:15px;display:inline;color:var(--brand-primary);"></i> Estimated Transit: <strong>{{ $stateData['transit_days'] }} Business Days</strong>
            </span>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="shield-check" style="width:15px;height:15px;display:inline;color:var(--brand-primary);"></i> 30-Day Money-Back Guarantee
            </span>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                <i data-lucide="dollar-sign" style="width:15px;height:15px;display:inline;color:var(--brand-primary);"></i> Billed in USD ($)
            </span>
        </div>
    </div>

    <!-- Cities Grid for this State -->
    @if(count($cities) > 0)
        <div style="margin-bottom: 3.5rem;">
            <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
                Select Your City in {{ $stateData['name'] }} for Localized Delivery Times
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr)); gap: 1rem;">
                @foreach($cities as $city)
                    <a href="{{ route('seo.india_city', [$stateData['slug'], $city['slug']]) }}" 
                       style="display: block; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); text-decoration: none; color: inherit; transition: all 0.2s ease;">
                        <span style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary); display: block; margin-bottom: 0.35rem;">
                            {{ $city['name'] }}
                        </span>
                        <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 0.5rem; line-height: 1.4;">
                            {{ $city['focus'] }}
                        </p>
                        <span style="font-size: 0.75rem; color: #10B981; font-weight: 500;">
                            ✓ Doorstep Delivery: {{ $city['transit_days'] }} Days
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Popular Products Delivering to this State (Strictly USD Pricing) -->
    <div style="margin-bottom: 3.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 8px;">
            <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary);">
                Popular Gadgets Delivering to {{ $stateData['name'] }}
            </h2>
            <a href="{{ route('store.shop') }}" style="font-size: 0.875rem; color: var(--brand-primary); font-weight: 600; text-decoration: none;">View All Catalog (USD) →</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr)); gap: 1.25rem;">
            @foreach($popularProducts as $product)
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
                                <span style="font-size: 0.75rem; color: #10B981; font-weight: 500;">✓ Ships to {{ $stateData['code'] }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- All Other Indian States Navigation -->
    <div style="border-top: 1px solid var(--border-color); padding-top: 2.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
            Explore Other States Across India
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            @foreach($allStates as $sSlug => $sData)
                @if($sSlug !== $stateData['slug'])
                    <a href="{{ route('seo.india_state', $sSlug) }}" 
                       style="padding: 0.35rem 0.75rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">
                        {{ $sData['name'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

</div>
@endsection
