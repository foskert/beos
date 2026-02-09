<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Audit;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
class ProductPriceInternalService
{
    public function recordAudit(ProductPrice $price, string $event): void
    {
        $newValues = $price->getAttributes();
        $oldValues = null;

        Audit::create([
            'event'          => 'created',
            'auditable_id'   => $price->id,
            'auditable_type' => get_class($price),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'user_id'        => Auth::id()??'0',
            'url'            => Request::fullUrl()??'localhost',
            'ip_address'     => Request::ip()??'127.0.0.1',
        ]);
        Log::info(__('product.prices.audit_created', ['id' => $price->id]));
    }
}
