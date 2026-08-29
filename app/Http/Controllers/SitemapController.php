<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\Seo\UsGeoDataService;
use App\Services\Seo\GuideDataService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Master Sitemap Index (/sitemap.xml)
     */
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url', 'https://atozgadgetz.com'), '/');

        $sitemaps = [
            $baseUrl . '/sitemap-main.xml',
            $baseUrl . '/sitemap-products.xml',
            $baseUrl . '/sitemap-categories.xml',
            $baseUrl . '/sitemap-locations.xml',
            $baseUrl . '/sitemap-guides.xml',
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sitemaps as $sitemapUrl) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . htmlspecialchars($sitemapUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex, follow',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    /**
     * Main Static & Collection Hubs Sitemap (/sitemap-main.xml)
     */
    public function main(): Response
    {
        $baseUrl = rtrim(config('app.url', 'https://atozgadgetz.com'), '/');

        $urls = [
            ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/shop', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/gadgets-under-10', 'priority' => '0.85', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gadgets-under-20', 'priority' => '0.85', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gadgets-under-50', 'priority' => '0.85', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gadgets-under-100', 'priority' => '0.85', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gifts', 'priority' => '0.85', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gifts/for-gamers', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gifts/for-tech-lovers', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gifts/under-50', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/gifts/under-100', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/use-case/travel-gadgets', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/use-case/home-office-gadgets', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/use-case/car-gadgets', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/use-case/fitness-gadgets', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/use-case/kitchen-gadgets', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/faq', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/about-us', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/shipping-policy', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/return-and-refund-policy', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/privacy-policy', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/terms-conditions', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        return $this->buildUrlSetResponse($urls);
    }

    /**
     * Active Products Sitemap (/sitemap-products.xml)
     */
    public function products(): Response
    {
        $products = Product::where('status', 'active')
            ->where('is_active', true)
            ->with(['cjProduct'])
            ->latest('updated_at')
            ->get();

        $urls = [];
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('store.product', $product->slug),
                'lastmod' => ($product->updated_at ?? now())->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.85',
                'image' => !empty($product->thumbnail_url) && !str_ends_with($product->thumbnail_url, 'favicon.png') ? [
                    'loc' => $product->thumbnail_url,
                    'title' => $product->name
                ] : null
            ];
        }

        return $this->buildUrlSetResponse($urls);
    }

    /**
     * Categories Sitemap (/sitemap-categories.xml)
     */
    public function categories(): Response
    {
        $baseUrl = rtrim(config('app.url', 'https://atozgadgetz.com'), '/');
        $categories = Category::where('status', 'active')->get();

        $urls = [];
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => $baseUrl . '/shop?category=' . urlencode($category->slug),
                'lastmod' => ($category->updated_at ?? now())->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        return $this->buildUrlSetResponse($urls);
    }

    /**
     * USA Geo Landing Pages Sitemap (/sitemap-locations.xml)
     */
    public function locations(): Response
    {
        $baseUrl = rtrim(config('app.url', 'https://atozgadgetz.com'), '/');
        $urls = [];

        // National USA Hub
        $urls[] = [
            'loc' => route('seo.usa_national'),
            'lastmod' => now()->startOfWeek()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.9'
        ];

        // 50 US States
        foreach (UsGeoDataService::getAllStates() as $stateSlug => $state) {
            $urls[] = [
                'loc' => route('seo.usa_state', $stateSlug),
                'lastmod' => now()->startOfWeek()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        // 60+ Major US Cities
        foreach (UsGeoDataService::getAllCities() as $city) {
            $urls[] = [
                'loc' => route('seo.usa_city', [$city['state_slug'], $city['slug']]),
                'lastmod' => now()->startOfWeek()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.75'
            ];
        }

        return $this->buildUrlSetResponse($urls);
    }

    /**
     * Buying Guides Sitemap (/sitemap-guides.xml)
     */
    public function guides(): Response
    {
        $urls = [];

        // Guides Index
        $urls[] = [
            'loc' => route('seo.guides_index'),
            'lastmod' => now()->startOfWeek()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.85'
        ];

        // Individual Guides
        foreach (GuideDataService::getAllGuides() as $slug => $guide) {
            $urls[] = [
                'loc' => route('seo.guide_detail', $slug),
                'lastmod' => date('c', strtotime($guide['updated_at'])),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        return $this->buildUrlSetResponse($urls);
    }

    /**
     * Helper to render valid XML urlset response
     */
    protected function buildUrlSetResponse(array $urls): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($urls as $item) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . ($item['lastmod'] ?? now()->toAtomString()) . "</lastmod>\n";
            $xml .= "    <changefreq>" . ($item['changefreq'] ?? 'weekly') . "</changefreq>\n";
            $xml .= "    <priority>" . ($item['priority'] ?? '0.7') . "</priority>\n";

            if (!empty($item['image'])) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($item['image']['loc'], ENT_XML1, 'UTF-8') . "</image:loc>\n";
                $xml .= "      <image:title>" . htmlspecialchars($item['image']['title'] ?? '', ENT_XML1, 'UTF-8') . "</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex, follow',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}
