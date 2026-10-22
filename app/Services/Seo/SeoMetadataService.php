<?php

namespace App\Services\Seo;

use App\Models\Product;
use Illuminate\Support\Str;

class SeoMetadataService
{
    /**
     * Archetype keyword dictionaries and spec templates.
     */
    private static array $archetypes = [
        'watches_women' => [
            'triggers' => ['watch for women', 'womens watch', 'ladies watch', 'women quartz', 'bracelet watch', 'female watch', 'girl watch'],
            'primary_term' => "Women's Quartz Watch",
            'short_tail' => ['womens watch', 'ladies watch', 'girls watch', 'quartz watch', 'waterproof watch'],
            'mid_tail' => ["women's quartz watch", 'vintage ladies watch', 'waterproof steel watch', 'women dress watch', 'luxury wrist watch for women'],
            'long_tail' => ['vintage high end waterproof quartz watch for women', 'elegant stainless steel bracelet watch for ladies', 'waterproof everyday quartz watch for girls'],
            'brand_tail' => ['AtoZGadgets watch', 'AtoZGadgets womens accessories', 'gadgetz lifestyle store USA', 'home gadgetz'],
            'specs' => [
                'Movement Type' => 'Precision Quartz Movement',
                'Strap & Case Material' => 'Polished 316L Stainless Steel',
                'Water Resistance' => '30M / 3ATM (Rain & Splash Resistant)',
                'Clasp Type' => 'Fold-Over Push-Button Deployment Clasp',
                'Dial Window' => 'Hardened Mineral Scratch-Resistant Glass',
                'Target Audience' => 'Women, Ladies, Girls, Fashion Gifting',
                'Warranty & Guarantee' => '30-Day AtoZGadgets Replacement Guarantee',
            ],
            'faqs' => [
                'Is this women\'s quartz watch safe for handwashing and rain?' => 'Yes. It features a 30M (3ATM) water resistance rating, making it completely protected against rain, sink splashes, and daily moisture.',
                'Can the stainless steel bracelet be adjusted for different wrist sizes?' => 'Yes. The steel links can be easily removed or adjusted with standard watch pin tools to fit slender or wider ladies wrists comfortably.',
                'Does the watch come with a battery pre-installed?' => 'Yes! Every watch arrives with a fresh, pre-installed high-grade silver oxide battery ready to wear out of the box.',
                'How does AtoZGadgets ship this watch across the USA?' => 'All orders are dispatched within 24–48 hours via USPS Priority Mail or UPS with full door-to-door tracking across all 50 states.',
                'What is the AtoZGadgets satisfaction guarantee?' => 'We back every timepiece with our 30-Day Money-Back Guarantee. If it does not exceed your expectations, returns and exchanges are quick and easy.',
            ]
        ],

        'watches_men' => [
            'triggers' => ['mens watch', 'men watch', 'sports watch for male', 'quartz watch with a genuine'],
            'primary_term' => "Men's Sports & Luxury Watch",
            'short_tail' => ['mens watch', 'sports watch', 'waterproof watch', 'quartz watch', 'men wristwatch'],
            'mid_tail' => ["men's sports watch", 'waterproof steel quartz watch', 'vintage men watch', 'military sports watch'],
            'long_tail' => ['high end waterproof quartz watch for men', 'outdoor tactical sports watch for male students', 'mens business stainless steel wrist watch'],
            'brand_tail' => ['AtoZGadgets mens watch', 'gadgetz tech store', 'viral gadgetz USA'],
            'specs' => [
                'Movement Type' => 'High-Precision Quartz Movement',
                'Case Construction' => 'Reinforced Alloy / Stainless Steel',
                'Water Resistance' => '3ATM / 30M Splash Resistant',
                'Strap Type' => 'Heavy-Duty Steel / Silicone Sports Band',
                'Dial Window' => 'Scratch-Proof Mineral Crystal',
                'Ideal For' => 'Daily Wear, Outdoor Sports, Business Formal',
                'Quality Guarantee' => '30-Day Full AtoZGadgets Buyer Protection',
            ],
            'faqs' => [
                'Is this men\'s watch durable enough for sports and outdoor use?' => 'Yes. The case and dial are engineered with shock-absorbing alloy and scratch-resistant crystal to withstand active daily use.',
                'How accurate is the quartz movement?' => 'Quartz movements are among the most accurate in horology, typically deviating by less than ±15 seconds per month.',
                'Is fast US shipping available?' => 'Yes! We ship directly across all 50 US states via USPS and UPS with estimated 3–7 business day delivery.',
            ]
        ],

        'smart_wearables' => [
            'triggers' => ['smart ring', 'health tracker', 'fitness ring', 'smart watch', 'silicone sports watch strap'],
            'primary_term' => "Smart Health & Wearable Gadget",
            'short_tail' => ['smart ring', 'health tracker', 'smart watch', 'fitness tracker', 'smart gadgetz'],
            'mid_tail' => ['smart ring health tracker', 'heart rate sleep monitor ring', 'smart wearable gadgetz', 'fitness health ring'],
            'long_tail' => ['smart ring health tracker with sleep and heart rate monitoring', 'waterproof titanium health ring for fitness tracking', 'trending smart wearable gadgets on AtoZGadgets'],
            'brand_tail' => ['AtoZGadgets smart ring', 'smart gadgetz online', 'home gadgetz and wearables'],
            'specs' => [
                'Sensors' => 'Optical Heart Rate, Blood Oxygen (SpO2) & Temperature Sensor',
                'Waterproof Rating' => 'IP68 / 5ATM Waterproof (Swimming & Shower Safe)',
                'Battery Life' => 'Up to 5–7 Days Continuous Tracking on Single Charge',
                'Connectivity' => 'Bluetooth Low Energy (BLE 5.0) iOS & Android Compatible',
                'App Integration' => 'Free Companion App with Zero Subscription Fees',
                'Enclosure' => 'Hypoallergenic Titanium / Ceramic Bio-Compatible Alloy',
            ],
            'faqs' => [
                'Do I need a monthly subscription to track health data with this smart ring?' => 'No! Unlike other competitors, our smart ring connects to a 100% free companion mobile app with zero recurring monthly fees.',
                'Can I wear the smart ring while showering or swimming?' => 'Yes. With its IP68/5ATM waterproof enclosure, it is fully sealed for daily handwashing, showers, workouts, and swimming.',
                'How long does the battery last on a single charge?' => 'The high-density micro battery provides 5 to 7 days of continuous 24/7 health and sleep tracking per quick charge.',
            ]
        ],

        'kitchen_gadgets' => [
            'triggers' => ['silverware', 'storage container', 'vegetable peeler', 'bamboo cooking spoons', 'meat chopper', 'egg yolk separator', 'onion holder', 'kitchen smasher', 'freezer container'],
            'primary_term' => "Kitchen Gadget & Culinary Tool",
            'short_tail' => ['kitchen gadgets', 'home gadgetz', 'cooking tools', 'kitchen utensils', 'food prep tools'],
            'mid_tail' => ['smart kitchen gadgets', 'stainless steel cooking tools', 'viral kitchen gadgetz', 'dishwasher safe food prep'],
            'long_tail' => ['novelty ceramic egg yolk separator kitchen gadget', 'stainless steel onion holder for safe slicing', 'best kitchen utensils and tools on AtoZGadgets'],
            'brand_tail' => ['AtoZGadgets kitchen', 'home gadgetz kitchen tools', 'gadgetz online USA'],
            'specs' => [
                'Material Safety' => '100% Food-Grade, BPA-Free, Non-Toxic Construction',
                'Cleaning & Care' => 'Dishwasher Safe & Rust-Resistant',
                'Design' => 'Ergonomic Non-Slip Grip for Effortless Prep',
                'Application' => 'Home Kitchen, Meal Prep, Baking, Entertaining',
                'Durability' => 'Commercial-Grade Heavy-Duty Kitchen Tool',
            ],
            'faqs' => [
                'Is this kitchen gadget dishwasher safe?' => 'Yes! All materials are high-temperature resistant, food-grade, and safe for standard top-rack dishwasher cycles.',
                'Does this tool require assembly?' => 'No, it arrives 100% assembled, factory sanitized, and ready for immediate kitchen use.',
                'Why buy kitchen gadgets from AtoZGadgets?' => 'AtoZGadgets curates tested, viral kitchen tools backed by our 30-day money-back guarantee and fast 3–7 day delivery across the USA.',
            ]
        ],

        'christmas_holiday' => [
            'triggers' => ['christmas tree', 'christmas decoration', 'holiday tree', 'reindeer pendant', 'santa claus', 'pvc christmas', 'pe, pvc christmas', 'christmas standing figurine'],
            'primary_term' => "Holiday Decor & Christmas Tree",
            'short_tail' => ['christmas tree', 'holiday decor', 'christmas decorations', 'home gadgetz', 'artificial tree'],
            'mid_tail' => ['pvc artificial christmas tree', 'holiday home decorations indoor', 'reusable christmas tree with stand', 'festive home gadgetz'],
            'long_tail' => ['full lush artificial pvc christmas tree with sturdy metal stand', 'holiday indoor home decoration christmas tree on AtoZGadgets', 'vintage christmas figurines and indoor ornaments'],
            'brand_tail' => ['AtoZGadgets holiday', 'AtoZGadgets christmas decor', 'home gadgetz holiday store'],
            'specs' => [
                'Foliage Material' => 'Eco-Friendly, Non-Allergenic Flame-Retardant PVC / PE',
                'Base Construction' => 'Foldable Heavy-Duty Steel Sturdy Stand',
                'Branch Style' => 'Lush Hinged Full-Profile Branches',
                'Needle Shedding' => 'Zero Shedding (Engineered for Years of Reuse)',
                'Assembly Time' => 'Quick 3-Piece Section Assembly with Easy Fluffing',
                'Use Environment' => 'Indoor Living Room, Office Lobby, Holiday Party Display',
            ],
            'faqs' => [
                'How easy is it to assemble this artificial Christmas tree?' => 'Extremely simple! The tree features 3 hinged interlocking sections that snap into the included heavy-duty steel base in under 5 minutes.',
                'Are the branches flame-retardant and safe for homes with kids/pets?' => 'Yes. All branch needles are certified fire-retardant, non-toxic, and odorless for total home safety.',
                'Can this tree be packed away and reused year after year?' => 'Absolutely. The sturdy metal frame and memory-wire branches fold down neatly into a compact storage box season after season.',
            ]
        ],

        'ambient_decor' => [
            'triggers' => ['lamp', 'light', 'night lamp', 'crystal lamp', 'tapestry', 'sculpture', 'pillow case', 'decor', 'pendant', 'silhouette art', 'glass ball', 'lantern', 'candle'],
            'primary_term' => "Home Decor & Ambient Lighting",
            'short_tail' => ['home decor', 'night lamp', 'ambient lighting', 'home gadgetz', 'room aesthetic'],
            'mid_tail' => ['smart ambient lamp', 'crystal table night lamp', 'home decor aesthetic lighting', 'viral room gadgetz'],
            'long_tail' => ['crystal touch ambient desk night lamp for bedroom', 'modern home decor sculpture and accent lighting', 'trending aesthetic room gadgetz on AtoZGadgets'],
            'brand_tail' => ['AtoZGadgets home', 'home gadgetz decor', 'viral room gadgetz atoz'],
            'specs' => [
                'Lighting Technology' => 'Energy-Efficient Eye-Caring Warm/RGB LED',
                'Power Source' => 'USB Rechargeable / Plug-and-Play Low Voltage Safe',
                'Material' => 'Optical Acrylic / Metal Alloy / Ceramic Composite',
                'Control' => 'Touch Sensor / Multi-Mode Ambient Switch',
                'Placement' => 'Bedside Nightstand, Living Room Shelf, Desk, Patio',
            ],
            'faqs' => [
                'Is this lamp USB rechargeable or does it require constant plugging?' => 'Most models include a built-in rechargeable lithium battery with convenient USB charging, allowing cord-free portable illumination anywhere in your home.',
                'Is the light gentle enough for a nightlight?' => 'Yes! The LED diffusion emits a soft, warm, anti-glare glow that is relaxing and soothing for bedrooms and nurseries.',
            ]
        ],

        'personal_care_beauty' => [
            'triggers' => ['mask', 'bath bomb', 'earwax', 'shaving razor', 'massager', 'beauty instrument', 'essential oil'],
            'primary_term' => "Personal Care & Beauty Gadget",
            'short_tail' => ['beauty gadgets', 'personal care', 'face massager', 'wellness tools', 'gadgetz'],
            'mid_tail' => ['smart beauty gadgets', 'personal care wellness tools', 'foot massager roller', 'skincare accessories'],
            'long_tail' => ['led beauty mask facial photon therapy instrument', 'foot massager roller for plantar fasciitis relief', 'trending personal care and wellness gadgetz'],
            'brand_tail' => ['AtoZGadgets beauty', 'self care gadgetz', 'lifestyle gadgetz atoz'],
            'specs' => [
                'Safety Standard' => 'Dermatologist-Friendly, Hypoallergenic & Skin-Safe',
                'Power / Build' => 'Ergonomic Handheld Design / Rechargeable USB',
                'Portability' => 'Travel-Ready Compact Form Factor',
                'Intended Use' => 'Daily Self-Care, Relaxation, Skincare Routine',
            ],
            'faqs' => [
                'Is this beauty/care gadget suitable for sensitive skin?' => 'Yes. All contact surfaces are engineered from hypoallergenic, medical-grade materials designed for gentle daily use.',
                'Is it easy to clean after each use?' => 'Yes, smooth non-porous surfaces allow effortless sanitization with a damp cloth or warm rinse.',
            ]
        ],

        'sports_outdoor' => [
            'triggers' => ['boat', 'solar fan hat', 'beanie', 'security gate', 'guitar pick', 'diaphragm', 'respirator', 'cycling'],
            'primary_term' => "Sports, Outdoor & Tech Accessory",
            'short_tail' => ['outdoor gear', 'sports gadgets', 'tech accessories', 'gadgetz', 'hobby tech'],
            'mid_tail' => ['fast rc boat for lakes', 'solar fan cooling hat', 'outdoor sports accessories', 'useful tech gadgetz'],
            'long_tail' => ['high speed 2.4ghz remote control rc boat for pools and lakes', 'upf50 sun hat with dual built-in solar fans', 'trending outdoor hobby gadgetz on AtoZGadgets'],
            'brand_tail' => ['AtoZGadgets outdoor', 'sports gadgetz', 'gadgetz online USA'],
            'specs' => [
                'Construction' => 'Weatherproof, Impact-Resistant High-Durability Materials',
                'Design' => 'Field-Tested for Active Outdoor & Recreational Performance',
                'Battery / Operation' => 'High-Efficiency Power Management / Solar / Dual-Mode',
                'Application' => 'Outdoor Sports, Lake Fun, Travel, Hobbies & Home Safety',
            ],
            'faqs' => [
                'Is this item weather and splash resistant for outdoor use?' => 'Yes! Built with outdoor-grade, ruggedized materials designed to withstand wind, water splashes, and sun exposure.',
                'Does it come ready to use out of the box?' => 'Yes, complete factory hardware and instructions are included for fast immediate enjoyment.',
            ]
        ],
    ];

