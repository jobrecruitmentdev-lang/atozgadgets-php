@extends('layouts.store')

@section('title', ($product->name ?? 'Product') . ' - AtoZGadgets')

@section('meta_description', Str::limit(strip_tags($product->description ?? $product->name), 155))
@section('meta_keywords', addslashes($product->name) . ', buy online, USA fast shipping, premium gadgets, smart electronics')
@section('og_type', 'product')
@section('og_title', ($product->name ?? 'Product') . ' - AtoZGadgets')
@section('og_description', Str::limit(strip_tags($product->description ?? $product->name), 155))
@section('og_image', $product->customer_thumbnail)
@section('canonical', url()->current())

@section('meta')
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@graph": [
        {
          "@type": "Product",
          "@id": "{{ url()->current() }}#product",
          "name": "{{ addslashes($product->name) }}",
          "image": [
            "{{ $product->customer_thumbnail }}"
          ],
          "description": "{{ addslashes(Str::limit(strip_tags($product->description ?? $product->name), 250)) }}",
          "sku": "{{ $product->merchant_sku }}",
          "brand": {
            "@type": "Brand",
            "name": "{{ addslashes($product->brand->name ?? 'AtoZGadgets') }}"
          },
          "offers": {
            "@type": "Offer",
            "url": "{{ url()->current() }}",
            "priceCurrency": "USD",
            "price": "{{ number_format($product->effective_price, 2, '.', '') }}",
            "priceValidUntil": "{{ date('Y-12-31', strtotime('+1 year')) }}",
            "itemCondition": "https://schema.org/NewCondition",
            "availability": "{{ ($product->stock_quantity ?? 0) > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "seller": {
              "@type": "Organization",
              "name": "AtoZGadgets"
            },
            "shippingDetails": {
              "@type": "OfferShippingDetails",
              "shippingRate": {
                "@type": "MonetaryAmount",
                "value": "0.00",
                "currency": "USD"
              },
              "shippingDestination": {
                "@type": "DefinedRegion",
                "addressCountry": "US"
              },
              "deliveryTime": {
                "@type": "ShippingDeliveryTime",
                "handlingTime": {
                  "@type": "QuantitativeValue",
                  "minValue": 0,
                  "maxValue": 1,
                  "unitCode": "DAY"
                },
                "transitTime": {
                  "@type": "QuantitativeValue",
                  "minValue": 3,
                  "maxValue": 7,
                  "unitCode": "DAY"
                }
              }
            },
            "hasMerchantReturnPolicy": {
              "@type": "MerchantReturnPolicy",
              "applicableCountry": "US",
              "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
              "merchantReturnDays": 30,
              "returnMethod": "https://schema.org/ReturnByMail",
              "returnFees": "https://schema.org/FreeReturn"
            }
          }
          @if($product->review_count > 0)
          ,"aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $product->average_rating }}",
            "reviewCount": "{{ $product->review_count }}",
            "bestRating": "5",
            "worstRating": "1"
          }
          @endif
        },
        {
          "@type": "BreadcrumbList",
          "@id": "{{ url()->current() }}#breadcrumb",
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
              "name": "{{ $product->category->name ?? 'Shop' }}",
              "item": "{{ $product->category ? route('store.shop', ['category' => $product->category->slug]) : route('store.shop') }}"
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": "{{ addslashes($product->name) }}",
              "item": "{{ url()->current() }}"
            }
          ]
        }
      ]
    }
    </script>
@endsection

