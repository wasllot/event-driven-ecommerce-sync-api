<?php

namespace Tests\Feature\Jobs;

use App\Actions\Order\ReplicateOrderAction;
use App\DTOs\OrderData;
use App\Jobs\ProcessOrderReplication;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ProcessOrderReplicationTest extends TestCase
{
    public function test_job_calls_replication_action(): void
    {
        Log::shouldReceive('info')->twice();

        $orderData = new OrderData(1, 'REF', 'email', 100, [], 'paid');

        $mockAction = Mockery::mock(ReplicateOrderAction::class);
        $mockAction->shouldReceive('execute')
            ->once()
            ->with($orderData);

        $this->app->instance(ReplicateOrderAction::class, $mockAction);

        $job = new ProcessOrderReplication($orderData);
        $job->handle($mockAction);

        $this->assertTrue(true);
    }

    public function test_job_is_pushed_to_default_queue(): void
    {
        $orderData = new OrderData(1, 'REF', 'email', 100, [], 'paid');
        $job = new ProcessOrderReplication($orderData);

        $this->assertEquals('default', $job->queue);
    }
}
