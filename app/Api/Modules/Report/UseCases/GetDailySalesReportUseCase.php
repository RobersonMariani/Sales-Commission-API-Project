<?php

namespace App\Api\Modules\Report\UseCases;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Api\Modules\Report\Repositories\ReportRepository;
use Illuminate\Support\Collection;

/**
 * Caso de uso responsável pelo relatório de vendas agrupadas por dia.
 */
class GetDailySalesReportUseCase
{
    public function __construct(
        private readonly ReportRepository $repository,
    ) {}

    /**
     * Retorna as métricas agregadas por dia.
     */
    public function execute(SalesReportQueryData $query): Collection
    {
        return $this->repository->getDailySales($query);
    }
}
