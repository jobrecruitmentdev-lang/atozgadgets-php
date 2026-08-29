@extends('layouts.store')

@section('title', "{$meta['title']} — Trending Devices & Accessories USA | AtoZGadgets")
@section('meta_description', "{$meta['description']} Fast 3-7 day US shipping, 30-day returns, and verified customer reviews.")
@section('meta_keywords', "{$slug}, buy {$slug} online, trending gadgets USA, smart devices, everyday electronics")

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "name": "{{ addslashes($meta['title']) }}",
      "description": "{{ addslashes($meta['description']) }}",
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
          "name": "Use Cases",
          "item": "{{ route('store.shop') }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ addslashes($meta['title']) }}",
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
        <a href="{{ route('store.shop') }}" style="color: inherit; text-decoration: none;">Shop</a>
        <span>/</span>
        <span style="color: var(--brand-primary); font-weight: 600;">{{ $meta['title'] }}</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.12) 0%, rgba(20, 20, 28, 0.85) 100%); border: 1px solid rgba(201, 169, 98, 0.3); border-radius: var(--radius-lg); padding: 2.5rem 2rem; margin-bottom: 3rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            {{ $meta['tagline'] }}
        </div>
        <h1 style="font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            {{ $meta['title'] }}
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto; line-height: 1.6;">
            {{ $meta['description'] }}
        </p>
    </div>

    <!-- Product Grid -->
    @if($products->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
            @foreach($products as $product)
                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                    <a href="{{ route('store.product', $product->slug) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div style="position: relative; padding-top: 100%; background: #0a0a0f; overflow: hidden;">
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; padding: 1rem;">
                        </div>
                        <div style="padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <div style="font-size: 0.75rem; color: var(--brand-primary); font-weight: 600; text-transform: uppercase; margin-bottom: 0.35rem;">
                                {{ $product->category->name ?? 'Gadget' }}
                            </div>
                            <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.7rem;">
                                {{ $product->name }}
                            </h3>
                            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: auto; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                                <span style="font-size: 1.25rem; font-weight: 700; color: var(--brand-primary);">
                                    ${{ number_format($product->price, 2) }}
                                </span>
                                <span style="font-size: 0.75rem; color: #10B981; font-weight: 500;">
                                    ✓ Free US Shipping
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-bottom: 4rem;">
            {{ $products->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 4rem 2rem; background: var(--bg-surface); border-radius: var(--radius-md); border: 1px dashed var(--border-color); margin-bottom: 4rem;">
            <p style="font-size: 1.125rem; color: var(--text-secondary); margin-bottom: 1.5rem;">New items are currently being curated for this collection.</p>
            <a href="{{ route('store.shop') }}" style="display: inline-block; padding: 0.75rem 1.75rem; background: var(--brand-primary); color: #000; font-weight: 600; border-radius: var(--radius-md); text-decoration: none;">
                Browse All Products
            </a>
        </div>
    @endif

    <!-- Related Use Cases -->
    <div style="border-top: 1px solid var(--border-color); padding-top: 2.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
            More Lifestyle & Everyday Tech Hubs
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('seo.use_case', 'travel-gadgets') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Travel Gadgets</a>
            <a href="{{ route('seo.use_case', 'home-office-gadgets') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Work-From-Home Desk Tech</a>
            <a href="{{ route('seo.use_case', 'car-gadgets') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Car Accessories</a>
            <a href="{{ route('seo.use_case', 'fitness-gadgets') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Fitness & Wellness Tech</a>
            <a href="{{ route('seo.use_case', 'kitchen-gadgets') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Kitchen Utilities</a>
            <a href="{{ route('seo.price_hub', 50) }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Gadgets Under $50</a>
        </div>
    </div>

</div>
@endsection
