@extends('layouts.store')

@section('title', "{$guide['title']} | AtoZGadgets")
@section('meta_description', "{$guide['summary']} Expert buying advice, feature comparisons, and top recommendations.")
@section('meta_keywords', "{$guide['category']}, {$guide['slug']}, tech buying guide, gadget review 2026, best electronics USA")

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "@id": "{{ url()->current() }}#article",
      "headline": "{{ addslashes($guide['title']) }}",
      "description": "{{ addslashes($guide['summary']) }}",
      "image": "{{ $guide['image'] }}",
      "datePublished": "{{ $guide['published_at'] }}T08:00:00+00:00",
      "dateModified": "{{ $guide['updated_at'] }}T12:00:00+00:00",
      "author": {
        "@type": "Organization",
        "name": "{{ addslashes($guide['author']) }}",
        "url": "{{ url('/') }}"
      },
      "publisher": {
        "@type": "Organization",
        "name": "AtoZGadgets",
        "logo": {
          "@type": "ImageObject",
          "url": "https://atozgadgetz.com/brand/atoz-logo.png"
        }
      },
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ url()->current() }}"
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
          "name": "Guides",
          "item": "{{ route('seo.guides_index') }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ addslashes($guide['title']) }}",
          "item": "{{ url()->current() }}"
        }
      ]
    }
    @if(!empty($guide['faqs'])),
    {
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($guide['faqs'] as $index => $faq)
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
    @endif
  ]
}
</script>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 2.5rem 1.5rem;">

    <!-- Breadcrumb -->
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <a href="{{ route('seo.guides_index') }}" style="color: inherit; text-decoration: none;">Guides</a>
        <span>/</span>
        <span style="color: var(--brand-primary); font-weight: 600;">{{ $guide['category'] }}</span>
    </nav>

    <!-- Article Header -->
    <header style="margin-bottom: 2.5rem;">
        <div style="display: inline-block; padding: 0.35rem 0.85rem; background: rgba(201, 169, 98, 0.15); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            {{ $guide['category'] }}
        </div>
        <h1 style="font-size: clamp(2rem, 4vw, 2.75rem); font-weight: 800; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.25;">
            {{ $guide['title'] }}
        </h1>
        <p style="font-size: 1.125rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
            {{ $guide['summary'] }}
        </p>
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1.25rem; font-size: 0.8125rem; color: var(--text-muted); padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <span>✍️ <strong>{{ $guide['author'] }}</strong></span>
            <span>📅 Updated: {{ date('F j, Y', strtotime($guide['updated_at'])) }}</span>
            <span>⏱️ {{ $guide['reading_time'] }}</span>
        </div>
    </header>

    <!-- Article Body Sections -->
    <main style="margin-bottom: 3.5rem;">
        @foreach($guide['content_sections'] as $section)
            <section style="margin-bottom: 2.5rem;">
                <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.85rem; line-height: 1.3;">
                    {{ $section['heading'] }}
                </h2>
                <div style="font-size: 1.05rem; color: var(--text-secondary); line-height: 1.7;">
                    {!! $section['body'] !!}
                </div>
            </section>
        @endforeach
    </main>

    <!-- Recommended Products from Store -->
    @if(isset($recommendedProducts) && $recommendedProducts->count() > 0)
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 3.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
                Featured Gadgets in This Category
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;">
                @foreach($recommendedProducts as $product)
                    <a href="{{ route('store.product', $product->slug) }}" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; text-decoration: none; color: inherit; display: block; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
                        <div style="position: relative; padding-top: 80%; background: #0a0a0f; border-radius: var(--radius-sm); overflow: hidden; margin-bottom: 0.75rem;">
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; padding: 0.5rem;">
                        </div>
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.35rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.3rem;">
                            {{ $product->name }}
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: var(--brand-primary);">${{ number_format($product->price, 2) }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Guide FAQs -->
    @if(!empty($guide['faqs']))
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 3.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
                Questions Answered in This Guide
            </h3>
            <div style="display: grid; gap: 1rem;">
                @foreach($guide['faqs'] as $faq)
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: var(--radius-md); padding: 1.25rem;">
                        <h4 style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem;">{{ $faq['q'] }}</h4>
                        <p style="font-size: 0.9375rem; color: var(--text-secondary); line-height: 1.6; margin: 0;">{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Related Guides -->
    @if(!empty($relatedGuides))
        <div style="border-top: 1px solid var(--border-color); padding-top: 2.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
                More Buying Guides
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                @foreach($relatedGuides as $rel)
                    <a href="{{ route('seo.guide_detail', $rel['slug']) }}" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; text-decoration: none; color: inherit; display: block; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--brand-primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
                        <span style="font-size: 0.75rem; color: var(--brand-primary); font-weight: 600; text-transform: uppercase;">{{ $rel['category'] }}</span>
                        <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin: 0.35rem 0 0.5rem; line-height: 1.3;">{{ $rel['title'] }}</h4>
                        <span style="font-size: 0.75rem; color: var(--brand-primary); font-weight: 600;">Read →</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
