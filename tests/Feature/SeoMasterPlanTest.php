<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class SeoMasterPlanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['role_id' => 1]);
        $category = Category::create(['slug' => 'tech-gadgets', 'name' => 'Tech Gadgets', 'status' => 'active']);

        Product::create([
            'name' => 'Smart LED Desk Lamp',
            'slug' => 'smart-led-desk-lamp',
            'sku' => 'SKU-LED-LAMP-01',
            'price' => 29.99,
            'category_id' => $category->id,
            'created_by' => $user->id,
            'is_active' => true,
            'status' => 'active',
            'description' => 'A smart dimmable LED desk lamp with wireless charging base.'
        ]);

        Product::create([
            'name' => 'High Power Cordless Car Vacuum',
            'slug' => 'cordless-car-vacuum',
            'sku' => 'SKU-VAC-CAR-01',
            'price' => 45.00,
            'category_id' => $category->id,
            'created_by' => $user->id,
            'is_active' => true,
            'status' => 'active',
            'description' => 'High suction handheld vacuum for car cleaning and road trips.'
        ]);
    }

    public function test_sitemap_index_and_sub_sitemaps_return_200_xml(): void
    {
        $indexResponse = $this->get('/sitemap.xml');
        $indexResponse->assertStatus(200);
        $indexResponse->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $indexResponse->assertSee('<sitemapindex', false);
        $indexResponse->assertSee('sitemap-main.xml', false);
        $indexResponse->assertSee('sitemap-products.xml', false);
        $indexResponse->assertSee('sitemap-locations.xml', false);
        $indexResponse->assertSee('sitemap-guides.xml', false);

        $mainResponse = $this->get('/sitemap-main.xml');
        $mainResponse->assertStatus(200);
        $mainResponse->assertSee('gadgets-under-50', false);
        $mainResponse->assertSee('faq', false);

        $prodResponse = $this->get('/sitemap-products.xml');
        $prodResponse->assertStatus(200);
        $prodResponse->assertSee('smart-led-desk-lamp', false);

        $locResponse = $this->get('/sitemap-locations.xml');
        $locResponse->assertStatus(200);
        $locResponse->assertSee('/usa', false);
        $locResponse->assertSee('/usa/california/los-angeles', false);
        $locResponse->assertSee('/usa/new-york/new-york-city', false);

        $guideResponse = $this->get('/sitemap-guides.xml');
        $guideResponse->assertStatus(200);
        $guideResponse->assertSee('/guides/best-smart-home-gadgets', false);
    }

    public function test_price_tier_landing_pages_return_200_and_schema(): void
    {
        foreach ([10, 20, 50, 100] as $budget) {
            $response = $this->get("/gadgets-under-{$budget}");
            $response->assertStatus(200);
            $response->assertSee("Under \${$budget}");
            $response->assertSee('CollectionPage', false);
            $response->assertSee('FAQPage', false);
        }

        // Invalid budget should 404
        $invalidResponse = $this->get('/gadgets-under-999');
        $invalidResponse->assertStatus(404);
    }

    public function test_use_case_and_gift_collections_return_200(): void
    {
        $useCaseResponse = $this->get('/use-case/car-gadgets');
        $useCaseResponse->assertStatus(200);
        $useCaseResponse->assertSee('Car Gadgets', false);
        $useCaseResponse->assertSee('CollectionPage', false);

        $giftsIndex = $this->get('/gifts');
        $giftsIndex->assertStatus(200);
        $giftsIndex->assertSee('Ultimate Tech & Gadget Gift Guide', false);

        $giftCategory = $this->get('/gifts/for-gamers');
        $giftCategory->assertStatus(200);
        $giftCategory->assertSee('For Gamers', false);
    }

    public function test_usa_geo_targeting_hubs_return_200(): void
    {
        $natResponse = $this->get('/usa');
        $natResponse->assertStatus(200);
        $natResponse->assertSee('All 50 US States Coverage Directory', false);

        $stateResponse = $this->get('/usa/california');
        $stateResponse->assertStatus(200);
        $stateResponse->assertSee('California', false);
        $stateResponse->assertSee('Los Angeles', false);

        $cityResponse = $this->get('/usa/california/los-angeles');
        $cityResponse->assertStatus(200);
        $cityResponse->assertSee('Los Angeles', false);
        $cityResponse->assertSee('business days', false);
        $cityResponse->assertSee('FAQPage', false);

        $nyCityResponse = $this->get('/usa/new-york/new-york-city');
        $nyCityResponse->assertStatus(200);
        $nyCityResponse->assertSee('New York City', false);

        // Invalid state or city should 404
        $invalidState = $this->get('/usa/atlantis');
        $invalidState->assertStatus(404);
    }

    public function test_buying_guides_and_faq_master_return_200(): void
    {
        $guidesIndex = $this->get('/guides');
        $guidesIndex->assertStatus(200);
        $guidesIndex->assertSee('Tech & Gadget Buying Guides', false);

        $guideDetail = $this->get('/guides/best-smart-home-gadgets');
        $guideDetail->assertStatus(200);
        $guideDetail->assertSee('Article', false);
        $guideDetail->assertSee('Smart Home', false);

        $faqMaster = $this->get('/faq');
        $faqMaster->assertStatus(200);
        $faqMaster->assertSee('Frequently Asked Questions', false);
        $faqMaster->assertSee('FAQPage', false);

        $llmsTxt = $this->get('/llms.txt');
        $llmsTxt->assertStatus(200);
        $llmsTxt->assertSee('AtoZGadgets', false);
    }

    public function test_product_page_includes_dynamic_faq_schema(): void
    {
        $product = Product::first();
        $response = $this->get('/product/' . $product->slug);
        $response->assertStatus(200);
        $response->assertSee('Product FAQs', false);
        $response->assertSee('FAQPage', false);
        $response->assertSee('OfferShippingDetails', false);
        $response->assertSee('MerchantReturnPolicy', false);
    }
}
