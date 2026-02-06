<?php

namespace App\Interfaces;

use App\DTOs\ProductData;
use App\DTOs\OrderData;

interface ECommerceProviderInterface
{
    public function getProduct(int $id): ?ProductData;

    /**
     * @return \Illuminate\Support\Collection<int, ProductData>
     */
    public function getProducts(array $filters = []): \Illuminate\Support\Collection;

    public function updateStock(string $reference, int $quantity): bool;
    public function createOrder(OrderData $orderData): int;
}
