<?php

namespace App\Actions\Sync;

use App\DTOs\ProductData;
use App\Interfaces\ECommerceProviderInterface;
use App\Services\PrestaShop\PrestaShopFactory;
use Illuminate\Support\Facades\Log;

class SyncProductAction
{
    public function __construct(
        protected PrestaShopFactory $factory
    ) {
    }

    public function execute(ProductData $productData): void
    {
        Log::info("Syncing product: {$productData->reference}");

        $source = $this->factory->make('source');
        $client = $this->factory->make('client');

        // Demonstrate fetching from source (optional verification step)
        // In a real flow, we might fetch fresh data or just trust the payload
        $sourceProduct = $source->getProduct($productData->id);

        if ($sourceProduct) {
            Log::info("Fetched product from source: {$sourceProduct->name}");
        }

        // Sync to client
        $client->updateStock($productData->reference, $productData->stock);

        Log::info("Product synced successfully to client: {$productData->reference}");
    }
}
