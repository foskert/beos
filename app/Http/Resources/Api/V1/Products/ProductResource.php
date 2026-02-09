<?php

namespace App\Http\Resources\Api\V1\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Symfony\Component\Translation\t;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
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
                    'exchange_rate' =>format_decimal($this->getCurrency->exchange_rate),
                ];
            }),
            'manufacturing_cost' => format_decimal($this->manufacturing_cost),
            'tax_cost'           => format_decimal($this->tax_cost),
            'created_at'         => format_date($this->created_at),
            'updated_at'         => format_date($this->updated_at),
        ];
    }
}
