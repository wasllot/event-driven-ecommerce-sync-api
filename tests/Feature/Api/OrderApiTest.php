<?php

namespace Tests\Feature\Api;

use App\DTOs\OrderData;
use App\Jobs\ProcessOrderReplication;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    public function test_replicate_order_endpoint_dispatches_job(): void
    {
        Queue::fake();

        $payload = [
            'id' => 500,
            'reference' => 'API-ORD-X',
            'customer_email' => 'api@test.com',
            'total' => 300.50,
            'items' => [],
            'status' => 'pending'
        ];

        $response = $this->postJson('/api/sync/order', $payload, [
            'X-API-KEY' => 'test-token'
        ]);

        $response->assertStatus(202);

        Queue::assertPushed(ProcessOrderReplication::class, function ($job) use ($payload) {
            return $job->orderData->reference === $payload['reference'];
        });
    }
}
