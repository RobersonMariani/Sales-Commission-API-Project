<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\UseCases;

use App\Api\Modules\Sale\Data\CreateSaleData;
use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

/**
 * Caso de uso responsável pela criação de uma nova venda.
 */
class CreateSaleUseCase
{
    public function __construct(
        private readonly SaleRepository $repository,
    ) {}

    /**
     * Cria uma venda calculando a comissão automaticamente dentro de uma transação.
     */
    public function execute(CreateSaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $rate = $data->commissionRate / 100;
            $commission = round($data->value * $rate, 2);
            $modelData = array_merge($data->toArrayModel(), ['commission' => $commission]);

            $sale = $this->repository->create($modelData);

            return $sale->load('seller:id,name,email');
        });
    }
}
