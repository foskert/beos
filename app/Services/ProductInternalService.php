<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
class ProductInternalService
{
    public function recordAudit(Product $product, string $event): void
    {
        $newValues = $product->getAttributes();
        $oldValues = null;
        switch ($event) {
            case 'created':
                $oldValues = null;
                $newValues = $product->getAttributes();
                break;
            case 'updated':
                $oldValues = array_intersect_key($product->getRawOriginal(), $product->getChanges());
                $newValues = $product->getChanges();
                break;

            case 'deleted':
                $oldValues = $product->getRawOriginal();
                break;
        }
        if ($event === 'updated' && empty($newValues)) {
        return;
    }
        Audit::create([
            'event'          => $event,
            'auditable_id'   => $product->id,
            'auditable_type' => get_class($product),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'user_id'        => 1,
            'url'            => 'v1',
            'ip_address'     => '127.0.0.1',
        ]);

        Log::info(__('product.audits.message', ['id' => $product->id]));
    }
}