    /**
     * Resolve the archetype definition matching a product.
     */
    public static function resolveArchetype(Product $product): array
    {
        $text = strtolower(($product->name ?? '') . ' ' . ($product->category->name ?? '') . ' ' . ($product->slug ?? ''));

        foreach (self::$archetypes as $key => $data) {
            foreach ($data['triggers'] as $trigger) {
                if (str_contains($text, $trigger)) {
                    return $data;
                }
            }
        }

        // Generic lifestyle/gadget fallback archetype
        return [
            'primary_term' => 'Smart Tech Gadget',
            'short_tail' => ['viral gadgets', 'smart electronics', 'home gadgetz', 'tech accessories', 'online gadgets USA'],
            'mid_tail' => ['trending smart gadgets', 'useful home electronics', 'viral products atozgadgets', 'innovative tech gifts'],
            'long_tail' => ['best viral gadgets and smart home electronics on AtoZGadgets', 'unique everyday tech gadgets with fast US shipping'],
            'brand_tail' => ['AtoZGadgets', 'gadgetz store', 'home gadgetz online'],
            'specs' => [
                'Condition' => 'Brand New (Factory Sealed Retail Packaging)',
                'Build Quality' => 'Rigorous Manufacturer Quality-Tested Inspection',
                'Power / Specs' => 'Universal Standard Compatible Components',
                'Intended Use' => 'Modern Lifestyle, Home Utility, Gifting',
                'Buyer Protection' => '30-Day Money-Back Guarantee & Direct Courier Tracking',
            ],
            'faqs' => [
                'Is this product brand new and authentic?' => 'Yes. Every gadget sold on AtoZGadgets is 100% brand new, authentic, and delivered in original retail packaging.',
                'How fast does AtoZGadgets ship to US addresses?' => 'We dispatch orders in 24–48 hours via USPS Priority Mail and UPS Direct Delivery, with standard arrival in 3 to 7 business days.',
                'What if I need to return or exchange my order?' => 'We offer a 30-Day Money-Back Guarantee. Simply email support@atozgadgetz.com for a hassle-free replacement or full refund.',
            ]
        ];
    }

