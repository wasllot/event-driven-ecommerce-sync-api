<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessProductSync;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup API User
        $this->user = User::factory()->create();
        $this->withHeader('X-API-KEY', 'test_api_key_12345');
        config([
            'services.api.token' => 'test_api_key_12345',
            'services.prestashop.source.url' => 'http://test-source.com',
            'services.prestashop.source.key' => 'key',
        ]);
    }

    public function test_can_list_products()
    {
        // Mock external PrestaShop API
        Http::fake([
            '*/api/products*' => Http::response([
                'products' => [
                    [
                        'id' => 1,
                        'name' => [['value' => 'Product A']],
                        'reference' => 'REF-A',
                        'price' => '10.00',
                        'quantity' => '5',
                        'active' => '1'
                    ]
                ]
            ], 200)
        ]);

        // dump(config('services.prestashop.source')); // verify config

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Product A']);
    }

    public function test_can_queue_migration()
    {
        Queue::fake();

        // Mock getting specific product details for migration
        Http::fake([
            '*/api/products/1' => Http::response([
                'product' => [
                    'id' => 1,
                    'name' => [['value' => 'Product A']],
                    'reference' => 'REF-A',
                    'price' => '10.00'
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/products/migrate', [
            'ids' => [1]
        ]);

        $response->assertStatus(202);

        // Check if job was pushed
        Queue::assertPushed(ProcessProductSync::class);
    }

    public function test_validation_fails_for_invalid_migration_request()
    {
        $response = $this->postJson('/api/products/migrate', [
            'ids' => 'not-an-array'
        ]);

        $response->assertStatus(422);
    }
}
