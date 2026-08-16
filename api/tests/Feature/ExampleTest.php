<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_json_response(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertJsonPath('message', 'This application is API-only. Use the /api prefix.');
    }

    public function test_the_api_health_endpoint_returns_ok(): void
    {
        $response = $this->get('/api');

        $response->assertOk()
            ->assertJsonPath('status', 'ok');
    }
}
