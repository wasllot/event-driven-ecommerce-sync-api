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

    /**
     * @return \Illuminate\Support\Collection<int, ProductData>
     */
    public function getProducts(array $filters = []): \Illuminate\Support\Collection
    {
        // Dummy implementation for list - in real world, fetch /api/products?display=full
        $response = Http::withBasicAuth($this->apiKey, '')
            ->get("{$this->baseUrl}/api/products", $filters + ['display' => 'full', 'limit' => 10]);

        if ($response->successful()) {
            $products = $response->json('products');
            return collect($products)->map(fn($data) => $this->mapToDto($data));
        }

        return collect([]);
    }

    public function getProduct(int $id): ?ProductData
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->get("{$this->baseUrl}/api/products/{$id}");

        if ($response->successful()) {
            $data = $response->json('product');
            return $this->mapToDto($data);
        }

        return null;
    }

    protected function mapToDto(array $data): ProductData
    {
        return ProductData::fromArray([
            'id' => $data['id'] ?? 0,
            'name' => $data['name'][0]['value'] ?? ($data['name'] ?? 'Unknown'), // Handle multi-lang
            'reference' => $data['reference'] ?? 'N/A',
            'price' => (float) ($data['price'] ?? 0),
            'stock' => (int) ($data['quantity'] ?? 0), // PrestaShop often requires a separate call for stock, simplified here
            'active' => (bool) ($data['active'] ?? false),
            'attributes' => [],
            'description' => $data['description'][0]['value'] ?? ($data['description'] ?? null),
            'description_short' => $data['description_short'][0]['value'] ?? ($data['description_short'] ?? null),
            'categories' => [], // Need logic to extract category names
            // Images usually require getting 'associations' -> 'images' and building URLs
            'images' => isset($data['associations']['images'])
                ? collect($data['associations']['images'])->map(fn($img) => "{$this->baseUrl}/api/images/products/{$data['id']}/{$img['id']}")->toArray()
                : [],
            'weight' => (float) ($data['weight'] ?? 0.0),
            'ean13' => $data['ean13'] ?? null,
        ]);
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
