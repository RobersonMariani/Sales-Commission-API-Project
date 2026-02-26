<?php

namespace App\Api\Modules\Report\UseCases;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Api\Modules\Report\Repositories\ReportRepository;
use stdClass;

/**
 * Caso de uso responsável pelo relatório geral de vendas.
 */
class GetSalesReportUseCase
{
    public function __construct(
        private readonly ReportRepository $repository,
    ) {}

    /**
     * Retorna as métricas agregadas do relatório geral de vendas.
     */
    public function execute(SalesReportQueryData $query): stdClass
    {
        return $this->repository->getSalesSummary($query);
    }
}
