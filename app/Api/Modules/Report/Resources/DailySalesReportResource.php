<?php

namespace App\Api\Modules\Report\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource que formata as métricas de vendas por dia para a resposta JSON.
 */
class DailySalesReportResource extends JsonResource
{
    /**
     * Transforma o recurso em array para a resposta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => (string) $this->resource->date,
            'total_sales' => (int) $this->resource->total_sales,
            'total_value' => (float) $this->resource->total_value,
            'total_commission' => (float) $this->resource->total_commission,
        ];
    }
}
