<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Controllers;

use App\Api\Modules\Sale\Data\CreateSaleData;
use App\Api\Modules\Sale\Data\SaleQueryData;
use App\Api\Modules\Sale\Resources\SaleResource;
use App\Api\Modules\Sale\UseCases\CreateSaleUseCase;
use App\Api\Modules\Sale\UseCases\GetSalesUseCase;
use App\Api\Modules\Sale\UseCases\GetSaleUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controlador responsável pelas operações de vendas.
 */
class SaleController extends Controller
{
    /**
     * Cadastra uma nova venda no sistema.
     */
    public function store(Request $request, CreateSaleUseCase $useCase): Response
    {
        $data = CreateSaleData::validateAndCreate($request->all());

        return SaleResource::make($useCase->execute($data))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Lista as vendas com paginação e filtro opcional por vendedor.
     */
    public function index(Request $request, GetSalesUseCase $useCase): AnonymousResourceCollection
    {
        $query = SaleQueryData::validateAndCreate($request->query());

        return SaleResource::collection($useCase->execute($query));
    }

    /**
     * Exibe os detalhes de uma venda específica.
     */
    public function show(int $sale, GetSaleUseCase $useCase): SaleResource
    {
        return SaleResource::make($useCase->execute($sale));
    }
}