    /**
     * Generate an ultra-high ranking Meta Title (max 60 chars).
     * Formula: [Primary Target Keyword] - [Key Benefit] | AtoZGadgets
     */
    public static function getMetaTitle(Product $product): string
    {
        $archetype = self::resolveArchetype($product);
        $cleanName = trim(preg_replace('/\bCJ[A-Z0-9_-]+\b/i', '', $product->name));
        $cleanName = preg_replace('/-[a-f0-9]{6}$/i', '', $cleanName); // remove slug hash if present in title
        $cleanName = trim(preg_replace('/\s+/', ' ', $cleanName));

        // Keep up to 52 characters of clean product name to preserve critical keywords
        $titleKeywords = Str::limit($cleanName, 52, '');
        $title = "{$titleKeywords} | AtoZGadgets";

        return $title;
    }

    /**
     * Generate an SEO & AEO optimized Meta Description (145 - 155 chars).
     * Guaranteed non-empty, keyword-rich, and conversion focused.
     */
    public static function getMetaDescription(Product $product): string
    {
        $cleanName = trim(preg_replace('/\bCJ[A-Z0-9_-]+\b/i', '', $product->name));
        $cleanName = Str::limit($cleanName, 55, '');
        $price = '$' . number_format($product->effective_price, 2);

        $archetype = self::resolveArchetype($product);
        $primaryKw = $archetype['short_tail'][0] ?? 'gadgets';

        $desc = "Shop {$cleanName} at AtoZGadgets for only {$price}. Premium {$primaryKw} with fast 3-7 day US delivery & 30-day money-back guarantee. Buy online today!";

        if (strlen($desc) > 160) {
            $desc = Str::limit($desc, 155, '...');
        }

        return $desc;
    }

