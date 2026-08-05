<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_status_ok()
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_non_existent_route_returns_404()
    {
        $response = $this->getJson('/non-existent-route-for-e2e');

        $response->assertStatus(404);
    }
}
