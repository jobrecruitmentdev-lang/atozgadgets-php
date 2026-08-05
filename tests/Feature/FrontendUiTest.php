<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class FrontendUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_and_displays_storefront()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        // Assuming there is some common layout text or store name
        $response->assertSee('AtoZ');
    }

    public function test_shop_page_loads_categories()
    {
        Category::updateOrCreate(
            ['slug' => 'tech-gadgets'],
            ['name' => 'Tech Gadgets', 'status' => 'active']
        );

        $response = $this->get('/shop');
        
        // Assert the shop route loads successfully
        $response->assertStatus(200);
        // We assert that the Blade view is rendering the category we just seeded
        $response->assertSee('Tech Gadgets');
    }

    public function test_login_page_renders_html_form()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        
        // Assert basic HTML inputs exist for the Blade auth UI
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('<form', false);
    }

    public function test_static_informational_pages_load_correctly()
    {
        $pages = [
            '/about-us',
            '/contact',
            '/privacy-policy',
            '/terms-conditions',
            '/return-and-refund-policy'
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);
            $response->assertStatus(200);
        }
    }
}
