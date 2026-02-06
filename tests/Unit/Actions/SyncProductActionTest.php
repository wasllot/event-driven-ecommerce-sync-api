<?php

namespace Tests\Unit\Actions;

use App\Actions\Sync\SyncProductAction;
use App\DTOs\ProductData;
use App\Interfaces\ECommerceProviderInterface;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase; // Using TestCase to have facade access (Log)

use App\Services\PrestaShop\PrestaShopFactory;

class SyncProductActionTest extends TestCase
{
    public function test_sync_product_updates_stock(): void
    {
        Log::spy();

        $productData = new ProductData(
            id: 1,
            name: 'P1',
            reference: 'SKU-001',
            price: 10.0,
            stock: 50,
            active: true
        );

        $mockSource = Mockery::mock(ECommerceProviderInterface::class);
        $mockSource->shouldReceive('getProduct')
            ->once()
            ->with(1)
            ->andReturn($productData);

        $mockClient = Mockery::mock(ECommerceProviderInterface::class);
        $mockClient->shouldReceive('updateStock')
            ->once()
            ->with('SKU-001', 50)
            ->andReturn(true);

        $mockFactory = Mockery::mock(PrestaShopFactory::class);
        $mockFactory->shouldReceive('make')->with('source')->andReturn($mockSource);
        $mockFactory->shouldReceive('make')->with('client')->andReturn($mockClient);

        $action = new SyncProductAction($mockFactory);
        $action->execute($productData);

        Log::shouldHaveReceived('info')->times(3);
        $this->assertTrue(true);
    }
}
