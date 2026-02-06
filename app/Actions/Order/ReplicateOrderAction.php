<?php

namespace App\Actions\Order;

use App\DTOs\OrderData;
use App\Interfaces\ECommerceProviderInterface;
use Illuminate\Support\Facades\Log;

class ReplicateOrderAction
{
    public function __construct(
        protected ECommerceProviderInterface $provider
    ) {
    }

    public function execute(OrderData $orderData): void
    {
        Log::info("Replicating order: {$orderData->reference}");

        // Logic to push order to source (wholesaler)
        $newOrderId = $this->provider->createOrder($orderData);

        Log::info("Order replicated successfully. New ID: {$newOrderId}");
    }
}
