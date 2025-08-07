<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic API test example.
     */
    public function test_health_check_returns_successful_response(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
    }
}
