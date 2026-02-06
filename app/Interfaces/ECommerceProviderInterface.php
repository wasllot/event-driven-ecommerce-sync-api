<?php

namespace App\Interfaces;

use App\DTOs\ProductData;
use App\DTOs\OrderData;

interface ECommerceProviderInterface
{
    public function getProduct(int $id): ?ProductData;
    public function updateStock(string $reference, int $quantity): bool;
    public function createOrder(OrderData $orderData): int;
}
