<?php
namespace App\Observers;

use App\Models\Product;
use App\Jobs\ProductInternalJob;

class ProductObserver
{
    public function created(Product $product): void
    {
        ProductInternalJob::dispatch($product, 'created')->afterCommit();
    }

    public function updated(Product $product): void
    {
        if ($product->wasChanged()) {
            ProductInternalJob::dispatch($product, 'updated')->afterCommit();
        }
    }
    public function deleted(Product $product): void
    {
        $event = $product->isForceDeleting() ? 'force_deleted' : 'deleted';
        ProductInternalJob::dispatch($product, $event)->afterCommit();
    }
}
