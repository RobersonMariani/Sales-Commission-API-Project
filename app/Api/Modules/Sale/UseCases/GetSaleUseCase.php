<?php

namespace App\Api\Modules\Sale\UseCases;

use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Models\Sale;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetSaleUseCase
{
    public function __construct(
        private readonly SaleRepository $repository,
    ) {}

    public function execute(int $id): Sale
    {
        $sale = $this->repository->findById($id);

        if ($sale === null) {
            throw new ModelNotFoundException('Sale not found.');
        }

        return $sale;
    }
}
