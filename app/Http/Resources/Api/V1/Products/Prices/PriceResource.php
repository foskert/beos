<?php

namespace App\Http\Resources\Api\V1\Products\Prices;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PriceResource extends JsonResource
{

    public function toArray(Request $request): array
    {
          return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => format_decimal($this->price),
            'currency' => $this->when($this->relationLoaded('getCurrency') && $this->getCurrency, function() {
                return [
                    'name'          => $this->getCurrency->name,
                    'symbol'        => strtoupper(trim($this->getCurrency->symbol)),
                    'exchange_rate' => format_decimal($this->getCurrency->exchange_rate),
                ];
            }),

            'prices' =>  $this->when($this->relationLoaded('getPrices') && $this->getCurrency, function() {
                return $this->getPrices->map(function ($priceItem) {
                    return [
                        'price' => format_decimal($priceItem->price),
                        'currency' => $priceItem->currency ? [
                            'name'          => $priceItem->currency->name,
                            'symbol'        => strtoupper(trim($priceItem->currency->symbol)),
                            'exchange_rate' => format_decimal($priceItem->currency->exchange_rate),
                        ] : null,
                    ];
                });
            }),
            'manufacturing_cost' => format_decimal($this->manufacturing_cost),
            'tax_cost'  => format_decimal($this->tax_cost),
            'created_at'  => format_date($this->created_at),
            'updated_at'  => format_date($this->updated_at),
        ];
    }
}