    /**
     * Generate hyper-targeted keyword permutations for Google & Search engine spiders.
     */
    public static function getMetaKeywords(Product $product): string
    {
        $archetype = self::resolveArchetype($product);
        $cleanName = strtolower(trim(preg_replace('/\bCJ[A-Z0-9_-]+\b/i', '', $product->name)));

        $keywords = array_merge(
            [$cleanName],
            $archetype['short_tail'],
            $archetype['mid_tail'],
            $archetype['brand_tail']
        );

        $unique = array_unique(array_filter(array_map('trim', $keywords)));
        return implode(', ', array_slice($unique, 0, 15));
    }

    /**
     * Generate 40-word AEO Direct Answer Block for ChatGPT, Perplexity, and AI Overviews.
     */
    public static function getAeoDirectAnswer(Product $product): string
    {
        $cleanName = trim(preg_replace('/\bCJ[A-Z0-9_-]+\b/i', '', $product->name));
        $price = '$' . number_format($product->effective_price, 2);
        $archetype = self::resolveArchetype($product);
        $primaryTerm = $archetype['primary_term'];

        return "{$cleanName} is a verified, high-performance {$primaryTerm} available on AtoZGadgets for {$price}. Engineered with premium materials for long-lasting durability, it includes fast 3–7 day priority shipping across all 50 US states and a 30-day money-back guarantee.";
    }

