<?php

namespace App\Api\Modules\Sale\Resources;

use App\Api\Modules\Seller\Resources\SellerResource;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Sale */
class SaleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seller_id' => $this->seller_id,
            'value' => (float) $this->value,
            'commission' => (float) $this->commission,
            'sale_date' => $this->sale_date->format('Y-m-d'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'seller' => $this->whenLoaded('seller', fn () => SellerResource::make($this->seller)),
        ];
    }
}
