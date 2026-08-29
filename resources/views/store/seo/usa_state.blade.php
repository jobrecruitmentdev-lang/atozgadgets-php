@extends('layouts.store')

@section('title', "Shop Gadgets Online {$stateData['name']} — Fast Delivery to {$stateData['code']} | AtoZGadgets")
@section('meta_description', "Buy trending gadgets, smart home devices, and electronics delivered across {$stateData['name']} in {$stateData['transit_days']} business days. 30-day money-back guarantee.")
@section('meta_keywords', "gadgets {$stateData['name']}, electronics {$stateData['name']}, buy gadgets {$stateData['name']}, smart home {$stateData['code']}, gadgets online {$stateData['name']}")

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "name": "Shop Gadgets Online in {{ addslashes($stateData['name']) }}",
      "description": "Fast USPS Priority and UPS Express gadget delivery across {{ addslashes($stateData['name']) }} ({{ $stateData['code'] }}).",
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
          "name": "USA Hub",
          "item": "{{ route('seo.usa_national') }}"
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
<div style="max-width: 1280px; margin: 0 auto; padding: 2.5rem 1.5rem;">

    <!-- Breadcrumb -->
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <a href="{{ route('seo.usa_national') }}" style="color: inherit; text-decoration: none;">USA Hub</a>
        <span>/</span>
        <span style="color: var(--brand-primary); font-weight: 600;">{{ $stateData['name'] }}</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.12) 0%, rgba(20, 20, 28, 0.85) 100%); border: 1px solid rgba(201, 169, 98, 0.3); border-radius: var(--radius-lg); padding: 3rem 2rem; margin-bottom: 3.5rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            {{ $stateData['code'] }} • {{ $stateData['region'] }} US Delivery Zone
        </div>
        <h1 style="font-size: clamp(1.85rem, 4vw, 3rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Shop Trending Gadgets Delivered to {{ $stateData['name'] }}
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto 1.5rem; line-height: 1.6;">
            AtoZGadgets provides expedited shipping across {{ $stateData['name'] }}, with standard delivery in <strong>{{ $stateData['transit_days'] }} business days</strong> to residential and commercial addresses.
        </p>
    </div>

    <!-- Major Cities in State -->
    @if(count($cities) > 0)
        <div style="margin-bottom: 4rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem;">
                Major City Delivery Zones in {{ $stateData['name'] }}
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem;">
                @foreach($cities as $city)
                    <a href="{{ route('seo.usa_city', [$stateData['slug'], $city['slug']]) }}" 
                       style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; text-decoration: none; color: inherit; transition: all 0.2s;"
                       onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-3px)'"
                       onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                        <div style="font-weight: 700; color: var(--text-primary); font-size: 1.05rem; margin-bottom: 0.25rem;">
                            {{ $city['name'] }}
                        </div>
                        <div style="font-size: 0.8125rem; color: var(--brand-primary); margin-bottom: 0.35rem;">
                            {{ $city['focus'] }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            Transit: <strong>{{ $city['transit_days'] }} business days</strong>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Popular Products in State -->
    <div style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem;">
            Popular Gadgets Shipped to {{ $stateData['name'] }}
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
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

    <!-- All Other States Directory -->
    <div style="border-top: 1px solid var(--border-color); padding-top: 2.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
            Explore Other US State Delivery Hubs
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            @foreach($allStates as $sSlug => $s)
                @if($sSlug !== $stateData['slug'])
                    <a href="{{ route('seo.usa_state', $sSlug) }}" style="padding: 0.35rem 0.75rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">
                        {{ $s['name'] }} ({{ $s['code'] }})
                    </a>
                @endif
            @endforeach
        </div>
    </div>

</div>
@endsection
