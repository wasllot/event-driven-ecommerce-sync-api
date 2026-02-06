<?php

namespace App\Actions\Product;

use App\Services\PrestaShop\PrestaShopFactory;
use App\DTOs\ProductData;
use Illuminate\Support\Collection;

class ListProductsAction
{
    public function __construct(
        protected PrestaShopFactory $prestaShopFactory
    ) {
    }

    /**
     * @return Collection<int, ProductData>
     */
    public function execute(array $filters = []): Collection
    {
        // Always fetch from 'source' (Wholesaler)
        $service = $this->prestaShopFactory->make('source');

        return $service->getProducts($filters);
    }
}
