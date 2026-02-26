<?php

namespace App\Api\Modules\Report\Controllers;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Api\Modules\Report\Resources\SalesReportResource;
use App\Api\Modules\Report\Resources\SellerSalesReportResource;
use App\Api\Modules\Report\UseCases\GetSalesBySellerReportUseCase;
use App\Api\Modules\Report\UseCases\GetSalesReportUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controlador responsável pelos endpoints de relatórios de vendas.
 */
class ReportController extends Controller
{
    /**
     * Retorna o relatório geral de vendas com métricas agregadas.
     */
    public function salesSummary(Request $request, GetSalesReportUseCase $useCase): SalesReportResource
    {
        $query = SalesReportQueryData::validateAndCreate($request->query());

        return SalesReportResource::make($useCase->execute($query));
    }

    /**
     * Retorna o relatório de vendas agrupado por vendedor.
     */
    public function salesBySeller(Request $request, GetSalesBySellerReportUseCase $useCase): AnonymousResourceCollection
    {
        $query = SalesReportQueryData::validateAndCreate($request->query());

        return SellerSalesReportResource::collection($useCase->execute($query));
    }
}
