<?php

namespace App\Jobs;

use App\Actions\Order\ReplicateOrderAction;
use App\DTOs\OrderData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessOrderReplication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public OrderData $orderData
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(ReplicateOrderAction $replicateOrderAction): void
    {
        try {
            Log::info("Processing order replication for: {$this->orderData->reference}");

            $replicateOrderAction->execute($this->orderData);

            Log::info("Order replication processed successfully for: {$this->orderData->reference}");
        } catch (Throwable $exception) {
            Log::error("Failed to replicate order {$this->orderData->reference}: " . $exception->getMessage());

            $this->release(30);
        }
    }
}
