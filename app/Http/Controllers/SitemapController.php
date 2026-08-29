<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML Sitemap for Google, Bing & AI Search Crawlers
     */
    public function index(): Response
    {
        $baseUrl = config('app.url', 'https://atozgadgetz.com');
        $baseUrl = rtrim($baseUrl, '/');

        // 1. Static Pages
        $staticPages = [
            ['loc' => $baseUrl . '/', 'lastmod' => now()->startOfWeek()->toAtomString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => $baseUrl . '/shop', 'lastmod' => now()->startOfWeek()->toAtomString(), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => $baseUrl . '/about-us', 'lastmod' => now()->startOfMonth()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/contact', 'lastmod' => now()->startOfMonth()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/privacy-policy', 'lastmod' => now()->startOfYear()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/terms-conditions', 'lastmod' => now()->startOfYear()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/return-and-refund-policy', 'lastmod' => now()->startOfYear()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/shipping-policy', 'lastmod' => now()->startOfYear()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        // 2. Active Categories
        $categories = Category::where('status', 'active')->get();

        // 3. Active Published Products
        $products = Product::where('status', 'active')
            ->where('is_active', true)
            ->with(['cjProduct'])
            ->latest('updated_at')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // Render Static Pages
        foreach ($staticPages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($page['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $page['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $page['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        // Render Categories
        foreach ($categories as $category) {
            $catUrl = $baseUrl . '/shop?category=' . urlencode($category->slug);
            $lastmod = ($category->updated_at ?? now())->toAtomString();

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($catUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // Render Products with Images
        foreach ($products as $product) {
            $prodUrl = route('store.product', $product->slug);
            $lastmod = ($product->updated_at ?? now())->toAtomString();
            $imageUrl = $product->thumbnail_url;

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($prodUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>0.85</priority>\n";

            if (!empty($imageUrl) && !str_ends_with($imageUrl, 'favicon.png')) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . "</image:loc>\n";
                $xml .= "      <image:title>" . htmlspecialchars($product->name, ENT_XML1, 'UTF-8') . "</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex, follow', // Do not index the sitemap XML itself in search results, but follow URLs
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}
