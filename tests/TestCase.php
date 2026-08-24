<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure tests run in ultra-fast in-memory sandbox mode by default
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                \App\Models\Setting::set('cj_sandbox_mode', '1');
            }
        } catch (\Throwable $e) {
            // Ignore if DB not migrated in this test context
        }
    }

    protected function tearDown(): void
    {
        if ($this->app) {
            try {
                \App\Models\Setting::set('cj_sandbox_mode', '0');
            } catch (\Throwable $e) {
                // Ignore during teardown
            }
        }

        parent::tearDown();
    }
}
