<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use function Laravel\Prompts\table;

/**
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property float $exchange_rate
 */
class Currency extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:8',
            'deleted_at'    => 'datetime',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
}
