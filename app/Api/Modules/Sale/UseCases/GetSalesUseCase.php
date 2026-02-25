<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\UseCases;

use App\Api\Modules\Sale\Data\SaleQueryData;
use App\Api\Modules\Sale\Repositories\SaleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Caso de uso responsável pela listagem paginada de vendas.
 */
class GetSalesUseCase
{
    public function __construct(
        private readonly SaleRepository $repository,
    ) {}

    /**
     * Retorna as vendas paginadas com filtro opcional por vendedor.
     */
    public function execute(SaleQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
