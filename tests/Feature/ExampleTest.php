<?php

namespace Tests\Feature;

use Database\Seeders\TopicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(TopicSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_sitemap_returns_xml(): void
    {
        $this->seed(TopicSeeder::class);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml');
        $response->assertSee('<urlset', false);
        $response->assertSee(route('topics.index'), false);
    }
}
