<?php

namespace App\Services\Seo;

use App\Models\Product;
use App\Models\Category;

class FaqDataService
{
    /**
     * Master Knowledge Base FAQs grouped by topic.
     */
    public static function getMasterFaqs(): array
    {
        return [
            'General & Storefront' => [
                [
                    'q' => 'What is AtoZGadgets?',
                    'a' => 'AtoZGadgets is a curated online electronics and innovative tech store specializing in verified viral gadgets, smart home automation, high-performance mobile and car accessories, and everyday smart devices with fast delivery across the United States and worldwide.'
                ],
                [
                    'q' => 'Where can I buy trending tech gadgets online in the USA?',
                    'a' => 'You can shop the full collection of trending gadgets at https://atozgadgetz.com/shop. We provide end-to-end buyer protection, verified manufacturer quality checks, and expedited US priority delivery.'
                ],
                [
                    'q' => 'Are products on AtoZGadgets brand new and authentic?',
                    'a' => 'Yes. Every gadget and electronic item listed on AtoZGadgets is 100% brand new, tested for quality assurance, and shipped securely in original factory packaging.'
                ],
            ],

            'US Shipping & Delivery' => [
                [
                    'q' => 'Does AtoZGadgets ship to all 50 US States?',
                    'a' => 'Yes! AtoZGadgets ships to all 50 US States, including residential addresses, business suites, apartments, and military PO boxes via USPS Priority Mail, UPS, and DHL Express.'
                ],
                [
                    'q' => 'How long does shipping take to the United States?',
                    'a' => 'Standard US delivery typically takes 3 to 7 business days. Major metropolitan areas (New York, Los Angeles, Chicago, Dallas, Houston, Miami) frequently receive packages in 3 to 5 business days.'
                ],
                [
                    'q' => 'How much does shipping cost?',
                    'a' => 'We offer FREE Standard Shipping across the United States on qualified promotional orders. Live real-time courier rates are calculated transparently at checkout with no hidden surcharges.'
                ],
                [
                    'q' => 'Will I receive a real-time tracking number?',
                    'a' => 'Yes. As soon as your order is dispatched, a tracking confirmation email with live courier tracking links (USPS/UPS/DHL) is automatically sent to your registered email address.'
                ],
                [
                    'q' => 'Do you ship to residential apartments and office buildings?',
                    'a' => 'Yes. Our logistics partners deliver directly to apartment doorsteps, leasing offices, parcel lockers, and corporate front desks across the USA.'
                ],
            ],

            'Payment Security & Methods' => [
                [
                    'q' => 'What payment methods does AtoZGadgets accept?',
                    'a' => 'We accept PayPal (including PayPal Credit & Pay in 4) and all major Credit/Debit Cards including Visa, MasterCard, American Express, and Discover.'
                ],
                [
                    'q' => 'Is checkout on AtoZGadgets secure?',
                    'a' => 'Yes. All checkout sessions are encrypted with industry-standard 256-bit SSL/TLS encryption. We do not store raw card numbers or financial credentials on our servers.'
                ],
            ],

            'Returns & 30-Day Guarantee' => [
                [
                    'q' => 'What is the AtoZGadgets return policy?',
                    'a' => 'We offer a 30-Day Money-Back Guarantee. If you are not completely satisfied with your gadget, you can return it within 30 days of delivery for a full replacement or refund.'
                ],
                [
                    'q' => 'How do I initiate a return or exchange?',
                    'a' => 'Simply contact our 24/7 customer support team at support@atozgadgetz.com with your order number and photo/video evidence if an item arrived defective.'
                ],
            ],

            'Smart Home Tech' => [
                [
                    'q' => 'What are the best smart home gadgets for modern apartments?',
                    'a' => 'Top recommended smart home gadgets include automated sensor night lights, wireless smart plugs, touchless kitchen sanitizers, and compact robotic cleaning devices that require zero permanent wiring.'
                ],
                [
                    'q' => 'Are smart gadgets easy to set up for beginners?',
                    'a' => 'Yes! All smart home gadgets in our catalog are plug-and-play and include step-by-step English user manuals for fast 5-minute setup.'
                ],
            ],

            'Car & Commuter Tech' => [
                [
                    'q' => 'What are essential car gadgets for long road trips?',
                    'a' => 'Essential road trip gadgets include high-power cordless car vacuums, fast wireless MagSafe car mounts, dual USB-C fast car chargers, and portable tire inflators.'
                ],
                [
                    'q' => 'Can a portable car vacuum effectively clean pet hair and sand?',
                    'a' => 'Yes. Our high-RPM brushless motor vacuums feature cyclonic suction (up to 9000Pa) and specialized crevice nozzles designed specifically for tight upholstery and floor mats.'
                ],
            ],

            'Gaming & Desk Setup' => [
                [
                    'q' => 'What tech accessories improve a home office or gaming desk?',
                    'a' => 'Ergonomic cable management trays, RGB ambient desk lighting, multi-device fast wireless charging docks, and wrist-support mousepads significantly elevate desk comfort and productivity.'
                ],
                [
                    'q' => 'Are there great gaming gadgets under $50?',
                    'a' => 'Yes! AtoZGadgets curates premium mechanical keycap sets, braided high-speed cables, headset stands, and RGB mouse mats all under $50.'
                ],
            ],

            'Budget & Deals' => [
                [
                    'q' => 'What gadgets can I buy for under $10 and $20?',
                    'a' => 'You can find ultra-useful cable organizers, phone ring stands, mini LED lights, screen cleaners, and adapter dongles under $10 and $20 in our dedicated budget collections.'
                ],
                [
                    'q' => 'Does AtoZGadgets offer volume discounts or bundle deals?',
                    'a' => 'Yes, multi-item checkout discounts and seasonal coupon codes are available directly on product pages and in the cart.'
                ],
            ]
        ];
    }

