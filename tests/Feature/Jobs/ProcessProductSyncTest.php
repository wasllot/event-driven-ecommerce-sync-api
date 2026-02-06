<?php

namespace Tests\Feature\Jobs;

use App\Actions\Sync\SyncProductAction;
use App\DTOs\ProductData;
use App\Jobs\ProcessProductSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ProcessProductSyncTest extends TestCase
{
    public function test_job_calls_action_successfully(): void
    {
        Log::shouldReceive('info')->twice();

        $productData = new ProductData(1, 'Test', 'REF', 10, 5, true);

        // Mock the Action dependency
        $mockAction = Mockery::mock(SyncProductAction::class);
        $mockAction->shouldReceive('execute')
            ->once()
            ->with($productData);

        // Bind mock to container
        $this->app->instance(SyncProductAction::class, $mockAction);

        // Dispatch job synchronously for verification
        $job = new ProcessProductSync($productData);
        $job->handle($mockAction);

        $this->assertTrue(true);
    }

    public function test_job_is_pushed_to_high_queue(): void
    {
        $productData = new ProductData(1, 'Test', 'REF', 10, 5, true);
        $job = new ProcessProductSync($productData);

        $this->assertEquals('high', $job->queue);
    }
}
