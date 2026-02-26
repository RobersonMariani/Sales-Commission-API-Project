<?php

namespace App\Api\Modules\Report\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource que formata as métricas do relatório geral de vendas para a resposta JSON.
 */
class SalesReportResource extends JsonResource
{
    /**
     * Transforma o recurso em array para a resposta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'total_sales' => (int) $this->resource->total_sales,
            'total_value' => (float) $this->resource->total_value,
            'total_commission' => (float) $this->resource->total_commission,
            'average_value' => (float) $this->resource->average_value,
            'average_commission' => (float) $this->resource->average_commission,
        ];
    }
}
