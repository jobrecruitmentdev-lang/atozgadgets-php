@extends('layouts.store')

@section('title', 'Frequently Asked Questions (FAQ) — AtoZGadgets Help & Customer Care')
@section('meta_description', 'Find answers to common questions about US delivery, payment methods, returns, 30-day money-back guarantee, and product compatibility at AtoZGadgets.')
@section('meta_keywords', 'AtoZGadgets FAQ, shipping FAQ, returns FAQ, tech gadget support, customer care USA')

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url()->current() }}#webpage",
      "name": "Frequently Asked Questions — AtoZGadgets",
      "description": "Comprehensive customer help and technical FAQs for AtoZGadgets.",
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
          "name": "FAQ",
          "item": "{{ url()->current() }}"
        }
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        @php
          $allFlatFaqs = [];
          foreach($faqCategories as $catFaqs) {
            foreach($catFaqs as $f) {
              $allFlatFaqs[] = $f;
            }
          }
        @endphp
        @foreach($allFlatFaqs as $index => $faq)
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
<div style="max-width: 1000px; margin: 0 auto; padding: 2.5rem 1.5rem;">

    <!-- Breadcrumb -->
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <span style="color: var(--brand-primary); font-weight: 600;">FAQ & Help Center</span>
    </nav>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, rgba(201, 169, 98, 0.15) 0%, rgba(20, 20, 28, 0.9) 100%); border: 1px solid rgba(201, 169, 98, 0.35); border-radius: var(--radius-lg); padding: 3rem 2rem; margin-bottom: 3.5rem; text-align: center;">
        <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(201, 169, 98, 0.2); border: 1px solid var(--brand-primary); border-radius: var(--radius-full); color: var(--brand-primary); font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            Customer Knowledge Base
        </div>
        <h1 style="font-size: clamp(1.85rem, 4vw, 3rem); font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.2;">
            Frequently Asked Questions
        </h1>
        <p style="font-size: 1.0625rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto 1.5rem; line-height: 1.6;">
            Everything you need to know about our fast US shipping, 30-day money-back guarantee, secure checkout, and product compatibility.
        </p>

        <!-- Search Input Filter -->
        <div style="max-width: 500px; margin: 0 auto; position: relative;">
            <input type="text" id="faq-search-input" placeholder="Search questions (e.g. shipping, returns, PayPal)..." 
                   style="width: 100%; padding: 0.85rem 1.25rem; background: rgba(0,0,0,0.5); border: 1px solid rgba(201, 169, 98, 0.4); border-radius: var(--radius-full); color: #fff; font-size: 0.9375rem; outline: none;"
                   onkeyup="filterFaqs(this.value)">
        </div>
    </div>

    <!-- FAQ Categories & Accordions -->
    <div id="faq-container">
        @foreach($faqCategories as $categoryTitle => $faqs)
            <div class="faq-category-block" style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 1.25rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                    {{ $categoryTitle }}
                </h2>
                <div style="display: grid; gap: 1rem;">
                    @foreach($faqs as $faq)
                        <div class="faq-item" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; transition: border-color 0.2s;">
                            <h3 class="faq-question" style="font-size: 1.05rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; cursor: pointer;">
                                {{ $faq['q'] }}
                            </h3>
                            <p class="faq-answer" style="font-size: 0.9375rem; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                {{ $faq['a'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Contact Support Card -->
    <div style="background: var(--bg-surface); border: 1px dashed var(--brand-primary); border-radius: var(--radius-lg); padding: 2.5rem; text-align: center; margin-top: 4rem;">
        <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Still Have Questions?</h3>
        <p style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 1.25rem;">Our customer care team is available 24/7 to help you with order inquiries and tracking.</p>
        <a href="{{ route('store.contact') }}" style="display: inline-block; padding: 0.75rem 2rem; background: var(--brand-primary); color: #000; font-weight: 600; border-radius: var(--radius-md); text-decoration: none;">
            Contact Support
        </a>
    </div>

</div>

<script>
function filterFaqs(query) {
    query = query.toLowerCase().trim();
    const categories = document.querySelectorAll('.faq-category-block');

    categories.forEach(cat => {
        let hasVisibleFaq = false;
        const items = cat.querySelectorAll('.faq-item');
        
        items.forEach(item => {
            const q = item.querySelector('.faq-question').innerText.toLowerCase();
            const a = item.querySelector('.faq-answer').innerText.toLowerCase();
            
            if (q.includes(query) || a.includes(query)) {
                item.style.display = 'block';
                hasVisibleFaq = true;
            } else {
                item.style.display = 'none';
            }
        });

        cat.style.display = hasVisibleFaq ? 'block' : 'none';
    });
}
</script>
@endsection
