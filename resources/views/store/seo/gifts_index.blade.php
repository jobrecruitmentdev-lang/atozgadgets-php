@extends('layouts.store')

@section('title', 'Best Tech & Gadget Gifts 2026 — Cool Electronic Gift Ideas USA | AtoZGadgets')
@section('meta_description', 'Discover trending tech gifts for gamers, tech lovers, friends, and family. Shop gadgets under $50 and $100 with free fast US shipping and 30-day returns.')
@section('meta_keywords', 'tech gifts, gadget gifts, best tech gifts 2026, gifts for gamers, gifts for tech lovers, unique electronic gifts')

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "name": "Best Tech & Gadget Gifts 2026",
      "description": "Curated collection of unique electronics and cool gadget gifts for birthdays, holidays, and celebrations.",
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
          "name": "Gifts Guide",
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
        <span style="color: var(--brand-primary); font-weight: 600;">Tech Gifts</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.15) 0%, rgba(20, 20, 28, 0.9) 100%); border: 1px solid rgba(201, 169, 98, 0.35); border-radius: var(--radius-lg); padding: 3rem 2rem; margin-bottom: 3.5rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            2026 Curated Gift Guide
        </div>
        <h1 style="font-size: clamp(1.85rem, 4vw, 3rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            The Ultimate Tech & Gadget Gift Guide
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto 2rem; line-height: 1.6;">
            Find the perfect gift for tech lovers, gamers, commuters, and smart home enthusiasts. Delivered across the USA with 30-day money-back guarantee.
        </p>

        <!-- Recipient Quick Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; max-width: 960px; margin: 0 auto;">
            <a href="{{ route('seo.gift_category', 'for-gamers') }}" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); padding: 1.25rem; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🎮</div>
                <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">For Gamers</div>
                <div style="font-size: 0.8125rem; color: var(--text-secondary);">RGB gear, audio & desk upgrades</div>
            </a>
            <a href="{{ route('seo.gift_category', 'for-tech-lovers') }}" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); padding: 1.25rem; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">⚡</div>
                <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">For Tech Lovers</div>
                <div style="font-size: 0.8125rem; color: var(--text-secondary);">Innovative viral gadgets & tools</div>
            </a>
            <a href="{{ route('seo.gift_category', 'under-50') }}" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); padding: 1.25rem; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🎁</div>
                <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Gifts Under $50</div>
                <div style="font-size: 0.8125rem; color: var(--text-secondary);">High value, budget-friendly tech</div>
            </a>
            <a href="{{ route('seo.gift_category', 'under-100') }}" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); padding: 1.25rem; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">✨</div>
                <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Gifts Under $100</div>
                <div style="font-size: 0.8125rem; color: var(--text-secondary);">Premium devices & smart hubs</div>
            </a>
        </div>
    </div>

    <!-- Featured Gifts Grid -->
    <div style="margin-bottom: 4rem;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">Top Trending Gift Ideas</h2>
            <a href="{{ route('store.shop') }}" style="font-size: 0.875rem; color: var(--brand-primary); font-weight: 600; text-decoration: none;">View All Catalog →</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
            @foreach($featuredGifts as $product)
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

</div>
@endsection