    /**
     * Generate rich specifications dynamically if product lacks custom DB specs.
     */
    public static function getDynamicSpecifications(Product $product): array
    {
        // 1. If product has real DB specifications, use them
        $dbSpecs = $product->relationLoaded('specifications') ? $product->specifications : $product->specifications()->get();
        if ($dbSpecs && $dbSpecs->count() > 0) {
            $list = [];
            foreach ($dbSpecs as $s) {
                $list[$s->name] = $s->value;
            }
            return $list;
        }

        // 2. Derive archetype specifications
        $archetype = self::resolveArchetype($product);
        $specs = [
            'Product Name' => $product->name,
            'Merchant SKU' => $product->merchant_sku,
            'Category' => $product->category->name ?? $archetype['primary_term'],
            'Condition' => 'Brand New (Factory Sealed Retail Packaging)',
        ];

        foreach ($archetype['specs'] as $k => $v) {
            $specs[$k] = $v;
        }

        return $specs;
    }

    /**
     * Generate conversational, question-answering FAQs tailored specifically to this product.
     */
    public static function getProductFaqs(Product $product): array
    {
        $cleanName = trim(preg_replace('/\bCJ[A-Z0-9_-]+\b/i', '', $product->name));
        $price = '$' . number_format($product->effective_price, 2);
        $archetype = self::resolveArchetype($product);

        $faqs = [
            [
                'q' => "What is {$cleanName} and what are its key features?",
                'a' => self::getAeoDirectAnswer($product)
            ],
            [
                'q' => "How much does {$cleanName} cost and what shipping is included?",
                'a' => "{$cleanName} is currently available for {$price} on AtoZGadgets. We provide fast 3 to 7 business day doorstep delivery to all 50 US States via USPS Priority Mail and UPS Express with live online tracking."
            ],
        ];

        // Add archetype-specific FAQs
        foreach ($archetype['faqs'] as $q => $a) {
            $faqs[] = [
                'q' => str_replace('this product', $cleanName, $q),
                'a' => $a
            ];
        }

        // Add standard buyer guarantee FAQ
        $faqs[] = [
            'q' => "Can I return {$cleanName} if I am not satisfied?",
            'a' => "Yes! Every purchase on AtoZGadgets is covered by our 30-Day Money-Back Guarantee. If the item is defective or does not match your expectations, contact support@atozgadgetz.com for a swift return or replacement."
        ];

        return array_slice($faqs, 0, 6);
    }
}
