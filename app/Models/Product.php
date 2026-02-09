<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;

/**
 * @property string $name
 * @property float $price
 * @property int $currency_id
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost',
    ];
    protected $hidden = [
        'currency_id',
    ];

    protected function casts(): array
    {
        return [
            'price'              => 'decimal:2',
            'tax_cost'           => 'decimal:2',
            'manufacturing_cost' => 'decimal:2',
        ];
    }

    public function getCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function getPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class)->with('currency');
    }
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($inner) use ($request) {
                    $inner->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('currency_id'), function ($q) use ($request) {
                $q->where('currency_id', $request->currency_id);
            })
            ->when($request->filled('min_price'), function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            })
            ->when($request->filled('max_price'), function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            })
            ->when($request->filled('high_cost'), function ($q) {
                $q->whereColumn('manufacturing_cost', '>', 'price');
            });
    }
    public function regionalPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class)->with('currency');
    }

}
