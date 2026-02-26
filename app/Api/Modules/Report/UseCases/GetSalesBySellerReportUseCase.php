<?php

namespace App\Api\Modules\Report\UseCases;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Api\Modules\Report\Repositories\ReportRepository;
use Illuminate\Support\Collection;

/**
 * Caso de uso responsável pelo relatório de vendas agrupado por vendedor.
 */
class GetSalesBySellerReportUseCase
{
    public function __construct(
        private readonly ReportRepository $repository,
    ) {}

    /**
     * Retorna as métricas agregadas por vendedor.
     */
    public function execute(SalesReportQueryData $query): Collection
    {
        return $this->repository->getSalesBySeller($query);
    }
}
