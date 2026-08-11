<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the admin catalog import view loads with premium UI.
     *
     * @return void
     */
    public function test_admin_catalog_import_view_loads_successfully()
    {
        $response = $this->get('/admin/catalog/import');

        $response->assertStatus(200);
        $response->assertSee('CJ Dropshipping Gateway');
        $response->assertSee('CJ API Connected');
        $response->assertSee('Fetch New Products from CJ');
    }
}
