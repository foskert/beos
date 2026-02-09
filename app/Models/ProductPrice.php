<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $product_id
 * @property float $price
 * @property int $currency_id
 */
class ProductPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'currency_id',
        'price',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'price'       => 'decimal:2',
            'product_id'  => 'integer',
            'currency_id' => 'integer',
        ];
    }

    public function getProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
