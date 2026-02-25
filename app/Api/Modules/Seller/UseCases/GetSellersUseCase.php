<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\UseCases;

use App\Api\Modules\Seller\Data\SellerQueryData;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Models\Seller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetSellersUseCase
{
    public function __construct(
        private readonly SellerRepository $repository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Seller>
     */
    public function execute(SellerQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
