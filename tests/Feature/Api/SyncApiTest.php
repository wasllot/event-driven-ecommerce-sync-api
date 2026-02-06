<?php

namespace Tests\Feature\Api;

use App\DTOs\ProductData;
use App\Jobs\ProcessProductSync;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SyncApiTest extends TestCase
{
    public function test_sync_product_endpoint_dispatches_job(): void
    {
        Queue::fake();

        $payload = [
            'id' => 1,
            'name' => 'API Test Product',
            'reference' => 'API-SKU-001',
            'price' => 19.99,
            'stock' => 100,
            'active' => true,
            'attributes' => ['size' => 'M']
        ];

        $response = $this->postJson('/api/sync/product', $payload);

        $response->assertStatus(202);

        Queue::assertPushed(ProcessProductSync::class, function ($job) use ($payload) {
            return $job->productData->reference === $payload['reference'];
        });
    }
}