    /**
     * Generate dynamic, product-specific FAQs derived from authentic database attributes.
     */
    public static function getProductFaqs(Product $product): array
    {
        $price = '$' . number_format($product->price, 2);
        $name = $product->name;
        $categoryName = $product->category->name ?? 'Gadget';

        $faqs = [
            [
                'q' => "What is {$name} and how does it work?",
                'a' => "{$name} is a high-performance {$categoryName} engineered for convenience, durability, and daily reliability. " . (!empty($product->description) ? \Illuminate\Support\Str::limit(strip_tags($product->description), 150) : "It features intuitive controls and modern craftsmanship.")
            ],
            [
                'q' => "How much does {$name} cost on AtoZGadgets?",
                'a' => "{$name} is currently available for {$price} on AtoZGadgets, backed by our best-price promise and fast US delivery."
            ],
            [
                'q' => "Does AtoZGadgets ship {$name} across the United States?",
                'a' => "Yes! We ship {$name} to all 50 US States via USPS Priority Mail and UPS Express. Standard delivery takes 3 to 7 business days with end-to-end tracking."
            ],
            [
                'q' => "What is included with {$name}?",
                'a' => "Every order includes the brand new {$name}, factory accessories, standard charging/mounting cables (if applicable), and an official user manual in retail packaging."
            ],
            [
                'q' => "Can I return {$name} if I am not satisfied?",
                'a' => "Yes. {$name} is covered by our 30-Day Money-Back Guarantee. If you experience any issues or are not completely satisfied, you can request a hassle-free return or replacement."
            ],
            [
                'q' => "What payment methods can I use to buy {$name}?",
                'a' => "You can securely purchase {$name} using PayPal, Visa, MasterCard, American Express, or Discover with 256-bit encrypted checkout."
            ]
        ];

        return $faqs;
    }

    /**
     * Generate city-specific shipping and delivery FAQs.
     */
    public static function getCityFaqs(array $city, array $state): array
    {
        $cityName = $city['name'];
        $stateName = $state['name'];
        $transit = $city['transit_days'] ?? '3-5';
        $focus = $city['focus'] ?? 'Smart Gadgets & Consumer Electronics';

        return [
            [
                'q' => "Does AtoZGadgets deliver gadgets to {$cityName}, {$stateName}?",
                'a' => "Yes! We ship directly to all residential, apartment, and business addresses across {$cityName} and the surrounding {$stateName} metropolitan area."
            ],
            [
                'q' => "How fast is shipping to {$cityName}?",
                'a' => "Orders to {$cityName} typically arrive within {$transit} business days via USPS Priority Mail and UPS Direct Delivery. Real-time tracking is provided upon dispatch."
            ],
            [
                'q' => "Can I order {$focus} online in {$cityName}?",
                'a' => "Yes. AtoZGadgets curates top-rated {$focus} with doorstep delivery in {$cityName}, complete with a 30-day money-back guarantee."
            ],
            [
                'q' => "Is free shipping available to {$cityName}, {$stateName}?",
                'a' => "Yes! We offer free standard shipping on qualifying orders delivered anywhere in {$cityName} and across {$stateName}."
            ],
            [
                'q' => "How do I track my gadget delivery in {$cityName}?",
                'a' => "Once shipped, you will receive an email with your USPS/UPS tracking number to monitor real-time delivery milestones right to your {$cityName} doorstep."
            ]
        ];
    }

    /**
     * Generate state-specific shipping and delivery FAQs.
     */
    public static function getCategoryFaqs(Category $category): array
    {
        $catName = $category->name;
        return [
            [
                'q' => "What types of {$catName} can I buy online?",
                'a' => "Our {$catName} collection features top-rated, factory-tested products designed for daily convenience, reliability, and value. Browse all models with verified customer reviews and fast US shipping."
            ],
            [
                'q' => "How long does shipping take for {$catName} in the USA?",
                'a' => "All {$catName} orders ship directly to US addresses within 3 to 7 business days with comprehensive courier tracking."
            ],
            [
                'q' => "Are {$catName} covered by a warranty or guarantee?",
                'a' => "Yes, all items in the {$catName} collection include our 30-day money-back guarantee and manufacturer defect protection."
            ]
        ];
    }

    /**
     * Generate budget collection FAQs.
     */
    public static function getPriceHubFaqs(int $budget): array
    {
        return [
            [
                'q' => "What are the best tech gadgets under \${$budget}?",
                'a' => "Our curated Under \${$budget} collection features top-value smart home accessories, mobile tools, cable organizers, and viral tech devices handpicked for maximum performance without breaking the bank."
            ],
            [
                'q' => "Are gadgets under \${$budget} good quality?",
                'a' => "Yes! Every budget gadget is vetted for build quality, safety certifications, and real-world durability, backed by our 30-day money-back guarantee."
            ],
            [
                'q' => "Does free shipping apply to items under \${$budget}?",
                'a' => "Yes, qualifying multi-item cart orders receive free shipping across the USA regardless of individual item price."
            ]
        ];
    }
}
