<?php

namespace App\Jobs;

use App\Actions\Sync\SyncProductAction;
use App\DTOs\ProductData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessProductSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ProductData $productData
    ) {
        $this->onQueue('high'); // Set priority to high
    }

    /**
     * Execute the job.
     */
    public function handle(SyncProductAction $syncProductAction): void
    {
        try {
            Log::info("Processing sync for product: {$this->productData->reference}");

            $syncProductAction->execute($this->productData);

            Log::info("Sync processed successfully for: {$this->productData->reference}");
        } catch (Throwable $exception) {
            Log::error("Failed to process sync for product {$this->productData->reference}: " . $exception->getMessage());

            // Release the job back to the queue to retry securely
            $this->release(10); // Retry after 10 seconds
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }
}
