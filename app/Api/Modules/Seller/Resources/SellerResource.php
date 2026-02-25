<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Resources;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource que formata os dados de um vendedor para a resposta JSON.
 *
 * @mixin Seller
 */
class SellerResource extends JsonResource
{
    /**
     * Transforma o recurso de vendedor em array para a resposta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
