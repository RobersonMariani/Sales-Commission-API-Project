<?php

namespace App\Api\Modules\Sale\UseCases;

use App\Api\Modules\Sale\Data\SaleQueryData;
use App\Api\Modules\Sale\Repositories\SaleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetSalesUseCase
{
    public function __construct(
        private readonly SaleRepository $repository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, \App\Models\Sale>
     */
    public function execute(SaleQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
