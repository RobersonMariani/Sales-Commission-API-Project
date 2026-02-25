<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\UseCases;

use App\Api\Modules\Seller\Data\SellerQueryData;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Caso de uso responsável pela listagem paginada de vendedores.
 */
class GetSellersUseCase
{
    public function __construct(
        private readonly SellerRepository $repository,
    ) {}

    /**
     * Retorna os vendedores paginados.
     */
    public function execute(SellerQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
