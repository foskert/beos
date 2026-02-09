<?php

namespace App\Jobs;

use App\Models\ProductPrice;
use App\Services\ProductPriceInternalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductPriceInternalJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $tries   = 5;
    public $backoff =  [60, 300, 600];
    /**
     * Create a new job instance.
     */
    public function __construct(
        public ProductPrice $price,
        public string $event
    ){}

    /**
     * Execute the job.
     */
    public function handle(ProductPriceInternalService $service): void
    {
        if (!$this->price->exists && $this->event !== 'deleted') {
            return;
        }

        $service->recordAudit($this->price, $this->event);
    }

     public function failed(Throwable $exception): void
    {
        Log::critical(__('price.audits.failed', ['id' => $this->price->id]), [
            'error' => $exception->getMessage()
        ]);
    }
}
