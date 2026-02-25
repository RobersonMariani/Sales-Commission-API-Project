<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Resources;

use App\Api\Modules\Seller\Resources\SellerResource;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource que formata os dados de uma venda para a resposta JSON.
 *
 * @mixin Sale
 */
class SaleResource extends JsonResource
{
    /**
     * Transforma o recurso de venda em array para a resposta JSON.
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
