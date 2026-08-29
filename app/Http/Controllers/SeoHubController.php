<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Services\Seo\UsGeoDataService;
use App\Services\Seo\FaqDataService;
use App\Services\Seo\GuideDataService;

class SeoHubController extends Controller
{
    /**
     * Dedicated Price-Tier Landing Pages (/gadgets-under-10, /gadgets-under-20, etc.)
     */
    public function priceHub(int $budget)
    {
        $validBudgets = [10, 20, 50, 100];
        if (!in_array($budget, $validBudgets)) {
            abort(404);
        }

        $products = Product::published()
            ->where('price', '<=', $budget)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $faqs = FaqDataService::getPriceHubFaqs($budget);
        $otherBudgets = array_filter($validBudgets, fn($b) => $b !== $budget);

        return view('store.seo.price_hub', compact('budget', 'products', 'faqs', 'otherBudgets'));
    }

    /**
     * Dedicated Use-Case Collections (/use-case/{slug})
     */
    public function useCase(string $slug)
    {
        $useCases = [
            'travel-gadgets' => [
                'title' => 'Best Travel Gadgets & Compact Portable Tech',
                'description' => 'Shop TSA-friendly travel tech accessories, GaN fast chargers, noise-isolating audio, and compact electronic gear for road trips and flights.',
                'keywords' => ['travel', 'portable', 'compact', 'wireless', 'charger', 'adapter', 'luggage'],
                'tagline' => 'Engineered for Frequent Flyers, Road Trippers & Digital Nomads'
            ],
            'home-office-gadgets' => [
                'title' => 'Best Work-From-Home & Desk Setup Tech Gadgets',
                'description' => 'Maximize desk productivity with multi-device wireless charging stations, cable management tools, RGB screenbar lights, and ergonomic accessories.',
                'keywords' => ['desk', 'office', 'charging', 'stand', 'cable', 'led', 'holder'],
                'tagline' => 'Clean Ergonomics & Wireless Charging for Modern Workspaces'
            ],
            'car-gadgets' => [
                'title' => 'Must-Have Car Gadgets & Automotive Accessories',
                'description' => 'Upgrade your daily commute with cordless car vacuums, MagSafe magnetic phone mounts, dual USB-C chargers, and road trip tech tools.',
                'keywords' => ['car', 'auto', 'vacuum', 'mount', 'charger', 'tire', 'holder'],
                'tagline' => 'Convenience, Power & Spotless Cleanliness for Every Vehicle'
            ],
            'fitness-gadgets' => [
                'title' => 'Best Fitness, Workout & Wellness Tech Gadgets',
                'description' => 'Track your performance and recover faster with smartwatch bands, portable massagers, workout accessories, and wearable health tech.',
                'keywords' => ['fitness', 'sport', 'watch', 'band', 'massage', 'health', 'strap'],
                'tagline' => 'Performance Tracking & Daily Recovery Tech'
            ],
            'kitchen-gadgets' => [
                'title' => 'Innovative Kitchen Gadgets & Smart Home Utilities',
                'description' => 'Cook faster and maintain effortless hygiene with touchless sensors, smart kitchen utilities, and compact electronic tools.',
                'keywords' => ['kitchen', 'dispenser', 'sensor', 'cleaner', 'scale', 'cutter', 'cook'],
                'tagline' => 'Modern Convenience for Food Prep & Daily Cleanup'
            ],
        ];

        if (!isset($useCases[$slug])) {
            abort(404);
        }

        $meta = $useCases[$slug];
        $keywords = $meta['keywords'];

        $products = Product::published()
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('name', 'like', "%{$kw}%")
                      ->orWhere('description', 'like', "%{$kw}%");
                }
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('store.seo.use_case', compact('slug', 'meta', 'products'));
    }

    /**
     * Main Gifts Discovery Hub (/gifts)
     */
    public function giftsIndex()
    {
        $featuredGifts = Product::published()->latest()->limit(8)->get();
        $under50 = Product::published()->where('price', '<=', 50)->latest()->limit(4)->get();
        $under100 = Product::published()->where('price', '<=', 100)->latest()->limit(4)->get();

        return view('store.seo.gifts_index', compact('featuredGifts', 'under50', 'under100'));
    }

    /**
     * Segmented Gift Collections (/gifts/{slug})
     */
    public function giftCategory(string $slug)
    {
        $giftCategories = [
            'for-gamers' => [
                'title' => 'Best Tech Gifts for Gamers & PC / Console Enthusiasts',
                'description' => 'Level up their battlestation with RGB lighting, gaming headset stands, high-speed cables, and mechanical key accessories.',
                'filter' => ['game', 'gaming', 'headset', 'rgb', 'mouse', 'cable']
            ],
            'for-tech-lovers' => [
                'title' => 'Coolest Gadget Gifts for Tech Lovers & Early Adopters',
                'description' => 'Discover innovative wireless charging hubs, smart home devices, and futuristic electronics perfect for birthdays and holidays.',
                'filter' => ['smart', 'wireless', 'charger', 'watch', 'audio', 'sensor']
            ],
            'under-50' => [
                'title' => 'Best Tech Gifts Under $50 — High Value, Zero Cheap Junk',
                'description' => 'Shop top-rated gadgets, portable vacuums, and charging accessories that feel premium without exceeding a $50 budget.',
                'max_price' => 50
            ],
            'under-100' => [
                'title' => 'Premium Tech Gifts Under $100 for Friends & Family',
                'description' => 'Impress your loved ones with high-performance audio gadgets, smart home kits, and wireless workstation docks under $100.',
                'max_price' => 100
            ],
        ];

        if (!isset($giftCategories[$slug])) {
            abort(404);
        }

        $meta = $giftCategories[$slug];
        $query = Product::published();

        if (isset($meta['max_price'])) {
            $query->where('price', '<=', $meta['max_price']);
        }

        if (isset($meta['filter'])) {
            $terms = $meta['filter'];
            $query->where(function($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('name', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                }
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('store.seo.gift_category', compact('slug', 'meta', 'products'));
    }

    /**
     * USA National Shipping & 50-State Hub (/usa)
     */
    public function usaNational()
    {
        $states = UsGeoDataService::getAllStates();
        $topCities = UsGeoDataService::getTopCommercialCities(24);
        $trendingProducts = Product::published()->latest()->limit(8)->get();
        $shippingFaqs = FaqDataService::getMasterFaqs()['US Shipping & Delivery'] ?? [];

        return view('store.seo.usa_national', compact('states', 'topCities', 'trendingProducts', 'shippingFaqs'));
    }

    /**
     * State Landing Page (/usa/{state})
     */
    public function usaState(string $state)
    {
        $stateData = UsGeoDataService::getStateBySlug($state);
        if (!$stateData) {
            abort(404);
        }

        $cities = UsGeoDataService::getCitiesForState($state);
        $popularProducts = Product::published()->latest()->limit(8)->get();
        $allStates = UsGeoDataService::getAllStates();

        return view('store.seo.usa_state', compact('stateData', 'cities', 'popularProducts', 'allStates'));
    }

    /**
     * City Landing Page (/usa/{state}/{city})
     */
    public function usaCity(string $state, string $city)
    {
        $stateData = UsGeoDataService::getStateBySlug($state);
        $cityData = UsGeoDataService::getCityBySlug($state, $city);

        if (!$stateData || !$cityData) {
            abort(404);
        }

        $products = Product::published()->latest()->limit(12)->get();
        $faqs = FaqDataService::getCityFaqs($cityData, $stateData);
        $siblingCities = array_filter(
            UsGeoDataService::getCitiesForState($state),
            fn($c) => $c['slug'] !== $city
        );

        return view('store.seo.usa_city', compact('stateData', 'cityData', 'products', 'faqs', 'siblingCities'));
    }

    /**
     * Buying Guides Index (/guides)
     */
    public function guidesIndex()
    {
        $guides = GuideDataService::getAllGuides();
        return view('store.seo.guides_index', compact('guides'));
    }

    /**
     * Buying Guide Detail (/guides/{slug})
     */
    public function guideDetail(string $slug)
    {
        $guide = GuideDataService::getGuideBySlug($slug);
        if (!$guide) {
            abort(404);
        }

        $relatedGuides = GuideDataService::getRelatedGuides($slug, 3);
        $recommendedProducts = Product::published()->latest()->limit(4)->get();

        return view('store.seo.guide_detail', compact('guide', 'relatedGuides', 'recommendedProducts'));
    }

    /**
     * Master Knowledge Base / FAQ (/faq)
     */
    public function faqMaster()
    {
        $faqCategories = FaqDataService::getMasterFaqs();
        return view('store.seo.faq_master', compact('faqCategories'));
    }
}
