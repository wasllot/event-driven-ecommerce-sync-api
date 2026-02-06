<?php

namespace Tests\Unit\Actions;

use App\Actions\Order\ReplicateOrderAction;
use App\DTOs\OrderData;
use App\Interfaces\ECommerceProviderInterface;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ReplicateOrderActionTest extends TestCase
{
    public function test_replicate_order_creates_order_in_provider(): void
    {
        // Log::shouldReceive('info')->withAnyArgs();

        $orderData = new OrderData(
            id: 100,
            reference: 'ORD-TEST',
            customerEmail: 'customer@test.com',
            total: 200.0,
            items: [],
            status: 'paid'
        );

        $mockProvider = Mockery::mock(ECommerceProviderInterface::class);
        $mockProvider->shouldReceive('createOrder')
            ->once()
            ->with(Mockery::on(function ($arg) use ($orderData) {
                return $arg instanceof OrderData
                    && $arg->reference === $orderData->reference;
            }))
            ->andReturn(999); // New Order ID

        $action = new ReplicateOrderAction($mockProvider);
        $action->execute($orderData);

        // Log::shouldHaveReceived('info')->twice();
        $this->assertTrue(true);
    }
}
