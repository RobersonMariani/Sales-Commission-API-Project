<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\UseCases;

use App\Api\Modules\Sale\Data\SaleQueryData;
use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetSalesUseCase
{
    public function __construct(
        private readonly SaleRepository $repository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Sale>
     */
    public function execute(SaleQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
