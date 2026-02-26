<?php

namespace App\Api\Modules\Report\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource que formata as métricas de vendas por vendedor para a resposta JSON.
 */
class SellerSalesReportResource extends JsonResource
{
    /**
     * Transforma o recurso em array para a resposta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'seller_id' => (int) $this->resource->seller_id,
            'seller_name' => (string) $this->resource->seller_name,
            'seller_email' => (string) $this->resource->seller_email,
            'total_sales' => (int) $this->resource->total_sales,
            'total_value' => (float) $this->resource->total_value,
            'total_commission' => (float) $this->resource->total_commission,
        ];
    }
}
