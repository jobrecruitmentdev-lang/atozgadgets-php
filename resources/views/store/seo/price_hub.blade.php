@extends('layouts.store')

@section('title', "Best Tech Gadgets Under \${$budget} — Affordable Electronics & Smart Devices USA | AtoZGadgets")
@section('meta_description', "Explore top-rated smart tech gadgets, electronic accessories, and innovative home tools under \${$budget}. Fast 3-7 day US shipping, 30-day returns, and 24/7 support.")
@section('meta_keywords', "gadgets under \${$budget}, best tech under \${$budget}, affordable gadgets USA, cheap tech products, budget electronics")

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "name": "Best Tech Gadgets Under ${{ $budget }}",
      "description": "Discover high-value electronics, smart home gadgets, and tech accessories under ${{ $budget }} with fast US delivery.",
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
          "name": "Shop",
          "item": "{{ route('store.shop') }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "Gadgets Under ${{ $budget }}",
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
<div style="max-width: 1280px; margin: 0 auto; padding: 2.5rem 1.5rem;">

    <!-- Breadcrumb -->
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <a href="{{ route('store.shop') }}" style="color: inherit; text-decoration: none;">Shop</a>
        <span>/</span>
        <span style="color: var(--brand-primary); font-weight: 600;">Under ${{ $budget }}</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.12) 0%, rgba(20, 20, 28, 0.85) 100%); border: 1px solid rgba(201, 169, 98, 0.3); border-radius: var(--radius-lg); padding: 2.5rem 2rem; margin-bottom: 3rem; text-align: center; position: relative; overflow: hidden;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            Budget Value Collection
        </div>
        <h1 style="font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Best Tech Gadgets Under ${{ $budget }}
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto 1.75rem; line-height: 1.6;">
            High performance without high prices. Handpicked electronics, smart home accessories, and viral tech essentials engineered for everyday convenience.
        </p>

        <!-- Budget Quick Filter Badges -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem;">
            @foreach([10, 20, 50, 100] as $b)
                <a href="{{ route('seo.price_hub', $b) }}" 
                   style="padding: 0.5rem 1.25rem; border-radius: var(--radius-full); font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s; {{ $b === $budget ? 'background: var(--brand-primary); color: #000; box-shadow: 0 4px 12px rgba(201, 169, 98, 0.3);' : 'background: rgba(255,255,255,0.05); color: var(--text-secondary); border: 1px solid rgba(255,255,255,0.1);' }}">
                    Under ${{ $b }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Product Grid -->
    @if($products->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
            @foreach($products as $product)
                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                    <a href="{{ route('store.product', $product->slug) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div style="position: relative; padding-top: 100%; background: #0a0a0f; overflow: hidden;">
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; padding: 1rem;">
                            <div style="position: absolute; top: 0.75rem; left: 0.75rem; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); color: var(--brand-primary); padding: 0.25rem 0.6rem; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600;">
                                Under ${{ $budget }}
                            </div>
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
            <p style="font-size: 1.125rem; color: var(--text-secondary); margin-bottom: 1.5rem;">New items are being updated for this budget tier.</p>
            <a href="{{ route('store.shop') }}" style="display: inline-block; padding: 0.75rem 1.75rem; background: var(--brand-primary); color: #000; font-weight: 600; border-radius: var(--radius-md); text-decoration: none;">
                Browse All Products
            </a>
        </div>
    @endif

    <!-- FAQ Section -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem; margin-bottom: 3.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;">
            Frequently Asked Questions — Gadgets Under ${{ $budget }}
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

    <!-- Related Collections Hub Links -->
    <div style="border-top: 1px solid var(--border-color); padding-top: 2.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
            Explore More Collections & US Delivery Guides
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('seo.usa_national') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">USA Fast Delivery Hub</a>
            <a href="{{ route('seo.gifts_index') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Tech Gifts Guide</a>
            <a href="{{ route('seo.use_case', 'travel-gadgets') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Travel Gadgets</a>
            <a href="{{ route('seo.use_case', 'car-gadgets') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Car Accessories</a>
            <a href="{{ route('seo.guides_index') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Buying Guides</a>
            <a href="{{ route('seo.faq_master') }}" style="padding: 0.4rem 0.9rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--text-secondary); text-decoration: none;">Master FAQ</a>
        </div>
    </div>

</div>
@endsection
