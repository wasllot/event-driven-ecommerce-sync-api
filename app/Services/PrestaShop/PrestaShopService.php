<?php

namespace App\Services\PrestaShop;

use App\DTOs\OrderData;
use App\DTOs\ProductData;
use App\Interfaces\ECommerceProviderInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class PrestaShopService implements ECommerceProviderInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct(string $baseUrl, string $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    public function getProduct(int $id): ?ProductData
    {
        // Dummy implementation to demonstrate connection
        $response = Http::withBasicAuth($this->apiKey, '')
            ->get("{$this->baseUrl}/api/products/{$id}");

        if ($response->successful()) {
            // In a real scenario, map the XML/JSON response to DTO
            $data = $response->json('product');
            return ProductData::fromArray([
                'id' => $data['id'] ?? $id,
                'name' => $data['name'] ?? 'Unknown',
                'reference' => $data['reference'] ?? 'N/A',
                'price' => (float) ($data['price'] ?? 0),
                'stock' => (int) ($data['quantity'] ?? 0),
                'active' => (bool) ($data['active'] ?? false),
                'attributes' => []
            ]);
        }

        return null;
    }

    public function updateStock(string $reference, int $quantity): bool
    {
        // Dummy implementation
        // Real implementation would search by reference then update stock_available
        return true;
    }

    public function createOrder(OrderData $orderData): int
    {
        // Dummy implementation
        return rand(1000, 9999);
    }
}
