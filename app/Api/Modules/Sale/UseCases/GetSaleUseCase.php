<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\UseCases;

use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Models\Sale;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Caso de uso responsável pela busca de uma venda específica.
 */
class GetSaleUseCase
{
    public function __construct(
        private readonly SaleRepository $repository,
    ) {}

    /**
     * Busca uma venda pelo ID ou lança exceção se não encontrada.
     *
     * @throws ModelNotFoundException Quando a venda não é encontrada.
     */
    public function execute(int $id): Sale
    {
        $sale = $this->repository->findById($id);

        if ($sale === null) {
            throw new ModelNotFoundException('Sale not found.');
        }

        return $sale;
    }
}