@section('content')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; flex-wrap: wrap; }
    .breadcrumb a { transition: color 0.3s; color: var(--text-secondary); text-decoration: none; }
    .breadcrumb a:hover { color: var(--accent); }

    .product-layout { display: grid; grid-template-columns: 1.1fr 1fr; gap: 50px; align-items: start; }
    @media (max-width: 900px) { .product-layout { grid-template-columns: 1fr; gap: 32px; } }
    
    /* Gallery */
    .gallery-container { position: sticky; top: 110px; display: flex; flex-direction: column; gap: 16px; }
    .main-image-wrap { width: 100%; aspect-ratio: 1/1; border-radius: 20px; border: 1px solid var(--glass-border); background: #111; overflow: hidden; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
    .main-image { width: 100%; height: 100%; object-fit: contain; transition: transform 0.4s ease; }
    .main-image:hover { transform: scale(1.03); }
    
    .thumbnails-strip { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 8px; scrollbar-width: thin; }
    .thumb-btn { width: 72px; height: 72px; border-radius: 12px; border: 2px solid var(--glass-border); background: #141414; padding: 4px; cursor: pointer; flex-shrink: 0; transition: all 0.2s; }
    .thumb-btn.active, .thumb-btn:hover { border-color: var(--accent); transform: translateY(-2px); }
    .thumb-btn img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }

    /* Product Info */
    .product-info h1 { font-size: clamp(26px, 3.5vw, 36px); font-weight: 800; line-height: 1.2; letter-spacing: -0.5px; margin-bottom: 12px; color: var(--text-primary); }
    
    .rating-summary { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 14px; }
    .stars { color: #f59e0b; display: inline-flex; gap: 2px; }
    .review-count-badge { color: var(--text-secondary); text-decoration: underline; cursor: pointer; }

    .price-wrap { display: flex; align-items: baseline; gap: 14px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border); flex-wrap: wrap; }
    .price-tag { font-size: 38px; font-weight: 800; color: var(--accent); }
    .old-price { font-size: 20px; text-decoration: line-through; color: var(--text-secondary); }
    .save-badge { background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 13px; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(16, 185, 129, 0.3); }

    /* Stock & SKU */
    .meta-pills { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .meta-pill { font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 8px; background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); color: var(--text-secondary); display: inline-flex; align-items: center; gap: 6px; }
    .pill-instock { color: #10b981; border-color: rgba(16, 185, 129, 0.3); }
    .pill-lowstock { color: #f59e0b; border-color: rgba(245, 158, 11, 0.3); }
    .pill-confirming { color: #3b82f6; border-color: rgba(59, 130, 246, 0.3); }
    .pill-outofstock { color: #ef4444; border-color: rgba(239, 68, 68, 0.3); }

    /* Variants */
    .variant-section { margin-bottom: 28px; }
    .variant-label { font-size: 14px; font-weight: 700; margin-bottom: 10px; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.5px; }
    .variant-options { display: flex; gap: 10px; flex-wrap: wrap; }
    .variant-option { padding: 10px 16px; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.03); color: var(--text-primary); cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; }
    .variant-option.active, .variant-option:hover { border-color: var(--accent); background: rgba(201, 169, 98, 0.15); color: var(--accent); }

    /* Actions */
    .action-row { display: flex; gap: 14px; margin-bottom: 30px; }
    .qty-picker { display: flex; align-items: center; border: 1px solid var(--glass-border); border-radius: 12px; background: rgba(255,255,255,0.04); }
    .qty-btn { width: 44px; height: 52px; background: transparent; border: none; color: var(--text-primary); font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .qty-input { width: 48px; text-align: center; background: transparent; border: none; color: var(--text-primary); font-weight: 700; font-size: 16px; }
    .btn-cart { flex: 1; padding: 16px 24px; font-size: 16px; font-weight: 700; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-cart:disabled { opacity: 0.5; cursor: not-allowed; }

    /* Trust Strip */
    .trust-badges-box { background: rgba(255,255,255,0.02); padding: 20px; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 36px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .tb-item { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-secondary); min-width: 0; word-break: break-word; }
    .tb-item i { color: var(--accent); width: 18px; height: 18px; flex-shrink: 0; }

    @media (max-width: 480px) {
        .trust-badges-box { grid-template-columns: 1fr; gap: 12px; padding: 16px; }
        .action-row { flex-direction: column; gap: 12px; }
        .qty-picker { width: 100%; justify-content: center; }
        .qty-btn { width: 50px; }
        .variant-option { padding: 8px 12px; font-size: 13px; }
    }

    /* Payment Gateways Strip */
    .payment-methods-strip { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding: 12px 16px; border-radius: 10px; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); font-size: 13px; color: var(--text-secondary); flex-wrap: wrap; }
    .pm-pill { background: rgba(255,255,255,0.06); padding: 4px 10px; border-radius: 6px; font-weight: 600; color: var(--text-primary); font-size: 12px; }

    /* Product Tabs */
    .tabs-section { margin-top: 50px; border-top: 1px solid var(--glass-border); padding-top: 36px; }
    .tabs-header { display: flex; gap: 24px; border-bottom: 1px solid var(--glass-border); margin-bottom: 24px; overflow-x: auto; }
    .tab-btn { padding: 12px 0; font-size: 16px; font-weight: 700; color: var(--text-secondary); background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; transition: all 0.3s; white-space: nowrap; }
    .tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }
    .tab-pane { display: none; color: rgba(255,255,255,0.85); line-height: 1.8; font-size: 15px; }
    .tab-pane.active { display: block; }

    /* Specifications Table */
    .specs-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    .specs-table tr { border-bottom: 1px solid rgba(255,255,255,0.06); }
    .specs-table td { padding: 12px 16px; font-size: 14px; }
    .specs-table td:first-child { width: 35%; color: var(--text-secondary); font-weight: 600; background: rgba(255,255,255,0.02); }
    .specs-table td:last-child { color: var(--text-primary); font-weight: 500; }

    /* Reviews Section */
    .reviews-container { display: flex; flex-direction: column; gap: 24px; }
    .review-card { background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 14px; padding: 20px; }
    .review-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .review-author { font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; font-size: 14px; }
    .badge-verified { background: rgba(16,185,129,0.15); color: #10b981; font-size: 11px; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
    .review-date { font-size: 12px; color: var(--text-secondary); }
    .review-title { font-weight: 700; color: var(--text-primary); margin-bottom: 6px; font-size: 15px; }
    .review-body { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }

    .review-form-box { background: rgba(20,20,20,0.6); border: 1px solid var(--glass-border); border-radius: 16px; padding: 24px; margin-top: 30px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary); }
    .form-control { width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--glass-border); background: #0a0a0a; color: var(--text-primary); font-size: 14px; outline: none; }
    .form-control:focus { border-color: var(--accent); }

    /* Interactive 5-Star Rating Widget */
    .rating-row-container { display: flex; align-items: center; gap: 16px; margin-bottom: 8px; flex-wrap: wrap; }
    .star-rating-widget { display: inline-flex !important; flex-direction: row-reverse !important; gap: 6px; font-size: 28px; line-height: 1; user-select: none; }
    .star-rating-widget input[type="radio"] { display: none !important; }
    .star-rating-widget label { display: inline-block !important; color: #4b5563; cursor: pointer; transition: color 0.15s ease, transform 0.15s ease; margin: 0 !important; padding: 2px; }
    .star-rating-widget label:hover,
    .star-rating-widget label:hover ~ label,
    .star-rating-widget input[type="radio"]:checked ~ label { color: #f59e0b !important; }
    .star-rating-widget label:hover { transform: scale(1.2); }
    .rating-score-hint { font-size: 13px; font-weight: 600; color: #f59e0b; background: rgba(245, 158, 11, 0.1); padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(245, 158, 11, 0.2); }
</style>

<div class="breadcrumb" data-aos="fade-right">
    <a href="{{ route('store.home') }}">Home</a> <i data-lucide="chevron-right" style="width:14px;"></i>
    <a href="{{ route('store.shop') }}">Products</a> <i data-lucide="chevron-right" style="width:14px;"></i>
    
    @if(isset($product) && $product->category)
        @php
            $cat = $product->category;
            $hierarchy = [];
            while($cat) {
                array_unshift($hierarchy, $cat);
                $cat = $cat->parent;
            }
        @endphp
        @foreach($hierarchy as $h)
            <a href="{{ route('store.shop', ['category' => $h->slug]) }}">{{ $h->name }}</a> <i data-lucide="chevron-right" style="width:14px;"></i>
        @endforeach
    @endif
    
    <span style="color: var(--text-primary); font-weight: 500;" title="{{ $product->name }}">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</span>
</div>

<div class="product-layout">
    <!-- Pillar 1: Media Gallery -->
    <div class="gallery-container" data-aos="fade-up">
        <div class="main-image-wrap">
            <img id="mainProductImg" fetchpriority="high" decoding="sync" src="{{ $product->customer_thumbnail }}" alt="{{ $product->name }}" class="main-image">
        </div>
        
        @if($product->media && $product->media->count() > 0)
            <div class="thumbnails-strip">
                <button type="button" class="thumb-btn active" onclick="switchMainImage('{{ $product->customer_thumbnail }}', this)">
                    <img src="{{ $product->customer_thumbnail }}" alt="{{ $product->name }} - Main View">
                </button>
                @foreach($product->media as $idx => $m)
                    @if($m->public_url !== $product->customer_thumbnail && $m->url !== $product->thumbnail_image)
                        <button type="button" class="thumb-btn" onclick="switchMainImage('{{ $m->public_url }}', this)">
                            <img src="{{ $m->public_url }}" alt="{{ $product->name }} - View {{ $idx + 1 }}">
                        </button>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Product Info & Commercial Selection -->
    <div class="product-info" data-aos="fade-left" data-aos-delay="100">
        <h1>{{ $product->name }}</h1>
        
        <!-- Pillar 3: Authentic Review Summary -->
        <div class="rating-summary">
            <div class="stars">
                @for($i = 1; $i <= 5; $i++)
                    <i data-lucide="star" style="width:16px; height:16px; {{ ($product->review_count > 0 && $i <= round($product->average_rating)) ? 'fill: #f59e0b;' : 'opacity: 0.3;' }}"></i>
                @endfor
            </div>
            @if($product->review_count > 0)
                <span style="font-weight: 700; color: var(--text-primary);">{{ $product->average_rating }}</span>
                <span class="review-count-badge" onclick="showTab('tab-reviews')">({{ $product->review_count }} verified reviews)</span>
            @else
                <span style="color: var(--text-secondary); font-size: 13px;">(0 customer reviews)</span>
            @endif
        </div>

        <div class="price-wrap">
            <span class="price-tag" id="displayPrice">${{ number_format($product->effective_price, 2) }}</span>
            @if($product->has_active_discount)
                <span class="old-price" id="displayOldPrice">${{ number_format($product->price, 2) }}</span>
                @php
                    $discountPct = round((($product->price - $product->effective_price) / $product->price) * 100);
                @endphp
                <span class="save-badge">Save {{ $discountPct }}%</span>
            @endif
        </div>

        <!-- Pillar 4 & 6: Truthful Dynamic Stock & Merchant SKU -->
        <div class="meta-pills">
            @php $avail = $product->availability; @endphp
            <span class="meta-pill {{ $avail['badge_class'] }}">
                <i data-lucide="{{ $avail['icon'] }}" style="width:14px;height:14px;"></i> {{ $avail['label'] }}
            </span>
            <span class="meta-pill"><i data-lucide="tag" style="width:14px;height:14px;"></i> SKU: {{ $product->merchant_sku }}</span>
            @if($product->brand)
                <span class="meta-pill"><i data-lucide="award" style="width:14px;height:14px;"></i> {{ $product->brand->name }}</span>
            @endif
        </div>

        <form action="{{ route('store.cart.add') }}" method="POST" onsubmit="const b=this.querySelector('.btn-cart'); if(b){ b.disabled=true; b.innerHTML='Adding...'; }">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="variant_id" id="selectedVariantId" value="{{ $product->variants->first()->id ?? '' }}">

            <!-- Variant Selector -->
            @if($product->variants && $product->variants->count() > 0)
                <div class="variant-section">
                    <div class="variant-label">Select Option / Variant</div>
                    <div class="variant-options" role="radiogroup" aria-label="Product Options">
                        @foreach($product->variants as $idx => $variant)
                            <button type="button" 
                                    class="variant-option {{ $idx === 0 ? 'active' : '' }}" 
                                    data-variant-id="{{ $variant->id }}"
                                    data-price="{{ number_format(\App\Services\Catalog\PricingService::resolveCustomerPrice($product, $variant), 2) }}"
                                    data-image="{{ $variant->image_url ?? '' }}"
                                    onclick="selectVariant(this)">
                                {{ $variant->display_name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="action-row">
                <div class="qty-picker">
                    <button type="button" class="qty-btn" onclick="const q = document.getElementById('qty'); q.value = Math.max(1, parseInt(q.value) - 1);">-</button>
                    <input type="number" id="qty" name="quantity" value="1" min="1" max="99" class="qty-input" readonly>
                    <button type="button" class="qty-btn" onclick="const q = document.getElementById('qty'); q.value = parseInt(q.value) + 1;">+</button>
                </div>
                <button type="submit" class="btn btn-primary btn-cart" {{ !$avail['is_purchasable'] ? 'disabled' : '' }}>
                    <i data-lucide="shopping-bag" style="display:inline; width:18px; margin-right:8px; vertical-align:middle;"></i> 
                    {{ $avail['is_purchasable'] ? 'Add to Cart' : 'Out of Stock' }}
                </button>
            </div>
        </form>

        <!-- Pillar 7: Dynamic Payment Gateway Badges -->
        <div class="payment-methods-strip">
            <span style="font-weight: 600; color: var(--text-primary);"><i data-lucide="shield-check" style="width:14px;height:14px;display:inline;margin-right:4px;"></i> Accepted Payments:</span>
            @foreach($paymentMethods ?? [] as $pm)
                <span class="pm-pill">{{ $pm['badge'] }}</span>
            @endforeach
        </div>

        <!-- Pillar 5: Grounded Shipping & Trust Elements -->
        <div class="trust-badges-box">
            <div class="tb-item">
                <i data-lucide="truck"></i>
                <span><strong>Standard Delivery</strong><br>7–15 business days</span>
            </div>
            <div class="tb-item">
                <i data-lucide="shield-check"></i>
                <span><strong>{{ $trustHeadline ?? 'Secure Checkout' }}</strong><br>256-Bit SSL Encrypted</span>
            </div>
            <div class="tb-item">
                <i data-lucide="rotate-ccw"></i>
                <span><strong>7-Day Returns</strong><br>Full replacement guarantee</span>
            </div>
            <div class="tb-item">
                <i data-lucide="headphones"></i>
                <span><strong>Dedicated Support</strong><br>Online order tracking</span>
            </div>
        </div>
    </div>
</div>

<!-- 3. Rich Tabs: Description, Specifications, Shipping & Reviews -->
<div class="tabs-section" data-aos="fade-up">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="showTab('tab-desc', this)">Product Description</button>
        <button class="tab-btn" onclick="showTab('tab-specs', this)">Technical Specifications</button>
        <button class="tab-btn" onclick="showTab('tab-shipping', this)">Shipping & Guarantee</button>
        <button class="tab-btn" onclick="showTab('tab-reviews', this)">Customer Reviews ({{ $product->review_count }})</button>
    </div>

    <!-- Tab 1: Description (Pillar 2 Clean Content) -->
    <div id="tab-desc" class="tab-pane active">
        @if(!empty($product->description))
            {!! strip_tags($product->description, '<p><br><b><strong><ul><ol><li><span><em><i><div><h1><h2><h3><h4><h5><h6>') !!}
        @else
            <p>{{ $product->name }} is designed for premium performance, durability, and daily convenience. Sourced from certified manufacturers with rigorous quality assurance checks before fulfillment.</p>
        @endif
    </div>

    <!-- Tab 2: Specifications -->
    <div id="tab-specs" class="tab-pane">
        @if($product->specifications && $product->specifications->count() > 0)
            <table class="specs-table">
                @foreach($product->specifications as $spec)
                    <tr>
                        <td>{{ $spec->name }}</td>
                        <td>{{ $spec->value }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <table class="specs-table">
                <tr>
                    <td>Product Name</td>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <td>Merchant SKU</td>
                    <td>{{ $product->merchant_sku }}</td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td>{{ $product->category->name ?? 'Gadgets' }}</td>
                </tr>
                <tr>
                    <td>Condition</td>
                    <td>Brand New (Factory Sealed)</td>
                </tr>
            </table>
        @endif
    </div>

    <!-- Tab 3: Shipping & Returns (Pillar 5) -->
    <div id="tab-shipping" class="tab-pane">
        <h4 style="color: var(--text-primary); margin-bottom: 10px;">Shipping Details</h4>
        <p>All orders are processed and dispatched within 1–3 business days. You will receive an automated shipping confirmation with your tracking link as soon as your package leaves our fulfillment center.</p>
        <ul style="padding-left: 20px; margin-top: 10px; margin-bottom: 20px;">
            <li><strong>Standard Delivery:</strong> 7–15 Business Days</li>
            <li><strong>Order Tracking:</strong> Live online tracking accessible 24/7</li>
            <li><strong>Secure Handover:</strong> Verified delivery confirmation</li>
        </ul>
        <h4 style="color: var(--text-primary); margin-bottom: 10px;">Return & Replacement Policy</h4>
        <p>We provide a 7-day hassle-free return and replacement policy for any defective or damaged products upon arrival. Contact customer support with your order number to initiate a return.</p>
    </div>

    <!-- Tab 4: Customer Reviews (Pillar 3 Authentic Reviews) -->
    <div id="tab-reviews" class="tab-pane">
        <div class="reviews-container">
            @forelse($product->approvedReviews as $review)
                <div class="review-card">
                    <div class="review-meta">
                        <div class="review-author">
                            {{ $review->user->first_name ?? 'Customer' }}
                            @if($review->verified_purchase)
                                <span class="badge-verified"><i data-lucide="check" style="width:12px;height:12px;display:inline;"></i> Verified Purchase</span>
                            @endif
                        </div>
                        <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="stars" style="margin-bottom: 8px;">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" style="width:14px;height:14px; {{ $i <= $review->rating ? 'fill: #f59e0b;' : 'opacity: 0.3;' }}"></i>
                        @endfor
                    </div>
                    @if(!empty($review->title))
                        <div class="review-title">{{ $review->title }}</div>
                    @endif
                    <div class="review-body">{{ $review->review }}</div>
                </div>
            @empty
                <p style="color: var(--text-secondary);">No customer reviews yet for this product. Be the first to share your experience!</p>
            @endforelse

            <!-- Review Submission Form -->
            <div class="review-form-box">
                <h3 style="color: var(--text-primary); margin-bottom: 16px; font-size: 18px;">Write a Customer Review</h3>
                <form action="{{ route('store.product.review', $product->slug) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Rating</label>
                        <div class="rating-row-container">
                            <div class="star-rating-widget" role="radiogroup" aria-label="Product rating">
                                <input type="radio" id="star5" name="rating" value="5" checked onchange="document.getElementById('ratingHint').innerText = '5 Stars (Excellent)'">
                                <label for="star5" title="5 Stars - Excellent">★</label>

                                <input type="radio" id="star4" name="rating" value="4" onchange="document.getElementById('ratingHint').innerText = '4 Stars (Very Good)'">
                                <label for="star4" title="4 Stars - Very Good">★</label>

                                <input type="radio" id="star3" name="rating" value="3" onchange="document.getElementById('ratingHint').innerText = '3 Stars (Average)'">
                                <label for="star3" title="3 Stars - Average">★</label>

                                <input type="radio" id="star2" name="rating" value="2" onchange="document.getElementById('ratingHint').innerText = '2 Stars (Below Average)'">
                                <label for="star2" title="2 Stars - Below Average">★</label>

                                <input type="radio" id="star1" name="rating" value="1" onchange="document.getElementById('ratingHint').innerText = '1 Star (Poor)'">
                                <label for="star1" title="1 Star - Poor">★</label>
                            </div>
                            <span class="rating-score-hint" id="ratingHint">5 Stars (Excellent)</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Review Headline / Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Summarize your experience (e.g. Great build quality!)" required>
                    </div>
                    <div class="form-group">
                        <label>Your Review</label>
                        <textarea name="review" rows="4" class="form-control" placeholder="What did you like or dislike about this product?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-weight: 700;">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function switchMainImage(src, btn) {
        document.getElementById('mainProductImg').src = src;
        document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    function selectVariant(elem) {
        if (!elem) return;
        const variantId = elem.getAttribute('data-variant-id');
        const price = elem.getAttribute('data-price');
        const imageUrl = elem.getAttribute('data-image');

        if (variantId) {
            document.getElementById('selectedVariantId').value = variantId;
        }
        if (price) {
            document.getElementById('displayPrice').innerText = '$' + price;
        }
        document.querySelectorAll('.variant-option').forEach(el => el.classList.remove('active'));
        elem.classList.add('active');

        if (imageUrl && imageUrl.trim() !== '') {
            document.getElementById('mainProductImg').src = imageUrl;
        }
    }

    function showTab(tabId, btn) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        const target = document.getElementById(tabId);
        if (target) target.classList.add('active');
        if (btn) {
            btn.classList.add('active');
        } else {
            const matchingBtn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick').includes(tabId));
            if (matchingBtn) matchingBtn.classList.add('active');
        }
    }
</script>
@endsection