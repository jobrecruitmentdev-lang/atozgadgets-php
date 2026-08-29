@extends('layouts.store')

@section('title', 'Tech & Gadget Buying Guides 2026 — Expert Reviews & Tips | AtoZGadgets')
@section('meta_description', 'Read comprehensive buying guides and reviews on smart home automation, car gadgets, work-from-home desk setups, travel accessories, and tech gifts.')
@section('meta_keywords', 'tech buying guides, gadget reviews 2026, best smart home guides, car gadget tips, tech gifts guide')

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "CollectionPage",
      "@id": "{{ url()->current() }}#collection",
      "name": "Tech & Gadget Buying Guides",
      "description": "Expert advice, product comparisons, and buying guides for smart electronics and viral gadgets.",
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
          "name": "Buying Guides",
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
        <span style="color: var(--brand-primary); font-weight: 600;">Buying Guides</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.15) 0%, rgba(20, 20, 28, 0.9) 100%); border: 1px solid rgba(201, 169, 98, 0.35); border-radius: var(--radius-lg); padding: 3rem 2rem; margin-bottom: 3.5rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            AtoZGadgets Knowledge & Tech Lab
        </div>
        <h1 style="font-size: clamp(1.85rem, 4vw, 3rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Tech & Gadget Buying Guides
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto; line-height: 1.6;">
            Expert advice, comprehensive product breakdowns, and practical setup tips to help you choose the best electronics, smart home tech, and everyday tools.
        </p>
    </div>

    <!-- Guides Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.75rem; margin-bottom: 4rem;">
        @foreach($guides as $guide)
            <article style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                <a href="{{ route('seo.guide_detail', $guide['slug']) }}" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%;">
                    <div style="padding: 1.75rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span style="font-size: 0.75rem; color: var(--brand-primary); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                {{ $guide['category'] }}
                            </span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                ⏱️ {{ $guide['reading_time'] }}
                            </span>
                        </div>
                        <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem; line-height: 1.4;">
                            {{ $guide['title'] }}
                        </h2>
                        <p style="font-size: 0.9375rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.25rem;">
                            {{ $guide['summary'] }}
                        </p>
                        <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem;">
                            <span style="color: var(--text-muted);">By {{ $guide['author'] }}</span>
                            <span style="color: var(--brand-primary); font-weight: 600;">Read Guide →</span>
                        </div>
                    </div>
                </a>
            </article>
        @endforeach
    </div>

</div>
@endsection
