<?php

namespace App\Actions\Product;

use App\Services\PrestaShop\PrestaShopFactory;
use App\Jobs\ProcessProductSync;
use Illuminate\Support\Facades\Log;

class MigrateProductAction
{
    public function __construct(
        protected PrestaShopFactory $prestaShopFactory
    ) {
    }

    public function execute(array $productIds): void
    {
        $sourceService = $this->prestaShopFactory->make('source');

        foreach ($productIds as $id) {
            try {
                // 1. Fetch full product data from Source
                $productData = $sourceService->getProduct($id);

                if (!$productData) {
                    Log::warning("Product migration failed: Product ID {$id} not found on source.");
                    continue;
                }

                // 2. Dispatch Sync Job (reusing existing logic to 'sync' which also creates/updates)
                // We dispatch to the queue to handle the actual API call to the Client asynchronously
                ProcessProductSync::dispatch($productData);

                Log::info("Queued migration for Product ID: {$id}");

            } catch (\Exception $e) {
                Log::error("Error queuing migration for Product ID {$id}: " . $e->getMessage());
            }
        }
    }
}
